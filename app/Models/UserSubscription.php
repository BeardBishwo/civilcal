<?php
/**
 * User Subscription Model
 * 
 * Manages user subscription data
 */

namespace App\Models;

use App\Core\Database;

class UserSubscription
{
    private $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Create a new subscription
     */
    public function create($data)
    {
        $sql = "INSERT INTO user_subscriptions (
            user_id, plan_id, paypal_subscription_id, paypal_plan_id,
            status, billing_cycle, amount, currency,
            current_period_start, current_period_end, next_billing_date,
            is_trial, trial_start, trial_end
        ) VALUES (
            :user_id, :plan_id, :paypal_subscription_id, :paypal_plan_id,
            :status, :billing_cycle, :amount, :currency,
            :current_period_start, :current_period_end, :next_billing_date,
            :is_trial, :trial_start, :trial_end
        )";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }
    
    /**
     * Get user's active subscription
     */
    public function getActiveSubscription($userId)
    {
        $sql = "SELECT us.*, sp.name as plan_name, sp.features 
                FROM user_subscriptions us
                INNER JOIN subscription_plans sp ON us.plan_id = sp.id
                WHERE us.user_id = :user_id AND us.status = 'active'
                ORDER BY us.created_at DESC
                LIMIT 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
    
    /**
     * Update subscription status
     */
    public function updateStatus($subscriptionId, $status, $additionalData = [])
    {
        $sql = "UPDATE user_subscriptions SET status = :status, updated_at = NOW()";
        $params = ['subscription_id' => $subscriptionId, 'status' => $status];
        
        if (isset($additionalData['cancelled_at'])) {
            $sql .= ", cancelled_at = :cancelled_at";
            $params['cancelled_at'] = $additionalData['cancelled_at'];
        }
        
        if (isset($additionalData['cancellation_reason'])) {
            $sql .= ", cancellation_reason = :cancellation_reason";
            $params['cancellation_reason'] = $additionalData['cancellation_reason'];
        }
        
        $sql .= " WHERE id = :subscription_id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }
    
    /**
     * Get subscription by PayPal ID
     */
    public function getByPayPalId($paypalSubscriptionId)
    {
        $sql = "SELECT * FROM user_subscriptions WHERE paypal_subscription_id = :paypal_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['paypal_id' => $paypalSubscriptionId]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
    
    /**
     * Get all user subscriptions
     */
    public function getUserSubscriptions($userId)
    {
        $sql = "SELECT us.*, sp.name as plan_name 
                FROM user_subscriptions us
                INNER JOIN subscription_plans sp ON us.plan_id = sp.id
                WHERE us.user_id = :user_id
                ORDER BY us.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
