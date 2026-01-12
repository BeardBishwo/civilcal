<?php

/**
 * Migration: Create Word Bank Table
 * 
 * Objectives:
 * 1. Store engineering terms and definitions.
 * 2. Support multi-language (Nepali/English).
 * 3. Categorize by difficulty and syllabus section.
 */

if (file_exists(dirname(__DIR__, 2) . '/app/bootstrap.php')) {
    require_once dirname(__DIR__, 2) . '/app/bootstrap.php';
} else {
    require_once dirname(__DIR__) . '/../app/bootstrap.php';
}

use App\Core\Database;

try {
    $db = Database::getInstance();
    $conn = $db->getPdo();

    echo "🔄 Creating `word_bank` table...\n";

    $sql = "CREATE TABLE IF NOT EXISTS `word_bank` (
        `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `term` VARCHAR(255) NOT NULL,
        `definition` TEXT NOT NULL,
        `category_id` INT UNSIGNED NULL,
        `difficulty_level` TINYINT UNSIGNED DEFAULT 1, -- 1: Easy, 2: Easy-Mid, 3: Medium, 4: Hard, 5: Expert
        `language` ENUM('en', 'np') DEFAULT 'en',
        `synonyms` TEXT NULL, -- Comma separated
        `usage_example` TEXT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX (`term`),
        INDEX (`category_id`),
        INDEX (`difficulty_level`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

    $conn->exec($sql);
    echo "✅ Table `word_bank` created successfully.\n";

    // Create blueprint_reveals table to track progress
    echo "🔄 Creating `blueprint_reveals` table...\n";
    $sql2 = "CREATE TABLE IF NOT EXISTS `blueprint_reveals` (
        `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `user_id` INT UNSIGNED NOT NULL,
        `blueprint_id` VARCHAR(100) NOT NULL, -- e.g. 'beam_structure', 'dam_cross_section'
        `revealed_percentage` TINYINT UNSIGNED DEFAULT 0,
        `is_completed` TINYINT(1) DEFAULT 0,
        `last_played_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY `user_blueprint` (`user_id`, `blueprint_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

    $conn->exec($sql2);
    echo "✅ Table `blueprint_reveals` created successfully.\n";
} catch (Exception $e) {
    echo "❌ Migration Error: " . $e->getMessage() . "\n";
    exit(1);
}
