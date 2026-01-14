<?php

namespace App\Controllers\Admin\Quiz;

use App\Core\Controller;
use Exception;

class ExamController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->auth->check() || !$this->auth->isAdmin()) {
            header('Location: ' . app_base_url('login'));
            exit;
        }
    }

    public function index()
    {
        $page = $_GET['page'] ?? 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;

        // Build Query
        $params = [];
        $where = ["1=1"];

        if (!empty($_GET['status'])) {
            $where[] = "e.status = ?";
            $params[] = $_GET['status'];
        }
        if (!empty($_GET['course_id'])) {
            $where[] = "e.course_id = ?";
            $params[] = $_GET['course_id'];
        }
        if (!empty($_GET['education_level_id'])) {
            $where[] = "e.education_level_id = ?";
            $params[] = $_GET['education_level_id'];
        }
        if (!empty($_GET['position_level_id'])) {
            // Check existence in junction table
            $where[] = "EXISTS (SELECT 1 FROM exam_position_levels epl WHERE epl.exam_id = e.id AND epl.position_level_id = ?)";
            $params[] = $_GET['position_level_id'];
        }
        if (!empty($_GET['search'])) {
            $where[] = "e.title LIKE ?";
            $params[] = "%" . $_GET['search'] . "%";
        }

        $whereSql = implode(' AND ', $where);

        // Count
        $total = $this->db->query("SELECT COUNT(*) FROM quiz_exams e WHERE $whereSql", $params)->fetchColumn();

        // Fetch
        $exams = $this->db->query("SELECT e.* FROM quiz_exams e WHERE $whereSql ORDER BY e.id DESC LIMIT $limit OFFSET $offset", $params)->fetchAll();

        $this->view->render('admin/quiz/exams/index', [
            'page_title' => 'Exam Manager',
            'exams' => $exams,
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'courses' => $this->db->query("SELECT id, title FROM syllabus_nodes WHERE type = 'course' ORDER BY order_index ASC")->fetchAll(),
            'educationLevels' => $this->db->query("SELECT id, title FROM syllabus_nodes WHERE type = 'education_level' ORDER BY order_index ASC")->fetchAll(),
            'positionLevels' => $this->db->query("SELECT id, title FROM position_levels WHERE is_active = 1 ORDER BY order_index ASC")->fetchAll()
        ]);
    }

    public function create()
    {
        $this->view->render('admin/quiz/exams/form', [
            'page_title' => 'Create New Exam/Mock Test',
            'exam' => null,
            'courses' => $this->db->query("SELECT id, title FROM syllabus_nodes WHERE type = 'course' ORDER BY order_index ASC")->fetchAll(),
            'educationLevels' => $this->db->query("SELECT id, title, parent_id FROM syllabus_nodes WHERE type = 'education_level' ORDER BY order_index ASC")->fetchAll(),
            'positionLevels' => $this->db->query("SELECT id, title FROM position_levels WHERE is_active = 1 ORDER BY order_index ASC")->fetchAll(),
            'action' => app_base_url('admin/quiz/exams/store')
        ]);
    }

    public function store()
    {
        try {
            $title = $_POST['title'] ?? '';
            $slug = $_POST['slug'] ?? $this->slugify($title);

            if (empty($title)) throw new Exception("Title is required");

            $data = [
                'title' => $title,
                'slug' => $slug,
                'description' => $_POST['description'] ?? '',
                'type' => $_POST['type'] ?? 'practice',
                'mode' => $_POST['mode'] ?? 'practice',
                'duration_minutes' => (int)($_POST['duration_minutes'] ?? 0),
                'total_marks' => (int)($_POST['total_marks'] ?? 0),
                'pass_percentage' => (float)($_POST['pass_percentage'] ?? 40),
                'negative_marking_rate' => (float)($_POST['negative_marking_rate'] ?? 0),
                'shuffle_questions' => isset($_POST['shuffle_questions']) ? 1 : 0,
                'is_premium' => isset($_POST['is_premium']) ? 1 : 0,
                'price' => (float)($_POST['price'] ?? 0),
                'price' => (float)($_POST['price'] ?? 0),
                'status' => $_POST['status'] ?? 'draft',
                'course_id' => !empty($_POST['course_id']) ? $_POST['course_id'] : null,
                'education_level_id' => !empty($_POST['education_level_id']) ? $_POST['education_level_id'] : null
            ];

            if (!empty($_POST['start_datetime'])) $data['start_datetime'] = $_POST['start_datetime'];
            if (!empty($_POST['end_datetime'])) $data['end_datetime'] = $_POST['end_datetime'];

            $this->db->insert('quiz_exams', $data);
            $examId = $this->db->lastInsertId();

            // Sync Position Levels
            if (!empty($_POST['position_levels'])) {
                foreach ($_POST['position_levels'] as $plId) {
                    $this->db->insert('exam_position_levels', [
                        'exam_id' => $examId,
                        'position_level_id' => $plId
                    ]);
                }
            }

            $this->jsonResponse(['success' => true, 'message' => 'Exam Created', 'redirect' => app_base_url('admin/quiz/exams/builder/' . $examId)]);
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $exam = $this->db->findOne('quiz_exams', ['id' => $id]);
        if (!$exam) {
            header('Location: ' . app_base_url('admin/quiz/exams'));
            exit;
        }

        $this->view->render('admin/quiz/exams/form', [
            'page_title' => 'Edit Exam',
            'exam' => $exam,
            'courses' => $this->db->query("SELECT id, title FROM syllabus_nodes WHERE type = 'course' ORDER BY order_index ASC")->fetchAll(),
            'educationLevels' => $this->db->query("SELECT id, title, parent_id FROM syllabus_nodes WHERE type = 'education_level' ORDER BY order_index ASC")->fetchAll(),
            'positionLevels' => $this->db->query("SELECT id, title FROM position_levels WHERE is_active = 1 ORDER BY order_index ASC")->fetchAll(),
            'existingPositionLevels' => $this->db->query("SELECT position_level_id FROM exam_position_levels WHERE exam_id = ?", [$id])->fetchAll(7), // FETCH_COLUMN
            'action' => app_base_url('admin/quiz/exams/update/' . $id)
        ]);
    }

    public function update($id)
    {
        try {
            $title = $_POST['title'] ?? '';

            $data = [
                'title' => $title,
                'description' => $_POST['description'] ?? '',
                'type' => $_POST['type'] ?? 'practice',
                'mode' => $_POST['mode'] ?? 'practice',
                'duration_minutes' => (int)($_POST['duration_minutes'] ?? 0),
                'total_marks' => (int)($_POST['total_marks'] ?? 0),
                'pass_percentage' => (float)($_POST['pass_percentage'] ?? 40),
                'negative_marking_rate' => (float)($_POST['negative_marking_rate'] ?? 0),
                'shuffle_questions' => isset($_POST['shuffle_questions']) ? 1 : 0,
                'is_premium' => isset($_POST['is_premium']) ? 1 : 0,
                'price' => (float)($_POST['price'] ?? 0),
                'price' => (float)($_POST['price'] ?? 0),
                'status' => $_POST['status'] ?? 'draft',
                'course_id' => !empty($_POST['course_id']) ? $_POST['course_id'] : null,
                'education_level_id' => !empty($_POST['education_level_id']) ? $_POST['education_level_id'] : null
            ];

            if (!empty($_POST['slug'])) $data['slug'] = $_POST['slug'];
            if (!empty($_POST['start_datetime'])) $data['start_datetime'] = $_POST['start_datetime'];
            if (!empty($_POST['end_datetime'])) $data['end_datetime'] = $_POST['end_datetime'];

            $this->db->update('quiz_exams', $data, "id = :id", ['id' => $id]);

            // Sync Position Levels
            $this->db->delete('exam_position_levels', "exam_id = :id", ['id' => $id]);
            if (!empty($_POST['position_levels'])) {
                foreach ($_POST['position_levels'] as $plId) {
                    $this->db->insert('exam_position_levels', [
                        'exam_id' => $id,
                        'position_level_id' => $plId
                    ]);
                }
            }

            $this->jsonResponse(['success' => true, 'message' => 'Exam Updated', 'redirect' => app_base_url('admin/quiz/exams')]);
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Question Builder View for Exam
     */
    public function builder($id)
    {
        $exam = $this->db->findOne('quiz_exams', ['id' => $id]);
        if (!$exam) {
            header('Location: ' . app_base_url('admin/quiz/exams'));
            exit;
        }

        // Fetch existing questions in this exam
        $sql = "
            SELECT eq.*, q.question, q.type, q.difficulty_level 
            FROM quiz_exam_questions eq 
            JOIN quiz_questions q ON eq.question_id = q.id 
            WHERE eq.exam_id = :exam_id 
            ORDER BY eq.`order` ASC
        ";
        $stmt = $this->db->getPdo()->prepare($sql);
        $stmt->execute(['exam_id' => $id]);
        $questions = $stmt->fetchAll();


        // Decode JSON question slightly for summary preview
        foreach ($questions as &$q) {
            // The 'question' field might be JSON or plain text
            $questionText = $q['question'] ?? '';
            if (json_decode($questionText)) {
                $c = json_decode($questionText, true);
                $q['summary_text'] = substr(strip_tags($c['text'] ?? $questionText), 0, 100);
            } else {
                $q['summary_text'] = substr(strip_tags($questionText), 0, 100);
            }
        }

        $this->view->render('admin/quiz/exams/builder', [
            'page_title' => 'Exam Builder: ' . $exam['title'],
            'exam' => $exam,
            'existing_questions' => $questions
        ]);
    }

    /**
     * API to Add Question to Exam
     */
    public function addQuestionToExam()
    {
        try {
            $examId = $_POST['exam_id'];
            $questionId = $_POST['question_id'];

            // Check if already exists
            $exists = $this->db->count('quiz_exam_questions', ['exam_id' => $examId, 'question_id' => $questionId]);
            if ($exists) throw new Exception("Question already in this exam");

            // Get max order
            $stmt = $this->db->getPdo()->prepare("SELECT MAX(`order`) FROM quiz_exam_questions WHERE exam_id = ?");
            $stmt->execute([$examId]);
            $maxOrder = $stmt->fetchColumn() ?: 0;

            $this->db->insert('quiz_exam_questions', [
                'exam_id' => $examId,
                'question_id' => $questionId,
                'order' => $maxOrder + 1
            ]);

            $this->jsonResponse(['success' => true, 'message' => 'Added']);
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function removeQuestionFromExam()
    {
        try {
            $examId = $_POST['exam_id'];
            $questionId = $_POST['question_id'];

            $this->db->delete('quiz_exam_questions', "exam_id = :eid AND question_id = :qid", ['eid' => $examId, 'qid' => $questionId]);

            $this->jsonResponse(['success' => true, 'message' => 'Removed']);
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    // --- Helpers ---

    private function slugify($text)
    {
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        $text = preg_replace('~[^-\w]+~', '', $text);
        $text = trim($text, '-');
        $text = preg_replace('~-+~', '-', $text);
        $text = strtolower($text);
        if (empty($text)) return 'n-a';
        return $text;
    }

    private function jsonResponse($data)
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
