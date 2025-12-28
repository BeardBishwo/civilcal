<?php

namespace App\Database\Migrations;

use App\Core\Database;

class AddShuffleToExams
{
    public function up()
    {
        $db = new Database();
        $pdo = $db->getPdo();
        
        // Add shuffle_questions column if it doesn't exist
        $sql = "SHOW COLUMNS FROM `quiz_exams` LIKE 'shuffle_questions'";
        $stmt = $pdo->query($sql);
        
        if ($stmt->rowCount() == 0) {
            $pdo->exec("ALTER TABLE `quiz_exams` ADD COLUMN `shuffle_questions` TINYINT(1) DEFAULT 0 AFTER `negative_marking_rate`");
            echo "Added shuffle_questions column to quiz_exams table.\n";
        } else {
            echo "Column shuffle_questions already exists.\n";
        }
    }

    public function down()
    {
        $db = new Database();
        $pdo = $db->getPdo();
        $pdo->exec("ALTER TABLE `quiz_exams` DROP COLUMN `shuffle_questions`");
    }
}
