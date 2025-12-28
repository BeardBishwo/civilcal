<?php

use App\Core\Database;

class Migration_Create_Ghost_Mode_Tables
{
    public function up()
    {
        $db = new Database();
        $pdo = $db->getPdo();

        // 1. Bot Profiles
        $sql1 = "
        CREATE TABLE IF NOT EXISTS bot_profiles (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL,
            avatar_url VARCHAR(255) DEFAULT NULL,
            skill_level TINYINT DEFAULT 5, -- 1 to 10
            is_active BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";
        $pdo->exec($sql1);

        // 2. Quiz Lobbies (The Room)
        $sql2 = "
        CREATE TABLE IF NOT EXISTS quiz_lobbies (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            code VARCHAR(10) NOT NULL UNIQUE,
            exam_id BIGINT UNSIGNED NOT NULL,
            host_user_id BIGINT UNSIGNED DEFAULT NULL, -- Who created it
            status ENUM('waiting', 'active', 'finished') DEFAULT 'waiting',
            start_time TIMESTAMP NULL DEFAULT NULL,
            current_question_index INT DEFAULT 0,
            next_question_at TIMESTAMP NULL DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (exam_id) REFERENCES quiz_exams(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";
        $pdo->exec($sql2);

        // 3. Lobby Participants (Real Users + Bots)
        $sql3 = "
        CREATE TABLE IF NOT EXISTS quiz_lobby_participants (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            lobby_id BIGINT UNSIGNED NOT NULL,
            user_id BIGINT UNSIGNED DEFAULT NULL, -- NULL if Bot
            bot_profile_id BIGINT UNSIGNED DEFAULT NULL, -- NULL if Real User
            is_bot BOOLEAN DEFAULT FALSE,
            status ENUM('ready', 'active', 'eliminated', 'finished') DEFAULT 'ready',
            current_score INT DEFAULT 0,
            last_pulse_at TIMESTAMP NULL DEFAULT NULL, -- For heartbeat
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (lobby_id) REFERENCES quiz_lobbies(id) ON DELETE CASCADE,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (bot_profile_id) REFERENCES bot_profiles(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";
        $pdo->exec($sql3);

        echo "Ghost Mode Tables Created Successfully.\n";

        // Seed Bot Profiles
        $this->seedBots($pdo);
    }

    public function down()
    {
        $db = new Database();
        $pdo = $db->getPdo();
        $pdo->exec("DROP TABLE IF EXISTS quiz_lobby_participants");
        $pdo->exec("DROP TABLE IF EXISTS quiz_lobbies");
        $pdo->exec("DROP TABLE IF EXISTS bot_profiles");
        echo "Ghost Mode Tables Dropped.\n";
    }

    private function seedBots($pdo)
    {
        // Check if bots exist
        $stmt = $pdo->query("SELECT COUNT(*) FROM bot_profiles");
        if ($stmt->fetchColumn() > 0) {
            echo "Bots already seeded.\n";
            return;
        }

        $bots = [
            ['Ramesh_Civil', 7], ['Sita_Engineer', 8], ['Govt_Prep_King', 9], ['Newbie_Civil', 3],
            ['Loksewa_Topper', 10], ['Anjali_K', 6], ['Bishwo_Student', 5], ['Kathmandu_Eng', 7],
            ['Pokhara_Civil', 6], ['Engineer_Babu', 4], ['Tech_Guru_Nepal', 8], ['Study_Hard', 9],
            ['Exam_Warrior', 7], ['Civil_Master_2024', 8], ['Bridge_Builder', 6], ['Road_Master', 5],
            ['Concrete_King', 7], ['Survey_Pro', 8], ['Drafting_Queen', 6], ['Site_Manager_X', 7],
            ['Structure_God', 9], ['Water_Res_Eng', 8], ['Geo_Tech_Pro', 7], ['Enviro_Eng', 6],
            ['Urban_Planner_N', 5], ['Transport_Hero', 7], ['Hydropower_Fan', 8], ['Irrigation_Bro', 6],
            ['Nepal_Eng_Council', 9], ['IOE_Topper', 10], ['Pulchowk_Dreamer', 8], ['Thapathali_Rock', 7],
            ['WRC_Sniper', 8], ['ERC_Warrior', 7], ['KEC_Legend', 6], ['NEC_Pro', 7],
            ['Acme_Ace', 6], ['Khwopa_King', 8], ['Himalayan_Eng', 7], ['Everest_Builder', 9],
            ['Lumbini_Learner', 5], ['Janakpur_Eng', 6], ['Biratnagar_Pro', 7], ['Chitwan_Civil', 8],
            ['Dharan_Tech', 7], ['Butwal_Builder', 6], ['Hetauda_Hero', 7], ['Surkhet_Civil', 5],
            ['Dhangadhi_Eng', 6], ['Mahendranagar_Pro', 7]
        ];

        $stmt = $pdo->prepare("INSERT INTO bot_profiles (username, skill_level, avatar_url) VALUES (:name, :skill, :avatar)");
        
        foreach ($bots as $bot) {
            // Using UI Avatars for generic persistent avatars
            $avatar = "https://ui-avatars.com/api/?name=" . urlencode($bot[0]) . "&background=random&color=fff";
            $stmt->execute([
                'name' => $bot[0],
                'skill' => $bot[1],
                'avatar' => $avatar
            ]);
        }
        
        echo "Seeded " . count($bots) . " Bot Profiles.\n";
    }
}
