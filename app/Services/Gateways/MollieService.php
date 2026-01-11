<?php

namespace App\Services\Gateways;

use App\Core\Database;
use App\Services\SettingsService;
use App\Models\User;
use App\Models\Payment;
use App\Models\Subscription;

class MollieService
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

    public function getConfig()
    {
        return [
            'enabled' => SettingsService::get('mollie_enabled') === '1',
            'api_key' => SettingsService::get('mollie_api_key'),
        ];
    }

    public function checkout($user, $planId, $type = 'monthly')
    {
        $config = $this->getConfig();
        if (!$config['enabled'] || !$config['api_key']) {
            throw new \Exception('Mollie is not configured.');
        }

        $plan = $this->subscriptionModel->find($planId);
        if (!$plan) {
            throw new \Exception('Invalid plan selected.');
        }

        $price = $plan['price_monthly'];
        $description = $plan['name'] . ' - Monthly Subscription';

        if ($type === 'yearly') {
            $price = $plan['price_yearly'];
            $description = $plan['name'] . ' - Yearly Subscription';
        } elseif ($type === 'lifetime') {
             $price = $plan['price_lifetime'];
             $description = $plan['name'] . ' - Lifetime Access';
        }

        try {
            // Using Mollie API Client or direct cURL if library not present.
            // Assuming library usage similar to reference, but simplified for dependencies.
            // Reference uses `\Mollie\Api\MollieApiClient`.
            if (!class_exists('\Mollie\Api\MollieApiClient')) {
                throw new \Exception('Mollie API Client library not installed.');
            }

            $mollie = new \Mollie\Api\MollieApiClient();
            $mollie->setApiKey($config['api_key']);

            $payment = $mollie->payments->create([
                "amount" => [
                    "currency" => "USD", // Should be strictly formatted or configurable
                    "value" => number_format($price, 2, '.', '')
                ],
                "description" => $description,
                "redirectUrl" => app_base_url('/payment/callback/mollie?plan_id=' . $planId . '&type=' . $type),
                "webhookUrl"  => app_base_url('/payment/webhook/mollie'),
                "metadata" => [
                    "user_id" => $user->id,
                    "plan_id" => $planId,
                    "type" => $type
                ],
            ]);

            return $payment->getCheckoutUrl();

        } catch (\Exception $e) {
            throw new \Exception('Mollie Checkout Error: ' . $e->getMessage());
        }
    }

    /**
     * Verify payment status via API (for callback verification)
     */
    public function verifyPayment($paymentId)
    {
        $config = $this->getConfig();
        if (!$config['enabled'] || !$config['api_key']) {
            return false;
        }

        try {
            $mollie = new \Mollie\Api\MollieApiClient();
            $mollie->setApiKey($config['api_key']);
            $payment = $mollie->payments->get($paymentId);

            return $payment->isPaid();
        } catch (\Exception $e) {
            error_log('Mollie Verify Error: ' . $e->getMessage());
            return false;
        }
    }

    public function handleCallback($paymentId, $planId, $type)
    {
        return $this->verifyPayment($paymentId);
    }

    public function handleWebhook()
    {
        $config = $this->getConfig();
        $id = $_POST['id'] ?? null;
        
        if (!$id) {
            http_response_code(400); 
            return;
        }

        try {
            $mollie = new \Mollie\Api\MollieApiClient();
            $mollie->setApiKey($config['api_key']);
            $payment = $mollie->payments->get($id);

            if ($payment->isPaid() && !$payment->hasRefunds() && !$payment->hasChargebacks()) {
                $userId = $payment->metadata->user_id ?? null;
                $planId = $payment->metadata->plan_id ?? null;
                $type = $payment->metadata->type ?? 'monthly';

                if ($userId && $planId) {
                     $db = Database::getInstance();

                     // 0. Idempotency check: Has this payment already been processed?
                     $stmtIdem = $db->getPdo()->prepare("SELECT id FROM payments WHERE transaction_id = ? AND status = 'completed'");
                     $stmtIdem->execute([$id]);
                     if ($stmtIdem->fetch()) {
                         http_response_code(200); // Already processed
                         return;
                     }

                     // 1. Calculate expiry (Handle "Lost Days")
                     $stmtUser = $db->getPdo()->prepare("SELECT subscription_ends_at FROM users WHERE id = ?");
                     $stmtUser->execute([$userId]);
                     $userSub = $stmtUser->fetch();
                     
                     $startTime = time();
                     if ($userSub && !empty($userSub['subscription_ends_at'])) {
                         $currentExpiry = strtotime($userSub['subscription_ends_at']);
                         if ($currentExpiry > $startTime) {
                             $startTime = $currentExpiry;
                         }
                     }

                     $duration = '+1 month';
                     if ($type === 'yearly') {
                          $duration = '+1 year';
                     } elseif ($type === 'lifetime') {
                          $duration = '+100 years';
                     }
                     $expiry = date('Y-m-d H:i:s', strtotime($duration, $startTime));

                     // 2. Update Users Table (Fast Access)
                     $stmtUpdate = $db->getPdo()->prepare("
                        UPDATE users 
                        SET subscription_id = ?, subscription_status = 'active', subscription_ends_at = ? 
                        WHERE id = ?
                     ");
                     $stmtUpdate->execute([$planId, $expiry, $userId]);

                     // 3. Update User Subscriptions Table (Audit History)
                     $this->subscriptionModel->create([
                         'user_id' => $userId,
                         'plan_id' => $planId,
                         'paypal_subscription_id' => 'mollie_' . $id,
                         'paypal_plan_id' => $planId,
                         'status' => 'active',
                         'billing_cycle' => $type,
                         'amount' => $payment->amount->value,
                         'currency' => $payment->amount->currency,
                         'current_period_start' => date('Y-m-d H:i:s'),
                         'current_period_end' => $expiry,
                         'next_billing_date' => $expiry,
                         'is_trial' => 0,
                         'trial_start' => null,
                         'trial_end' => null
                     ]);

                     // 4. Create Payment Record (Already exists in user's request as step 4)
                     $this->paymentModel->create([
                         'user_id' => $userId,
                         'subscription_id' => $planId,
                         'amount' => $payment->amount->value,
                         'currency' => $payment->amount->currency,
                         'payment_method' => 'mollie',
                         'status' => 'completed',
                         'starts_at' => date('Y-m-d H:i:s'),
                         'ends_at' => $expiry,
                         'transaction_id' => $id
                     ]);
                }
            }
        } catch (\Exception $e) {
             error_log('Mollie Webhook Error: ' . $e->getMessage());
             http_response_code(500);
             return;
        }

        http_response_code(200);
    }
}
