<?php

namespace App\Http\Controllers;

use App\Role as Role;
use App\User as User;
use App\UserPreferences as UserPreferences;
use App\UserType as UserType;
use App\Scheme as Scheme;
use App\SchemeUser as SchemeUser;
use App\QuestionAnswer as QuestionAnswer;
use Illuminate\Http\Request as Request;
use Illuminate\Http\Response as Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View as View;
use Illuminate\Support\Facades\Hash as Hash;

class UserController extends Controller
{
    use SchemeAuthentication;
    use RedirectMessages;
    use EmailValidation;

    /**
     * Create a new user controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return Response|View
     */
    public function index(Request $request)
    {
        if (!$this->isSystemAdministrator($request->user())) {
            return redirect()->back();
        }

        $data = $this->applySessionToData($request);
        $data['accessLevel'] = 'sysadmin';
        $data['canEmail'] = $this->canEmail(config('mail.host'));
        $data['adminUser'] = Auth::user();

        $users = [];
        foreach (User::all() as $user) {
            if (!$user->hasRole('sysadmin')) {
                $users[] = $user;
            }
        }
        $data['users'] = $users;

        return view('admin.users.index', $data);
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
            return redirect()->back();
        }

        $data = $this->applySessionToData($request);
        $data['accessLevel'] = 'sysadmin';

        return view('admin.users.create', $data);
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
            return redirect()->back();
        }
        $this->validate($request, [
            'name' => 'required|min:3|max:50|regex:/^[a-zA-Z ]+$/u',
            'email' => 'required|email|unique:users,email',
            'password' => [
                'required',
                'min:6',
                'confirmed'
            ]
        ]);

        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
        ]);
        $user->roles()->attach(Role::whereName('user')->first());
        UserPreferences::create(['user_id' => $user->id]);

        return redirect()->route('admin.users.show', ['user_id' => $user->id]);
    }

    /**
     * Display the specified resource.
     *
     * @param int $userID
     * @param Request $request
     * @return Response|View
     */
    public function show(int $userID, Request $request)
    {
        if (!$this->isSystemAdministrator($request->user())) {
            return redirect()->back();
        }
        $user = User::find($userID);
        if (!isset($user)) {
            return redirect()->route('admin.users.index');
        }
        if ($user->hasRole('sysadmin')) {
            return redirect()->back();
        }

        $data = $this->applySessionToData($request);
        $data['accessLevel'] = 'sysadmin';
        $data['user'] = $user;
        $data['userTypesNames'] = UserType::getAllNames();

        if (isset($user->created_at)) {
            try {
                $data['joinDate'] = (new \DateTimeImmutable($user->created_at))->format('d/m/Y H:i:s');
            } catch (\Exception $ignored) {
            }
        }
        if (!isset($data['joinDate'])) $data['joinDate'] = 'Unknown';

        $schemesData = [];
        foreach (SchemeUser::whereUserId($userID)->get() as $schemeUser) {
            $schemesData[] = [
                'scheme' => $schemeUser->scheme,
                'schemeUser' => $schemeUser
            ];
        }
        $data['schemesData'] = $schemesData;

        return view('admin.users.view', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $userID
     * @param Request $request
     * @return Response|View
     */
    public function edit(int $userID, Request $request)
    {
        if (!$this->isSystemAdministrator($request->user())) {
            return redirect()->back();
        }
        $user = User::find($userID);
        if (!isset($user)) {
            return redirect()->route('admin.users.index');
        }
        if ($user->hasRole('sysadmin')) {
            return redirect()->back();
        }

        $data = $this->applySessionToData($request);
        $data['accessLevel'] = 'sysadmin';
        $data['user'] = $user;

        return view('admin.users.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param int $userID
     * @param Request $request
     * @return Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(int $userID, Request $request)
    {
        if (!$this->isSystemAdministrator($request->user())) {
            return redirect()->route('user.home');
        }
        $user = User::find($userID);
        if (!isset($user)) {
            return redirect()->route('admin.users.index');
        }

        $validationArray = [
            'nickname' => 'nullable|max:16|regex:/^[a-zA-Z][a-zA-Z\s]*$/',
            'bio' => 'nullable|max:250',
            'alt_email' => 'nullable|email|max:254|unique:users,email,' . $user->id
        ];
        if (!$user->isMicrosoftAccount()) {
            $validationArray['name'] = 'required|max:191|regex:/^[a-zA-Z\s]+$/';
            $validationArray['department'] = 'nullable|max:100';
            $validationArray['password'] = 'nullable|min:6';
        }
        $this->validate($request, $validationArray);

        if (!$user->isMicrosoftAccount()) { // Only allow certain properties to be changed if the user is not a Microsoft user.
            $user->name = $request->input('name');
            $user->department = $request->input('department');
            if (isset($user->department) && strlen($user->department) === 0) {
                $user->department = null;
            }

            $password = $request->input('password');
            if (isset($password)) {
                $user->password = Hash::make($password);
            }
        }

        $inputNickname = $request->input('nickname');
        if (!isset($inputNickname) || strlen($inputNickname) === 0) {
            $user->nickname = null;
        }
        $inputBio = $request->input('bio');
        if (!isset($inputBio) || strlen($inputBio) === 0) {
            $user->bio = '';
        }
        $inputAltEmail = $request->input('alt_email');
        if (!isset($inputAltEmail) || strlen($inputAltEmail) === 0) {
            $user->alt_email = null;
        }

        $user->save();

        return redirect()->route('admin.users.show', ['user_id' => $userID])->with('success', 'Successfully updated the profile of \'' . $user->getFullName() . '\'.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $userID
     * @param Request $request
     * @return Response
     */
    public function destroy(int $userID, Request $request)
    {
        if (!$this->isSystemAdministrator($request->user())) {
            return redirect()->back();
        }
        $user = User::find($userID);
        if (!isset($user)) {
            return redirect()->route('admin.users.index');
        }
        if ($user->isMicrosoftAccount() || $user->hasRole('sysadmin')) { // Prevent the destruction of Microsoft user accounts and system administrators.
            return redirect()->back();
        }

        QuestionAnswer::whereUserId($user->id)->delete();
        SchemeUser::whereUserId($user->id)->delete();
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Successfully deleted user \'' . $user->getFullName() . '\'.');
    }

    /**
     * Update the request user (who is a system administrator)'s credentials.
     *
     * @param Request $request
     * @return Response
     */
    public function updateCredentials(Request $request)
    {
        $user = Auth::user();
        if (!$this->isSystemAdministrator($user)) {
            return redirect()->back();
        }
        $this->validate($request, [
            'name' => 'required|min:3|max:50|regex:/^[a-zA-Z ]+$/u',
            'password' => 'nullable|min:6'
        ]);

        $user->name = $request->input('name');
        $inputPassword = $request->input('password');
        if (isset($inputPassword)) {
            $user->password = Hash::make($inputPassword);
        }
        $changed = $user->isDirty();
        $user->save();

        $redirectResponse = redirect()->route('admin.users.index');
        if ($changed) {
            $redirectResponse = $redirectResponse->with('success', 'Successfully updated your details.');
        }
        return $redirectResponse;
    }

    /**
     * Ban/unban the specified resource.
     *
     * @param int $userID
     * @param Request $request
     * @return Response
     */
    public function toggleBan(int $userID, Request $request)
    {
        if (!$this->isSystemAdministrator($request->user())) {
            return redirect()->back();
        }
        $user = User::find($userID);
        if (!isset($user)) {
            return redirect()->route('admin.users.index');
        }
        if ($user->hasRole('sysadmin')) {
            return redirect()->back();
        }
        $user->banned = !$user->banned;
        $user->save();

        return redirect()->back()->with('success', 'Successfully ' . ($user->banned ? 'banned' : 'unbanned') . ' \'' . $user->getFullName() . '\'.');
    }
}
