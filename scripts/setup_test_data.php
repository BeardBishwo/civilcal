<?php
require 'app/bootstrap.php';

try {
    $db = \App\Core\Database::getInstance()->getPdo();
    
    echo "--- 1. GRANTING RESOURCES TO BISHWO (ID: 3) ---\n";
    $db->exec("UPDATE user_resources SET coins = 999999, bricks = 999999, cement = 999999, steel = 999999, sand = 999999, wood_logs = 999999, wood_planks = 999999 WHERE user_id = 3");
    $db->exec("UPDATE users SET coins = 999999 WHERE id = 3");
    echo "Resources successfully granted to Bishwo.\n";
    
    echo "\n--- 2. CREATING 20 REALISTIC DUMMY USERS ---\n";
    
    $firstNames = ['Aria', 'Ethan', 'Sophia', 'Liam', 'Olivia', 'Noah', 'Ava', 'Lucas', 'Mia', 'Mason', 'Isabella', 'Logan', 'Riley', 'Caleb', 'Zoe', 'Owen', 'Lily', 'Jack', 'Layla', 'Wyatt'];
    $lastNames = ['Smith', 'Johnson', 'Williams', 'Brown', 'Jones', 'Garcia', 'Miller', 'Davis', 'Rodriguez', 'Martinez', 'Hernandez', 'Lopez', 'Gonzalez', 'Wilson', 'Anderson', 'Thomas', 'Taylor', 'Moore', 'Jackson', 'Martin'];
    
    $userIds = [];
    for ($i = 0; $i < 20; $i++) {
        $fname = $firstNames[$i];
        $lname = $lastNames[$i];
        $username = strtolower($fname) . rand(10, 99);
        $email = $username . "@example.com";
        
        $stmt = $db->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $existing = $stmt->fetch();
        
        if ($existing) {
            $userIds[] = $existing['id'];
            echo "User $username already exists (ID: {$existing['id']})\n";
        } else {
            $stmt = $db->prepare("INSERT INTO users (username, first_name, last_name, password, email, is_active, role) VALUES (?, ?, ?, 'dummy', ?, 1, 'user')");
            $stmt->execute([$username, $fname, $lname, $email]);
            $newId = $db->lastInsertId();
            $userIds[] = $newId;
            echo "Created User $username (ID: $newId)\n";
        }
    }
    
    // Add previously created dummy users if they exist
    $prevUsers = ['ProEngineer', 'QuizMaster', 'DesignKing', 'CodeNinja', 'Bishwo_Test'];
    foreach ($prevUsers as $pu) {
        $stmt = $db->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$pu]);
        $row = $stmt->fetch();
        if ($row) $userIds[] = $row['id'];
    }
    
    // Also include Bishwo
    $userIds[] = 3;
    $userIds = array_unique($userIds);
    
    echo "\n--- 3. GENERATING LEADERBOARD DATA FOR " . count($userIds) . " USERS ---\n";
    $periods = [
        ['type' => 'weekly', 'value' => date('Y-W')],
        ['type' => 'monthly', 'value' => date('Y-m')],
        ['type' => 'yearly', 'value' => date('Y')]
    ];
    
    foreach ($periods as $p) {
        // Clear existing for these specific periods
        $db->prepare("DELETE FROM quiz_leaderboard_aggregates WHERE period_type = ? AND period_value = ?")->execute([$p['type'], $p['value']]);
        
        echo "Processing {$p['type']} ({$p['value']})...\n";
        foreach ($userIds as $uid) {
            $score = rand(500, 8000);
            $tests = rand(2, 50);
            $acc = rand(60, 99);
            
            $sqlLb = "INSERT INTO quiz_leaderboard_aggregates 
                      (user_id, period_type, period_value, category_id, total_score, tests_taken, accuracy_avg)
                      VALUES (:uid, :ptype, :pval, 0, :score, :tests, :acc)";
            
            $db->prepare($sqlLb)->execute([
                'uid' => $uid,
                'ptype' => $p['type'],
                'pval' => $p['value'],
                'score' => $score,
                'tests' => $tests,
                'acc' => $acc
            ]);
        }
    }
    
    echo "\n--- 4. CLEARING LEADERBOARD CACHE ---\n";
    $db->exec("DELETE FROM leaderboard_cache");
    echo "Cache cleared.\n";
    
    echo "\nSUCCESS: Expanded test data generation completed!\n";
    
} catch (Exception $e) {
    echo "CRITICAL ERROR: " . $e->getMessage() . "\n";
}
