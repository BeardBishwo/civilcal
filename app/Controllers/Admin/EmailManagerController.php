<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\EmailThread;
use App\Models\EmailTemplate;
use App\Services\EmailService;
use App\Models\User;

require_once __DIR__ . '/../../Services/EmailManager.php';

class EmailManagerController extends Controller
{
    private $emailThread;
    private $emailTemplate;
    private $emailService;

    public function __construct()
    {
        parent::__construct();
        $this->emailThread = new EmailThread();
        $this->emailTemplate = new EmailTemplate();
        $this->emailService = new EmailService();
    }

    public function index()
    {
        return $this->dashboard();
    }

    public function dashboard()
    {
        $stats = $this->emailThread->getStatistics();
        $recentThreads = $this->emailThread->getRecentThreads(5);
        $templateStats = $this->emailTemplate->getStats();

        $data = [
            'stats' => $stats,
            'recentThreads' => $recentThreads,
            'templateStats' => $templateStats,
            'page_title' => 'Email Manager Dashboard',
            'breadcrumbs' => [
                ['title' => 'Dashboard', 'url' => app_base_url('/admin')],
                ['title' => 'Email Manager', 'url' => app_base_url('/admin/email-manager')]
            ]
        ];
        
        // Use the View class's render method to properly use themes/admin layout
        $this->view->render('admin/email-manager/dashboard', $data);
    }

    public function sendTestEmail()
    {
        header('Content-Type: application/json');
        
        try {
            // Get test email from JSON body or POST data
            $input = json_decode(file_get_contents('php://input'), true);
            if (!$input) {
                $input = $_POST;
            }
            
            $testEmail = $input['test_email'] ?? '';
            
            if (empty($testEmail)) {
                echo json_encode(['success' => false, 'message' => 'Test email address is required']);
                return;
            }

            $emailManager = new \EmailManager();
            $result = $emailManager->testEmailSettings($testEmail);

            echo json_encode($result);
        } catch (\Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function saveTemplate()
    {
        echo "Template Saved";
    }

    public function stats()
    {
        // Get email statistics
        $stats = $this->emailThread->getStatistics();

        // Calculate additional metrics
        $highPriority = $this->emailThread->getThreadCountByPriority('high');
        $urgent = $this->emailThread->getThreadCountByPriority('urgent');

        $data = [
            'total_threads' => $stats['total'] ?? 0,
            'unread_threads' => $stats['new_count'] ?? 0,
            'resolved_threads' => $stats['resolved_count'] ?? 0,
            'high_priority' => ($highPriority + $urgent),
            'in_progress' => $stats['in_progress_count'] ?? 0
        ];

        // Return JSON for AJAX
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    public function threads()
    {
        $filters = [];

        // Get filter parameters from request
        if (isset($_GET['status']) && $_GET['status'] !== 'all') {
            $filters['status'] = $_GET['status'];
        }

        if (isset($_GET['category']) && $_GET['category'] !== 'all') {
            $filters['category'] = $_GET['category'];
        }

        if (isset($_GET['priority']) && $_GET['priority'] !== 'all') {
            $filters['priority'] = $_GET['priority'];
        }

        if (isset($_GET['search']) && !empty($_GET['search'])) {
            $filters['search'] = $_GET['search'];
        }

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;

        $threads = $this->emailThread->getThreadsWithFilters($filters, $page, $limit);
        $totalCount = $this->emailThread->getThreadCountWithFilters($filters);

        // Return JSON for AJAX requests
        if ($this->isAjaxRequest()) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'threads' => $threads,
                'total' => $totalCount,
                'page' => $page,
                'limit' => $limit,
                'total_pages' => ceil($totalCount / $limit)
            ]);
            exit;
        }

        // Return view for regular requests
        $data = [
            'threads' => $threads,
            'total' => $totalCount,
            'page' => $page,
            'limit' => $limit,
            'total_pages' => ceil($totalCount / $limit),
            'filters' => $filters,
            'page_title' => 'Email Threads',
            'breadcrumbs' => [
                ['title' => 'Dashboard', 'url' => app_base_url('/admin')],
                ['title' => 'Email Manager', 'url' => app_base_url('/admin/email-manager')],
                ['title' => 'Threads', 'url' => app_base_url('/admin/email-manager/threads')]
            ]
        ];
        
        // Use the View class's render method to properly use themes/admin layout
        $this->view->render('admin/email-manager/threads', $data);
    }

    private function isAjaxRequest()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    public function viewThread($id)
    {
        $thread = $this->emailThread->getThreadById($id);

        if (!$thread) {
            $data = [
                'message' => 'Thread not found',
                'page_title' => 'Error',
                'breadcrumbs' => [
                    ['title' => 'Dashboard', 'url' => app_base_url('/admin')],
                    ['title' => 'Email Manager', 'url' => app_base_url('/admin/email-manager')],
                    ['title' => 'Error', 'url' => '']
                ]
            ];
            
            // Use the View class's render method to properly use themes/admin layout
            $this->view->render('admin/email-manager/error', $data);
            return;
        }

        $availableAssignees = $this->emailThread->getAvailableAssignees();
        $templates = $this->emailTemplate->getActiveTemplates();

        $data = [
            'thread' => $thread,
            'availableAssignees' => $availableAssignees,
            'templates' => $templates,
            'page_title' => 'Thread: ' . $thread['subject'],
            'breadcrumbs' => [
                ['title' => 'Dashboard', 'url' => app_base_url('/admin')],
                ['title' => 'Email Manager', 'url' => app_base_url('/admin/email-manager')],
                ['title' => 'Threads', 'url' => app_base_url('/admin/email-manager/threads')],
                ['title' => 'View Thread', 'url' => '']
            ]
        ];
        
        // Use the View class's render method to properly use themes/admin layout
        $this->view->render('admin/email-manager/thread-detail', $data);
    }

    public function reply($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->jsonResponse(['error' => 'Method not allowed'], 405);
        }

        $thread = $this->emailThread->getThreadById($id);
        if (!$thread) {
            return $this->jsonResponse(['error' => 'Thread not found'], 404);
        }

        $message = $_POST['message'] ?? '';
        $isInternal = isset($_POST['is_internal']) && $_POST['is_internal'] === '1';
        $userId = $_SESSION['user']['id'] ?? null;

        if (empty($message)) {
            return $this->jsonResponse(['error' => 'Message is required'], 400);
        }

        $success = $this->emailThread->addResponseToThread($id, $userId, $message, $isInternal);

        if ($success) {
            return $this->jsonResponse([
                'success' => true,
                'message' => 'Reply added successfully'
            ]);
        } else {
            return $this->jsonResponse(['error' => 'Failed to add reply'], 500);
        }
    }

    private function jsonResponse($data, $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    public function updateStatus($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->jsonResponse(['error' => 'Method not allowed'], 405);
        }

        $thread = $this->emailThread->getThreadById($id);
        if (!$thread) {
            return $this->jsonResponse(['error' => 'Thread not found'], 404);
        }

        $status = $_POST['status'] ?? '';
        $validStatuses = ['new', 'in_progress', 'resolved', 'closed'];

        if (!in_array($status, $validStatuses)) {
            return $this->jsonResponse(['error' => 'Invalid status'], 400);
        }

        $success = $this->emailThread->updateThread($id, ['status' => $status]);

        if ($success) {
            return $this->jsonResponse([
                'success' => true,
                'message' => 'Status updated successfully'
            ]);
        } else {
            return $this->jsonResponse(['error' => 'Failed to update status'], 500);
        }
    }

    public function assign($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->jsonResponse(['error' => 'Method not allowed'], 405);
        }

        $thread = $this->emailThread->getThreadById($id);
        if (!$thread) {
            return $this->jsonResponse(['error' => 'Thread not found'], 404);
        }

        $assignedTo = $_POST['assigned_to'] ?? null;

        // Validate that the assignee exists
        if ($assignedTo !== null) {
            $userModel = new User();
            $assignee = $userModel->find($assignedTo);
            if (!$assignee) {
                return $this->jsonResponse(['error' => 'Assignee not found'], 400);
            }
        }

        $success = $this->emailThread->updateThread($id, ['assigned_to' => $assignedTo]);

        if ($success) {
            return $this->jsonResponse([
                'success' => true,
                'message' => 'Thread assigned successfully'
            ]);
        } else {
            return $this->jsonResponse(['error' => 'Failed to assign thread'], 500);
        }
    }

    public function updatePriority($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->jsonResponse(['error' => 'Method not allowed'], 405);
        }

        $thread = $this->emailThread->getThreadById($id);
        if (!$thread) {
            return $this->jsonResponse(['error' => 'Thread not found'], 404);
        }

        $priority = $_POST['priority'] ?? '';
        $validPriorities = ['low', 'medium', 'high', 'urgent'];

        if (!in_array($priority, $validPriorities)) {
            return $this->jsonResponse(['error' => 'Invalid priority'], 400);
        }

        $success = $this->emailThread->updateThread($id, ['priority' => $priority]);

        if ($success) {
            return $this->jsonResponse([
                'success' => true,
                'message' => 'Priority updated successfully'
            ]);
        } else {
            return $this->jsonResponse(['error' => 'Failed to update priority'], 500);
        }
    }

    public function templates()
    {
        $filters = [];

        if (isset($_GET['category']) && $_GET['category'] !== 'all') {
            $filters['category'] = $_GET['category'];
        }

        if (isset($_GET['is_active'])) {
            $filters['is_active'] = (bool)$_GET['is_active'];
        }

        if (isset($_GET['search']) && !empty($_GET['search'])) {
            $filters['search'] = $_GET['search'];
        }

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = 20;

        $templatesData = $this->emailTemplate->getAll($filters, $page, $perPage);

        // Return JSON for AJAX requests
        if ($this->isAjaxRequest()) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'templates' => $templatesData['templates'],
                'total' => $templatesData['total'],
                'page' => $page,
                'per_page' => $perPage,
                'total_pages' => $templatesData['total_pages']
            ]);
            exit;
        }

        // Return view for regular requests
        $data = [
            'templates' => $templatesData['templates'],
            'total' => $templatesData['total'],
            'page' => $page,
            'per_page' => $perPage,
            'total_pages' => $templatesData['total_pages'],
            'filters' => $filters,
            'templateTypes' => $this->emailTemplate->getTemplateTypes(),
            'page_title' => 'Email Templates',
            'breadcrumbs' => [
                ['title' => 'Dashboard', 'url' => app_base_url('/admin')],
                ['title' => 'Email Manager', 'url' => app_base_url('/admin/email-manager')],
                ['title' => 'Templates', 'url' => app_base_url('/admin/email-manager/templates')]
            ]
        ];
        
        // Use the View class's render method to properly use themes/admin layout
        $this->view->render('admin/email-manager/templates', $data);
    }

    public function createTemplate()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => $_POST['name'] ?? '',
                'subject' => $_POST['subject'] ?? '',
                'content' => $_POST['content'] ?? '',
                'category' => $_POST['category'] ?? 'general',
                'description' => $_POST['description'] ?? '',
                'is_active' => isset($_POST['is_active']),
                'created_by' => $_SESSION['user']['id'] ?? null,
                'variables' => $_POST['variables'] ?? []
            ];

            $validation = $this->emailTemplate->validate($data);

            if (!$validation['valid']) {
                return $this->jsonResponse([
                    'success' => false,
                    'errors' => $validation['errors']
                ], 400);
            }

            $templateId = $this->emailTemplate->create($data);

            if ($templateId) {
                return $this->jsonResponse([
                    'success' => true,
                    'message' => 'Template created successfully',
                    'template_id' => $templateId
                ]);
            } else {
                return $this->jsonResponse([
                    'success' => false,
                    'error' => 'Failed to create template'
                ], 500);
            }
        }

        // Return form for GET requests
        $data = [
            'template' => null,
            'templateTypes' => $this->emailTemplate->getTemplateTypes(),
            'page_title' => 'Create Email Template',
            'breadcrumbs' => [
                ['title' => 'Dashboard', 'url' => app_base_url('/admin')],
                ['title' => 'Email Manager', 'url' => app_base_url('/admin/email-manager')],
                ['title' => 'Templates', 'url' => app_base_url('/admin/email-manager/templates')],
                ['title' => 'Create Template', 'url' => '']
            ]
        ];
        
        // Use the View class's render method to properly use themes/admin layout
        $this->view->render('admin/email-manager/template-form', $data);
    }

    public function editTemplate($id)
    {
        $template = $this->emailTemplate->getTemplateById($id);

        if (!$template) {
            $data = [
                'message' => 'Template not found',
                'page_title' => 'Error',
                'breadcrumbs' => [
                    ['title' => 'Dashboard', 'url' => app_base_url('/admin')],
                    ['title' => 'Email Manager', 'url' => app_base_url('/admin/email-manager')],
                    ['title' => 'Error', 'url' => '']
                ]
            ];
            
            // Use the View class's render method to properly use themes/admin layout
            $this->view->render('admin/email-manager/error', $data);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => $_POST['name'] ?? '',
                'subject' => $_POST['subject'] ?? '',
                'content' => $_POST['content'] ?? '',
                'category' => $_POST['category'] ?? 'general',
                'description' => $_POST['description'] ?? '',
                'is_active' => isset($_POST['is_active']),
                'variables' => $_POST['variables'] ?? []
            ];

            $validation = $this->emailTemplate->validate($data);

            if (!$validation['valid']) {
                return $this->jsonResponse([
                    'success' => false,
                    'errors' => $validation['errors']
                ], 400);
            }

            $success = $this->emailTemplate->update($id, $data);

            if ($success) {
                return $this->jsonResponse([
                    'success' => true,
                    'message' => 'Template updated successfully'
                ]);
            } else {
                return $this->jsonResponse([
                    'success' => false,
                    'error' => 'Failed to update template'
                ], 500);
            }
        }

        // Return form for GET requests
        $data = [
            'template' => $template,
            'templateTypes' => $this->emailTemplate->getTemplateTypes(),
            'page_title' => 'Edit Email Template',
            'breadcrumbs' => [
                ['title' => 'Dashboard', 'url' => app_base_url('/admin')],
                ['title' => 'Email Manager', 'url' => app_base_url('/admin/email-manager')],
                ['title' => 'Templates', 'url' => app_base_url('/admin/email-manager/templates')],
                ['title' => 'Edit Template', 'url' => '']
            ]
        ];
        
        // Use the View class's render method to properly use themes/admin layout
        $this->view->render('admin/email-manager/template-form', $data);
    }

    public function updateTemplate($id)
    {
        // Accept POST method for form submissions
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->jsonResponse(['error' => 'Method not allowed'], 405);
        }

        $template = $this->emailTemplate->getTemplateById($id);
        if (!$template) {
            return $this->jsonResponse(['error' => 'Template not found'], 404);
        }

        $data = [
            'name' => $_POST['name'] ?? '',
            'subject' => $_POST['subject'] ?? '',
            'content' => $_POST['content'] ?? '',
            'category' => $_POST['category'] ?? 'general',
            'description' => $_POST['description'] ?? '',
            'is_active' => isset($_POST['is_active']),
            'variables' => $_POST['variables'] ?? []
        ];

        $validation = $this->emailTemplate->validate($data);

        if (!$validation['valid']) {
            return $this->jsonResponse([
                'success' => false,
                'errors' => $validation['errors']
            ], 400);
        }

        $success = $this->emailTemplate->update($id, $data);

        if ($success) {
            return $this->jsonResponse([
                'success' => true,
                'message' => 'Template updated successfully'
            ]);
        } else {
            return $this->jsonResponse([
                'success' => false,
                'error' => 'Failed to update template'
            ], 500);
        }
    }

    public function deleteTemplate($id)
    {
        // Accept POST method for AJAX delete requests
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->jsonResponse(['error' => 'Method not allowed'], 405);
        }

        $template = $this->emailTemplate->getTemplateById($id);
        if (!$template) {
            return $this->jsonResponse(['error' => 'Template not found'], 404);
        }

        $success = $this->emailTemplate->delete($id);

        if ($success) {
            return $this->jsonResponse([
                'success' => true,
                'message' => 'Template deleted successfully'
            ]);
        } else {
            return $this->jsonResponse([
                'success' => false,
                'error' => 'Failed to delete template'
            ], 500);
        }
    }

    public function useTemplate($id)
    {
        $template = $this->emailTemplate->getTemplateById($id);

        if (!$template) {
            return $this->jsonResponse(['error' => 'Template not found'], 404);
        }

        // Return template data for use in forms
        return $this->jsonResponse([
            'success' => true,
            'template' => $template
        ]);
    }

    public function error()
    {
        $data = [
            'message' => 'An error occurred',
            'page_title' => 'Email Manager Error',
            'breadcrumbs' => [
                ['title' => 'Dashboard', 'url' => app_base_url('/admin')],
                ['title' => 'Email Manager', 'url' => app_base_url('/admin/email-manager')],
                ['title' => 'Error', 'url' => '']
            ]
        ];
        
        // Use the View class's render method to properly use themes/admin layout
        $this->view->render('admin/email-manager/error', $data);
    }

    public function threadDetail($id)
    {
        $thread = $this->emailThread->getThreadById($id);

        if (!$thread) {
            return $this->view->render('admin/email-manager/error', [
                'message' => 'Thread not found',
                'pageTitle' => 'Error'
            ]);
        }

        return $this->view->render('admin/email-manager/thread-detail', [
            'thread' => $thread,
            'pageTitle' => 'Thread Details'
        ]);
    }
    public function settings()
    {
        require_once __DIR__ . '/../../app/Services/EmailManager.php';
        $emailManager = new \EmailManager();
        $settings = $emailManager->getSettings();

        $data = [
            'settings' => $settings,
            'page_title' => 'Email Settings',
            'breadcrumbs' => [
                ['title' => 'Dashboard', 'url' => app_base_url('/admin')],
                ['title' => 'Email Manager', 'url' => app_base_url('/admin/email-manager')],
                ['title' => 'Settings', 'url' => app_base_url('/admin/email-manager/settings')]
            ]
        ];
        
        // Use the View class's render method to properly use themes/admin layout
        $this->view->render('admin/email-manager/settings', $data);
    }

    public function updateSettings()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->jsonResponse(['error' => 'Method not allowed'], 405);
        }

        require_once __DIR__ . '/../../app/Services/EmailManager.php';
        $emailManager = new \EmailManager();
        $newSettings = [
            'from_name' => $_POST['email_from_name'] ?? '',
            'from_address' => $_POST['email_from_address'] ?? '',
            'smtp_host' => $_POST['email_smtp_host'] ?? '',
            'smtp_port' => $_POST['email_smtp_port'] ?? '',
            'smtp_username' => $_POST['email_smtp_user'] ?? '',
            'smtp_password' => $_POST['email_smtp_pass'] ?? '',
            'smtp_encryption' => $_POST['email_smtp_secure'] ?? 'tls'
        ];

        // Map from_address to from_email for consistency
        $newSettings['from_email'] = $newSettings['from_address'];
        unset($newSettings['from_address']);

        try {
            if ($emailManager->updateSettings($newSettings)) {
                $_SESSION['success'] = 'Settings updated successfully';
            } else {
                $_SESSION['error'] = 'Failed to update settings';
            }
        } catch (\Exception $e) {
            error_log("Email settings update error: " . $e->getMessage());
            $_SESSION['error'] = 'Error: ' . $e->getMessage();
        }

        header('Location: ' . app_base_url('/admin/email-manager/settings'));
        exit;
        exit;
    }

    public function testEmail()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->jsonResponse(['error' => 'Method not allowed'], 405);
        }

        $email = $_POST['test_email'] ?? '';
        if (empty($email)) {
            return $this->jsonResponse(['error' => 'Email address required'], 400);
        }

        $emailManager = new \EmailManager();

        // Note: This tests currently SAVED settings. 
        // Ideally we would test with the submitted form data, but that requires more complex logic.

        $result = $emailManager->testEmailSettings($email);

        return $this->jsonResponse($result);
    }
}