<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class Page
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAll($filters = [], $page = 1, $perPage = 10)
    {
        $offset = ($page - 1) * $perPage;
        $params = [];
        $whereSql = "WHERE 1=1";

        if (!empty($filters['status'])) {
            $whereSql .= " AND p.status = ?";
            $params[] = $filters['status'];
        }

        if (!empty($filters['search'])) {
            $whereSql .= " AND (p.title LIKE ? OR p.slug LIKE ?)";
            $searchTerm = "%" . $filters['search'] . "%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        // Count total
        $countSql = "SELECT COUNT(*) as total FROM pages p $whereSql";
        $stmt = $this->db->getPdo()->prepare($countSql);
        $stmt->execute($params);
        $total = $stmt->fetch()['total'];

        // Get results
        $sql = "
            SELECT p.*, 
                   CONCAT(u.first_name, ' ', u.last_name) as author_name,
                   u.username as author_username
            FROM pages p
            LEFT JOIN users u ON p.author_id = u.id
            $whereSql
            ORDER BY p.updated_at DESC
            LIMIT $perPage OFFSET $offset
        ";

        $stmt = $this->db->getPdo()->prepare($sql);
        $stmt->execute($params);
        $pages = $stmt->fetchAll();

        return [
            'data' => $pages,
            'total' => $total,
            'current_page' => $page,
            'per_page' => $perPage,
            'last_page' => ceil($total / $perPage)
        ];
    }

    public function find($id)
    {
        $sql = "
            SELECT p.*, 
                   CONCAT(u.first_name, ' ', u.last_name) as author_name,
                   u.username as author_username
            FROM pages p
            LEFT JOIN users u ON p.author_id = u.id
            WHERE p.id = ?
        ";
        
        $stmt = $this->db->getPdo()->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function findBySlug($slug)
    {
        $sql = "SELECT * FROM pages WHERE slug = ?";
        $stmt = $this->db->getPdo()->prepare($sql);
        $stmt->execute([$slug]);
        return $stmt->fetch();
    }

    public function create($data)
    {
        $placeholders = [];
        $columns = [];
        $values = [];

        // Handle slug generation if not provided
        if (empty($data['slug']) && !empty($data['title'])) {
            $data['slug'] = $this->generateSlug($data['title']);
        }

        foreach ($data as $key => $value) {
            $columns[] = $key;
            $placeholders[] = '?';
            $values[] = $value;
        }

        $sql = "INSERT INTO pages (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $placeholders) . ")";
        
        $stmt = $this->db->getPdo()->prepare($sql);
        
        if ($stmt->execute($values)) {
            return $this->db->lastInsertId();
        }
        
        return false;
    }

    public function update($id, $data)
    {
        $sets = [];
        $values = [];

        foreach ($data as $key => $value) {
            $sets[] = "$key = ?";
            $values[] = $value;
        }

        $values[] = $id;
        
        $sql = "UPDATE pages SET " . implode(', ', $sets) . ", updated_at = NOW() WHERE id = ?";
        
        $stmt = $this->db->getPdo()->prepare($sql);
        return $stmt->execute($values);
    }

    public function delete($id)
    {
        $stmt = $this->db->getPdo()->prepare("DELETE FROM pages WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function generateSlug($title)
    {
        // Simple slug generation
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
        
        // Ensure unique
        $originalSlug = $slug;
        $count = 1;
        
        while ($this->findBySlug($slug)) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }
        
        return $slug;
    }

    public function getStats()
    {
        $stmt = $this->db->getPdo()->query("
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'published' THEN 1 ELSE 0 END) as published,
                SUM(CASE WHEN status = 'draft' THEN 1 ELSE 0 END) as draft
            FROM pages
        ");
        return $stmt->fetch();
    }
}
