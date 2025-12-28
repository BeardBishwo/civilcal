<?php

namespace App\Controllers\Admin\Quiz;

use App\Core\Controller;

class QuizDashboardController extends Controller
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
        // Gather key metrics
        
        // 1. Total Questions
        $totalQuestions = $this->db->count('quiz_questions');
        
        // 2. Active Exams
        $activeExams = $this->db->count('quiz_exams', ['status' => 'published']);
        
        // 3. User Attempts (All time)
        $totalAttempts = $this->db->count('quiz_attempts');
        
        // 4. Recent Attempts 
        $sql = "
            SELECT qa.*, u.email, qe.title as exam_title 
            FROM quiz_attempts qa
            JOIN users u ON qa.user_id = u.id 
            JOIN quiz_exams qe ON qa.exam_id = qe.id
            ORDER BY qa.started_at DESC LIMIT 5
        ";
        $stmt = $this->db->getPdo()->prepare($sql);
        $stmt->execute();
        $recentAttempts = $stmt->fetchAll();

        $this->view->render('admin/quiz/dashboard', [
            'page_title' => 'Quiz System Dashboard',
            'stats' => [
                'total_questions' => $totalQuestions,
                'active_exams' => $activeExams,
                'total_attempts' => $totalAttempts
            ],
            'recent_attempts' => $recentAttempts
        ]);
    }
}
