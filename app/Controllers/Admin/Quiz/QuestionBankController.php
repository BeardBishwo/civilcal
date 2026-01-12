<?php

namespace App\Controllers\Admin\Quiz;

use App\Core\Controller;
use App\Services\SyllabusService;
use Exception;

class QuestionBankController extends Controller
{
    private $syllabusService;

    public function __construct()
    {
        parent::__construct();
        if (!$this->auth->check() || !$this->auth->isAdmin()) {
            header('Location: ' . app_base_url('login'));
            exit;
        }
        $this->syllabusService = new SyllabusService();
    }

    /**
     * List Questions
     */
    public function index()
    {
        $page = $_GET['page'] ?? 1;
        $perPage = $_GET['per_page'] ?? 10;

        // Validate per_page to prevent abuse
        $allowedPerPage = [5, 10, 20, 50, 100, 200];
        if (!in_array($perPage, $allowedPerPage)) {
            $perPage = 10;
        }

        $limit = (int)$perPage;
        $offset = ($page - 1) * $limit;

        $where = [];
        $params = [];

        // Filter by Topic
        if (!empty($_GET['topic_id'])) {
            $where[] = "q.topic_id = :topic_id";
            $params['topic_id'] = $_GET['topic_id'];
        }

        // Filter by Type (Enhanced for Theory)
        if (!empty($_GET['type'])) {
            $type = $_GET['type'];

            if ($type === 'THEORY') {
                // All theory questions
                $where[] = "q.type = 'THEORY'";
            } elseif ($type === 'THEORY_SHORT') {
                // Short answer only
                $where[] = "q.type = 'THEORY' AND (q.theory_type = 'short' OR q.default_marks <= 4)";
            } elseif ($type === 'THEORY_LONG') {
                // Long answer only
                $where[] = "q.type = 'THEORY' AND (q.theory_type = 'long' OR q.default_marks > 4)";
            } elseif ($type === 'SEQUENCE') {
                // Map frontend SEQUENCE to backend ORDER
                $where[] = "q.type = 'ORDER'";
            } else {
                // Other types
                $where[] = "q.type = :type";
                $params['type'] = $type;
            }
        }

        // Filter by Course/Stream (OPTIMIZED)
        if (!empty($_GET['stream'])) {
            $where[] = "q.course_id = :course_id";
            $params['course_id'] = $_GET['stream'];
        }

        // Filter by Education Level (OPTIMIZED)
        if (!empty($_GET['education_level'])) {
            $where[] = "q.edu_level_id = :edu_level";
            $params['edu_level'] = $_GET['education_level'];
        }

        // Filter by Category (NEW)
        if (!empty($_GET['category_id'])) {
            $where[] = "q.category_id = :category_id";
            $params['category_id'] = $_GET['category_id'];
        }

        // Filter by Position Level (Junction Table)
        if (!empty($_GET['level_tag'])) {
            $where[] = "EXISTS (SELECT 1 FROM question_position_levels qpl WHERE qpl.question_id = q.id AND qpl.position_level_id = :level_tag)";
            $params['level_tag'] = $_GET['level_tag']; // ID integer
        }

        // Search
        if (!empty($_GET['search'])) {
            $where[] = "JSON_UNQUOTE(JSON_EXTRACT(q.content, '$.text')) LIKE :search";
            $params['search'] = '%' . $_GET['search'] . '%';
        }

        $whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

        // Count total
        $sqlCount = "SELECT COUNT(*) as total FROM quiz_questions q $whereClause";
        $stmt = $this->db->getPdo()->prepare($sqlCount);
        $stmt->execute($params);
        $total = $stmt->fetchColumn();

        // Fetch items with linked education levels
        $sql = "
            SELECT q.*, 
                   t.name as topic_name,
                   GROUP_CONCAT(DISTINCT qel.education_level_id ORDER BY qel.education_level_id) as linked_education_levels
            FROM quiz_questions q 
            LEFT JOIN quiz_topics t ON q.topic_id = t.id 
            LEFT JOIN question_education_levels qel ON q.id = qel.question_id
            $whereClause 
            GROUP BY q.id
            ORDER BY q.id DESC 
            LIMIT $limit OFFSET $offset
        ";

        $stmt = $this->db->getPdo()->prepare($sql);
        $stmt->execute($params);
        $questions = $stmt->fetchAll();

        // Calculate Stats
        $stats = ['total' => $total, 'mcq' => 0, 'tf' => 0, 'multi' => 0, 'order' => 0, 'theory' => 0, 'theory_short' => 0, 'theory_long' => 0];
        $stmtStats = $this->db->getPdo()->query("SELECT type, COUNT(*) as count FROM quiz_questions GROUP BY type");
        while ($row = $stmtStats->fetch()) {
            $type = $row['type'];
            if ($type == 'MCQ') $stats['mcq'] += $row['count'];
            if ($type == 'TF') $stats['tf'] += $row['count'];
            if ($type == 'MULTI') $stats['multi'] += $row['count'];
            if ($type == 'ORDER') $stats['order'] += $row['count'];
            if ($type == 'THEORY') $stats['theory'] += $row['count'];
        }

        // Get theory sub-type counts
        $theoryStats = $this->db->query("
            SELECT 
                SUM(CASE WHEN theory_type = 'short' OR default_marks <= 4 THEN 1 ELSE 0 END) as short_count,
                SUM(CASE WHEN theory_type = 'long' OR default_marks > 4 THEN 1 ELSE 0 END) as long_count
            FROM quiz_questions 
            WHERE type = 'THEORY'
        ")->fetch();
        $stats['theory_short'] = $theoryStats['short_count'] ?? 0;
        $stats['theory_long'] = $theoryStats['long_count'] ?? 0;

        // Load roots from syllabus tree
        $mainCategories = $this->db->query("SELECT id, title FROM syllabus_nodes WHERE parent_id IS NULL AND is_active = 1 ORDER BY order_index ASC")->fetchAll();

        $this->view->render('admin/quiz/questions/index', [
            'page_title' => 'Question Bank',
            'questions' => $questions,
            'total' => $total,
            'stats' => $stats,
            'page' => $page,
            'limit' => $limit,
            'offset' => $offset,
            'totalPages' => ceil($total / $limit),
            'mainCategories' => $mainCategories,
            'courses' => $this->db->query("SELECT id, title FROM syllabus_nodes WHERE type = 'course' ORDER BY order_index ASC")->fetchAll(),
            'educationLevels' => $this->db->query("SELECT id, title FROM syllabus_nodes WHERE type = 'education_level' ORDER BY order_index ASC")->fetchAll(),
            'positionLevels' => $this->db->query("SELECT id, title FROM position_levels WHERE is_active = 1 ORDER BY order_index ASC")->fetchAll()
        ]);
    }

    /**
     * Show Create Form
     */
    public function create()
    {
        $mainCategories = $this->db->query("SELECT * FROM syllabus_nodes WHERE parent_id IS NULL ORDER BY order_index ASC")->fetchAll();
        $subNodes = $this->db->query("SELECT id, parent_id, title FROM syllabus_nodes WHERE parent_id IS NOT NULL ORDER BY order_index ASC")->fetchAll();

        $groupedSub = [];
        foreach ($subNodes as $node) {
            $groupedSub[$node['parent_id']][] = $node;
        }

        // NEW: Course / Streams
        $streams = [
            'civil' => 'Civil Engineering',
            'electrical' => 'Electrical Engineering',
            'computer' => 'Computer Engineering',
            'general' => 'General Awareness'
        ];

        // NEW: Education Levels
        $educationLevels = [
            'tslc' => 'TSLC (Asst. Sub-Engineer)',
            'diploma' => 'Diploma (Sub-Engineer)',
            'bachelor' => 'Bachelor (Engineer)',
            'master' => 'Master (Senior Engineer)'
        ];

        // Position Levels (PSC)
        $pscLevels = [
            ['id' => 'level_4', 'name' => 'Level 4 (Assistant)'],
            ['id' => 'level_5', 'name' => 'Level 5 (Sub-Engineer)'],
            ['id' => 'level_6', 'name' => 'Level 6 (Engineer)'],
            ['id' => 'level_7', 'name' => 'Level 7 (Senior)']
        ];

        $this->view->render('admin/quiz/questions/form', [
            'page_title' => 'Add New Question',
            'mainCategories' => $mainCategories,
            'subCategories' => $groupedSub,
            'courses' => $this->db->query("SELECT id, title FROM syllabus_nodes WHERE type = 'course' ORDER BY order_index ASC")->fetchAll(),
            'educationLevels' => $this->db->query("SELECT id, title, parent_id FROM syllabus_nodes WHERE type = 'education_level' ORDER BY order_index ASC")->fetchAll(),
            'positionLevels' => $this->db->query("SELECT id, title, level_number FROM position_levels WHERE is_active = 1 ORDER BY order_index ASC")->fetchAll(),
            'question' => null,
            'action' => app_base_url('admin/quiz/questions/store')
        ]);
    }

    /**
     * Show Edit Form
     */
    public function edit($id)
    {
        $question = $this->db->findOne('quiz_questions', ['id' => $id]);
        if (!$question) {
            header('Location: ' . app_base_url('admin/quiz/questions'));
            exit;
        }

        // Decode JSON fields
        $question['content'] = json_decode($question['content'], true);
        $question['options'] = json_decode($question['options'], true);
        $question['tags'] = json_decode($question['tags'] ?? '[]', true);
        $question['level_tags'] = json_decode($question['level_tags'] ?? '[]', true);

        // Fetch mappings
        $question['mappings'] = $this->db->query("SELECT * FROM question_stream_map WHERE question_id = :id", ['id' => $id])->fetchAll();

        // Fetch current position levels
        $selectedPosLevels = $this->db->query("SELECT position_level_id FROM question_position_levels WHERE question_id = :id", ['id' => $id])->fetchAll(\PDO::FETCH_COLUMN);
        $question['position_levels'] = $selectedPosLevels;

        // Fetch current education level links
        $selectedEduLevels = $this->db->query("SELECT education_level_id FROM question_education_levels WHERE question_id = :id", ['id' => $id])->fetchAll(\PDO::FETCH_COLUMN);
        $question['education_level_links'] = $selectedEduLevels;

        $mainCategories = $this->db->query("SELECT * FROM syllabus_nodes WHERE parent_id IS NULL ORDER BY order_index ASC")->fetchAll();
        $subNodes = $this->db->query("SELECT id, parent_id, title FROM syllabus_nodes WHERE parent_id IS NOT NULL ORDER BY order_index ASC")->fetchAll();

        $groupedSub = [];
        foreach ($subNodes as $node) {
            $groupedSub[$node['parent_id']][] = $node;
        }

        $this->view->render('admin/quiz/questions/form', [
            'page_title' => 'Edit Question',
            'mainCategories' => $mainCategories,
            'subCategories' => $groupedSub,
            'courses' => $this->db->query("SELECT id, title FROM syllabus_nodes WHERE type = 'course' ORDER BY order_index ASC")->fetchAll(),
            'educationLevels' => $this->db->query("SELECT id, title, parent_id FROM syllabus_nodes WHERE type = 'education_level' ORDER BY order_index ASC")->fetchAll(),
            'positionLevels' => $this->db->query("SELECT id, title, level_number FROM position_levels WHERE is_active = 1 ORDER BY order_index ASC")->fetchAll(),
            'question' => $question,
            'action' => app_base_url('admin/quiz/questions/update/' . $id)
        ]);
    }

    /**
     * Store Question
     */
    public function store()
    {
        try {
            if (empty($_POST['syllabus_node_id'])) throw new Exception("Topic is required");
            if (empty($_POST['question_text'])) throw new Exception("Question text is required");

            $type = $_POST['type'];

            $content = [
                'text' => $_POST['question_text'],
                'image' => $_POST['question_image'] ?? null
            ];

            $options = [];
            if (!empty($_POST['options'])) {
                // Clean up logic: always check is_correct flag
                // The view sets options[i][is_correct] = 1 for ALL types (MCQ, TF, Multi, Order)
                foreach ($_POST['options'] as $idx => $opt) {
                    $isCorrect = (isset($opt['is_correct']) && $opt['is_correct'] == 1) ? 1 : 0;

                    $options[] = [
                        'id' => $idx + 1,
                        'text' => $opt['text'] ?? '',
                        'is_correct' => $isCorrect
                    ];
                }
            }

            // Tags: Combine Stream + Education Level into general tags
            $tags = [];
            if (!empty($_POST['stream'])) $tags[] = $_POST['stream'];
            if (!empty($_POST['education_level'])) $tags[] = $_POST['education_level'];

            $levelTags = !empty($_POST['level_tags']) ? json_encode($_POST['level_tags']) : json_encode([]);

            // NEW: Relational Filter Persistence
            $filterContext = [
                'course_id' => null,
                'edu_level_id' => null,
                'category_id' => null,
                'sub_category_id' => null
            ];

            if (!empty($_POST['mappings']) && is_array($_POST['mappings'])) {
                foreach ($_POST['mappings'] as $m) {
                    // RESOLVE CONTEXT FROM DEEPEST NODE (Topic > Unit > Category > Level > Course)
                    $deepestNodeId = !empty($m['topic_id']) ? $m['topic_id'] : (!empty($m['unit_id']) ? $m['unit_id'] : (!empty($m['category_id']) ? $m['category_id'] : (!empty($m['level_id']) ? $m['level_id'] : (!empty($m['course_id']) ? $m['course_id'] : null))));

                    if ($deepestNodeId) {
                        $filterContext = $this->syllabusService->resolveFilterContext($deepestNodeId);
                        break; // Use the first valid unit/topic for master filtering
                    }
                }
            }

            $data = [
                'unique_code' => 'Q-' . time(),
                'topic_id' => 0,
                'course_id' => $filterContext['course_id'],
                'edu_level_id' => $filterContext['edu_level_id'],
                'category_id' => $filterContext['category_id'],
                'sub_category_id' => $filterContext['sub_category_id'],
                'type' => $type,
                'content' => json_encode($content),
                'options' => json_encode($options),
                'answer_explanation' => $_POST['answer_explanation'] ?? '',
                'difficulty_level' => $_POST['difficulty_level'] ?? 1,
                'level_tags' => $levelTags, // Position Levels (4,5,6)
                'tags' => json_encode($tags), // Stream/Education context
                'default_marks' => $_POST['default_marks'] ?? 1.0,
                'default_negative_marks' => $_POST['default_negative_marks'] ?? 0.0,
                'status' => $_POST['status'] ?? 'approved',
                'is_active' => isset($_POST['is_active']) ? 1 : 0,
                'created_by' => $_SESSION['user']['id'] ?? null
            ];

            if (!$this->db->insert('quiz_questions', $data)) {
                throw new Exception("Failed to insert");
            }

            $questionId = $this->db->lastInsertId();

            // Sync Junction Table: question_position_levels
            if (!empty($_POST['position_levels'])) {
                $posLevels = is_array($_POST['position_levels']) ? $_POST['position_levels'] : [];
                foreach ($posLevels as $plId) {
                    $this->db->insert('question_position_levels', [
                        'question_id' => $questionId,
                        'position_level_id' => $plId
                    ]);
                }
            }

            // Sync Junction Table: question_education_levels (NEW)
            if (!empty($_POST['education_level_links'])) {
                $eduLevels = is_array($_POST['education_level_links']) ? $_POST['education_level_links'] : [];
                foreach ($eduLevels as $levelId) {
                    $this->db->insert('question_education_levels', [
                        'question_id' => $questionId,
                        'education_level_id' => $levelId
                    ]);
                }
            }

            // Map to Syllabus (Multiple Mappings Support)
            if (!empty($_POST['mappings']) && is_array($_POST['mappings'])) {
                $isPrimary = true; // First mapping is primary
                foreach ($_POST['mappings'] as $mapping) {
                    // Determine Syllabus Node ID (Deepest Available Level)
                    $nodeId = !empty($mapping['topic_id']) ? $mapping['topic_id'] : (!empty($mapping['unit_id']) ? $mapping['unit_id'] : (!empty($mapping['category_id']) ? $mapping['category_id'] : (!empty($mapping['level_id']) ? $mapping['level_id'] : (!empty($mapping['course_id']) ? $mapping['course_id'] : null))));

                    if ($nodeId) {
                        $this->db->insert('question_stream_map', [
                            'question_id' => $questionId,
                            'syllabus_node_id' => $nodeId,
                            'difficulty_in_stream' => $_POST['difficulty_level'] ?? 3,
                            'priority' => $mapping['priority'] ?? 1,
                            'is_primary' => $isPrimary ? 1 : 0
                        ]);
                        $isPrimary = false; // Only first is primary
                    }
                }
            }

            $this->jsonResponse(['success' => true, 'message' => 'Saved Successfully', 'redirect' => app_base_url('admin/quiz/questions')]);
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Update Question
     */
    public function update($id)
    {
        try {
            if (empty($_POST['question_text'])) throw new Exception("Question text is required");

            $type = $_POST['type'];
            $content = [
                'text' => $_POST['question_text'],
                'image' => $_POST['question_image'] ?? null
            ];

            $options = [];
            if (!empty($_POST['options'])) {
                foreach ($_POST['options'] as $idx => $opt) {
                    $isCorrect = (isset($opt['is_correct']) && $opt['is_correct'] == 1) ? 1 : 0;
                    $options[] = [
                        'id' => $idx + 1,
                        'text' => $opt['text'] ?? '',
                        'is_correct' => $isCorrect
                    ];
                }
            }

            $tags = [];
            if (!empty($_POST['stream'])) $tags[] = $_POST['stream'];
            if (!empty($_POST['education_level'])) $tags[] = $_POST['education_level'];

            $levelTags = !empty($_POST['level_tags']) ? json_encode($_POST['level_tags']) : json_encode([]);

            // NEW: Relational Filter Persistence
            $filterContext = [
                'course_id' => null,
                'edu_level_id' => null,
                'category_id' => null,
                'sub_category_id' => null
            ];

            if (!empty($_POST['mappings']) && is_array($_POST['mappings'])) {
                foreach ($_POST['mappings'] as $m) {
                    $deepestNodeId = !empty($m['topic_id']) ? $m['topic_id'] : (!empty($m['unit_id']) ? $m['unit_id'] : (!empty($m['category_id']) ? $m['category_id'] : (!empty($m['level_id']) ? $m['level_id'] : (!empty($m['course_id']) ? $m['course_id'] : null))));

                    if ($deepestNodeId) {
                        $filterContext = $this->syllabusService->resolveFilterContext($deepestNodeId);
                        break;
                    }
                }
            }

            $data = [
                'type' => $type,
                'course_id' => $filterContext['course_id'],
                'edu_level_id' => $filterContext['edu_level_id'],
                'category_id' => $filterContext['category_id'],
                'sub_category_id' => $filterContext['sub_category_id'],
                'content' => json_encode($content),
                'options' => json_encode($options),
                'answer_explanation' => $_POST['answer_explanation'] ?? '',
                'difficulty_level' => $_POST['difficulty_level'] ?? 1,
                'level_tags' => $levelTags,
                'tags' => json_encode($tags),
                'status' => $_POST['status'] ?? 'approved',
                'default_marks' => $_POST['default_marks'] ?? 1.0,
                'default_negative_marks' => $_POST['default_negative_marks'] ?? 0.0,
                'is_active' => isset($_POST['is_active']) ? 1 : 0,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $this->db->update('quiz_questions', $data, ['id' => $id]);

            // Sync Position Levels
            $this->db->delete('question_position_levels', ['question_id' => $id]);
            if (!empty($_POST['position_levels'])) {
                foreach ($_POST['position_levels'] as $plId) {
                    $this->db->insert('question_position_levels', [
                        'question_id' => $id,
                        'position_level_id' => $plId
                    ]);
                }
            }

            // Sync Education Level Links (NEW)
            $this->db->delete('question_education_levels', ['question_id' => $id]);
            if (!empty($_POST['education_level_links'])) {
                foreach ($_POST['education_level_links'] as $levelId) {
                    $this->db->insert('question_education_levels', [
                        'question_id' => $id,
                        'education_level_id' => $levelId
                    ]);
                }
            }

            // Sync Mappings
            $this->db->delete('question_stream_map', ['question_id' => $id]);
            if (!empty($_POST['mappings']) && is_array($_POST['mappings'])) {
                $isPrimary = true;
                foreach ($_POST['mappings'] as $mapping) {
                    $nodeId = !empty($mapping['topic_id']) ? $mapping['topic_id'] : (!empty($mapping['unit_id']) ? $mapping['unit_id'] : (!empty($mapping['category_id']) ? $mapping['category_id'] : (!empty($mapping['level_id']) ? $mapping['level_id'] : (!empty($mapping['course_id']) ? $mapping['course_id'] : null))));

                    if ($nodeId) {
                        $this->db->insert('question_stream_map', [
                            'question_id' => $id,
                            'syllabus_node_id' => $nodeId,
                            'difficulty_in_stream' => $_POST['difficulty_level'] ?? 3,
                            'priority' => $mapping['priority'] ?? 1,
                            'is_primary' => $isPrimary ? 1 : 0
                        ]);
                        $isPrimary = false;
                    }
                }
            }

            $this->jsonResponse(['success' => true, 'message' => 'Updated Successfully', 'redirect' => app_base_url('admin/quiz/questions')]);
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function delete($id)
    {
        try {
            // Check if question exists
            $question = $this->db->findOne('quiz_questions', ['id' => $id]);
            if (!$question) {
                return $this->jsonResponse(['success' => false, 'status' => 'error', 'message' => 'Question not found']);
            }

            // 1. Delete syllabus mappings (Primary/Secondary)
            $this->db->delete('question_stream_map', "question_id = :qid", ['qid' => $id]);

            // 2. Delete position levels (Junction)
            $this->db->delete('question_position_levels', "question_id = :qid", ['qid' => $id]);

            // 3. Delete education level links (Junction - NEW)
            $this->db->delete('question_education_levels', "question_id = :qid", ['qid' => $id]);

            // 4. Finally delete the question
            $this->db->delete('quiz_questions', "id = :id", ['id' => $id]);

            return $this->jsonResponse(['success' => true, 'status' => 'success', 'message' => 'Question deleted successfully']);
        } catch (Exception $e) {
            return $this->jsonResponse(['success' => false, 'status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function bulkDelete()
    {
        try {
            // Get IDs from POST
            $input = json_decode(file_get_contents('php://input'), true);
            $ids = $input['ids'] ?? [];

            if (empty($ids) || !is_array($ids)) {
                return $this->jsonResponse(['success' => false, 'status' => 'error', 'message' => 'No questions selected']);
            }

            // Validate IDs are integers
            $ids = array_filter($ids, 'is_numeric');
            $ids = array_map('intval', $ids);

            if (empty($ids)) {
                return $this->jsonResponse(['success' => false, 'status' => 'error', 'message' => 'Invalid question IDs']);
            }

            $successCount = 0;
            $failCount = 0;

            // Use transaction for better performance
            $this->db->beginTransaction();

            try {
                $placeholders = implode(',', array_fill(0, count($ids), '?'));

                // 1. Delete all mappings in one query
                $stmt = $this->db->prepare("DELETE FROM question_stream_map WHERE question_id IN ($placeholders)");
                $stmt->execute($ids);

                // 2. Delete all position levels in one query
                $stmt = $this->db->prepare("DELETE FROM question_position_levels WHERE question_id IN ($placeholders)");
                $stmt->execute($ids);

                // 3. Delete all questions in one query
                $stmt = $this->db->prepare("DELETE FROM quiz_questions WHERE id IN ($placeholders)");
                $stmt->execute($ids);

                $successCount = $stmt->rowCount();

                $this->db->commit();

                return $this->jsonResponse([
                    'success' => true,
                    'status' => 'success',
                    'message' => "Successfully deleted $successCount question(s)",
                    'deleted' => $successCount
                ]);
            } catch (Exception $e) {
                $this->db->rollBack();
                throw $e;
            }
        } catch (Exception $e) {
            return $this->jsonResponse(['success' => false, 'status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    private function jsonResponse($data)
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
