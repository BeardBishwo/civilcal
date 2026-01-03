<?php

namespace App\Controllers\Admin\Quiz;

use App\Core\Controller;
use App\Core\Database;

class ResultsController extends Controller
{
    protected $db;

    public function __construct()
    {
        parent::__construct();
        $this->requireAdmin();
    }

    public function index()
    {
        $page = $_GET['page'] ?? 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;
        
        $search = $_GET['search'] ?? '';

        $where = "attempt.status IS NOT NULL";
        $params = [];

        if (!empty($search)) {
            $where .= " AND (u.email LIKE :search OR u.username LIKE :search OR e.title LIKE :search)";
            $params['search'] = "%$search%";
        }

        // Fetch Attempts
        $sql = "
            SELECT attempt.*, 
                   u.username, u.email,
                   e.title as exam_title
            FROM quiz_attempts attempt
            JOIN users u ON attempt.user_id = u.id
            JOIN quiz_exams e ON attempt.exam_id = e.id
            WHERE $where
            ORDER BY attempt.started_at DESC
            LIMIT $limit OFFSET $offset
        ";

        $stmt = $this->db->getPdo()->prepare($sql);
        $stmt->execute($params);
        $attempts = $stmt->fetchAll();

        // Count Total
        $countSql = "
            SELECT COUNT(*) 
            FROM quiz_attempts attempt
            JOIN users u ON attempt.user_id = u.id
            JOIN quiz_exams e ON attempt.exam_id = e.id
            WHERE $where
        ";
        $countStmt = $this->db->getPdo()->prepare($countSql);
        $countStmt->execute($params);
        $total = $countStmt->fetchColumn();

        // Advanced Analytics Stats
        $stats = [
            'total_attempts' => $total,
            'completed_attempts' => 0,
            'avg_score' => 0,
            'pass_rate' => 0,
            'highest_score' => 0
        ];

        if ($total > 0) {
            // Calculate Completed
            $completedSql = "SELECT COUNT(*) FROM quiz_attempts WHERE status = 'completed'";
            $stats['completed_attempts'] = $this->db->getPdo()->query($completedSql)->fetchColumn();

            // Calculate Avg Score & Highest
            $scoreSql = "SELECT AVG(score) as avg_score, MAX(score) as max_score FROM quiz_attempts WHERE status = 'completed'";
            $scoreRow = $this->db->getPdo()->query($scoreSql)->fetch();
            $stats['avg_score'] = round($scoreRow['avg_score'] ?? 0, 2);
            $stats['highest_score'] = round($scoreRow['max_score'] ?? 0, 2);

            // Calculate Pass Rate (Assume > 40% is pass for now unless exam has specific pass_mark)
            // Ideally we check against exam.pass_percentage, but for global stats we can just use completed ratio or generic
            $stats['pass_rate'] = round(($stats['completed_attempts'] / $total) * 100, 1);
        }

        // Top Performers (Best Avg Score)
        $topPerformersSql = "
            SELECT u.username, u.avatar, AVG(qa.score) as avg_score, COUNT(qa.id) as attempts
            FROM quiz_attempts qa
            JOIN users u ON qa.user_id = u.id
            WHERE qa.status = 'completed'
            GROUP BY u.id
            ORDER BY avg_score DESC
            LIMIT 5
        ";
        $topPerformers = $this->db->getPdo()->query($topPerformersSql)->fetchAll();

        // Render with Admin Layout
        if (isset($this->view)) {
            $this->view->render('admin/quiz/results/index', [
                'page_title' => 'Results & Analytics',
                'attempts' => $attempts,
                'total' => $total,
                'page' => $page,
                'limit' => $limit,
                'stats' => $stats,
                'top_performers' => $topPerformers
            ]);
        } else {
           // Fallback if View service is missing (unlikely in Admin)
           extract([
                'attempts' => $attempts,
                'total' => $total,
                'page' => $page,
                'limit' => $limit,
                'stats' => $stats,
                'top_performers' => $topPerformers
           ]);
           require BASE_PATH . '/themes/admin/views/quiz/results/index.php';
        }
    }

    /**
     * View specific attempt details (Question wise analysis)
     * For future enterprise expansion
     */
    public function show($id)
    {
        // Implementation for detailed result view
    }
}
