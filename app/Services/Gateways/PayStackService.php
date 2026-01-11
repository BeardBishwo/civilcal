<?php

namespace App\Services\Gateways;

use App\Core\Database;
use App\Services\SettingsService;
use App\Models\User;
use App\Models\Payment;
use App\Models\Subscription;

class PayStackService
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
            'enabled' => SettingsService::get('paystack_enabled') === '1',
            'public_key' => SettingsService::get('paystack_public_key'),
            'secret_key' => SettingsService::get('paystack_secret_key'),
        ];
    }

    public function checkout($user, $planId, $type = 'monthly')
    {
        $config = $this->getConfig();
        if (!$config['enabled'] || !$config['public_key'] || !$config['secret_key']) {
            throw new \Exception('PayStack is not configured.');
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

        // Initialize PayStack Transaction
        $url = "https://api.paystack.co/transaction/initialize";
        $fields = [
            'email' => $user->email,
            'amount' => $price * 100, // Amount in kobo/cents
            'currency' => 'NGN', // Should be configurable, but default PayStack is NGN usually, or USD if enabled
            'callback_url' => app_base_url('/payment/callback/paystack?plan_id=' . $planId . '&type=' . $type . '&user_id=' . $user->id),
            'metadata' => [
                'user_id' => $user->id,
                'plan_id' => $planId,
                'type' => $type
            ]
        ];

        $fields_string = http_build_query($fields);
        // Open connection
        $ch = curl_init();
        
        // Set the url, number of POST vars, POST data
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_POST, true);
        curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Authorization: Bearer " . $config['secret_key'],
            "Cache-Control: no-cache",
        ));
        
        // So that curl_exec returns the contents of the cURL; rather than echoing it
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 
        
        // Execute post
        $result = curl_exec($ch);
        $response = json_decode($result, true);
        
        curl_close($ch);

        if (isset($response['status']) && $response['status']) {
            return $response['data']['authorization_url'];
        } else {
            throw new \Exception('PayStack Initialization Error: ' . ($response['message'] ?? 'Unknown error'));
        }
    }

    public function handleCallback($reference, $planId, $type)
    {
        $config = $this->getConfig();
        
        if (!$reference) return false;

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.paystack.co/transaction/verify/" . rawurlencode($reference),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer " . $config['secret_key'],
                "Cache-Control: no-cache",
            ),
        ));
        
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        
        if ($err) {
            error_log('PayStack Verify Error: ' . $err);
            return false;
        }
        
        $result = json_decode($response, true);
        
        if ($result && isset($result['data']['status']) && $result['data']['status'] === 'success') {
             // Check for Replay Attack (Idempotency)
             $db = Database::getInstance();
             $existingPayment = $db->findOne('payments', ['transaction_id' => $reference]);
             
             if ($existingPayment) {
                 return true; // Already processed
             }

             $userId = $result['data']['metadata']['user_id'] ?? $_GET['user_id'] ?? null;
             
             if ($userId) {
                 // Calculate expiry
                 $expiry = date('Y-m-d H:i:s', strtotime('+1 month'));
                 if ($type === 'yearly') {
                      $expiry = date('Y-m-d H:i:s', strtotime('+1 year'));
                 } elseif ($type === 'lifetime') {
                      $expiry = date('Y-m-d H:i:s', strtotime('+100 years'));
                 }

                 // Update User
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
                     'amount' => $result['data']['amount'] / 100,
                     'currency' => $result['data']['currency'],
                     'payment_method' => 'paystack',
                     'status' => 'completed',
                     'starts_at' => date('Y-m-d H:i:s'),
                     'ends_at' => $expiry,
                     'transaction_id' => $reference
                 ]);
                 
                 return true;
             }
        }
        
        return false;
    }

    public function handleWebhook()
    {
        // PayStack Webhooks are POST requests with specific signatures
        // Logic for auto-renewal would go here
        http_response_code(200);
    }
}
