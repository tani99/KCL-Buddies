<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request as Request;
use Illuminate\Http\Response as Response;
use Illuminate\View\View as View;
use Illuminate\Support\Facades\Mail as Mail;
use App\User as User;
use App\UserType as UserType;
use App\UserPreferences as UserPreferences;
use App\Scheme as Scheme;
use App\SchemeUser as SchemeUser;
use App\Mail\SchemeMessage as SchemeMessage;
use App\Mail\GlobalMessage as GlobalMessage;

final class EmailController extends Controller
{
    use SchemeAuthentication;
    use EmailValidation;

    /**
     * Display the scheme mail page.
     *
     * @param int $schemeID
     * @param Request $request
     * @return Response|View
     */
    public function emailSchemeUsersPage(int $schemeID, Request $request)
    {
        $scheme = Scheme::find($schemeID);
        if (!isset($scheme)) {
            return redirect()->route('schemes.index');
        }
        // Ensure the request user has administrator privileges to the scheme.
        $accessLevel = $this->getSchemeAccess($schemeID, $request->user());
        if ($accessLevel !== -1 && $accessLevel !== 1) {
            return redirect()->back();
        }
        if (!$this->canEmail(config('mail.host'))) {
            return redirect()->route('schemes.index');
        }

        $data = [];
        $data['accessLevel'] = $accessLevel === -1 ? 'sysadmin' : 'user';
        $data['scheme'] = $scheme;
        $data['userTypes'] = UserType::all()->keyBy('id')->all();

        return view('scheme.mailer', $data);
    }

    /**
     * Send an email to scheme users.
     *
     * @param int $schemeID
     * @param Request $request
     * @return Response|View
     * @throws \Illuminate\Validation\ValidationException
     */
    public function emailSchemeUsers(int $schemeID, Request $request)
    {
        $scheme = Scheme::find($schemeID);
        if (!isset($scheme)) {
            return redirect()->route('schemes.index');
        }
        if (!$this->checkAccessToScheme($schemeID, $request->user())) {
            return redirect()->route('schemes.index');
        }
        if (!$this->canEmail(config('mail.host'))) {
            return redirect()->route('schemes.index');
        }
        $this->validate($request, [
            'user_type' => 'required',
            'subject' => 'nullable|max:80',
            'content' => 'required|min:1|max:1000'
        ]);

        $userTypeID = $request->input('user_type');

        $schemeUserBuilder = SchemeUser::whereSchemeId($schemeID)->whereApproved(true);
        if ($userTypeID != '-1') {
            if (UserType::find($userTypeID) === null) {
                return redirect()->back()->withInput()->withErrors(['user_type' => 'Unknown user type']);
            }
            $schemeUserBuilder = $schemeUserBuilder->whereUserTypeId($userTypeID);
        }

        // Get all the user IDs to email along with their scheme preference of having email subscriptions.
        $userIDs = [];
        $schemeUsersSubPreference = [];
        foreach ($schemeUserBuilder->get() as $schemeUser) {
            $userIDs[] = $schemeUser->user_id;
            $schemeUsersSubPreference[$schemeUser->user_id] = $schemeUser->getPreference('subscribed');
        }

        // Get all the preferences of each user.
        $usersPreferences = [];
        foreach (UserPreferences::whereIn('user_id', $userIDs)->get() as $userPreferences) {
            $usersPreferences[$userPreferences->user_id] = $userPreferences;
        }

        // Send the email to all users.
        $emailCount = 0;
        foreach (User::whereIn('id', $userIDs)->get() as $user) {
            if ($usersPreferences[$user->id]->email_notifs && $schemeUsersSubPreference[$user->id]) {
                Mail::to($user->email)->queue(new SchemeMessage($request, $scheme));
                ++$emailCount;
            }
        }

        return redirect()->route('schemes.show', ['scheme_id' => $schemeID])->with('success', 'Successfully emailed ' . $emailCount . ' user' . ($emailCount != 1 ? 's' : '') . '!');
    }

    /**
     * Display the users mail page.
     *
     * @param Request $request
     * @return Response|View
     */
    public function emailUsersPage(Request $request)
    {
        if (!$this->isSystemAdministrator($request->user())) {
            return redirect()->back();
        }
        if (!$this->canEmail(config('mail.host'))) {
            return redirect()->route('admin.users.index');
        }

        $data = [];
        $data['accessLevel'] = 'sysadmin';
        return view('admin.users.mail', $data);
    }

    /**
     * Send an email to all users.
     *
     * @param Request $request
     * @return Response|View
     * @throws \Illuminate\Validation\ValidationException
     */
    public function emailUsers(Request $request)
    {
        if (!$this->isSystemAdministrator($request->user())) {
            return redirect()->back();
        }
        if (!$this->canEmail(config('mail.host'))) {
            return redirect()->route('admin.users.index');
        }
        $this->validate($request, [
            'subject' => 'nullable|max:80',
            'content' => 'required|min:1|max:2000'
        ]);

        // Send an email to all users that are not banned.
        $userEmails = User::whereBanned(false)->pluck('email')->all();
        Mail::to($request->user()->email)->bcc($userEmails)->send(new GlobalMessage($request));
        $emailedUsersCount = count($userEmails);

        return redirect()->route('admin.users.index')->with('success', 'Successfully emailed ' . $emailedUsersCount . ' user' . ($emailedUsersCount != 1 ? 's' : '') . '.');
    }
}
