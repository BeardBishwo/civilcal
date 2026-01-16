<?php

namespace App\Models;

use App\Core\Model;

class BlueprintReveal extends Model
{
    protected $table = 'blueprint_reveals';

    /**
     * Get user progress for all blueprints
     */
    public function getUserProgress($userId)
    {
        return $this->where(['user_id' => $userId]);
    }

    /**
     * Get progress for specific blueprint
     */
    public function getBlueprintProgress($userId, $blueprintId)
    {
        $result = $this->where(['user_id' => $userId, 'blueprint_id' => $blueprintId]);
        return $result ? $result[0] : null;
    }

    /**
     * Update progress with section-level tracking
     */
    public function updateProgress($userId, $blueprintId, $sectionsRevealed = [], $totalAttempts = null, $bestScore = null, $hintsUsed = null)
    {
        // Calculate percentage based on sections revealed
        $totalSections = count($sectionsRevealed);
        $revealedCount = count(array_filter($sectionsRevealed, function($section) {
            return $section['revealed'] ?? false;
        }));
        $percentage = $totalSections > 0 ? round(($revealedCount / $totalSections) * 100) : 0;

        $data = [
            'revealed_percentage' => $percentage,
            'revealed_sections' => json_encode($sectionsRevealed)
        ];

        if ($totalAttempts !== null) {
            $data['total_attempts'] = $totalAttempts;
        }

        if ($bestScore !== null) {
            $data['best_score'] = $bestScore;
        }

        if ($hintsUsed !== null) {
            $data['hints_used'] = $hintsUsed;
        }

        // Check if blueprint is fully revealed
        if ($percentage >= 100) {
            $data['completed_at'] = date('Y-m-d H:i:s');
        }

        $sql = "INSERT INTO {$this->table} (user_id, blueprint_id, revealed_percentage, revealed_sections, total_attempts, best_score, hints_used, completed_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE
                    revealed_percentage = GREATEST(revealed_percentage, VALUES(revealed_percentage)),
                    revealed_sections = VALUES(revealed_sections),
                    total_attempts = VALUES(total_attempts),
                    best_score = GREATEST(COALESCE(best_score, 0), VALUES(best_score)),
                    hints_used = hints_used + VALUES(hints_used),
                    completed_at = COALESCE(completed_at, VALUES(completed_at))";

        return $this->db->query($sql, [
            $userId,
            $blueprintId,
            $data['revealed_percentage'],
            $data['revealed_sections'],
            $data['total_attempts'] ?? 1,
            $data['best_score'] ?? 0,
            $data['hints_used'] ?? 0,
            $data['completed_at'] ?? null
        ]);
    }

    /**
     * Record a blueprint attempt
     */
    public function recordAttempt($userId, $blueprintId, $sectionsRevealed, $correctMatches, $totalMatches, $hintsUsed, $timeTaken)
    {
        $score = $totalMatches > 0 ? round(($correctMatches / $totalMatches) * 100) : 0;

        $sql = "INSERT INTO blueprint_attempts
                (user_id, blueprint_id, sections_revealed, correct_matches, total_matches, hints_used, time_taken_seconds, final_score, completed_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP)";

        return $this->db->query($sql, [
            $userId,
            $blueprintId,
            $sectionsRevealed,
            $correctMatches,
            $totalMatches,
            $hintsUsed,
            $timeTaken,
            $score
        ]);
    }

    /**
     * Get user statistics
     */
    public function getUserStats($userId)
    {
        $sql = "SELECT
                    COUNT(DISTINCT br.blueprint_id) as blueprints_started,
                    COUNT(DISTINCT CASE WHEN br.revealed_percentage >= 100 THEN br.blueprint_id END) as blueprints_completed,
                    AVG(br.revealed_percentage) as avg_completion,
                    SUM(br.total_attempts) as total_attempts,
                    AVG(br.best_score) as avg_best_score,
                    SUM(br.hints_used) as total_hints_used
                FROM {$this->table} br
                WHERE br.user_id = ?";

        $result = $this->db->query($sql, [$userId]);
        return $result ? $result[0] : null;
    }

    /**
     * Get leaderboard for blueprint
     */
    public function getBlueprintLeaderboard($blueprintId, $limit = 10)
    {
        $sql = "SELECT
                    u.username,
                    br.revealed_percentage,
                    br.best_score,
                    br.total_attempts,
                    br.completed_at,
                    br.hints_used
                FROM {$this->table} br
                JOIN users u ON br.user_id = u.id
                WHERE br.blueprint_id = ? AND br.revealed_percentage > 0
                ORDER BY br.revealed_percentage DESC, br.best_score DESC, br.total_attempts ASC
                LIMIT ?";

        return $this->db->query($sql, [$blueprintId, $limit]);
    }

    /**
     * Check if user has prerequisite blueprints completed
     */
    public function hasPrerequisites($userId, $prerequisiteIds)
    {
        if (empty($prerequisiteIds)) {
            return true;
        }

        $placeholders = str_repeat('?,', count($prerequisiteIds) - 1) . '?';
        $sql = "SELECT COUNT(*) as completed_count
                FROM {$this->table}
                WHERE user_id = ? AND blueprint_id IN ({$placeholders}) AND revealed_percentage >= 100";

        $params = array_merge([$userId], $prerequisiteIds);
        $result = $this->db->query($sql, $params);

        return $result && $result[0]['completed_count'] == count($prerequisiteIds);
    }
}
