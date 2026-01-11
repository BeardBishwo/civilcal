<?php

namespace App\Services\Gateways;

use App\Core\Database;
use App\Services\SettingsService;
use App\Models\User;
use App\Models\Payment;
use App\Models\Subscription;

class PaddleService
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
            'billing_enabled' => SettingsService::get('paddle_billing_enabled') === '1',
            'classic_enabled' => SettingsService::get('paddle_classic_enabled') === '1',
            'vendor_id' => SettingsService::get('paddle_vendor_id'),
            'auth_code' => SettingsService::get('paddle_auth_code'),
            'public_key' => SettingsService::get('paddle_public_key'),
            'client_token' => SettingsService::get('paddle_client_token'),
            'api_key' => SettingsService::get('paddle_api_key'),
            'environment' => SettingsService::get('paddle_environment', 'sandbox'),
        ];
    }

    public function checkout($user, $planId, $type = 'monthly')
    {
        $config = $this->getConfig();
        $plan = $this->subscriptionModel->find($planId);
        
        if (!$plan) {
            throw new \Exception('Invalid plan selected.');
        }

        // Logic for Paddle Billing (New)
        if ($config['billing_enabled']) {
             // For Billing, usually involves client-side integration or creating a txn via API
             // Reference PaddleBilling.php uses environment JS variable and custom attributes
             // Here we might just return the correct product ID to be used in frontend JS,
             // OR if we strictly need a redirect URL setup, we would need to generate one.
             // Given the reference structure, it seems they might be using client-side checkout.
             // BUT, to keep consistent with controller "redirect" pattern:
             // We can't easily do a server-side redirect for Paddle Billing without more API work.
             // Let's assume we return a URL to a local view that initiates Paddle.js
             
             // However, for simplicity and standardizing, let's assume we use the Plan ID (Paddle Price ID)
             // stored in our DB for that plan.
             // For now, let's look at doing a simple redirect to an overlay trigger page if needed.
             
             // Wait, the reference PaddleBilling.php `checkout` just returns a div with attributes.
             // The controller expects a URL to redirect to.
             // We might need to adjust the controller or return a special URL that renders the specific view.
             
             // Let's create a specialized route/view for Paddle Checkout if we can't do direct URL.
             // Or, better, we generate a transaction via API and return the checkout URL.
             
             $priceId = ($type === 'monthly') ? $plan['paddle_billing_monthly_id'] : $plan['paddle_billing_yearly_id']; // Needed column
             // If columns don't exist, we can't proceed. Assuming they might or we use generic.
             
             // Simplification: Throw exception if not fully integrated
             // return app_base_url('/payment/paddle-billing/checkout?plan_id=' . $planId);
             throw new \Exception("Paddle Billing requires Frontend JS Integ. Please use other gateway for now.");
        }

        // Logic for Paddle Classic
        if ($config['classic_enabled']) {
             $productId = ($type === 'monthly') ? $plan['paddle_monthly_id'] : $plan['paddle_yearly_id']; // User needs to save these
             
             // Identify Sandbox vs Live
             $vendorId = $config['vendor_id'];
             
             // Classic Paddle Overlay link standard construction
             // https://sandbox-checkout.paddle.com/checkout/product/{product_id}?
             
             $baseUrl = ($config['environment'] === 'sandbox') ? 'https://sandbox-checkout.paddle.com/checkout/product/' : 'https://checkout.paddle.com/checkout/product/';
             
             $url = $baseUrl . $productId . '?vendor=' . $vendorId . '&customer_email=' . urlencode($user->email) . '&passthrough=' . urlencode(json_encode([
                 'user_id' => $user->id,
                 'plan_id' => $planId,
                 'type' => $type
             ]));
             
             return $url;
        }

        throw new \Exception('Paddle is not enabled.');
    }

    public function handleCallback($data)
    {
        // Paddle Webhook/Callback logic.
        // It POSTs data to webhook URL.
        
        // This is primarily for Webhook processing.
        return true;
    }

    public function handleWebhook()
    {
        $config = $this->getConfig();
        $input = $_POST;

        if ($config['billing_enabled']) {
             // Verify Signature for Billing
             // Logic to verify Paddle Billing webhook signature
             // ...
             // Parse Payload
             // event_type: transaction.completed
             // data: ...
        }
        
        if ($config['classic_enabled']) {
             // Verify Signature for Classic
             $public_key = $config['public_key'];
             $signature = $input['p_signature'] ?? '';
             
             if (!$signature || !$public_key) {
                 http_response_code(403);
                 die('Missing signature or public key');
             }

             // Verification Logic
             $data = $input;
             unset($data['p_signature']);
             foreach ($data as $key => $value) {
                 if (!is_string($value)) {
                     $data[$key] = (string)$value;
                 }
             }
             ksort($data);
             $serialized = serialize($data);
             
             $verified = openssl_verify($serialized, base64_decode($signature), $public_key, OPENSSL_ALGO_SHA1);
             
             if ($verified !== 1) {
                 http_response_code(403);
                 die('Invalid signature');
             }

             if (isset($input['alert_name']) && $input['alert_name'] === 'payment_succeeded') {
                 $passthrough = json_decode($input['passthrough'], true);
                 $userId = $passthrough['user_id'] ?? null;
                 $planId = $passthrough['plan_id'] ?? null;
                 $cycleType = $passthrough['type'] ?? 'monthly';
                 
                  if ($userId && $planId) {
                       $db = Database::getInstance();
                       
                       // Fetch current user subscription info to handle "Lost Days"
                       $stmt = $db->getPdo()->prepare("SELECT subscription_ends_at FROM users WHERE id = ?");
                       $stmt->execute([$userId]);
                       $userSub = $stmt->fetch();
                       
                       $startTime = time();
                       if ($userSub && !empty($userSub['subscription_ends_at'])) {
                           $currentExpiry = strtotime($userSub['subscription_ends_at']);
                           if ($currentExpiry > $startTime) {
                               $startTime = $currentExpiry;
                           }
                       }

                       // Calculate new expiry
                       $duration = ($cycleType === 'yearly') ? '+1 year' : '+1 month';
                       $newExpiry = date('Y-m-d H:i:s', strtotime($duration, $startTime));
                       
                       // 1. Update Users Table (Fast Access)
                       $stmt = $db->getPdo()->prepare("
                          UPDATE users 
                          SET subscription_id = ?, subscription_status = 'active', subscription_ends_at = ? 
                          WHERE id = ?
                       ");
                       $stmt->execute([$planId, $newExpiry, $userId]);

                       // 2. Update User Subscriptions Table (Audit History)
                       $this->subscriptionModel->create([
                           'user_id' => $userId,
                           'plan_id' => $planId,
                           'paypal_subscription_id' => $input['subscription_id'] ?? 'paddle_' . ($input['checkout_id'] ?? uniqid()),
                           'paypal_plan_id' => $input['subscription_plan_id'] ?? $planId,
                           'status' => 'active',
                           'billing_cycle' => $cycleType,
                           'amount' => $input['sale_gross'] ?? 0,
                           'currency' => $input['currency'] ?? 'USD',
                           'current_period_start' => date('Y-m-d H:i:s'),
                           'current_period_end' => $newExpiry,
                           'next_billing_date' => $newExpiry,
                           'is_trial' => 0,
                           'trial_start' => null,
                           'trial_end' => null
                       ]);

                       // 3. Create Payment Record
                       $this->paymentModel->create([
                           'user_id' => $userId,
                           'subscription_id' => $planId,
                           'amount' => $input['sale_gross'] ?? 0,
                           'currency' => $input['currency'] ?? 'USD',
                           'payment_method' => 'paddle',
                           'status' => 'completed',
                           'starts_at' => date('Y-m-d H:i:s'),
                           'ends_at' => $newExpiry,
                           'transaction_id' => $input['order_id'] ?? $input['checkout_id'] ?? uniqid()
                       ]);
                  }
             }
        }
        
        http_response_code(200);
    }
}
