<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request as Request;
use Illuminate\Http\Response as Response;
use Illuminate\View\View as View;
use App\User as User;
use App\UserType as UserType;
use App\Scheme as Scheme;
use App\SchemeUser as SchemeUser;
use App\SchemePairing as SchemePairing;
use App\SchemeQuestion as SchemeQuestion;
use App\BannedSchemeUser as BannedSchemeUser;
use App\QuestionAnswer as QuestionAnswer;

class SchemeUserController extends Controller
{
    use SchemeAuthentication;
    use RedirectMessages;
    use EmailValidation;

    /**
     * Create a new scheme-user controller instance.
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
        $schemeAccess = $this->getSchemeAccess($schemeID, $request->user());
        if ($schemeAccess !== -1 && $schemeAccess !== 1) {
            return redirect()->back();
        }
        $scheme = Scheme::find($schemeID);
        if (!isset($scheme)) {
            return redirect()->route('schemes.index');
        }

        $data = $this->applySessionToData($request);
        $data['scheme'] = $scheme;
        $data['accessLevel'] = $schemeAccess === -1 ? 'sysadmin' : 'user';
        $data['canEmail'] = $this->canEmail(config('mail.host'));
        $data['canApproveUsers'] = true;
        $data['canKickUsers'] = true;
        $data['canBanUsers'] = true;

        $userTypeNames = []; // A mapping of user type IDs to their singular names
        foreach (UserType::all() as $userType) {
            $userTypeNames[$userType->id] = $userType->getNames()['singular'];
        }

        $users = []; // A mapping of user IDs to their data (including the user type name, join date and approved)
        foreach (SchemeUser::whereSchemeId($schemeID)->get() as $schemeUser) {
            $userData = [];
            $userData['schemeUserID'] = $schemeUser->id;
            $userData['userTypeName'] = $userTypeNames[$schemeUser->user_type_id];
            try {
                $userData['joinDate'] = isset($schemeUser->created_at) ? (new \DateTime($schemeUser->created_at))->format('d/m/Y') : 'Unknown';
            } catch (\Exception $ignored) {
                $userData['joinDate'] = 'Unknown';
            }
            $userData['approved'] = $schemeUser->approved;

            $users[$schemeUser->user_id] = $userData;
        }
        // Add 'user' field to the data array of each user.
        foreach (User::whereIn('id', array_keys($users))->get() as $user) {
            $users[$user->id]['user'] = $user;
        }
        $data['users'] = $users;

        $bannedUserIDs = BannedSchemeUser::whereSchemeId($schemeID)->pluck('user_id')->toArray();
        $bannedUsers = User::whereIn('id', $bannedUserIDs)->get()->keyBy('id')->all();
        $data['bannedUsers'] = $bannedUsers;

        return view('scheme.users.index', $data);
    }

    /**
     * Approve the specified resource.
     *
     * @param int $schemeUserID
     * @param Request $request
     * @return Response
     */
    public function approve(int $schemeUserID, Request $request)
    {
        $schemeUser = SchemeUser::find($schemeUserID);
        if (!isset($schemeUser)) {
            return redirect()->back();
        }
        if (!$this->checkAccessToScheme($schemeUser->scheme_id, $request->user())) {
            return redirect()->back();
        }
        if ($schemeUser->approved) {
            return redirect()->back();
        }
        $schemeUser->approved = true;
        $schemeUser->save();

        $userName = $schemeUser->user->getFullName();
        return redirect()->route('schemes.users.index', ['scheme_id' => $schemeUser->scheme_id])->with('success', 'Successfully approved \'' . $userName . '\'!');
    }

    /**
     * Approve all resources in the specified scheme.
     *
     * @param int $schemeID
     * @param Request $request
     * @return Response
     */
    public function approveAll(int $schemeID, Request $request)
    {
        if (Scheme::find($schemeID) === null) {
            return redirect()->back();
        }
        if (!$this->checkAccessToScheme($schemeID, $request->user())) {
            return redirect()->back();
        }
        SchemeUser::whereSchemeId($schemeID)->whereApproved(false)->update(['approved' => true]);
        return redirect()->route('schemes.users.index', ['scheme_id' => $schemeID])->with('success', 'Successfully approved all users!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $schemeUserID
     * @param Request $request
     * @return Response
     */
    public function kick(int $schemeUserID, Request $request)
    {
        $schemeUser = SchemeUser::find($schemeUserID);
        if (!isset($schemeUser)) {
            return redirect()->back();
        }
        if (!$this->checkAccessToScheme($schemeUser->scheme_id, $request->user())) {
            return redirect()->back();
        }

        $this->removeAnswers($schemeUser->scheme_id, $schemeUser->user_id);
        $schemeUser->delete();

        // Delete the pairing group if the user has been paired.
        $schemePairingID = $schemeUser->pairing_id;
        if (isset($schemePairingID)) {
            SchemeUser::wherePairingId($schemePairingID)->update(['pairing_id' => null]);
            $schemePairing = SchemePairing::find($schemePairingID);
            $schemePairing->delete();
        }

        $userName = $schemeUser->user->getFullName();
        return redirect()->route('schemes.users.index', ['scheme_id' => $schemeUser->scheme_id])->with('success', 'Successfully kicked \'' . $userName . '\'!');
    }

    /**
     * Remove all resources from storage belonging to the specified scheme.
     *
     * @param int $schemeID
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function kickAll(int $schemeID, Request $request)
    {
        if (Scheme::find($schemeID) === null) {
            return redirect()->back();
        }
        if (!$this->checkAccessToScheme($schemeID, $request->user())) {
            return redirect()->back();
        }

        $this->removeAnswers($schemeID);
        SchemeUser::whereSchemeId($schemeID)->delete();
        SchemePairing::whereSchemeId($schemeID)->delete();

        return redirect()->route('schemes.users.index', ['scheme_id' => $schemeID])->with('success', 'Successfully kicked all users!');
    }

    /**
     * Ban the specified resource in storage.
     *
     * @param int $schemeUserID
     * @param Request $request
     * @return Response
     */
    public function ban(int $schemeUserID, Request $request)
    {
        $schemeUser = SchemeUser::find($schemeUserID);
        if (!isset($schemeUser)) {
            return redirect()->back();
        }
        if (!$this->checkAccessToScheme($schemeUser->scheme_id, $request->user())) {
            return redirect()->back();
        }
        $this->removeAnswers($schemeUser->scheme_id, $schemeUser->user_id);
        $schemeUser->delete();

        // Remove the pairing group if the user has been paired.
        $schemePairingID = $schemeUser->pairing_id;
        if (isset($schemePairingID)) {
            SchemeUser::wherePairingId($schemePairingID)->update(['pairing_id' => null]);
            $schemePairing = SchemePairing::find($schemePairingID);
            $schemePairing->delete();
        }

        $bannedSchemeUser = new BannedSchemeUser();
        $bannedSchemeUser->scheme_id = $schemeUser->scheme_id;
        $bannedSchemeUser->user_id = $schemeUser->user_id;
        $bannedSchemeUser->save();

        $userName = $schemeUser->user->getFullName();
        return redirect()->route('schemes.users.index', ['scheme_id' => $schemeUser->scheme_id])->with('success', 'Successfully banned \'' . $userName . '\'!');
    }

    /**
     * Unban the specified resource in storage.
     *
     * @param int $schemeID
     * @param int $userID
     * @param Request $request
     * @return Response
     */
    public function unban(int $schemeID, int $userID, Request $request)
    {
        if (!$this->checkAccessToScheme($schemeID, $request->user())) {
            return redirect()->back();
        }
        $bannedSchemeUser = BannedSchemeUser::whereSchemeId($schemeID)->whereUserId($userID)->first();
        if (!isset($bannedSchemeUser)) {
            return redirect()->back();
        }
        $userName = $bannedSchemeUser->user->getFullName();
        $bannedSchemeUser->delete();

        return redirect()->route('schemes.users.index', ['scheme_id' => $schemeID])->with('success', 'Successfully unbanned \'' . $userName . '\'.');
    }

    /**
     * Remove answers to questions in the specified scheme from storage.
     * If the user ID is specified, only remove answers answered from that user.
     *
     * @param int $schemeID
     * @param int|null $userID
     */
    private function removeAnswers(int $schemeID, int $userID = null): void
    {
        $schemeQuestionIDs = [];
        foreach (SchemeQuestion::whereSchemeId($schemeID)->get() as $schemeQuestion) {
            $schemeQuestionIDs[] = $schemeQuestion->id;
        }
        $questionAnswerBuilder = QuestionAnswer::whereIn('scheme_question_id', $schemeQuestionIDs);
        if (isset($userID)) {
            $questionAnswerBuilder = $questionAnswerBuilder->whereUserId($userID);
        }
        $questionAnswerBuilder->delete();
    }
}
