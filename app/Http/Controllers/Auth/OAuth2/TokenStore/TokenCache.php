<?php

namespace App\Http\Controllers\Auth\OAuth2\TokenStore;
use Illuminate\Http\Request;

class TokenCache {

    private $request;

    public function __construct(Request $requestIn) {
        $this->request = $requestIn;
    }

    /**
     * Store access token, refresh token, and refresh token expiry time in
     * the Laravel session.
     * @param $access_token
     * @param $refresh_token
     * @param $expires
     */
    public function storeTokens($access_token, $refresh_token, $expires) {
        $this->request->session()->put('access_token', $access_token);
        $this->request->session()->put('refresh_token', $refresh_token);
        $this->request->session()->put('token_expires', $expires);
    }

    /**
     * Clear the token information from the session.
     */
    public function clearTokens() {
        $this->request->session()->forget(array('access_token','refresh_token','token_expires'));
    }

    /**
     * Check to see if the access token exists/is expired and take
     * steps to return the access token accordingly.
     * @return mixed|string
     */
    public function getAccessToken() {
        // Check if tokens exist
        if (!$this->request->session()->has('access_token') ||
            !$this->request->session()->has('refresh_token') ||
            !$this->request->session()->has('token_expires')) {
            return '';
        }

        // Check if token is expired
        //Get current time + 5 minutes (to allow for time differences)
        $now = time() + 300;
        if ($this->request->session()->get('token_expires') <= $now) {
            $oauthClient = new \League\OAuth2\Client\Provider\GenericProvider([
                'clientId'                => env('OAUTH_APP_ID'),
                'clientSecret'            => env('OAUTH_APP_PASSWORD'),
                'redirectUri'             => env('OAUTH_REDIRECT_URI'),
                'urlAuthorize'            => env('OAUTH_AUTHORITY').env('OAUTH_AUTHORIZE_ENDPOINT'),
                'urlAccessToken'          => env('OAUTH_AUTHORITY').env('OAUTH_TOKEN_ENDPOINT'),
                'urlResourceOwnerDetails' => '',
                'scopes'                  => env('OAUTH_SCOPES')
            ]);

            try {
                $newToken = $oauthClient->getAccessToken('refresh_token', [
                    'refresh_token' => $this->request->session()->get('refresh_token')
                ]);

                // Store the new values
                $this-> storeTokens($newToken->getToken(), $newToken->getRefreshToken(),
                    $newToken->getExpires());

                return $newToken->getToken();
            }
            catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
                return '';
            }
        }
        else {
            // Token is still valid, just return it
            return $this->request->session()->get('access_token');
        }
    }
}

