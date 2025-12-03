<?php

/**
 * Script to seed initial notifications
 */

try {
    $pdo = new PDO("mysql:host=localhost;dbname=bishwo_calculator", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if table exists
    $result = $pdo->query("SHOW TABLES LIKE 'admin_notifications'")->fetch();
    if (!$result) {
        echo "❌ admin_notifications table not found\n";
        exit;
    }

    // Clear existing notifications for fresh start
    $pdo->exec("DELETE FROM admin_notifications");

    // Seed initial notifications for testing
    $adminUserId = 1;

    $notifications = [
        [
            'user_id' => $adminUserId,
            'title' => 'Welcome to Admin Panel',
            'message' => 'Your admin dashboard is ready to use. Explore all the features!',
            'type' => 'success',
            'data' => json_encode(['action' => 'welcome']),
            'is_read' => 0,
            'created_at' => date('Y-m-d H:i:s')
        ],
        [
            'user_id' => $adminUserId,
            'title' => 'System Update Available',
            'message' => 'A new system update is available. Check the updates section.',
            'type' => 'info',
            'data' => json_encode(['update_version' => '1.2.0']),
            'is_read' => 0,
            'created_at' => date('Y-m-d H:i:s', strtotime('-1 day'))
        ],
        [
            'user_id' => $adminUserId,
            'title' => 'Security Alert',
            'message' => 'New login detected from a different IP address. If this was not you, please reset your password.',
            'type' => 'warning',
            'data' => json_encode(['ip_address' => '192.168.1.100']),
            'is_read' => 0,
            'created_at' => date('Y-m-d H:i:s', strtotime('-2 days'))
        ],
        [
            'user_id' => null,
            'title' => 'Maintenance Scheduled',
            'message' => 'System maintenance is scheduled for tomorrow at 2:00 AM. Brief downtime expected.',
            'type' => 'info',
            'data' => json_encode(['maintenance_time' => '02:00 AM']),
            'is_read' => 0,
            'created_at' => date('Y-m-d H:i:s', strtotime('-3 days'))
        ],
        [
            'user_id' => $adminUserId,
            'title' => 'Backup Completed',
            'message' => 'Automatic backup completed successfully. Backup size: 45.2MB',
            'type' => 'success',
            'data' => json_encode(['backup_name' => 'backup_2023-11-15.sql']),
            'is_read' => 1,
            'read_at' => date('Y-m-d H:i:s', strtotime('-1 hour')),
            'created_at' => date('Y-m-d H:i:s', strtotime('-4 days'))
        ]
    ];

    // Insert notifications
    foreach ($notifications as $notification) {
        $columns = implode(', ', array_keys($notification));
        $placeholders = ':' . implode(', :', array_keys($notification));

        $sql = "INSERT INTO admin_notifications ($columns) VALUES ($placeholders)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($notification);
    }

    echo "✅ Seeded " . count($notifications) . " notifications\n";
    echo "🎉 Database setup complete!\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}