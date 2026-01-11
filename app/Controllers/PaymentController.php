<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Services\SettingsService;
use App\Services\Gateways\StripeService;
use App\Services\Gateways\PayPalService;
use App\Models\Subscription;
use App\Core\Auth;

class PaymentController extends Controller
{
    private $stripeService;
    private $payPalService;
    private $mollieService;
    private $payStackService;
    private $paddleService;
    private $bankTransferService;
    private $subscriptionModel;

    public function __construct()
    {
        parent::__construct();
        $this->stripeService = new StripeService();
        $this->payPalService = new PayPalService();
        $this->mollieService = new \App\Services\Gateways\MollieService();
        $this->payStackService = new \App\Services\Gateways\PayStackService();
        $this->paddleService = new \App\Services\Gateways\PaddleService();
        $this->bankTransferService = new \App\Services\Gateways\BankTransferService();
        $this->subscriptionModel = new Subscription();
    }

    /**
     * Initiate Checkout
     */
    public function checkout($gateway)
    {
        // Ensure user is logged in
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . app_base_url('/login'));
            exit;
        }

        $user = (new \App\Models\User())->find($_SESSION['user_id']); // Simple user retrieval
        $planId = $_GET['plan_id'] ?? null;
        $type = $_GET['type'] ?? 'monthly';

        if (!$planId) {
            die('Plan ID required');
        }

        try {
            $redirectUrl = '';

            switch ($gateway) {
                case 'stripe':
                    $redirectUrl = $this->stripeService->checkout($user, $planId, $type);
                    break;
                case 'paypal':
                    $redirectUrl = $this->payPalService->checkout($user, $planId, $type);
                    break;
                case 'mollie':
                    $redirectUrl = $this->mollieService->checkout($user, $planId, $type);
                    break;
                case 'paystack':
                    $redirectUrl = $this->payStackService->checkout($user, $planId, $type);
                    break;
                case 'paddle':
                    $redirectUrl = $this->paddleService->checkout($user, $planId, $type);
                    break;
                case 'bank':
                    $redirectUrl = $this->bankTransferService->checkout($user, $planId, $type);
                    break;
                default:
                    die('Invalid Gateway');
            }

            if ($redirectUrl) {
                header('Location: ' . $redirectUrl);
                exit;
            }

        } catch (\Exception $e) {
            // Log error
            error_log("Checkout Error [$gateway]: " . $e->getMessage());
            die("Error initiating payment: " . $e->getMessage());
        }
    }

    /**
     * Handle Gateway Callbacks (Return URLs)
     */
    public function callback($gateway)
    {
        $success = false;
        
        try {
            switch ($gateway) {
                case 'stripe':
                    $sessionId = $_GET['session_id'] ?? null;
                    $planId = $_GET['plan_id'] ?? null;
                    $type = $_GET['type'] ?? 'monthly';
                    
                    if ($sessionId && $planId) {
                        $success = $this->stripeService->handleCallback($sessionId, $planId, $type);
                    }
                    break;

                case 'paypal':
                    $paymentId = $_GET['paymentId'] ?? null;
                    $payerId = $_GET['PayerID'] ?? null;
                    $planId = $_GET['plan_id'] ?? null;
                    $type = $_GET['type'] ?? 'monthly';

                    if ($paymentId && $payerId && $planId) {
                        $success = $this->payPalService->handleCallback($paymentId, $payerId, $planId, $type);
                    }
                    break;

                case 'mollie':
                    $paymentId = $_GET['id'] ?? null;
                    $planId = $_GET['plan_id'] ?? null;
                    $type = $_GET['type'] ?? 'monthly';
                    
                    if ($paymentId && $planId) {
                        $success = $this->mollieService->verifyPayment($paymentId);
                    }
                    break;

                case 'paystack':
                    $reference = $_GET['reference'] ?? null;
                    $planId = $_GET['plan_id'] ?? null;
                    $type = $_GET['type'] ?? 'monthly';
                    
                    if ($reference && $planId) {
                        $success = $this->payStackService->handleCallback($reference, $planId, $type);
                    }
                    break;
            }
        } catch (\Exception $e) {
             error_log("Callback Error [$gateway]: " . $e->getMessage());
        }

        if ($success) {
            header('Location: ' . app_base_url('/dashboard?payment=success'));
        } else {
            header('Location: ' . app_base_url('/pricing?payment=failed'));
        }
        exit;
    }

    /**
     * Handle Webhooks (Async)
     */
    public function webhook($gateway)
    {
        $payload = @file_get_contents('php://input');
        
        switch ($gateway) {
            case 'stripe':
                $sigHeader = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';
                $this->stripeService->handleWebhook($payload, $sigHeader);
                break;
            case 'mollie':
                $this->mollieService->handleWebhook();
                break;
            case 'paystack':
                $this->payStackService->handleWebhook();
                break;
            case 'paddle':
                $this->paddleService->handleWebhook();
                break;
            case 'paypal':
                $this->payPalService->handleWebhook($payload);
                break;
        }
        
        exit;
    }

}

