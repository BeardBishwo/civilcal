<?php

namespace App\Controllers\Admin\Quiz;

use App\Core\Controller;
use App\Core\Database;
use App\Services\SyllabusService;

class SyllabusController extends Controller
{
    protected $db;
    protected $syllabusService;

    public function __construct()
    {
        parent::__construct();
        $this->requireAuth();
        $this->db = Database::getInstance();
        $this->syllabusService = new SyllabusService();
    }

    public function index()
    {
        // Positioning Dashboard (High-level View)
        $sql = "SELECT level, COUNT(*) as total_nodes, 
                SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active_nodes,
                SUM(questions_weight) as total_weight, MAX(updated_at) as last_modified
                FROM syllabus_nodes GROUP BY level ORDER BY level ASC";
        $positions = $this->db->query($sql)->fetchAll();
        
        $stats = [
            'total_positions' => count($positions),
            'active_syllabuses' => $this->db->query("SELECT COUNT(DISTINCT level) as c FROM syllabus_nodes WHERE is_active = 1")->fetch()['c'] ?? 0,
            'total_questions' => $this->db->query("SELECT SUM(questions_weight) as s FROM syllabus_nodes")->fetch()['s'] ?? 0,
            'node_count' => $this->db->query("SELECT COUNT(*) as c FROM syllabus_nodes")->fetch()['c'] ?? 0
        ];

        return $this->view('admin/quiz/syllabus/index', [
            'page_title' => 'Syllabus Dashboard',
            'positions' => $positions,
            'stats' => $stats
        ]);
    }

    public function manage($level = null)
    {
        // Prioritize query parameter for better compatibility with special characters
        $level = $_GET['level'] ?? $level;
        
        if ($level) {
            $level = urldecode($level);
        }

        if (!$level) {
            $_SESSION['error'] = "Level not specified";
            header('Location: ' . app_base_url('admin/quiz/syllabus'));
            exit;
        }

        $isUnassigned = (str_contains($level, 'Unassigned'));
        
        // Fetch nodes with joined names for display
        $sql = "
            SELECT sn.*, 
                   qc.name as category_name, 
                   qt.name as topic_name
            FROM syllabus_nodes sn
            LEFT JOIN quiz_categories qc ON sn.linked_category_id = qc.id
            LEFT JOIN quiz_topics qt ON sn.linked_topic_id = qt.id
            WHERE " . ($isUnassigned ? "(sn.level IS NULL OR sn.level = '' OR sn.level LIKE '%Unassigned%')" : "sn.level = :level") . "
            ORDER BY sn.order ASC, sn.id ASC
        ";
        
        $params = $isUnassigned ? [] : ['level' => $level];
        $nodes = $this->db->query($sql, $params)->fetchAll();

        // Recursively build tree
        $nodesTree = $this->buildSyllabusTree($nodes);

        // Fetch dropdown data
        $categories = $this->db->find('quiz_categories', ['is_active' => 1], 'name ASC');
        $topics = $this->db->find('quiz_topics', ['is_active' => 1], 'name ASC');

        // Fetch global settings for this level
        $settings = $this->db->findOne('syllabus_settings', ['level' => $level]);
        if (!$settings) {
            $settings = [
                'level' => $level,
                'total_time' => 0,
                'full_marks' => 0,
                'pass_marks' => 0,
                'negative_rate' => 0.00
            ];
        }

        return $this->view('admin/quiz/syllabus/manage', [
            'page_title' => "Editing: $level",
            'nodes' => $nodes,
            'nodesTree' => $nodesTree,
            'level' => $level,
            'settings' => $settings,
            'categories' => $categories,
            'topics' => $topics
        ]);
    }

    private function buildSyllabusTree($nodes, $parentId = null, $depth = 0)
    {
        $branch = [];
        foreach ($nodes as $node) {
            // Loose comparison (==) handles string '0' vs int 0 vs null
            if ($node['parent_id'] == $parentId) {
                $node['depth'] = $depth;
                $children = $this->buildSyllabusTree($nodes, $node['id'], $depth + 1);
                $node['children'] = $children;
                $branch[] = $node;
            }
        }
        return $branch;
    }

    // --- FIX: Improved Store Method ---
    public function store()
    {
        // Sanitize Input
        $parentId = !empty($_POST['parent_id']) ? $_POST['parent_id'] : null;
        
        $data = [
            'parent_id' => $parentId,
            'level' => $_POST['level'] ?? 'Unassigned',
            'title' => trim($_POST['title'] ?? 'New Item'),
            'type' => $_POST['type'] ?? 'unit',
            'linked_category_id' => !empty($_POST['linked_category_id']) ? $_POST['linked_category_id'] : null,
            'linked_topic_id' => !empty($_POST['linked_topic_id']) ? $_POST['linked_topic_id'] : null,
            'questions_weight' => (int)($_POST['questions_weight'] ?? 0),
            'time_minutes' => (int)($_POST['time_minutes'] ?? 0),
            'marks_per_question' => (float)($_POST['marks_per_question'] ?? 0),
            'is_active' => isset($_POST['is_active']) ? 1 : 0
        ];

        // Determine Order (Append to end)
        $lastOrder = $this->db->query("SELECT MAX(`order`) as m FROM syllabus_nodes WHERE level = ?", [$data['level']])->fetch()['m'] ?? 0;
        $data['order'] = $lastOrder + 1;

        if ($this->db->create('syllabus_nodes', $data)) {
            echo json_encode(['status' => 'success', 'message' => 'Node created successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Database error']);
        }
    }

    // --- FIX: Improved Update Method ---
    public function update($id)
    {
        $data = [
            'title' => trim($_POST['title'] ?? ''),
            'type' => $_POST['type'] ?? 'unit',
            'linked_category_id' => !empty($_POST['linked_category_id']) ? $_POST['linked_category_id'] : null,
            'linked_topic_id' => !empty($_POST['linked_topic_id']) ? $_POST['linked_topic_id'] : null,
            'questions_weight' => (int)($_POST['questions_weight'] ?? 0),
            'time_minutes' => (int)($_POST['time_minutes'] ?? 0),
            'marks_per_question' => (float)($_POST['marks_per_question'] ?? 0),
            'is_active' => isset($_POST['is_active']) ? 1 : 0
        ];

        if (empty($data['title'])) {
            echo json_encode(['status' => 'error', 'message' => 'Title is required']);
            return;
        }

        if ($this->db->update('syllabus_nodes', $data, "id = :id", ['id' => $id])) {
            echo json_encode(['status' => 'success', 'message' => 'Updated successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Update failed']);
        }
    }

    public function delete($id)
    {
        // Basic delete (in production, you might want to check for children first)
        if ($this->db->delete('syllabus_nodes', "id = :id", ['id' => $id])) {
            // Also delete children to prevent orphans
            $this->db->delete('syllabus_nodes', "parent_id = :id", ['id' => $id]);
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error']);
        }
    }
    
    public function toggleStatus($id)
    {
        $node = $this->db->findOne('syllabus_nodes', ['id' => $id]);
        if (!$node) return;
        $newStatus = $node['is_active'] ? 0 : 1;
        $this->db->update('syllabus_nodes', ['is_active' => $newStatus], "id = :id", ['id' => $id]);
    }

    /**
     * AJAX: Delete all nodes for a specific level
     */
    public function deleteLevel()
    {
        $level = $_POST['level'] ?? null;
        if (!$level) {
            echo json_encode(['status' => 'error', 'message' => 'Level missing']);
            return;
        }

        $isUnassigned = (str_contains($level, 'Unassigned'));
        
        if ($isUnassigned) {
            $this->db->query("DELETE FROM syllabus_nodes WHERE level IS NULL OR level = '' OR level LIKE '%Unassigned%'");
        } else {
            $this->db->delete('syllabus_nodes', "level = :level", ['level' => $level]);
        }
        
        // Also remove settings
        $this->db->delete('syllabus_settings', "level = :level", ['level' => $level]);

        echo json_encode(['status' => 'success', 'message' => 'Syllabus structure cleared']);
    }

    /**
     * AJAX: Save global syllabus settings
     */
    public function saveSettings()
    {
        $level = $_POST['level'] ?? null;
        if (!$level) {
            echo json_encode(['status' => 'error', 'message' => 'Level missing']);
            return;
        }

        $data = [
            'level' => $level,
            'total_time' => (int)($_POST['total_time'] ?? 0),
            'full_marks' => (int)($_POST['full_marks'] ?? 0),
            'pass_marks' => (int)($_POST['pass_marks'] ?? 0),
            'negative_rate' => (float)($_POST['negative_rate'] ?? 0.00)
        ];

        $existing = $this->db->findOne('syllabus_settings', ['level' => $level]);
        if ($existing) {
            $this->db->update('syllabus_settings', $data, "level = :level", ['level' => $level]);
        } else {
            $this->db->create('syllabus_settings', $data);
        }

        echo json_encode(['status' => 'success', 'message' => 'Settings updated']);
    }

    /**
     * AJAX: Duplicate a node
     */
    public function duplicate($id)
    {
        $node = $this->db->findOne('syllabus_nodes', ['id' => $id]);
        if (!$node) {
            echo json_encode(['status' => 'error', 'message' => 'Node not found']);
            return;
        }

        $newNode = $node;
        unset($newNode['id']);
        unset($newNode['created_at']);
        unset($newNode['updated_at']);
        $newNode['title'] .= ' (Copy)';
        $newNode['order'] = $node['order'] + 1;

        if ($this->db->create('syllabus_nodes', $newNode)) {
            echo json_encode(['status' => 'success', 'message' => 'Node duplicated']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Duplication failed']);
        }
    }

    /**
     * AJAX: Move node up/down
     */
    public function move($id, $direction)
    {
        $node = $this->db->findOne('syllabus_nodes', ['id' => $id]);
        if (!$node) return;

        $currentOrder = $node['order'];
        $level = $node['level'];

        if ($direction === 'up') {
            $neighbor = $this->db->query("SELECT * FROM syllabus_nodes WHERE level = ? AND `order` < ? ORDER BY `order` DESC LIMIT 1", [$level, $currentOrder])->fetch();
        } else {
            $neighbor = $this->db->query("SELECT * FROM syllabus_nodes WHERE level = ? AND `order` > ? ORDER BY `order` ASC LIMIT 1", [$level, $currentOrder])->fetch();
        }

        if ($neighbor) {
            $this->db->update('syllabus_nodes', ['order' => $neighbor['order']], "id = :id", ['id' => $node['id']]);
            $this->db->update('syllabus_nodes', ['order' => $currentOrder], "id = :id", ['id' => $neighbor['id']]);
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Boundary reached']);
        }
    }

    /**
     * AJAX: Generate Exam from Syllabus Rules
     */
    public function generateExam()
    {
        try {
            $level = $_POST['level'] ?? 'Level 5'; // Fallback or dynamic
            $generator = new \App\Services\ExamGeneratorService();
            
            $options = [
                'shuffle' => true,
                'duration' => (int)($_POST['duration'] ?? 45),
                'negative_rate' => (float)($_POST['negative_rate'] ?? 20.00)
            ];

            $generatedExam = $generator->generateFromSyllabus($level, $options);
            
            // Save it automatically
            $examId = $generator->saveGeneratedExam($generatedExam, [
                'title' => "Mock Exam: $level (" . date('M d, Y') . ")",
                'type' => 'mock_test'
            ]);

            echo json_encode([
                'status' => 'success',
                'message' => "Exam generated successfully with " . count($generatedExam['questions']) . " questions.",
                'exam_id' => $examId,
                'redirect' => app_base_url('admin/quiz/exams/edit/' . $examId)
            ]);

        } catch (\Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
