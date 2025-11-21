<?php
namespace App\Models;

use App\Core\Model;

class ActivityLog extends Model
{
    protected $table = 'activity_logs';

    /**
     * Get all activity logs
     */
    public function getAll($limit = 50, $offset = 0)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} ORDER BY created_at DESC LIMIT ? OFFSET ?");
        $stmt->execute([$limit, $offset]);
        return $stmt->fetchAll();
    }

    /**
     * Get activity logs by user
     */
    public function getByUser($userId, $limit = 50, $offset = 0)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE user_id = ? ORDER BY created_at DESC LIMIT ? OFFSET ?");
        $stmt->execute([$userId, $limit, $offset]);
        return $stmt->fetchAll();
    }

    /**
     * Get activity logs by type
     */
    public function getByType($type, $limit = 50, $offset = 0)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE type = ? ORDER BY created_at DESC LIMIT ? OFFSET ?");
        $stmt->execute([$type, $limit, $offset]);
        return $stmt->fetchAll();
    }

    /**
     * Create a new activity log entry
     */
    public function create($userId, $type, $description, $metadata = [])
    {
        $metadataJson = json_encode($metadata);
        
        $stmt = $this->db->prepare("INSERT INTO {$this->table} (user_id, type, description, metadata, created_at) VALUES (?, ?, ?, ?, NOW())");
        return $stmt->execute([$userId, $type, $description, $metadataJson]);
    }

    /**
     * Get recent activity logs
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

    /**
     * Get activity summary by date range
     */
    public function getSummaryByDateRange($startDate, $endDate)
    {
        $stmt = $this->db->prepare("
            SELECT 
                DATE(created_at) as date,
                type,
                COUNT(*) as count
            FROM {$this->table}
            WHERE created_at BETWEEN ? AND ?
            GROUP BY DATE(created_at), type
            ORDER BY created_at DESC
        ");
        $stmt->execute([$startDate, $endDate]);
        return $stmt->fetchAll();
    }
}