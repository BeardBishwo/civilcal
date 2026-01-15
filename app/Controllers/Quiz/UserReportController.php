<?php

namespace App\Controllers\Quiz;

use App\Core\Controller;
use App\Core\Database;

class UserReportController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->requireAuth();
    }

    /**
     * GET: Show user's report history
     */
    public function index()
    {
        $db = Database::getInstance();

        $reports = $db->query("
            SELECT r.*, q.question as q_json, q.type as q_type
            FROM question_reports r
            LEFT JOIN quiz_questions q ON r.question_id = q.id
            WHERE r.user_id = ?
            ORDER BY r.created_at DESC
        ", [$_SESSION['user_id']])->fetchAll();

        // Format data for view
        foreach ($reports as &$r) {
            $content = json_decode($r['q_json'] ?? '{}', true);
            $r['question_text'] = $content['text'] ?? '[Deleted Question]';
        }

        // Leaderboard (Phase 10)
        $leaderboard = $db->query("
            SELECT u.username, 
                   COUNT(r.id) as total_reports,
                   SUM(CASE WHEN r.status = 'resolved' THEN 1 ELSE 0 END) as resolved_count
            FROM question_reports r
            JOIN users u ON r.user_id = u.id
            WHERE r.status != 'pending'
            GROUP BY r.user_id
            HAVING resolved_count > 0
            ORDER BY resolved_count DESC, total_reports ASC
            LIMIT 5
        ")->fetchAll();

        $this->view->render('quiz/user/reports', [
            'page_title' => 'My Reported Issues',
            'reports' => $reports,
            'leaderboard' => $leaderboard
        ]);
    }
}
