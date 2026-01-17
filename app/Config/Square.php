<?php
/**
 * Square Configuration
 *
 * Manages Square API credentials and settings
 */

namespace App\Config;

use App\Services\SettingsService;

class Square
{
    /**
     * Get Application ID
     */
    public static function getApplicationId()
    {
        return SettingsService::get('square_application_id') ?: getenv('SQUARE_APPLICATION_ID');
    }

    /**
     * Get Access Token
     */
    public static function getAccessToken()
    {
        return SettingsService::get('square_access_token') ?: getenv('SQUARE_ACCESS_TOKEN');
    }

    /**
     * Get Environment
     */
    public static function getEnvironment()
    {
        return SettingsService::get('square_environment') ?: getenv('SQUARE_ENVIRONMENT') ?: 'sandbox';
    }

    /**
     * Get Webhook Signature Key
     */
    public static function getWebhookSignatureKey()
    {
        return SettingsService::get('square_webhook_signature_key') ?: getenv('SQUARE_WEBHOOK_SIGNATURE_KEY');
    }

    /**
     * Get Square Client
     */
    public static function getClient()
    {
        return new \Square\SquareClient([
            'accessToken' => self::getAccessToken(),
            'environment' => self::getEnvironment(),
            'userAgentDetail' => 'BishwoCalculator'
        ]);
    }

    /**
     * Get API URL
     */
    public static function getApiUrl()
    {
        $environment = self::getEnvironment();
        return $environment === 'production'
            ? 'https://connect.squareup.com/v2'
            : 'https://connect.squareupsandbox.com/v2';
    }

    /**
     * Get Currency
     */
    public static function getCurrency()
    {
        return SettingsService::get('square_currency') ?: getenv('SQUARE_CURRENCY') ?: 'USD';
    }

    /**
     * Check if Square is enabled
     */
    public static function isEnabled()
    {
        return SettingsService::get('square_enabled') == '1';
    }

    /**
     * Get supported currencies
     */
    public static function getSupportedCurrencies()
    {
        return ['USD', 'CAD', 'GBP', 'EUR', 'JPY', 'AUD'];
    }

    /**
     * Validate configuration
     */
    public static function validateConfig()
    {
        $required = ['application_id', 'access_token'];
        foreach ($required as $field) {
            $method = 'get' . str_replace(' ', '', ucwords(str_replace('_', ' ', $field)));
            if (!self::$method()) {
                throw new \Exception("Square {$field} is not configured");
            }
        }
        return true;
    }
}