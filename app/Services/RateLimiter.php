<?php

namespace App\Services;

use App\Core\Database;
use Exception;

class RateLimiter
{
    private $db;
    private $table = 'rate_limits';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Check if action is allowed
     * 
     * @param string $key Identifier (User ID or IP)
     * @param string $action Action name
     * @param int $limit Max attempts
     * @param int $windowSeconds Time window in seconds
     * @return array ['allowed' => bool, 'remaining' => int]
     */
    public function check($key, $action, $limit, $windowSeconds)
    {
        $this->ensureTableExists();

        try {
            // Delete old records for this key/action to cleanup
            // (Optional: Garbage collection can be separate, but lazy verify is easier)

            $pdo = $this->db->getPdo();

            // cleanup expired windows globally occasionally? 
            // For now, just check specific record

            $stmt = $pdo->prepare("SELECT * FROM {$this->table} WHERE `key` = ? AND `action` = ?");
            $stmt->execute([$key, $action]);
            $record = $stmt->fetch(\PDO::FETCH_ASSOC);

            $now = time();

            if ($record) {
                $windowStart = strtotime($record['window_start']);

                if (($now - $windowStart) > $windowSeconds) {
                    // Window expired, reset
                    $resetStmt = $pdo->prepare("UPDATE {$this->table} SET hits = 1, window_start = FROM_UNIXTIME(?) WHERE `key` = ? AND `action` = ?");
                    $resetStmt->execute([$now, $key, $action]);

                    return ['allowed' => true, 'remaining' => $limit - 1];
                } else {
                    // Window active
                    if ($record['hits'] >= $limit) {
                        return ['allowed' => false, 'remaining' => 0];
                    } else {
                        // Increment
                        $incStmt = $pdo->prepare("UPDATE {$this->table} SET hits = hits + 1 WHERE `key` = ? AND `action` = ?");
                        $incStmt->execute([$key, $action]);

                        return ['allowed' => true, 'remaining' => $limit - ($record['hits'] + 1)];
                    }
                }
            } else {
                // First hit
                $insStmt = $pdo->prepare("INSERT INTO {$this->table} (`key`, `action`, hits, window_start) VALUES (?, ?, 1, FROM_UNIXTIME(?))");
                $insStmt->execute([$key, $action, $now]);

                return ['allowed' => true, 'remaining' => $limit - 1];
            }
        } catch (Exception $e) {
            // Fail open if DB error
            error_log("RateLimiter Error: " . $e->getMessage());
            return ['allowed' => true, 'remaining' => $limit];
        }
    }

    private function ensureTableExists()
    {
        // Simple check to avoid running CREATE TABLE every time
        // In production, this should be a migration
        static $checked = false;
        if ($checked) return;

        try {
            $pdo = $this->db->getPdo();
            $pdo->exec("
                CREATE TABLE IF NOT EXISTS {$this->table} (
                    `key` VARCHAR(191) NOT NULL,
                    `action` VARCHAR(191) NOT NULL,
                    `hits` INT DEFAULT 1,
                    `window_start` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (`key`, `action`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            ");
            $checked = true;
        } catch (Exception $e) {
            error_log("RateLimiter Table Creation Error: " . $e->getMessage());
        }
    }
}
