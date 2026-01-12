<?php

/**
 * Comprehensive Difficulty Scale Verification Script
 * Tests all 5 difficulty levels across the entire system
 */

require_once __DIR__ . '/app/Core/Database.php';

use App\Core\Database;

$db = Database::getInstance();
$pdo = $db->getPdo();

echo "=== DIFFICULTY SCALE VERIFICATION ===\n\n";

// Test 1: Check database schema supports 1-5
echo "1. Database Schema Check:\n";
$result = $pdo->query("SHOW CREATE TABLE quiz_questions")->fetch(PDO::FETCH_ASSOC);
if (strpos($result['Create Table'], 'difficulty_level') !== false) {
    echo "   ✓ difficulty_level column exists\n";

    // Check constraint
    $constraints = $pdo->query("SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS 
                                WHERE TABLE_NAME = 'quiz_questions' 
                                AND CONSTRAINT_TYPE = 'CHECK'")->fetchAll();
    if (!empty($constraints)) {
        echo "   ✓ CHECK constraint exists\n";
    } else {
        echo "   ⚠ No CHECK constraint found (may be OK depending on MySQL version)\n";
    }
} else {
    echo "   ✗ difficulty_level column NOT found\n";
}

// Test 2: Check question_stream_map has difficulty_in_stream
echo "\n2. Question Stream Map Check:\n";
$result = $pdo->query("SHOW CREATE TABLE question_stream_map")->fetch(PDO::FETCH_ASSOC);
if (strpos($result['Create Table'], 'difficulty_in_stream') !== false) {
    echo "   ✓ difficulty_in_stream column exists\n";
} else {
    echo "   ✗ difficulty_in_stream column NOT found\n";
}

// Test 3: Check existing questions difficulty distribution
echo "\n3. Existing Questions Difficulty Distribution:\n";
$stmt = $pdo->query("SELECT difficulty_level, COUNT(*) as count 
                     FROM quiz_questions 
                     WHERE difficulty_level IS NOT NULL 
                     GROUP BY difficulty_level 
                     ORDER BY difficulty_level");
$distribution = $stmt->fetchAll(PDO::FETCH_ASSOC);

$difficultyLabels = [
    1 => 'Easy',
    2 => 'Easy-Mid',
    3 => 'Medium',
    4 => 'Hard',
    5 => 'Expert'
];

if (empty($distribution)) {
    echo "   ⚠ No questions with difficulty levels found\n";
} else {
    foreach ($distribution as $row) {
        $level = $row['difficulty_level'];
        $label = $difficultyLabels[$level] ?? "Unknown ($level)";
        echo "   Level $level ($label): {$row['count']} questions\n";
    }
}

// Test 4: Check blueprint rules with difficulty distribution
echo "\n4. Blueprint Rules Difficulty Distribution Check:\n";
$stmt = $pdo->query("SELECT id, title FROM exam_blueprints WHERE is_active = 1 LIMIT 5");
$blueprints = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($blueprints)) {
    echo "   ⚠ No active blueprints found\n";
} else {
    foreach ($blueprints as $bp) {
        echo "   Blueprint: {$bp['title']}\n";
        $rules = $pdo->prepare("SELECT difficulty_distribution FROM blueprint_rules WHERE blueprint_id = ?");
        $rules->execute([$bp['id']]);
        $ruleData = $rules->fetchAll(PDO::FETCH_ASSOC);

        foreach ($ruleData as $rule) {
            if (!empty($rule['difficulty_distribution'])) {
                $dist = json_decode($rule['difficulty_distribution'], true);
                if ($dist) {
                    echo "      Distribution: ";
                    foreach ($dist as $level => $count) {
                        echo "$level:$count ";
                    }
                    echo "\n";
                }
            }
        }
    }
}

// Test 5: Verify ImportProcessor mapping
echo "\n5. ImportProcessor Difficulty Mapping Test:\n";
require_once __DIR__ . '/app/Services/Quiz/ImportProcessor.php';
$processor = new \App\Services\Quiz\ImportProcessor();

$testCases = [
    ['level' => '1', 'expected' => 1],
    ['level' => 'easy', 'expected' => 1],
    ['level' => '2', 'expected' => 2],
    ['level' => 'easy-mid', 'expected' => 2],
    ['level' => '3', 'expected' => 3],
    ['level' => 'medium', 'expected' => 3],
    ['level' => '4', 'expected' => 4],
    ['level' => 'hard', 'expected' => 4],
    ['level' => '5', 'expected' => 5],
    ['level' => 'expert', 'expected' => 5],
];

$allPassed = true;
foreach ($testCases as $test) {
    $row = ['level' => $test['level']];
    $result = $processor->processRow($row, 'test_batch', 1);

    if ($result['level'] == $test['expected']) {
        echo "   ✓ '{$test['level']}' → {$result['level']}\n";
    } else {
        echo "   ✗ '{$test['level']}' → {$result['level']} (expected {$test['expected']})\n";
        $allPassed = false;
    }
}

// Test 6: Verify QuestionImportService normalization
echo "\n6. QuestionImportService Difficulty Normalization Test:\n";
require_once __DIR__ . '/app/Services/QuestionImportService.php';
$importService = new \App\Services\QuestionImportService();

// Use reflection to test private method
$reflection = new ReflectionClass($importService);
$method = $reflection->getMethod('normalizeDifficulty');
$method->setAccessible(true);

$testCases2 = [
    'easy' => 1,
    'Easy' => 1,
    'EASY' => 1,
    'easy-mid' => 2,
    'easy_mid' => 2,
    'medium' => 3,
    'hard' => 4,
    'expert' => 5,
    '1' => 1,
    '5' => 5,
];

foreach ($testCases2 as $input => $expected) {
    $result = $method->invoke($importService, $input);
    if ($result == $expected) {
        echo "   ✓ '$input' → $result\n";
    } else {
        echo "   ✗ '$input' → $result (expected $expected)\n";
    }
}

// Test 7: Verify ExamGeneratorService mapping
echo "\n7. ExamGeneratorService Difficulty Mapping Test:\n";
try {
    require_once __DIR__ . '/app/Services/ExamGeneratorService.php';
    require_once __DIR__ . '/app/Services/ExamBlueprintService.php';
    require_once __DIR__ . '/app/Services/SyllabusService.php';

    $generator = new \App\Services\ExamGeneratorService();

    $reflection2 = new ReflectionClass($generator);
    $method2 = $reflection2->getMethod('mapDifficultyToLevel');
    $method2->setAccessible(true);

    $testCases3 = [
        'easy' => 1,
        'easy-mid' => 2,
        'easy_mid' => 2,
        'medium' => 3,
        'hard' => 4,
        'expert' => 5,
    ];

    foreach ($testCases3 as $input => $expected) {
        $result = $method2->invoke($generator, $input);
        if ($result == $expected) {
            echo "   ✓ '$input' → $result\n";
        } else {
            echo "   ✗ '$input' → $result (expected $expected)\n";
        }
    }
} catch (Exception $e) {
    echo "   ⚠ Could not test ExamGeneratorService: " . $e->getMessage() . "\n";
}

echo "\n=== VERIFICATION COMPLETE ===\n";
