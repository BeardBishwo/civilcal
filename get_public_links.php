<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

$_SERVER['REQUEST_URI'] = '/'; // Dummy for bootstrap
define('IGNORE_INSTALL_LOCK', true); // Dummy in case it matters

require_once __DIR__ . '/app/bootstrap.php';

// Bypass shutdown handler redirection if possible or just try to query directly
$db = \App\Core\Database::getInstance();

echo "EXAMS:\n";
try {
    $exams = $db->query("SELECT slug, title FROM quiz_exams LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($exams as $e) {
        echo "- " . $e['title'] . " (" . $e['slug'] . ")\n";
    }
} catch (Exception $e) {
    echo "Exams error: " . $e->getMessage() . "\n";
}

echo "\nPORTAL QUIZZES (COURSES):\n";
try {
    $quizzes = $db->query("SELECT slug, title FROM syllabus_nodes WHERE type = 'course' AND is_active = 1 LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($quizzes as $q) {
        echo "- " . $q['title'] . " (" . $q['slug'] . ")\n";
    }
} catch (Exception $e) {
    echo "Quizzes error: " . $e->getMessage() . "\n";
}
