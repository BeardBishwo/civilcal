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

        // Fetch level-wide settings
        $settingsRow = $this->db->findOne('syllabus_settings', ['level' => $level]);
        $settings = $settingsRow ? json_decode($settingsRow['settings'], true) : [];

        return $this->view('admin/quiz/syllabus/manage', [
            'page_title' => "Editing: $level",
            'nodes' => $nodes,
            'nodesTree' => $nodesTree,
            'level' => $level,
            'categories' => $categories,
            'topics' => $topics,
            'settings' => $settings
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
     * BULK SAVE: Handles the Grid UI payload
     * Transactional save that reconstructs the tree hierarchy from flat grid depth.
     */
    public function bulkSave()
    {
        $input = json_decode(file_get_contents('php://input'), true);
        $level = $input['level'] ?? null;
        $nodes = $input['nodes'] ?? [];
        $settings = $input['settings'] ?? [];

        if (!$level || empty($nodes)) {
            $this->jsonResponse(['status' => 'error', 'message' => 'No data provided']);
            return;
        }

        $this->db->beginTransaction();
        try {
            // 1. Clear existing structure for this level
            // We use DELETE/INSERT strategy to ensure clean ordering and hierarchy reconstruction
            $this->db->delete('syllabus_nodes', "level = :level", ['level' => $level]);

            // 2. Reconstruct Hierarchy & Save Nodes
            // The Grid provides a flat list with 'depth' (0,1,2,3).
            // We use a stack references to reconstruct parent_id relationships.
            
            $parentStack = [null]; // Stack: depth => parent_node_id
            
            foreach ($nodes as $index => $node) {
                $currentDepth = (int)$node['depth'];
                
                // Determine Parent ID
                // If depth is 0, parent is null (Stack[0-1] is undefined/null logic)
                // If depth is 2, parent is at Stack[1]
                $parentId = ($currentDepth > 0 && isset($parentStack[$currentDepth - 1])) 
                            ? $parentStack[$currentDepth - 1] 
                            : null;

                // Create Slug
                $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $node['title']), '-'));
                
                // Prepare Data
                $data = [
                    'level' => $level,
                    'title' => $node['title'],
                    'slug' => $slug,
                    'type' => $node['type'] ?? 'unit',
                    'parent_id' => $parentId,
                    'questions_weight' => $node['weight'] ?? 0,
                    'time_minutes' => $node['time'] ?? 0,
                    'question_count' => $node['qCount'] ?? 0,
                    'order' => $index,
                    'is_active' => 1
                ];
                
                // Insert
                $this->db->insert('syllabus_nodes', $data);
                $newId = $this->db->lastInsertId();
                
                // Update Stack: This node becomes the potential parent for the next depth (currentDepth + 1)
                $parentStack[$currentDepth] = $newId;
                
                // Clean stack for deeper levels to avoid stale references if we jump back up
                for($i = $currentDepth + 1; $i < 10; $i++) unset($parentStack[$i]);
            }

            // 3. Save Level Meta-Settings
            if (!empty($settings)) {
                // Map settings to actual table columns: total_time, full_marks, pass_marks, negative_rate
                $settingsData = [
                    'level' => $level,
                    'total_time' => $settings['total_time'] ?? 0,
                    'full_marks' => $settings['full_marks'] ?? 0,
                    'pass_marks' => $settings['pass_marks'] ?? 0,
                    'negative_rate' => $settings['negative_rate'] ?? 0.00
                ];

                $existing = $this->db->findOne('syllabus_settings', ['level' => $level]);
                if ($existing) {
                    // Update existing settings
                    // Remove 'level' from data for update (it's in WHERE clause)
                    $updateData = $settingsData;
                    unset($updateData['level']);
                    
                    $this->db->update('syllabus_settings', $updateData, "level = :level", ['level' => $level]);
                } else {
                    // Insert new settings
                    $this->db->insert('syllabus_settings', $settingsData);
                }
            }

            $this->db->commit();
            $this->jsonResponse(['status' => 'success']);

        } catch (\Exception $e) {
            $this->db->rollBack();
            $this->jsonResponse(['status' => 'error', 'message' => $e->getMessage()]);
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
        // Safety Check: Prevent deletion of Hierarchy Nodes via Syllabus Controller
        $node = $this->db->findOne('syllabus_nodes', ['id' => $id]);
        if ($node && in_array($node['type'], ['course', 'education_level', 'category', 'sub_category'])) {
             echo json_encode(['status' => 'error', 'message' => 'Protected Item: Cannot delete hierarchy nodes from Syllabus Manager.']);
             return;
        }

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
            // Protect shared hierarchy types from being deleted when clearing a level
            $protectedTypes = "type NOT IN ('course', 'education_level', 'category', 'sub_category')";

            if ($level === 'Unassigned / Draft' || empty($level)) {
                // Delete only non-protected nodes that are unassigned
                $this->db->delete('syllabus_nodes', "(level IS NULL OR level = '' OR level = 'Unassigned / Draft') AND $protectedTypes");
            } else {
                // 1. Delete Settings for this level (This removes it from the list if it's based on settings)
                $this->db->delete('syllabus_settings', "level = :target_level", ['target_level' => $level]);

                // 2. Unassign Protected Hierarchy Nodes (Don't delete them, just detach from this syllabus)
                // Fix: Use 'target_level' for WHERE clause to avoid collision with 'level' => null in SET data
                $this->db->update('syllabus_nodes', ['level' => null], "level = :target_level AND NOT ($protectedTypes)", ['target_level' => $level]);

                // 3. Delete Everything Else (Units, Chapters, Questions)
                $this->db->delete('syllabus_nodes', "level = :target_level AND $protectedTypes", ['target_level' => $level]);
            }
            echo json_encode(['status' => 'success', 'message' => "Syllabus for '$level' deleted successfully"]);
        } catch (\Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function duplicateLevel()
    {
        $oldLevel = $_POST['level'] ?? null;
        $newLevel = $_POST['newLevel'] ?? null;

        if (!$oldLevel || !$newLevel) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid parameters']);
            return;
        }

        try {
            $this->db->beginTransaction();
            
            // Fetch all nodes for the old level
            $nodes = $this->db->find('syllabus_nodes', ['level' => $oldLevel], '`order` ASC');
            
            $idMapping = []; // old_id => new_id

            // First pass: Insert all nodes without parent_id to get new IDs
            foreach ($nodes as $node) {
                $oldId = $node['id'];
                $data = $node;
                unset($data['id']);
                $data['level'] = $newLevel;
                $data['parent_id'] = null; // Temporary
                
                $this->db->insert('syllabus_nodes', $data);
                $idMapping[$oldId] = $this->db->lastInsertId();
            }

            // Second pass: Update parent_id based on mapping
            foreach ($nodes as $node) {
                if ($node['parent_id'] && isset($idMapping[$node['parent_id']])) {
                    $newId = $idMapping[$node['id']];
                    $newParentId = $idMapping[$node['parent_id']];
                    $this->db->update('syllabus_nodes', ['parent_id' => $newParentId], "id = :id", ['id' => $newId]);
                }
            }

            $this->db->commit();
            echo json_encode(['status' => 'success', 'message' => "Syllabus duplicated to '$newLevel'"]);
        } catch (\Exception $e) {
            $this->db->rollBack();
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