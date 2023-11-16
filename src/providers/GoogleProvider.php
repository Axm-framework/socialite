<?php

namespace Axm\Socialite\Providers;

use Axm\Socialite\MakesHttpRequests;

/**
 * GoogleProvider - A provider for Google OAuth2 authentication.
 */
class GoogleProvider
{
    use MakesHttpRequests;

    /**
     * Base URL for Google API.
     */
    const API_BASE_URL = 'https://www.googleapis.com/';

    /**
     * URL for authorization.
     */
    const AUTHORIZE_URL = 'https://accounts.google.com/o/oauth2/v2/auth';

    /**
     * URL for obtaining access token.
     */
    const ACCESS_TOKEN_URL = 'https://oauth2.googleapis.com/token';

    /**
     * URL for fetching user information.
     */
    const USER_INFO_URL = 'https://www.googleapis.com/oauth2/v2/userinfo';

    /**
     * URL for Google API documentation.
     */
    const API_DOCUMENTATION_URL = 'https://developers.google.com/identity/protocols/OAuth2';

    /**
     * Configuration array for the provider.
     *
     * @var array
     */
    private $config;

    /**
     * Constructor.
     * @param array $config Configuration array for the provider.
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * Get the URL for authorization.
     * @return string Authorization URL.
     */
    public function redirect()
    {
        $params = [
            'response_type' => 'code',
            'client_id'     => $this->config['client_id'],
            'redirect_uri'  => $this->config['redirect_uri'],
            'scope'         => 'openid email profile',
        ];

        $authorizeUrl = self::AUTHORIZE_URL . '?' . http_build_query($params);
        // Redirects user to the authorization URL
        $this->makeRedirect($authorizeUrl);
    }

    /**
     * Get the access token using the provided authorization code.
     *
     * @return string Access token.
     * @throws \RuntimeException If unable to obtain access token.
     */
    public function getAccessToken()
    {
        $code = $this->getCode();
        if (empty($code)) {
            return null;
        } else {
            app()->response->redirect('/home');
        }

        $params = [
            'code'          => $code,
            'client_id'     => $this->config['client_id'],
            'client_secret' => $this->config['client_secret'],
            'redirect_uri'  => $this->config['redirect_uri'],
            'grant_type'    => 'authorization_code',
        ];

        try {
            $response = $this->sendRequest(self::ACCESS_TOKEN_URL, 'POST', $params);
            app()->response->redirect('/home');
        } catch (\Throwable $e) {
            throw new \RuntimeException('Failed to obtain access token: ' . $e->getMessage());
        }

        return $response['access_token'];
    }

    /**
     * Get user information using the provided access token.
     *
     * @param string $access_token Access token.
     * @return object User information.
     * @throws \RuntimeException If unable to fetch user information.
     */
    public function getUserInfo(string $access_token): object
    {
        $headers = ['Authorization: Bearer ' . $access_token];

        try {
            $userInfo = $this->sendRequest(self::USER_INFO_URL, 'GET', [], $headers);
        } catch (\Throwable $e) {
            throw new \RuntimeException('Failed to fetch user information. ' . $e->getMessage());
        }

        return (object)$userInfo;
    }

    /**
     * @param string $url
     * 
     * @return [type]
     */
    public function makeRedirect(string $url)
    {
        if (!headers_sent()) {
            app()->response->redirect($url);
            exit;
        }
    }
}
