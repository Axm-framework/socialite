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
    const ACCESS_TOKEN_URL = 'https://accounts.google.com/o/oauth2/token';

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
        $this->makeRedirect($authorizeUrl);
    }

    /**
     * Get the access token using the provided authorization code.
     *
     * @return string Access token.
     * @throws \RuntimeException If unable to obtain access token.
     */
    public function user()
    {
        $code = $this->getCode();
        if (!empty($code)) {
            try {
                $params = $this->getParams($code);

                $curl = new \Axm\Http\Curl();
                $response = $curl->post(self::ACCESS_TOKEN_URL, $params);
                $data = json_decode($response['response'], true);

                $userInfoUrl = self::USER_INFO_URL . '?access_token=' . $data['access_token'];
                $userInfoResponse = $curl->get($userInfoUrl);
                $userInfo = json_decode($userInfoResponse['response'], true);

                $curl->close();
            } catch (\Throwable $e) {
                throw new \RuntimeException('Failed to obtain access token: ' . $e->getMessage());
            }

            return (object)$userInfo;
        }
    }

    /**
     * @return array
     */
    public function getParams($code): array
    {
        $params = [
            'code'          => $code,
            'client_id'     => $this->config['client_id'],
            'client_secret' => $this->config['client_secret'],
            'redirect_uri'  => $this->config['redirect_uri'],
            'grant_type'    => 'authorization_code',
        ];

        return $params;
    }

    /**
     * @return mixed
     */
    public function getCode()
    {
        return app()
            ->request
            ->get('code') ?? null;
    }

    /**
     * @param string $url
     * @return [type]
     */
    public function makeRedirect(string $url)
    {
        if (!headers_sent()) {
            app()
                ->response
                ->redirect($url);
        }
    }
}
