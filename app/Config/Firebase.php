<?php

namespace App\Config;

class Firebase
{
    public static function getConfig()
    {
        // Try getting from DB; fall back to empty defaults to prevent crashing if not set
        $settings = \App\Services\SettingsService::getAll('firebase');

        return [
            'apiKey' => $settings['firebase_apiKey'] ?? 'YOUR_API_KEY_HERE',
            'authDomain' => $settings['firebase_authDomain'] ?? 'YOUR_PROJECT.firebaseapp.com',
            'databaseURL' => $settings['firebase_databaseURL'] ?? '',
            'projectId' => $settings['firebase_projectId'] ?? 'YOUR_PROJECT_ID',
            'storageBucket' => $settings['firebase_storageBucket'] ?? '',
            'messagingSenderId' => $settings['firebase_messagingSenderId'] ?? '',
            'appId' => $settings['firebase_appId'] ?? '',
            'measurementId' => $settings['firebase_measurementId'] ?? ''
        ];
    }

    public static function getCredentialsPath()
    {
        return BASE_PATH . '/config/firebase_credentials.json';
    }
}
