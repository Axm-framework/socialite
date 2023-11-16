<?php

namespace Axm\Socialite;

trait MakesHttpRequests
{

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
    public function sendRequest(string $url, string $method, array $data = [], array $headers = []): array
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

    /**
     * @return [type]
     */
    public function getCode()
    {
        return app()->request->get('code') ?? null;
    }
}
