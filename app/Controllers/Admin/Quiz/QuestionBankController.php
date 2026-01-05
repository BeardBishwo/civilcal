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
        $limit = 20;
        $offset = ($page - 1) * $limit;
        
        $where = [];
        $params = [];
        
        // Filter by Topic
        if (!empty($_GET['topic_id'])) {
            $where[] = "q.topic_id = :topic_id";
            $params['topic_id'] = $_GET['topic_id'];
        }
        
        // Filter by Type
        if (!empty($_GET['type'])) {
            $where[] = "q.type = :type";
            $params['type'] = $_GET['type'];
        }

        // Filter by Course/Stream (NEW)
        if (!empty($_GET['stream'])) {
            $where[] = "JSON_CONTAINS(q.tags, :stream)";
            $params['stream'] = '"' . $_GET['stream'] . '"';
        }

        // Filter by Education Level (NEW)
        if (!empty($_GET['education_level'])) {
            $where[] = "JSON_CONTAINS(q.tags, :edu_level)";
            $params['edu_level'] = '"' . $_GET['education_level'] . '"';
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
        
        // Fetch items
        $sql = "
            SELECT q.*, t.name as topic_name 
            FROM quiz_questions q 
            LEFT JOIN quiz_topics t ON q.topic_id = t.id 
            $whereClause 
            ORDER BY q.id DESC 
            LIMIT $limit OFFSET $offset
        ";
        
        $stmt = $this->db->getPdo()->prepare($sql);
        $stmt->execute($params);
        $questions = $stmt->fetchAll();

        // Calculate Stats
        $stats = ['total' => $total, 'mcq' => 0, 'multi' => 0, 'order' => 0];
        $stmtStats = $this->db->getPdo()->query("SELECT type, COUNT(*) as count FROM quiz_questions GROUP BY type");
        while($row = $stmtStats->fetch()) {
            $type = $row['type'];
            if(in_array($type, ['MCQ', 'TF'])) $stats['mcq'] += $row['count'];
            if($type == 'MULTI') $stats['multi'] += $row['count'];
        }

        // Load roots from syllabus tree
        $mainCategories = $this->db->query("SELECT id, title FROM syllabus_nodes WHERE parent_id IS NULL AND is_active = 1 ORDER BY order_index ASC")->fetchAll();

        $this->view->render('admin/quiz/questions/index', [
            'page_title' => 'Question Bank',
            'questions' => $questions,
            'total' => $total,
            'stats' => $stats,
            'page' => $page,
            'limit' => $limit,
            'limit' => $limit,
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
            'subCategories' => $groupedSub,
            'courses' => $this->db->query("SELECT id, title FROM syllabus_nodes WHERE type = 'course' ORDER BY order_index ASC")->fetchAll(),
            'educationLevels' => $this->db->query("SELECT id, title, parent_id FROM syllabus_nodes WHERE type = 'education_level' ORDER BY order_index ASC")->fetchAll(),
            'positionLevels' => $this->db->query("SELECT id, title, level_number FROM position_levels WHERE is_active = 1 ORDER BY order_index ASC")->fetchAll(),
            'question' => null,
            'action' => app_base_url('admin/quiz/questions/store')
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
            if(!empty($_POST['stream'])) $tags[] = $_POST['stream'];
            if(!empty($_POST['education_level'])) $tags[] = $_POST['education_level'];
            
            $levelTags = !empty($_POST['level_tags']) ? json_encode($_POST['level_tags']) : json_encode([]);

            $data = [
                'unique_code' => 'Q-' . time(),
                'topic_id' => 0, 
                'type' => $type,
                'content' => json_encode($content),
                'options' => json_encode($options),
                'answer_explanation' => $_POST['answer_explanation'] ?? '',
                'difficulty_level' => $_POST['difficulty_level'] ?? 1,
                'level_tags' => $levelTags, // Position Levels (4,5,6)
                'tags' => json_encode($tags), // Stream/Education context
                'default_marks' => $_POST['default_marks'] ?? 1.0,
                'default_negative_marks' => $_POST['default_negative_marks'] ?? 0.0,
                'is_active' => isset($_POST['is_active']) ? 1 : 0,
                'default_negative_marks' => $_POST['default_negative_marks'] ?? 0.0,
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

            // Map to Syllabus
            $this->db->insert('question_stream_map', [
                'question_id' => $this->db->lastInsertId(),
                'syllabus_node_id' => $_POST['syllabus_node_id'],
                'difficulty_in_stream' => $_POST['difficulty_level'] ?? 3
            ]);

            $this->jsonResponse(['success' => true, 'message' => 'Saved Successfully', 'redirect' => app_base_url('admin/quiz/questions')]);

        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    private function jsonResponse($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}