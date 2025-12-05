<?php

namespace App\Models;

use App\Core\Database;
use Exception;

class Module
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Find a module by ID
     */
    public function find($id)
    {
        $stmt = $this->db->getPdo()->prepare("SELECT * FROM modules WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Find a module by name
     */
    public function findByName($name)
    {
        $stmt = $this->db->getPdo()->prepare("SELECT * FROM modules WHERE name = ?");
        $stmt->execute([$name]);
        return $stmt->fetch();
    }

    /**
     * Get all modules
     */
    public function getAll()
    {
        $stmt = $this->db->getPdo()->query("SELECT * FROM modules ORDER BY name ASC");
        return $stmt->fetchAll();
    }

    /**
     * Get active modules only
     */
    public function getActive()
    {
        $stmt = $this->db->getPdo()->prepare("SELECT * FROM modules WHERE is_active = 1 ORDER BY name ASC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Create a new module
     */
    public function create($data)
    {
        $stmt = $this->db->getPdo()->prepare("
            INSERT INTO modules (
                name, 
                description, 
                version, 
                category, 
                is_active, 
                created_at, 
                updated_at
            ) VALUES (?, ?, ?, ?, ?, NOW(), NOW())
        ");

        $result = $stmt->execute([
            $data['name'] ?? null,
            $data['description'] ?? null,
            $data['version'] ?? '1.0.0',
            $data['category'] ?? 'general',
            $data['is_active'] ?? 1
        ]);

        if ($result) {
            return $this->db->getPdo()->lastInsertId();
        }
        
        return false;
    }

    /**
     * Update an existing module
     */
    public function update($id, $data)
    {
        $updateFields = [];
        $values = [];

        // Prepare fields to update
        if (isset($data['name'])) {
            $updateFields[] = 'name = ?';
            $values[] = $data['name'];
        }
        if (isset($data['description'])) {
            $updateFields[] = 'description = ?';
            $values[] = $data['description'];
        }
        if (isset($data['version'])) {
            $updateFields[] = 'version = ?';
            $values[] = $data['version'];
        }
        if (isset($data['category'])) {
            $updateFields[] = 'category = ?';
            $values[] = $data['category'];
        }
        if (isset($data['is_active'])) {
            $updateFields[] = 'is_active = ?';
            $values[] = $data['is_active'];
        }

        $updateFields[] = 'updated_at = NOW()';

        if (empty($updateFields)) {
            return false;
        }

        $values[] = $id;

        $sql = "UPDATE modules SET " . implode(', ', $updateFields) . " WHERE id = ?";
        $stmt = $this->db->getPdo()->prepare($sql);

        return $stmt->execute($values);
    }

    /**
     * Delete a module by ID
     */
    public function delete($id)
    {
        $stmt = $this->db->getPdo()->prepare("DELETE FROM modules WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Activate a module
     */
    public function activate($id)
    {
        $stmt = $this->db->getPdo()->prepare("UPDATE modules SET is_active = 1, updated_at = NOW() WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Deactivate a module
     */
    public function deactivate($id)
    {
        $stmt = $this->db->getPdo()->prepare("UPDATE modules SET is_active = 0, updated_at = NOW() WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Get modules by category
     */
    public function getByCategory($category)
    {
        $stmt = $this->db->getPdo()->prepare("SELECT * FROM modules WHERE category = ? ORDER BY name ASC");
        $stmt->execute([$category]);
        return $stmt->fetchAll();
    }

    /**
     * Get module statistics
     */
    public function getStats()
    {
        $stmt = $this->db->getPdo()->query("
            SELECT 
                COUNT(*) as total_modules,
                SUM(is_active) as active_modules,
                COUNT(*) - SUM(is_active) as inactive_modules
            FROM modules
        ");
        return $stmt->fetch();
    }

    /**
     * Validate module data before saving
     */
    public function validate($data)
    {
        $errors = [];

        if (empty($data['name'])) {
            $errors[] = 'Module name is required';
        }

        if (empty($data['description'])) {
            $errors[] = 'Module description is required';
        }

        if (!isset($data['is_active']) || !is_numeric($data['is_active'])) {
            $data['is_active'] = 1; // default to active
        }

        if (!in_array($data['is_active'], [0, 1])) {
            $errors[] = 'Module active status must be 0 or 1';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'data' => $data
        ];
    }
}