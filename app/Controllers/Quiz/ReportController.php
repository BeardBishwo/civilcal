<?php

namespace App\Controllers\Quiz;

use App\Core\Controller;
use App\Core\Database;
use App\Services\Security;

class ReportController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->requireAuth();
    }

    /**
     * POST: Submit a question report
     */
    public function submit()
    {
        $questionId = $_POST['question_id'] ?? null;
        $issueType = $_POST['issue_type'] ?? 'other';
        $description = $_POST['description'] ?? '';

        if (!$questionId) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing question ID']);
            exit;
        }

        $db = Database::getInstance();
        $db->insert('question_reports', [
            'user_id' => $_SESSION['user_id'],
            'question_id' => $questionId,
            'issue_type' => $issueType,
            'description' => $description,
            'status' => 'pending',
            'created_at' => date('Y-m-d H:i:s')
        ]);

        echo json_encode(['success' => true, 'message' => 'Report submitted successfully. Thank you for helping us improve!']);
    }
}
