<?php
// Create test notifications for the logged-in user
require_once __DIR__ . '/../app/bootstrap.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    echo "Error: No user logged in. Please log in first.\n";
    exit(1);
}

$userId = $_SESSION['user_id'];

try {
    $notification = new \App\Models\Notification();
    
    // Create test notifications
    $notifications = [
        [
            'type' => 'system',
            'title' => 'Welcome to Notifications!',
            'message' => 'Your enterprise notification system is now active.',
            'icon' => 'fa-rocket',
            'priority' => 'high'
        ],
        [
            'type' => 'user_action',
            'title' => 'User Deleted Successfully',
            'message' => 'User "delete_me" has been removed from the system.',
            'icon' => 'fa-user-times',
            'priority' => 'normal'
        ],
        [
            'type' => 'alert',
            'title' => 'System Update Available',
            'message' => 'A new version is available. Click to update.',
            'action_url' => '/admin/settings',
            'action_text' => 'Update Now',
            'icon' => 'fa-exclamation-triangle',
            'priority' => 'urgent'
        ],
        [
            'type' => 'info',
            'title' => 'Database Backup Completed',
            'message' => 'Your database was successfully backed up at ' . date('Y-m-d H:i:s'),
            'icon' => 'fa-database',
            'priority' => 'low'
        ],
        [
            'type' => 'email',
            'title' => 'New Comment on Your Post',
            'message' => 'John Doe commented: "Great work on the notification system!"',
            'action_url' => '/posts/123',
            'action_text' => 'View Comment',
            'icon' => 'fa-comment',
            'priority' => 'normal'
        ]
    ];
    
    foreach ($notifications as $notif) {
        $notification->createNotification(
            $userId,
            $notif['type'],
            $notif['title'],
            $notif['message'],
            [
                'icon' => $notif['icon'],
                'priority' => $notif['priority'],
                'action_url' => $notif['action_url'] ?? null,
                'action_text' => $notif['action_text'] ?? null
            ]
        );
    }
    
    echo "✓ Created " . count($notifications) . " test notifications for user ID: $userId\n";
    echo "✓ You can now view them in the notification center!\n";
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
