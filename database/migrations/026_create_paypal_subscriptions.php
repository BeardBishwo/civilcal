<?php

class CreatePaypalSubscriptionsMigration {
    public function up($pdo = null) {
        if ($pdo === null) {
            $db = \App\Core\Database::getInstance();
            $pdo = $db->getPdo();
        }
        
        $sqlPath = BASE_PATH . '/database/paypal_subscription_schema.sql';
        if (file_exists($sqlPath)) {
            $sql = file_get_contents($sqlPath);
            // Split by semicolon but preserve those inside triggers/functions if possible
            // A simpler way is to just execute it if it's clean or use exec()
            $pdo->exec($sql);
        }
    }
    
    public function down($pdo = null) {
        if ($pdo === null) {
            $db = \App\Core\Database::getInstance();
            $pdo = $db->getPdo();
        }
        
        $tables = [
            'webhook_events', 
            'payment_methods', 
            'invoices', 
            'payments', 
            'user_subscriptions'
        ];
        
        foreach ($tables as $table) {
            $pdo->exec("DROP TABLE IF EXISTS $table");
        }
    }
}
