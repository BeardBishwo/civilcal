<?php
/**
 * 2Checkout Configuration
 *
 * Manages 2Checkout API credentials and settings
 */

namespace App\Config;

use App\Services\SettingsService;

class TwoCheckout
{
    /**
     * Get Merchant ID
     */
    public static function getMerchantId()
    {
        return SettingsService::get('twoco_merchant_id') ?: getenv('TWOCO_MERCHANT_ID');
    }

    /**
     * Get Secret Word
     */
    public static function getSecretWord()
    {
        return SettingsService::get('twoco_secret_word') ?: getenv('TWOCO_SECRET_WORD');
    }

    /**
     * Get Sandbox Mode
     */
    public static function isSandbox()
    {
        return SettingsService::get('twoco_sandbox') ?: getenv('TWOCO_SANDBOX') ?: true;
    }

    /**
     * Get API URL
     */
    public static function getApiUrl()
    {
        return self::isSandbox()
            ? 'https://sandbox.2checkout.com/api/'
            : 'https://www.2checkout.com/api/';
    }

    /**
     * Get Currency
     */
    public static function getCurrency()
    {
        return SettingsService::get('twoco_currency') ?: getenv('TWOCO_CURRENCY') ?: 'USD';
    }

    /**
     * Check if 2Checkout is enabled
     */
    public static function isEnabled()
    {
        return SettingsService::get('twoco_enabled') == '1';
    }

    /**
     * Get supported currencies
     */
    public static function getSupportedCurrencies()
    {
        return ['USD', 'EUR', 'GBP', 'CAD', 'AUD', 'CHF'];
    }

    /**
     * Validate configuration
     */
    public static function validateConfig()
    {
        $required = ['merchant_id', 'secret_word'];
        foreach ($required as $field) {
            $method = 'get' . str_replace(' ', '', ucwords(str_replace('_', ' ', $field)));
            if (!self::$method()) {
                throw new \Exception("2Checkout {$field} is not configured");
            }
        }
        return true;
    }
}