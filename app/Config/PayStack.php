<?php
/**
 * PayStack Configuration
 *
 * Manages PayStack API credentials and settings
 */

namespace App\Config;

use App\Services\SettingsService;

class PayStack
{
    /**
     * Get Public Key
     */
    public static function getPublicKey()
    {
        return SettingsService::get('paystack_public_key') ?: getenv('PAYSTACK_PUBLIC_KEY');
    }

    /**
     * Get Secret Key
     */
    public static function getSecretKey()
    {
        return SettingsService::get('paystack_secret_key') ?: getenv('PAYSTACK_SECRET_KEY');
    }

    /**
     * Get Environment
     */
    public static function getEnvironment()
    {
        return SettingsService::get('paystack_environment') ?: getenv('PAYSTACK_ENVIRONMENT') ?: 'test';
    }

    /**
     * Get API URL
     */
    public static function getApiUrl()
    {
        return self::getEnvironment() === 'live'
            ? 'https://api.paystack.co'
            : 'https://api.paystack.co';
    }

    /**
     * Get Currency
     */
    public static function getCurrency()
    {
        return SettingsService::get('paystack_currency') ?: getenv('PAYSTACK_CURRENCY') ?: 'USD';
    }

    /**
     * Check if PayStack is enabled
     */
    public static function isEnabled()
    {
        return SettingsService::get('paystack_enabled') == '1';
    }

    /**
     * Get supported currencies
     */
    public static function getSupportedCurrencies()
    {
        return ['USD', 'EUR', 'GBP', 'NGN', 'KES', 'GHS', 'ZAR'];
    }

    /**
     * Validate configuration
     */
    public static function validateConfig()
    {
        $required = ['public_key', 'secret_key'];
        foreach ($required as $field) {
            $method = 'get' . str_replace(' ', '', ucwords(str_replace('_', ' ', $field)));
            if (!self::$method()) {
                throw new \Exception("PayStack {$field} is not configured");
            }
        }
        return true;
    }
}