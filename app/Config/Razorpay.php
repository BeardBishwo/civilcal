<?php
/**
 * Razorpay Configuration
 *
 * Manages Razorpay API credentials and settings
 */

namespace App\Config;

use App\Services\SettingsService;

class Razorpay
{
    /**
     * Get Key ID
     */
    public static function getKeyId()
    {
        return SettingsService::get('razorpay_key_id') ?: getenv('RAZORPAY_KEY_ID');
    }

    /**
     * Get Key Secret
     */
    public static function getKeySecret()
    {
        return SettingsService::get('razorpay_key_secret') ?: getenv('RAZORPAY_KEY_SECRET');
    }

    /**
     * Get Environment
     */
    public static function getEnvironment()
    {
        return SettingsService::get('razorpay_environment') ?: getenv('RAZORPAY_ENVIRONMENT') ?: 'test';
    }

    /**
     * Get API URL
     */
    public static function getApiUrl()
    {
        return self::getEnvironment() === 'live'
            ? 'https://api.razorpay.com/v1'
            : 'https://api.razorpay.com/v1';
    }

    /**
     * Get Currency
     */
    public static function getCurrency()
    {
        return SettingsService::get('razorpay_currency') ?: getenv('RAZORPAY_CURRENCY') ?: 'USD';
    }

    /**
     * Check if Razorpay is enabled
     */
    public static function isEnabled()
    {
        return SettingsService::get('razorpay_enabled') == '1';
    }

    /**
     * Get supported currencies
     */
    public static function getSupportedCurrencies()
    {
        return ['USD', 'EUR', 'GBP', 'INR', 'SGD', 'AUD'];
    }

    /**
     * Validate configuration
     */
    public static function validateConfig()
    {
        $required = ['key_id', 'key_secret'];
        foreach ($required as $field) {
            $method = 'get' . str_replace(' ', '', ucwords(str_replace('_', ' ', $field)));
            if (!self::$method()) {
                throw new \Exception("Razorpay {$field} is not configured");
            }
        }
        return true;
    }
}