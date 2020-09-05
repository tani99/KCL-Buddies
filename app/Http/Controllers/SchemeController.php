<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request as Request;
use Illuminate\Http\Response as Response;
use Illuminate\View\View as View;
use Illuminate\Support\Facades\Storage as Storage;
use Illuminate\Contracts\Filesystem\FileNotFoundException as FileNotFoundException;
use App\User as User;
use App\UserType as UserType;
use App\Rule as Rule;
use App\Question as Question;
use App\QuestionAnswer as QuestionAnswer;
use App\QuestionType as QuestionType;
use App\Scheme as Scheme;
use App\SchemeType as SchemeType;
use App\SchemeAdmin as SchemeAdmin;
use App\SchemeRule as SchemeRule;
use App\SchemeUser as SchemeUser;
use App\SchemePairing as SchemePairing;
use App\BannedSchemeUser as BannedSchemeUser;
use App\SchemeQuestion as SchemeQuestion;
use App\SchemeJoinCode as SchemeJoinCode;
use App\CountryHandler as CountryHandler;

class SchemeController extends Controller
{
    use RuleValidation;
    use SchemeAuthentication;
    use RedirectMessages;

    // An array of inputs to validate when a scheme is created.
    private static $schemeFormInputsValidation = [
        'name' => 'required|max:191',
        'description' => 'required|min:20',
        'type_id' => 'required|numeric',
        'icon' => 'nullable|image|mimes:jpeg,png,jpg|max:2048|dimensions:width=200,height=200',
        'date_start' => 'required|date',
        'date_end' => 'required|date|after:date_start|after:now'
    ];

    private static $defaultSchemeErrorMessage = 'You are not an administrator for that scheme.';
    private static $sysadminOnlyMessage = 'You must be a system administrator to perform that action.';

    private static $joinCodeLength = 5;

    /**
     * Create a new scheme controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request)
    {
        $data = $this->applySessionToData($request);
        $data['schemeTypes'] = $this->fetchSchemeTypes();

        $schemes = [];
        if ($this->isSystemAdministrator($request->user())) {
            $data['accessLevel'] = 'sysadmin';

            // Fetch all schemes.
            foreach (Scheme::all() as $scheme) {
                $scheme->date_start = self::friendlyDate($scheme->date_start);
                $scheme->date_end = self::friendlyDate($scheme->date_end);
                $schemes[] = [$scheme, ['canEdit' => true, 'canViewUsers' => true]];
            }
        } else {
            $data['accessLevel'] = 'user';

            $joinedSchemesData = []; // Mapping of scheme IDs, that the current logged-in user is part of/awaiting approval for, to data about the scheme user
            foreach (SchemeUser::whereUserId($request->user()->id)->get() as $schemeUser) {
                $joinedSchemesData[$schemeUser->scheme_id] = [
                    'paired' => isset($schemeUser->pairing_id)
                ];
            }
            $adminSchemeIds = SchemeAdmin::whereUserId($request->user()->id)->pluck('scheme_id')->all(); // Array of scheme IDs that the current logged-in user is an admin of

            // Fetch all the schemes that the current logged-in user is an admin of or has joined.
            foreach (Scheme::all() as $scheme) {
                $scheme->date_start = self::friendlyDate($scheme->date_start);
                $scheme->date_end = self::friendlyDate($scheme->date_end);
                $schemeData = null;
                if (array_key_exists($scheme->id, $joinedSchemesData)) { // If the user has joined the scheme
                    $joinedSchemeData = $joinedSchemesData[$scheme->id];
                    $schemeData = [
                        'canEdit' => false,
                        'canViewUsers' => false,
                        'paired' => $joinedSchemeData['paired']
                    ];
                } else if (in_array($scheme->id, $adminSchemeIds)) { // If the user is an administrator of the scheme
                    $schemeData = [
                        'canEdit' => true,
                        'canViewUsers' => true
                    ];
                } else {
                    continue;
                }
                $schemes[] = [$scheme, $schemeData];
            }
        }
        $data['schemes'] = $schemes;
        return view('scheme.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Request $request
     * @return Response|View
     */
    public function create(Request $request)
    {
        if (!$this->isSystemAdministrator($request->user())) {
            return redirect()->route('schemes.index')->withErrors(['msg' => self::$sysadminOnlyMessage]);
        }
        $data = $this->applySessionToData($request);
        $data['accessLevel'] = 'sysadmin';
        $data['schemeTypes'] = $this->fetchSchemeTypes();
        $data['schemeAdmins'] = '';
        $data['departments'] = '';
        $data['name'] = $request->input('name');
        $data['dateStart'] = $request->input('dateStart');
        $data['dateEnd'] = $request->input('dateEnd');
        $data['icon'] = null;
        $data['maxQuestions'] = Question::all()->count(); // The maximum number of questions to add to the database when the scheme is created.

        // Fetch all the rules that do not have a default value.
        $rules = [];
        foreach (Rule::all() as $rule) {
            if (!isset($rule->default_value)) {
                $ruleData = [];
                $ruleData['name'] = $rule->name;
                $ruleData['nameLc'] = strtolower(str_replace(' ', '_', $rule->name));
                $ruleData['description'] = $rule->description;
                $ruleData['value'] = null;
                $ruleData['validation'] = isset($rule->validation) ? json_decode($rule->validation) : null;
                $rules[$rule->id] = $ruleData;
            }
        }
        $data['rules'] = $rules;

        return view('scheme.form', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        if (!$this->isSystemAdministrator($request->user())) {
            return redirect()->route('schemes.index')->withErrors(['msg' => self::$sysadminOnlyMessage]);
        }
        $this->validate($request, array_merge(self::$schemeFormInputsValidation, [
            'question_count' => 'required|integer|min:5'
        ]));

        // Parse the scheme admin email list input into an array of scheme admin user IDs.
        $schemeAdminUserIDs = $this->parseSchemeAdminUserIDs($request->input('scheme_admins'));
        if ($schemeAdminUserIDs instanceof Response) {
            return $schemeAdminUserIDs;
        }

        // Fetch all the rules without a default value.
        $rules = Rule::whereNull('default_value')->get()->all();
        $this->validate($request, $this->createRulesInputValidation($rules));

        // Parse the departments list input into an array of department names.
        $departmentsArray = $this->parseDepartments($request->input('departments'));

        // Create and store the scheme.
        $scheme = new Scheme();
        $scheme->name = $request->input('name');
        $scheme->description = $request->input('description');
        $scheme->type_id = $request->input('type_id');
        $scheme->departments = isset($departmentsArray) ? json_encode($departmentsArray) : null;
        $scheme->date_start = $request->input('date_start');
        $scheme->date_end = $request->input('date_end');
        $scheme->save();

        if (isset($request->icon)) { // Check if the user has submitted a scheme icon.
            $iconName = $scheme->id . '_icon' . time() . '.' . $request->icon->getClientOriginalExtension();
            $request->icon->storeAs('schemes/icons', $iconName);
            $scheme->icon = $iconName;
            $scheme->save();
        }

        // Create and store the join codes.
        $schemeJoinCodesData = [];
        foreach (UserType::all() as $userType) {
            $joinCode = self::generateJoinCode();
            if (!isset($joinCode)) {
                $scheme->delete();
                return redirect()->route('schemes.index')->withErrors(['msg' => 'Failed to generate a join code.']);
            }
            $schemeJoinCodesData[] = [
                'scheme_id' => $scheme->id,
                'user_type_id' => $userType->id,
                'join_code' => $joinCode
            ];
        }
        SchemeJoinCode::insert($schemeJoinCodesData);

        if (!empty($schemeAdminUserIDs)) {
            // Create and store the scheme admins.
            $schemeAdminsData = [];
            foreach ($schemeAdminUserIDs as $schemeAdminUserID) {
                $schemeAdminsData[] = [
                    'scheme_id' => $scheme->id,
                    'user_id' => $schemeAdminUserID
                ];
            }
            SchemeAdmin::insert($schemeAdminsData);
        }

        // Create and store the scheme rules.
        $schemeRulesData = [];
        foreach ($rules as $rule) {
            $schemeRulesData[] = [
                'scheme_id' => $scheme->id,
                'rule_id' => $rule->id,
                'value' => $request->input(strtolower(str_replace(' ', '_', $rule->name)))
            ];
        }
        SchemeRule::insert($schemeRulesData);

        // Create and store the scheme questions.
        $questions = Question::getAllOrdered()->take($request->input('question_count'));
        $schemeQuestionsData = [];
        foreach ($questions as $question) {
            $schemeQuestionsData[] = [
                'scheme_id' => $scheme->id,
                'question_id' => $question->id
            ];
        }
        SchemeQuestion::insert($schemeQuestionsData);

        return redirect()->route('schemes.index')->with('success', 'Successfully created scheme \'' . $scheme->name . '\'!');
    }

    /**
     * Display the specified resource.
     *
     * @param int $schemeID
     * @param Request $request
     * @return Response|View
     */
    public function show(int $schemeID, Request $request)
    {
        $data = $this->constructData($schemeID);
        if (!isset($data)) {
            return redirect()->route('schemes.index');
        }
        $user = $request->user();

        $schemeAdminUserIDs = SchemeAdmin::whereSchemeId($schemeID)->pluck('user_id')->all();
        $isSysadmin = $this->isSystemAdministrator($user);
        $isSchemeAdmin = $isSysadmin || in_array($user->id, $schemeAdminUserIDs);
        if (!$isSysadmin && !$isSchemeAdmin && SchemeUser::whereUserId($user->id)->whereSchemeId($schemeID)->first() === null) {
            return redirect()->route('schemes.index');
        }

        $this->applySessionToData($request, $data);
        $data['dateStart'] = self::friendlyDate($data['dateStart']);
        $data['dateEnd'] = self::friendlyDate($data['dateEnd']);
        $data['schemeAdmins'] = User::whereIn('id', $schemeAdminUserIDs)->get()->all();

        if ($isSysadmin) {
            $data['accessLevel'] = 'sysadmin';
            $data['schemeLevel'] = 'sysadmin';
            $data['canEdit'] = true;
        } else {
            $data['accessLevel'] = 'user';
            if ($isSchemeAdmin) {
                $data['schemeLevel'] = 'admin';
                $data['canEdit'] = true;
            } else {
                $data['schemeLevel'] = 'user';
            }
        }
        if (!isset($data['canEdit'])) $data['canEdit'] = false;

        if ($data['canEdit'] === true) {
            // Fetch all the scheme join codes.
            $joinCodes = []; // An associative array mapping user type IDs to an array of data containing the user type model instance and the join code (as a string).
            foreach (UserType::all() as $userType) {
                $schemeJoinCode = SchemeJoinCode::whereSchemeId($schemeID)->whereUserTypeId($userType->id)->first();
                if (isset($schemeJoinCode)) {
                    $joinCodes[$userType->id] = [
                        'userType' => $userType,
                        'joinCode' => $schemeJoinCode->join_code
                    ];
                }
            }
            $data['joinCodes'] = $joinCodes;
        }

        return view('scheme.view', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $schemeID
     * @param Request $request
     * @return Response|View
     */
    public function edit(int $schemeID, Request $request)
    {
        // Ensure the request user is a system administrator or a scheme administrator of the specified scheme.
        $schemeAccess = $this->getSchemeAccess($schemeID, $request->user());
        if ($schemeAccess !== -1 && $schemeAccess !== 1) {
            return redirect()->route('schemes.index')->withErrors(['msg' => self::$defaultSchemeErrorMessage]);
        }

        $data = $this->constructData($schemeID);
        if (!isset($data)) {
            return redirect()->route('schemes.index');
        }
        $this->applySessionToData($request, $data);

        $data['schemeTypes'] = $this->fetchSchemeTypes();
        $data['departments'] = isset($data['departments']) ? implode(PHP_EOL, $data['departments']) : null;
        $data['dateStartInput'] = $data['dateStart'];
        $data['dateEndInput'] = $data['dateEnd'];

        // Fetch all the emails of the scheme admins.
        $schemeAdminEmails = [];
        foreach (SchemeAdmin::whereSchemeId($schemeID)->get() as $schemeAdmin) {
            $schemeAdminEmails[] = User::find($schemeAdmin->user_id)->email;
        }
        $data['schemeAdmins'] = implode(PHP_EOL, $schemeAdminEmails);

        // Fetch invalid admin emails if present.
        $invalidAdminEmails = $request->session()->has('invalidAdminEmails') ? $request->session()->get('invalidAdminEmails') : null;
        if (isset($invalidAdminEmails) && !is_array($invalidAdminEmails)) {
            $invalidAdminEmails = null;
        }
        $data['invalidAdminEmails'] = $invalidAdminEmails;

        $data['accessLevel'] = $schemeAccess === -1 ? 'sysadmin' : 'user';
        return view('scheme.form', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param int $schemeID
     * @param Request $request
     * @return Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(int $schemeID, Request $request)
    {
        $scheme = Scheme::find($schemeID);
        if (!isset($scheme)) {
            return redirect()->route('schemes.index');
        }
        $schemeAccess = $this->getSchemeAccess($schemeID, $request->user());
        if ($schemeAccess !== -1 && $schemeAccess !== 1) {
            return redirect()->route('schemes.index')->withErrors(['msg' => self::$defaultSchemeErrorMessage]);
        }
        $isSysadmin = $schemeAccess === -1;

        $this->validate($request, self::$schemeFormInputsValidation);
        $schemeAdminUserIDs = [];
        if ($isSysadmin) {
            // Parse the input scheme admin emails string into an array of scheme admin user IDs.
            $schemeAdminUserIDs = $this->parseSchemeAdminUserIDs($request->input('scheme_admins'));
            if ($schemeAdminUserIDs instanceof Response) {
                return $schemeAdminUserIDs;
            }
        }

        $schemeTypeID = $request->input('type_id');
        if (SchemeType::find($schemeTypeID) === null) {
            return redirect()->back()->withInput()->withErrors(['type_id' => 'Invalid scheme type']);
        }

        $scheme->name = $request->input('name');
        $scheme->description = $request->input('description');
        $scheme->type_id = $schemeTypeID;
        if (isset($request->icon)) {
            if (isset($scheme->icon)) {
                Storage::disk('public')->delete('schemes/icons/' . $scheme->icon);
            }

            $iconName = $scheme->id . '_icon' . time() . '.' . $request->icon->getClientOriginalExtension();
            $request->icon->storeAs('schemes/icons', $iconName);
            $scheme->icon = $iconName;
        }
        $scheme->date_start = $request->input('date_start');
        $scheme->date_end = $request->input('date_end');
        if ($isSysadmin) {
            // Parse the departments input string into an array of department names.
            $departmentsArray = $this->parseDepartments($request->input('departments'));
            if (isset($departmentsArray)) {
                // Remove users that are currently in the scheme if their department is not in the new departments array.
                $scheme->departments = json_encode($departmentsArray);
                $userIDsInScheme = SchemeUser::whereSchemeId($scheme->id)->pluck('user_id')->all();
                $usersToDelete = User::whereIn('id', $userIDsInScheme)->whereNotIn('department', $departmentsArray)->pluck('id')->toArray();
                SchemeUser::whereIn('user_id', $usersToDelete)->delete();
            } else {
                $scheme->departments = null;
            }
        }
        $scheme->save();

        if ($isSysadmin) {
            // Update the list of scheme admins.
            $currentSchemeAdmins = SchemeAdmin::whereSchemeId($schemeID)->get();
            $currentSchemeAdminIDs = [];
            $schemeAdminIDsForRemoval = [];
            foreach ($currentSchemeAdmins as $currentSchemeAdmin) {
                $currentSchemeAdminID = $currentSchemeAdmin->user_id;
                if (in_array($currentSchemeAdminID, $schemeAdminUserIDs)) {
                    $currentSchemeAdminIDs[] = $currentSchemeAdminID;
                } else {
                    $schemeAdminIDsForRemoval[] = $currentSchemeAdmin->id;
                }
            }
            SchemeAdmin::destroy($schemeAdminIDsForRemoval);
            $schemeAdminsData = [];
            foreach ($schemeAdminUserIDs as $schemeAdminUserID) {
                if (!in_array($schemeAdminUserID, $currentSchemeAdminIDs)) {
                    $schemeAdminsData[] = [
                        'scheme_id' => $schemeID,
                        'user_id' => $schemeAdminUserID
                    ];
                }
            }
            SchemeAdmin::insert($schemeAdminsData);
        }

        return redirect()->route('schemes.show', ['scheme_id' => $scheme->id])->with('success', 'Successfully updated the scheme!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $schemeID
     * @param Request $request
     * @return Response
     */
    public function destroy(int $schemeID, Request $request)
    {
        $scheme = Scheme::find($schemeID);
        if (!isset($scheme)) {
            return redirect()->route('schemes.index');
        }
        $schemeAccess = $this->getSchemeAccess($schemeID, $request->user());
        if ($schemeAccess !== -1) { // Redirect the user if they are not a system administrator.
            return redirect()->route('schemes.index')->withErrors(['msg' => self::$sysadminOnlyMessage]);
        }

        // Delete all question answers belonging to this scheme.
        foreach (SchemeQuestion::whereSchemeId($schemeID)->get() as $schemeQuestion) {
            QuestionAnswer::whereSchemeQuestionId($schemeQuestion->id)->delete();
        }

        // Delete all data relating to this scheme.
        SchemeQuestion::whereSchemeId($schemeID)->delete();
        SchemeJoinCode::whereSchemeId($schemeID)->delete();
        SchemeRule::whereSchemeId($schemeID)->delete();
        SchemeAdmin::whereSchemeId($schemeID)->delete();
        SchemeUser::whereSchemeId($schemeID)->delete();
        SchemePairing::whereSchemeId($schemeID)->delete();
        $schemeName = $scheme->name;
        $scheme->delete();

        return redirect()->route('schemes.index')->with('success', 'Successfully deleted scheme \'' . $schemeName . '\'.');
    }

    /**
     * Handle the request user attempting to join a scheme with a join code in the request.
     * @param Request $request
     * @return Response|View
     * @throws \Illuminate\Validation\ValidationException
     */
    public function apply(Request $request)
    {
        $this->validate($request, ['join_code' => 'required']);
        $joinCode = $request->input('join_code');
        return $this->applyWithJoinCode($joinCode, $request);
    }

    /**
     * Handle the request user attempting to join a scheme with the specified join code.
     *
     * @param string $joinCode A scheme's join code
     * @param Request $request
     * @return Response|View
     */
    public function applyJoinCode(string $joinCode, Request $request)
    {
        return $this->applyWithJoinCode($joinCode, $request);
    }

    /**
     * Handle the request user attempting to join a scheme.
     *
     * @param string $joinCode A scheme's join code
     * @param Request $request
     * @return Response|View
     */
    private function applyWithJoinCode(string $joinCode, Request $request)
    {
        $schemeJoinCode = SchemeJoinCode::whereJoinCode($joinCode)->first();
        if (!isset($schemeJoinCode)) {
            return redirect()->route('schemes.index')->withErrors(['msg' => 'Invalid join code.']);
        }
        if ($this->checkAccessToScheme($schemeJoinCode->scheme_id, $request->user())) {
            return redirect()->route('schemes.index')->withErrors(['msg' => 'You cannot join that scheme as you administer it!']);
        }
        $scheme = $schemeJoinCode->scheme;
        $userType = $schemeJoinCode->userType;
        if (!isset($scheme) || !isset($userType)) {
            return redirect()->route('schemes.index');
        }
        $canJoin = $this->canJoinScheme($scheme, $request->user());
        if (isset($canJoin)) {
            return redirect()->route('schemes.index')->withErrors(['msg' => 'You cannot join that scheme: ' . $canJoin]);
        }

        $data = [];
        $data['accessLevel'] = 'user';
        $data['scheme'] = $scheme;
        $data['joinCode'] = $joinCode;
        $data['userTypeID'] = $userType->id;
        $data['userTypeNames'] = $userType->getNames();

        // Fetch all the questions in the scheme to join.
        $questions = [];
        $questionIDs = [];
        foreach (SchemeQuestion::getOrderedQuestions($scheme->id)->get() as $schemeQuestion) {
            $question = $schemeQuestion->question;
            if (isset($question)) {
                $userTypeIDs = json_decode($question->user_type_ids);
                if (in_array($userType->id, $userTypeIDs)) {
                    $questions[] = $question;
                    $questionIDs[] = $question->id;
                }
            }
        }
        $data['questions'] = $questions;
        $data['questionIDs'] = $questionIDs;

        return view('scheme.join', $data);
    }

    /**
     * Process the questionnaire and add the request user to the member list.
     *
     * @param Request $request
     * @return Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function join(Request $request)
    {
        $this->validate($request, ['join_code' => 'required']);

        $user = $request->user();
        $joinCode = $request->input('join_code');
        $schemeJoinCode = SchemeJoinCode::whereJoinCode($joinCode)->first();
        if (!isset($schemeJoinCode)) {
            return redirect()->route('schemes.index')->withErrors(['msg' => 'Invalid join code.']);
        }
        if ($this->checkAccessToScheme($schemeJoinCode->scheme_id, $user)) {
            return redirect()->back();
        }
        $scheme = $schemeJoinCode->scheme;
        $userType = $schemeJoinCode->userType;
        if (!isset($scheme) || !isset($userType)) {
            return redirect()->back();
        }
        $canJoin = $this->canJoinScheme($scheme, $user);
        if (isset($canJoin)) {
            return redirect()->back();
        }

        // Process the questionnaire answers and store them.
        $questionTypes = QuestionType::all()->keyBy('id')->all();
        $questionAnswersData = [];
        $currentQuestionNumber = 1;
        foreach (SchemeQuestion::getOrderedQuestions($scheme->id)->get() as $schemeQuestion) {
            $question = $schemeQuestion->question;
            if (isset($question)) {
                $validation = $question->getValidation();
                $questionType = $questionTypes[$question->type_id];
                $answer = null;
                if ($questionType->name == 'number_range') {
                    $answer = $this->processQuestionTypeNumberRange($request, $question, $validation, $currentQuestionNumber);
                } elseif ($questionType->name == 'ranking') {
                    $answer = $this->processQuestionTypeRanking($request, $question, $validation, $currentQuestionNumber);
                } elseif ($questionType->name == 'location') {
                    $answer = $this->processQuestionTypeLocation($request, $question, $validation, $currentQuestionNumber);
                } elseif ($questionType->name == 'frequency') {
                    $answer = $this->processQuestionTypeFrequency($request, $question, $validation, $currentQuestionNumber);
                } elseif ($questionType->name == 'date') {
                    $answer = $this->processQuestionTypeDate($request, $question, $validation, $currentQuestionNumber);
                } elseif ($questionType->name == 'checkbox_multiple') {
                    $answer = $this->processQuestionTypeCheckboxMultiple($request, $question, $validation, $currentQuestionNumber);
                } elseif ($questionType->name == 'number') {
                    $answer = $this->processQuestionTypeNumber($request, $question, $validation, $currentQuestionNumber);
                } elseif ($questionType->name == 'colour') {
                    $answer = $this->processQuestionTypeColour($request, $question, $validation, $currentQuestionNumber);
                } elseif ($questionType->name == 'preference_gender' || $questionType->name == 'preference_age') {
                    $answer = $this->processQuestionTypePreferences($request, $question, $validation, $currentQuestionNumber);
                } else {
                    ++$currentQuestionNumber;
                    continue;
                }
                if ($answer instanceof Response) {
                    return $answer;
                }

                $questionAnswersData[] = [
                    'scheme_question_id' => $schemeQuestion->id,
                    'user_id' => $request->user()->id,
                    'answer' => json_encode($answer)
                ];
            }
            ++$currentQuestionNumber;
        }
        if (!empty($questionAnswersData)) QuestionAnswer::insert($questionAnswersData);

        $schemeUser = new SchemeUser();
        $schemeUser->scheme_id = $scheme->id;
        $schemeUser->user_id = $request->user()->id;
        $schemeUser->user_type_id = $userType->id;
        if ($userType->id == 1) {
            $schemeUser->approved = true; // If the user type is Newbies, approve them automatically.
            $schemeUser->save();
            return redirect()->route('schemes.show', ['scheme_id' => $scheme->id])->with('success', 'Successfully joined scheme: ' . $scheme->name . '');
        } elseif ($userType->id == 2) {
            $schemeUser->save();
            return redirect()->route('schemes.show', ['scheme_id' => $scheme->id])->with('info', 'Successfully requested to join scheme: ' . $scheme->name . '. You may change your preferences on this page.');
        } else {
            return redirect()->route('schemes.index');
        }
    }

    /**
     * Reset the join code of the specified user type for the specified resource.
     *
     * @param int $schemeID
     * @param int $userTypeID
     * @param Request $request
     * @return Response
     */
    public function resetJoinCode(int $schemeID, int $userTypeID, Request $request)
    {
        $user = $request->user();
        if (!$this->isSystemAdministrator($user)) {
            return redirect()->back();
        }
        $schemeJoinCode = SchemeJoinCode::whereSchemeId($schemeID)->whereUserTypeId($userTypeID)->first();
        if (!isset($schemeJoinCode)) {
            return redirect()->back();
        }
        $schemeJoinCode->join_code = self::generateJoinCode();
        if (!isset($schemeJoinCode->join_code)) return redirect()->back()->withErrors(['msg' => 'Could not generate a new join code.']);
        $schemeJoinCode->save();

        return redirect()->route('schemes.show', ['scheme_id' => $schemeID])->with('success', 'Successfully reset the join code.');
    }

    /**
     * Delete the icon of the specified resource.
     *
     * @param int $schemeID
     * @param Request $request
     * @return Response
     */
    public function deleteIcon(int $schemeID, Request $request)
    {
        $user = $request->user();
        if (!$this->checkAccessToScheme($schemeID, $user)) {
            return redirect()->back();
        }
        $scheme = Scheme::find($schemeID);
        if (!isset($scheme)) {
            return redirect()->route('schemes.index');
        }

        if (isset($scheme->icon)) { // Only delete the icon if there is one
            Storage::disk('public')->delete('schemes/icons/' . $scheme->icon);

            $scheme->icon = null;
            $scheme->save();
        }

        return redirect()->route('schemes.edit', ['scheme_id' => $schemeID])->with('success', 'Successfully removed the icon!');
    }

    /**
     * Display the user preferences page for the specified resource.
     *
     * @param int $schemeID
     * @param Request $request
     * @return Response|View
     */
    public function preferences(int $schemeID, Request $request)
    {
        $scheme = Scheme::find($schemeID);
        if (!isset($scheme)) {
            return redirect()->route('schemes.index');
        }
        $schemeUser = SchemeUser::whereSchemeId($schemeID)->whereUserId($request->user()->id)->first();
        if (!isset($schemeUser)) {
            return redirect()->back();
        }

        $data = [];
        $data['accessLevel'] = 'user';
        $data['scheme'] = $scheme;
        $data['preferences'] = $schemeUser->getPreferences() ?? [];

        $data['canChangeMaxNewbies'] = $schemeUser->user_type_id == 2 && $scheme->getRuleValue(1) == 1;

        return view('scheme.preferences', $data);
    }

    /**
     * Update the user preferences for the specified resource in storage.
     *
     * @param int $schemeID
     * @param Request $request
     * @return Response
     */
    public function updatePreferences(int $schemeID, Request $request)
    {
        if (Scheme::find($schemeID) === null) {
            return redirect()->route('schemes.index');
        }
        $schemeUser = SchemeUser::whereSchemeId($schemeID)->whereUserId($request->user()->id)->first();
        if (!isset($schemeUser)) {
            return redirect()->back();
        }

        $maxNewbies = $request->input('max_newbies');
        if (isset($maxNewbies)) {
            if ($maxNewbies < 1) {
                return redirect()->back()->withInput()->withErrors(['max_newbies' => 'The minimum number of newbies is 1.']);
            } else if ($maxNewbies > 5) {
                return redirect()->back()->withInput()->withErrors(['max_newbies' => 'The maximum number of newbies is 5.']);
            }
        }

        $subscribed = $request->input('subscribed');

        $preferences = $schemeUser->getPreferences() ?? [];

        // Max newbies
        if (isset($maxNewbies)) $preferences['max_newbies'] = $maxNewbies;
        else unset($preferences['max_newbies']);

        // Email subscriptions
        $preferences['subscribed'] = isset($subscribed) && $subscribed == '1';

        $schemeUser->preferences = !empty($preferences) ? json_encode($preferences) : null;
        $schemeUser->save();

        return redirect()->route('schemes.show', ['scheme_id' => $schemeID])->with('success', 'Successfully updated your preferences.');
    }

    /**
     * @param Scheme $scheme
     * @param User $user
     * @return string|null An error message if the specified user cannot join the specified scheme.
     */
    private function canJoinScheme(Scheme $scheme, User $user): ?string
    {
        $schemeUser = SchemeUser::whereSchemeId($scheme->id)->whereUserId($user->id)->first();
        if (isset($schemeUser)) {
            if ($schemeUser->approved) {
                return 'You are already part of this scheme!';
            } else {
                return 'You have already applied to join this scheme!';
            }
        } elseif (BannedSchemeUser::whereUserId($user->id)->first() != null) {
            return 'You are banned from this scheme.';
        }
        $departments = $scheme->getDepartments();
        if (isset($departments)) {
            if (!isset($user->department) || !in_array($user->department, $departments)) {
                return 'The department you are a student in does not belong to this scheme.';
            }
        }

        try {
            $endDate = date_create_immutable_from_format('Y-m-d h:i:s', $scheme->date_end . ' 11:59:00');
            if ((new \DateTimeImmutable()) >= $endDate) {
                return 'The deadline has already passed!';
            }
        } catch (\Exception $e) {
            report($e);
        }
        return null;
    }

    /**
     * Create an array of data containing information about a scheme, for use in views.
     *
     * @param int $schemeID
     * @return array|null An array of scheme data, or null if a scheme with the specified ID does not exist.
     */
    private function constructData(int $schemeID): ?array
    {
        $data = [];
        $data['schemeID'] = $schemeID;

        $scheme = Scheme::find($schemeID);
        if (isset($scheme)) {
            $data['name'] = $scheme->name;
            $data['description'] = $scheme->description;
            $data['typeID'] = $scheme->type_id;
            $data['type'] = $scheme->schemeType->name;
            $data['icon'] = $scheme->icon;
            $data['departments'] = $scheme->departments;
            $data['dateStart'] = $scheme->date_start;
            $data['dateEnd'] = $scheme->date_end;

            if (isset($data['departments'])) $data['departments'] = json_decode($data['departments']);
            return $data;
        } else {
            return null;
        }
    }

    /**
     * Fetch all the scheme types from the database and store them in an associative array,
     * mapping scheme type IDs to their model class {@link SchemeType}.
     *
     * @return array An associative array of scheme types.
     */
    private function fetchSchemeTypes(): array
    {
        return SchemeType::all()->keyBy('id')->all();
    }

    /**
     * @param string|null $departmentsInput The input text of departments separated by new lines
     * @return array|null An array of department names, or null if no departments.
     */
    private function parseDepartments(?string $departmentsInput): ?array
    {
        if (isset($departmentsInput) && strlen($departmentsInput) > 0) {
            $departments = [];
            $departmentsInput = explode(PHP_EOL, $departmentsInput);
            foreach ($departmentsInput as $department) {
                $trimmedDepartment = trim($department);
                if (strlen($trimmedDepartment) === 0) continue;
                $trimmedDepartment = str_replace('\r', '', $trimmedDepartment);
                $departments[] = $trimmedDepartment;
            }
            if (!empty($departments)) return $departments;
        }
        return null;
    }

    /**
     * @param string|null $schemeAdmins The input text of scheme admins separated by new lines
     * @return array|Response An array of scheme admin user IDs, or a redirect response if an invalid scheme admin email is present.
     */
    private function parseSchemeAdminUserIDs(?string $schemeAdmins)
    {
        $schemeAdminUserIDs = [];
        if (isset($schemeAdmins) && strlen($schemeAdmins) > 0) {
            $schemeAdminsArray = explode(PHP_EOL, $schemeAdmins);
            $invalidEmails = [];
            foreach ($schemeAdminsArray as $schemeAdminEmail) {
                $trimmedSchemeAdminEmail = trim($schemeAdminEmail);
                if (strlen($trimmedSchemeAdminEmail) === 0) continue;
                $trimmedSchemeAdminEmail = str_replace('\r', '', $trimmedSchemeAdminEmail);
                if (!filter_var($trimmedSchemeAdminEmail, FILTER_VALIDATE_EMAIL)) {
                    return redirect()->back()->withInput()->withErrors(['scheme_admins' => 'Please enter valid email addresses. \'' . $trimmedSchemeAdminEmail . '\' is not valid.']);
                }
                $schemeAdminUser = User::whereEmail($trimmedSchemeAdminEmail)->first();
                if (isset($schemeAdminUser)) {
                    $schemeAdminUserIDs[] = $schemeAdminUser->id;
                } else {
                    $invalidEmails[] = $trimmedSchemeAdminEmail;
                }
            }
            if (!empty($invalidEmails)) {
                return redirect()->back()->withInput()->with('invalidAdminEmails', $invalidEmails)->withErrors(['scheme_admins' => 'Invalid user email' . (count($invalidEmails) != 1 ? 's' : '') . ' provided.']);
            }
        }
        return $schemeAdminUserIDs;
    }

    /**
     * For the questionnaire, process the question type: number_range
     *
     * @param Request $request
     * @param Question $question
     * @param array|null $validation
     * @param int $currentQuestionNumber
     * @return array|Response The answer to the question represented as an array. If there is an error with the answer, a Response is returned instead.
     */
    private function processQuestionTypeNumberRange(Request $request, Question $question, ?array $validation, int $currentQuestionNumber)
    {
        $answer = [];

        $rangeCount = isset($validation) ? ($validation['options'] ?? 0) : 0; // The number of options expected in the answer for the question
        for ($i = 0; $i < $rangeCount; $i++) {
            $inputAnswer = $request->input('q' . $question->id . '-' . $i);
            if (!isset($inputAnswer)) {
                return redirect()->back()->withInput()->withErrors(['msg' => 'Missing answer in range for question ' . $currentQuestionNumber]);
            }
            if (isset($validation)) {
                if (in_array('min', $validation)) {
                    if ($inputAnswer < $validation['min']) {
                        return redirect()->back()->withInput()->withErrors(['msg' => 'Number is smaller than the minimum for question ' . $currentQuestionNumber]);
                    }
                }
                if (in_array('max', $validation)) {
                    if ($inputAnswer > $validation['max']) {
                        return redirect()->back()->withInput()->withErrors(['msg' => 'Number exceeds maximum in range for question ' . $currentQuestionNumber]);
                    }
                }
            }
            $answer[] = $inputAnswer;
        }

        return $answer;
    }

    /**
     * For the questionnaire, process the question type: ranking
     *
     * @param Request $request
     * @param Question $question
     * @param array|null $validation
     * @param int $currentQuestionNumber
     * @return array|Response The answer to the question represented as an array. If there is an error with the answer, a Response is returned instead.
     */
    private function processQuestionTypeRanking(Request $request, Question $question, ?array $validation, int $currentQuestionNumber)
    {
        $answer = [];

        $count = isset($validation) ? ($validation['options'] ?? 0) : 0; // The number of options expected in the answer for the question

        $expectedTotal = 0; // The total sum of the rankings expected
        for ($i = 0; $i < $count; $i++) {
            $expectedTotal += ($i + 1);
        }
        $actualTotal = 0; // The total sum of the rankings inputted
        for ($i = 0; $i < $count; $i++) {
            $inputAnswer = $request->input('q' . $question->id . '-' . $i);
            if (!isset($inputAnswer)) {
                return redirect()->back()->withInput()->withErrors(['msg' => 'Missing ranking for question ' . $currentQuestionNumber]);
            }
            $actualTotal += $inputAnswer;
            $answer[] = $inputAnswer;
        }
        if ($actualTotal != $expectedTotal) {
            return redirect()->withInput()->back()->withErrors(['msg' => 'Invalid rankings for question ' . $currentQuestionNumber]);
        }

        return $answer;
    }

    /**
     * For the questionnaire, process the question type: location
     *
     * @param Request $request
     * @param Question $question
     * @param array|null $validation
     * @param int $currentQuestionNumber
     * @return array|Response The answer to the question represented as an array. If there is an error with the answer, a Response is returned instead.
     */
    private function processQuestionTypeLocation(Request $request, Question $question, ?array $validation, int $currentQuestionNumber)
    {
        $inputAnswer = $request->input('q' . $question->id);
        if (isset($inputAnswer)) { // If the answer inputted is a country code
            try {
                $answer = CountryHandler::alpha2LatLonLookup($inputAnswer); // Fetch the lat,long of the country code
                if (isset($answer)) {
                    return $answer;
                } else {
                    return redirect()->back();
                }
            } catch (FileNotFoundException $e) {
                return redirect()->withInput()->back()->withException($e);
            }
        } else {
            $inputAnswerLat = $request->input('q' . $question->id . '-lat');
            $inputAnswerLong = $request->input('q' . $question->id . '-long');
            if (isset($inputAnswerLat) && isset($inputAnswerLong)) {
                if ($inputAnswerLat >= -90 && $inputAnswerLat <= 90 && $inputAnswerLong >= -180 && $inputAnswerLong <= 180) {
                    settype($inputAnswerLat, 'float');
                    settype($inputAnswerLong, 'float');
                    return [$inputAnswerLat, $inputAnswerLong];
                } else {
                    return redirect()->back()->withInput()->withErrors(['msg' => 'Latitude or longitude is out of range for question ' . $currentQuestionNumber]);
                }
            } else {
                return redirect()->back()->withInput()->withErrors(['msg' => 'Invalid latitude and longitude for question ' . $currentQuestionNumber]);
            }
        }
    }

    /**
     * For the questionnaire, process the question type: frequency
     *
     * @param Request $request
     * @param Question $question
     * @param array|null $validation
     * @param int $currentQuestionNumber
     * @return array|Response The answer to the question represented as an array. If there is an error with the answer, a Response is returned instead.
     */
    private function processQuestionTypeFrequency(Request $request, Question $question, ?array $validation, int $currentQuestionNumber)
    {
        $number = $request->input('q' . $question->id);
        if ($number == '365' || $number == '52' || $number == '12' || $number == '1' || $number == '0') { // Answer validation
            return [$number];
        } else {
            return redirect()->back()->withInput()->withErrors(['msg' => 'Invalid frequency for question ' . $currentQuestionNumber]);
        }
    }

    /**
     * For the questionnaire, process the question type: date
     *
     * @param Request $request
     * @param Question $question
     * @param array|null $validation
     * @param int $currentQuestionNumber
     * @return array|Response The answer to the question represented as an array. If there is an error with the answer, a Response is returned instead.
     */
    private function processQuestionTypeDate(Request $request, Question $question, ?array $validation, int $currentQuestionNumber)
    {
        $inputAnswer = $request->input('q' . $question->id);
        if (preg_match('/^(19|20)\d\d[-](0[1-9]|1[012])[-](0[1-9]|[12][0-9]|3[01])$/', $inputAnswer) !== false) { // Ensure the answer inputted is a valid date
            $dateTimeZone = new \DateTimeZone('Europe\London');
            $date = \DateTimeImmutable::createFromFormat('Y-m-d', $inputAnswer, $dateTimeZone);
            if ($date !== false) {
                if (isset($validation)) {
                    if (in_array('min', $validation)) {
                        if ($date < \DateTimeImmutable::createFromFormat('Y-m-d', $validation['min'], $dateTimeZone)) {
                            return redirect()->back()->withInput()->withErrors(['msg' => 'Date is lower than the minimum for question ' . $currentQuestionNumber]);
                        }
                    }
                    if (in_array('max', $validation)) {
                        if ($date > \DateTimeImmutable::createFromFormat('Y-m-d', $validation['max'], $dateTimeZone)) {
                            return redirect()->back()->withInput()->withErrors(['msg' => 'Date exceeds maximum for question ' . $currentQuestionNumber]);
                        }
                    }
                }
                return [$inputAnswer];
            } else {
                return redirect()->back()->withInput()->withErrors(['msg' => 'Invalid date for question ' . $currentQuestionNumber]);
            }
        } else {
            return redirect()->back()->withInput()->withErrors(['msg' => 'Invalid date for question ' . $currentQuestionNumber]);
        }
    }

    /**
     * For the questionnaire, process the question type: checkbox_multiple
     *
     * @param Request $request
     * @param Question $question
     * @param array|null $validation
     * @param int $currentQuestionNumber
     * @return array|Response The answer to the question represented as an array. If there is an error with the answer, a Response is returned instead.
     */
    private function processQuestionTypeCheckboxMultiple(Request $request, Question $question, ?array $validation, int $currentQuestionNumber)
    {
        $answer = [];

        $answerCount = isset($validation) ? ($validation['options'] ?? 0) : 0; // The number of options expected in the answer for the question
        for ($i = 0; $i < $answerCount; $i++) {
            $inputAnswer = $request->input('q' . $question->id . '-' . $i);
            $answer[] = isset($inputAnswer) ? 1 : 0;
        }

        return $answer;
    }

    /**
     * For the questionnaire, process the question type: number
     *
     * @param Request $request
     * @param Question $question
     * @param array|null $validation
     * @param int $currentQuestionNumber
     * @return array|Response The answer to the question represented as an array. If there is an error with the answer, a Response is returned instead.
     */
    private function processQuestionTypeNumber(Request $request, Question $question, ?array $validation, int $currentQuestionNumber)
    {
        $number = $request->input('q' . $question->id);
        if (is_numeric($number)) {
            if (isset($validation)) {
                if (in_array('min', $validation)) {
                    if ($number < $validation['min']) {
                        return redirect()->back()->withInput()->withErrors(['msg' => 'Number too small for question ' . $currentQuestionNumber]);
                    }
                }
                if (in_array('max', $validation)) {
                    if ($number > $validation['max']) {
                        return redirect()->back()->withInput()->withErrors(['msg' => 'Number too large for question ' . $currentQuestionNumber]);
                    }
                }
            }
            return [$number];
        } else {
            return redirect()->back()->withInput()->withErrors(['msg' => 'Invalid number for question ' . $currentQuestionNumber]);
        }
    }

    /**
     * For the questionnaire, process the question type: colour
     *
     * @param Request $request
     * @param Question $question
     * @param array|null $validation
     * @param int $currentQuestionNumber
     * @return array|Response The answer to the question represented as an array. If there is an error with the answer, a Response is returned instead.
     */
    private function processQuestionTypeColour(Request $request, Question $question, ?array $validation, int $currentQuestionNumber)
    {
        $hex = $request->input('q' . $question->id);
        if (preg_match('/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/', $hex)) {
            return [$hex];
        } else {
            return redirect()->back()->withInput()->withErrors(['msg' => 'Invalid colour for question ' . $currentQuestionNumber]);
        }
    }

    /**
     * For the questionnaire, process the question types involving preferences.
     *
     * @param Request $request
     * @param Question $question
     * @param array|null $validation
     * @param int $currentQuestionNumber
     * @return array|Response The answer to the question represented as an array. If there is an error with the answer, a Response is returned instead.
     */
    private function processQuestionTypePreferences(Request $request, Question $question, ?array $validation, int $currentQuestionNumber)
    {
        $inputAnswer = $request->input('q' . $question->id);
        return [($inputAnswer == 'on' || $inputAnswer == '1') ? 1 : 0];
    }

    /**
     * @param string $sqlDate The date string in SQL format
     * @return string A human readable format of the date: dd/mm/YYYY
     */
    private static function friendlyDate(string $sqlDate): string
    {
        try {
            $dateTime = new \DateTimeImmutable($sqlDate);
            return $dateTime->format('d/m/Y');
        } catch (\Exception $e) {
            report($e);
            return '00/00/0000';
        }
    }

    /**
     * @return string A random unique join code.
     */
    private static function generateJoinCode(): string
    {
        $joinCode = null;
        $attempts = 0;
        do {
            if ($attempts >= 100) return null;
            $joinCode = strtoupper(substr(md5(microtime()), rand(0, 26), self::$joinCodeLength));
            $attempts++;
        } while (SchemeJoinCode::whereJoinCode($joinCode)->first() !== null);
        return $joinCode;
    }
}
