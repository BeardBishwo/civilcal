<?php
/**
 * Flutterwave Configuration
 *
 * Manages Flutterwave API credentials and settings
 */

namespace App\Config;

use App\Services\SettingsService;

class Flutterwave
{
    /**
     * Get Public Key
     */
    public static function getPublicKey()
    {
        return SettingsService::get('flutterwave_public_key') ?: getenv('FLUTTERWAVE_PUBLIC_KEY');
    }

    /**
     * Get Secret Key
     */
    public static function getSecretKey()
    {
        return SettingsService::get('flutterwave_secret_key') ?: getenv('FLUTTERWAVE_SECRET_KEY');
    }

    /**
     * Get Encryption Key
     */
    public static function getEncryptionKey()
    {
        return SettingsService::get('flutterwave_encryption_key') ?: getenv('FLUTTERWAVE_ENCRYPTION_KEY');
    }

    /**
     * Get Environment
     */
    public static function getEnvironment()
    {
        return SettingsService::get('flutterwave_environment') ?: getenv('FLUTTERWAVE_ENVIRONMENT') ?: 'sandbox';
    }

    /**
     * Get API URL
     */
    public static function getApiUrl()
    {
        return self::getEnvironment() === 'live'
            ? 'https://api.flutterwave.com/v3'
            : 'https://api.flutterwave.com/v3';
    }

    /**
     * Get Currency
     */
    public static function getCurrency()
    {
        return SettingsService::get('flutterwave_currency') ?: getenv('FLUTTERWAVE_CURRENCY') ?: 'USD';
    }

    /**
     * Check if Flutterwave is enabled
     */
    public static function isEnabled()
    {
        return SettingsService::get('flutterwave_enabled') == '1';
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
        $required = ['public_key', 'secret_key', 'encryption_key'];
        foreach ($required as $field) {
            $method = 'get' . str_replace(' ', '', ucwords(str_replace('_', ' ', $field)));
            if (!self::$method()) {
                throw new \Exception("Flutterwave {$field} is not configured");
            }
        }
        return true;
    }
}