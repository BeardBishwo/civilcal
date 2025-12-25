<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class Advertisement
{
    protected $pdo;
    protected $table = 'advertisements';

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getPdo();
    }

    /**
     * Get all advertisements
     */
    public function getAll()
    {
        $stmt = $this->pdo->query("SELECT * FROM {$this->table} ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get active advertisements by location
     */
    public function getActiveByLocation($location)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE location = ? AND is_active = 1");
        $stmt->execute([$location]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Create new advertisement
     */
    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} (name, location, code, is_active, created_at) VALUES (?, ?, ?, ?, NOW())";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $data['name'],
            $data['location'],
            $data['code'],
            isset($data['is_active']) ? 1 : 0
        ]);
    }

    /**
     * Find advertisement by ID
     */
    public function find($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Update advertisement
     */
    public function update($id, $data)
    {
        $sql = "UPDATE {$this->table} SET name = ?, location = ?, code = ?, is_active = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $data['name'],
            $data['location'],
            $data['code'],
            isset($data['is_active']) ? 1 : 0,
            $id
        ]);
    }

    /**
     * Delete advertisement
     */
    public function delete($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Toggle active status
     */
    public function toggleStatus($id)
    {
        $sql = "UPDATE {$this->table} SET is_active = NOT is_active WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }
}
