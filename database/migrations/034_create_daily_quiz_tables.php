<?php
/**
 * Migration: Create Daily Quiz & Streak Tables
 * 
 * This migration creates the tables for the Daily Quest Engine:
 * - daily_quiz_schedule: Stores auto-generated daily quizzes
 * - user_streaks: Tracks user participation streaks and multipliers
 * - daily_quiz_attempts: Logs user attempts to prevent duplicates
 */

if (file_exists(dirname(__DIR__, 3) . '/app/bootstrap.php')) {
    require_once dirname(__DIR__, 3) . '/app/bootstrap.php';
} else {
    require_once dirname(__DIR__) . '/../app/bootstrap.php';
}

use App\Core\Database;

try {
    $db = Database::getInstance();
    $conn = $db->getPdo();

    $conn->beginTransaction();

    // 1. Daily Quiz Schedule
    $sql_daily_schedule = "
    CREATE TABLE IF NOT EXISTS `daily_quiz_schedule` (
        `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        `date` DATE NOT NULL COMMENT 'e.g., 2026-01-04',
        `target_stream_id` BIGINT(20) UNSIGNED NULL COMMENT 'Link to syllabus_nodes (Part type). Null = General',
        `questions` JSON NOT NULL COMMENT 'Array of 10 Question IDs',
        `reward_coins` INT(11) DEFAULT 50,
        `is_active` TINYINT(1) DEFAULT 1,
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `unique_daily` (`date`, `target_stream_id`),
        KEY `date` (`date`),
        KEY `target_stream_id` (`target_stream_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";
    $conn->exec($sql_daily_schedule);
    echo "✅ Created daily_quiz_schedule\n";

    // 2. User Streaks
    $sql_user_streaks = "
    CREATE TABLE IF NOT EXISTS `user_streaks` (
        `user_id` INT(11) NOT NULL,
        `current_streak` INT(11) DEFAULT 0,
        `highest_streak` INT(11) DEFAULT 0,
        `last_activity_date` DATE NULL COMMENT 'Last time they played Daily Quiz',
        `streak_freeze_left` INT(11) DEFAULT 0 COMMENT 'Power-up: Allows missing a day',
        `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`user_id`),
        CONSTRAINT `fk_streaks_user` FOREIGN KEY (`user_id`) 
            REFERENCES `users` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";
    $conn->exec($sql_user_streaks);
    echo "✅ Created user_streaks\n";

    // 3. Daily Quiz Attempts (History)
    $sql_attempts = "
    CREATE TABLE IF NOT EXISTS `daily_quiz_attempts` (
        `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        `user_id` INT(11) NOT NULL,
        `daily_quiz_id` BIGINT(20) UNSIGNED NOT NULL,
        `score` INT(11) NOT NULL,
        `coins_earned` INT(11) DEFAULT 0,
        `completed_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `user_id` (`user_id`),
        KEY `daily_quiz_id` (`daily_quiz_id`),
        UNIQUE KEY `unique_attempt` (`user_id`, `daily_quiz_id`),
        CONSTRAINT `fk_daily_attempts_user` FOREIGN KEY (`user_id`) 
            REFERENCES `users` (`id`) ON DELETE CASCADE,
        CONSTRAINT `fk_daily_attempts_quiz` FOREIGN KEY (`daily_quiz_id`) 
            REFERENCES `daily_quiz_schedule` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";
    $conn->exec($sql_attempts);
    echo "✅ Created daily_quiz_attempts\n";

    $conn->commit();
    echo "🎉 Daily Quest Migration Completed Successfully!\n";

} catch (Exception $e) {
    if (isset($conn)) {
        $conn->rollBack();
    }
    echo "❌ Error in migration: " . $e->getMessage() . "\n";
    exit(1);
}
?>
