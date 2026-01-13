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
     * Main Quiz Landing Page - Enhanced Shop-Like Dashboard
     */
    public function index()
    {
        $userStreamId = null;
        $activeStream = null;
        if (isset($_SESSION['user_id'])) {
            $user = $this->db->findOne('users', ['id' => $_SESSION['user_id']]);
            $userStreamId = $user['stream_id'] ?? null;
            if ($userStreamId) {
                $activeStream = $this->db->findOne('syllabus_nodes', ['id' => $userStreamId]);
            }
        }

        // 1. Fetch Categories (Courses) with exam counts
        $sqlCats = "
            SELECT c.id, c.title as name, c.slug, 
                   COUNT(e.id) as exam_count
            FROM syllabus_nodes c
            LEFT JOIN quiz_exams e ON e.course_id = c.id AND e.status = 'published'
            WHERE c.type = 'course' AND c.is_active = 1
        ";

        $params = [];
        if ($userStreamId) {
            $sqlCats .= " AND c.id = :stream_id ";
            $params['stream_id'] = $userStreamId;
        }

        $sqlCats .= " GROUP BY c.id HAVING exam_count > 0 ORDER BY c.order ASC";
        $categories = $this->db->query($sqlCats, $params)->fetchAll();

        // 2. Fetch ALL Published Exams with Stats
        $sqlExams = "
            SELECT e.*, 
                   (SELECT COUNT(*) FROM quiz_exam_questions WHERE exam_id = e.id) as question_count,
                   COUNT(DISTINCT a.id) as attempt_count,
                   AVG(a.accuracy) as avg_score,
                   c.title as category_name,
                   c.slug as category_slug
            FROM quiz_exams e 
            LEFT JOIN quiz_attempts a ON e.id = a.exam_id
            LEFT JOIN syllabus_nodes c ON e.course_id = c.id
            WHERE e.status = 'published' 
        ";

        // Filter exams if user has a stream preference
        if ($userStreamId) {
            $sqlExams .= " AND e.course_id = :stream_id ";
        }

        $sqlExams .= " GROUP BY e.id HAVING question_count > 0 ORDER BY e.created_at DESC";

        // Reuse params if stream_id set
        $allExams = $this->db->query($sqlExams, $params)->fetchAll();

        // 3. Calculate Badges for Each Exam
        $now = time();
        foreach ($allExams as &$exam) {
            $exam['badges'] = [];

            // NEW badge (created in last 7 days)
            $createdTime = strtotime($exam['created_at']);
            if (($now - $createdTime) < (7 * 86400)) {
                $exam['badges'][] = ['label' => 'NEW', 'color' => 'blue'];
            }

            // POPULAR badge (more than 10 attempts)
            if ($exam['attempt_count'] > 10) {
                $exam['badges'][] = ['label' => 'POPULAR', 'color' => 'red'];
            }

            // PREMIUM badge
            if ($exam['is_premium']) {
                $exam['badges'][] = ['label' => 'PREMIUM', 'color' => 'yellow'];
            }

            // EASY/HARD badge based on avg score
            if ($exam['avg_score'] !== null) {
                if ($exam['avg_score'] >= 75) {
                    $exam['badges'][] = ['label' => 'EASY', 'color' => 'green'];
                } elseif ($exam['avg_score'] < 50) {
                    $exam['badges'][] = ['label' => 'HARD', 'color' => 'purple'];
                }
            }
        }

        // 4. Group Exams by Category
        $examsByCategory = [];
        foreach ($allExams as $exam) {
            $catSlug = $exam['category_slug'] ?? 'uncategorized';
            if (!isset($examsByCategory[$catSlug])) {
                $examsByCategory[$catSlug] = [
                    'name' => $exam['category_name'] ?? 'Uncategorized',
                    'slug' => $catSlug,
                    'exams' => []
                ];
            }
            $examsByCategory[$catSlug]['exams'][] = $exam;
        }

        // 5. Featured/Editor's Choice (top 3 by attempts)
        $featured = array_slice($allExams, 0, 3);
        foreach ($featured as &$f) {
            $f['badges'][] = ['label' => 'FEATURED', 'color' => 'pink'];
        }

        // 6. User's Recent Activity (if logged in)
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
            'examsByCategory' => $examsByCategory,
            'examsByCategory' => $examsByCategory,
            'featured' => $featured,
            'allExams' => $allExams, // Keep for legacy if needed
            'exams' => $allExams,    // Alias matching the View
            'activeStream' => $activeStream, // Pass active stream for UI banner
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
