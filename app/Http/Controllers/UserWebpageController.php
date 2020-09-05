<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request as Request;
use Illuminate\Http\Response as Response;
use Illuminate\View\View as View;
use Illuminate\Support\Facades\Auth as Auth;
use Illuminate\Support\Facades\Storage as Storage;
use App\User as User;
use App\UserType as UserType;
use App\UserPreferences as UserPreferences;
use App\Scheme as Scheme;
use App\SchemeUser as SchemeUser;

class UserWebpageController extends Controller
{
    use RedirectMessages;

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the user dashboard.
     *
     * @param Request $request
     * @return Response|View
     */
    public function userIndex(Request $request)
    {
        $data = [];
        if ($this->isNormalUser($request->user())) {
            $data['accessLevel'] = 'user';

            $schemeUsers = []; // A mapping of scheme IDs to the SchemeUser instance of the request user
            foreach (SchemeUser::whereUserId($request->user()->id)->get() as $schemeUser) {
                $schemeUsers[$schemeUser->scheme_id] = $schemeUser;
            }
            $schemesData = []; // A mapping of scheme IDs to an array containing the scheme and the SchemeUser instance of the request user
            foreach (Scheme::whereIn('id', array_keys($schemeUsers))->get() as $scheme) {
                $schemesData[$scheme->id] = [
                    'scheme' => $scheme,
                    'schemeUser' => $schemeUsers[$scheme->id]
                ];
            }
            $data['schemesData'] = $schemesData;
            $data['userTypesNames'] = UserType::getAllNames();

            return view('user.dashboard', $data);
        } else if ($this->isSystemAdministrator($request->user())) {
            return redirect()->route('admin.home');
        } else return redirect()->back();
    }

    /**
     * Show the user profile page.
     *
     * @param Request $request
     * @return Response|View
     */
    public function userProfile(Request $request)
    {
        $user = Auth::user();
        if ($this->isNormalUser($user)) {
            $preferences = UserPreferences::whereUserId($user->id)->first();

            $data = $this->applySessionToData($request);
            $data['accessLevel'] = 'user';
            $data['user'] = $user;
            $data['preferences'] = $preferences;
            return view('user.profile', $data);
        } else return redirect()->back();
    }

    /**
     * Save the user profile in storage.
     *
     * @param Request $request
     * @return Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function saveUserProfile(Request $request)
    {
        $user = Auth::user();
        if ($this->isNormalUser($user)) {
            $this->validate($request,
                [
                    'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048|dimensions:width=256,height=256',
                    'nickname' => 'nullable|max:16|regex:/^[a-zA-Z][a-zA-Z\s]+$/',
                    'department' => 'nullable|max:100',
                    'bio' => 'max:250',
                    'birthdate' => 'nullable|date',
                    'gender' => 'integer|min:1|max:4',
                    'alt_email' => 'nullable|email|max:254|unique:users,email,' . $user->id,
                    'phone_number' => 'nullable|numeric|digits_between:11,12'
                ]
            );

            $user->nickname = self::validateUserInput('nickname', $request->input('nickname'));
            $user->bio = self::validateUserInput('bio', $request->input('bio'));
            $user->birthdate = self::validateUserInput('birthdate', $request->input('birthdate'));
            $user->country = self::validateUserInput('country', $request->input('country'));
            $user->gender = self::validateUserInput('gender', $request->input('gender'));
            $user->alt_email = self::validateUserInput('alt_email', $request->input('alt_email'));
            $user->phone_number = self::validateUserInput('phone_number', $request->input('phone_number'));
            if (!$user->isMicrosoftAccount()) {
                $user->department = self::validateUserInput('department', $request->input('department'));
            }

            if (isset($request->avatar)) {
                $avatarName = $user->id . '_avatar' . time() . '.' . $request->avatar->getClientOriginalExtension();
                $request->avatar->storeAs('avatars', $avatarName);
                $user->avatar = $avatarName;
            }
            $modifiedUser = $user->isDirty();
            $user->save();

            $userPreferences = $user->preferences;
            if (isset($userPreferences)) {
                $userPreferences->email_notifs = $request->input('email_notifs') == '1' ? 1 : 0;
                $userPreferences->birthdate_private = $request->input('birth_date_private') == '1' ? 1 : 0;
                $userPreferences->gender_private = $request->input('gender_private') == '1' ? 1 : 0;
                $modifiedUser = $modifiedUser || $userPreferences->isDirty();
                $userPreferences->save();
            }

            $response = redirect()->route('user.profile.index');
            if ($modifiedUser) {
                $response = $response->with('success', 'Successfully updated your profile!');
            }
            return $response;
        } else return redirect()->back();
    }

    /**
     * Delete the profile picture of the currently logged in user.
     *
     * @return Response
     */
    public function deleteProfilePicture()
    {
        $user = Auth::user();
        if ($this->isNormalUser($user)) {
            if (isset($user->avatar)) {
                Storage::disk('public')->delete('avatars/' . $user->avatar);

                $user->avatar = null;
                $user->save();
            }

            return redirect()->route('user.profile.index')->with('success', 'Successfully removed your profile picture!');
        } else return redirect()->back();
    }

    /**
     * Show the admin dashboard.
     *
     * @param Request $request
     * @return Response|View
     */
    public function adminIndex(Request $request)
    {
        $data = [];
        if ($this->isSystemAdministrator($request->user())) {
            $data['accessLevel'] = 'sysadmin';
            return view('admin.dashboard', $data);
        } else return redirect()->back();
    }

    /**
     * @param User $user
     * @return bool True if the specified user is a normal user of the system.
     */
    private function isNormalUser(User $user): bool
    {
        return $user->hasRole('user');
    }

    /**
     * @param User $user
     * @return bool True if the specified user is an admin of the system.
     */
    private function isSystemAdministrator(User $user): bool
    {
        return $user->hasRole('sysadmin');
    }

    /**
     * Process user input and get a validated version of the input value.
     *
     * @param string $propertyName
     * @param string|null $inputValue
     * @return mixed|null
     */
    private static function validateUserInput(string $propertyName, ?string $inputValue)
    {
        if ($propertyName === 'nickname') {
            if (isset($inputValue)) {
                $nickname = trim($inputValue);
                return strlen($nickname) > 0 ? $nickname : null;
            } else {
                return null;
            }
        } else if ($propertyName === 'bio') {
            return $inputValue ?? '';
        } else if ($propertyName === 'department' || $propertyName === 'country' || $propertyName === 'alt_email' || $propertyName === 'phone_number') {
            $trimmedInput = isset($inputValue) ? trim($inputValue) : '';
            return strlen($trimmedInput) > 0 ? $trimmedInput : null;
        } else {
            return $inputValue;
        }
    }
}
