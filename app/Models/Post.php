<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class Post
{
    protected $db;
    protected $table = 'posts';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Get all posts with optional status filter
     */
    public function getAll($status = null)
    {
        $sql = "SELECT p.*, u.full_name as author_name 
                FROM {$this->table} p
                LEFT JOIN users u ON p.author_id = u.id";
        
        $params = [];
        if ($status) {
            $sql .= " WHERE p.status = :status";
            $params['status'] = $status;
        }
        
        $sql .= " ORDER BY p.created_at DESC";
        
        return $this->db->find($this->table, $params, 'created_at DESC');
    }

    /**
     * Find post by ID
     */
    public function find($id)
    {
        return $this->db->findOne($this->table, ['id' => $id]);
    }

    /**
     * Find post by SLUG
     */
    public function findBySlug($slug)
    {
        return $this->db->findOne($this->table, ['slug' => $slug, 'status' => 'published']);
    }

    /**
     * Create new post
     */
    public function create($data)
    {
        return $this->db->insert($this->table, $data);
    }

    /**
     * Update post
     */
    public function update($id, $data)
    {
        return $this->db->update($this->table, $data, 'id = :id', ['id' => $id]);
    }

    /**
     * Delete post
     */
    public function delete($id)
    {
        return $this->db->delete($this->table, 'id = :id', ['id' => $id]);
    }

    /**
     * Get recent posts for widget
     */
    public function getRecent($limit = 5)
    {
        return $this->db->find($this->table, ['status' => 'published'], 'created_at DESC', $limit);
    }
}
