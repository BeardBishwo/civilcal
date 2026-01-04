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
            SELECT q.*, t.name as topic_name, s.name as subject_name 
            FROM quiz_questions q 
            LEFT JOIN quiz_topics t ON q.topic_id = t.id 
            LEFT JOIN quiz_subjects s ON t.subject_id = s.id 
            $whereClause 
            ORDER BY q.id DESC 
            LIMIT $limit OFFSET $offset
        ";
        
        $stmt = $this->db->getPdo()->prepare($sql);
        $stmt->execute($params);
        $questions = $stmt->fetchAll();

        // Calculate Stats for Header
        $stats = [
            'total' => $total,
            'mcq' => 0,
            'multi' => 0,
            'order' => 0
        ];

        // Efficiently fetch counts for specific types
        $stmtStats = $this->db->getPdo()->query("SELECT type, COUNT(*) as count FROM quiz_questions GROUP BY type");
        while($row = $stmtStats->fetch()) {
            $type = $row['type'];
            if(in_array($type, ['MCQ', 'mcq_single', 'mcq_single', 'true_false', 'TF'])) $stats['mcq'] += $row['count'];
            if(in_array($type, ['MULTI', 'mcq_multi'])) $stats['multi'] += $row['count'];
            if($type == 'ORDER') $stats['order'] += $row['count'];
        }

        // Load roots from syllabus tree for export scope
        $mainCategories = $this->db->query("SELECT id, title FROM syllabus_nodes WHERE parent_id IS NULL AND is_active = 1 ORDER BY order_index ASC")->fetchAll();

        $this->view->render('admin/quiz/questions/index', [
            'page_title' => 'Question Bank',
            'questions' => $questions,
            'total' => $total,
            'stats' => $stats,
            'page' => $page,
            'limit' => $limit,
            'mainCategories' => $mainCategories
        ]);
    }

    /**
     * Show Create Form
     */
    public function create()
    {
        // 1. Fetch Main Categories (Sorted by custom order)
        // Main categories are Roots (parent_id IS NULL)
        $mainCategories = $this->db->query("SELECT * FROM syllabus_nodes WHERE parent_id IS NULL ORDER BY order_index ASC")->fetchAll();

        // 2. Fetch Sub-Categories (Also Sorted)
        // Grouped by parent in the view logic or prepare here.
        // Let's fetch all active sections/units and pass to view as JSON for the JS filter.
        $subNodes = $this->db->query("SELECT id, parent_id, title, is_premium FROM syllabus_nodes WHERE parent_id IS NOT NULL ORDER BY order_index ASC")->fetchAll();
        
        // Convert to array keyed by parent_id for easier JS handling if we were building it here, 
        // but passing the flat list to JS to filter is often easier for dynamic chains.
        // Actually, let's match the user's requested structure:
        // $subCategories->groupBy('parent_id') logic.
        
        $groupedSub = [];
        foreach ($subNodes as $node) {
            $groupedSub[$node['parent_id']][] = $node;
        }

        $this->view->render('admin/quiz/questions/form', [
            'page_title' => 'Add New Question',
            'mainCategories' => $mainCategories,
            'subCategories' => $groupedSub,
            'last_main_id' => $_SESSION['last_q_main_id'] ?? null,
            'last_sub_id' => $_SESSION['last_q_sub_id'] ?? null,
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
            // Validation
            if (empty($_POST['syllabus_node_id'])) throw new Exception("Topic (Syllabus Node) is required");
            if (empty($_POST['question_text'])) throw new Exception("Question text is required");
            if (empty($_POST['type'])) throw new Exception("Question type is required");

            $type = $_POST['type'];
            $nodeId = $_POST['syllabus_node_id'];
            
            // Build Content JSON
            $content = [
                'text' => $_POST['question_text'],
                'image' => $_POST['question_image'] ?? null,
                'latex' => $_POST['question_latex'] ?? null
            ];

            // Build Options JSON
            $options = [];
            if ($type !== 'text' && $type !== 'numerical') { 
                 if (!empty($_POST['options']) && is_array($_POST['options'])) {
                     foreach ($_POST['options'] as $idx => $opt) {
                         $options[] = [
                             'id' => $idx + 1,
                             'text' => $opt['text'] ?? '',
                             'image' => $opt['image'] ?? null,
                             'is_correct' => isset($opt['is_correct']) && $opt['is_correct'] == 1 ? true : false,
                             'order' => $idx
                         ];
                     }
                 }
            } else if ($type === 'numerical' && isset($_POST['numerical_answer'])) {
                $options[] = [
                    'id' => 1,
                    'text' => $_POST['numerical_answer'],
                    'is_correct' => true,
                    'tolerance' => $_POST['numerical_tolerance'] ?? 0
                ];
            }

            // Insert into quiz_questions
            // Note: 'topic_id' is legacy. We set it to 0 or derived if needed. 
            // Ideally we'd map it, but for now 0.
            
            $data = [
                'unique_code' => 'Q-' . time() . '-' . rand(100,999),
                'topic_id' => 0, // Legacy bypass
                'type' => $type,
                'content' => json_encode($content),
                'options' => json_encode($options),
                'answer_explanation' => $_POST['answer_explanation'] ?? '',
                'difficulty_level' => $_POST['difficulty_level'] ?? 1,
                'default_marks' => $_POST['default_marks'] ?? 1.0,
                'default_negative_marks' => $_POST['default_negative_marks'] ?? 0.0,
                'tags' => !empty($_POST['tags']) ? json_encode(explode(',', $_POST['tags'])) : null,
                'is_active' => isset($_POST['is_active']) ? 1 : 0,
                'created_by' => $_SESSION['user']['id'] ?? null
            ];
            
            // Insert Question
            if (!$this->db->insert('quiz_questions', $data)) {
                throw new Exception("Failed to insert question record");
            }
            $questionId = $this->db->lastInsertId();
            
            // Map to Syllabus (Question Stream Map)
            $node = $this->db->findOne('syllabus_nodes', ['id' => $nodeId]);
            $stream = $node['level'] ?? 'General'; // Default stream if logic allows, or fetch recursive
            
            // Insert into stream map
            $mapData = [
                'question_id' => $questionId,
                'stream' => $stream, // e.g., 'Level 5'
                'syllabus_node_id' => $nodeId,
                'difficulty_in_stream' => $_POST['difficulty_level'] ?? 3,
                'is_practical' => 0
            ];
            $this->db->insert('question_stream_map', $mapData);

            // SAVE SESSION MEMORY (Advanced Workspace Feature)
            $_SESSION['last_q_main_id'] = $_POST['syllabus_main_id'] ?? null;
            $_SESSION['last_q_sub_id'] = $nodeId;

            // Update Counts
            $this->syllabusService->recalculateQuestionCounts();

            $this->jsonResponse(['success' => true, 'message' => 'Question Added Successfully', 'redirect' => app_base_url('admin/quiz/questions')]);

        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Edit Form
     */
    public function edit($id)
    {
        $question = $this->db->findOne('quiz_questions', ['id' => $id]);
        if (!$question) {
             header('Location: ' . app_base_url('admin/quiz/questions'));
             exit;
        }

        // Decode JSON for view
        $question['content_decoded'] = json_decode($question['content'], true);
        $question['options_decoded'] = json_decode($question['options'] ?? '[]', true);
        $question['tags_decoded'] = json_decode($question['tags'] ?? '[]', true);
        
        // Need to fetch hierarchy for this question's topic to populate dropdowns
        $topic = $this->db->findOne('quiz_topics', ['id' => $question['topic_id']]);
        $subject = $this->db->findOne('quiz_subjects', ['id' => $topic['subject_id']]);
        
        $question['subject_id'] = $subject['id'];
        $question['category_id'] = $subject['category_id'];

        $categories = $this->db->find('quiz_categories', ['is_active' => 1]);

        $this->view->render('admin/quiz/questions/form', [
            'page_title' => 'Edit Question',
            'categories' => $categories,
            'question' => $question,
            'action' => app_base_url('admin/quiz/questions/update/' . $id)
        ]);
    }

    /**
     * Update Question
     */
    public function update($id)
    {
        try {
            if (empty($_POST['topic_id'])) throw new Exception("Topic is required");

            $type = $_POST['type'];
            
            // Build Content
            $content = [
                'text' => $_POST['question_text'],
                'image' => $_POST['question_image'] ?? null,
                'latex' => $_POST['question_latex'] ?? null
            ];

            // Build Options
            $options = [];
            if ($type !== 'text' && $type !== 'numerical') {
                 if (!empty($_POST['options']) && is_array($_POST['options'])) {
                     foreach ($_POST['options'] as $idx => $opt) {
                         $options[] = [
                             'id' => $idx + 1, 
                             'text' => $opt['text'] ?? '',
                             'image' => $opt['image'] ?? null,
                             'is_correct' => isset($opt['is_correct']) && $opt['is_correct'] == 1 ? true : false,
                             'order' => $idx
                         ];
                     }
                 }
            } else if ($type === 'numerical' && isset($_POST['numerical_answer'])) {
                $options[] = [
                    'id' => 1,
                    'text' => $_POST['numerical_answer'],
                    'is_correct' => true,
                    'tolerance' => $_POST['numerical_tolerance'] ?? 0
                ];
            }

            $data = [
                'topic_id' => $_POST['topic_id'],
                'type' => $type,
                'content' => json_encode($content),
                'options' => json_encode($options),
                'answer_explanation' => $_POST['answer_explanation'] ?? '',
                'difficulty_level' => $_POST['difficulty_level'] ?? 1,
                'default_marks' => $_POST['default_marks'] ?? 1.0,
                'default_negative_marks' => $_POST['default_negative_marks'] ?? 0.0,
                'tags' => !empty($_POST['tags']) ? json_encode(explode(',', $_POST['tags'])) : null,
                'is_active' => isset($_POST['is_active']) ? 1 : 0
            ];
            
            $this->db->update('quiz_questions', $data, "id = :id", ['id' => $id]);
            
            $this->jsonResponse(['success' => true, 'message' => 'Question Updated Successfully', 'redirect' => app_base_url('admin/quiz/questions')]);

        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'error' => $e->getMessage()]);
        }
    }
    
    public function delete($id) 
    {
        try {
            $this->db->delete('quiz_questions', "id = :id", ['id' => $id]);
            $this->syllabusService->recalculateQuestionCounts();
            $this->jsonResponse(['success' => true, 'message' => 'Question Deleted']);
        } catch (Exception $e) {
             $this->jsonResponse(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * AJAX Search for Exam Builder
     */
    public function searchJson()
    {
        $term = $_GET['q'] ?? '';
        $topicId = $_GET['topic_id'] ?? '';
        $type = $_GET['type'] ?? '';
        
        $where = ["q.is_active = 1"];
        $params = [];
        
        if (!empty($term)) {
            $where[] = "JSON_UNQUOTE(JSON_EXTRACT(q.content, '$.text')) LIKE :term";
            $params['term'] = '%' . $term . '%';
        }
        
        if (!empty($topicId)) {
            $where[] = "q.topic_id = :topic_id";
            $params['topic_id'] = $topicId;
        }
        
        if (!empty($type)) {
            $where[] = "q.type = :type";
            $params['type'] = $type;
        }
        
        $whereClause = "WHERE " . implode(" AND ", $where);
        
        $sql = "
            SELECT q.id, q.unique_code, q.type, q.difficulty_level, q.content, t.name as topic_name
            FROM quiz_questions q 
            LEFT JOIN quiz_topics t ON q.topic_id = t.id 
            $whereClause 
            ORDER BY q.id DESC 
            LIMIT 20
        ";
        
        $stmt = $this->db->getPdo()->prepare($sql);
        $stmt->execute($params);
        $questions = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        // Format for frontend
        foreach ($questions as &$q) {
            $c = json_decode($q['content'], true);
            $text = strip_tags($c['text'] ?? '');
            if (strlen($text) > 80) $text = substr($text, 0, 80) . '...';
            $q['text'] = $text;
            unset($q['content']); // remove heavyweight content
        }
        
        $this->jsonResponse($questions);
    }

    private function jsonResponse($data)
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
