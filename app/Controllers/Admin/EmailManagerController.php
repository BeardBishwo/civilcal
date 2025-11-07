<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\EmailThread;
use App\Models\EmailResponse;
use App\Models\EmailTemplate;
use Exception;

/**
 * Email Manager Controller
 * Professional email management system for admin support
 */
class EmailManagerController extends Controller
{
    private $emailThreadModel;
    private $emailResponseModel;
    private $emailTemplateModel;
    
    public function __construct() {
        parent::__construct();
        $this->requireRole('admin'); // Only admins can access email management
        
        $this->emailThreadModel = new EmailThread();
        $this->emailResponseModel = new EmailResponse();
        $this->emailTemplateModel = new EmailTemplate();
    }
    
    /**
     * Email management dashboard
     * GET /admin/email-manager
     */
    public function index() {
        try {
            // Get dashboard statistics
            $stats = $this->getDashboardStats();
            
            // Get recent threads
            $recentThreads = $this->emailThreadModel->getRecentThreads(10);
            
            // Get pending assignments
            $pendingThreads = $this->emailThreadModel->getThreadsByStatus('pending', 5);
            
            $data = [
                'title' => 'Email Manager - Dashboard',
                'stats' => $stats,
                'recent_threads' => $recentThreads,
                'pending_threads' => $pendingThreads
            ];
            
            $this->adminView('email-manager/dashboard', $data);
            
        } catch (Exception $e) {
            error_log("Email manager dashboard error: " . $e->getMessage());
            $this->redirect('/admin/dashboard?error=Failed to load email manager');
        }
    }
    
    /**
     * Get all email threads with filtering
     * GET /admin/email-manager/threads
     */
    public function threads() {
        try {
            $page = max(1, intval($_GET['page'] ?? 1));
            $limit = min(50, max(1, intval($_GET['limit'] ?? 20)));
            $status = $_GET['status'] ?? 'all';
            $priority = $_GET['priority'] ?? 'all';
            $assignedTo = $_GET['assigned_to'] ?? 'all';
            $search = trim($_GET['search'] ?? '');
            
            $filters = [
                'status' => $status !== 'all' ? $status : null,
                'priority' => $priority !== 'all' ? $priority : null,
                'assigned_to' => $assignedTo !== 'all' ? $assignedTo : null,
                'search' => $search ?: null
            ];
            
            $threads = $this->emailThreadModel->getThreadsWithFilters($filters, $page, $limit);
            $totalThreads = $this->emailThreadModel->getThreadCountWithFilters($filters);
            $totalPages = ceil($totalThreads / $limit);
            
            // Get available assignees for filter dropdown
            $availableAssignees = $this->emailThreadModel->getAvailableAssignees();
            
            $this->json([
                'success' => true,
                'data' => [
                    'threads' => $threads,
                    'pagination' => [
                        'current_page' => $page,
                        'total_pages' => $totalPages,
                        'total_threads' => $totalThreads,
                        'per_page' => $limit
                    ],
                    'filters' => $filters,
                    'available_assignees' => $availableAssignees
                ]
            ]);
            
        } catch (Exception $e) {
            error_log("Email threads error: " . $e->getMessage());
            $this->json(['success' => false, 'message' => 'Failed to load email threads'], 500);
        }
    }
    
    /**
     * View a specific email thread
     * GET /admin/email-manager/threads/{threadId}
     */
    public function viewThread($threadId) {
        try {
            $thread = $this->emailThreadModel->getThreadById($threadId);
            
            if (!$thread) {
                $this->json(['success' => false, 'message' => 'Email thread not found'], 404);
                return;
            }
            
            // Get all responses for this thread
            $responses = $this->emailResponseModel->getResponsesByThread($threadId);
            
            // Mark thread as read if not already
            if ($thread['status'] === 'unread') {
                $this->emailThreadModel->markAsRead($threadId);
            }
            
            $this->json([
                'success' => true,
                'data' => [
                    'thread' => $thread,
                    'responses' => $responses
                ]
            ]);
            
        } catch (Exception $e) {
            error_log("View thread error: " . $e->getMessage());
            $this->json(['success' => false, 'message' => 'Failed to load email thread'], 500);
        }
    }
    
    /**
     * Create a new email thread (manual entry)
     * POST /admin/email-manager/threads
     */
    public function createThread() {
        $this->requireRole('admin');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Method not allowed'], 405);
            return;
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        
        // Validate required fields
        $customerEmail = trim($input['customer_email'] ?? '');
        $customerName = trim($input['customer_name'] ?? '');
        $subject = trim($input['subject'] ?? '');
        $priority = $input['priority'] ?? 'normal';
        $category = $input['category'] ?? 'general';
        $initialMessage = trim($input['message'] ?? '');
        $assignedTo = $input['assigned_to'] ?? null;
        $isInternal = filter_var($input['is_internal'] ?? false, FILTER_VALIDATE_BOOLEAN);
        
        if (!$customerEmail || !$subject || !$initialMessage) {
            $this->json(['success' => false, 'message' => 'Customer email, subject, and message are required'], 400);
            return;
        }
        
        if (!filter_var($customerEmail, FILTER_VALIDATE_EMAIL)) {
            $this->json(['success' => false, 'message' => 'Invalid customer email address'], 400);
            return;
        }
        
        if (!in_array($priority, ['low', 'normal', 'high', 'urgent'])) {
            $this->json(['success' => false, 'message' => 'Invalid priority level'], 400);
            return;
        }
        
        try {
            // Create the thread
            $thread = $this->emailThreadModel->createThread([
                'customer_email' => $customerEmail,
                'customer_name' => $customerName,
                'subject' => $subject,
                'priority' => $priority,
                'category' => $category,
                'status' => 'open',
                'assigned_to' => $assignedTo,
                'created_by' => $this->getUser()['id']
            ]);
            
            if ($thread) {
                // Add initial message as first response
                $response = $this->emailResponseModel->addResponse([
                    'thread_id' => $thread['id'],
                    'user_id' => $this->getUser()['id'],
                    'content' => $initialMessage,
                    'is_internal' => $isInternal,
                    'is_customer' => false
                ]);
                
                if ($response) {
                    $this->json([
                        'success' => true,
                        'message' => 'Email thread created successfully',
                        'data' => [
                            'thread_id' => $thread['id']
                        ]
                    ]);
                } else {
                    $this->json(['success' => false, 'message' => 'Failed to create initial response'], 500);
                }
            } else {
                $this->json(['success' => false, 'message' => 'Failed to create email thread'], 500);
            }
            
        } catch (Exception $e) {
            error_log("Create thread error: " . $e->getMessage());
            $this->json(['success' => false, 'message' => 'An error occurred while creating the thread'], 500);
        }
    }
    
    /**
     * Add response to a thread
     * POST /admin/email-manager/threads/{threadId}/responses
     */
    public function addResponse($threadId) {
        $this->requireRole('admin');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Method not allowed'], 405);
            return;
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        
        $content = trim($input['content'] ?? '');
        $isInternal = filter_var($input['is_internal'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $isCustomer = filter_var($input['is_customer'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $useTemplate = $input['use_template'] ?? null;
        $templateVariables = $input['template_variables'] ?? [];
        
        if (!$content && !$useTemplate) {
            $this->json(['success' => false, 'message' => 'Response content or template is required'], 400);
            return;
        }
        
        try {
            // Verify thread exists
            $thread = $this->emailThreadModel->getThreadById($threadId);
            if (!$thread) {
                $this->json(['success' => false, 'message' => 'Email thread not found'], 404);
                return;
            }
            
            // Check if user can respond to this thread
            if ($thread['assigned_to'] && $thread['assigned_to'] != $this->getUser()['id'] && $this->getUser()['role'] !== 'admin') {
                $this->json(['success' => false, 'message' => 'This thread is assigned to another agent'], 403);
                return;
            }
            
            // Process template if used
            if ($useTemplate) {
                $template = $this->emailTemplateModel->getTemplateById($useTemplate);
                if ($template) {
                    $content = $this->emailTemplateModel->processTemplate($template['content'], $templateVariables);
                }
            }
            
            if (!$content) {
                $this->json(['success' => false, 'message' => 'Template processing failed or resulted in empty content'], 400);
                return;
            }
            
            // Add the response
            $response = $this->emailResponseModel->addResponse([
                'thread_id' => $threadId,
                'user_id' => $this->getUser()['id'],
                'content' => $content,
                'is_internal' => $isInternal,
                'is_customer' => $isCustomer
            ]);
            
            if ($response) {
                // Update thread status and last activity
                $newStatus = $isCustomer && !$isInternal ? 'pending' : 'open';
                $this->emailThreadModel->updateThread($threadId, [
                    'status' => $newStatus,
                    'last_response_at' => date('Y-m-d H:i:s')
                ]);
                
                // Send email notification if this is a customer-facing response
                if ($isCustomer && !$isInternal) {
                    // Email sending logic would go here
                    // For now, we'll just log it
                    error_log("Email would be sent to: " . $thread['customer_email']);
                }
                
                $this->json([
                    'success' => true,
                    'message' => 'Response added successfully',
                    'data' => [
                        'response' => $response
                    ]
                ]);
            } else {
                $this->json(['success' => false, 'message' => 'Failed to add response'], 500);
            }
            
        } catch (Exception $e) {
            error_log("Add response error: " . $e->getMessage());
            $this->json(['success' => false, 'message' => 'An error occurred while adding the response'], 500);
        }
    }
    
    /**
     * Update thread status/assignment
     * PUT /admin/email-manager/threads/{threadId}
     */
    public function updateThread($threadId) {
        $this->requireRole('admin');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
            $this->json(['success' => false, 'message' => 'Method not allowed'], 405);
            return;
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        
        $updates = [];
        
        if (isset($input['status'])) {
            $validStatuses = ['open', 'pending', 'resolved', 'closed'];
            if (!in_array($input['status'], $validStatuses)) {
                $this->json(['success' => false, 'message' => 'Invalid status'], 400);
                return;
            }
            $updates['status'] = $input['status'];
        }
        
        if (isset($input['priority'])) {
            $validPriorities = ['low', 'normal', 'high', 'urgent'];
            if (!in_array($input['priority'], $validPriorities)) {
                $this->json(['success' => false, 'message' => 'Invalid priority'], 400);
                return;
            }
            $updates['priority'] = $input['priority'];
        }
        
        if (isset($input['assigned_to'])) {
            $updates['assigned_to'] = $input['assigned_to'] ?: null;
        }
        
        if (isset($input['category'])) {
            $updates['category'] = trim($input['category']);
        }
        
        if (empty($updates)) {
            $this->json(['success' => false, 'message' => 'No valid updates provided'], 400);
            return;
        }
        
        try {
            $success = $this->emailThreadModel->updateThread($threadId, $updates);
            
            if ($success) {
                $updatedThread = $this->emailThreadModel->getThreadById($threadId);
                
                $this->json([
                    'success' => true,
                    'message' => 'Thread updated successfully',
                    'data' => [
                        'thread' => $updatedThread
                    ]
                ]);
            } else {
                $this->json(['success' => false, 'message' => 'Failed to update thread'], 500);
            }
            
        } catch (Exception $e) {
            error_log("Update thread error: " . $e->getMessage());
            $this->json(['success' => false, 'message' => 'An error occurred while updating the thread'], 500);
        }
    }
    
    /**
     * Delete a thread (admin only)
     * DELETE /admin/email-manager/threads/{threadId}
     */
    public function deleteThread($threadId) {
        $this->requireRole('admin');
        
        try {
            $success = $this->emailThreadModel->deleteThread($threadId);
            
            if ($success) {
                $this->json([
                    'success' => true,
                    'message' => 'Email thread deleted successfully'
                ]);
            } else {
                $this->json(['success' => false, 'message' => 'Failed to delete thread'], 500);
            }
            
        } catch (Exception $e) {
            error_log("Delete thread error: " . $e->getMessage());
            $this->json(['success' => false, 'message' => 'An error occurred while deleting the thread'], 500);
        }
    }
    
    /**
     * Email templates management
     * GET /admin/email-manager/templates
     */
    public function templates() {
        try {
            $templates = $this->emailTemplateModel->getAllTemplates();
            
            $this->json([
                'success' => true,
                'data' => [
                    'templates' => $templates
                ]
            ]);
            
        } catch (Exception $e) {
            error_log("Email templates error: " . $e->getMessage());
            $this->json(['success' => false, 'message' => 'Failed to load email templates'], 500);
        }
    }
    
    /**
     * Create email template
     * POST /admin/email-manager/templates
     */
    public function createTemplate() {
        $this->requireRole('admin');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Method not allowed'], 405);
            return;
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        
        $name = trim($input['name'] ?? '');
        $subject = trim($input['subject'] ?? '');
        $content = trim($input['content'] ?? '');
        $category = trim($input['category'] ?? 'general');
        $variables = $input['variables'] ?? [];
        
        if (!$name || !$content) {
            $this->json(['success' => false, 'message' => 'Template name and content are required'], 400);
            return;
        }
        
        try {
            $template = $this->emailTemplateModel->createTemplate([
                'name' => $name,
                'subject' => $subject,
                'content' => $content,
                'category' => $category,
                'variables' => json_encode($variables),
                'created_by' => $this->getUser()['id']
            ]);
            
            if ($template) {
                $this->json([
                    'success' => true,
                    'message' => 'Email template created successfully',
                    'data' => [
                        'template' => $template
                    ]
                ]);
            } else {
                $this->json(['success' => false, 'message' => 'Failed to create template'], 500);
            }
            
        } catch (Exception $e) {
            error_log("Create template error: " . $e->getMessage());
            $this->json(['success' => false, 'message' => 'An error occurred while creating the template'], 500);
        }
    }
    
    /**
     * Update email template
     * PUT /admin/email-manager/templates/{templateId}
     */
    public function updateTemplate($templateId) {
        $this->requireRole('admin');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
            $this->json(['success' => false, 'message' => 'Method not allowed'], 405);
            return;
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        
        $updates = [];
        
        if (isset($input['name'])) $updates['name'] = trim($input['name']);
        if (isset($input['subject'])) $updates['subject'] = trim($input['subject']);
        if (isset($input['content'])) $updates['content'] = trim($input['content']);
        if (isset($input['category'])) $updates['category'] = trim($input['category']);
        if (isset($input['variables'])) $updates['variables'] = json_encode($input['variables']);
        
        if (empty($updates)) {
            $this->json(['success' => false, 'message' => 'No valid updates provided'], 400);
            return;
        }
        
        try {
            $success = $this->emailTemplateModel->updateTemplate($templateId, $updates);
            
            if ($success) {
                $updatedTemplate = $this->emailTemplateModel->getTemplateById($templateId);
                
                $this->json([
                    'success' => true,
                    'message' => 'Template updated successfully',
                    'data' => [
                        'template' => $updatedTemplate
                    ]
                ]);
            } else {
                $this->json(['success' => false, 'message' => 'Failed to update template'], 500);
            }
            
        } catch (Exception $e) {
            error_log("Update template error: " . $e->getMessage());
            $this->json(['success' => false, 'message' => 'An error occurred while updating the template'], 500);
        }
    }
    
    /**
     * Delete email template
     * DELETE /admin/email-manager/templates/{templateId}
     */
    public function deleteTemplate($templateId) {
        $this->requireRole('admin');
        
        try {
            $success = $this->emailTemplateModel->deleteTemplate($templateId);
            
            if ($success) {
                $this->json([
                    'success' => true,
                    'message' => 'Email template deleted successfully'
                ]);
            } else {
                $this->json(['success' => false, 'message' => 'Failed to delete template'], 500);
            }
            
        } catch (Exception $e) {
            error_log("Delete template error: " . $e->getMessage());
            $this->json(['success' => false, 'message' => 'An error occurred while deleting the template'], 500);
        }
    }
    
    /**
     * Get email statistics
     * GET /admin/email-manager/stats
     */
    public function stats() {
        try {
            $stats = $this->getDashboardStats();
            
            $this->json([
                'success' => true,
                'data' => $stats
            ]);
            
        } catch (Exception $e) {
            error_log("Email stats error: " . $e->getMessage());
            $this->json(['success' => false, 'message' => 'Failed to load email statistics'], 500);
        }
    }
    
    /**
     * Get dashboard statistics
     */
    private function getDashboardStats() {
        return [
            'total_threads' => $this->emailThreadModel->getTotalThreadCount(),
            'open_threads' => $this->emailThreadModel->getThreadCountByStatus('open'),
            'pending_threads' => $this->emailThreadModel->getThreadCountByStatus('pending'),
            'resolved_threads' => $this->emailThreadModel->getThreadCountByStatus('resolved'),
            'closed_threads' => $this->emailThreadModel->getThreadCountByStatus('closed'),
            'urgent_threads' => $this->emailThreadModel->getThreadCountByPriority('urgent'),
            'high_priority_threads' => $this->emailThreadModel->getThreadCountByPriority('high'),
            'avg_response_time' => $this->emailThreadModel->getAverageResponseTime(),
            'unread_threads' => $this->emailThreadModel->getThreadCountByStatus('unread')
        ];
    }
}
?>
