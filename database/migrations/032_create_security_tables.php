<?php

namespace App\Database\Migrations;

use App\Core\Database;

class CreateSecurityTables
{
    public function up()
    {
        $db = Database::getInstance();
        $pdo = $db->getPdo();

        $sqlSecurityLogs = "
        CREATE TABLE IF NOT EXISTS `security_logs` (
            `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `user_id` INT NULL,
            `ip_address` VARCHAR(45) NULL,
            `event_type` VARCHAR(100) NOT NULL,
            `endpoint` VARCHAR(255) NULL,
            `details` JSON NULL,
            `severity` ENUM('low','medium','high','critical') DEFAULT 'medium',
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX `idx_user_created` (`user_id`, `created_at`),
            INDEX `idx_severity_created` (`severity`, `created_at`),
            CONSTRAINT `fk_security_logs_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

        $sqlRateLimits = "
        CREATE TABLE IF NOT EXISTS `rate_limits` (
            `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `user_id` INT NOT NULL,
            `endpoint` VARCHAR(255) NOT NULL,
            `request_count` INT UNSIGNED DEFAULT 0,
            `window_start` DATETIME NOT NULL,
            `last_request` DATETIME DEFAULT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX `idx_user_endpoint` (`user_id`, `endpoint`),
            INDEX `idx_window_start` (`window_start`),
            CONSTRAINT `fk_rate_limits_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

        $sqlBannedIps = "
        CREATE TABLE IF NOT EXISTS `banned_ips` (
            `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `ip_address` VARCHAR(45) NOT NULL UNIQUE,
            `reason` VARCHAR(255) NULL,
            `expires_at` DATETIME NULL,
            `is_permanent` TINYINT(1) DEFAULT 0,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX `idx_expires_at` (`expires_at`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

        $sqlNonces = "
        CREATE TABLE IF NOT EXISTS `nonces` (
            `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `user_id` INT NOT NULL,
            `nonce` VARCHAR(128) NOT NULL UNIQUE,
            `scope` VARCHAR(64) NOT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `expires_at` DATETIME NULL,
            `is_consumed` TINYINT(1) DEFAULT 0,
            `consumed_at` DATETIME NULL,
            INDEX `idx_user_scope` (`user_id`, `scope`),
            INDEX `idx_expires_at` (`expires_at`),
            CONSTRAINT `fk_nonces_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

        try {
            $pdo->exec($sqlSecurityLogs);
            $pdo->exec($sqlRateLimits);
            $pdo->exec($sqlBannedIps);
            $pdo->exec($sqlNonces);
            echo "Migrated: security tables created successfully.\n";
        } catch (\PDOException $e) {
            echo "Migration Failed: " . $e->getMessage() . "\n";
        }
    }

    public function down()
    {
        $db = Database::getInstance();
        $pdo = $db->getPdo();

        $pdo->exec("DROP TABLE IF EXISTS `nonces`");
        $pdo->exec("DROP TABLE IF EXISTS `banned_ips`");
        $pdo->exec("DROP TABLE IF EXISTS `rate_limits`");
        $pdo->exec("DROP TABLE IF EXISTS `security_logs`");

        echo "Dropped security tables.\n";
    }
}
