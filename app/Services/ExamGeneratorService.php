<?php

namespace App\Services;

use App\Core\Database;
use Exception;

/**
 * Exam Generator Service
 * 
 * Generates exams based on blueprints:
 * - Reads blueprint rules
 * - Queries questions from syllabus nodes
 * - Applies difficulty distribution
 * - Handles wildcard injection (out-of-syllabus questions)
 * - Shuffles and assembles final exam
 */
class ExamGeneratorService
{
    private $db;
    private $blueprintService;
    private $syllabusService;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->blueprintService = new ExamBlueprintService();
        $this->syllabusService = new SyllabusService();
    }

    /**
     * Generate exam from blueprint
     * 
     * @param int $blueprintId
     * @param array $options ['shuffle' => true, 'include_wildcard' => true]
     * @return array Generated exam data
     */
    public function generateFromBlueprint($blueprintId, $options = [])
    {
        $blueprint = $this->blueprintService->getBlueprintWithRules($blueprintId);
        
        // Validate blueprint first
        $validation = $this->blueprintService->validateBlueprint($blueprintId);
        if (!$validation['valid']) {
            throw new Exception("Invalid blueprint: " . implode(', ', $validation['errors']));
        }

        $shuffle = $options['shuffle'] ?? true;
        $includeWildcard = $options['include_wildcard'] ?? true;

        $selectedQuestions = [];
        $wildcardCount = 0;

        // Calculate wildcard questions
        if ($includeWildcard && $blueprint['wildcard_percentage'] > 0) {
            $wildcardCount = ceil($blueprint['total_questions'] * ($blueprint['wildcard_percentage'] / 100));
        }

        $regularQuestionCount = $blueprint['total_questions'] - $wildcardCount;

        // Process each rule
        foreach ($blueprint['rules'] as $rule) {
            $questions = $this->getQuestionsForRule($rule, $blueprint['level']);
            $selectedQuestions = array_merge($selectedQuestions, $questions);
        }

        // Add wildcard questions if enabled
        if ($wildcardCount > 0) {
            $wildcardQuestions = $this->getWildcardQuestions($wildcardCount, $blueprint['level'], $selectedQuestions);
            $selectedQuestions = array_merge($selectedQuestions, $wildcardQuestions);
        }

        // Shuffle if requested
        if ($shuffle) {
            shuffle($selectedQuestions);
        }

        return [
            'blueprint_id' => $blueprintId,
            'blueprint_title' => $blueprint['title'],
            'total_questions' => count($selectedQuestions),
            'regular_questions' => $regularQuestionCount,
            'wildcard_questions' => $wildcardCount,
            'questions' => $selectedQuestions,
            'metadata' => [
                'duration_minutes' => $blueprint['duration_minutes'],
                'total_marks' => $blueprint['total_marks'],
                'negative_marking_rate' => $blueprint['negative_marking_rate'],
                'level' => $blueprint['level']
            ]
        ];
    }

    /**
     * Generate exam DIRECTLY from Syllabus (The "Heart of PSC" way)
     * 
     * @param string $level e.g., 'Level 5'
     * @param array $options
     * @return array Generated exam data
     */
    public function generateFromSyllabus($level, $options = [])
    {
        // 1. Fetch all syllabus nodes for this level with weight > 0
        $sql = "SELECT * FROM syllabus_nodes WHERE level = :level AND is_active = 1 AND questions_weight > 0 ORDER BY `order` ASC";
        $stmt = $this->db->getPdo()->prepare($sql);
        $stmt->execute(['level' => $level]);
        $nodes = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if (empty($nodes)) {
            throw new Exception("No weighted syllabus nodes found for level: $level");
        }

        $selectedQuestions = [];
        $totalTarget = 0;
        $generationLogs = [];

        // 2. Process each weighted node as a rule
        foreach ($nodes as $node) {
            $targetCount = $node['questions_weight'];
            $totalTarget += $targetCount;
            $nodeId = $node['id'];
            $nodeTitle = $node['title'];

            // Get questions using our refined query (handles linked_cat/sub/top)
            // Pass node constraints for type and difficulty filtering
            $qs = $this->queryQuestions(
                [$nodeId], 
                $level, 
                $node['difficulty_constraint'] ?? null, 
                $targetCount,
                $node['question_type'] ?? null
            );

            // Fallback Logic: Relax constraints if we didn't find enough questions
            if (count($qs) < $targetCount) {
                $found = count($qs);
                $needed = $targetCount - $found;
                $generationLogs[] = "Node [$nodeTitle]: Found $found/$targetCount with strict constraints. Relaxing filters.";
                
                // Fallback 1: Relax Difficulty
                $qs2 = $this->queryQuestions([$nodeId], $level, 'any', $needed, $node['question_type'] ?? null);
                // Ensure no duplicates
                $ids = array_column($qs, 'id');
                foreach($qs2 as $q) {
                    if(!in_array($q['id'], $ids)) {
                        $qs[] = $q;
                        $ids[] = $q['id'];
                    }
                }

                // Fallback 2: Relax Type if still not enough
                if (count($qs) < $targetCount) {
                    $needed = $targetCount - count($qs);
                    $qs3 = $this->queryQuestions([$nodeId], $level, 'any', $needed, 'any');
                    foreach($qs3 as $q) {
                        if(!in_array($q['id'], $ids)) {
                            $qs[] = $q;
                            $ids[] = $q['id'];
                        }
                    }
                }
            } else {
                $generationLogs[] = "Node [$nodeTitle]: Successfully found $targetCount questions.";
            }

            $selectedQuestions = array_merge($selectedQuestions, $qs);
        }

        // Log results (Optional: can be saved to DB or log file)
        // file_put_contents(STDOUT, implode("\n", $generationLogs));

        // Shuffle if requested
        if ($options['shuffle'] ?? true) {
            shuffle($selectedQuestions);
        }

        // 3. Fetch Level-Wide Settings for Negative Marking and Exam Constraints
        $settings = $this->db->findOne('syllabus_settings', ['level' => $level]);
        
        return [
            'blueprint_id' => null,
            'blueprint_title' => "Auto-Syllabus Exam ($level)",
            'total_questions' => count($selectedQuestions),
            'regular_questions' => count($selectedQuestions),
            'wildcard_questions' => 0,
            'questions' => $selectedQuestions,
            'metadata' => [
                'duration_minutes' => $settings['total_time'] ?? ($options['duration'] ?? 45),
                'total_marks' => $settings['full_marks'] ?? (count($selectedQuestions) * 2), 
                'negative_marking_rate' => $settings['negative_rate'] ?? ($options['negative_rate'] ?? 20.00),
                'negative_marking_unit' => 'percent',
                'negative_marking_basis' => 'per-q',
                'level' => $level
            ]
        ];
    }

    /**
     * Get questions for a specific rule
     */
    private function getQuestionsForRule($rule, $level)
    {
        $nodeId = $rule['syllabus_node_id'];
        $required = $rule['questions_required'];
        $difficultyDist = $rule['difficulty_distribution'] ?? null;

        // Get all child nodes (if this is a section, get all units under it)
        $children = $this->syllabusService->getAllChildren($nodeId);
        $nodeIds = array_merge([$nodeId], array_column($children, 'id'));

        $questions = [];

        if ($difficultyDist && is_array($difficultyDist)) {
            // Apply difficulty distribution
            foreach ($difficultyDist as $difficulty => $count) {
                $difficultyLevel = $this->mapDifficultyToLevel($difficulty);
                $qs = $this->queryQuestions($nodeIds, $level, $difficultyLevel, $count);
                $questions = array_merge($questions, $qs);
            }
        } else {
            // No difficulty distribution, just get random questions
            $questions = $this->queryQuestions($nodeIds, $level, null, $required);
        }

        return $questions;
    }

    /**
     * Query questions from database
     * 
     * @param array $nodeIds Syllabus node IDs
     * @param string $level Level filter
     * @param mixed $difficultyLevel Difficulty constraint (easy, medium, hard, mixed, any)
     * @param int $limit Number of questions to fetch
     * @param string $questionType Question type constraint (mcq_single, true_false, multi_select, subjective, any)
     */
    private function queryQuestions($nodeIds, $level, $difficultyLevel = null, $limit = 10, $questionType = null)
    {
        $nodeIdsStr = implode(',', array_map('intval', $nodeIds));

        // Refined SQL to use the "Heart of PSC" linkages
        $sql = "
            SELECT DISTINCT q.*, qsm.difficulty_in_stream, qsm.stream
            FROM quiz_questions q
            LEFT JOIN question_stream_map qsm ON q.id = qsm.question_id
            WHERE q.is_active = 1 AND q.status = 'approved'
            AND (
                -- 1. Direct Topic Linkage
                q.topic_id IN (
                    SELECT linked_topic_id FROM syllabus_nodes WHERE id IN ($nodeIdsStr) AND linked_topic_id IS NOT NULL
                )
                -- 2. Direct Subject Linkage (pulls all topics in that subject)
                OR q.topic_id IN (
                    SELECT id FROM quiz_topics WHERE subject_id IN (
                        SELECT linked_subject_id FROM syllabus_nodes WHERE id IN ($nodeIdsStr) AND linked_subject_id IS NOT NULL
                    )
                )
                -- 3. Direct Category Linkage (pulls all subjects and topics in that category)
                OR q.topic_id IN (
                    SELECT qt.id FROM quiz_topics qt 
                    JOIN quiz_subjects qs ON qt.subject_id = qs.id
                    WHERE qs.category_id IN (
                        SELECT linked_category_id FROM syllabus_nodes WHERE id IN ($nodeIdsStr) AND linked_category_id IS NOT NULL
                    )
                )
                -- 4. Direct QSM Linkage (Level Mapping)
                OR qsm.syllabus_node_id IN ($nodeIdsStr)
                -- 5. RELATIONAL Persistence Linkage (NEW - FAST)
                OR q.sub_category_id IN ($nodeIdsStr)
                OR q.category_id IN ($nodeIdsStr)
            )
        ";

        $params = [];

        if ($level) {
            $sql .= " AND (qsm.stream LIKE :level OR qsm.stream IS NULL)";
            $params['level'] = "%$level%";
        }

        // Difficulty filtering with support for 'any' and 'mixed'
        if ($difficultyLevel && $difficultyLevel !== 'any' && $difficultyLevel !== 'mixed') {
            $difficultyNumeric = $this->mapDifficultyToLevel($difficultyLevel);
            $sql .= " AND (q.difficulty_level = :difficulty OR qsm.difficulty_in_stream = :difficulty)";
            $params['difficulty'] = $difficultyNumeric;
        }
        
        // Question Type filtering
        if ($questionType && $questionType !== 'any') {
            $sql .= " AND q.type = :question_type";
            $params['question_type'] = $questionType;
        }

        $sql .= " ORDER BY RAND() LIMIT :limit";

        $stmt = $this->db->getPdo()->prepare($sql);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        $stmt->bindValue(':limit', (int)$limit, \PDO::PARAM_INT);
        
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Get wildcard (out-of-syllabus) questions
     */
    private function getWildcardQuestions($count, $level, $excludeQuestions = [])
    {
        $excludeIds = array_column($excludeQuestions, 'id');
        $excludeIdsStr = !empty($excludeIds) ? implode(',', array_map('intval', $excludeIds)) : '0';

        $sql = "
            SELECT q.*, qsm.difficulty_in_stream, qsm.stream
            FROM quiz_questions q
            LEFT JOIN question_stream_map qsm ON q.id = qsm.question_id
            WHERE q.is_active = 1 AND q.status = 'approved'
            AND q.id NOT IN ($excludeIdsStr)
            AND qsm.is_practical = 1
        ";

        $params = [];

        if ($level) {
            $sql .= " AND (qsm.stream LIKE :level OR qsm.stream IS NULL)";
            $params['level'] = "%$level%";
        }

        $sql .= " ORDER BY RAND() LIMIT :limit";

        $stmt = $this->db->getPdo()->prepare($sql);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        $stmt->bindValue(':limit', (int)$count, \PDO::PARAM_INT);
        
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Save generated exam to quiz_exams and quiz_exam_questions
     */
    public function saveGeneratedExam($generatedExam, $examData = [])
    {
        $pdo = $this->db->getPdo();
        $pdo->beginTransaction();

        try {
            // Create exam record
            $examInsertData = array_merge([
                'title' => $generatedExam['blueprint_title'] . ' - ' . date('Y-m-d H:i'),
                'slug' => $this->slugify($generatedExam['blueprint_title']) . '-' . time(),
                'type' => 'mock_test',
                'mode' => 'exam',
                'duration_minutes' => $generatedExam['metadata']['duration_minutes'],
                'total_marks' => $generatedExam['metadata']['total_marks'],
                'negative_marking_rate' => $generatedExam['metadata']['negative_marking_rate'],
                'negative_marking_unit' => $generatedExam['metadata']['negative_marking_unit'] ?? 'percent',
                'negative_marking_basis' => $generatedExam['metadata']['negative_marking_basis'] ?? 'per-q',
                'status' => 'published'
            ], $examData);

            $this->db->insert('quiz_exams', $examInsertData);
            $examId = $this->db->lastInsertId();

            // Insert questions
            $order = 1;
            foreach ($generatedExam['questions'] as $question) {
                $this->db->insert('quiz_exam_questions', [
                    'exam_id' => $examId,
                    'question_id' => $question['id'],
                    'order' => $order++
                ]);
            }

            $pdo->commit();
            return $examId;

        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Map difficulty string to numeric level
     */
    private function mapDifficultyToLevel($difficulty)
    {
        $map = [
            'easy' => 1,
            'easy_mid' => 2,
            'medium' => 3,
            'hard' => 4,
            'expert' => 5
        ];

        return $map[strtolower($difficulty)] ?? 3;
    }

    /**
     * Get exam statistics
     */
    public function getExamStatistics($examId)
    {
        $sql = "
            SELECT 
                COUNT(*) as total_questions,
                AVG(q.difficulty_level) as avg_difficulty,
                SUM(CASE WHEN q.type = 'mcq_single' THEN 1 ELSE 0 END) as mcq_count,
                SUM(CASE WHEN q.type = 'numerical' THEN 1 ELSE 0 END) as numerical_count,
                SUM(CASE WHEN q.type = 'true_false' THEN 1 ELSE 0 END) as tf_count
            FROM quiz_exam_questions eq
            JOIN quiz_questions q ON eq.question_id = q.id
            WHERE eq.exam_id = :exam_id
        ";

        $stmt = $this->db->getPdo()->prepare($sql);
        $stmt->execute(['exam_id' => $examId]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
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
