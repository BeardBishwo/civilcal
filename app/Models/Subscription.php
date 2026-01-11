<?php

namespace App\Models;

use App\Core\Database;

class Subscription {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function getAll() {
        $stmt = $this->db->getPdo()->query("SELECT * FROM subscriptions WHERE is_active = true ORDER BY price_monthly ASC");
        return $stmt->fetchAll();
    }
    
    public function find($id) {
        $stmt = $this->db->getPdo()->prepare("SELECT * FROM subscriptions WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function getFeatures($id) {
        $subscription = $this->find($id);
        return $subscription ? json_decode($subscription['features'], true) : [];
    }

    public function create($data) {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        
        $sql = "INSERT INTO subscription_plans ($columns) VALUES ($placeholders)";
        $stmt = $this->db->getPdo()->prepare($sql);
        return $stmt->execute(array_values($data));
    }

    public function update($id, $data) {
        $fields = [];
        $values = [];
        foreach ($data as $key => $value) {
            $fields[] = "$key = ?";
            $values[] = $value;
        }
        $values[] = $id;
        
        $sql = "UPDATE subscription_plans SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $this->db->getPdo()->prepare($sql);
        return $stmt->execute($values);
    }

    public function getTransactions($limit = 10) {
        $sql = "SELECT p.*, u.username as user, sp.name as plan 
                FROM payments p 
                JOIN users u ON p.user_id = u.id 
                LEFT JOIN subscription_plans sp ON p.subscription_id = sp.id 
                ORDER BY p.created_at DESC LIMIT ?";
        $stmt = $this->db->getPdo()->prepare($sql);
        $stmt->bindValue(1, (int)$limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getStats() {
        $stats = [
            'total_revenue' => 0,
            'monthly_recurring' => 0,
            'active_subscribers' => 0,
            'conversion_rate' => 0
        ];

        // Total Revenue
        $stmt = $this->db->getPdo()->query("SELECT SUM(amount) FROM payments WHERE status = 'completed'");
        $stats['total_revenue'] = (float)$stmt->fetchColumn();

        // Active Subscribers
        $stmt = $this->db->getPdo()->query("SELECT COUNT(*) FROM user_subscriptions WHERE status = 'active'");
        $stats['active_subscribers'] = (int)$stmt->fetchColumn();

        // Monthly Recurring (Estimated)
        $stmt = $this->db->getPdo()->query("SELECT SUM(amount) FROM user_subscriptions WHERE status = 'active' AND billing_cycle = 'monthly'");
        $stats['monthly_recurring'] = (float)$stmt->fetchColumn();

        return $stats;
    }
}
