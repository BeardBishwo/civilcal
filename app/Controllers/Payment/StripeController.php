<?php

namespace App\Controllers\Payment;

use App\Core\Controller;
use App\Services\StripeService;
use App\Models\Plan; 
use App\Models\User;
USE App\Models\Subscription;

class StripeController extends Controller
{
    protected $stripeService;

    public function __construct()
    {
        parent::__construct();
        $this->stripeService = new StripeService();
    }

    /**
     * Initiate Checkout
     */
    public function checkout()
    {
        $this->requireAuth();

        $planId = $_GET['plan_id'] ?? null;
        $type = $_GET['type'] ?? 'monthly';

        if (!$planId) {
            $this->redirect('/pricing?error=invalid_plan');
        }

        // Ideally fetch plan from DB. Since I don't have Plan model code, I'll use a raw query or assumption.
        // Assuming there is a way to get Plan. For now, I'll use a helper or model.
        // Let's assume a generic DB fetch for now to be safe.
        $db = \App\Core\Database::getInstance()->getPdo();
        $stmt = $db->prepare("SELECT * FROM plans WHERE id = ?");
        $stmt->execute([$planId]);
        $plan = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$plan) {
            $this->redirect('/pricing?error=plan_not_found');
        }

        $user = $_SESSION['user']; 

        $result = $this->stripeService->createCheckoutSession($plan, $type, [
            'id' => $user['id'],
            'email' => $user['email']
        ]);

        if ($result['success']) {
            header("Location: " . $result['url']);
            exit;
        } else {
            $this->redirect('/pricing?error=' . urlencode($result['message']));
        }
    }

    /**
     * Handle Success Return
     */
    public function success()
    {
        $sessionId = $_GET['session_id'] ?? null;
        // In a real app, verify session here or wait for webhook.
        // For UI feedback:
        $this->view('payment/success', ['message' => 'Payment successful! Your subscription will be active shortly.']);
    }

    /**
     * Handle Cancellation
     */
    public function cancel()
    {
        $this->redirect('/pricing?info=cancelled');
    }

    /**
     * Webhook Handler
     */
    public function webhook()
    {
        $payload = @file_get_contents('php://input');
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';

        $event = $this->stripeService->verifyWebhook($payload, $sig_header);

        if (!$event) {
            http_response_code(400); // Bad Request
            exit();
        }

        // Handle the event
        switch ($event->type) {
            case 'checkout.session.completed':
                $session = $event->data->object;
                $this->handleCheckoutCompleted($session);
                break;
            case 'invoice.payment_succeeded':
                // Handle recurring payment success if needed
                break;
            // ... handle other events
        }

        http_response_code(200);
    }

    protected function handleCheckoutCompleted($session)
    {
        // Activate subscription in DB
        // Metadata contains user_id, plan_id
        $userId = $session->metadata->user_id;
        $planId = $session->metadata->plan_id;
        $billingCycle = $session->metadata->billing_cycle;
        $stripeSubId = $session->subscription;

        // DB Update Logic (Simplified)
        $db = \App\Core\Database::getInstance()->getPdo();
        
        // Calculate end date
        $endDate = ($billingCycle === 'yearly') ? date('Y-m-d H:i:s', strtotime('+1 year')) : date('Y-m-d H:i:s', strtotime('+1 month'));

        // Insert/Update User Subscription
        $stmt = $db->prepare("INSERT INTO user_subscriptions (user_id, plan_id, stripe_subscription_id, status, starts_at, ends_at, created_at) VALUES (?, ?, ?, 'active', NOW(), ?, NOW()) ON DUPLICATE KEY UPDATE plan_id=?, stripe_subscription_id=?, status='active', ends_at=?, updated_at=NOW()");
        $stmt->execute([$userId, $planId, $stripeSubId, $endDate, $planId, $stripeSubId, $endDate]);
    }
}
