<?php

namespace App\Models;

use App\Core\Database;
use Exception;

class Plugin
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Find a plugin by ID
     */
    public function find($id)
    {
        $stmt = $this->db->getPdo()->prepare("SELECT * FROM plugins WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Find a plugin by name
     */
    public function findByName($name)
    {
        $stmt = $this->db->getPdo()->prepare("SELECT * FROM plugins WHERE name = ?");
        $stmt->execute([$name]);
        return $stmt->fetch();
    }

    /**
     * Get all plugins
     */
    public function getAll()
    {
        $stmt = $this->db->getPdo()->query("SELECT * FROM plugins ORDER BY name ASC");
        return $stmt->fetchAll();
    }

    /**
     * Get active plugins only
     */
    public function getActive()
    {
        $stmt = $this->db->getPdo()->prepare("SELECT * FROM plugins WHERE is_active = 1 ORDER BY name ASC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Create a new plugin
     */
    public function create($data)
    {
        $stmt = $this->db->getPdo()->prepare("
            INSERT INTO plugins (
                name, 
                description, 
                version, 
                author, 
                website, 
                is_active, 
                config, 
                created_at, 
                updated_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
        ");

        $result = $stmt->execute([
            $data['name'] ?? null,
            $data['description'] ?? null,
            $data['version'] ?? '1.0.0',
            $data['author'] ?? null,
            $data['website'] ?? null,
            $data['is_active'] ?? 1,
            !empty($data['config']) ? json_encode($data['config']) : null
        ]);

        if ($result) {
            return $this->db->getPdo()->lastInsertId();
        }
        
        return false;
    }

    /**
     * Update an existing plugin
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
        if (isset($data['author'])) {
            $updateFields[] = 'author = ?';
            $values[] = $data['author'];
        }
        if (isset($data['website'])) {
            $updateFields[] = 'website = ?';
            $values[] = $data['website'];
        }
        if (isset($data['is_active'])) {
            $updateFields[] = 'is_active = ?';
            $values[] = $data['is_active'];
        }
        if (isset($data['config'])) {
            $updateFields[] = 'config = ?';
            $values[] = json_encode($data['config']);
        }

        $updateFields[] = 'updated_at = NOW()';

        if (empty($updateFields)) {
            return false;
        }

        $values[] = $id;

        $sql = "UPDATE plugins SET " . implode(', ', $updateFields) . " WHERE id = ?";
        $stmt = $this->db->getPdo()->prepare($sql);

        return $stmt->execute($values);
    }

    /**
     * Delete a plugin by ID
     */
    public function delete($id)
    {
        $stmt = $this->db->getPdo()->prepare("DELETE FROM plugins WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Activate a plugin
     */
    public function activate($id)
    {
        $stmt = $this->db->getPdo()->prepare("UPDATE plugins SET is_active = 1, updated_at = NOW() WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Deactivate a plugin
     */
    public function deactivate($id)
    {
        $stmt = $this->db->getPdo()->prepare("UPDATE plugins SET is_active = 0, updated_at = NOW() WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Get plugin configuration
     */
    public function getConfiguration($id)
    {
        $plugin = $this->find($id);
        if ($plugin && !empty($plugin['config'])) {
            return json_decode($plugin['config'], true);
        }
        return [];
    }

    /**
     * Update plugin configuration
     */
    public function updateConfiguration($id, $config)
    {
        $stmt = $this->db->getPdo()->prepare("UPDATE plugins SET config = ?, updated_at = NOW() WHERE id = ?");
        return $stmt->execute([json_encode($config), $id]);
    }

    /**
     * Get plugin statistics
     */
    public function getStats()
    {
        $stmt = $this->db->getPdo()->query("
            SELECT 
                COUNT(*) as total_plugins,
                SUM(is_active) as active_plugins,
                COUNT(*) - SUM(is_active) as inactive_plugins
            FROM plugins
        ");
        return $stmt->fetch();
    }

    /**
     * Validate plugin data before saving
     */
    public function validate($data)
    {
        $errors = [];

        if (empty($data['name'])) {
            $errors[] = 'Plugin name is required';
        }

        if (empty($data['description'])) {
            $errors[] = 'Plugin description is required';
        }

        if (!isset($data['is_active']) || !is_numeric($data['is_active'])) {
            $data['is_active'] = 1; // default to active
        }

        if (!in_array($data['is_active'], [0, 1])) {
            $errors[] = 'Plugin active status must be 0 or 1';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'data' => $data
        ];
    }
    
    /**
     * Get plugins by author
     */
    public function getByAuthor($author)
    {
        $stmt = $this->db->getPdo()->prepare("SELECT * FROM plugins WHERE author LIKE ? ORDER BY name ASC");
        $stmt->execute(["%{$author}%"]);
        return $stmt->fetchAll();
    }
}