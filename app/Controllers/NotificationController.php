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

        $personal = $this->notificationModel->getByUser($user->id, $limit, $offset);

        // Fetch Global (Only on first page to keep pagination simple, or separate section?)
        // For "Timeline" view, we should merge.
        // But pagination of merged arrays is complex without a robust SQL Union.
        // Quick Fix: Fetch Globals and prepend them to the list if on page 1.
        $globals = [];
        if ($page === 1) {
            $globals = $this->notificationModel->getGlobalUnread($user->id, $user->role ?? 'user', $user->plan_id ?? null);
        }

        // Merge & Sort
        $notifications = array_merge($globals, $personal);
        usort($notifications, function ($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });

        // Slice to respect limit if globbals pushed us over (Optional, but UI wants 20)
        // $notifications = array_slice($notifications, 0, $limit);

        $unreadCount = $this->notificationModel->getCountByUser($user->id) + count($globals);

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
