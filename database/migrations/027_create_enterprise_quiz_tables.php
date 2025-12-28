<?php
/**
 * Migration: Create Enterprise Quiz Module Tables
 * 
 * Tables:
 * - quiz_categories (Stream/Faculty)
 * - quiz_subjects
 * - quiz_topics
 * - quiz_questions (JSON-based)
 * - quiz_exams (Test definitions)
 * - quiz_exam_questions (Linker)
 * - quiz_attempts (User sessions)
 * - quiz_attempt_answers (User responses)
 */

if (file_exists(dirname(__DIR__, 3) . '/app/bootstrap.php')) {
    require_once dirname(__DIR__, 3) . '/app/bootstrap.php';
} else {
    // Fallback for direct execution if needed
    require_once dirname(__DIR__) . '/../app/bootstrap.php';
}

use App\Core\Database;

try {
    $db = Database::getInstance();
    $conn = $db->getPdo(); // Access PDO directly for transactions

    $conn->beginTransaction();

    // 1. Categories (Streams like "Civil Engineering", "Management")
    $sql_categories = "
    CREATE TABLE IF NOT EXISTS `quiz_categories` (
        `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
        `name` VARCHAR(255) NOT NULL,
        `slug` VARCHAR(255) NOT NULL,
        `image` VARCHAR(255) NULL,
        `description` TEXT NULL,
        `order` INT(11) DEFAULT 0,
        `is_active` TINYINT(1) DEFAULT 1,
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `slug` (`slug`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";
    $conn->exec($sql_categories);
    echo "✅ Created quiz_categories\n";

    // 2. Subjects (e.g., "Soil Mechanics", "Fluid Mechanics")
    $sql_subjects = "
    CREATE TABLE IF NOT EXISTS `quiz_subjects` (
        `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
        `category_id` INT(11) UNSIGNED NOT NULL,
        `name` VARCHAR(255) NOT NULL,
        `slug` VARCHAR(255) NOT NULL,
        `order` INT(11) DEFAULT 0,
        `is_active` TINYINT(1) DEFAULT 1,
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `unique_subject` (`category_id`, `slug`),
        CONSTRAINT `fk_quiz_subjects_category` FOREIGN KEY (`category_id`) REFERENCES `quiz_categories` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";
    $conn->exec($sql_subjects);
    echo "✅ Created quiz_subjects\n";

    // 3. Topics (e.g., "Consolidation", "Shear Strength")
    $sql_topics = "
    CREATE TABLE IF NOT EXISTS `quiz_topics` (
        `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
        `subject_id` INT(11) UNSIGNED NOT NULL,
        `name` VARCHAR(255) NOT NULL,
        `slug` VARCHAR(255) NOT NULL,
        `order` INT(11) DEFAULT 0,
        `is_active` TINYINT(1) DEFAULT 1,
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `unique_topic` (`subject_id`, `slug`),
        CONSTRAINT `fk_quiz_topics_subject` FOREIGN KEY (`subject_id`) REFERENCES `quiz_subjects` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";
    $conn->exec($sql_topics);
    echo "✅ Created quiz_topics\n";

    // 4. Question Bank
    $sql_questions = "
    CREATE TABLE IF NOT EXISTS `quiz_questions` (
        `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
        `unique_code` VARCHAR(50) NULL COMMENT 'Admin reference code',
        `topic_id` INT(11) UNSIGNED NULL,
        `type` ENUM('mcq_single', 'mcq_multi', 'numerical', 'true_false', 'match', 'text') NOT NULL DEFAULT 'mcq_single',
        `content` JSON NOT NULL COMMENT 'Contains question text, images, LaTeX',
        `options` JSON NULL COMMENT 'Array of options with is_correct flag',
        `answer_explanation` LONGTEXT NULL,
        `difficulty_level` TINYINT(1) DEFAULT 3 COMMENT '1=Easy, 5=Hard',
        `default_marks` DECIMAL(5,2) DEFAULT 1.00,
        `default_negative_marks` DECIMAL(5,2) DEFAULT 0.00,
        `tags` JSON NULL,
        `is_active` TINYINT(1) DEFAULT 1,
        `created_by` INT(11) UNSIGNED NULL,
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `unique_code` (`unique_code`),
        KEY `topic_id` (`topic_id`),
        CONSTRAINT `fk_quiz_questions_topic` FOREIGN KEY (`topic_id`) REFERENCES `quiz_topics` (`id`) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";
    $conn->exec($sql_questions);
    echo "✅ Created quiz_questions\n";

    // 5. Exams / Mock Tests
    $sql_exams = "
    CREATE TABLE IF NOT EXISTS `quiz_exams` (
        `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
        `title` VARCHAR(255) NOT NULL,
        `slug` VARCHAR(255) NOT NULL,
        `description` TEXT NULL,
        `type` ENUM('mock_test', 'practice', 'past_paper', 'live_quiz') NOT NULL DEFAULT 'practice',
        `mode` ENUM('exam', 'practice') NOT NULL DEFAULT 'exam',
        `duration_minutes` INT(11) DEFAULT 0 COMMENT '0 = unlimited',
        `total_marks` INT(11) DEFAULT 0,
        `pass_percentage` DECIMAL(5,2) DEFAULT 40.00,
        `negative_marking_rate` DECIMAL(5,2) DEFAULT 0.00 COMMENT 'Deduction per wrong answer',
        `start_datetime` DATETIME NULL COMMENT 'For live scheduled quizzes',
        `end_datetime` DATETIME NULL,
        `is_premium` TINYINT(1) DEFAULT 0,
        `price` DECIMAL(10,2) DEFAULT 0.00,
        `status` ENUM('draft', 'published', 'archived') DEFAULT 'draft',
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `slug` (`slug`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";
    $conn->exec($sql_exams);
    echo "✅ Created quiz_exams\n";

    // 6. Exam-Question Linker
    $sql_exam_questions = "
    CREATE TABLE IF NOT EXISTS `quiz_exam_questions` (
        `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
        `exam_id` INT(11) UNSIGNED NOT NULL,
        `question_id` INT(11) UNSIGNED NOT NULL,
        `order` INT(11) DEFAULT 0,
        `custom_marks` DECIMAL(5,2) NULL,
        PRIMARY KEY (`id`),
        KEY `exam_id` (`exam_id`),
        UNIQUE KEY `unique_exam_question` (`exam_id`, `question_id`),
        CONSTRAINT `fk_quiz_eq_exam` FOREIGN KEY (`exam_id`) REFERENCES `quiz_exams` (`id`) ON DELETE CASCADE,
        CONSTRAINT `fk_quiz_eq_question` FOREIGN KEY (`question_id`) REFERENCES `quiz_questions` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";
    $conn->exec($sql_exam_questions);
    echo "✅ Created quiz_exam_questions\n";

    // 7. Quiz Attempts (User Sessions)
    $sql_attempts = "
    CREATE TABLE IF NOT EXISTS `quiz_attempts` (
        `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
        `user_id` INT(11) NOT NULL,
        `exam_id` INT(11) UNSIGNED NOT NULL,
        `status` ENUM('ongoing', 'completed', 'abandoned') DEFAULT 'ongoing',
        `score` DECIMAL(8,2) DEFAULT 0.00,
        `total_questions_attempted` INT(11) DEFAULT 0,
        `correct_answers` INT(11) DEFAULT 0,
        `wrong_answers` INT(11) DEFAULT 0,
        `accuracy` DECIMAL(5,2) DEFAULT 0.00,
        `time_taken_seconds` INT(11) DEFAULT 0,
        `ip_address` VARCHAR(45) NULL,
        `started_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        `completed_at` DATETIME NULL,
        PRIMARY KEY (`id`),
        KEY `user_id` (`user_id`),
        KEY `exam_id` (`exam_id`),
        CONSTRAINT `fk_quiz_attempts_exam` FOREIGN KEY (`exam_id`) REFERENCES `quiz_exams` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";
    $conn->exec($sql_attempts);
    echo "✅ Created quiz_attempts\n";

    // 8. Attempt Answers (Detailed Responses)
    $sql_attempt_answers = "
    CREATE TABLE IF NOT EXISTS `quiz_attempt_answers` (
        `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
        `attempt_id` INT(11) UNSIGNED NOT NULL,
        `question_id` INT(11) UNSIGNED NOT NULL,
        `selected_options` JSON NULL COMMENT 'IDs of selected options',
        `text_answer` TEXT NULL COMMENT 'For numerical/text questions',
        `is_correct` TINYINT(1) DEFAULT 0,
        `marks_earned` DECIMAL(5,2) DEFAULT 0.00,
        `time_spent_seconds` INT(11) DEFAULT 0,
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `attempt_id` (`attempt_id`),
        KEY `question_id` (`question_id`),
        CONSTRAINT `fk_quiz_aa_attempt` FOREIGN KEY (`attempt_id`) REFERENCES `quiz_attempts` (`id`) ON DELETE CASCADE,
        CONSTRAINT `fk_quiz_aa_question` FOREIGN KEY (`question_id`) REFERENCES `quiz_questions` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";
    $conn->exec($sql_attempt_answers);
    echo "✅ Created quiz_attempt_answers\n";

    $conn->commit();
    echo "🎉 Enterprise Quiz Module Migration Completed Successfully!\n";

} catch (Exception $e) {
    if (isset($conn)) {
        $conn->rollBack();
    }
    echo "❌ Error in migration: " . $e->getMessage() . "\n";
    exit(1);
}
?>
