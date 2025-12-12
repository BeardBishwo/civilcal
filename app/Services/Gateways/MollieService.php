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

    public function handleCallback($paymentId, $planId, $type)
    {
        // specific logic for callback if needed, usually just redirect to dashboard
        // Verification happens via Webhook for Mollie usually, but we can double check
        return true;
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
                     // Calculate expiry
                     $expiry = date('Y-m-d H:i:s', strtotime('+1 month'));
                     if ($type === 'yearly') {
                          $expiry = date('Y-m-d H:i:s', strtotime('+1 year'));
                     } elseif ($type === 'lifetime') {
                          $expiry = date('Y-m-d H:i:s', strtotime('+100 years'));
                     }

                     // Update User
                     $db = Database::getInstance();
                     $stmt = $db->getPdo()->prepare("
                        UPDATE users 
                        SET subscription_id = ?, subscription_status = 'active', subscription_ends_at = ? 
                        WHERE id = ?
                     ");
                     $stmt->execute([$planId, $expiry, $userId]);

                     // Payment Record
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
