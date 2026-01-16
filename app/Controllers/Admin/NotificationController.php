<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\Notification;
use App\Core\Auth;
use Exception;

class NotificationController extends Controller
{
    private $notificationModel;

    public function __construct()
    {
        parent::__construct();
        $this->notificationModel = new Notification();
    }

    /**
     * Get notifications for the current user
     */
    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            header('Location: /login');
            exit;
        }

        $page = (int)($_GET['page'] ?? 1);
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $notifications = $this->notificationModel->getByUser($user->id, $limit, $offset);
        $unreadCount = $this->notificationModel->getCountByUser($user->id);

        $totalNotifications = $this->notificationModel->getTotalCountByUser($user->id);
        $totalPages = ceil($totalNotifications / $limit);

        // Fetch users for the dropdown (limit to 100 for performance, or use searching later)
        $userModel = new \App\Models\User();
        $users = $userModel->findAll(['limit' => 100, 'order' => 'created_at DESC']);

        $data = [
            'user' => $user,
            'users' => $users,
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
            'page' => $page,
            'totalPages' => $totalPages,
            'totalNotifications' => $totalNotifications,
            'page_title' => 'Notifications - Admin Panel',
            'currentPage' => 'notifications'
        ];

        $this->view->render('admin/notifications/index', $data);
    }

    /**
     * Get notifications via API
     */
    public function getNotifications()
    {
        $user = Auth::user();
        if (!$user) {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'Access denied']);
            return;
        }

        try {
            $page = (int)($_GET['page'] ?? 1);
            $limit = (int)($_GET['limit'] ?? 20);
            $offset = ($page - 1) * $limit;
            $type = $_GET['type'] ?? null;
            $unreadOnly = (bool)($_GET['unread_only'] ?? false);

            $notifications = [];
            if ($unreadOnly) {
                $notifications = $this->notificationModel->getUnreadByUser($user->id, $limit, $offset);
            } else {
                // In a real implementation, you would filter by type if specified
                $notifications = $this->notificationModel->getByUser($user->id, $limit, $offset);
            }

            $unreadCount = $this->notificationModel->getCountByUser($user->id);

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'notifications' => $notifications,
                'unread_count' => $unreadCount,
                'page' => $page,
                'total' => count($notifications)
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'Failed to get notifications: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($id)
    {
        $user = Auth::user();
        if (!$user) {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'Access denied']);
            return;
        }

        try {
            $result = $this->notificationModel->markAsRead($id, $user->id);

            header('Content-Type: application/json');
            echo json_encode([
                'success' => $result,
                'message' => $result ? 'Notification marked as read' : 'Failed to mark notification as read'
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'Failed to mark notification as read: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        $user = Auth::user();
        if (!$user) {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'Access denied']);
            return;
        }

        try {
            $result = $this->notificationModel->markAllAsRead($user->id);

            header('Content-Type: application/json');
            echo json_encode([
                'success' => $result,
                'message' => $result ? 'All notifications marked as read' : 'Failed to mark notifications as read'
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'Failed to mark all notifications as read: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Create a new notification (for admin use)
     */
    public function create()
    {
        $user = Auth::user();
        if (!$user || !$user->is_admin) {
            http_response_code(403);
            echo json_encode(['error' => 'Access denied']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $target = $input['target'] ?? 'user'; // 'user' or 'all'
        $title = $input['title'] ?? '';
        $message = $input['message'] ?? '';
        $type = $input['type'] ?? 'info';
        $userId = $input['user_id'] ?? null;
        $url = $input['url'] ?? '#';

        if (empty($title) || empty($message)) {
            http_response_code(400);
            echo json_encode(['error' => 'Title and message are required']);
            return;
        }

        if ($target === 'all') {
            $result = $this->notificationModel->broadcast($title, $message, $type, $url);
        } else {
            if (!$userId) {
                http_response_code(400);
                echo json_encode(['error' => 'User ID is required for single notification']);
                return;
            }
            $result = $this->notificationModel->create($userId, $title, $message, $url, $type);
        }

        header('Content-Type: application/json');
        echo json_encode([
            'success' => $result,
            'message' => $result ? 'Notification sent successfully' : 'Failed to send notification'
        ]);
    }

    /**
     * Delete a notification
     */
    public function delete($id)
    {
        $user = Auth::user();
        if (!$user || !$user->is_admin) {
            http_response_code(403);
            echo json_encode(['error' => 'Access denied']);
            return;
        }

        $result = $this->notificationModel->delete($id, $user->id);

        header('Content-Type: application/json');
        echo json_encode([
            'success' => $result,
            'message' => $result ? 'Notification deleted successfully' : 'Failed to delete notification'
        ]);
    }

    /**
     * Get unread notification count for the current user
     */
    public function getUnreadCount()
    {
        $user = Auth::user();
        if (!$user) {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'Access denied']);
            return;
        }

        try {
            $unreadCount = $this->notificationModel->getCountByUser($user->id);

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'unread_count' => $unreadCount
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'Failed to get unread count: ' . $e->getMessage()
            ]);
        }
    }
}
