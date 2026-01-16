<?php

namespace App\Services;

use App\Core\Database;

class BotEngine
{
    private $db;
    private $firebase;

    public function __construct()
    {
        $this->db = \App\Core\Database::getInstance();
        try {
            // Initialize Firebase for writing Bot moves
            $factory = (new \Kreait\Firebase\Factory)
                ->withServiceAccount(\App\Config\Firebase::getCredentialsPath())
                ->withDatabaseUri(\App\Config\Firebase::getConfig()['databaseURL']);
            $this->firebase = $factory->createDatabase();
        } catch (\Exception $e) {
            // Fallback for non-firebase environments or errors
            error_log("BotEngine Firebase Init Error: " . $e->getMessage());
        }
    }

    /**
     * Process Bot Moves for a Lobby (The Pulse)
     * Called by the Host User's browser every 1s during Game
     */
    public function processGamePulse($lobbyId, $questionId, $startTime)
    {
        $bots = $this->getPendingBots($lobbyId, $questionId);

        foreach ($bots as $bot) {
            $this->simulateBotMove($bot, $questionId, $startTime);
        }
    }

    private function getPendingBots($lobbyId, $questionId)
    {
        try {
            return $this->db->fetchAll("
                SELECT p.*, b.skill_level, l.code as room_code 
                FROM quiz_lobby_participants p
                JOIN bot_profiles b ON p.bot_profile_id = b.id
                JOIN quiz_lobbies l ON p.lobby_id = l.id
                WHERE p.lobby_id = :lid 
                AND p.is_bot = 1 
                AND (p.last_answered_index < :qidx OR p.last_answered_index IS NULL)
            ", [
                'lid' => $lobbyId,
                'qidx' => $questionId
            ]);
        } catch (\Exception $e) {
            error_log("BotEngine Query Error: " . $e->getMessage());
            return [];
        }
    }

    private function simulateBotMove($bot, $questionId, $questionStartTime)
    {
        // 1. Calculate Reaction Time (Humanized)
        // Base 3s + Random Variance
        $reactionTime = 3 + rand(0, 4) + ((10 - $bot['skill_level']) * 0.5);

        $now = time();
        $elapsed = $now - $questionStartTime;

        if ($elapsed > $reactionTime) {
            // TIME TO ANSWER
            $this->submitBotAnswer($bot, $questionId);
        }
    }

    private function submitBotAnswer($bot, $questionId)
    {
        // 2. Accuracy Algorithm (Engagement Optimized)
        $accuracyChance = 50 + ($bot['skill_level'] * 5); // Level 1 = 55%, Level 10 = 100% (capped below)

        // EOM Adjustment (Rubber Banding)
        // If bot is winning too hard, nerf it. If losing, buf it.
        // Implementation skipped for brevity, keeping simple skill check
        $accuracyChance = max(20, min(95, $accuracyChance));
        $isCorrect = (rand(0, 100) < $accuracyChance);
        $marks = $isCorrect ? 4 : -1;

        // 3. MySQL Update (System of Record)
        $sql = "UPDATE quiz_lobby_participants 
                SET current_score = current_score + :marks, 
                    last_answered_index = :qidx,
                    last_pulse_at = NOW() 
                WHERE id = :id";

        $this->db->query($sql, [
            'marks' => $marks,
            'qidx' => $questionId,
            'id' => $bot['id']
        ]);

        // 4. Firebase Push (The Visual Event)
        if ($this->firebase) {
            try {
                $updates = [
                    "current_score" => $bot['current_score'] + $marks,
                    "last_move" => [
                        "q_index" => $questionId,
                        "correct" => $isCorrect,
                        "timestamp" => time() * 1000
                    ]
                ];

                // Update specific bot in the room
                $this->firebase->getReference("rooms/{$bot['room_code']}/players/{$bot['user_id']}")
                    ->update($updates);
            } catch (\Exception $e) {
                error_log("BotFirebase Push Error: " . $e->getMessage());
            }
        }
    }
}
