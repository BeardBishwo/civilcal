<?php
namespace App\Services;

use App\Core\Database;

class NonceService
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Generate a new nonce for quiz session
     */
    public function generate($userId, $quizType = 'general')
    {
        $sessionId = bin2hex(random_bytes(32));
        $nonce = bin2hex(random_bytes(32));

        $this->db->query(
            "INSERT INTO quiz_sessions (user_id, session_id, nonce, quiz_type) 
             VALUES (:uid, :sid, :nonce, :type)",
            [
                'uid' => $userId,
                'sid' => $sessionId,
                'nonce' => $nonce,
                'type' => $quizType
            ]
        );

        return [
            'session_id' => $sessionId,
            'nonce' => $nonce
        ];
    }

    /**
     * Validate and consume nonce (one-time use)
     */
    public function validateAndConsume($nonce, $userId, $quizType = null)
    {
        // Find nonce
        $criteria = [
            'nonce' => $nonce,
            'user_id' => $userId
        ];

        if ($quizType !== null) {
            $criteria['quiz_type'] = $quizType;
        }

        $session = $this->db->findOne('quiz_sessions', $criteria);

        if (!$session) {
            SecurityMonitor::log($userId, 'invalid_nonce', '', ['nonce' => substr($nonce, 0, 10)], 'high');
            return false;
        }

        // Check if already consumed
        if ($session['is_consumed']) {
            SecurityMonitor::log($userId, 'nonce_replay_attempt', '', [
                'nonce' => substr($nonce, 0, 10),
                'consumed_at' => $session['consumed_at']
            ], 'critical');
            return false;
        }

        // Check if expired (30 minutes)
        $createdAt = strtotime($session['created_at']);
        if (time() - $createdAt > 1800) {
            SecurityMonitor::log($userId, 'expired_nonce', '', ['nonce' => substr($nonce, 0, 10)], 'medium');
            return false;
        }

        // Consume nonce
        $this->db->query(
            "UPDATE quiz_sessions 
             SET is_consumed = 1, consumed_at = NOW() 
             WHERE id = :id",
            ['id' => $session['id']]
        );

        return true;
    }

    /**
     * Clean up old sessions (run via cron)
     */
    public static function cleanup()
    {
        $db = Database::getInstance();
        // Delete sessions older than 24 hours
        $db->query("DELETE FROM quiz_sessions WHERE created_at < DATE_SUB(NOW(), INTERVAL 24 HOUR)");
    }
}
