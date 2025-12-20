<?php
require_once __DIR__ . '/app/bootstrap.php';

echo "=== NOTIFICATION SYSTEM DIAGNOSTIC ===\n\n";

// 1. Check database connection
try {
    $db = \App\Core\Database::getInstance();
    echo "✓ Database connection successful\n\n";
} catch (Exception $e) {
    echo "✗ Database connection failed: " . $e->getMessage() . "\n";
    exit;
}

// 2. Check if notifications table exists
try {
    $stmt = $db->query("SHOW TABLES LIKE 'notifications'");
    $tableExists = $stmt->fetch();
    if ($tableExists) {
        echo "✓ Notifications table exists\n";
        
        // Check table structure
        $stmt = $db->query("DESCRIBE notifications");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "\nTable structure:\n";
        foreach ($columns as $col) {
            echo "  - {$col['Field']} ({$col['Type']})\n";
        }
        
        // Check row count
        $stmt = $db->query("SELECT COUNT(*) as count FROM notifications");
        $count = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "\nTotal notifications: {$count['count']}\n";
        
    } else {
        echo "✗ Notifications table does NOT exist\n";
    }
} catch (Exception $e) {
    echo "✗ Error checking table: " . $e->getMessage() . "\n";
}

// 3. Check APP_BASE constant
echo "\n=== Configuration ===\n";
echo "APP_BASE: '" . (defined('APP_BASE') ? APP_BASE : 'NOT DEFINED') . "'\n";
echo "APP_URL: '" . (defined('APP_URL') ? APP_URL : 'NOT DEFINED') . "'\n";

// 4. Test app_base_url function
echo "\n=== URL Generation ===\n";
echo "app_base_url('/api/notifications/unread-count'): " . app_base_url('/api/notifications/unread-count') . "\n";

// 5. Check if routes file exists
echo "\n=== Routes ===\n";
if (file_exists(BASE_PATH . '/app/routes.php')) {
    echo "✓ routes.php exists\n";
    $routesContent = file_get_contents(BASE_PATH . '/app/routes.php');
    $notificationRoutes = preg_match_all('/\/api\/notifications/', $routesContent, $matches);
    echo "Found {$notificationRoutes} notification API routes\n";
} else {
    echo "✗ routes.php NOT found\n";
}

// 6. Check if NotificationController exists
echo "\n=== Controllers ===\n";
$adminController = BASE_PATH . '/app/Controllers/Admin/NotificationController.php';
$publicController = BASE_PATH . '/app/Controllers/NotificationController.php';

if (file_exists($adminController)) {
    echo "✓ Admin NotificationController exists\n";
} else {
    echo "✗ Admin NotificationController NOT found\n";
}

if (file_exists($publicController)) {
    echo "✓ Public NotificationController exists\n";
} else {
    echo "✗ Public NotificationController NOT found\n";
}

// 7. Test session
echo "\n=== Session ===\n";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
echo "Session ID: " . session_id() . "\n";
echo "User ID in session: " . ($_SESSION['user_id'] ?? 'NOT SET') . "\n";

echo "\n=== END DIAGNOSTIC ===\n";
