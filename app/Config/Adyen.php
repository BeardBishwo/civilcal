<?php
/**
 * Adyen Configuration
 *
 * Manages Adyen API credentials and settings
 */

namespace App\Config;

use App\Services\SettingsService;

class Adyen
{
    /**
     * Get Adyen API Key
     */
    public static function getApiKey()
    {
        return SettingsService::get('adyen_api_key') ?: getenv('ADYEN_API_KEY');
    }

    /**
     * Get Adyen Merchant Account
     */
    public static function getMerchantAccount()
    {
        return SettingsService::get('adyen_merchant_account') ?: getenv('ADYEN_MERCHANT_ACCOUNT');
    }

    /**
     * Get Adyen Client Key
     */
    public static function getClientKey()
    {
        return SettingsService::get('adyen_client_key') ?: getenv('ADYEN_CLIENT_KEY');
    }

    /**
     * Get Adyen Environment
     */
    public static function getEnvironment()
    {
        return SettingsService::get('adyen_environment') ?: getenv('ADYEN_ENVIRONMENT') ?: 'test';
    }

    /**
     * Get Adyen API URL
     */
    public static function getApiUrl()
    {
        $environment = self::getEnvironment();
        return $environment === 'live'
            ? 'https://checkout-live.adyen.com/v71'
            : 'https://checkout-test.adyen.com/v71';
    }

    /**
     * Get Currency
     */
    public static function getCurrency()
    {
        return SettingsService::get('adyen_currency') ?: getenv('ADYEN_CURRENCY') ?: 'USD';
    }

    /**
     * Check if Adyen is enabled
     */
    public static function isEnabled()
    {
        return SettingsService::get('adyen_enabled') == '1';
    }

    /**
     * Get supported currencies
     */
    public static function getSupportedCurrencies()
    {
        return ['USD', 'EUR', 'GBP', 'CAD', 'AUD', 'JPY'];
    }

    /**
     * Validate configuration
     */
    public static function validateConfig()
    {
        $required = ['api_key', 'merchant_account', 'client_key'];
        foreach ($required as $field) {
            $method = 'get' . str_replace(' ', '', ucwords(str_replace('_', ' ', $field)));
            if (!self::$method()) {
                throw new \Exception("Adyen {$field} is not configured");
            }
        }
        return true;
    }
}