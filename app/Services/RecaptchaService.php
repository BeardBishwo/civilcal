<?php

namespace App\Services;

use GuzzleHttp\Client;

class RecaptchaService
{
    private $provider;
    private $secretKey;
    private $client;

    public function __construct()
    {
        $this->provider = SettingsService::get('captcha_provider', 'recaptcha_v3');
        $this->secretKey = SettingsService::get('recaptcha_secret_key');
        
        $this->client = new Client([
            'timeout'  => 5.0,
            'verify'   => false
        ]);
    }

    public function verify($response, $ip = null)
    {
        // If captcha is disabled globally or no key, skip check (or fail depending on strictness - we'll skip for dev friendliness if not configured)
        if (empty($this->secretKey)) {
            return true; 
        }

        if (empty($response)) {
            return false;
        }

        $url = $this->getVerifyUrl();
        
        try {
            $params = [
                'secret' => $this->secretKey,
                'response' => $response
            ];
            
            if ($ip) {
                $params['remoteip'] = $ip;
            }

            $res = $this->client->post($url, [
                'form_params' => $params
            ]);

            $body = json_decode($res->getBody(), true);

            if ($this->provider === 'recaptcha_v3') {
                return isset($body['success']) && $body['success'] === true && ($body['score'] ?? 0) >= 0.5;
            }

            return isset($body['success']) && $body['success'] === true;

        } catch (\Exception $e) {
            return false;
        }
    }

    private function getVerifyUrl()
    {
        switch ($this->provider) {
            case 'hcaptcha':
                return 'https://hcaptcha.com/siteverify';
            case 'turnstile':
                return 'https://challenges.cloudflare.com/turnstile/v0/siteverify';
            case 'recaptcha_v2':
            case 'recaptcha_v3':
            default:
                return 'https://www.google.com/recaptcha/api/siteverify';
        }
    }

    public function getScript()
    {
        switch ($this->provider) {
            case 'hcaptcha':
                return '<script src="https://js.hcaptcha.com/1/api.js" async defer></script>';
            case 'turnstile':
                return '<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>';
            case 'recaptcha_v2':
                return '<script src="https://www.google.com/recaptcha/api.js" async defer></script>';
            case 'recaptcha_v3':
                $key = SettingsService::get('recaptcha_site_key');
                return '<script src="https://www.google.com/recaptcha/api.js?render=' . $key . '"></script>';
            default: // v3 default
                $key = SettingsService::get('recaptcha_site_key');
                return '<script src="https://www.google.com/recaptcha/api.js?render=' . $key . '"></script>';
        }
    }

    public function getWidget()
    {
        $key = SettingsService::get('recaptcha_site_key');
        
        switch ($this->provider) {
            case 'hcaptcha':
                return '<div class="h-captcha" data-sitekey="' . $key . '"></div>';
            case 'turnstile':
                return '<div class="cf-turnstile" data-sitekey="' . $key . '"></div>';
            case 'recaptcha_v2':
                return '<div class="g-recaptcha" data-sitekey="' . $key . '"></div>';
            case 'recaptcha_v3':
                // v3 is invisible, typically handles via JS button binding or automatic execution
                return '<input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">';
            default:
                return '<input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">';
        }
    }
}
