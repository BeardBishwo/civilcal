<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class Menu
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAll($filters = [])
    {
        $params = [];
        $whereSql = "WHERE 1=1";

        if (!empty($filters['location'])) {
            $whereSql .= " AND location = ?";
            $params[] = $filters['location'];
        }

        if (isset($filters['is_active'])) {
            $whereSql .= " AND is_active = ?";
            $params[] = $filters['is_active'];
        }

        if (!empty($filters['search'])) {
            $whereSql .= " AND name LIKE ?";
            $params[] = "%" . $filters['search'] . "%";
        }

        $sql = "SELECT * FROM menus $whereSql ORDER BY display_order ASC, created_at DESC";
        
        $stmt = $this->db->getPdo()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function find($id)
    {
        $stmt = $this->db->getPdo()->prepare("SELECT * FROM menus WHERE id = ?");
        $stmt->execute([$id]);
        $menu = $stmt->fetch();
        
        if ($menu) {
            // Decode JSON items if string
            if (is_string($menu['items'])) {
                $menu['items'] = json_decode($menu['items'], true) ?? [];
            }
        }
        
        return $menu;
    }

    public function findByLocation($location)
    {
        $stmt = $this->db->getPdo()->prepare("SELECT * FROM menus WHERE location = ? AND is_active = 1 ORDER BY display_order ASC LIMIT 1");
        $stmt->execute([$location]);
        $menu = $stmt->fetch();
        
        if ($menu && is_string($menu['items'])) {
            $menu['items'] = json_decode($menu['items'], true) ?? [];
        }
        
        return $menu;
    }

    public function create($data)
    {
        // Ensure items is JSON
        if (isset($data['items']) && is_array($data['items'])) {
            $data['items'] = json_encode($data['items']);
        }

        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        
        $sql = "INSERT INTO menus ($columns) VALUES ($placeholders)";
        $stmt = $this->db->getPdo()->prepare($sql);
        
        if ($stmt->execute(array_values($data))) {
            return $this->db->lastInsertId();
        }
        
        return false;
    }

    public function update($id, $data)
    {
        // Ensure items is JSON
        if (isset($data['items']) && is_array($data['items'])) {
            $data['items'] = json_encode($data['items']);
        }

        $sets = [];
        $values = [];

        foreach ($data as $key => $value) {
            $sets[] = "$key = ?";
            $values[] = $value;
        }
        $values[] = $id;

        $sql = "UPDATE menus SET " . implode(', ', $sets) . ", updated_at = NOW() WHERE id = ?";
        
        $stmt = $this->db->getPdo()->prepare($sql);
        return $stmt->execute($values);
    }

    public function delete($id)
    {
        $stmt = $this->db->getPdo()->prepare("DELETE FROM menus WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    public function getStats()
    {
        $stmt = $this->db->getPdo()->query("
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active,
                COUNT(DISTINCT location) as locations
            FROM menus
        ");
        return $stmt->fetch();
    }
}
