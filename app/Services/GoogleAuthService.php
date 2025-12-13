<?php

namespace App\Services;

use GuzzleHttp\Client;
use Exception;

class GoogleAuthService
{
    private $clientId;
    private $clientSecret;
    private $redirectUri;
    private $client;

    public function __construct()
    {
        $this->clientId = SettingsService::get('google_client_id');
        $this->clientSecret = SettingsService::get('google_client_secret');
        // Construct the callback URL dynamically based on app_base_url
        $this->redirectUri = app_base_url('user/login/google/callback');
        
        $this->client = new Client([
            'base_uri' => 'https://accounts.google.com',
            'timeout'  => 10.0,
            'verify'   => false // Disable SSL verification for local dev if needed, typically true for prod
        ]);
    }

    public function getAuthUrl()
    {
        if (empty($this->clientId)) {
            throw new Exception('Google Client ID is not configured.');
        }

        $params = [
            'response_type' => 'code',
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUri,
            'scope' => 'email profile openid',
            'access_type' => 'offline',
            'prompt' => 'consent'
        ];

        return 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);
    }

    public function getUserFromCode($code)
    {
        if (empty($this->clientSecret)) {
            throw new Exception('Google Client Secret is not configured.');
        }

        try {
            // Exchange authorization code for access token
            $response = $this->client->post('https://oauth2.googleapis.com/token', [
                'form_params' => [
                    'code' => $code,
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'redirect_uri' => $this->redirectUri,
                    'grant_type' => 'authorization_code'
                ]
            ]);

            $data = json_decode($response->getBody(), true);
            $accessToken = $data['access_token'] ?? null;

            if (!$accessToken) {
                throw new Exception('Failed to retrieve access token.');
            }

            // Get user profile info
            $profileResponse = $this->client->get('https://www.googleapis.com/oauth2/v3/userinfo', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken
                ]
            ]);

            return json_decode($profileResponse->getBody(), true);

        } catch (Exception $e) {
            // Log error here if logging service exists
            throw new Exception('Google Login Failed: ' . $e->getMessage());
        }
    }
}
