<?php

namespace App\Controllers\Admin\Quiz;

use App\Core\Controller;
use Exception;

class QuestionBankController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->auth->check() || !$this->auth->isAdmin()) {
             header('Location: ' . app_base_url('login'));
             exit;
        }
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

        // Load Categories for filter dropdown
        $categories = $this->db->find('quiz_categories', ['is_active' => 1]);

        $this->view->render('admin/quiz/questions/index', [
            'page_title' => 'Question Bank',
            'questions' => $questions,
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'categories' => $categories
        ]);
    }

    /**
     * Show Create Form
     */
    public function create()
    {
        // Fetch categories for the specialized dropdown chain
        $categories = $this->db->find('quiz_categories', ['is_active' => 1]);
        
        $this->view->render('admin/quiz/questions/form', [
            'page_title' => 'Add New Question',
            'categories' => $categories,
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
            if (empty($_POST['topic_id'])) throw new Exception("Topic is required");
            if (empty($_POST['question_text'])) throw new Exception("Question text is required");
            if (empty($_POST['type'])) throw new Exception("Question type is required");

            $type = $_POST['type'];
            
            // Build Content JSON
            $content = [
                'text' => $_POST['question_text'],
                'image' => $_POST['question_image'] ?? null,
                'latex' => $_POST['question_latex'] ?? null
            ];

            // Build Options JSON
            $options = [];
            if ($type !== 'text' && $type !== 'numerical') { // Numerical might not need options in some cases, but sticking to logic
                 if (!empty($_POST['options']) && is_array($_POST['options'])) {
                     foreach ($_POST['options'] as $idx => $opt) {
                         $options[] = [
                             'id' => $idx + 1, // Simple ID generation
                             'text' => $opt['text'] ?? '',
                             'image' => $opt['image'] ?? null,
                             'is_correct' => isset($opt['is_correct']) && $opt['is_correct'] == 1 ? true : false,
                             'order' => $idx
                         ];
                     }
                 }
            } else if ($type === 'numerical' && isset($_POST['numerical_answer'])) {
                // For numerical, we might store the answer in options or a dedicated field. 
                // Let's store as a correct option for consistency in some schemas, 
                // OR just put it in content/metadata. New schema supports flexible JSON.
                // Let's store it as valid range in content for now, or single option.
                $options[] = [
                    'id' => 1,
                    'text' => $_POST['numerical_answer'],
                    'is_correct' => true,
                    'tolerance' => $_POST['numerical_tolerance'] ?? 0
                ];
            }

            // Insert
            $data = [
                'unique_code' => 'Q-' . time() . '-' . rand(100,999),
                'topic_id' => $_POST['topic_id'],
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
            
            $this->db->insert('quiz_questions', $data);
            
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
