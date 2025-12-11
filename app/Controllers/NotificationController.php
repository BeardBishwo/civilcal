<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Notification;

class NotificationController extends Controller
{
    private $notificationModel;

    public function __construct()
    {
        parent::__construct();
        $this->notificationModel = new Notification();
    }

    /**
     * Get user notifications (AJAX)
     */
    public function index()
    {
        $userId = $_SESSION['user_id'] ?? null;
        
        if (!$userId) {
            // Check if AJAX request
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                exit;
            }
            // Redirect to login for regular requests
            header('Location: ' . app_base_url('/login'));
            exit;
        }

        $filters = [
            'limit' => $_GET['limit'] ?? 20
        ];

        if (isset($_GET['is_read'])) {
            $filters['is_read'] = (int)$_GET['is_read'];
        }

        $notifications = $this->notificationModel->getUserNotifications($userId, $filters);
        
        echo json_encode([
            'success' => true,
            'notifications' => $notifications
        ]);
    }

    /**
     * Get unread count (AJAX)
     */
    public function getUnreadCount()
    {
        $userId = $_SESSION['user_id'] ?? null;
        
        if (!$userId) {
            echo json_encode(['success' => false, 'count' => 0]);
            exit;
        }

        $count = $this->notificationModel->getUnreadCount($userId);
        
        echo json_encode([
            'success' => true,
            'count' => $count
        ]);
    }

    /**
     * Mark notification as read (AJAX)
     */
    public function markAsRead($id)
    {
        $userId = $_SESSION['user_id'] ?? null;
        
        if (!$userId) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $result = $this->notificationModel->markAsRead($id, $userId);
        
        echo json_encode([
            'success' => $result,
            'message' => $result ? 'Marked as read' : 'Failed to mark as read'
        ]);
    }

    /**
     * Mark all as read (AJAX)
     */
    public function markAllAsRead()
    {
        $userId = $_SESSION['user_id'] ?? null;
        
        if (!$userId) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $result = $this->notificationModel->markAllAsRead($userId);
        
        echo json_encode([
            'success' => $result,
            'message' => $result ? 'All marked as read' : 'Failed to mark all as read'
        ]);
    }

    /**
     * Delete notification (AJAX)
     */
    public function delete($id)
    {
        $userId = $_SESSION['user_id'] ?? null;
        
        if (!$userId) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $result = $this->notificationModel->delete($id, $userId);
        
        echo json_encode([
            'success' => $result,
            'message' => $result ? 'Notification deleted' : 'Failed to delete notification'
        ]);
    }

    /**
     * Show notification history page
     */
    public function history()
    {
        $userId = $_SESSION['user_id'] ?? null;
        
        if (!$userId) {
            redirect('/login');
            return;
        }

        // Get filters
        $filters = [
            'limit' => 20
        ];

        if (isset($_GET['type'])) {
            $filters['type'] = $_GET['type'];
        }

        if (isset($_GET['status'])) {
            $filters['is_read'] = $_GET['status'] === 'read' ? 1 : 0;
        }

        if (isset($_GET['priority'])) {
            $filters['priority'] = $_GET['priority'];
        }

        // Get page
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($page - 1) * $filters['limit'];

        // Get notifications
        $notifications = $this->notificationModel->getUserNotifications($userId, $filters);
        $totalCount = $this->notificationModel->getUnreadCount($userId) + count($this->notificationModel->getUserNotifications($userId, ['is_read' => 1]));

        // Render view
        $title = 'Notification History';
        require __DIR__ . '/../../themes/admin/views/notifications/history.php';
    }
}
