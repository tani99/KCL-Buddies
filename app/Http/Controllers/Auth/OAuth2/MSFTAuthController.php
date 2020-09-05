<?php

namespace App\Http\Controllers\Auth\OAuth2;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Microsoft\Graph\Graph;
use Microsoft\Graph\Model;
use App\Role as Role;
use App\User as User;
use App\UserPreferences as UserPreferences;
use App\Http\Controllers\Auth\OAuth2\TokenStore\TokenCache;

class MSFTAuthController extends Controller
{
    /**
     * Redirect the user to the OAuth2 Endpoint
     * @param Request $request
     */
    public function userLogin(Request $request)
    {
        if (session_status() == PHP_SESSION_NONE) session_start();
        $oauthClient = new \League\OAuth2\Client\Provider\GenericProvider([
            'clientId' => env('OAUTH_APP_ID'),
            'clientSecret' => env('OAUTH_APP_PASSWORD'),
            'redirectUri' => env('OAUTH_REDIRECT_URI'),
            'urlAuthorize' => env('OAUTH_AUTHORITY') . env('OAUTH_AUTHORIZE_ENDPOINT'),
            'urlAccessToken' => env('OAUTH_AUTHORITY') . env('OAUTH_TOKEN_ENDPOINT'),
            'urlResourceOwnerDetails' => '',
            'scopes' => env('OAUTH_SCOPES')
        ]);
        $authorizationUrl = $oauthClient->getAuthorizationUrl();
        // Save client state so we can validate in response
        $request->session()->flash('oauth_state', $oauthClient->getState());
        // Redirect to authorization endpoint
        header('Location: ' . $authorizationUrl);
        exit();
    }


    /**
     * Exchange the acquired OAuth2 authorization code for an auth token.
     * If MSFT user already has an account on system, log them in, otherwise
     * create a new account. If MSFT user is not a KCL user, redirect to home page.
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function retrieveToken(Request $request)
    {
        // Authorization code should be in the "code" query param
        if (isset($_GET['code'])) {
            // Check that state matches the initial state set in the userLogin() method
            if (empty($_GET['state'] || $_GET['state'] !== $request->session()->get('oauth_state'))) {
                exit('State provided in redirect does not match expected value.');
            }
            $oauthClient = new \League\OAuth2\Client\Provider\GenericProvider([
                'clientId' => env('OAUTH_APP_ID'),
                'clientSecret' => env('OAUTH_APP_PASSWORD'),
                'redirectUri' => env('OAUTH_REDIRECT_URI'),
                'urlAuthorize' => env('OAUTH_AUTHORITY') . env('OAUTH_AUTHORIZE_ENDPOINT'),
                'urlAccessToken' => env('OAUTH_AUTHORITY') . env('OAUTH_TOKEN_ENDPOINT'),
                'urlResourceOwnerDetails' => '',
                'scopes' => env('OAUTH_SCOPES')
            ]);
            try {
                // Make the token request
                $accessToken = $oauthClient->getAccessToken('authorization_code', [
                    'code' => $_GET['code']
                ]);
                // Save the access token and refresh tokens in session.
                $tokenCache = new TokenCache($request);
                $tokenCache->storeTokens($accessToken->getToken(), $accessToken->getRefreshToken(),
                    $accessToken->getExpires());
                $userDetails = $this->retrieveUserDetails($tokenCache->getAccessToken());
                $user = $this->verifyUserState($userDetails['email']);
                if ($user) {
                    if ($user->banned) {
                        return redirect('/');
                    }
                    Auth::login($user, true);
                    return redirect()->route('user.home');
                } else {
                    if(!ends_with($userDetails['email'],'@kcl.ac.uk')) return redirect()->route('welcome')->withErrors(array('non_kcl_account' => 'Please login using a KCL account'));
                    $user = User::create([
                        'name' => $userDetails['first_name'] . ' ' . $userDetails['surname'],
                        'email' => $userDetails['email'],
                        'password' => null,
                        'department' => $userDetails['officeLocation']
                    ]);
                    $user->roles()->attach(Role::whereName('user')->first());
                    UserPreferences::create(['user_id' => $user->id]);

                    Auth::login($user, true);
                    return redirect()->route('user.profile.index');
                }
            } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
                print_r($e->getResponseBody());
                exit('ERROR getting tokens: ' . $e->getMessage());
            }
        } elseif (isset($_GET['error'])) {
            exit('ERROR: ' . $_GET['error'] . ' - ' . $_GET['error_description']);
        }
        return redirect('/');
    }

    /*
     * Use Microsoft Graph API in order to retrieve the MSFT user's details.
     */
    public function retrieveUserDetails($accessToken)
    {
        $graph = new Graph();
        $graph->setAccessToken($accessToken);
        $user = $graph->createRequest('GET', '/me')
            ->setReturnType(Model\User::class)
            ->execute();
        $userDetails = [];
        $userDetails['email'] = $user->getMail() ?? $user->getUserPrincipalName();
        $userDetails['first_name'] = $user->getGivenName();
        $userDetails['surname'] = $user->getSurname();
        $userDetails['officeLocation'] = $user->getOfficeLocation();
        return $userDetails;
    }

    /**
     * Verify whether MSFT user has existing account on system.
     * @param $email Email Address of the MSFT user
     * @return mixed $user if user is found, false otherwise.
     */
    public function verifyUserState($email)
    {
        return User::whereEmail($email)->first();
    }
}
