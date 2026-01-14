<?php

/**
 * Subscription Controller (User-facing)
 * 
 * Handles subscription checkout and management for users
 */

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Services\PayPalService;
use App\Models\UserSubscription;
use App\Models\Payment;

class SubscriptionController extends Controller
{
    private $paypalService;
    private $subscriptionModel;
    private $paymentModel;

    public function __construct()
    {
        parent::__construct();
        $this->paypalService = new PayPalService();
        $this->subscriptionModel = new UserSubscription();
        $this->paymentModel = new Payment();
    }

    /**
     * Show checkout page for a specific plan
     */
    public function checkout($planId)
    {
        // Get plan details
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM subscription_plans WHERE id = ? AND is_active = 1");
        $stmt->execute([$planId]);
        $plan = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$plan) {
            $_SESSION['error'] = 'Plan not found';
            header('Location: ' . app_base_url('/pricing'));
            exit;
        }

        // Check if user already has an active subscription
        if (isset($_SESSION['user_id'])) {
            $activeSubscription = $this->subscriptionModel->getActiveSubscription($_SESSION['user_id']);
            if ($activeSubscription) {
                $_SESSION['info'] = 'You already have an active subscription';
                header('Location: ' . app_base_url('/user/subscription'));
                exit;
            }
        }

        // Decode features if JSON
        if (is_string($plan['features'])) {
            $plan['features'] = json_decode($plan['features'], true);
        }

        $this->view->render('subscriptions/checkout', [
            'plan' => $plan,
            'title' => 'Subscribe to ' . $plan['name']
        ]);
    }

    /**
     * Create subscription (AJAX)
     */
    public function create()
    {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Please login first']);
            return;
        }

        $planId = $_POST['plan_id'] ?? null;
        $billingCycle = $_POST['billing_cycle'] ?? 'monthly';

        if (!$planId) {
            echo json_encode(['success' => false, 'message' => 'Plan ID required']);
            return;
        }

        // Get plan details
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM subscription_plans WHERE id = ?");
        $stmt->execute([$planId]);
        $plan = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$plan) {
            echo json_encode(['success' => false, 'message' => 'Plan not found']);
            return;
        }

        // Get PayPal plan ID based on billing cycle
        $paypalPlanId = $billingCycle === 'yearly'
            ? $plan['paypal_plan_id_yearly']
            : $plan['paypal_plan_id_monthly'];

        if (empty($paypalPlanId)) {
            echo json_encode(['success' => false, 'message' => 'PayPal plan not configured']);
            return;
        }

        // Get user data
        $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);

        // Create subscription with PayPal
        $userData = [
            'plan_name' => $plan['name'],
            'user_email' => $user['email'],
            'user_name' => $user['username']
        ];

        $result = $this->paypalService->createSubscription($paypalPlanId, $userData);

        if ($result['success']) {
            // Store pending subscription in session
            $_SESSION['pending_subscription'] = [
                'plan_id' => $planId,
                'billing_cycle' => $billingCycle,
                'token' => $result['token']
            ];

            echo json_encode([
                'success' => true,
                'approval_url' => $result['approval_url'],
                'token' => $result['token']
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => $result['message']
            ]);
        }
    }

    /**
     * Handle successful subscription approval
     */
    public function success()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . app_base_url('/login'));
            exit;
        }

        $token = $_GET['token'] ?? null;
        $pendingData = $_SESSION['pending_subscription'] ?? null;

        if (!$token || !$pendingData) {
            $_SESSION['error'] = 'Invalid subscription request';
            header('Location: ' . app_base_url('/pricing'));
            exit;
        }

        // SECURITY: Prevent session swap exploit - verify token matches
        if ($pendingData['token'] !== $token) {
            error_log("Security Alert: Token mismatch for user " . $_SESSION['user_id'] . ". Expected: {$pendingData['token']}, Got: $token");
            $_SESSION['error'] = 'Security error: Payment token mismatch';
            header('Location: ' . app_base_url('/pricing'));
            exit;
        }

        // Execute the subscription
        $result = $this->paypalService->executeSubscription($token);

        if ($result['success']) {
            // Get plan details
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT * FROM subscription_plans WHERE id = ?");
            $stmt->execute([$pendingData['plan_id']]);
            $plan = $stmt->fetch(\PDO::FETCH_ASSOC);

            // 1. Get current user subscription info
            $stmtUser = $db->prepare("SELECT subscription_id, subscription_ends_at FROM users WHERE id = ?");
            $stmtUser->execute([$_SESSION['user_id']]);
            $userSub = $stmtUser->fetch(\PDO::FETCH_ASSOC);

            $startTime = time();
            if ($userSub && !empty($userSub['subscription_ends_at'])) {
                $currentExpiry = strtotime($userSub['subscription_ends_at']);
                if ($currentExpiry > $startTime) {
                    $startTime = $currentExpiry;
                }
            }

            $now = new \DateTime();
            $periodEnd = (new \DateTime())->setTimestamp($startTime);

            if ($pendingData['billing_cycle'] === 'yearly') {
                $periodEnd->modify('+1 year');
            } else {
                $periodEnd->modify('+1 month');
            }

            // 2. SECURITY: Prevent subscription downgrade
            $shouldUpdatePlanId = false;
            $currentPlanId = $userSub['subscription_id'] ?? null;

            if (!$currentPlanId || empty($userSub['subscription_ends_at'])) {
                // No current subscription, allow any plan
                $shouldUpdatePlanId = true;
            } elseif (strtotime($userSub['subscription_ends_at']) < time()) {
                // Current subscription expired, allow any plan
                $shouldUpdatePlanId = true;
            } elseif ($currentPlanId) {
                // Compare plan tiers to prevent downgrades
                $stmtCurrent = $db->prepare("SELECT price_monthly FROM subscription_plans WHERE id = ?");
                $stmtCurrent->execute([$currentPlanId]);
                $currentPlan = $stmtCurrent->fetch(\PDO::FETCH_ASSOC);

                if ($currentPlan) {
                    $currentPrice = $currentPlan['price_monthly'] ?? 0;
                    $newPrice = $plan['price_monthly'] ?? 0;

                    if ($newPrice >= $currentPrice) {
                        // Upgrading or same tier, allow
                        $shouldUpdatePlanId = true;
                    }
                    // else: Downgrade attempt, keep current plan ID
                } else {
                    // Can't find current plan, default to allowing
                    $shouldUpdatePlanId = true;
                }
            }

            // 3. Update Users Table (Fast Access)
            if ($shouldUpdatePlanId) {
                $stmtUpd = $db->prepare("
                    UPDATE users 
                    SET subscription_id = ?, subscription_status = 'active', subscription_ends_at = ? 
                    WHERE id = ?
                ");
                $stmtUpd->execute([
                    $pendingData['plan_id'],
                    $periodEnd->format('Y-m-d H:i:s'),
                    $_SESSION['user_id']
                ]);
            } else {
                // Just extend expiry, keep current plan (prevent downgrade)
                $stmtUpd = $db->prepare("
                    UPDATE users 
                    SET subscription_status = 'active', subscription_ends_at = ? 
                    WHERE id = ?
                ");
                $stmtUpd->execute([
                    $periodEnd->format('Y-m-d H:i:s'),
                    $_SESSION['user_id']
                ]);
            }

            // 4. Create Subscription Record (Audit History)
            $subscriptionData = [
                'user_id' => $_SESSION['user_id'],
                'plan_id' => $pendingData['plan_id'],
                'paypal_subscription_id' => $result['subscription_id'],
                'paypal_plan_id' => $pendingData['billing_cycle'] === 'yearly'
                    ? $plan['paypal_plan_id_yearly']
                    : $plan['paypal_plan_id_monthly'],
                'status' => 'active',
                'billing_cycle' => $pendingData['billing_cycle'],
                'amount' => $pendingData['billing_cycle'] === 'yearly'
                    ? $plan['price_yearly']
                    : $plan['price_monthly'],
                'currency' => 'USD',
                'current_period_start' => $now->format('Y-m-d H:i:s'),
                'current_period_end' => $periodEnd->format('Y-m-d H:i:s'),
                'next_billing_date' => $periodEnd->format('Y-m-d H:i:s'),
                'is_trial' => 0,
                'trial_start' => null,
                'trial_end' => null
            ];

            $this->subscriptionModel->create($subscriptionData);

            // Clear pending subscription
            unset($_SESSION['pending_subscription']);

            $_SESSION['success'] = 'Subscription activated successfully!';

            $this->view->render('subscriptions/success', [
                'plan' => $plan,
                'subscription' => $result['agreement'],
                'title' => 'Subscription Successful'
            ]);
        } else {
            $_SESSION['error'] = 'Failed to activate subscription: ' . $result['message'];
            header('Location: ' . app_base_url('/pricing'));
            exit;
        }
    }

    /**
     * Handle cancelled checkout
     */
    public function cancel()
    {
        unset($_SESSION['pending_subscription']);

        $this->view->render('subscriptions/cancel', [
            'title' => 'Subscription Cancelled'
        ]);
    }
}
