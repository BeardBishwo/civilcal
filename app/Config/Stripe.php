<?php

namespace App\Config;

use App\Services\SettingsService;

class Stripe
{
    /**
     * Get Stripe Secret Key
     */
    public static function getSecretKey()
    {
        return SettingsService::get('stripe_secret_key') ?: getenv('STRIPE_SECRET_KEY');
    }

    /**
     * Get Stripe Publishable Key
     */
    public static function getPublishableKey()
    {
        return SettingsService::get('stripe_publishable_key') ?: getenv('STRIPE_PUBLISHABLE_KEY');
    }

    /**
     * Get Stripe Webhook Secret
     */
    public static function getWebhookSecret()
    {
        return SettingsService::get('stripe_webhook_secret') ?: getenv('STRIPE_WEBHOOK_SECRET');
    }

    /**
     * Get Checkout Type (builtin vs hosted)
     */
    public static function getCheckoutType()
    {
        return SettingsService::get('stripe_checkout_type', 'hosted');
    }

    /**
     * Check if Stripe is enabled
     */
    public static function isEnabled()
    {
        return SettingsService::get('stripe_enabled') == '1';
    }

    /**
     * Get Currency
     */
    public static function getCurrency()
    {
        return SettingsService::get('currency_code', 'USD');
    }
}
