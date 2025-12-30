<?php
namespace App\Services;

use App\Core\Database;

class RateLimiter
{
    private $db;
    private $limits = [
        '/api/shop/purchase' => ['requests' => 10, 'window' => 60], // 10 per minute
        '/api/shop/purchase-resource' => ['requests' => 10, 'window' => 60],
        '/api/shop/sell-resource' => ['requests' => 10, 'window' => 60],
        '/api/shop/purchase-bundle' => ['requests' => 5, 'window' => 60],
        '/api/city/craft' => ['requests' => 20, 'window' => 60],
        'default' => ['requests' => 30, 'window' => 60] // Default: 30 per minute
    ];

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Check if user has exceeded rate limit for endpoint
     * @return array ['allowed' => bool, 'remaining' => int, 'reset_in' => int]
     */
    public function check($userId, $endpoint)
    {
        $limit = $this->limits[$endpoint] ?? $this->limits['default'];
        $maxRequests = $limit['requests'];
        $windowSeconds = $limit['window'];

        // Get current rate limit record
        $sql = "SELECT * FROM rate_limits 
                WHERE user_id = :uid AND endpoint = :endpoint 
                AND window_start > DATE_SUB(NOW(), INTERVAL :window SECOND)";
        
        $result = $this->db->query($sql, [
            'uid' => $userId,
            'endpoint' => $endpoint,
            'window' => $windowSeconds
        ])->fetch();

        if (!$result) {
            // First request in this window
            $this->db->query(
                "INSERT INTO rate_limits (user_id, endpoint, request_count, window_start) 
                 VALUES (:uid, :endpoint, 1, NOW())",
                ['uid' => $userId, 'endpoint' => $endpoint]
            );
            
            return [
                'allowed' => true,
                'remaining' => $maxRequests - 1,
                'reset_in' => $windowSeconds
            ];
        }

        // Check if limit exceeded
        if ($result['request_count'] >= $maxRequests) {
            $windowStart = strtotime($result['window_start']);
            $resetIn = $windowSeconds - (time() - $windowStart);
            
            // Log rate limit violation
            SecurityMonitor::log($userId, 'rate_limit_exceeded', $endpoint, [
                'requests' => $result['request_count'],
                'limit' => $maxRequests
            ], 'medium');
            
            return [
                'allowed' => false,
                'remaining' => 0,
                'reset_in' => max(0, $resetIn)
            ];
        }

        // Increment counter
        $this->db->query(
            "UPDATE rate_limits 
             SET request_count = request_count + 1, last_request = NOW() 
             WHERE id = :id",
            ['id' => $result['id']]
        );

        return [
            'allowed' => true,
            'remaining' => $maxRequests - ($result['request_count'] + 1),
            'reset_in' => $windowSeconds
        ];
    }

    /**
     * Reset rate limit for user (admin function)
     */
    public function reset($userId, $endpoint = null)
    {
        if ($endpoint) {
            $this->db->query(
                "DELETE FROM rate_limits WHERE user_id = :uid AND endpoint = :endpoint",
                ['uid' => $userId, 'endpoint' => $endpoint]
            );
        } else {
            $this->db->query(
                "DELETE FROM rate_limits WHERE user_id = :uid",
                ['uid' => $userId]
            );
        }
    }

    /**
     * Clean up old rate limit records (run via cron)
     */
    public static function cleanup()
    {
        $db = Database::getInstance();
        $db->query("DELETE FROM rate_limits WHERE window_start < DATE_SUB(NOW(), INTERVAL 1 HOUR)");
    }
}
