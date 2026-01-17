<?php
/**
 * Braintree Configuration
 *
 * Manages Braintree API credentials and settings
 */

namespace App\Config;

use App\Services\SettingsService;

class Braintree
{
    /**
     * Get Merchant ID
     */
    public static function getMerchantId()
    {
        return SettingsService::get('braintree_merchant_id') ?: getenv('BRAINTREE_MERCHANT_ID');
    }

    /**
     * Get Public Key
     */
    public static function getPublicKey()
    {
        return SettingsService::get('braintree_public_key') ?: getenv('BRAINTREE_PUBLIC_KEY');
    }

    /**
     * Get Private Key
     */
    public static function getPrivateKey()
    {
        return SettingsService::get('braintree_private_key') ?: getenv('BRAINTREE_PRIVATE_KEY');
    }

    /**
     * Get Environment
     */
    public static function getEnvironment()
    {
        return SettingsService::get('braintree_environment') ?: getenv('BRAINTREE_ENVIRONMENT') ?: 'sandbox';
    }

    /**
     * Get Braintree Gateway
     */
    public static function getGateway()
    {
        $environment = self::getEnvironment();

        return new \Braintree\Gateway([
            'environment' => $environment,
            'merchantId' => self::getMerchantId(),
            'publicKey' => self::getPublicKey(),
            'privateKey' => self::getPrivateKey()
        ]);
    }

    /**
     * Get Currency
     */
    public static function getCurrency()
    {
        return SettingsService::get('braintree_currency') ?: getenv('BRAINTREE_CURRENCY') ?: 'USD';
    }

    /**
     * Check if Braintree is enabled
     */
    public static function isEnabled()
    {
        return SettingsService::get('braintree_enabled') == '1';
    }

    /**
     * Get supported currencies
     */
    public static function getSupportedCurrencies()
    {
        return ['USD', 'EUR', 'GBP', 'CAD', 'AUD'];
    }

    /**
     * Validate configuration
     */
    public static function validateConfig()
    {
        $required = ['merchant_id', 'public_key', 'private_key'];
        foreach ($required as $field) {
            $method = 'get' . str_replace(' ', '', ucwords(str_replace('_', ' ', $field)));
            if (!self::$method()) {
                throw new \Exception("Braintree {$field} is not configured");
            }
        }
        return true;
    }
}