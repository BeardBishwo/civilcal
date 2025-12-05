<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Database;

$db = Database::getInstance();
$stmt = $db->query("SELECT DATABASE()");
echo "Connected to DB: " . $stmt->fetchColumn() . "\n";

$stmt = $db->query("SELECT COUNT(*) as count FROM email_threads");
$count = $stmt->fetch()['count'];
echo "Threads: $count\n";

if ($count == 0) {
    // Create a dummy thread
    $db->query("INSERT INTO email_threads (subject, status, priority, created_at, from_name, from_email, message) VALUES ('Test Thread', 'new', 'medium', NOW(), 'John Doe', 'john@example.com', 'This is a test message.')");
    $id = $db->lastInsertId();
    echo "Created thread ID: $id\n";
} else {
    $stmt = $db->query("SELECT id FROM email_threads LIMIT 1");
    $id = $stmt->fetch()['id'];
    echo "Existing thread ID: $id\n";
}
