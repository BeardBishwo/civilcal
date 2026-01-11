<?php

namespace App\Services;

use App\Core\Database;

class BotEngine
{
    private $db;

    public function __construct()
    {
        $this->db = \App\Core\Database::getInstance();
    }

    /**
     * Process Bot Moves for a Lobby (The Pulse)
     * Called by the Host User's browser every 1s during Game
     */
    public function processGamePulse($lobbyId, $questionId, $startTime)
    {
        // 1. Get all Bots in this lobby who haven't answered this question yet
        // Note: We need a way to track "Answered Questions". 
        // For MVP, checking 'quiz_attempt_answers' table would be heavy if we don't have a 'current_question' context.
        // Assuming we pass $questionId.
        
        $bots = $this->getPendingBots($lobbyId, $questionId);
        
        foreach ($bots as $bot) {
            $this->simulateBotMove($bot, $questionId, $startTime);
        }
    }

    private function getPendingBots($lobbyId, $questionId)
    {
        try {
            return $this->db->query("
                SELECT p.*, b.skill_level 
                FROM quiz_lobby_participants p
                JOIN bot_profiles b ON p.bot_profile_id = b.id
                WHERE p.lobby_id = :lid 
                AND p.is_bot = 1 
                AND (p.last_answered_index < :qidx OR p.last_answered_index IS NULL)
            ", [
                'lid' => $lobbyId,
                'qidx' => $questionId 
            ])->fetchAll();
        } catch (\Exception $e) {
            error_log("BotEngine Query Error: " . $e->getMessage());
            return []; // Safe fallback
        }
    }

    private function simulateBotMove($bot, $questionId, $questionStartTime)
    {
        // 1. Calculate Reaction Time
        // Formula: Base 2s + Random(0-5s) + (10 - Skill)*0.5s
        // Higher skill = Faster? Or more human (med speed)?
        // Let's say Humanizer: 
        $reactionTime = 2 + rand(0, 5) + ((10 - $bot['skill_level']) * 0.3);
        
        $now = time();
        $elapsed = $now - $questionStartTime;

        if ($elapsed > $reactionTime) {
            // TIME TO ANSWER
            $this->submitBotAnswer($bot, $questionId);
        }
    }

    private function submitBotAnswer($bot, $questionId)
    {
        // 2. Accuracy Algorithm (EOM - Engagement Optimized Matchmaking)
        // If Host is losing significantly, bots become "dumber" to encourage a comeback.
        
        $accuracyChance = $bot['skill_level'] * 10; // Base: Level 5 = 50%
        
        // EOM Adjustment
        $host = $this->db->query("
            SELECT current_score 
            FROM quiz_lobby_participants 
            WHERE lobby_id = :lid AND is_bot = 0 
            LIMIT 1
        ", ['lid' => $bot['lobby_id']])->fetch();

        if ($host) {
            $diff = $bot['current_score'] - $host['current_score'];
            if ($diff > 10) {
                // Bot is winning too hard, drop accuracy by 30%
                $accuracyChance -= 30;
            } elseif ($diff < -10) {
                // Bot is losing too hard, boost accuracy to keep pressure
                $accuracyChance += 20;
            }
        }
        
        // Ensure range 10-90%
        $accuracyChance = max(10, min(90, $accuracyChance));
        
        $isCorrect = (rand(0, 100) < $accuracyChance);
        
        // Marks
        $marks = $isCorrect ? 4 : -1; // Standard rule
        
        // Update Bot Score and Action Log for Pressure Toasts
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
    }
}
