<?php

use App\Core\Database;

class Migration_AddExamTables
{
    public function up()
    {
        $db = Database::getInstance();

        // DROP TABLES TO ENSURE CLEAN STATE
        // Note: We use raw PDO exec if needed, or query. 
        // We disable FK checks momentarily to allow dropping parent first if needed (though dropping child first is better)
        $db->query("SET FOREIGN_KEY_CHECKS=0");
        $db->query("DROP TABLE IF EXISTS exam_answers");
        $db->query("DROP TABLE IF EXISTS exam_sessions");
        $db->query("SET FOREIGN_KEY_CHECKS=1");

        // Exam Sessions Table
        // category_id matches syllabus_nodes.id -> BIGINT(20) UNSIGNED
        // user_id matches users.id -> INT (Signed)
        $db->query("
            CREATE TABLE IF NOT EXISTS exam_sessions (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NULL,
                category_id BIGINT(20) UNSIGNED NULL,
                mode ENUM('practice', 'mock') NOT NULL,
                total_questions INT DEFAULT 0,
                score INT DEFAULT 0,
                status ENUM('started', 'completed') DEFAULT 'started',
                started_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                completed_at DATETIME NULL,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
                FOREIGN KEY (category_id) REFERENCES syllabus_nodes(id) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // Exam Answers Table
        // session_id matches exam_sessions.id (INT)
        // question_id matches quiz_questions.id -> Trying INT UNSIGNED
        $db->query("
             CREATE TABLE IF NOT EXISTS exam_answers (
                id INT AUTO_INCREMENT PRIMARY KEY,
                session_id INT NOT NULL,
                question_id INT UNSIGNED NOT NULL,
                user_answer TEXT NULL,
                is_correct TINYINT(1) DEFAULT 0,
                FOREIGN KEY (session_id) REFERENCES exam_sessions(id) ON DELETE CASCADE,
                FOREIGN KEY (question_id) REFERENCES quiz_questions(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");
    }
}
