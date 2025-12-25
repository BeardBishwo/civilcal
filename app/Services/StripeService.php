<?php

namespace App\Services;

use App\Config\Stripe as StripeConfig;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;

class StripeService
{
    public function __construct()
    {
        Stripe::setApiKey(StripeConfig::getSecretKey());
    }

    /**
     * Create Checkout Session for Subscription
     * 
     * @param array $plan Plan data from DB ['name', 'price_monthly', 'price_yearly', 'currency'...]
     * @param string $billingCycle 'monthly' or 'yearly'
     * @param array $user User data ['email', 'id']
     * @return array ['success' => bool, 'url' => string, 'session_id' => string]
     */
    public function createCheckoutSession($plan, $billingCycle, $user)
    {
        try {
            $price = $billingCycle === 'yearly' ? $plan['price_yearly'] : $plan['price_monthly'];
            $interval = $billingCycle === 'yearly' ? 'year' : 'month';
            $currency = StripeConfig::getCurrency();
            
            // Calculate amount in cents
            $amount = (int) ($price * 100);

            // Base URL for success/cancel
            $baseUrl = rtrim(app_base_url('/'), '/');

            $sessionConfig = [
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => $currency,
                        'product_data' => [
                            'name' => $plan['name'] . ' (' . ucfirst($billingCycle) . ')',
                            'description' => $plan['description'] ?? 'Subscription',
                        ],
                        'unit_amount' => $amount,
                        'recurring' => [
                            'interval' => $interval,
                        ],
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'subscription',
                'success_url' => $baseUrl . '/payment/stripe/success?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => $baseUrl . '/payment/stripe/cancel',
                'customer_email' => $user['email'],
                'metadata' => [
                    'user_id' => $user['id'],
                    'plan_id' => $plan['id'],
                    'billing_cycle' => $billingCycle
                ],
            ];

            $session = Session::create($sessionConfig);

            return [
                'success' => true,
                'url' => $session->url,
                'session_id' => $session->id
            ];

        } catch (\Exception $e) {
            error_log("Stripe Checkout Error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Verify Webhook Signature
     */
    public function verifyWebhook($payload, $sig_header)
    {
        $endpoint_secret = StripeConfig::getWebhookSecret();

        try {
            $event = Webhook::constructEvent(
                $payload, $sig_header, $endpoint_secret
            );
            return $event;
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            return false;
        } catch (SignatureVerificationException $e) {
            // Invalid signature
            return false;
        }
    }
}
