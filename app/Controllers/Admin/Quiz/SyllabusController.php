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
        if (!$level) $level = $_GET['level'] ?? null;
        if ($level) $level = urldecode($level);

        if (!$level) {
            $_SESSION['error'] = "Level not specified";
            header('Location: ' . app_base_url('admin/quiz/syllabus'));
            exit;
        }

        $isUnassigned = ($level === 'Unassigned / Draft');
        
        // Fetch nodes with joined names for display
        $sql = "
            SELECT sn.*, 
                   qc.name as category_name, 
                   qt.name as topic_name
            FROM syllabus_nodes sn
            LEFT JOIN quiz_categories qc ON sn.linked_category_id = qc.id
            LEFT JOIN quiz_topics qt ON sn.linked_topic_id = qt.id
            WHERE " . ($isUnassigned ? "(sn.level IS NULL OR sn.level = '' OR sn.level = 'Unassigned / Draft')" : "sn.level = :level") . "
            ORDER BY sn.order ASC, sn.id ASC
        ";
        
        $params = $isUnassigned ? [] : ['level' => $level];
        $nodes = $this->db->query($sql, $params)->fetchAll();

        // Recursively build tree
        $nodesTree = $this->buildSyllabusTree($nodes);

        // Fetch dropdown data
        $categories = $this->db->find('quiz_categories', ['is_active' => 1], 'name ASC');
        $topics = $this->db->find('quiz_topics', ['is_active' => 1], 'name ASC');

        return $this->view('admin/quiz/syllabus/manage', [
            'page_title' => "Editing: $level",
            'nodes' => $nodes,
            'nodesTree' => $nodesTree,
            'level' => $level,
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

    /**
     * NEW: Bulk Save for Pro Grid UI
     */
    public function bulkSave()
    {
        $input = json_decode(file_get_contents('php://input'), true);
        $level = $input['level'] ?? null;
        $nodes = $input['nodes'] ?? [];
        $settings = $input['settings'] ?? [];

        if (!$level || empty($nodes)) {
            echo json_encode(['status' => 'error', 'message' => 'No data provided']);
            return;
        }

        $this->db->beginTransaction();
        try {
            // 1. Clear existing for this level (Simplest way to handle re-ordering/deletes)
            // Note: In production, consider soft-deletes or smarter diffing to preserve IDs if needed
            $this->db->delete('syllabus_nodes', "level = :level", ['level' => $level]);

            // 2. Re-insert all nodes
            // We need to map the flat JS array back to parent_id logic based on depth
            // This is complex because JS grid relies on order + depth, DB needs parent_id.
            
            $parentStack = [null]; // Depth 0 parent is null
            $previousDepth = -1;

            foreach ($nodes as $index => $node) {
                $currentDepth = (int)$node['depth'];
                
                // Adjust stack based on depth change
                if ($currentDepth > $previousDepth) {
                    // Going deeper: Parent is previous node (which we haven't saved ID for yet? We need to save IDs)
                    // This logic requires saving parent first. 
                    // Let's assume nodes come in linear order.
                } 
                // Actually, simplest way: The UI sends depth. We can reconstruct parent_id on read or write.
                // The Grid UI doesn't explicitly track parent_id visually, it implies it by depth order.
                
                // Simplified Logic: 
                // If depth = 0, parent = null.
                // If depth > prevDepth, parent = prevNodeId.
                // If depth < prevDepth, pop from stack.
                
                // Better approach: Let's trust the JS structure is ordered.
                // We need to generate NEW IDs or map old ones.
                // For simplicity in this demo, we'll just save them as flat list with depth, 
                // and use a helper to reconstruct parent_ids if the DB schema strictly requires it.
                // Assuming DB schema has `parent_id`.
                
                // Stack to keep track of the last ID at each depth
                // $stack[0] = id_of_last_depth_0_item
                // $stack[1] = id_of_last_depth_1_item
                
                $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $node['title']), '-'));
                $data = [
                    'level' => $level,
                    'title' => $node['title'],
                    'slug' => $slug,
                    'type' => $node['type'],
                    'questions_weight' => $node['weight'],
                    'time_minutes' => $node['time'] ?? 0,
                    'question_count' => $node['qCount'] ?? 0,
                    'order' => $index,
                ];
                
                // Insert and get ID
                $this->db->insert('syllabus_nodes', $data);
                $newId = $this->db->lastInsertId();
                
                // Logic to update parent_id for the *next* items? 
                // Or better: update THIS item's parent_id based on stack.
                $parentId = null;
                if ($currentDepth > 0 && isset($parentStack[$currentDepth - 1])) {
                    $parentId = $parentStack[$currentDepth - 1];
                    // Update this node with parent
                    $this->db->update('syllabus_nodes', ['parent_id' => $parentId], "id = :id", ['id' => $newId]);
                }
                
                // Update stack for children
                $parentStack[$currentDepth] = $newId;
                
                // Clear deeper stack if we went up
                // (e.g. if we are at depth 1, stack[2], stack[3] are invalid)
                for($i = $currentDepth + 1; $i < 10; $i++) unset($parentStack[$i]);
            }

            $this->db->commit();
            echo json_encode(['status' => 'success']);

        } catch (\Exception $e) {
            $this->db->rollBack();
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

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
            'is_active' => isset($_POST['is_active']) ? 1 : 0
        ];

        // Determine Order (Append to end)
        $lastOrder = $this->db->query("SELECT MAX(`order`) as m FROM syllabus_nodes WHERE level = ?", [$data['level']])->fetch()['m'] ?? 0;
        $data['order'] = $lastOrder + 1;

        if ($this->db->insert('syllabus_nodes', $data)) {
            echo json_encode(['status' => 'success', 'message' => 'Node created successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Database error']);
        }
    }

    public function update($id)
    {
        $data = [
            'title' => trim($_POST['title'] ?? ''),
            'type' => $_POST['type'] ?? 'unit',
            'linked_category_id' => !empty($_POST['linked_category_id']) ? $_POST['linked_category_id'] : null,
            'linked_topic_id' => !empty($_POST['linked_topic_id']) ? $_POST['linked_topic_id'] : null,
            'questions_weight' => (int)($_POST['questions_weight'] ?? 0),
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

    public function deleteLevel()
    {
        $level = $_POST['level'] ?? null;
        if (!$level) {
            echo json_encode(['status' => 'error', 'message' => 'Level not specified']);
            return;
        }

        try {
            if ($level === 'Unassigned / Draft' || empty($level)) {
                $this->db->delete('syllabus_nodes', "level IS NULL OR level = '' OR level = 'Unassigned / Draft'");
            } else {
                $this->db->delete('syllabus_nodes', "level = :level", ['level' => $level]);
            }
            echo json_encode(['status' => 'success', 'message' => "Syllabus for '$level' deleted successfully"]);
        } catch (\Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
    
    public function toggleStatus($id)
    {
        $node = $this->db->findOne('syllabus_nodes', ['id' => $id]);
        if (!$node) return;
        $newStatus = $node['is_active'] ? 0 : 1;
        $this->db->update('syllabus_nodes', ['is_active' => $newStatus], "id = :id", ['id' => $id]);
    }

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