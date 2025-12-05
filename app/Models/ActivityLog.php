<?php

namespace App\Models;

use App\Core\Database;
use Exception;

class ActivityLog
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Find an activity log by ID
     */
    public function find($id)
    {
        $stmt = $this->db->getPdo()->prepare("SELECT * FROM activity_logs WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Get all activity logs with optional filters
     */
    public function getAll($filters = [], $page = 1, $perPage = 20)
    {
        $whereClause = "WHERE 1=1";
        $params = [];

        if (!empty($filters['user_id'])) {
            $whereClause .= " AND user_id = ?";
            $params[] = $filters['user_id'];
        }

        if (!empty($filters['action'])) {
            $whereClause .= " AND action = ?";
            $params[] = $filters['action'];
        }

        if (!empty($filters['activity_type'])) {
            $whereClause .= " AND activity_type = ?";
            $params[] = $filters['activity_type'];
        }

        if (!empty($filters['date_from'])) {
            $whereClause .= " AND created_at >= ?";
            $params[] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $whereClause .= " AND created_at <= ?";
            $params[] = $filters['date_to'];
        }

        if (!empty($filters['search'])) {
            $whereClause .= " AND (action LIKE ? OR description LIKE ?)";
            $searchTerm = "%{$filters['search']}%";
            $params = array_merge($params, [$searchTerm, $searchTerm]);
        }

        // Count total
        $countStmt = $this->db->getPdo()->prepare("SELECT COUNT(*) as total FROM activity_logs $whereClause");
        $countStmt->execute($params);
        $total = $countStmt->fetch()['total'];

        // Get logs with pagination
        $offset = ($page - 1) * $perPage;
        $stmt = $this->db->getPdo()->prepare("
            SELECT * FROM activity_logs
            $whereClause
            ORDER BY created_at DESC
            LIMIT $perPage OFFSET $offset
        ");
        $stmt->execute($params);
        $logs = $stmt->fetchAll();

        return [
            'logs' => $logs,
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'total_pages' => ceil($total / $perPage)
        ];
    }

    /**
     * Create a new activity log entry
     */
    public function create($data)
    {
        $stmt = $this->db->getPdo()->prepare("
            INSERT INTO activity_logs (
                user_id, 
                action, 
                activity_type, 
                description, 
                metadata, 
                ip_address, 
                user_agent, 
                created_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
        ");

        $result = $stmt->execute([
            $data['user_id'] ?? null,
            $data['action'] ?? null,
            $data['activity_type'] ?? 'general',
            $data['description'] ?? null,
            !empty($data['metadata']) ? json_encode($data['metadata']) : null,
            $data['ip_address'] ?? $_SERVER['REMOTE_ADDR'] ?? null,
            $data['user_agent'] ?? $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);

        if ($result) {
            return $this->db->getPdo()->lastInsertId();
        }
        
        return false;
    }

    /**
     * Log a user activity
     */
    public function logActivity($userId, $action, $activityType = 'general', $description = '', $metadata = [])
    {
        $data = [
            'user_id' => $userId,
            'action' => $action,
            'activity_type' => $activityType,
            'description' => $description,
            'metadata' => $metadata,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
        ];

        return $this->create($data);
    }

    /**
     * Log user login activity
     */
    public function logLogin($userId, $username = null)
    {
        $description = $username ? "User {$username} logged in" : "User logged in";
        return $this->logActivity($userId, 'login', 'authentication', $description);
    }

    /**
     * Log user logout activity
     */
    public function logLogout($userId, $username = null)
    {
        $description = $username ? "User {$username} logged out" : "User logged out";
        return $this->logActivity($userId, 'logout', 'authentication', $description);
    }

    /**
     * Log user registration activity
     */
    public function logRegistration($userId, $username = null)
    {
        $description = $username ? "New user {$username} registered" : "New user registered";
        return $this->logActivity($userId, 'registration', 'authentication', $description);
    }

    /**
     * Log admin panel access
     */
    public function logAdminAccess($userId, $page = null)
    {
        $description = $page ? "Accessed admin panel - {$page}" : "Accessed admin panel";
        return $this->logActivity($userId, 'admin_access', 'admin', $description);
    }

    /**
     * Log system activity
     */
    public function logSystem($action, $description, $metadata = [])
    {
        return $this->logActivity(null, $action, 'system', $description, $metadata);
    }

    /**
     * Get activity logs for a specific user
     */
    public function getByUser($userId, $limit = 50)
    {
        $stmt = $this->db->getPdo()->prepare("
            SELECT * FROM activity_logs 
            WHERE user_id = ? 
            ORDER BY created_at DESC
            LIMIT $limit
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    /**
     * Get recent activity logs
     */
    public function getRecent($limit = 50)
    {
        $stmt = $this->db->getPdo()->prepare("
            SELECT * FROM activity_logs 
            ORDER BY created_at DESC
            LIMIT $limit
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get activity logs by type
     */
    public function getByType($activityType, $limit = 50)
    {
        $stmt = $this->db->getPdo()->prepare("
            SELECT * FROM activity_logs 
            WHERE activity_type = ? 
            ORDER BY created_at DESC
            LIMIT $limit
        ");
        $stmt->execute([$activityType]);
        return $stmt->fetchAll();
    }

    /**
     * Delete activity logs older than specified date
     */
    public function deleteOlderThan($date)
    {
        $stmt = $this->db->getPdo()->prepare("DELETE FROM activity_logs WHERE created_at < ?");
        return $stmt->execute([$date]);
    }

    /**
     * Get activity log statistics
     */
    public function getStats()
    {
        $stmt = $this->db->getPdo()->query("
            SELECT 
                COUNT(*) as total_logs,
                COUNT(DISTINCT user_id) as unique_users,
                (SELECT action FROM activity_logs ORDER BY created_at DESC LIMIT 1) as latest_action,
                (SELECT COUNT(*) FROM activity_logs WHERE activity_type = 'authentication') as auth_logs,
                (SELECT COUNT(*) FROM activity_logs WHERE activity_type = 'admin') as admin_logs
            FROM activity_logs
        ");
        return $stmt->fetch();
    }

    /**
     * Get activity counts by type
     */
    public function getActivityTypeCounts()
    {
        $stmt = $this->db->getPdo()->query("
            SELECT 
                activity_type,
                COUNT(*) as count
            FROM activity_logs
            GROUP BY activity_type
            ORDER BY count DESC
        ");
        return $stmt->fetchAll();
    }

    /**
     * Get top actions
     */
    public function getTopActions($limit = 10)
    {
        $stmt = $this->db->getPdo()->query("
            SELECT 
                action,
                COUNT(*) as count
            FROM activity_logs
            GROUP BY action
            ORDER BY count DESC
            LIMIT $limit
        ");
        return $stmt->fetchAll();
    }

    /**
     * Get user activity timeline
     */
    public function getUserTimeline($userId, $days = 30)
    {
        $dateThreshold = date('Y-m-d H:i:s', strtotime("-{$days} days"));

        $stmt = $this->db->getPdo()->prepare("
            SELECT * FROM activity_logs
            WHERE user_id = ? AND created_at >= ?
            ORDER BY created_at DESC
        ");
        $stmt->execute([$userId, $dateThreshold]);
        return $stmt->fetchAll();
    }

    /**
     * Get activity count for various periods
     */
    public function getActivityCount($period = 'all', $additionalFilters = [])
    {
        $whereClause = "WHERE 1=1";
        $params = [];

        switch ($period) {
            case 'today':
                $whereClause .= " AND DATE(created_at) = CURDATE()";
                break;
            case 'week':
                $whereClause .= " AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
                break;
            case 'month':
                $whereClause .= " AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
                break;
        }

        // Apply additional filters if provided
        if (!empty($additionalFilters['activity_type'])) {
            $whereClause .= " AND activity_type = ?";
            $params[] = $additionalFilters['activity_type'];
        }

        if (!empty($additionalFilters['date_from'])) {
            $whereClause .= " AND created_at >= ?";
            $params[] = $additionalFilters['date_from'];
        }

        if (!empty($additionalFilters['date_to'])) {
            $whereClause .= " AND created_at <= ?";
            $params[] = $additionalFilters['date_to'];
        }

        if (!empty($additionalFilters['user_id'])) {
            $whereClause .= " AND user_id = ?";
            $params[] = $additionalFilters['user_id'];
        }

        $stmt = $this->db->getPdo()->prepare("SELECT COUNT(*) as count FROM activity_logs $whereClause");
        $stmt->execute($params);
        $result = $stmt->fetch();

        return $result ? (int)$result['count'] : 0;
    }

    /**
     * Get database connection
     */
    public function getDb()
    {
        return $this->db;
    }
}