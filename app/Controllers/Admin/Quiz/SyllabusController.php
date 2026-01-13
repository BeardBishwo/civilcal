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
        // Exclude "Unassigned / Draft" from the main listing (it's an internal pool, not a curriculum)
        $sql = "SELECT level, COUNT(*) as total_nodes, 
                SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active_nodes,
                SUM(questions_weight) as total_weight, MAX(updated_at) as last_modified
                FROM syllabus_nodes 
                WHERE level IS NOT NULL 
                  AND level != '' 
                  AND level != 'Unassigned / Draft'
                GROUP BY level 
                ORDER BY level ASC";
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
                   qt.name as topic_name,
                   pl.title as position_level_name
            FROM syllabus_nodes sn
            LEFT JOIN quiz_categories qc ON sn.linked_category_id = qc.id
            LEFT JOIN quiz_topics qt ON sn.linked_topic_id = qt.id
            LEFT JOIN position_levels pl ON sn.linked_position_level_id = pl.id
            WHERE " . ($isUnassigned ? "(sn.level IS NULL OR sn.level = '' OR sn.level = 'Unassigned / Draft')" : "sn.level = :level") . "
            ORDER BY sn.order ASC, sn.id ASC
        ";

        $params = $isUnassigned ? [] : ['level' => $level];
        $nodes = $this->db->query($sql, $params)->fetchAll();

        // Recursively build tree
        $nodesTree = $this->buildSyllabusTree($nodes);

        // Fetch Categories (Syllabus Nodes of type 'category')
        $categoriesSql = "
            SELECT cn.id, cn.title as name, cn.parent_id as edu_level_id, edu.parent_id as course_id
            FROM syllabus_nodes cn
            LEFT JOIN syllabus_nodes edu ON cn.parent_id = edu.id
            WHERE cn.type = 'category' AND cn.is_active = 1
            ORDER BY cn.title ASC
        ";
        $categories = $this->db->query($categoriesSql)->fetchAll();


        // Fetch Sub-Categories (Syllabus Nodes with a parent)
        // Aliasing fields to match JS expectations (id, name, category_id, category_name)
        $subjectsSql = "
            SELECT sn.id, sn.title as name, sn.parent_id as category_id, 
                   cat.title as category_name, cat.parent_id as edu_level_id, edu.parent_id as course_id
            FROM syllabus_nodes sn
            JOIN syllabus_nodes cat ON sn.parent_id = cat.id
            LEFT JOIN syllabus_nodes edu ON cat.parent_id = edu.id
            WHERE sn.parent_id IS NOT NULL AND sn.is_active = 1
              AND sn.type NOT IN ('course', 'education_level', 'category', 'position')
            ORDER BY sn.title ASC
        ";
        $subjects = $this->db->query($subjectsSql)->fetchAll();


        // Fetch Position Levels for Filtering
        $allPosLevels = $this->db->find('position_levels', [], 'title ASC');

        // Fetch Courses and Education Levels for Modal Filters
        $allCourses = $this->db->find('syllabus_nodes', ['type' => 'course', 'is_active' => 1], 'title ASC');
        $allEduLevels = $this->db->find('syllabus_nodes', ['type' => 'education_level', 'is_active' => 1], 'title ASC');

        // Fetch quiz subjects, subcategories, and topics for dropdowns
        $quizSubjects = $this->db->query("SELECT id, name, category_id FROM quiz_subjects WHERE is_active = 1 ORDER BY name ASC")->fetchAll();
        $quizSubcategories = $this->db->query("SELECT id, name, subject_id FROM quiz_subcategories WHERE is_active = 1 ORDER BY name ASC")->fetchAll();
        $quizTopics = $this->db->query("SELECT id, name, subject_id FROM quiz_topics WHERE is_active = 1 ORDER BY name ASC")->fetchAll();

        // Fetch level-wide settings
        $settingsRow = $this->db->findOne('syllabus_settings', ['level' => $level]);
        $settings = [];
        if ($settingsRow) {
            $settings = [
                'marks' => $settingsRow['full_marks'] ?? 0,
                'time' => $settingsRow['total_time'] ?? 0,
                'pass' => $settingsRow['pass_marks'] ?? 0,
                'negative_marking_rate' => $settingsRow['negative_rate'] ?? 0.00
            ];
        }

        // Fetch position level metadata for breadcrumbs
        $posLevel = $this->db->findOne('position_levels', ['slug' => $level]);
        if (!$posLevel) $posLevel = $this->db->findOne('position_levels', ['title' => $level]);

        $breadcrumbBase = [];
        if ($posLevel) {
            $course = $this->db->findOne('syllabus_nodes', ['id' => $posLevel['course_id']]);
            $edu = $this->db->findOne('syllabus_nodes', ['id' => $posLevel['education_level_id']]);
            if ($course) $breadcrumbBase[] = ['id' => $course['id'], 'name' => $course['title'], 'type' => 'course'];
            if ($edu) $breadcrumbBase[] = ['id' => $edu['id'], 'name' => $edu['title'], 'type' => 'education_level'];
            $breadcrumbBase[] = ['id' => $posLevel['id'], 'name' => $posLevel['title'], 'type' => 'position'];
        }

        return $this->view('admin/quiz/syllabus/manage', [
            'page_title' => "Editing: $level",
            'nodes' => $nodes,
            'nodesTree' => $nodesTree,
            'level' => $level,
            'categories' => $categories,
            'subjects' => $subjects,
            'quizSubjects' => $quizSubjects,
            'subcategories' => $quizSubcategories,
            'topics' => $quizTopics,
            'settings' => $settings,
            'breadcrumbBase' => $breadcrumbBase,
            'allCourses' => $allCourses,
            'allEduLevels' => $allEduLevels,
            'allPosLevels' => $allPosLevels
        ]);
    }

    private function buildSyllabusTree($nodes, $parentId = null, $depth = 0)
    {
        $branch = [];
        foreach ($nodes as $node) {
            if ($node['parent_id'] == $parentId) {
                $node['depth'] = $depth;
                $children = $this->buildSyllabusTree($nodes, $node['id'], $depth + 1);
                $node['children'] = $children;
                $branch[] = $node;
            }
        }
        return $branch;
    }

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
            $this->db->delete('syllabus_nodes', "level = :level", ['level' => $level]);
            $parentStack = [null];

            foreach ($nodes as $index => $node) {
                $currentDepth = (int)$node['depth'];
                $parentId = ($currentDepth > 0 && isset($parentStack[$currentDepth - 1]))
                    ? $parentStack[$currentDepth - 1]
                    : null;

                $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $node['title']), '-'));

                $data = [
                    'level' => $level,
                    'title' => $node['title'],
                    'slug' => $slug,
                    'type' => $node['type'] ?? 'topic',
                    'parent_id' => $parentId,
                    'questions_weight' => $node['weight'] ?? 0,
                    'time_minutes' => $node['time'] ?? 0,
                    'question_count' => $node['qCount'] ?? 0,
                    'question_optional' => $node['qOptional'] ?? 0,
                    'question_marks_each' => ($node['type'] === 'topic' || $node['type'] === 'position_level' || $node['type'] === 'unit') ? ($node['qEach'] ?? 0) : 0,
                    'question_type' => ($node['type'] === 'topic' || $node['type'] === 'position_level' || $node['type'] === 'unit') ? ($node['qType'] ?? 'any') : 'any',
                    'difficulty_constraint' => ($node['type'] === 'topic' || $node['type'] === 'position_level' || $node['type'] === 'unit') ? ($node['difficulty'] ?? 'any') : 'any',
                    'order' => $index,
                    'is_active' => 1,
                    // Hierarchy Links
                    'linked_category_id' => !empty($node['linked_category_id']) ? $node['linked_category_id'] : null,
                    'linked_topic_id' => !empty($node['linked_topic_id']) ? $node['linked_topic_id'] : null,
                    'linked_position_level_id' => !empty($node['linked_position_level_id']) ? $node['linked_position_level_id'] : null
                ];

                $this->db->insert('syllabus_nodes', $data);
                $newId = $this->db->lastInsertId();
                $parentStack[$currentDepth] = $newId;
                for ($i = $currentDepth + 1; $i < 10; $i++) unset($parentStack[$i]);
            }

            if (!empty($settings)) {
                $settingsData = [
                    'level' => $level,
                    'total_time' => (int)($settings['time'] ?? 0),
                    'full_marks' => (int)($settings['marks'] ?? 0),
                    'pass_marks' => (int)($settings['pass'] ?? 0),
                    'negative_rate' => (float)($settings['negValue'] ?? 0.00)
                ];

                $existing = $this->db->findOne('syllabus_settings', ['level' => $level]);
                if ($existing) {
                    $updateData = $settingsData;
                    unset($updateData['level']);
                    $this->db->update('syllabus_settings', $updateData, "level = :level", ['level' => $level]);
                } else {
                    $this->db->insert('syllabus_settings', $settingsData);
                }
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
        $parentId = !empty($_POST['parent_id']) ? $_POST['parent_id'] : null;
        $data = [
            'parent_id' => $parentId,
            'level' => $_POST['level'] ?? 'Unassigned',
            'title' => trim($_POST['title'] ?? 'New Item'),
            'type' => $_POST['type'] ?? 'topic',
            'linked_category_id' => !empty($_POST['linked_category_id']) ? $_POST['linked_category_id'] : null,
            'linked_topic_id' => !empty($_POST['linked_topic_id']) ? $_POST['linked_topic_id'] : null,
            'linked_position_level_id' => !empty($_POST['linked_position_level_id']) ? $_POST['linked_position_level_id'] : null,
            'questions_weight' => (int)($_POST['questions_weight'] ?? 0),
            'is_active' => isset($_POST['is_active']) ? 1 : 0
        ];
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
            'type' => $_POST['type'] ?? 'topic',
            'linked_category_id' => !empty($_POST['linked_category_id']) ? $_POST['linked_category_id'] : null,
            'linked_topic_id' => !empty($_POST['linked_topic_id']) ? $_POST['linked_topic_id'] : null,
            'linked_position_level_id' => !empty($_POST['linked_position_level_id']) ? $_POST['linked_position_level_id'] : null,
            'questions_weight' => (int)($_POST['questions_weight'] ?? 0),
            'is_active' => isset($_POST['is_active']) ? 1 : 0
        ];
        if ($this->db->update('syllabus_nodes', $data, "id = :id", ['id' => $id])) {
            echo json_encode(['status' => 'success', 'message' => 'Updated successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Update failed']);
        }
    }

    public function delete($id)
    {
        $node = $this->db->findOne('syllabus_nodes', ['id' => $id]);
        if ($node && in_array($node['type'], ['course', 'education_level', 'category', 'sub_category'])) {
            echo json_encode(['status' => 'error', 'message' => 'Protected Item: Cannot delete hierarchy nodes from Syllabus Manager.']);
            return;
        }
        if ($this->db->delete('syllabus_nodes', "id = :id", ['id' => $id])) {
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
            $protectedTypes = "type IN ('course', 'education_level', 'category', 'sub_category', 'section')";
            $this->db->delete('syllabus_settings', "level = :target_level", ['target_level' => $level]);
            $this->db->update('syllabus_nodes', ['level' => null], "level = :target_level AND ($protectedTypes)", ['target_level' => $level]);
            $this->db->delete('syllabus_nodes', "level = :target_level AND NOT ($protectedTypes)", ['target_level' => $level]);
            echo json_encode(['status' => 'success', 'message' => "Syllabus '$level' deleted. Master filters preserved."]);
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
            $nodes = $this->db->find('syllabus_nodes', ['level' => $oldLevel], '`order` ASC');
            $idMapping = [];
            foreach ($nodes as $node) {
                $oldId = $node['id'];
                $data = $node;
                unset($data['id']);
                $data['level'] = $newLevel;
                $data['parent_id'] = null;
                $this->db->insert('syllabus_nodes', $data);
                $idMapping[$oldId] = $this->db->lastInsertId();
            }
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
            $level = $_POST['level'] ?? 'Level 5';

            // Resolve course_id from position_levels
            $posLevel = $this->db->findOne('position_levels', ['slug' => $level]);
            if (!$posLevel) $posLevel = $this->db->findOne('position_levels', ['title' => $level]);
            $courseId = $posLevel['course_id'] ?? null;

            $generator = new \App\Services\ExamGeneratorService();
            $options = ['shuffle' => true, 'duration' => (int)($_POST['duration'] ?? 45), 'negative_rate' => (float)($_POST['negative_rate'] ?? 20.00)];
            $generatedExam = $generator->generateFromSyllabus($level, $options);

            $examData = [
                'title' => "Mock Exam: $level (" . date('M d, Y') . ")",
                'type' => 'mock_test',
                'course_id' => $courseId // Save resolved course_id
            ];

            $examId = $generator->saveGeneratedExam($generatedExam, $examData);
            echo json_encode(['status' => 'success', 'message' => "Exam generated successfully with " . count($generatedExam['questions']) . " questions.", 'exam_id' => $examId, 'redirect' => app_base_url('admin/quiz/exams/edit/' . $examId)]);
        } catch (\Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function getChildren()
    {
        header('Content-Type: application/json');
        try {
            $parentId = $_GET['parent_id'] ?? null;
            if ($parentId === null) {
                echo json_encode([]);
                return;
            }
            $sql = "SELECT id, title, type FROM syllabus_nodes WHERE parent_id = :parent_id AND is_active = 1 ORDER BY `order` ASC";
            $stmt = $this->db->getPdo()->prepare($sql);
            $stmt->execute(['parent_id' => $parentId]);
            $children = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            echo json_encode($children);
        } catch (\Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
        exit;
    }

    public function cloneSyllabus()
    {
        $input = json_decode(file_get_contents('php://input'), true);
        $oldLevel = $input['level'] ?? null;
        $versionLabel = $input['version_label'] ?? null;
        if (!$oldLevel || !$versionLabel) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid parameters']);
            return;
        }
        $versionSlug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $versionLabel), '-'));
        $newLevel = $oldLevel . '-' . $versionSlug;
        try {
            $this->db->beginTransaction();
            $existing = $this->db->findOne('syllabus_settings', ['level' => $newLevel]);
            if ($existing) throw new \Exception("A syllabus with slug '$newLevel' already exists.");
            $oldSettings = $this->db->findOne('syllabus_settings', ['level' => $oldLevel]);
            if (!$oldSettings) throw new \Exception("Source syllabus '$oldLevel' not found.");
            $newSettings = $oldSettings;
            $newSettings['level'] = $newLevel;
            $newSettings['version_label'] = $versionLabel;
            $newSettings['is_active'] = 0;
            $this->db->insert('syllabus_settings', $newSettings);
            $nodes = $this->db->find('syllabus_nodes', ['level' => $oldLevel], '`order` ASC');
            $idMapping = [];
            foreach ($nodes as $node) {
                $oldId = $node['id'];
                $data = $node;
                unset($data['id']);
                $data['level'] = $newLevel;
                $data['parent_id'] = null;
                $this->db->insert('syllabus_nodes', $data);
                $idMapping[$oldId] = $this->db->lastInsertId();
            }
            foreach ($nodes as $node) {
                if ($node['parent_id'] && isset($idMapping[$node['parent_id']])) {
                    $newId = $idMapping[$node['id']];
                    $newParentId = $idMapping[$node['parent_id']];
                    $this->db->update('syllabus_nodes', ['parent_id' => $newParentId], "id = :id", ['id' => $newId]);
                }
            }
            $this->db->commit();
            echo json_encode(['status' => 'success', 'new_level' => $newLevel]);
        } catch (\Exception $e) {
            $this->db->rollBack();
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
