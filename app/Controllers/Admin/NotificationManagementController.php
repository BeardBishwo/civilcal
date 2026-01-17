<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Services\NotificationService;
use App\Models\Notification;

class NotificationManagementController extends Controller
{
    private $notificationService;
    private $notificationModel;

    public function __construct()
    {
        parent::__construct();
        $this->notificationService = new NotificationService();
        $this->notificationModel = new Notification();
    }

    /**
     * Show admin notification management page
     */
    public function index()
    {
        // Get all notifications for admin view
        $notifications = $this->getAllNotifications();

        // Render view
        $this->view->render('admin/notifications/index', [
            'notifications' => $notifications,
            'title' => 'Notification Management'
        ]);
    }

    /**
     * Show send notification page
     */
    public function create()
    {
        // Render view
        $this->view->render('admin/notifications/create', [
            'title' => 'Send Notification'
        ]);
    }

    /**
     * Send notification to specific users
     */
    public function send()
    {
        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input || !isset($input['user_ids'], $input['type'], $input['title'], $input['message'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid input']);
            exit;
        }

        $result = $this->notificationService->sendBulk(
            $input['user_ids'],
            $input['type'],
            $input['title'],
            $input['message'],
            $input['options'] ?? []
        );

        echo json_encode([
            'success' => true,
            'message' => 'Notifications sent successfully',
            'results' => $result
        ]);
    }

    /**
     * Broadcast notification to all users
     */
    public function broadcast()
    {
        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input || !isset($input['type'], $input['title'], $input['message'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid input']);
            exit;
        }

        $options = $input['options'] ?? [];
        if (!empty($input['image_url'])) {
            $options['image_url'] = $input['image_url'];
        }
        if (!empty($input['send_email'])) {
            $options['send_email'] = true;
        }
        if (!empty($input['scheduled_at'])) {
            $options['scheduled_at'] = $input['scheduled_at'];
        }

        $result = $this->notificationService->broadcast(
            $input['type'],
            $input['title'],
            $input['message'],
            $options
        );

        echo json_encode([
            'success' => true,
            'message' => 'Broadcast sent successfully'
        ]);
    }

    /**
     * Send to all admins
     */
    public function sendToAdmins()
    {
        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input || !isset($input['type'], $input['title'], $input['message'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid input']);
            exit;
        }

        $result = $this->notificationService->sendToAdmins(
            $input['type'],
            $input['title'],
            $input['message'],
            $input['options'] ?? []
        );

        echo json_encode([
            'success' => true,
            'message' => 'Sent to all admins successfully'
        ]);
    }

    /**
     * Delete global notification
     */
    public function deleteGlobal($id)
    {
        if ($this->notificationModel->deleteGlobal($id)) {
            echo json_encode(['success' => true, 'message' => 'Broadcast deleted successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to delete broadcast']);
        }
    }

    /**
     * Get all notifications (admin view)
     */
    private function getAllNotifications()
    {
        $db = \App\Core\Database::getInstance();
        $stmt = $db->prepare("
            SELECT n.* 
            FROM global_notifications n
            ORDER BY n.created_at DESC
            LIMIT 100
        ");
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
