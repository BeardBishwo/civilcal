<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class Project
{
    protected $pdo;
    protected $table = 'projects';

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getPdo();
    }

    /**
     * Get all projects for a user
     */
    public function getAll($userId)
    {
        $stmt = $this->pdo->prepare("
            SELECT p.*, COUNT(ch.id) as calculation_count 
            FROM {$this->table} p
            LEFT JOIN calculation_history ch ON p.id = ch.project_id
            WHERE p.user_id = ?
            GROUP BY p.id
            ORDER BY p.updated_at DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Find specific project
     */
    public function find($id, $userId)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE id = ? AND user_id = ?");
        $stmt->execute([$id, $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Create project
     */
    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} (user_id, name, description, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())";
        $stmt = $this->pdo->prepare($sql);
        if ($stmt->execute([$data['user_id'], $data['name'], $data['description']])) {
            return $this->pdo->lastInsertId();
        }
        return false;
    }

    /**
     * Update project
     */
    public function update($id, $userId, $data)
    {
        $sql = "UPDATE {$this->table} SET name = ?, description = ?, updated_at = NOW() WHERE id = ? AND user_id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$data['name'], $data['description'], $id, $userId]);
    }

    /**
     * Delete project
     */
    public function delete($id, $userId)
    {
        $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE id = ? AND user_id = ?");
        return $stmt->execute([$id, $userId]);
    }

    /**
     * Get calculations for a project
     */
    public function getCalculations($projectId, $userId)
    {
        // Verify ownership first
        if (!$this->find($projectId, $userId)) {
            return [];
        }

        $stmt = $this->pdo->prepare("
            SELECT * FROM calculation_history 
            WHERE project_id = ? 
            ORDER BY created_at DESC
        ");
        $stmt->execute([$projectId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
