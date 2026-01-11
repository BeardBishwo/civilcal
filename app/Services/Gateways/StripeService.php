<?php

namespace App\Services\Gateways;

use App\Core\Database;
use App\Services\SettingsService;
use App\Models\User;
use App\Models\Payment;
use App\Models\Subscription;
use Stripe\StripeClient;
use Stripe\Exception\CardException;
use Stripe\Webhook;

class StripeService
{
    private $userModel;
    private $paymentModel;
    private $subscriptionModel;

    public function __construct()
    {
        $this->userModel = new User();
        $this->paymentModel = new Payment();
        $this->subscriptionModel = new Subscription();
    }

    /**
     * Get Stripe Configuration
     */
    public function getConfig()
    {
        return [
            'enabled' => SettingsService::get('stripe_enabled') === '1',
            'public' => SettingsService::get('stripe_publishable'),
            'secret' => SettingsService::get('stripe_secret'),
            'webhook_secret' => SettingsService::get('stripe_webhook_secret'),
        ];
    }

    /**
     * Generate Checkout Session
     */
    public function checkout($user, $planId, $type = 'monthly')
    {
        $config = $this->getConfig();

        if (!$config['enabled'] || !$config['public'] || !$config['secret']) {
            throw new \Exception('Stripe is not configured.');
        }

        $stripe = new StripeClient($config['secret']);
        $plan = $this->subscriptionModel->find($planId);

        if (!$plan) {
            throw new \Exception('Invalid plan selected.');
        }

        // Determine Price and Description
        $price = $plan['price_monthly'];
        $description = $plan['name'] . ' - Monthly Subscription';

        if ($type === 'yearly') {
            $price = $plan['price_yearly'];
            $description = $plan['name'] . ' - Yearly Subscription';
        } elseif ($type === 'lifetime') {
             $price = $plan['price_lifetime'];
             $description = $plan['name'] . ' - Lifetime Access';
        }

        // Create Checkout Session
        try {
            $session = $stripe->checkout->sessions->create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'usd', // Should come from settings in real app
                        'product_data' => [
                            'name' => $plan['name'],
                            'description' => $description,
                        ],
                        'unit_amount' => $price * 100, // Amount in cents
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment', // Or 'subscription' if using Stripe Subscriptions
                'success_url' => app_base_url('/payment/callback/stripe?session_id={CHECKOUT_SESSION_ID}&plan_id=' . $planId . '&type=' . $type),
                'cancel_url' => app_base_url('/payment/failed'),
                'customer_email' => $user->email,
                'metadata' => [
                    'user_id' => $user->id,
                    'plan_id' => $planId,
                    'type' => $type
                ]
            ]);

            return $session->url;

        } catch (\Exception $e) {
            throw new \Exception('Stripe Checkout Error: ' . $e->getMessage());
        }
    }

    /**
     * Handle Payment Success (Callback)
     */
    public function handleCallback($sessionId, $planId, $type)
    {
         $config = $this->getConfig();
         $stripe = new StripeClient($config['secret']);

         try {
             $session = $stripe->checkout->sessions->retrieve($sessionId);

             if ($session->payment_status === 'paid') {
                 // Fulfill the order
                 return $this->fulfillOrder($session);
             }
         } catch (\Exception $e) {
             error_log('Stripe Callback Error: ' . $e->getMessage());
         }

         return false;
    }

    /**
     * Fulfill the order (Update DB)
     */
    private function fulfillOrder($session)
    {
         $userId = $session->metadata->user_id ?? null;
         $planId = $session->metadata->plan_id ?? null;
         $type = $session->metadata->type ?? 'monthly';
         $transactionId = $session->id;

         if (!$userId) return false;

         // Idempotency check: Prevent double fulfillment
         $existingPayment = $this->paymentModel->findByTransactionId($transactionId);
         if ($existingPayment) {
             return true; // Already fulfilled
         }

         // Fetch user to check current subscription
         $userRecord = $this->userModel->find($userId);
         if (!$userRecord) return false;
         
         $currentExpiry = $userRecord['subscription_ends_at'] ?? null;
         $baseTime = time();

         // If current subscription is still active, stack on top of it
         if ($currentExpiry && strtotime($currentExpiry) > $baseTime) {
             $baseTime = strtotime($currentExpiry);
         }

         // Calculate new expiry
         if ($type === 'yearly') {
             $expiry = date('Y-m-d H:i:s', strtotime('+1 year', $baseTime));
         } elseif ($type === 'lifetime') {
             $expiry = date('Y-m-d H:i:s', strtotime('+100 years', $baseTime));
         } else {
             $expiry = date('Y-m-d H:i:s', strtotime('+1 month', $baseTime));
         }

         try {
             $db = Database::getInstance();
             $pdo = $db->getPdo();
             
             $pdo->beginTransaction();

             // Update User
             $stmt = $pdo->prepare("
                UPDATE users 
                SET subscription_id = ?, subscription_status = 'active', subscription_ends_at = ? 
                WHERE id = ?
             ");
             $stmt->execute([$planId, $expiry, $userId]);

             // Log Payment
             $this->paymentModel->create([
                 'user_id' => $userId,
                 'subscription_id' => $planId,
                 'amount' => $session->amount_total / 100,
                 'currency' => strtoupper($session->currency),
                 'payment_method' => 'stripe',
                 'status' => 'completed',
                 'starts_at' => date('Y-m-d H:i:s'),
                 'ends_at' => $expiry,
                 'transaction_id' => $transactionId 
             ]);

             $pdo->commit();
             return true;
         } catch (\Exception $e) {
             if (isset($pdo)) $pdo->rollBack();
             error_log('Fulfillment Error: ' . $e->getMessage());
             return false;
         }
    }

    /**
     * Handle Webhook
     */
    public function handleWebhook($payload, $sigHeader)
    {
        $config = $this->getConfig();
        $endpoint_secret = $config['webhook_secret'];

        try {
            $event = Webhook::constructEvent(
                $payload, $sigHeader, $endpoint_secret
            );
        } catch(\UnexpectedValueException $e) {
            // Invalid payload
            http_response_code(400);
            exit();
        } catch(\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            http_response_code(400);
            exit();
        }

        // Handle the event
        switch ($event->type) {
            case 'checkout.session.completed':
                $session = $event->data->object;
                $this->fulfillOrder($session);
                break;
            default:
                // Unexpected event type
                // http_response_code(400);
                // exit();
        }

        http_response_code(200);
    }
}
