<?php
namespace App\Models;

use App\Core\Model;

class Plugin extends Model
{
    protected $table = 'plugins';

    /**
     * Get all plugins
     */
    public function getAll()
    {
        $stmt = $this->db->query("SELECT * FROM {$this->table} ORDER BY name ASC");
        return $stmt->fetchAll();
    }

    /**
     * Get active plugins
     */
    public function getActive()
    {
        $stmt = $this->db->query("SELECT * FROM {$this->table} WHERE status = 'active' ORDER BY name ASC");
        return $stmt->fetchAll();
    }

    /**
     * Find plugin by name
     */
    public function findByName($name)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE name = ?");
        $stmt->execute([$name]);
        return $stmt->fetch();
    }

    /**
     * Activate a plugin
     */
    public function activate($name)
    {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET status = 'active', updated_at = NOW() WHERE name = ?");
        return $stmt->execute([$name]);
    }

    /**
     * Deactivate a plugin
     */
    public function deactivate($name)
    {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET status = 'inactive', updated_at = NOW() WHERE name = ?");
        return $stmt->execute([$name]);
    }
    
    /**
     * Install a plugin
     */
    public function install($pluginData)
    {
        $stmt = $this->db->prepare("INSERT INTO {$this->table} (name, version, description, status, created_at) VALUES (?, ?, ?, 'inactive', NOW())");
        return $stmt->execute([
            $pluginData['name'],
            $pluginData['version'],
            $pluginData['description']
        ]);
    }
}