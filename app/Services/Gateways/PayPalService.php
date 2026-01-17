<?php

namespace App\Services\Gateways;

use App\Core\Database;
use App\Services\SettingsService;
use App\Models\User;
use App\Models\Payment;
use App\Models\Subscription;
use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Api\Payer;
use PayPal\Api\Amount;
use PayPal\Api\Transaction;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Payment as PaypalPayment;
use PayPal\Api\PaymentExecution;
use PayPal\Exception\PayPalConnectionException;

class PayPalService
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
     * Get PayPal Configuration and Context
     */
    public function getContext()
    {
        $clientId = SettingsService::get('paypal_client_id');
        $clientSecret = SettingsService::get('paypal_secret');
        $sandbox = SettingsService::get('paypal_sandbox', true);

        if (!$clientId || !$clientSecret) {
            throw new \Exception('PayPal is not configured.');
        }

        $apiContext = new ApiContext(
            new OAuthTokenCredential($clientId, $clientSecret)
        );

        $apiContext->setConfig([
            'mode' => $sandbox ? 'sandbox' : 'live',
            'log.LogEnabled' => true,
            'log.FileName' => __DIR__ . '/../../storage/logs/paypal.log',
            'log.LogLevel' => 'DEBUG', // PLEASE USE `FINE` LEVEL FOR LOGGING IN LIVE ENVIRONMENTS
            'cache.enabled' => true,
        ]);

        return $apiContext;
    }

    /**
     * Initiate Payment
     */
    public function checkout($user, $planId, $type = 'monthly')
    {
        $apiContext = $this->getContext();
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

        // Create Payer
        $payer = new Payer();
        $payer->setPaymentMethod('paypal');

        // Create Amount
        $amount = new Amount();
        $amount->setTotal($price);
        $amount->setCurrency('USD'); // Configurable in future

        // Create Transaction
        $transaction = new Transaction();
        $transaction->setAmount($amount);
        $transaction->setDescription($description);
        $transaction->setInvoiceNumber(uniqid());

        // Create Redirect URLs
        $redirectUrls = new RedirectUrls();
        $redirectUrls->setReturnUrl(app_base_url('/payment/callback/paypal?success=true&plan_id=' . $planId . '&type=' . $type . '&user_id=' . $user->id))
            ->setCancelUrl(app_base_url('/payment/failed'));

        // Create Payment
        $payment = new PaypalPayment();
        $payment->setIntent('sale')
            ->setPayer($payer)
            ->setTransactions([$transaction])
            ->setRedirectUrls($redirectUrls);

        try {
            $payment->create($apiContext);
            return $payment->getApprovalLink();
        } catch (PayPalConnectionException $ex) {
            throw new \Exception('PayPal Connection Error: ' . $ex->getData());
        }
    }

    /**
     * Handle Callback (Success)
     */
    public function handleCallback($paymentId, $payerId, $planId, $type)
    {
        $apiContext = $this->getContext();
        $payment = PaypalPayment::get($paymentId, $apiContext);

        $execution = new PaymentExecution();
        $execution->setPayerId($payerId);

        try {
            $result = $payment->execute($execution, $apiContext);

            if ($result->getState() == 'approved') {
                 // Verify user and update subscription
                 // Retrieve user_id from GET parameters (passed in Return URL)
                 $userId = $_GET['user_id'] ?? null; 
                 
                 if (!$userId) {
                     // Fallback to session if mostly reliable, but don't depend on it exclusively
                     $userId = $_SESSION['user_id'] ?? null;
                 }

                 if (!$userId) return false;

                 // Calculate expiry
                 $expiry = date('Y-m-d H:i:s', strtotime('+1 month'));
                 if ($type === 'yearly') {
                      $expiry = date('Y-m-d H:i:s', strtotime('+1 year'));
                 } elseif ($type === 'lifetime') {
                      $expiry = date('Y-m-d H:i:s', strtotime('+100 years'));
                 }

                 // Update User Subscription
                 $db = Database::getInstance();
                 $stmt = $db->getPdo()->prepare("
                    UPDATE users 
                    SET subscription_id = ?, subscription_status = 'active', subscription_ends_at = ? 
                    WHERE id = ?
                 ");
                 $stmt->execute([$planId, $expiry, $userId]);

                 // Log Payment
                 $paymentData = [
                     'user_id' => $userId,
                     'subscription_id' => $planId,
                     'amount' => $result->transactions[0]->amount->total,
                     'currency' => $result->transactions[0]->amount->currency,
                     'payment_method' => 'paypal',
                     'status' => 'completed',
                     'starts_at' => date('Y-m-d H:i:s'),
                     'ends_at' => $expiry,
                     'paypal_order_id' => $paymentId
                 ];

                 $this->paymentModel->create($paymentData);

                 // Send payment confirmation email
                 $this->sendPaymentConfirmationEmail($userId, $paymentData);
                 
                 return true;
            }

        } catch (PayPalConnectionException $ex) {
             error_log('PayPal Execute Error: ' . $ex->getData());
        } catch (\Exception $ex) {
             error_log('PayPal Error: ' . $ex->getMessage());
        }

        return false;
    }

    /**
     * Send payment confirmation email
     */
    private function sendPaymentConfirmationEmail($userId, $paymentData)
    {
        try {
            // Get user details
            $user = $this->userModel->find($userId);
            if (!$user) return false;

            // Get plan details
            $plan = $this->subscriptionModel->find($paymentData['subscription_id']);
            if (!$plan) return false;

            // Send notification using template
            $notificationService = new \App\Services\NotificationService();
            $success = $notificationService->send(
                $userId,
                'payment_success',
                'Payment Successful - Welcome to Premium!',
                "Thank you for your payment of \${$paymentData['amount']} for {$plan['name']}. Your premium features are now active.",
                [
                    'user_email' => $user['email'],
                    'action_url' => app_base_url('/dashboard'),
                    'action_text' => 'Go to Dashboard',
                    'template' => 'payment_confirmation',
                    'template_data' => [
                        'transaction_id' => $paymentData['paypal_order_id'],
                        'plan_name' => $plan['name'],
                        'amount' => $paymentData['amount'],
                        'currency_symbol' => '$',
                        'payment_method' => 'PayPal',
                        'payment_date' => date('M j, Y g:i A'),
                        'dashboard_url' => app_base_url('/dashboard'),
                        'site_name' => \App\Services\SettingsService::get('site_name', 'Bishwo Calculator')
                    ]
                ]
            );

            return $success;
        } catch (\Exception $e) {
            error_log('Email sending error: ' . $e->getMessage());
            return false;
        }
    }
}
