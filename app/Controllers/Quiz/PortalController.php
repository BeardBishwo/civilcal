<?php

namespace App\Controllers\Quiz;

use App\Core\Controller;
use App\Core\Database;

class PortalController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Main Quiz Landing Page
     */
    public function index()
    {
        // 1. Fetch Categories (Streams) for filtering
        $sqlCats = "SELECT * FROM quiz_categories WHERE status = 1 ORDER BY `order` ASC";
        $categories = $this->db->query($sqlCats)->fetchAll();

        // 2. Fetch Featured/Latest Exams
        $sqlExams = "
            SELECT e.*, 
                   (SELECT COUNT(*) FROM quiz_exam_questions WHERE exam_id = e.id) as question_count
            FROM quiz_exams e 
            WHERE e.status = 'published' 
            ORDER BY e.created_at DESC 
            LIMIT 10
        ";
        $exams = $this->db->query($sqlExams)->fetchAll();

        // 3. User's Recent Activity (if logged in)
        $recentAttempts = [];
        $dailyBonus = null;

        if (isset($_SESSION['user_id'])) {
            // Trigger Daily Bonus
            $gs = new \App\Services\GamificationService();
            $dailyBonus = $gs->processDailyLoginBonus($_SESSION['user_id']);

            $sqlAttempts = "
                SELECT a.*, e.title as exam_title 
                FROM quiz_attempts a
                JOIN quiz_exams e ON a.exam_id = e.id
                WHERE a.user_id = :uid
                ORDER BY a.started_at DESC
                LIMIT 5
            ";
            $stmt = $this->db->getPdo()->prepare($sqlAttempts);
            $stmt->execute(['uid' => $_SESSION['user_id']]);
            $recentAttempts = $stmt->fetchAll();
        }

        $this->view('quiz/portal/index', [
            'categories' => $categories,
            'exams' => $exams,
            'recentAttempts' => $recentAttempts,
            'dailyBonus' => $dailyBonus,
            'title' => 'Quiz Portal | Bishwo Calculator'
        ]);

    }

    /**
     * Exam Details / Instruction Page before starting
     */
    public function overview($slug)
    {
        $stmt = $this->db->getPdo()->prepare("SELECT * FROM quiz_exams WHERE slug = :slug AND status = 'published'");
        $stmt->execute(['slug' => $slug]);
        $exam = $stmt->fetch();

        if (!$exam) {
            $this->redirect('/quiz'); // Or 404
        }

        $stmtQ = $this->db->getPdo()->prepare("SELECT COUNT(*) FROM quiz_exam_questions WHERE exam_id = :eid");
        $stmtQ->execute(['eid' => $exam['id']]);
        $qCount = $stmtQ->fetchColumn();

        $this->view('quiz/portal/overview', [
            'exam' => $exam,
            'question_count' => $qCount,
            'title' => $exam['title'] . ' - Overview'
        ]);
    }
}
