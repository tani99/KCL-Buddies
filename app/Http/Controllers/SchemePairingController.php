<?php

namespace App\Http\Controllers;

use App\Jobs\RunAlgorithm;
use App\Scheme as Scheme;
use App\SchemePairing as SchemePairing;
use App\SchemeUser as SchemeUser;
use App\User as User;
use App\UserPreferences;
use App\UserType as UserType;
use App\Mail\PairingsNotify as PairingsNotify;
use Illuminate\Http\Request as Request;
use Illuminate\Http\Response as Response;
use Illuminate\View\View as View;
use Illuminate\Support\Facades\Mail as Mail;

class SchemePairingController extends Controller
{
    use RedirectMessages;
    use SchemeAuthentication;
    use EmailValidation;

    /**
     * Create a new scheme-pairing controller instance.
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
        $user = $request->user();
        $schemeAccess = $this->getSchemeAccess($schemeID, $user);
        if ($schemeAccess !== -1 && $schemeAccess !== 1) {
            return redirect()->back();
        }
        $scheme = Scheme::find($schemeID);

        $userTypeNames = UserType::getAllNames();

        // Get a mapping of pairing IDs to an array of scheme users.
        $allSchemeUsers = SchemeUser::whereSchemeId($schemeID)->whereApproved(true)->get();
        $pairingIDsToUsers = [];
        $userIDs = [];
        foreach ($allSchemeUsers as $schemeUser) {
            $userIDs[] = $schemeUser->user_id;
            if (isset($schemeUser->pairing_id)) {
                $pairingIDsToUsers[$schemeUser->pairing_id][] = $schemeUser;
            }
        }
        $users = User::whereIn('id', $userIDs)->get()->keyBy('id')->all();

        $pairings = []; // A mapping of scheme pairing IDs to an array with index 0 being an array of buddies, and index 1 being an array of newbies
        $unpaired = []; // An array containing arrays of unpaired users data with the user and user type name in an array
        $populatedUserIDs = []; // An array of user IDs that have been sorted into paired/unpaired
        foreach (SchemePairing::whereSchemeId($schemeID)->get() as $schemePairing) {
            $buddies = [];
            $newbies = [];
            $pairingUsers = $pairingIDsToUsers[$schemePairing->id];
            if (isset($pairingUsers)) {
                foreach ($pairingUsers as $schemeUser) {
                    if ($schemeUser->approved) {
                        $user = $users[$schemeUser->user_id];
                        if (isset($schemeUser->pairing_id)) {
                            if ($schemeUser->user_type_id == 1) {
                                $newbies[] = $user;
                            } elseif ($schemeUser->user_type_id == 2) {
                                $buddies[] = $user;
                            }
                        } else {
                            $userTypeName = $userTypeNames[$schemeUser->user_type_id]['singular'];
                            $unpaired[] = ['user' => $user, 'userTypeName' => $userTypeName];
                        }
                        $populatedUserIDs[] = $schemeUser->user_id;
                    }
                }
            }
            if (empty($buddies) || empty($newbies)) {
                SchemeUser::wherePairingId($schemePairing->id)->update(['pairing_id' => null]);
                $schemePairing->delete();
            }
            $pairings[$schemePairing->id] = [$buddies, $newbies];
        }
        unset($pairingIDsToUsers);
        // Populate the unpaired array completely.
        foreach ($allSchemeUsers as $schemeUser) {
            if (!in_array($schemeUser->user_id, $populatedUserIDs)) {
                $user = $users[$schemeUser->user_id];
                $userTypeName = $userTypeNames[$schemeUser->user_type_id]['singular'];
                $unpaired[] = ['user' => $user, 'userTypeName' => $userTypeName];
            }
        }

        $data = $this->applySessionToData($request);
        $data['accessLevel'] = $schemeAccess === -1 ? 'sysadmin' : 'user';
        $data['scheme'] = $scheme;
        $data['pairings'] = $pairings;
        $data['unpaired'] = $unpaired;
        $data['userTypeNames'] = $userTypeNames;
        $data['canEmail'] = $this->canEmail(config('mail.host'));

        return view('scheme.users.pairings', $data);
    }

    /**
     * Display a listing of the resource for regular users.
     *
     * @param int $schemeID
     * @param Request $request
     * @return Response|View
     */
    public function userIndex(int $schemeID, Request $request)
    {
        $scheme = Scheme::find($schemeID);
        if (!isset($scheme)) {
            return redirect()->route('schemes.index');
        }
        $user = $request->user();
        $schemeUser = SchemeUser::whereUserId($user->id)->whereSchemeId($schemeID)->whereApproved(true)->whereNotNull('pairing_id')->first();
        if (!isset($schemeUser)) {
            return redirect()->route('schemes.index');
        }

        $pairingSchemeUsers = SchemeUser::wherePairingId($schemeUser->pairing_id)->whereApproved(true)->get();
        $userIDs = [];
        foreach ($pairingSchemeUsers as $schemeUser) {
            $userIDs[] = $schemeUser->user_id;
        }
        $users = User::whereIn('id', $userIDs)->get()->keyBy('id')->all();
        $usersPreferences = UserPreferences::whereIn('user_id', $userIDs)->get()->keyBy('user_id')->all();
        unset($userIDs);

        $userTypeNames = UserType::getAllNames();

        // Get a mapping of user type IDs to an array of users in the pair belonging to that type.
        $pairingUsers = [];
        foreach ($pairingSchemeUsers as $schemeUser) {
            $user = $users[$schemeUser->user_id];
            $pairingUsers[$schemeUser->user_type_id][] = $user;
        }
        // Check for empty user types.
        foreach ($userTypeNames as $userTypeID => $userTypeName) {
            $userTypePairings = $pairingUsers[$userTypeID];
            if (!isset($userTypePairings) || empty($userTypePairings)) {
                SchemeUser::wherePairingId($schemeUser->pairing_id)->update(['pairing_id' => null]);
                SchemePairing::destroy($schemeUser->pairing_id);
                return redirect()->route('schemes.index');
            }
        }
        krsort($pairingUsers); // Sort the user types in reverse

        $data = $this->applySessionToData($request);
        $data['accessLevel'] = 'user';
        $data['scheme'] = $scheme;
        $data['pairingUsers'] = $pairingUsers;
        $data['userTypeNames'] = $userTypeNames;
        $data['usersPreferences'] = $usersPreferences;
        return view('scheme.users.pairing_users', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param int $schemeID
     * @param Request $request
     * @return Response
     */
    public function store(int $schemeID, Request $request)
    {
        $user = $request->user();
        if (!$this->checkAccessToScheme($schemeID, $user)) {
            return redirect()->back();
        }
        $scheme = Scheme::find($schemeID);
        if ($scheme->type_id != 3 && !$this->isSystemAdministrator($user)) {
            return redirect()->route('schemes.show', ['scheme_id' => $schemeID]);
        }

        $this->dispatchNow((new RunAlgorithm($schemeID))->onQueue('algorithmManual'));
        return redirect()->route('schemes.pairs.index', ['scheme_id' => $schemeID]);
    }

    /**
     * Remove the specified resource from storage for the specified scheme.
     *
     * @param int $schemeID
     * @param int $pairingID
     * @param Request $request
     * @return Response
     */
    public function destroy(int $schemeID, int $pairingID, Request $request)
    {
        $schemePairing = SchemePairing::find($pairingID);
        if (!isset($schemePairing) || $schemeID != $schemePairing->scheme_id) {
            return redirect()->back();
        }
        if (!$this->checkAccessToScheme($schemeID, $request->user())) {
            return redirect()->back();
        }

        SchemeUser::wherePairingId($schemePairing->id)->update(['pairing_id' => null]);
        $schemePairing->delete();

        return redirect()->route('schemes.pairs.index', ['scheme_id' => $schemeID])->with('success', 'Successfully deleted that pair.');
    }

    /**
     * Remove all resources from storage belonging to the specified scheme.
     *
     * @param int $schemeID
     * @param Request $request
     * @return Response
     */
    public function destroyAll(int $schemeID, Request $request)
    {
        if (Scheme::find($schemeID) === null) {
            return redirect()->route('schemes.index');
        }
        if (!$this->checkAccessToScheme($schemeID, $request->user())) {
            return redirect()->back();
        }

        SchemePairing::whereSchemeId($schemeID)->delete();

        return redirect()->route('schemes.pairs.index', ['scheme_id' => $schemeID])->with('success', 'Successfully deleted all pairs.');
    }

    /**
     * Email all users with their pairings.
     *
     * @param Scheme $scheme
     * @param array $pairingUserIDs An array of all the user IDs to email
     * @param array $buddyPairings All buddy groups, as produced by the algorithm.
     * @param array $newbiesPairings All newbie groups, as produced by the algorithm.
     * @return int The number of emails sent.
     */
    public static function sendEmailToPairings(Scheme $scheme, array $pairingUserIDs, array $buddyPairings, array $newbiesPairings): int
    {
        $emailCount = 0;
        try {
            $usersPreferences = UserPreferences::whereIn('user_id', $pairingUserIDs)->get()->keyBy('user_id')->all();
            $newbiesNames = UserType::find(1)->getNames();
            $buddiesNames = UserType::find(2)->getNames();
            for ($i = 0; $i < count($buddyPairings); ++$i) {
                $buddies = $buddyPairings[$i];
                $newbies = $newbiesPairings[$i];
                if (empty($buddies) || empty($newbies)) continue;
                foreach ($buddies as $buddy) {
                    if ($usersPreferences[$buddy->id]->email_notifs && (!isset($schemeUsersSubPreference) || $schemeUsersSubPreference[$buddy->id])) {
                        Mail::to($buddy->email)->send(new PairingsNotify($buddy, $scheme, $newbies, $newbiesNames));
                        ++$emailCount;
                    }
                }
                foreach ($newbies as $newbie) {
                    if ($usersPreferences[$newbie->id]->email_notifs && (!isset($schemeUsersSubPreference) || $schemeUsersSubPreference[$newbie->id])) {
                        Mail::to($newbie->email)->send(new PairingsNotify($newbie, $scheme, $buddies, $buddiesNames));
                        ++$emailCount;
                    }
                }
            }
        } catch (\Exception $e) {
            report($e);
        }
        return $emailCount;
    }
}
