<?php
namespace App\Models;

use App\Core\Model;

class AuditLog extends Model
{
    protected $table = 'audit_logs';

    /**
     * Get all audit logs
     */
    public function getAll($limit = 50, $offset = 0)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} ORDER BY created_at DESC LIMIT ? OFFSET ?");
        $stmt->execute([$limit, $offset]);
        return $stmt->fetchAll();
    }

    /**
     * Get audit logs by user
     */
    public function getByUser($userId, $limit = 50, $offset = 0)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE user_id = ? ORDER BY created_at DESC LIMIT ? OFFSET ?");
        $stmt->execute([$userId, $limit, $offset]);
        return $stmt->fetchAll();
    }

    /**
     * Get audit logs by action
     */
    public function getByAction($action, $limit = 50, $offset = 0)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE action = ? ORDER BY created_at DESC LIMIT ? OFFSET ?");
        $stmt->execute([$action, $limit, $offset]);
        return $stmt->fetchAll();
    }

    /**
     * Create a new audit log entry
     */
    public function create($userId, $action, $details = [], $ipAddress = null)
    {
        $detailsJson = json_encode($details);
        $ipAddress = $ipAddress ?: $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        
        $stmt = $this->db->prepare("INSERT INTO {$this->table} (user_id, action, details, ip_address, created_at) VALUES (?, ?, ?, ?, NOW())");
        return $stmt->execute([$userId, $action, $detailsJson, $ipAddress]);
    }

    /**
     * Get recent audit logs
     */
    public function getRecent($days = 7, $limit = 50)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table} 
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? DAY) 
            ORDER BY created_at DESC 
            LIMIT ?
        ");
        $stmt->execute([$days, $limit]);
        return $stmt->fetchAll();
    }
}