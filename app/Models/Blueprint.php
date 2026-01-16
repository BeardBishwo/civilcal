<?php

namespace App\Models;

use App\Core\Model;

class Blueprint extends Model
{
    protected $table = 'blueprints';

    /**
     * Get all available blueprints
     */
    public function getAllBlueprints()
    {
        $sql = "SELECT b.*,
                       COUNT(bs.id) as total_sections,
                       COALESCE(br.revealed_sections, '[]') as user_progress
                FROM {$this->table} b
                LEFT JOIN blueprint_sections bs ON b.id = bs.blueprint_id
                LEFT JOIN blueprint_reveals br ON b.id = br.blueprint_id
                    AND br.user_id = ?
                WHERE b.is_active = 1
                GROUP BY b.id
                ORDER BY b.difficulty_level ASC, b.created_at DESC";

        return $this->db->query($sql, [$_SESSION['user_id'] ?? 0]);
    }

    /**
     * Get blueprint by ID with full details
     */
    public function getBlueprintById($id)
    {
        $sql = "SELECT b.*,
                       COUNT(bs.id) as total_sections,
                       COALESCE(br.revealed_sections, '[]') as user_progress,
                       br.total_attempts,
                       br.best_score
                FROM {$this->table} b
                LEFT JOIN blueprint_sections bs ON b.id = bs.blueprint_id
                LEFT JOIN blueprint_reveals br ON b.id = br.blueprint_id
                    AND br.user_id = ?
                WHERE b.id = ? AND b.is_active = 1
                GROUP BY b.id";

        $result = $this->db->query($sql, [$_SESSION['user_id'] ?? 0, $id]);
        return $result ? $result[0] : null;
    }

    /**
     * Get blueprint by slug
     */
    public function getBlueprintBySlug($slug)
    {
        $sql = "SELECT b.*,
                       COUNT(bs.id) as total_sections,
                       COALESCE(br.revealed_sections, '[]') as user_progress
                FROM {$this->table} b
                LEFT JOIN blueprint_sections bs ON b.id = bs.blueprint_id
                LEFT JOIN blueprint_reveals br ON b.id = br.blueprint_id
                    AND br.user_id = ?
                WHERE b.slug = ? AND b.is_active = 1
                GROUP BY b.id";

        $result = $this->db->query($sql, [$_SESSION['user_id'] ?? 0, $slug]);
        return $result ? $result[0] : null;
    }

    /**
     * Get blueprint sections for progressive revelation
     */
    public function getBlueprintSections($blueprintId)
    {
        $sql = "SELECT * FROM blueprint_sections
                WHERE blueprint_id = ?
                ORDER BY section_order ASC";

        return $this->db->query($sql, [$blueprintId]);
    }

    /**
     * Get blueprints by difficulty level
     */
    public function getBlueprintsByDifficulty($difficulty)
    {
        $sql = "SELECT b.*,
                       COUNT(bs.id) as total_sections,
                       COALESCE(br.revealed_sections, '[]') as user_progress
                FROM {$this->table} b
                LEFT JOIN blueprint_sections bs ON b.id = bs.blueprint_id
                LEFT JOIN blueprint_reveals br ON b.id = br.blueprint_id
                    AND br.user_id = ?
                WHERE b.difficulty_level = ? AND b.is_active = 1
                GROUP BY b.id
                ORDER BY b.created_at DESC";

        return $this->db->query($sql, [$_SESSION['user_id'] ?? 0, $difficulty]);
    }

    /**
     * Get blueprints by category
     */
    public function getBlueprintsByCategory($category)
    {
        $sql = "SELECT b.*,
                       COUNT(bs.id) as total_sections,
                       COALESCE(br.revealed_sections, '[]') as user_progress
                FROM {$this->table} b
                LEFT JOIN blueprint_sections bs ON b.id = bs.blueprint_id
                LEFT JOIN blueprint_reveals br ON b.id = br.blueprint_id
                    AND br.user_id = ?
                WHERE b.category = ? AND b.is_active = 1
                GROUP BY b.id
                ORDER BY b.difficulty_level ASC";

        return $this->db->query($sql, [$_SESSION['user_id'] ?? 0, $category]);
    }

    /**
     * Create new blueprint
     */
    public function createBlueprint($data)
    {
        $sql = "INSERT INTO {$this->table} (
                    slug, title, description, category, difficulty_level,
                    prerequisite_blueprint_id, estimated_completion_time,
                    full_svg_content, layer_definitions, preview_image,
                    learning_objectives, key_terms, syllabus_topic_ids,
                    total_sections, base_reward_coins, hint_penalty_coins,
                    created_by
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        return $this->db->query($sql, [
            $data['slug'],
            $data['title'],
            $data['description'],
            $data['category'],
            $data['difficulty_level'] ?? 1,
            $data['prerequisite_blueprint_id'] ?? null,
            $data['estimated_completion_time'] ?? 10,
            $data['full_svg_content'],
            json_encode($data['layer_definitions'] ?? []),
            $data['preview_image'] ?? null,
            json_encode($data['learning_objectives'] ?? []),
            json_encode($data['key_terms'] ?? []),
            json_encode($data['syllabus_topic_ids'] ?? []),
            $data['total_sections'] ?? 5,
            $data['base_reward_coins'] ?? 50,
            $data['hint_penalty_coins'] ?? 5,
            $_SESSION['user_id']
        ]);
    }

    /**
     * Update blueprint
     */
    public function updateBlueprint($id, $data)
    {
        $sql = "UPDATE {$this->table} SET
                    title = ?, description = ?, category = ?,
                    difficulty_level = ?, prerequisite_blueprint_id = ?,
                    estimated_completion_time = ?, full_svg_content = ?,
                    layer_definitions = ?, preview_image = ?,
                    learning_objectives = ?, key_terms = ?,
                    syllabus_topic_ids = ?, total_sections = ?,
                    base_reward_coins = ?, hint_penalty_coins = ?,
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = ?";

        return $this->db->query($sql, [
            $data['title'],
            $data['description'],
            $data['category'],
            $data['difficulty_level'] ?? 1,
            $data['prerequisite_blueprint_id'] ?? null,
            $data['estimated_completion_time'] ?? 10,
            $data['full_svg_content'],
            json_encode($data['layer_definitions'] ?? []),
            $data['preview_image'] ?? null,
            json_encode($data['learning_objectives'] ?? []),
            json_encode($data['key_terms'] ?? []),
            json_encode($data['syllabus_topic_ids'] ?? []),
            $data['total_sections'] ?? 5,
            $data['base_reward_coins'] ?? 50,
            $data['hint_penalty_coins'] ?? 5,
            $id
        ]);
    }
    /**
     * Get user progress for a blueprint (Proxy to BlueprintReveal)
     */
    public function getUserProgress($userId, $blueprintId)
    {
        $revealModel = new \App\Models\BlueprintReveal();
        $progress = $revealModel->getBlueprintProgress($userId, $blueprintId);

        if ($progress) {
            $sections = json_decode($progress['revealed_sections'] ?? '[]', true) ?: [];
            return [
                'sections_revealed' => count($sections),
                'total_sections' => $progress['total_sections'] ?? 5,
                'revealed_percentage' => (int)($progress['revealed_percentage'] ?? 0)
            ];
        }

        return [
            'sections_revealed' => 0,
            'total_sections' => 5, // Default fallback
            'revealed_percentage' => 0
        ];
    }

    /**
     * Update user progress (Proxy to BlueprintReveal)
     * Robust implementation: ensures we don't accidentally decrease progress.
     */
    public function updateUserProgress($userId, $blueprintId, $sectionsCount)
    {
        $revealModel = new \App\Models\BlueprintReveal();

        // Get current progress to prevent regression
        $currentProgress = $this->getUserProgress($userId, $blueprintId);
        if ($sectionsCount <= $currentProgress['sections_revealed']) {
            return true; // Consider it success if we aren't moving backwards
        }

        // Convert count back to simple array representation for compatibility
        $sections = [];
        for ($i = 0; $i < $sectionsCount; $i++) {
            $sections[] = ['section_id' => $i, 'revealed' => true];
        }

        return $revealModel->updateProgress($userId, $blueprintId, $sections);
    }

    /**
     * Check if user meets prerequisites for a blueprint
     */
    public function checkPrerequisites($userId, $blueprintId)
    {
        $blueprint = $this->getBlueprintById($blueprintId);
        if (!$blueprint || empty($blueprint['prerequisite_blueprint_id'])) {
            return true;
        }

        $revealModel = new \App\Models\BlueprintReveal();
        return $revealModel->hasPrerequisites($userId, (array)$blueprint['prerequisite_blueprint_id']);
    }
}
