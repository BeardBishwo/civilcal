<?php
require_once __DIR__ . '/../app/bootstrap.php';

use App\Services\Quiz\DailyQuizService;

$service = new DailyQuizService();
$db = \App\Core\Database::getInstance();

echo "Clearing stale daily quiz schedule (today and future)...\n";
$db->query("DELETE FROM daily_quiz_schedule WHERE date >= ?", [date('Y-m-d')]);

echo "Generating fresh quizzes for the week...\n";
$result = $service->autoGenerateWeek();

if ($result) {
    echo "SUCCESS: Fresh quizzes generated.\n";
} else {
    echo "FAILURE: Generation failed.\n";
}
