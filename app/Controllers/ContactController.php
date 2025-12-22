<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\EmailThread;

class ContactController extends Controller
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
            'page_title' => 'Contact Us',
            'meta_description' => 'Get in touch with us. We\'re here to help!',
        ];

        $this->view->render('contact', $data);
    }

    public function submit()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            return;
        }

        // Get form data
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $category = $_POST['category'] ?? 'general';
        $priority = $_POST['priority'] ?? 'medium';
        $subject = trim($_POST['subject'] ?? '');
        $message = trim($_POST['message'] ?? '');

        // Validation
        $errors = [];

        if (empty($name)) {
            $errors[] = 'Name is required';
        }

        if (empty($email)) {
            $errors[] = 'Email is required';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email address';
        }

        if (empty($subject)) {
            $errors[] = 'Subject is required';
        }

        if (empty($message)) {
            $errors[] = 'Message is required';
        }

        if (!empty($errors)) {
            echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
            return;
        }

        // Prepare thread data
        $threadData = [
            'user_id' => null, // Public contact form
            'from_email' => $email,
            'from_name' => $name,
            'subject' => $subject,
            'category' => $category,
            'priority' => $priority,
            'status' => 'new'
        ];

        // Create thread
        try {
            $success = $this->emailThread->create($threadData);

            if ($success) {
                // Get the last inserted ID
                $db = \App\Core\Database::getInstance();
                $threadId = $db->lastInsertId();
                
                // Add initial message with phone number if provided
                $fullMessage = $message;
                if (!empty($phone)) {
                    $fullMessage = "Phone: $phone\n\n" . $message;
                }

                $this->emailThread->addResponseToThread($threadId, null, $fullMessage, false);

                // Send auto-response email (optional)
                // TODO: Implement auto-response using EmailManager

                echo json_encode([
                    'success' => true,
                    'message' => 'Thank you for contacting us! We\'ll get back to you soon.',
                    'thread_id' => $threadId
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to submit contact form']);
            }
        } catch (\Exception $e) {
            error_log('Contact form error: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'An error occurred. Please try again later.']);
        }
    }
}
