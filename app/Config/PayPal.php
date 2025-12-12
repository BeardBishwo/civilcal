<?php
/**
 * PayPal Configuration
 * 
 * Manages PayPal API credentials and settings for subscription payments
 */

namespace App\Config;

use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;

class PayPal
{
    /**
     * PayPal API Context
     * @var ApiContext
     */
    private static $apiContext = null;

    /**
     * Get PayPal API Context
     * 
     * @return ApiContext
     */
    public static function getApiContext()
    {
        if (self::$apiContext === null) {
            self::$apiContext = self::createApiContext();
        }

        return self::$apiContext;
    }

    /**
     * Create PayPal API Context
     * 
     * @return ApiContext
     */
    private static function createApiContext()
    {
        // Get credentials from environment or config
        $clientId = self::getClientId();
        $clientSecret = self::getClientSecret();
        $mode = self::getMode();

        // Create OAuth credential
        $oauthCredential = new OAuthTokenCredential($clientId, $clientSecret);

        // Create API context
        $apiContext = new ApiContext($oauthCredential);

        // Set configuration
        $apiContext->setConfig([
            'mode' => $mode, // 'sandbox' or 'live'
            'log.LogEnabled' => true,
            'log.FileName' => __DIR__ . '/../../storage/logs/paypal.log',
            'log.LogLevel' => 'INFO', // FINE, INFO, WARN or ERROR
            'cache.enabled' => true,
            'cache.FileName' => __DIR__ . '/../../storage/cache/paypal-cache',
            'http.ConnectionTimeOut' => 30,
            'http.Retry' => 1,
        ]);

        return $apiContext;
    }

    /**
     * Get PayPal Client ID
     * 
     * @return string
     */
    public static function getClientId()
    {
        $mode = self::getMode();
        
        if ($mode === 'sandbox') {
            return getenv('PAYPAL_SANDBOX_CLIENT_ID') ?: 'YOUR_SANDBOX_CLIENT_ID';
        }
        
        return getenv('PAYPAL_LIVE_CLIENT_ID') ?: 'YOUR_LIVE_CLIENT_ID';
    }

    /**
     * Get PayPal Client Secret
     * 
     * @return string
     */
    public static function getClientSecret()
    {
        $mode = self::getMode();
        
        if ($mode === 'sandbox') {
            return getenv('PAYPAL_SANDBOX_CLIENT_SECRET') ?: 'YOUR_SANDBOX_CLIENT_SECRET';
        }
        
        return getenv('PAYPAL_LIVE_CLIENT_SECRET') ?: 'YOUR_LIVE_CLIENT_SECRET';
    }

    /**
     * Get PayPal Mode (sandbox or live)
     * 
     * @return string
     */
    public static function getMode()
    {
        return getenv('PAYPAL_MODE') ?: 'sandbox';
    }

    /**
     * Check if in sandbox mode
     * 
     * @return bool
     */
    public static function isSandbox()
    {
        return self::getMode() === 'sandbox';
    }

    /**
     * Check if in live mode
     * 
     * @return bool
     */
    public static function isLive()
    {
        return self::getMode() === 'live';
    }

    /**
     * Get PayPal Webhook ID
     * 
     * @return string
     */
    public static function getWebhookId()
    {
        $mode = self::getMode();
        
        if ($mode === 'sandbox') {
            return getenv('PAYPAL_SANDBOX_WEBHOOK_ID') ?: '';
        }
        
        return getenv('PAYPAL_LIVE_WEBHOOK_ID') ?: '';
    }

    /**
     * Get Return URL for successful payment
     * 
     * @return string
     */
    public static function getReturnUrl()
    {
        $baseUrl = getenv('APP_URL') ?: 'http://localhost';
        return rtrim($baseUrl, '/') . '/subscribe/success';
    }

    /**
     * Get Cancel URL for cancelled payment
     * 
     * @return string
     */
    public static function getCancelUrl()
    {
        $baseUrl = getenv('APP_URL') ?: 'http://localhost';
        return rtrim($baseUrl, '/') . '/subscribe/cancel';
    }

    /**
     * Get Webhook URL
     * 
     * @return string
     */
    public static function getWebhookUrl()
    {
        $baseUrl = getenv('APP_URL') ?: 'http://localhost';
        return rtrim($baseUrl, '/') . '/webhooks/paypal';
    }

    /**
     * Get currency code
     * 
     * @return string
     */
    public static function getCurrency()
    {
        return getenv('PAYPAL_CURRENCY') ?: 'USD';
    }

    /**
     * Get brand name shown in PayPal checkout
     * 
     * @return string
     */
    public static function getBrandName()
    {
        return getenv('APP_NAME') ?: 'Bishwo Calculator';
    }

    /**
     * Validate configuration
     * 
     * @return array Array of validation errors (empty if valid)
     */
    public static function validateConfig()
    {
        $errors = [];

        $clientId = self::getClientId();
        $clientSecret = self::getClientSecret();

        if (empty($clientId) || $clientId === 'YOUR_SANDBOX_CLIENT_ID' || $clientId === 'YOUR_LIVE_CLIENT_ID') {
            $errors[] = 'PayPal Client ID is not configured';
        }

        if (empty($clientSecret) || $clientSecret === 'YOUR_SANDBOX_CLIENT_SECRET' || $clientSecret === 'YOUR_LIVE_CLIENT_SECRET') {
            $errors[] = 'PayPal Client Secret is not configured';
        }

        $mode = self::getMode();
        if (!in_array($mode, ['sandbox', 'live'])) {
            $errors[] = 'PayPal mode must be either "sandbox" or "live"';
        }

        return $errors;
    }

    /**
     * Test API connection
     * 
     * @return array ['success' => bool, 'message' => string]
     */
    public static function testConnection()
    {
        try {
            $errors = self::validateConfig();
            
            if (!empty($errors)) {
                return [
                    'success' => false,
                    'message' => 'Configuration errors: ' . implode(', ', $errors)
                ];
            }

            // Try to get an access token
            $apiContext = self::getApiContext();
            $credential = $apiContext->getCredential();
            $token = $credential->getAccessToken($apiContext->getConfig());

            if (!empty($token)) {
                return [
                    'success' => true,
                    'message' => 'Successfully connected to PayPal ' . self::getMode() . ' API',
                    'mode' => self::getMode()
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to get access token from PayPal'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Connection error: ' . $e->getMessage()
            ];
        }
    }
}
