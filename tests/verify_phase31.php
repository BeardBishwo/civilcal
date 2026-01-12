<?php
// tests/verify_phase31.php

echo "Phase 31: Game-Breaking Logic Verification (Static Analysis)\n";
echo "==========================================================\n";

$pass = 0;
$fail = 0;

function assertCondition($name, $condition)
{
    global $pass, $fail;
    if ($condition) {
        echo "[PASS] $name\n";
        $pass++;
    } else {
        echo "[FAIL] $name\n";
        $fail++;
    }
}

// 1. Verify Economy Infinite Money Fix (Negative Quantity)
try {
    echo "\nTesting Economy Fix...\n";
    $file = file_get_contents(__DIR__ . '/../app/Services/GamificationService.php');
    if (strpos($file, 'if ($quantity < 1)') !== false) {
        assertCondition("GamificationService: Negative Quantity Check Exists", true);
    } else {
        assertCondition("GamificationService: Negative Quantity Check Exists", false);
    }
} catch (Exception $e) {
    echo "Economy Test Error: " . $e->getMessage() . "\n";
    $fail++;
}

// 2. Verify Reviewer Controller Security
echo "\nTesting Library Viewer Security...\n";
$viewerFile = file_get_contents(__DIR__ . '/../app/Controllers/ViewerController.php');
if (strpos($viewerFile, 'if (($file->price ?? 0) <= 0)') !== false && strpos($viewerFile, 'SELECT id FROM user_library') !== false) {
    assertCondition("ViewerController: Ownership Check Implemented", true);
} else {
    assertCondition("ViewerController: Ownership Check Implemented", false);
}
if (strpos($viewerFile, 'public function show($id)') !== false) {
    assertCondition("ViewerController: Method Renamed to show()", true);
} else {
    assertCondition("ViewerController: Method Renamed to show()", false);
}

// 3. Verify Exam Time Limit
echo "\nTesting Exam Time Limit...\n";
$examFile = file_get_contents(__DIR__ . '/../app/Controllers/Quiz/ExamEngineController.php');
if (strpos($examFile, '$elapsed > $allowedDuration') !== false && strpos($examFile, 'http_response_code(408)') !== false) {
    assertCondition("ExamEngineController: Server-Side Time Limit Check Implemented", true);
} else {
    assertCondition("ExamEngineController: Server-Side Time Limit Check Implemented", false);
}

// 4. Verify Quiz Reward Farming
echo "\nTesting Quiz Reward Farming...\n";
if (strpos($examFile, 'SELECT id FROM quiz_attempts WHERE user_id = :uid AND exam_id = :eid AND status = \'completed\'') !== false) {
    assertCondition("ExamEngineController: Previous Completion Check Implemented", true);
} else {
    assertCondition("ExamEngineController: Previous Completion Check Implemented", false);
}

if (strpos($examFile, 'if (!$alreadyCompleted)') !== false) {
    assertCondition("ExamEngineController: Reward Processing Gated", true);
} else {
    assertCondition("ExamEngineController: Reward Processing Gated", false);
}

echo "\nSummary: $pass Passed, $fail Failed\n";
