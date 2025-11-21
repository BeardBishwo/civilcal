<?php
namespace App\Models;

use App\Core\Model;

class Module extends Model
{
    protected $table = 'modules';

    /**
     * Get all modules
     */
    public function getAll()
    {
        $stmt = $this->db->query("SELECT * FROM {$this->table} ORDER BY name ASC");
        return $stmt->fetchAll();
    }

    /**
     * Get active modules
     */
    public function getActive()
    {
        $stmt = $this->db->query("SELECT * FROM {$this->table} WHERE status = 'active' ORDER BY name ASC");
        return $stmt->fetchAll();
    }

    /**
     * Find module by name
     */
    public function findByName($name)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE name = ?");
        $stmt->execute([$name]);
        return $stmt->fetch();
    }

    /**
     * Activate a module
     */
    public function activate($name)
    {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET status = 'active', updated_at = NOW() WHERE name = ?");
        return $stmt->execute([$name]);
    }

    /**
     * Deactivate a module
     */
    public function deactivate($name)
    {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET status = 'inactive', updated_at = NOW() WHERE name = ?");
        return $stmt->execute([$name]);
    }
}