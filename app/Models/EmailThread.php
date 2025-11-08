<?php

namespace App\Models;

use App\Core\Database;

class EmailThread {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function find($id) {
        $stmt = $this->db->getPdo()->prepare("
            SELECT et.*, u.first_name, u.last_name, u.email as user_email,
                   a.first_name as assigned_first_name, a.last_name as assigned_last_name
            FROM email_threads et
            LEFT JOIN users u ON et.user_id = u.id
            LEFT JOIN users a ON et.assigned_to = a.id
            WHERE et.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function create($data) {
        $stmt = $this->db->getPdo()->prepare("
            INSERT INTO email_threads (user_id, from_email, from_name, subject, message, category, priority) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        
        return $stmt->execute([
            $data['user_id'] ?? null,
            $data['from_email'],
            $data['from_name'],
            $data['subject'],
            $data['message'],
            $data['category'] ?? 'general',
            $data['priority'] ?? 'medium'
        ]);
    }
    
    public function getAll($filters = []) {
        $sql = "
            SELECT et.*, u.first_name, u.last_name, u.email as user_email,
                   a.first_name as assigned_first_name, a.last_name as assigned_last_name
            FROM email_threads et
            LEFT JOIN users u ON et.user_id = u.id
            LEFT JOIN users a ON et.assigned_to = a.id
            WHERE 1=1
        ";
        $params = [];
        
        if (!empty($filters['status']) && $filters['status'] !== 'all') {
            $sql .= " AND et.status = ?";
            $params[] = $filters['status'];
        }
        
        if (!empty($filters['category']) && $filters['category'] !== 'all') {
            $sql .= " AND et.category = ?";
            $params[] = $filters['category'];
        }
        
        if (!empty($filters['priority']) && $filters['priority'] !== 'all') {
            $sql .= " AND et.priority = ?";
            $params[] = $filters['priority'];
        }
        
        $sql .= " ORDER BY et.created_at DESC";
        
        $stmt = $this->db->getPdo()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    public function getUnassigned() {
        $stmt = $this->db->getPdo()->prepare("
            SELECT et.*, u.first_name, u.last_name, u.email as user_email
            FROM email_threads et
            LEFT JOIN users u ON et.user_id = u.id
            WHERE et.assigned_to IS NULL AND et.status = 'new'
            ORDER BY et.created_at ASC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function update($id, $data) {
        $sql = "UPDATE email_threads SET ";
        $params = [];
        $updates = [];
        
        if (isset($data['status'])) {
            $updates[] = "status = ?";
            $params[] = $data['status'];
        }
        
        if (isset($data['priority'])) {
            $updates[] = "priority = ?";
            $params[] = $data['priority'];
        }
        
        if (isset($data['assigned_to'])) {
            $updates[] = "assigned_to = ?";
            $params[] = $data['assigned_to'];
        }
        
        if (isset($data['category'])) {
            $updates[] = "category = ?";
            $params[] = $data['category'];
        }
        
        if (empty($updates)) return false;
        
        $sql .= implode(', ', $updates) . ", updated_at = NOW() WHERE id = ?";
        $params[] = $id;
        
        $stmt = $this->db->getPdo()->prepare($sql);
        return $stmt->execute($params);
    }
    
    public function addResponse($id, $userId, $message, $isInternal = false) {
        $db = $this->db->getPdo();
        
        try {
            $db->beginTransaction();
            
            // Add response
            $stmt = $db->prepare("
                INSERT INTO email_responses (thread_id, user_id, message, is_internal_note) 
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([$id, $userId, $message, $isInternal ? 1 : 0]);
            
            // Update thread
            $stmt = $db->prepare("
                UPDATE email_threads 
                SET response_count = response_count + 1, 
                    last_response_at = NOW(),
                    status = CASE WHEN status = 'new' THEN 'in_progress' ELSE status END
                WHERE id = ?
            ");
            $stmt->execute([$id]);
            
            $db->commit();
            return true;
        } catch (\Exception $e) {
            $db->rollback();
            return false;
        }
    }
    
    public function getWithResponses($id) {
        $stmt = $this->db->getPdo()->prepare("
            SELECT et.*, u.first_name, u.last_name, u.email as user_email,
                   a.first_name as assigned_first_name, a.last_name as assigned_last_name
            FROM email_threads et
            LEFT JOIN users u ON et.user_id = u.id
            LEFT JOIN users a ON et.assigned_to = a.id
            WHERE et.id = ?
        ");
        $stmt->execute([$id]);
        $thread = $stmt->fetch();
        
        if ($thread) {
            // Get responses
            $stmt = $this->db->getPdo()->prepare("
                SELECT er.*, u.first_name, u.last_name, u.email
                FROM email_responses er
                LEFT JOIN users u ON er.user_id = u.id
                WHERE er.thread_id = ?
                ORDER BY er.created_at ASC
            ");
            $stmt->execute([$id]);
            $thread['responses'] = $stmt->fetchAll();
        }
        
        return $thread;
    }
    
    public function getStatistics() {
        $stmt = $this->db->getPdo()->query("
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'new' THEN 1 ELSE 0 END) as new_count,
                SUM(CASE WHEN status = 'in_progress' THEN 1 ELSE 0 END) as in_progress_count,
                SUM(CASE WHEN status = 'resolved' THEN 1 ELSE 0 END) as resolved_count
            FROM email_threads
        ");
        return $stmt->fetch();
    }
    
    public function markAsResolved($id) {
        $stmt = $this->db->getPdo()->prepare("UPDATE email_threads SET status = 'resolved' WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    public function reopen($id) {
        $stmt = $this->db->getPdo()->prepare("UPDATE email_threads SET status = 'in_progress' WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /* Compatibility wrappers used by controllers */
    public function getRecentThreads($limit = 10)
    {
        $limit = intval($limit);
        $stmt = $this->db->getPdo()->prepare("SELECT * FROM email_threads ORDER BY created_at DESC LIMIT ?");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    public function getThreadsByStatus($status, $limit = 10)
    {
        $stmt = $this->db->getPdo()->prepare("SELECT * FROM email_threads WHERE status = ? ORDER BY created_at DESC LIMIT ?");
        $stmt->execute([$status, intval($limit)]);
        return $stmt->fetchAll();
    }

    public function getThreadsWithFilters($filters = [], $page = 1, $limit = 20)
    {
        $offset = ($page - 1) * $limit;
        $sql = "SELECT * FROM email_threads WHERE 1=1";
        $params = [];

        if (!empty($filters['status'])) {
            $sql .= " AND status = ?";
            $params[] = $filters['status'];
        }
        if (!empty($filters['priority'])) {
            $sql .= " AND priority = ?";
            $params[] = $filters['priority'];
        }
        if (!empty($filters['assigned_to'])) {
            $sql .= " AND assigned_to = ?";
            $params[] = $filters['assigned_to'];
        }
        if (!empty($filters['search'])) {
            $sql .= " AND (subject LIKE ? OR from_email LIKE ? OR from_name LIKE ?)";
            $term = '%' . $filters['search'] . '%';
            $params[] = $term; $params[] = $term; $params[] = $term;
        }

        $sql .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
        $params[] = intval($limit);
        $params[] = intval($offset);

        $stmt = $this->db->getPdo()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getThreadCountWithFilters($filters = [])
    {
        $sql = "SELECT COUNT(*) as cnt FROM email_threads WHERE 1=1";
        $params = [];
        if (!empty($filters['status'])) { $sql .= " AND status = ?"; $params[] = $filters['status']; }
        if (!empty($filters['priority'])) { $sql .= " AND priority = ?"; $params[] = $filters['priority']; }
        if (!empty($filters['assigned_to'])) { $sql .= " AND assigned_to = ?"; $params[] = $filters['assigned_to']; }
        if (!empty($filters['search'])) { $sql .= " AND (subject LIKE ? OR from_email LIKE ? OR from_name LIKE ?)"; $term = '%' . $filters['search'] . '%'; $params[] = $term; $params[] = $term; $params[] = $term; }

        $stmt = $this->db->getPdo()->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch();
        return $row ? intval($row['cnt']) : 0;
    }

    public function getAvailableAssignees()
    {
        // return users who can be assigned; basic implementation
        $stmt = $this->db->getPdo()->prepare("SELECT id, first_name, last_name, email FROM users WHERE role IN ('admin','agent') ORDER BY first_name ASC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getThreadById($id)
    {
        return $this->getWithResponses($id);
    }

    public function markAsRead($id)
    {
        $stmt = $this->db->getPdo()->prepare("UPDATE email_threads SET status = 'read' WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function createThread($data)
    {
        $success = $this->create($data);
        if ($success) {
            $lastId = $this->db->lastInsertId();
            return $this->find($lastId);
        }
        return false;
    }

    public function addResponseToThread($threadId, $userId, $message, $isInternal = false)
    {
        return $this->addResponse($threadId, $userId, $message, $isInternal);
    }

    public function updateThread($id, $data)
    {
        return $this->update($id, $data);
    }

    public function deleteThread($id)
    {
        $stmt = $this->db->getPdo()->prepare("DELETE FROM email_threads WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getTotalThreadCount()
    {
        $stmt = $this->db->getPdo()->query("SELECT COUNT(*) as cnt FROM email_threads");
        $row = $stmt->fetch();
        return $row ? intval($row['cnt']) : 0;
    }

    public function getThreadCountByStatus($status)
    {
        $stmt = $this->db->getPdo()->prepare("SELECT COUNT(*) as cnt FROM email_threads WHERE status = ?");
        $stmt->execute([$status]);
        $row = $stmt->fetch();
        return $row ? intval($row['cnt']) : 0;
    }

    public function getThreadCountByPriority($priority)
    {
        $stmt = $this->db->getPdo()->prepare("SELECT COUNT(*) as cnt FROM email_threads WHERE priority = ?");
        $stmt->execute([$priority]);
        $row = $stmt->fetch();
        return $row ? intval($row['cnt']) : 0;
    }

    public function getAverageResponseTime()
    {
        // Basic implementation: average time between created_at and last_response_at in hours
        $stmt = $this->db->getPdo()->query("SELECT AVG(TIMESTAMPDIFF(HOUR, created_at, last_response_at)) as avg_hours FROM email_threads WHERE last_response_at IS NOT NULL");
        $row = $stmt->fetch();
        return $row ? floatval($row['avg_hours']) : 0.0;
    }
}
?>
