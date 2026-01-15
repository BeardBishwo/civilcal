<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\EmailThread;
use App\Services\FileService;
use App\Core\Auth;

class ReportController extends Controller
{
    private $emailThread;

    public function __construct()
    {
        parent::__construct();
        $this->emailThread = new EmailThread();
    }

    public function index()
    {
        $data = [
            'page_title' => 'Report an Issue',
            'meta_description' => 'Report bugs, incorrect calculations or structural concerns directly to our team.',
        ];

        $this->view->render('report', $data);
    }

    public function submit()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            return;
        }

        // Get and sanitize form data
        $name = sanitize_text_field($_POST['name'] ?? '');
        $email = sanitize_text_field($_POST['email'] ?? '');
        $link = sanitize_text_field($_POST['link'] ?? '');
        $subject = sanitize_text_field($_POST['subject'] ?? 'Bug Report');
        $message = sanitize_text_field($_POST['message'] ?? '');
        $priority = sanitize_text_field($_POST['priority'] ?? 'medium');

        // Validation
        $errors = [];
        if (empty($name)) $errors[] = 'Name is required';
        if (empty($link)) $errors[] = 'Calculator Link is required';
        if (empty($message)) $errors[] = 'Details are required';

        // Strictly validate link domain
        $siteUrl = parse_url(app_base_url(), PHP_URL_HOST);
        $linkUrl = parse_url($link, PHP_URL_HOST);

        if ($linkUrl && $linkUrl !== $siteUrl) {
            $errors[] = 'Only links from ' . $siteUrl . ' are allowed.';
        }

        if (empty($email)) {
            $errors[] = 'Email is required';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email address';
        }

        // Handle File Upload (Security Hardening via FileService)
        $screenshotPath = null;
        if (!empty($_FILES['screenshot']['name']) && $_FILES['screenshot']['error'] === UPLOAD_ERR_OK) {
            $userId = Auth::id() ?: 0;
            $upload = FileService::uploadUserFile($_FILES['screenshot'], $userId, 'report_screenshot');

            if ($upload['success']) {
                $screenshotPath = $upload['path'];
            } else {
                $errors[] = $upload['error'] ?? 'Screenshot upload failed';
            }
        }

        if (!empty($errors)) {
            echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
            return;
        }

        // Prepare thread data
        $threadData = [
            'user_id' => null,
            'from_email' => $email,
            'from_name' => $name,
            'subject' => $subject,
            'message' => $message,
            'category' => 'report',
            'priority' => $priority,
            'calculator_url' => $link,
            'screenshot_path' => $screenshotPath
        ];

        try {
            $success = $this->emailThread->create($threadData);

            if ($success) {
                $db = \App\Core\Database::getInstance();
                $threadId = $db->lastInsertId();

                // Add initial message
                $fullMessage = "Link: $link\n\n" . $message;
                $this->emailThread->addResponseToThread($threadId, null, $fullMessage, false);

                echo json_encode([
                    'success' => true,
                    'message' => 'Your report has been submitted. Thank you for helping us improve!',
                    'thread_id' => $threadId
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to submit report. Please try again.']);
            }
        } catch (\Exception $e) {
            error_log('Report submission error: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'An error occurred. Please try again later.']);
        }
    }
}
