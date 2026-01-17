<?php
/**
 * CoinPayments Configuration
 *
 * Manages CoinPayments API credentials and settings
 */

namespace App\Config;

use App\Services\SettingsService;

class CoinPayments
{
    /**
     * Get Merchant ID
     */
    public static function getMerchantId()
    {
        return SettingsService::get('coinpayments_merchant_id') ?: getenv('COINPAYMENTS_MERCHANT_ID');
    }

    /**
     * Get Public Key
     */
    public static function getPublicKey()
    {
        return SettingsService::get('coinpayments_public_key') ?: getenv('COINPAYMENTS_PUBLIC_KEY');
    }

    /**
     * Get Private Key
     */
    public static function getPrivateKey()
    {
        return SettingsService::get('coinpayments_private_key') ?: getenv('COINPAYMENTS_PRIVATE_KEY');
    }

    /**
     * Get IPN Secret
     */
    public static function getIpnSecret()
    {
        return SettingsService::get('coinpayments_ipn_secret') ?: getenv('COINPAYMENTS_IPN_SECRET');
    }

    /**
     * Get Environment
     */
    public static function getEnvironment()
    {
        return SettingsService::get('coinpayments_environment') ?: getenv('COINPAYMENTS_ENVIRONMENT') ?: 'test';
    }

    /**
     * Get API URL
     */
    public static function getApiUrl()
    {
        return 'https://www.coinpayments.net/api.php';
    }

    /**
     * Get supported currencies
     */
    public static function getSupportedCurrencies()
    {
        return ['BTC', 'ETH', 'LTC', 'USDT', 'BCH', 'XRP', 'DASH'];
    }

    /**
     * Check if CoinPayments is enabled
     */
    public static function isEnabled()
    {
        return SettingsService::get('coinpayments_enabled') == '1';
    }

    /**
     * Get CoinPayments API Client
     */
    public static function getClient()
    {
        return new \CoinpaymentsAPI\CoinpaymentsAPI(
            self::getPrivateKey(),
            self::getPublicKey(),
            self::getMerchantId()
        );
    }

    /**
     * Validate configuration
     */
    public static function validateConfig()
    {
        $required = ['merchant_id', 'public_key', 'private_key', 'ipn_secret'];
        foreach ($required as $field) {
            $method = 'get' . str_replace(' ', '', ucwords(str_replace('_', ' ', $field)));
            if (!self::$method()) {
                throw new \Exception("CoinPayments {$field} is not configured");
            }
        }
        return true;
    }
}