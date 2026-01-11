<?php
/**
 * Webhook Controller
 * 
 * Handles PayPal webhook events for automatic subscription updates
 */

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Services\PayPalService;
use App\Models\UserSubscription;
use App\Models\Payment;

class WebhookController extends Controller
{
    private $paypalService;
    private $subscriptionModel;
    private $paymentModel;
    private $db;
    
    public function __construct()
    {
        parent::__construct();
        $this->paypalService = new PayPalService();
        $this->subscriptionModel = new UserSubscription();
        $this->paymentModel = new Payment();
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Handle PayPal webhook events
     */
    public function paypal()
    {
        // Get webhook payload
        $payload = file_get_contents('php://input');
        $headers = getallheaders();
        
        // Log the webhook event
        $this->logWebhookEvent($payload, $headers);
        
        // Parse payload
        $event = json_decode($payload, true);
        
        if (!$event || !isset($event['event_type'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid payload']);
            return;
        }
        
        // Verify webhook signature (Mandatory for security)
        if (!$this->paypalService->verifyWebhookSignature($headers, $payload)) {
            error_log('PayPal webhook signature verification failed - REJECTED');
            http_response_code(403);
            echo json_encode(['error' => 'Unverified webhook signature']);
            return;
        }
        
        // Process event based on type
        try {
            $this->processEvent($event);
            
            // Update webhook event as processed
            $this->markWebhookProcessed($event['id']);
            
            http_response_code(200);
            echo json_encode(['success' => true]);
            
        } catch (\Exception $e) {
            // Log error
            error_log('Webhook processing error: ' . $e->getMessage());
            $this->logWebhookError($event['id'], $e->getMessage());
            
            http_response_code(500);
            echo json_encode(['error' => 'Processing failed']);
        }
    }
    
    /**
     * Process webhook event based on type
     */
    private function processEvent($event)
    {
        $eventType = $event['event_type'];
        
        switch ($eventType) {
            case 'BILLING.SUBSCRIPTION.CREATED':
                $this->handleSubscriptionCreated($event);
                break;
                
            case 'BILLING.SUBSCRIPTION.ACTIVATED':
                $this->handleSubscriptionActivated($event);
                break;
                
            case 'PAYMENT.SALE.COMPLETED':
                $this->handlePaymentCompleted($event);
                break;
                
            case 'BILLING.SUBSCRIPTION.CANCELLED':
                $this->handleSubscriptionCancelled($event);
                break;
                
            case 'BILLING.SUBSCRIPTION.SUSPENDED':
                $this->handleSubscriptionSuspended($event);
                break;
                
            case 'BILLING.SUBSCRIPTION.EXPIRED':
                $this->handleSubscriptionExpired($event);
                break;
                
            case 'BILLING.SUBSCRIPTION.UPDATED':
                $this->handleSubscriptionUpdated($event);
                break;
                
            case 'PAYMENT.SALE.REFUNDED':
                $this->handlePaymentRefunded($event);
                break;
                
            default:
                // Log unhandled event type
                error_log('Unhandled webhook event type: ' . $eventType);
        }
    }
    
    /**
     * Handle subscription created event
     */
    private function handleSubscriptionCreated($event)
    {
        $resource = $event['resource'];
        $subscriptionId = $resource['id'];
        
        // Update subscription status if exists
        $subscription = $this->subscriptionModel->getByPayPalId($subscriptionId);
        
        if ($subscription) {
            $this->subscriptionModel->updateStatus($subscription['id'], 'pending');
        }
    }
    
    /**
     * Handle subscription activated event
     */
    private function handleSubscriptionActivated($event)
    {
        $resource = $event['resource'];
        $subscriptionId = $resource['id'];
        
        $subscription = $this->subscriptionModel->getByPayPalId($subscriptionId);
        
        if ($subscription) {
            $this->subscriptionModel->updateStatus($subscription['id'], 'active');
        }
    }
    
    /**
     * Handle payment completed event
     */
    private function handlePaymentCompleted($event)
    {
        $resource = $event['resource'];
        $billingAgreementId = $resource['billing_agreement_id'] ?? null;
        
        if (!$billingAgreementId) {
            return;
        }
        
        // Get subscription
        $subscription = $this->subscriptionModel->getByPayPalId($billingAgreementId);
        
        if (!$subscription) {
            return;
        }
        
        // Create payment record
        $paymentData = [
            'user_id' => $subscription['user_id'],
            'subscription_id' => $subscription['id'],
            'invoice_id' => null,
            'paypal_payment_id' => $resource['id'],
            'paypal_order_id' => $resource['parent_payment'] ?? null,
            'paypal_payer_id' => $resource['payer']['payer_info']['payer_id'] ?? null,
            'amount' => $resource['amount']['total'],
            'currency' => $resource['amount']['currency'],
            'payment_method' => 'paypal',
            'status' => 'completed',
            'transaction_type' => 'subscription',
            'description' => 'Subscription payment',
            'metadata' => json_encode($resource)
        ];
        
        $this->paymentModel->create($paymentData);
        
        // Update subscription next billing date
        $currentEnd = new \DateTime($subscription['current_period_end']);
        $nextBilling = clone $currentEnd;
        
        if ($subscription['billing_cycle'] === 'yearly') {
            $nextBilling->modify('+1 year');
        } else {
            $nextBilling->modify('+1 month');
        }
        
        $sql = "UPDATE user_subscriptions SET 
                current_period_start = :period_start,
                current_period_end = :period_end,
                next_billing_date = :next_billing
                WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'period_start' => $currentEnd->format('Y-m-d H:i:s'),
            'period_end' => $nextBilling->format('Y-m-d H:i:s'),
            'next_billing' => $nextBilling->format('Y-m-d H:i:s'),
            'id' => $subscription['id']
        ]);
    }
    
    /**
     * Handle subscription cancelled event
     */
    private function handleSubscriptionCancelled($event)
    {
        $resource = $event['resource'];
        $subscriptionId = $resource['id'];
        
        $subscription = $this->subscriptionModel->getByPayPalId($subscriptionId);
        
        if ($subscription) {
            $this->subscriptionModel->updateStatus($subscription['id'], 'cancelled', [
                'cancelled_at' => date('Y-m-d H:i:s'),
                'cancellation_reason' => 'Cancelled via PayPal'
            ]);
        }
    }
    
    /**
     * Handle subscription suspended event
     */
    private function handleSubscriptionSuspended($event)
    {
        $resource = $event['resource'];
        $subscriptionId = $resource['id'];
        
        $subscription = $this->subscriptionModel->getByPayPalId($subscriptionId);
        
        if ($subscription) {
            $this->subscriptionModel->updateStatus($subscription['id'], 'suspended');
        }
    }
    
    /**
     * Handle subscription expired event
     */
    private function handleSubscriptionExpired($event)
    {
        $resource = $event['resource'];
        $subscriptionId = $resource['id'];
        
        $subscription = $this->subscriptionModel->getByPayPalId($subscriptionId);
        
        if ($subscription) {
            $this->subscriptionModel->updateStatus($subscription['id'], 'expired');
        }
    }
    
    /**
     * Handle subscription updated event
     */
    private function handleSubscriptionUpdated($event)
    {
        // Handle subscription updates (plan changes, etc.)
        $resource = $event['resource'];
        $subscriptionId = $resource['id'];
        
        // Log for now, implement specific logic as needed
        error_log('Subscription updated: ' . $subscriptionId);
    }
    
    /**
     * Handle payment refunded event
     */
    private function handlePaymentRefunded($event)
    {
        $resource = $event['resource'];
        $saleId = $resource['sale_id'] ?? null;
        
        if (!$saleId) {
            return;
        }
        
        // Find payment by PayPal ID
        $sql = "SELECT * FROM payments WHERE paypal_payment_id = :payment_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['payment_id' => $saleId]);
        $payment = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if ($payment) {
            // Update payment status
            $sql = "UPDATE payments SET 
                    status = 'refunded',
                    refunded_amount = :amount,
                    refunded_at = NOW(),
                    refund_reason = :reason
                    WHERE id = :id";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'amount' => $resource['amount']['total'],
                'reason' => 'PayPal refund',
                'id' => $payment['id']
            ]);
        }
    }
    
    /**
     * Log webhook event to database
     */
    private function logWebhookEvent($payload, $headers)
    {
        $event = json_decode($payload, true);
        
        if (!$event) {
            return;
        }
        
        $sql = "INSERT INTO webhook_events (
            event_id, event_type, resource_type, resource_id, payload
        ) VALUES (
            :event_id, :event_type, :resource_type, :resource_id, :payload
        )";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'event_id' => $event['id'] ?? uniqid('webhook_'),
            'event_type' => $event['event_type'] ?? 'unknown',
            'resource_type' => $event['resource_type'] ?? null,
            'resource_id' => $event['resource']['id'] ?? null,
            'payload' => $payload
        ]);
    }
    
    /**
     * Mark webhook as processed
     */
    private function markWebhookProcessed($eventId)
    {
        $sql = "UPDATE webhook_events SET 
                processed = 1, 
                processed_at = NOW() 
                WHERE event_id = :event_id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['event_id' => $eventId]);
    }
    
    /**
     * Log webhook processing error
     */
    private function logWebhookError($eventId, $error)
    {
        $sql = "UPDATE webhook_events SET 
                processing_error = :error 
                WHERE event_id = :event_id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'event_id' => $eventId,
            'error' => $error
        ]);
    }
}
