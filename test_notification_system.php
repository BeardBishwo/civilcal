<?php
/**
 * Notification System Test Script
 * Tests all components of the notification system
 */

require_once 'app/bootstrap.php';

use App\Core\Database;

class NotificationSystemTester
{
    private $db;
    private $connection;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->connection = $this->db->getPdo();
    }

    /**
     * Run all tests
     */
    public function runTests()
    {
        echo "🧪 Starting Notification System Tests...\n\n";
        
        $tests = [
            'Database Structure' => 'testDatabaseStructure',
            'Sample Data' => 'testSampleData',
            'API Endpoints' => 'testApiEndpoints',
            'Notification CRUD' => 'testNotificationCRUD',
            'Real-time Features' => 'testRealTimeFeatures'
        ];

        $passed = 0;
        $total = count($tests);

        foreach ($tests as $testName => $method) {
            echo "🔍 Testing: $testName\n";
            try {
                $this->$method();
                echo "   ✅ PASSED\n";
                $passed++;
            } catch (Exception $e) {
                echo "   ❌ FAILED: " . $e->getMessage() . "\n";
            }
            echo "\n";
        }

        echo "📊 Test Results: $passed/$total tests passed\n";
        
        if ($passed === $total) {
            echo "🎉 All tests passed! Notification system is ready for use.\n";
        } else {
            echo "⚠️ Some tests failed. Please review the issues above.\n";
        }

        return $passed === $total;
    }

    /**
     * Test database structure
     */
    private function testDatabaseStructure()
    {
        // Check table exists
        $result = $this->connection->query("SHOW TABLES LIKE 'admin_notifications'");
        if ($result->rowCount() === 0) {
            throw new Exception("admin_notifications table does not exist");
        }

        // Check columns
        $columns = $this->connection->query("DESCRIBE admin_notifications")->fetchAll();
        $columnNames = array_column($columns, 'Field');
        
        $requiredColumns = ['id', 'user_id', 'title', 'message', 'type', 'is_read', 'created_at'];
        foreach ($requiredColumns as $column) {
            if (!in_array($column, $columnNames)) {
                throw new Exception("Missing required column: $column");
            }
        }

        // Check indexes
        $indexes = $this->connection->query("SHOW INDEX FROM admin_notifications")->fetchAll();
        $indexNames = array_column($indexes, 'Key_name');
        
        if (!in_array('idx_user_id', $indexNames)) {
            throw new Exception("Missing index on user_id");
        }

        if (!in_array('idx_is_read', $indexNames)) {
            throw new Exception("Missing index on is_read");
        }
    }

    /**
     * Test sample data
     */
    private function testSampleData()
    {
        $count = $this->connection->query("SELECT COUNT(*) as count FROM admin_notifications")->fetch()['count'];
        
        if ($count === 0) {
            throw new Exception("No sample data found");
        }

        $unreadCount = $this->connection->query("SELECT COUNT(*) as count FROM admin_notifications WHERE is_read = 0")->fetch()['count'];
        
        echo "   Total notifications: $count\n";
        echo "   Unread notifications: $unreadCount\n";

        // Check data types and formats
        $stmt = $this->connection->query("SELECT type, COUNT(*) as count FROM admin_notifications GROUP BY type");
        $types = $stmt->fetchAll();
        
        $validTypes = ['info', 'success', 'warning', 'error', 'system', 'security'];
        foreach ($types as $typeRow) {
            if (!in_array($typeRow['type'], $validTypes)) {
                throw new Exception("Invalid notification type: " . $typeRow['type']);
            }
        }
    }

    /**
     * Test API endpoints
     */
    private function testApiEndpoints()
    {
        // Test unread count query (simulating API response)
        $stmt = $this->connection->prepare("
            SELECT COUNT(*) as unread_count 
            FROM admin_notifications 
            WHERE is_read = 0 AND (user_id = 1 OR user_id IS NULL)
        ");
        $stmt->execute();
        $result = $stmt->fetch();
        
        if (!isset($result['unread_count'])) {
            throw new Exception("API query failed - unread_count not found");
        }

        echo "   Unread count for user 1: " . $result['unread_count'] . "\n";

        // Test notification list query
        $stmt = $this->connection->prepare("
            SELECT id, title, message, type, is_read, created_at, data
            FROM admin_notifications 
            WHERE user_id = 1 OR user_id IS NULL 
            ORDER BY created_at DESC 
            LIMIT 5
        ");
        $stmt->execute();
        $notifications = $stmt->fetchAll();
        
        if (!is_array($notifications)) {
            throw new Exception("API query failed - notifications list not found");
        }

        echo "   Retrieved " . count($notifications) . " notifications\n";

        // Validate notification data
        foreach ($notifications as $notification) {
            if (empty($notification['title']) || empty($notification['message'])) {
                throw new Exception("Invalid notification data - missing title or message");
            }
            
            // Test JSON data parsing
            if (!empty($notification['data'])) {
                $data = json_decode($notification['data'], true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new Exception("Invalid JSON data in notification " . $notification['id']);
                }
            }
        }
    }

    /**
     * Test notification CRUD operations
     */
    private function testNotificationCRUD()
    {
        // Test CREATE
        $stmt = $this->connection->prepare("
            INSERT INTO admin_notifications 
            (user_id, title, message, type, data, is_read, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, NOW())
        ");
        
        $testData = [
            1,
            'Test Notification',
            'This is a test notification for CRUD testing',
            'info',
            json_encode(['test' => true]),
            0
        ];
        
        $stmt->execute($testData);
        $testId = $this->connection->lastInsertId();
        
        if (!$testId) {
            throw new Exception("Failed to create test notification");
        }

        echo "   Created test notification with ID: $testId\n";

        // Test READ
        $stmt = $this->connection->prepare("SELECT * FROM admin_notifications WHERE id = ?");
        $stmt->execute([$testId]);
        $notification = $stmt->fetch();
        
        if (!$notification || $notification['title'] !== 'Test Notification') {
            throw new Exception("Failed to read notification");
        }

        echo "   Successfully read notification\n";

        // Test UPDATE (mark as read)
        $stmt = $this->connection->prepare("
            UPDATE admin_notifications 
            SET is_read = 1, read_at = NOW() 
            WHERE id = ?
        ");
        $result = $stmt->execute([$testId]);
        
        if (!$result) {
            throw new Exception("Failed to update notification");
        }

        // Verify update
        $stmt = $this->connection->prepare("SELECT is_read, read_at FROM admin_notifications WHERE id = ?");
        $stmt->execute([$testId]);
        $updated = $stmt->fetch();
        
        if ($updated['is_read'] != 1) {
            throw new Exception("Failed to mark notification as read");
        }

        echo "   Successfully updated notification\n";

        // Test DELETE
        $stmt = $this->connection->prepare("DELETE FROM admin_notifications WHERE id = ?");
        $result = $stmt->execute([$testId]);
        
        if (!$result) {
            throw new Exception("Failed to delete notification");
        }

        // Verify deletion
        $stmt = $this->connection->prepare("SELECT id FROM admin_notifications WHERE id = ?");
        $stmt->execute([$testId]);
        $deleted = $stmt->fetch();
        
        if ($deleted) {
            throw new Exception("Notification was not properly deleted");
        }

        echo "   Successfully deleted notification\n";
    }

    /**
     * Test real-time features
     */
    private function testRealTimeFeatures()
    {
        // Test notification count changes
        $initialCount = $this->connection->query("SELECT COUNT(*) as count FROM admin_notifications WHERE is_read = 0")->fetch()['count'];
        
        // Create a new notification
        $stmt = $this->connection->prepare("
            INSERT INTO admin_notifications 
            (user_id, title, message, type, is_read, created_at) 
            VALUES (1, 'Real-time Test', 'Testing real-time features', 'info', 0, NOW())
        ");
        $stmt->execute();
        
        $newCount = $this->connection->query("SELECT COUNT(*) as count FROM admin_notifications WHERE is_read = 0")->fetch()['count'];
        
        if ($newCount !== $initialCount + 1) {
            throw new Exception("Real-time count update failed");
        }

        echo "   Real-time count updated: $initialCount -> $newCount\n";

        // Clean up test notification
        $this->connection->exec("DELETE FROM admin_notifications WHERE title = 'Real-time Test'");
    }
}

// Run tests
$tester = new NotificationSystemTester();
$tester->runTests();
?>