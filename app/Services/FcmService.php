<?php

namespace App\Services;

use GuzzleHttp\Client;

class FcmService
{
    protected $client;
    protected $serverKey;
    protected $projectId;

    public function __construct()
    {
        $this->client = new Client();
        $this->serverKey = file_get_contents(base_path(env('FCM_CREDENTIALS_PATH')));
        $this->projectId = env('FCM_PROJECT_ID');
    }

    public function sendNotification($deviceToken, $title, $body, $data = [])
    {
        $url = "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send";
        $message = [
            'message' => [
                'token' => $deviceToken,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                ],
                'data' => $data,
            ],
        ];

        $response = $this->client->post($url, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->getAccessToken(),
                'Content-Type'  => 'application/json',
            ],
            'json' => $message,
        ]);

        return json_decode($response->getBody(), true);
    }

    private function getAccessToken()
    {
        $credentials = json_decode($this->serverKey, true);
        $tokenUrl = $credentials['token_uri'];

        $response = $this->client->post($tokenUrl, [
            'form_params' => [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $this->createJwt($credentials),
            ],
        ]);

        $accessToken = json_decode($response->getBody(), true)['access_token'];

        return $accessToken;
    }

    private function createJwt($credentials)
    {
        $now = time();
        $header = json_encode(['alg' => 'RS256', 'typ' => 'JWT']);
        $payload = json_encode([
            'iss' => $credentials['client_email'],
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'aud' => $credentials['token_uri'],
            'iat' => $now,
            'exp' => $now + 3600,
        ]);

        $base64UrlHeader = rtrim(strtr(base64_encode($header), '+/', '-_'), '=');
        $base64UrlPayload = rtrim(strtr(base64_encode($payload), '+/', '-_'), '=');
        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $credentials['private_key'], true);
        $base64UrlSignature = rtrim(strtr(base64_encode($signature), '+/', '-_'), '=');

        return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
    }
}
