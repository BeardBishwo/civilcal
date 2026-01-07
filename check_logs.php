<?php
require_once 'app/bootstrap.php';
$db = App\Core\Database::getInstance();
$tables = $db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
if (in_array('system_logs', $tables)) {
    $errors = $db->query("SELECT count(*) FROM system_logs WHERE level = 'ERROR'")->fetchColumn();
    echo "PHP System Errors: " . ($errors ?: 0) . "\n";
    if ($errors > 0) {
        $latest = $db->query("SELECT * FROM system_logs WHERE level = 'ERROR' ORDER BY id DESC LIMIT 5")->fetchAll();
        print_r($latest);
    }
} else {
    echo "No system_logs table found.\n";
}
