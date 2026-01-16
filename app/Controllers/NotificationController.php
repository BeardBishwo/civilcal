<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Notification;
use App\Core\Auth;

class NotificationController extends Controller
{
    private $notificationModel;

    public function __construct()
    {
        parent::__construct();
        $this->notificationModel = new Notification();
    }

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

        // Define data to pass to view
        $data = [
            'user' => $user,
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
            'page' => $page,
            'totalPages' => $totalPages,
            'totalNotifications' => $totalNotifications,
            'page_title' => 'My Notifications',
            'currentPage' => 'notifications'
        ];

        // Render the user-specific view from the default theme
        $this->view->render('user/notifications', $data);
    }
}
