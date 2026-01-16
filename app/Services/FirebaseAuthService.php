<?php

namespace App\Services;

use Kreait\Firebase\Factory;

class FirebaseAuthService
{
    private $factory;
    private $auth;

    public function __construct()
    {
        $credentialsPath = \App\Config\Firebase::getCredentialsPath();

        if (!file_exists($credentialsPath)) {
            throw new \Exception("Firebase credentials file missing at: " . $credentialsPath);
        }

        try {
            $this->factory = (new Factory)->withServiceAccount($credentialsPath);
            $this->auth = $this->factory->createAuth();
        } catch (\Throwable $e) {
            throw new \Exception("Failed to initialize Firebase Factory: " . $e->getMessage());
        }
    }

    public function createCustomToken($userId, $isAdmin = false)
    {
        $additionalClaims = [
            'premium_user' => true,
            'is_admin' => $isAdmin
        ];

        // Create a token valid for 1 hour
        $customToken = $this->auth->createCustomToken($userId, $additionalClaims);
        // Note: The Kreait library returns a Token object, toString() gives the JWT string.
        return $customToken->toString();
    }
}
