<?php
/**
 * Authorize.net Configuration
 *
 * Manages Authorize.net API credentials and settings
 */

namespace App\Config;

use App\Services\SettingsService;

class AuthorizeNet
{
    /**
     * Get API Login ID
     */
    public static function getApiLoginId()
    {
        return SettingsService::get('authorizenet_api_login_id') ?: getenv('AUTHORIZENET_API_LOGIN_ID');
    }

    /**
     * Get Transaction Key
     */
    public static function getTransactionKey()
    {
        return SettingsService::get('authorizenet_transaction_key') ?: getenv('AUTHORIZENET_TRANSACTION_KEY');
    }

    /**
     * Get Sandbox Mode
     */
    public static function isSandbox()
    {
        return SettingsService::get('authorizenet_sandbox') ?: getenv('AUTHORIZENET_SANDBOX') ?: true;
    }

    /**
     * Get API URL
     */
    public static function getApiUrl()
    {
        return self::isSandbox()
            ? 'https://apitest.authorize.net/xml/v1/request.api'
            : 'https://api.authorize.net/xml/v1/request.api';
    }

    /**
     * Get Currency
     */
    public static function getCurrency()
    {
        return SettingsService::get('authorizenet_currency') ?: getenv('AUTHORIZENET_CURRENCY') ?: 'USD';
    }

    /**
     * Check if Authorize.net is enabled
     */
    public static function isEnabled()
    {
        return SettingsService::get('authorizenet_enabled') == '1';
    }

    /**
     * Get supported currencies
     */
    public static function getSupportedCurrencies()
    {
        return ['USD', 'CAD'];
    }

    /**
     * Validate configuration
     */
    public static function validateConfig()
    {
        $required = ['api_login_id', 'transaction_key'];
        foreach ($required as $field) {
            $method = 'get' . str_replace(' ', '', ucwords(str_replace('_', ' ', $field)));
            if (!self::$method()) {
                throw new \Exception("Authorize.net {$field} is not configured");
            }
        }
        return true;
    }
}