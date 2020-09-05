<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request as Request;
use Illuminate\Http\Response as Response;
use Illuminate\View\View as View;
use App\User as User;
use App\Scheme as Scheme;
use App\Question as Question;
use App\SchemeQuestion as SchemeQuestion;
use App\SchemeUser as SchemeUser;

class SchemeQuestionController extends Controller
{
    use SchemeAuthentication;
    use RedirectMessages;

    private static $unmodifiableQuestionsErrorMsg = 'You cannot add/remove questions once a user has completed the questionnaire once!';

    /**
     * Create a new scheme-question controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @param int $schemeID
     * @param Request $request
     * @return Response|View
     */
    public function index(int $schemeID, Request $request)
    {
        $scheme = Scheme::find($schemeID);
        if (!isset($scheme)) {
            return redirect()->route('schemes.index');
        }
        $schemeLevel = $this->getSchemeAccess($schemeID, $request->user());
        if ($schemeLevel !== -1 && $schemeLevel !== 1) {
            return redirect()->route('schemes.index');
        }

        $questionIDs = [];
        $questionWeightings = []; // A mapping of question IDs to their weighting
        foreach (SchemeQuestion::getOrderedQuestions($schemeID)->get() as $schemeQuestion) {
            $questionIDs[] = $schemeQuestion->question_id;
            $questionWeightings[$schemeQuestion->question_id] = $schemeQuestion->weight;
        }
        $questionsData = []; // A mapping of question IDs to their data (e.g. title and weight)
        foreach (Question::whereIn('id', $questionIDs)->get() as $question) {
            $questionsData[$question->id] = [
                'id' => $question->id,
                'title' => $question->title,
                'weight' => $questionWeightings[$question->id],
                'validation' => $question->getValidation()
            ];
        }
        $questions = [];
        foreach ($questionIDs as $questionID) {
            $questions[] = $questionsData[$questionID];
        }

        $data = $this->applySessionToData($request);
        $data['accessLevel'] = $schemeLevel === -1 ? 'sysadmin' : 'user';
        $data['scheme'] = $scheme;
        $data['questions'] = $questions;
        $data['questionIDs'] = $questionIDs;

        return view('scheme.questions.index', $data);
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
        $scheme = Scheme::find($schemeID);
        if (!isset($scheme)) {
            return redirect()->route('schemes.index');
        }
        $schemeLevel = $this->getSchemeAccess($schemeID, $request->user());
        if ($schemeLevel !== -1 && $schemeLevel !== 1) {
            return redirect()->route('schemes.index');
        }

        $questionsData = []; // A mapping of question IDs to their data
        foreach (Question::getAllOrdered() as $question) {
            $questionsData[$question->id] = [
                'id' => $question->id,
                'title' => $question->title
            ];
        }

        // Fetch all the current questions in the scheme.
        $questionIDs = SchemeQuestion::getOrderedQuestions($schemeID)->pluck('question_id')->all();
        $currentQuestions = [];
        foreach ($questionIDs as $questionID) {
            $currentQuestions[$questionID] = $questionsData[$questionID];
        }
        // Fetch all the questions not currently in the scheme.
        $allQuestions = [];
        foreach ($questionsData as $questionID => $questionData) {
            if (!in_array($questionID, $questionIDs)) {
                $allQuestions[$questionID] = $questionData;
            }
        }

        $data = $this->applySessionToData($request);
        $data['accessLevel'] = $schemeLevel === -1 ? 'sysadmin' : 'user';
        $data['scheme'] = $scheme;
        $data['currentQuestions'] = $currentQuestions;
        $data['allQuestions'] = $allQuestions;
        $data['canChange'] = $this->canChangeQuestions($schemeID, $request->user());
        return view('scheme.questions.edit', $data);
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
        if (!isset($scheme) || !$this->checkAccessToScheme($schemeID, $request->user())) {
            return redirect()->route('schemes.index');
        }
        $this->validate($request, [
            'active_questions' => 'required|array|min:5'
        ]);

        $activeQuestionsInput = $request->input('active_questions');

        if ($this->canChangeQuestions($schemeID, $request->user())) {
            // Fetch all the questions and their data.
            $questionsData = [];
            foreach ($activeQuestionsInput as $questionID) {
                $question = Question::find($questionID);
                if (isset($question)) {
                    $questionsData[] = [
                        'scheme_id' => $schemeID,
                        'question_id' => $questionID
                    ];
                } else {
                    return redirect()->route('schemes.questions.edit', ['scheme_id' => $schemeID]);
                }
            }
            SchemeQuestion::whereSchemeId($schemeID)->delete();
            SchemeQuestion::insert($questionsData);
        } else {
            $currentSchemeQuestions = []; // A mapping of question IDs to their scheme question instance
            $questionIDs = [];
            foreach (SchemeQuestion::getOrderedQuestions($schemeID)->get() as $schemeQuestion) {
                $questionIDs[] = $schemeQuestion->question_id;
                $currentSchemeQuestions[$schemeQuestion->question_id] = $schemeQuestion;
            }
            // Ensure the user has not added or removed any questions.
            $numberOfQuestions = count($activeQuestionsInput);
            if (count($questionIDs) !== $numberOfQuestions) {
                return redirect()->route('schemes.questions.edit', ['scheme_id' => $schemeID])->withErrors(['msg' => self::$unmodifiableQuestionsErrorMsg]);
            }
            foreach ($activeQuestionsInput as $questionID) {
                if (!in_array($questionID, $questionIDs)) {
                    return redirect()->route('schemes.questions.edit', ['scheme_id' => $schemeID])->withErrors(['msg' => self::$unmodifiableQuestionsErrorMsg]);
                }
            }
            for ($i = 0; $i < $numberOfQuestions; $i++) {
                $questionID = $activeQuestionsInput[$i];
                $schemeQuestion = $currentSchemeQuestions[$questionID];
                $schemeQuestion->priority = $numberOfQuestions - 1 - $i;
                $schemeQuestion->save();
            }
        }

        return redirect()->route('schemes.questions.index', ['scheme_id' => $schemeID])->with('success', 'Successfully updated the questions in the questionnaire!');
    }

    /**
     * Update the weightings of the specified resource in storage.
     *
     * @param int $schemeID
     * @param Request $request
     * @return Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function updateWeightings(int $schemeID, Request $request)
    {
        $scheme = Scheme::find($schemeID);
        if (!isset($scheme) || !$this->checkAccessToScheme($schemeID, $request->user())) {
            return redirect()->route('schemes.index');
        }
        $schemeQuestions = SchemeQuestion::whereSchemeId($schemeID)->get();

        $validationArray = [];
        foreach ($schemeQuestions as $schemeQuestion) {
            $validationArray['q' . $schemeQuestion->question_id . '-weight'] = 'nullable|numeric|min:0|max:9999.99';
        }
        $this->validate($request, $validationArray);

        foreach ($schemeQuestions as $schemeQuestion) {
            $questionWeight = $request->input('q' . $schemeQuestion->question_id . '-weight');
            if (isset($questionWeight)) {
                settype($questionWeight, 'float');
                if ($schemeQuestion->weight != $questionWeight) {
                    $schemeQuestion->weight = $questionWeight;
                    $schemeQuestion->save();
                }
            }
        }

        return redirect()->route('schemes.questions.index', ['scheme_id' => $schemeID])->with('success', 'Successfully updated the questions weighting in the questionnaire!');
    }

    /**
     * @param int $schemeID
     * @param User $user
     * @return bool True if the user can change the questions in the questionnaire for the specified scheme.
     */
    private function canChangeQuestions(int $schemeID, User $user): bool
    {
        $canChange = true;
        if (SchemeUser::whereSchemeId($schemeID)->first() !== null) {
            $canChange = false;
        }
        return $canChange;
    }
}
