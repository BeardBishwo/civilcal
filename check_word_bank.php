<?php
require_once 'app/bootstrap.php';
try {
    $db = \App\Core\Database::getInstance();
    $stmt = $db->query('SELECT COUNT(*) as count FROM word_bank');
    $result = $stmt->fetch();
    echo 'Word bank records: ' . $result['count'] . PHP_EOL;

    // Also check a few sample records
    $stmt = $db->query('SELECT term, definition FROM word_bank LIMIT 5');
    $records = $stmt->fetchAll();
    echo 'Sample records:' . PHP_EOL;
    foreach ($records as $record) {
        echo '- ' . $record['term'] . ': ' . substr($record['definition'], 0, 50) . '...' . PHP_EOL;
    }
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
}
?>