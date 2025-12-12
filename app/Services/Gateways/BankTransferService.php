<?php

namespace App\Services\Gateways;

use App\Core\Database;
use App\Services\SettingsService;
use App\Models\User;
use App\Models\Payment;
use App\Models\Subscription;

class BankTransferService
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
            'enabled' => SettingsService::get('bank_enabled') === '1',
            'details' => SettingsService::get('bank_details'), // Instructions
        ];
    }

    public function checkout($user, $planId, $type = 'monthly')
    {
        $config = $this->getConfig();
        if (!$config['enabled']) {
            throw new \Exception('Bank Transfer is not enabled.');
        }

        $plan = $this->subscriptionModel->find($planId);
        if (!$plan) {
            throw new \Exception('Invalid plan selected.');
        }

        // Bank transfer doesn't act like a redirect gateway usually.
        // It shows instructions. 
        // We can redirect to a page that lists the instructions.
        
        return app_base_url('/payment/bank-transfer/instructions?plan_id=' . $planId . '&type=' . $type);
    }

    /**
     *  Manual confirmation usually handled by Admin, 
     *  but we might have a user "I have sent it" button.
     */
    public function processManualRequest($userId, $planId, $type, $reference = null)
    {
         // Create a 'pending' payment record
         $plan = $this->subscriptionModel->find($planId);
         $amount = ($type === 'monthly') ? $plan['price_monthly'] : $plan['price_yearly'];
         
         $this->paymentModel->create([
             'user_id' => $userId,
             'subscription_id' => $planId,
             'amount' => $amount,
             'currency' => 'USD',
             'payment_method' => 'bank_transfer',
             'status' => 'pending', // Pending Admin Approval
             'starts_at' => date('Y-m-d H:i:s'),
             'transaction_id' => $reference // User provided ref
         ]);
         
         return true;
    }
}
