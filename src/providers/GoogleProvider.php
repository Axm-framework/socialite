<?php

namespace Axm\Socialite\Providers;

/**
 * GoogleProvider - A provider for Google OAuth2 authentication.
 */
class GoogleProvider
{
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
     *
     * @param array $config Configuration array for the provider.
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * Get the URL for authorization.
     *
     * @return string Authorization URL.
     */
    public function getAuthorizationUrl(): string
    {
        $params = [
            'response_type' => 'code',
            'client_id'     => $this->config['client_id'],
            'redirect_uri'  => $this->config['redirect_uri'],
            'scope'         => 'openid email profile',
        ];

        return self::AUTHORIZE_URL . '?' . http_build_query($params);
    }

    /**
     * Get the access token using the provided authorization code.
     *
     * @param string $code Authorization code.
     * @return string Access token.
     * @throws \RuntimeException If unable to obtain access token.
     */
    public function getAccessToken(string $code): string
    {
        $params = [
            'code'          => $code,
            'client_id'     => $this->config['client_id'],
            'client_secret' => $this->config['client_secret'],
            'redirect_uri'  => $this->config['redirect_uri'],
            'grant_type'    => 'authorization_code',
        ];

        $response = $this->sendRequest(self::ACCESS_TOKEN_URL, 'POST', $params);

        if (!isset($response['access_token'])) {
            throw new \RuntimeException('Failed to obtain access token');
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
        $headers  = ['Authorization: Bearer ' . $access_token];
        $userInfo = $this->sendRequest(self::USER_INFO_URL, 'GET', [], $headers);

        if (empty($userInfo)) {
            throw new \RuntimeException('Failed to fetch user information');
        }

        return (object)$userInfo;
    }

    /**
     * Send an HTTP request to the specified URL with the given method, data, and headers.
     *
     * @param string $url URL of the request.
     * @param string $method HTTP method (e.g., GET, POST).
     * @param array $data Data to be sent with the request.
     * @param array $headers Headers for the request.
     * @return array Response data.
     * @throws \RuntimeException If the HTTP request fails.
     */
    private function sendRequest(string $url, string $method, array $data = [], array $headers = []): array
    {
        $ch = curl_init($url);

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST  => $method,
            CURLOPT_POSTFIELDS => ($method === 'POST') ? http_build_query($data) : null,
            CURLOPT_HTTPHEADER => $headers,
        ]);

        $response = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        if ($statusCode >= 200 && $statusCode < 300) {
            return json_decode($response, true);
        }

        throw new \RuntimeException('HTTP Request Failed with status code: ' . $statusCode);
    }
}
