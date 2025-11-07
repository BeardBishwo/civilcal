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
}
