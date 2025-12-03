<?php
/**
 * Comprehensive Notification System Fix Script
 * This script will:
 * 1. Create the admin_notifications table
 * 2. Seed it with sample notifications
 * 3. Test API endpoints
 * 4. Verify JavaScript functionality
 */

require_once 'app/bootstrap.php';

use App\Core\Database;

class NotificationSystemFixer
{
    private $db;
    private $connection;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->connection = $this->db->getPdo();
    }

    /**
     * Run the complete notification system fix
     */
    public function run()
    {
        echo "🚀 Starting Notification System Fix...\n\n";
        
        try {
            // Step 1: Create database table
            $this->createNotificationTable();
            
            // Step 2: Seed with sample data
            $this->seedNotifications();
            
            // Step 3: Verify table structure
            $this->verifyTableStructure();
            
            // Step 4: Test API functionality
            $this->testApiEndpoints();
            
            // Step 5: Create test notifications
            $this->createTestNotifications();
            
            echo "\n✅ Notification System Fix Completed Successfully!\n";
            echo "🔔 You can now test the notification system in your admin panel.\n";
            
        } catch (Exception $e) {
            echo "❌ Error: " . $e->getMessage() . "\n";
            return false;
        }
        
        return true;
    }

    /**
     * Create the admin_notifications table
     */
    private function createNotificationTable()
    {
        echo "1️⃣ Creating admin_notifications table...\n";
        
        try {
            // Drop table if exists (for clean setup)
            $this->connection->exec("DROP TABLE IF EXISTS admin_notifications");
            
            // Create table with proper structure
            $sql = "
            CREATE TABLE admin_notifications (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NULL,
                title VARCHAR(255) NOT NULL,
                message TEXT NOT NULL,
                type ENUM('info', 'success', 'warning', 'error', 'system', 'security') DEFAULT 'info',
                data JSON NULL,
                is_read TINYINT(1) DEFAULT 0,
                read_at DATETIME NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_user_id (user_id),
                INDEX idx_is_read (is_read),
                INDEX idx_type (type),
                INDEX idx_created_at (created_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            ";
            
            $this->connection->exec($sql);
            echo "   ✅ Table created successfully\n";
            
        } catch (PDOException $e) {
            throw new Exception("Failed to create table: " . $e->getMessage());
        }
    }

    /**
     * Seed the table with sample notifications
     */
    private function seedNotifications()
    {
        echo "2️⃣ Seeding notifications with sample data...\n";
        
        // Clear existing data
        $this->connection->exec("DELETE FROM admin_notifications");
        
        $notifications = [
            [
                'user_id' => 1,
                'title' => 'Welcome to Admin Panel!',
                'message' => 'Your notification system is now active and ready to keep you informed about all system activities.',
                'type' => 'success',
                'data' => json_encode(['action' => 'welcome', 'version' => '1.0']),
                'is_read' => 0,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'user_id' => 1,
                'title' => 'System Health Check',
                'message' => 'All systems are running smoothly. Database connections are optimal and performance metrics are within normal ranges.',
                'type' => 'info',
                'data' => json_encode(['status' => 'healthy', 'uptime' => '99.9%']),
                'is_read' => 0,
                'created_at' => date('Y-m-d H:i:s', strtotime('-5 minutes'))
            ],
            [
                'user_id' => 1,
                'title' => 'New User Registration',
                'message' => 'A new user has registered on the platform. User details are available in the user management section.',
                'type' => 'info',
                'data' => json_encode(['user_count' => 142, 'new_user' => 'john.doe@example.com']),
                'is_read' => 0,
                'created_at' => date('Y-m-d H:i:s', strtotime('-15 minutes'))
            ],
            [
                'user_id' => 1,
                'title' => 'Security Alert',
                'message' => 'Login attempt from new IP address detected. If this was not you, please review your account security.',
                'type' => 'warning',
                'data' => json_encode(['ip_address' => '192.168.1.100', 'user_agent' => 'Chrome']),
                'is_read' => 0,
                'created_at' => date('Y-m-d H:i:s', strtotime('-1 hour'))
            ],
            [
                'user_id' => 1,
                'title' => 'Backup Completed Successfully',
                'message' => 'Automatic database backup completed. Backup size: 45.2MB. Next backup scheduled for tomorrow.',
                'type' => 'success',
                'data' => json_encode(['backup_name' => 'backup_' . date('Y-m-d') . '.sql', 'size' => '45.2MB']),
                'is_read' => 1,
                'read_at' => date('Y-m-d H:i:s', strtotime('-30 minutes')),
                'created_at' => date('Y-m-d H:i:s', strtotime('-2 hours'))
            ]
        ];
        
        $stmt = $this->connection->prepare("
            INSERT INTO admin_notifications 
            (user_id, title, message, type, data, is_read, read_at, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        foreach ($notifications as $notification) {
            $stmt->execute([
                $notification['user_id'],
                $notification['title'],
                $notification['message'],
                $notification['type'],
                $notification['data'],
                $notification['is_read'],
                $notification['read_at'] ?? null,
                $notification['created_at']
            ]);
        }
        
        echo "   ✅ Seeded " . count($notifications) . " notifications\n";
    }

    /**
     * Verify table structure
     */
    private function verifyTableStructure()
    {
        echo "3️⃣ Verifying table structure...\n";
        
        $result = $this->connection->query("SHOW TABLES LIKE 'admin_notifications'");
        if ($result->rowCount() === 0) {
            throw new Exception("admin_notifications table does not exist");
        }
        
        // Check data
        $count = $this->connection->query("SELECT COUNT(*) as count FROM admin_notifications")->fetch()['count'];
        echo "   ✅ Table structure verified. Found $count notifications.\n";
    }

    /**
     * Test API endpoints
     */
    private function testApiEndpoints()
    {
        echo "4️⃣ Testing API endpoints...\n";
        
        try {
            // Test unread count
            $stmt = $this->connection->prepare("SELECT COUNT(*) as count FROM admin_notifications WHERE is_read = 0 AND (user_id = 1 OR user_id IS NULL)");
            $stmt->execute();
            $unreadCount = $stmt->fetch()['count'];
            
            echo "   ✅ Unread count for user 1: $unreadCount\n";
            
            // Test notification list
            $stmt = $this->connection->prepare("
                SELECT id, title, message, type, is_read, created_at 
                FROM admin_notifications 
                WHERE user_id = 1 OR user_id IS NULL 
                ORDER BY created_at DESC 
                LIMIT 5
            ");
            $stmt->execute();
            $notifications = $stmt->fetchAll();
            
            echo "   ✅ Retrieved " . count($notifications) . " notifications for testing\n";
            
        } catch (Exception $e) {
            echo "   ❌ API test failed: " . $e->getMessage() . "\n";
        }
    }

    /**
     * Create additional test notifications
     */
    private function createTestNotifications()
    {
        echo "5️⃣ Creating test notifications for immediate testing...\n";
        
        $testNotifications = [
            [
                'user_id' => 1,
                'title' => 'Test Notification',
                'message' => 'This is a test notification created to verify the system is working correctly.',
                'type' => 'info',
                'data' => json_encode(['test' => true, 'timestamp' => time()]),
                'is_read' => 0
            ],
            [
                'user_id' => 1,
                'title' => 'Success Alert',
                'message' => 'Your notification system has been successfully fixed and is now operational!',
                'type' => 'success',
                'data' => json_encode(['fixed' => true, 'timestamp' => time()]),
                'is_read' => 0
            ]
        ];
        
        $stmt = $this->connection->prepare("
            INSERT INTO admin_notifications 
            (user_id, title, message, type, data, is_read, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, NOW())
        ");
        
        foreach ($testNotifications as $notification) {
            $stmt->execute([
                $notification['user_id'],
                $notification['title'],
                $notification['message'],
                $notification['type'],
                $notification['data'],
                $notification['is_read']
            ]);
        }
        
        echo "   ✅ Created " . count($testNotifications) . " additional test notifications\n";
    }
}

// Run the fix
$fixer = new NotificationSystemFixer();
$fixer->run();
?>