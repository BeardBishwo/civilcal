<?php

namespace App\Models;

use App\Core\Model;

class WordBank extends Model
{
    protected $table = 'word_bank';

    public function getRandomTerms($count = 5, $difficulty = null, $categoryId = null)
    {
        $params = [];
        $where = "1=1";

        if ($difficulty) {
            $where .= " AND wb.difficulty_level = ?";
            $params[] = $difficulty;
        }

        if ($categoryId) {
            $where .= " AND wb.category_id = ?";
            $params[] = $categoryId;
        }

        $sql = "SELECT wb.*, sn.title as category_name 
                FROM {$this->table} wb 
                LEFT JOIN syllabus_nodes sn ON wb.category_id = sn.id 
                WHERE {$where} 
                ORDER BY RAND() LIMIT " . (int)$count;

        return $this->db->query($sql, $params)->fetchAll();
    }

    /**
     * Get all active categories for word bank linking
     */
    public function getCategories()
    {
        return $this->db->query("SELECT id, title FROM syllabus_nodes WHERE type = 'category' AND is_active = 1 ORDER BY title ASC")->fetchAll();
    }

    /**
     * Get terms by category with stats
     */
    public function getTermsByCategory($categoryId = null)
    {
        if ($categoryId) {
            return $this->db->query("SELECT * FROM {$this->table} WHERE category_id = ? ORDER BY term ASC", [$categoryId])->fetchAll();
        }

        // Get all terms with category names
        $sql = "SELECT wb.*, sn.title as category_name 
                FROM {$this->table} wb 
                LEFT JOIN syllabus_nodes sn ON wb.category_id = sn.id 
                ORDER BY wb.term ASC";
        return $this->db->query($sql)->fetchAll();
    }

    /**
     * Record user's attempt at a word
     */
    public function recordProgress($userId, $termId, $isCorrect, $points = 0)
    {
        $table = 'word_bank_progress';

        // Create table if not exists
        $this->ensureProgressTable();

        $data = [
            'user_id' => $userId,
            'term_id' => $termId,
            'is_correct' => $isCorrect ? 1 : 0,
            'points' => $points,
            'attempted_at' => date('Y-m-d H:i:s')
        ];

        $keys = array_keys($data);
        $columns = implode(', ', array_map(fn($k) => "`$k`", $keys));
        $placeholders = ':' . implode(', :', $keys);

        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    /**
     * Get user statistics
     */
    public function getUserStats($userId)
    {
        $this->ensureProgressTable();

        $sql = "
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN is_correct = 1 THEN 1 ELSE 0 END) as correct,
                SUM(points) as points
            FROM word_bank_progress
            WHERE user_id = ?
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetch();
    }

    /**
     * Ensure progress table exists
     */
    private function ensureProgressTable()
    {
        $sql = "
            CREATE TABLE IF NOT EXISTS `word_bank_progress` (
                `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `user_id` INT UNSIGNED NOT NULL,
                `term_id` BIGINT UNSIGNED NOT NULL,
                `is_correct` TINYINT(1) DEFAULT 0,
                `points` INT DEFAULT 0,
                `attempted_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX (`user_id`),
                INDEX (`term_id`),
                FOREIGN KEY (`user_id`) REFERENCES users(id) ON DELETE CASCADE,
                FOREIGN KEY (`term_id`) REFERENCES word_bank(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";

        try {
            $this->db->getPdo()->exec($sql);
        } catch (\Exception $e) {
            // Table may already exist
        }
    }

    /**
     * Increment usage count for a term
     */
    public function incrementUsage($termId)
    {
        $sql = "UPDATE {$this->table} SET usage_count = usage_count + 1 WHERE id = ?";
        return $this->db->query($sql, [$termId]);
    }
}
