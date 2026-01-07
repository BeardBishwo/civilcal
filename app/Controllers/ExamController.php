<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;

class ExamController extends Controller
{
    protected $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = Database::getInstance();
    }

    /**
     * Exam Hub: List all categories (syllabus nodes)
     */
    public function index()
    {
        // Get categories that have questions
        // Get categories that have questions AND are fully active (hierarchy check)
        // Ancestry: Category -> Education Level -> Course
        $categories = $this->db->query("
            SELECT s.*, count(q.id) as question_count 
            FROM syllabus_nodes s
            JOIN syllabus_nodes level ON s.parent_id = level.id
            JOIN syllabus_nodes course ON level.parent_id = course.id
            JOIN quiz_questions q ON s.id = q.category_id
            WHERE q.is_active = 1
            AND s.is_active = 1
            AND level.is_active = 1
            AND course.is_active = 1
            GROUP BY s.id
            ORDER BY s.title
        ")->fetchAll();

        $this->view('exams/index', [
            'categories' => $categories,
            'page_title' => 'Exam Hub'
        ]);
    }

    /**
     * Category Overview: Choose Mode (Practice vs Mock)
     */
    public function category($slug)
    {
        // Fetch Category with Hierarchy Validation
        // We verify that the Category itself, its Level (parent), and Course (grandparent) are ALL active.
        $category = $this->db->query("
            SELECT c.* 
            FROM syllabus_nodes c
            JOIN syllabus_nodes level ON c.parent_id = level.id
            JOIN syllabus_nodes course ON level.parent_id = course.id
            WHERE c.slug = :slug
            AND c.is_active = 1
            AND level.is_active = 1
            AND course.is_active = 1
        ", ['slug' => $slug])->fetch();

        if (!$category) {
            http_response_code(404);
            $this->view('errors/404', ['message' => 'Category not found or currently unavailable.']); // Better UX
            return;
        }

        $stats = $this->db->query("
            SELECT 
                count(*) as total,
                SUM(CASE WHEN type = 'MCQ' THEN 1 ELSE 0 END) as mcq_count,
                SUM(CASE WHEN type = 'THEORY' THEN 1 ELSE 0 END) as theory_count
            FROM quiz_questions 
            WHERE category_id = :id AND is_active = 1
        ", ['id' => $category['id']])->fetch();

        $this->view('exams/category', [
            'category' => $category,
            'stats' => $stats,
            'page_title' => $category['title'] . ' - Exam Prep'
        ]);
    }

    /**
     * Start an Exam Session
     */
    public function start()
    {
        $categoryId = $_POST['category_id'];
        $mode = $_POST['mode']; // 'practice' or 'mock'
        
        $limit = ($mode === 'mock') ? 50 : 20; // Default limits

        // Create session
        $data = [
            'user_id' => current_user()['id'] ?? null,
            'category_id' => $categoryId,
            'mode' => $mode,
            'total_questions' => $limit,
            'status' => 'started',
            'started_at' => date('Y-m-d H:i:s')
        ];

        $sessionId = $this->db->insert('exam_sessions', $data);

        // For Mock mode, we might want to pre-select questions? 
        // For now, we'll select them dynamically in take() or store them in a temp table?
        // Simpler: Just redirect to take() and fetch random questions there. 
        // Note: Ideally we store the question IDs for this session to ensure consistency.

        echo json_encode(['success' => true, 'redirect' => app_base_url('exams/take/' . $sessionId)]);
    }

    /**
     * The Exam Interface
     */
    public function take($sessionId)
    {
        $session = $this->db->findOne('exam_sessions', ['id' => $sessionId]);
        if (!$session || $session['status'] === 'completed') {
            redirect('exams/result/' . $sessionId);
            return;
        }

        $category = $this->db->findOne('syllabus_nodes', ['id' => $session['category_id']]);

        // Fetch Questions (Random for now)
        // In a real app, we should check if we already picked questions for this session.
        // Implementation: We will select questions and just send them to the frontend.
        // If the user reloads, we ideally want the SAME questions.
        // Optimization: Store question_ids in a column in exam_sessions JSON?
        // For MVP: We will fetch random questions every load (warning: reload changes questions).
        
        $limit = $session['total_questions'];
        $questions = $this->db->query("
            SELECT id, content, type, theory_type, default_marks 
            FROM quiz_questions 
            WHERE category_id = :cat_id AND is_active = 1
            ORDER BY RAND()
            LIMIT $limit
        ", ['cat_id' => $session['category_id']])->fetchAll();

        // Prepare questions (hide correct answers)
        $cleanQuestions = [];
        foreach ($questions as $q) {
            $content = json_decode($q['content'], true);
            $cleanQuestions[] = [
                'id' => $q['id'],
                'text' => $content['text'],
                'options' => $content['options'] ?? [],
                'type' => $q['type'],
                // DO NOT SEND CORRECT ANSWER
            ];
        }

        $this->view('exams/take', [
            'session' => $session,
            'category' => $category,
            'questions' => $cleanQuestions,
            'page_title' => 'Taking Exam'
        ]);
    }

    /**
     * Submit Answer (AJAX) - Used for Practice Mode feedback or autosave
     */
    public function checkAnswer()
    {
        $questionId = $_POST['question_id'];
        $userAnswer = $_POST['answer']; // Index or Text
        
        $question = $this->db->findOne('quiz_questions', ['id' => $questionId]);
        $content = json_decode($question['content'], true);
        
        $correctAnswer = $content['correct']; // Index usually
        $isCorrect = ((string)$userAnswer === (string)$correctAnswer) ? 1 : 0;

        echo json_encode([
            'correct' => $isCorrect,
            'correct_answer' => $correctAnswer,
            'explanation' => $question['answer_explanation']
        ]);
    }

    /**
     * Submit Exam (Finish)
     */
    public function submit()
    {
        $sessionId = $_POST['session_id'];
        $answers = json_decode($_POST['answers'], true); // Array of {q_id, answer}
        
        $score = 0;
        
        if ($answers) {
            foreach ($answers as $ans) {
                $q = $this->db->findOne('quiz_questions', ['id' => $ans['question_id']]);
                if ($q) {
                    $content = json_decode($q['content'], true);
                    $correct = $content['correct'] ?? null;
                    
                    $isCorrect = ((string)$ans['answer'] === (string)$correct) ? 1 : 0;
                    if ($isCorrect) $score++;

                    // Save answer
                    $this->db->insert('exam_answers', [
                        'session_id' => $sessionId,
                        'question_id' => $q['id'],
                        'user_answer' => $ans['answer'],
                        'is_correct' => $isCorrect
                    ]);
                }
            }
        }

        $this->db->update('exam_sessions', [
            'status' => 'completed',
            'score' => $score,
            'completed_at' => date('Y-m-d H:i:s')
        ], ['id' => $sessionId]);

        echo json_encode(['success' => true, 'redirect' => app_base_url('exams/result/' . $sessionId)]);
    }

    /**
     * Exam Result
     */
    public function result($sessionId)
    {
        $session = $this->db->findOne('exam_sessions', ['id' => $sessionId]);
         if (!$session) {
            http_response_code(404);
            echo "Session not found";
            return;
        }

        $answers = $this->db->query("
            SELECT ea.*, q.content, q.answer_explanation 
            FROM exam_answers ea
            JOIN quiz_questions q ON ea.question_id = q.id
            WHERE ea.session_id = :sid
        ", ['sid' => $sessionId])->fetchAll();

        $this->view('exams/result', [
            'session' => $session,
            'answers' => $answers,
            'page_title' => 'Exam Results'
        ]);
    }
}
