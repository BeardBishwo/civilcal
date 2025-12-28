<?php

namespace App\Services;

use App\Core\Database;
use Exception;

class LobbyService
{
    private $db;
    private $botEngine;

    public function __construct()
    {
        $this->db = \App\Core\Database::getInstance();
        $this->botEngine = new BotEngine();
    }

    /**
     * Create a New Lobby
     */
    public function createLobby($examId, $hostUserId)
    {
        $code = strtoupper(substr(md5(uniqid()), 0, 5));
        $sql = "INSERT INTO quiz_lobbies (code, exam_id, host_user_id, status, start_time) 
                VALUES (:code, :eid, :uid, 'waiting', :start)";
        
        $startTime = date('Y-m-d H:i:s', time() + 30);
        
        $this->db->query($sql, [
            'code' => $code,
            'eid' => $examId,
            'uid' => $hostUserId,
            'start' => $startTime
        ]);
        
        $lobbyId = $this->db->getPdo()->lastInsertId();
        $this->joinLobby($code, $hostUserId);
        
        return ['lobby_id' => $lobbyId, 'code' => $code];
    }

    /**
     * Join an existing Lobby
     */
    public function joinLobby($code, $userId)
    {
        $lobby = $this->db->find('quiz_lobbies', ['code' => $code]);
        if (!$lobby) {
            throw new Exception("Lobby not found");
        }
        
        $exists = $this->db->find('quiz_lobby_participants', ['lobby_id' => $lobby['id'], 'user_id' => $userId]);
        if ($exists) {
            return $lobby;
        }

        $this->db->query("INSERT INTO quiz_lobby_participants (lobby_id, user_id, status) VALUES (:lid, :uid, 'ready')", [
            'lid' => $lobby['id'],
            'uid' => $userId
        ]);
        
        return $lobby;
    }

    /**
     * Get Lobby Status (The Pulse)
     */
    public function getLobbyStatus($lobbyId, $currentUserId)
    {

        if (!$lobby) return null;

        $participants = $this->getParticipants($lobbyId);
        
        // 1. Ghost Injection
        $this->checkGhostInjection($lobby, count($participants));
        
        // 2. Payout Protocol (If game finished)
        if ($lobby['status'] === 'finished' && $lobby['payout_distributed'] == 0) {
            $this->distributeWagerRewards($lobbyId);
            $lobby['payout_distributed'] = 1; // Update local copy
        }

        // 3. Refresh participants
        $participants = $this->getParticipants($lobbyId);

        // 4. Bot Pressure Events
        $events = [];
        foreach ($participants as $p) {
            if ($p['is_bot'] && !empty($p['last_pulse_at'])) {
                $lastAnswered = strtotime($p['last_pulse_at']);
                if (time() - $lastAnswered <= 3) {
                    $events[] = [
                        'type' => 'bot_answered',
                        'name' => $p['name'],
                        'message' => "{$p['name']} just answered!"
                    ];
                }
            }
        }

        return [
            'lobby' => $lobby,
            'participants' => $participants,
            'events' => $events,
            'time_remaining' => strtotime($lobby['start_time']) - time()
        ];
    }

    private function distributeWagerRewards($lobbyId)
    {
        $this->db->query("UPDATE quiz_lobbies SET payout_distributed = 1 WHERE id = :id", ['id' => $lobbyId]);

        $participants = $this->getParticipants($lobbyId);
        usort($participants, function($a, $b) {
            return $b['current_score'] - $a['current_score'];
        });

        $winners = array_slice($participants, 0, 3);
        $ms = new MissionService();

        foreach ($winners as $p) {
            if ($p['is_bot']) continue;

            if ($p['wager_amount'] > 0) {
                $reward = $p['wager_amount'] * 2;
                $this->db->query("UPDATE user_resources SET coins = coins + :amt WHERE user_id = :uid", [
                    'amt' => $reward,
                    'uid' => $p['user_id']
                ]);
            }
            $ms->updateProgress($p['user_id'], 'win_battles');
        }
    }

    public function getParticipants($lobbyId)
    {
        $sql = "
            SELECT p.*, 
                   COALESCE(u.username, b.username) as name,
                   COALESCE(b.avatar_url, 'default.png') as avatar,
                   b.skill_level
            FROM quiz_lobby_participants p
            LEFT JOIN users u ON p.user_id = u.id
            LEFT JOIN bot_profiles b ON p.bot_profile_id = b.id
            WHERE p.lobby_id = :lid
        ";
        return $this->db->fetchAll($sql, ['lid' => $lobbyId]);
    }

    private function checkGhostInjection($lobby, $currentCount)
    {
        if ($lobby['status'] !== 'waiting') return;
        $timeLeft = strtotime($lobby['start_time']) - time();
        $targetPlayers = 4;
        
        if ($timeLeft <= 10 && $timeLeft > 0 && $currentCount < $targetPlayers) {
            $needed = $targetPlayers - $currentCount;
            $this->injectBots($lobby['id'], $needed);
        }
    }

    private function injectBots($lobbyId, $count)
    {
        $sql = "SELECT id FROM bot_profiles WHERE is_active = 1 ORDER BY RAND() LIMIT $count";
        $bots = $this->db->fetchAll($sql);
        
        foreach ($bots as $bot) {
            try {
                $check = $this->db->find('quiz_lobby_participants', ['lobby_id' => $lobbyId, 'bot_profile_id' => $bot['id']]);
                if (!$check) {
                    $this->db->query("INSERT INTO quiz_lobby_participants (lobby_id, bot_profile_id, is_bot, status) VALUES (:lid, :bid, 1, 'ready')", [
                        'lid' => $lobbyId,
                        'bid' => $bot['id']
                    ]);
                }
            } catch (Exception $e) {}
        }
    }
}
