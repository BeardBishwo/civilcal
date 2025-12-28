<?php

namespace App\Controllers\Admin\Quiz;

use App\Core\Controller;
use App\Core\Database;

class ResultsController extends Controller
{
    private $db;

    public function __construct()
    {
        parent::__construct();
        $this->requireAdmin();
        $this->db = new Database();
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

        $this->view('quiz/results/index', [
            'attempts' => $attempts,
            'total' => $total,
            'page' => $page,
            'limit' => $limit
        ]);
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
