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

        // SECURITY: CSRF Protection
        if (!isset($_POST['csrf_token']) || !\App\Services\Security::validateCsrfToken($_POST['csrf_token'])) {
            die('Invalid CSRF token. Please refresh the page and try again.');
        }

        $user = (new \App\Models\User())->find($_SESSION['user_id']);
        $planId = $_POST['plan_id'] ?? null;
        $type = $_POST['type'] ?? 'monthly';

        if (!$planId) {
            die('Plan ID required');
        }

        // SECURITY: Store payment intent in session to prevent callback manipulation
        $_SESSION['pending_payment'] = [
            'plan_id' => $planId,
            'type' => $type,
            'user_id' => $_SESSION['user_id'],
            'created_at' => time()
        ];

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

        // SECURITY: Validate payment against session to prevent manipulation
        $pendingPayment = $_SESSION['pending_payment'] ?? null;

        if (!$pendingPayment) {
            error_log("Payment callback without pending payment session");
            header('Location: ' . app_base_url('/pricing?payment=failed&reason=no_session'));
            exit;
        }

        // Check session hasn't expired (30 minutes)
        if (time() - $pendingPayment['created_at'] > 1800) {
            unset($_SESSION['pending_payment']);
            error_log("Payment callback with expired session");
            header('Location: ' . app_base_url('/pricing?payment=failed&reason=expired'));
            exit;
        }

        try {
            switch ($gateway) {
                case 'stripe':
                    $sessionId = $_GET['session_id'] ?? null;

                    // SECURITY: Use plan_id from session, not URL
                    if ($sessionId) {
                        $success = $this->stripeService->handleCallback(
                            $sessionId,
                            $pendingPayment['plan_id'],  // From session, not URL!
                            $pendingPayment['type']       // From session, not URL!
                        );
                    }
                    break;

                case 'paypal':
                    $paymentId = $_GET['paymentId'] ?? null;
                    $payerId = $_GET['PayerID'] ?? null;

                    // SECURITY: Use plan_id from session, not URL
                    if ($paymentId && $payerId) {
                        $success = $this->payPalService->handleCallback(
                            $paymentId,
                            $payerId,
                            $pendingPayment['plan_id'],  // From session, not URL!
                            $pendingPayment['type']       // From session, not URL!
                        );
                    }
                    break;

                case 'mollie':
                    $paymentId = $_GET['id'] ?? null;

                    // SECURITY: Use plan_id from session, not URL
                    if ($paymentId) {
                        $success = $this->mollieService->verifyPayment($paymentId);
                    }
                    break;

                case 'paystack':
                    $reference = $_GET['reference'] ?? null;

                    // SECURITY: Use plan_id from session, not URL
                    if ($reference) {
                        $success = $this->payStackService->handleCallback(
                            $reference,
                            $pendingPayment['plan_id'],  // From session, not URL!
                            $pendingPayment['type']       // From session, not URL!
                        );
                    }
                    break;
            }
        } catch (\Exception $e) {
            error_log("Callback Error [$gateway]: " . $e->getMessage());
        }

        // Clear pending payment from session
        unset($_SESSION['pending_payment']);

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
