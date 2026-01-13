<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

define('BISHWO_CALCULATOR', true);
require_once __DIR__ . '/app/bootstrap.php';

use App\Models\User;
use App\Services\ProfileService;
use App\Core\Database;

try {
    echo "--- Starting Verification for User Feed Personalization ---\n\n";

    $db = Database::getInstance();
    $userModel = new User();
    $profileService = new ProfileService();

    // 1. Create a Test User
    $email = 'verify_feed_' . time() . '@test.com';
    $password = 'password123';
    $userData = [
        'email' => $email,
        'username' => 'verify_' . time(),
        'password' => password_hash($password, PASSWORD_DEFAULT),
        'first_name' => 'Verify',
        'last_name' => 'User',
        'terms_agreed' => 1
    ];

    $userId = $userModel->create($userData);
    if (!$userId) {
        throw new Exception("FAILED: Could not create test user.");
    }
    echo "[PASS] Created test user ID: $userId\n";

    // 2. Test Standard Stream Selection
    echo "\n--- Testing Standard Stream Selection ---\n";
    $stream = $db->query("SELECT id FROM syllabus_nodes WHERE type = 'course' LIMIT 1")->fetch();
    if (!$stream) {
        echo "[SKIP] No streams found in DB to test standard selection.\n";
    } else {
        $streamId = $stream['id'];
        $updateData = [
            'stream_id' => $streamId,
            'custom_stream' => '',
            'education_level' => 'BACHELOR\'S'
        ];

        $profileService->updateProfile($userId, $updateData);

        // Verify DB
        $user = $userModel->find($userId);
        $career = $userModel->getCareerInterests($userId);

        if ($user['stream_id'] == $streamId && empty($career['custom_stream'])) {
            echo "[PASS] Standard Stream saved correctly (stream_id: $streamId, custom_stream: NULL).\n";
        } else {
            echo "[FAIL] Standard Stream mismatch! User stream_id: {$user['stream_id']}, Custom: " . ($career['custom_stream'] ?? 'NULL') . "\n";
        }
    }

    // 3. Test Manual Stream Entry
    echo "\n--- Testing Manual Stream Entry ---\n";
    $manualStream = "Artificial Intelligence";
    $manualEdu = "Ph.D."; // Should be uppercase
    $updateDataManual = [
        'stream_id' => '',
        'custom_stream' => $manualStream,
        'education_level' => $manualEdu
    ];

    $profileService->updateProfile($userId, $updateDataManual);

    // Verify DB
    $userManual = $userModel->find($userId);
    $careerManual = $userModel->getCareerInterests($userId);

    if (empty($userManual['stream_id'])) {
        echo "[PASS] User stream_id correctly cleared.\n";
    } else {
        echo "[FAIL] User stream_id NOT cleared: {$userManual['stream_id']}\n";
    }

    if (($careerManual['custom_stream'] ?? '') === 'ARTIFICIAL INTELLIGENCE') {
        echo "[PASS] Custom Stream saved and uppercased: {$careerManual['custom_stream']}\n";
    } else {
        echo "[FAIL] Custom Stream failed: Expected 'ARTIFICIAL INTELLIGENCE', got '" . ($careerManual['custom_stream'] ?? 'NULL') . "'\n";
    }

    if (($careerManual['education_level'] ?? '') === 'PH.D.') {
        echo "[PASS] Education Level saved and uppercased: {$careerManual['education_level']}\n";
    } else {
        echo "[FAIL] Education Level failed: Expected 'PH.D.', got '" . ($careerManual['education_level'] ?? 'NULL') . "'\n";
    }

    // 4. Test Study Mode
    echo "\n--- Testing Study Mode ---\n";
    $updateDataMode = ['study_mode' => 'world'];
    $profileService->updateProfile($userId, $updateDataMode);
    $userMode = $userModel->find($userId);

    if ($userMode['study_mode'] === 'world') {
        echo "[PASS] Study Mode updated to 'world'.\n";
    } else {
        echo "[FAIL] Study Mode failed: {$userMode['study_mode']}\n";
    }

    // 5. Verify Content Filtering Logic (Simulating PortalController)
    echo "\n--- Testing Feed Content Filtering ---\n";

    // Create Dummy Streams
    $pdo = $db->getPdo();
    $pdo->exec("INSERT INTO syllabus_nodes (title, type, is_active, `order`, slug) VALUES ('Test Stream A', 'course', 1, 998, 'test-stream-a')");
    $streamA = $pdo->lastInsertId();
    $pdo->exec("INSERT INTO syllabus_nodes (title, type, is_active, `order`, slug) VALUES ('Test Stream B', 'course', 1, 999, 'test-stream-b')");
    $streamB = $pdo->lastInsertId();

    // Create Dummy Exams
    $pdo->exec("INSERT INTO quiz_exams (title, slug, course_id, status, created_at) VALUES ('Exam for A', 'exam-a', $streamA, 'published', NOW())");
    $examA = $pdo->lastInsertId();
    $pdo->exec("INSERT INTO quiz_exams (title, slug, course_id, status, created_at) VALUES ('Exam for B', 'exam-b', $streamB, 'published', NOW())");
    $examB = $pdo->lastInsertId();

    // Mock PortalController Query Logic
    $getContent = function ($userId) use ($db) {
        $user = $db->findOne('users', ['id' => $userId]);
        $streamId = $user['stream_id'];

        $sql = "SELECT id FROM quiz_exams WHERE status = 'published'";
        $params = [];
        if ($streamId) {
            $sql .= " AND course_id = :stream_id";
            $params['stream_id'] = $streamId;
        }
        return $db->query($sql, $params)->fetchAll(PDO::FETCH_COLUMN);
    };

    // Test 1: User selected Stream A
    $profileService->updateProfile($userId, ['stream_id' => $streamA]);
    $resultsA = $getContent($userId);

    if (in_array($examA, $resultsA) && !in_array($examB, $resultsA)) {
        echo "[PASS] Filtering works: User on Stream A sees Exam A only.\n";
    } else {
        echo "[FAIL] Filtering failed for Stream A. Got IDs: " . implode(',', $resultsA) . "\n";
    }

    // Test 2: User selected Stream B
    $profileService->updateProfile($userId, ['stream_id' => $streamB]);
    $resultsB = $getContent($userId);

    if (in_array($examB, $resultsB) && !in_array($examA, $resultsB)) {
        echo "[PASS] Filtering works: User on Stream B sees Exam B only.\n";
    } else {
        echo "[FAIL] Filtering failed for Stream B. Got IDs: " . implode(',', $resultsB) . "\n";
    }

    // Test 3: User Manual (No ID)
    $profileService->updateProfile($userId, ['stream_id' => '', 'custom_stream' => 'MANUAL TEST']);
    $resultsManual = $getContent($userId);

    if (in_array($examA, $resultsManual) && in_array($examB, $resultsManual)) {
        echo "[PASS] Fallback works: Manual User sees ALL exams.\n";
    } else {
        echo "[FAIL] Fallback failed. Got IDs: " . implode(',', $resultsManual) . "\n";
    }

    // Clean up Dummy Data
    $pdo->exec("DELETE FROM syllabus_nodes WHERE id IN ($streamA, $streamB)");
    $pdo->exec("DELETE FROM quiz_exams WHERE id IN ($examA, $examB)");


    // Cleanup User
    echo "\n--- Cleanup ---\n";
    $db->delete('users', 'id = :id', ['id' => $userId]);
    $db->delete('career_interests', 'user_id = :id', ['id' => $userId]);

    echo "\nVerification Complete.\n";
} catch (Exception $e) {
    echo "\n[ERROR] Exception: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
} catch (Error $e) {
    echo "\n[ERROR] Fatal Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
