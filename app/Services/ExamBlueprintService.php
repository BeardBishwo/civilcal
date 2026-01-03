<?php

namespace App\Services;

use App\Core\Database;
use Exception;

/**
 * Exam Blueprint Service
 * 
 * Manages exam blueprints (recipes) and their distribution rules
 */
class ExamBlueprintService
{
    private $db;
    private $syllabusService;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->syllabusService = new SyllabusService();
    }

    /**
     * Get all blueprints
     */
    public function getAllBlueprints($activeOnly = false)
    {
        $conditions = $activeOnly ? ['is_active' => 1] : [];
        return $this->db->find('exam_blueprints', $conditions, 'created_at DESC');
    }

    /**
     * Get blueprint with its rules
     */
    public function getBlueprintWithRules($blueprintId)
    {
        $blueprint = $this->db->findOne('exam_blueprints', ['id' => $blueprintId]);
        if (!$blueprint) {
            throw new Exception("Blueprint not found");
        }

        // Get rules with syllabus node details
        $sql = "
            SELECT br.*, sn.title as node_title, sn.type as node_type, sn.level as node_level
            FROM blueprint_rules br
            JOIN syllabus_nodes sn ON br.syllabus_node_id = sn.id
            WHERE br.blueprint_id = :blueprint_id
            ORDER BY br.`order` ASC
        ";

        $stmt = $this->db->getPdo()->prepare($sql);
        $stmt->execute(['blueprint_id' => $blueprintId]);
        $rules = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Decode JSON difficulty distribution
        foreach ($rules as &$rule) {
            if (!empty($rule['difficulty_distribution'])) {
                $rule['difficulty_distribution'] = json_decode($rule['difficulty_distribution'], true);
            }
        }

        $blueprint['rules'] = $rules;
        return $blueprint;
    }

    /**
     * Create new blueprint
     */
    public function createBlueprint($data)
    {
        // Auto-generate slug
        if (empty($data['slug'])) {
            $data['slug'] = $this->slugify($data['title']);
        }

        if ($this->db->insert('exam_blueprints', $data)) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    /**
     * Update blueprint
     */
    public function updateBlueprint($blueprintId, $data)
    {
        return $this->db->update('exam_blueprints', $data, "id = :id", ['id' => $blueprintId]);
    }

    /**
     * Delete blueprint (CASCADE will delete rules)
     */
    public function deleteBlueprint($blueprintId)
    {
        return $this->db->delete('exam_blueprints', "id = :id", ['id' => $blueprintId]);
    }

    /**
     * Add rule to blueprint
     */
    public function addRule($blueprintId, $ruleData)
    {
        // Validate syllabus node exists
        $node = $this->db->findOne('syllabus_nodes', ['id' => $ruleData['syllabus_node_id']]);
        if (!$node) {
            throw new Exception("Syllabus node not found");
        }

        // Validate blueprint exists
        $blueprint = $this->db->findOne('exam_blueprints', ['id' => $blueprintId]);
        if (!$blueprint) {
            throw new Exception("Blueprint not found");
        }

        // Encode difficulty distribution if provided
        if (isset($ruleData['difficulty_distribution']) && is_array($ruleData['difficulty_distribution'])) {
            $ruleData['difficulty_distribution'] = json_encode($ruleData['difficulty_distribution']);
        }

        // Get max order
        if (!isset($ruleData['order'])) {
            $sql = "SELECT MAX(`order`) FROM blueprint_rules WHERE blueprint_id = :blueprint_id";
            $stmt = $this->db->getPdo()->prepare($sql);
            $stmt->execute(['blueprint_id' => $blueprintId]);
            $maxOrder = $stmt->fetchColumn() ?: 0;
            $ruleData['order'] = $maxOrder + 1;
        }

        $ruleData['blueprint_id'] = $blueprintId;
        return $this->db->insert('blueprint_rules', $ruleData);
    }

    /**
     * Update rule
     */
    public function updateRule($ruleId, $ruleData)
    {
        // Encode difficulty distribution if provided
        if (isset($ruleData['difficulty_distribution']) && is_array($ruleData['difficulty_distribution'])) {
            $ruleData['difficulty_distribution'] = json_encode($ruleData['difficulty_distribution']);
        }

        return $this->db->update('blueprint_rules', $ruleData, "id = :id", ['id' => $ruleId]);
    }

    /**
     * Delete rule
     */
    public function deleteRule($ruleId)
    {
        return $this->db->delete('blueprint_rules', "id = :id", ['id' => $ruleId]);
    }

    /**
     * Validate blueprint (check if total questions match)
     */
    public function validateBlueprint($blueprintId)
    {
        $blueprint = $this->getBlueprintWithRules($blueprintId);
        
        $totalRequired = 0;
        foreach ($blueprint['rules'] as $rule) {
            $totalRequired += $rule['questions_required'];
        }

        $errors = [];

        if ($totalRequired != $blueprint['total_questions']) {
            $errors[] = "Total questions in rules ($totalRequired) doesn't match blueprint total ({$blueprint['total_questions']})";
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'total_required' => $totalRequired,
            'blueprint_total' => $blueprint['total_questions']
        ];
    }

    /**
     * Get blueprint summary (for preview)
     */
    public function getBlueprintSummary($blueprintId)
    {
        $blueprint = $this->getBlueprintWithRules($blueprintId);
        
        $summary = [
            'title' => $blueprint['title'],
            'level' => $blueprint['level'],
            'total_questions' => $blueprint['total_questions'],
            'duration_minutes' => $blueprint['duration_minutes'],
            'sections' => []
        ];

        foreach ($blueprint['rules'] as $rule) {
            $summary['sections'][] = [
                'node_title' => $rule['node_title'],
                'node_type' => $rule['node_type'],
                'questions' => $rule['questions_required'],
                'difficulty' => $rule['difficulty_distribution']
            ];
        }

        return $summary;
    }

    /**
     * Helper: Slugify text
     */
    private function slugify($text)
    {
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        $text = preg_replace('~[^-\w]+~', '', $text);
        $text = trim($text, '-');
        $text = preg_replace('~-+~', '-', $text);
        $text = strtolower($text);
        return empty($text) ? 'n-a' : $text;
    }
}
