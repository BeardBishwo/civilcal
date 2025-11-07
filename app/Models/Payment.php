<?php

namespace App\Models;

use App\Core\Database;

class Payment {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function create($data) {
        $stmt = $this->db->getPdo()->prepare("
            INSERT INTO payments (user_id, subscription_id, amount, currency, payment_method, paypal_order_id, paypal_payer_id, status, billing_cycle, starts_at, ends_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        return $stmt->execute([
            $data['user_id'],
            $data['subscription_id'],
            $data['amount'],
            isset($data['currency']) ? $data['currency'] : 'USD',
            isset($data['payment_method']) ? $data['payment_method'] : 'paypal',
            isset($data['paypal_order_id']) ? $data['paypal_order_id'] : null,
            isset($data['paypal_payer_id']) ? $data['paypal_payer_id'] : null,
            isset($data['status']) ? $data['status'] : 'pending',
            isset($data['billing_cycle']) ? $data['billing_cycle'] : 'monthly',
            isset($data['starts_at']) ? $data['starts_at'] : date('Y-m-d H:i:s'),
            isset($data['ends_at']) ? $data['ends_at'] : date('Y-m-d H:i:s', strtotime('+1 month'))
        ]);
    }
    
    public function findByPaypalOrderId($orderId) {
        $stmt = $this->db->getPdo()->prepare("SELECT * FROM payments WHERE paypal_order_id = ?");
        $stmt->execute([$orderId]);
        return $stmt->fetch();
    }
    
    public function updateStatus($paymentId, $status) {
        $stmt = $this->db->getPdo()->prepare("UPDATE payments SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $paymentId]);
    }
}
