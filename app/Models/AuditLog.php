<?php

namespace App\Models;

use App\Core\Database;
use Exception;

class AuditLog
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Find an audit log by ID
     */
    public function find($id)
    {
        $stmt = $this->db->getPdo()->prepare("SELECT * FROM audit_logs WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Get all audit logs with optional filters
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

        if (!empty($filters['entity_type'])) {
            $whereClause .= " AND entity_type = ?";
            $params[] = $filters['entity_type'];
        }

        if (!empty($filters['entity_id'])) {
            $whereClause .= " AND entity_id = ?";
            $params[] = $filters['entity_id'];
        }

        if (!empty($filters['date_from'])) {
            $whereClause .= " AND created_at >= ?";
            $params[] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $whereClause .= " AND created_at <= ?";
            $params[] = $filters['date_to'];
        }

        // Count total
        $countStmt = $this->db->getPdo()->prepare("SELECT COUNT(*) as total FROM audit_logs $whereClause");
        $countStmt->execute($params);
        $total = $countStmt->fetch()['total'];

        // Get logs with pagination
        $offset = ($page - 1) * $perPage;
        $stmt = $this->db->getPdo()->prepare("
            SELECT * FROM audit_logs
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
     * Create a new audit log entry
     */
    public function create($data)
    {
        $stmt = $this->db->getPdo()->prepare("
            INSERT INTO audit_logs (
                user_id, 
                action, 
                entity_type, 
                entity_id, 
                old_values, 
                new_values, 
                ip_address, 
                user_agent, 
                created_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ");

        $result = $stmt->execute([
            $data['user_id'] ?? null,
            $data['action'] ?? null,
            $data['entity_type'] ?? null,
            $data['entity_id'] ?? null,
            !empty($data['old_values']) ? json_encode($data['old_values']) : null,
            !empty($data['new_values']) ? json_encode($data['new_values']) : null,
            $data['ip_address'] ?? $_SERVER['REMOTE_ADDR'] ?? null,
            $data['user_agent'] ?? $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);

        if ($result) {
            return $this->db->getPdo()->lastInsertId();
        }
        
        return false;
    }

    /**
     * Log an action with old and new values
     */
    public function logAction($userId, $action, $entityType, $entityId, $oldValues = null, $newValues = null)
    {
        $data = [
            'user_id' => $userId,
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
        ];

        return $this->create($data);
    }

    /**
     * Get audit logs for a specific entity
     */
    public function getByEntity($entityType, $entityId)
    {
        $stmt = $this->db->getPdo()->prepare("
            SELECT * FROM audit_logs 
            WHERE entity_type = ? AND entity_id = ? 
            ORDER BY created_at DESC
        ");
        $stmt->execute([$entityType, $entityId]);
        return $stmt->fetchAll();
    }

    /**
     * Get audit logs for a specific user
     */
    public function getByUser($userId, $limit = 50)
    {
        $stmt = $this->db->getPdo()->prepare("
            SELECT * FROM audit_logs 
            WHERE user_id = ? 
            ORDER BY created_at DESC
            LIMIT $limit
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    /**
     * Get recent audit logs
     */
    public function getRecent($limit = 50)
    {
        $stmt = $this->db->getPdo()->prepare("
            SELECT * FROM audit_logs 
            ORDER BY created_at DESC
            LIMIT $limit
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Delete audit logs older than specified date
     */
    public function deleteOlderThan($date)
    {
        $stmt = $this->db->getPdo()->prepare("DELETE FROM audit_logs WHERE created_at < ?");
        return $stmt->execute([$date]);
    }

    /**
     * Get audit log statistics
     */
    public function getStats()
    {
        $stmt = $this->db->getPdo()->query("
            SELECT 
                COUNT(*) as total_logs,
                COUNT(DISTINCT user_id) as unique_users,
                COUNT(DISTINCT entity_type) as unique_entities,
                (SELECT action FROM audit_logs ORDER BY created_at DESC LIMIT 1) as latest_action
            FROM audit_logs
        ");
        return $stmt->fetch();
    }

    /**
     * Get action counts by type
     */
    public function getActionCounts()
    {
        $stmt = $this->db->getPdo()->query("
            SELECT 
                action,
                COUNT(*) as count
            FROM audit_logs
            GROUP BY action
            ORDER BY count DESC
        ");
        return $stmt->fetchAll();
    }

    /**
     * Get entity type counts
     */
    public function getEntityTypeCounts()
    {
        $stmt = $this->db->getPdo()->query("
            SELECT 
                entity_type,
                COUNT(*) as count
            FROM audit_logs
            GROUP BY entity_type
            ORDER BY count DESC
        ");
        return $stmt->fetchAll();
    }
}