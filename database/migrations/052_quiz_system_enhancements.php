<?php

class QuizSystemEnhancements
{
    public function up()
    {
        $pdo = \App\Core\Database::getInstance()->getPdo();

        // 1. Add User Study Goals
        echo "Adding study goals to users table...\n";
        try {
            $pdo->exec("ALTER TABLE users ADD COLUMN selected_course_id INT NULL");
            $pdo->exec("ALTER TABLE users ADD COLUMN selected_edu_level_id INT NULL");
        } catch (\PDOException $e) {
            echo "Columns may already exist: " . $e->getMessage() . "\n";
        }

        // 2. Track Syllabus Progress
        echo "Creating user_syllabus_progress table...\n";
        $sql = "CREATE TABLE IF NOT EXISTS user_syllabus_progress (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            syllabus_node_id BIGINT(20) UNSIGNED NOT NULL,
            status ENUM('locked', 'unlocked', 'completed', 'mastered') DEFAULT 'unlocked',
            score INT DEFAULT 0,
            attempts INT DEFAULT 0,
            completed_at TIMESTAMP NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY unique_user_node (user_id, syllabus_node_id),
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (syllabus_node_id) REFERENCES syllabus_nodes(id) ON DELETE CASCADE
        )";
        $pdo->exec($sql);

        // 3. Seed Site Settings for Feature Toggles
        echo "Seeding Feature Toggles...\n";
        $settings = [
            'quiz_mode_daily' => '1',
            'quiz_mode_zone' => '1',
            'quiz_mode_true_false' => '1',
            'quiz_mode_contest' => '1',
            'quiz_mode_battle_1v1' => '0',
            'quiz_mode_battle_group' => '0',
            'quiz_mode_exam' => '1',
            'quiz_mode_math_mania' => '1',
            'quiz_mode_guess_word' => '1',
            'quiz_mode_multi_match' => '1',
        ];

        $sqlInsert = "INSERT IGNORE INTO site_settings (setting_key, setting_value, setting_group) VALUES (:key, :value, 'quiz_modes')";
        $stmt = $pdo->prepare($sqlInsert);

        foreach ($settings as $key => $value) {
            $stmt->execute(['key' => $key, 'value' => $value]);
        }

        echo "✅ Quiz System Enhancements Applied.\n";
    }

    public function down()
    {
        $pdo = \App\Core\Database::getInstance()->getPdo();
        $pdo->exec("DROP TABLE IF EXISTS user_syllabus_progress");
        $pdo->exec("ALTER TABLE users DROP COLUMN selected_course_id");
        $pdo->exec("ALTER TABLE users DROP COLUMN selected_edu_level_id");
        $pdo->exec("DELETE FROM site_settings WHERE group_name = 'quiz_modes'");
    }
}
