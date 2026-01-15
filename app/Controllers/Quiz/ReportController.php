<?php

namespace App\Controllers\Quiz;

use App\Core\Controller;
use App\Core\Database;
use App\Services\FileService;
use App\Core\Auth;

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

        // Anti-Spam Cooldown (60 seconds)
        $lastReport = $db->query("
            SELECT created_at FROM question_reports 
            WHERE user_id = ? 
            ORDER BY created_at DESC LIMIT 1
        ", [$_SESSION['user_id']])->fetch();

        if ($lastReport) {
            $lastTime = strtotime($lastReport['created_at']);
            if (time() - $lastTime < 60) {
                http_response_code(429);
                echo json_encode(['error' => 'Slow down! You can only submit one report per minute.']);
                exit;
            }
        }

        // Handle Secure Screenshot Upload via FileService
        $screenshotPath = null;
        if (!empty($_FILES['screenshot']['name']) && $_FILES['screenshot']['error'] === UPLOAD_ERR_OK) {
            $upload = FileService::uploadUserFile($_FILES['screenshot'], Auth::id(), 'report_screenshot');

            if ($upload['success']) {
                $screenshotPath = $upload['path'];
            } else {
                http_response_code(400);
                echo json_encode(['error' => $upload['error'] ?? 'Screenshot upload failed']);
                exit;
            }
        }

        $db->insert('question_reports', [
            'user_id' => Auth::id(),
            'question_id' => $questionId,
            'issue_type' => $issueType,
            'description' => $description,
            'screenshot' => $screenshotPath, // NEW
            'status' => 'pending',
            'created_at' => date('Y-m-d H:i:s')
        ]);

        echo json_encode(['success' => true, 'message' => 'Report submitted successfully. Thank you for helping us improve!']);
    }
}
