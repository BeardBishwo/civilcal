<?php
/**
 * Simple test endpoint to verify notification system is working
 * Access this via: /api/test-notifications
 */

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, X-CSRF-Token");

// Handle preflight requests
if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit();
}

try {
    require_once '../app/bootstrap.php';
    
    use App\Models\Notification;
    
    $notificationModel = new Notification();
    
    // Get all admin users
    $db = App\Core\Database::getInstance();
    $connection = $db->getPdo();
    
    $stmt = $connection->query("SELECT id, email FROM users WHERE is_admin = 1 LIMIT 1");
    $adminUser = $stmt->fetch();
    
    if (!$adminUser) {
        echo json_encode([
            "success" => false,
            "error" => "No admin users found",
            "message" => "Please create an admin user first"
        ]);
        exit;
    }
    
    // Get notifications for admin user
    $notifications = $notificationModel->getUnreadByUser($adminUser['id'], 10, 0);
    $unreadCount = $notificationModel->getCountByUser($adminUser['id']);
    
    echo json_encode([
        "success" => true,
        "message" => "Notification system test successful",
        "data" => [
            "admin_user" => [
                "id" => $adminUser['id'],
                "email" => $adminUser['email']
            ],
            "unread_count" => $unreadCount,
            "notifications" => $notifications,
            "test_time" => date("Y-m-d H:i:s")
        ]
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "error" => "Test failed: " . $e->getMessage(),
        "message" => "There was an error testing the notification system"
    ]);
}
?>