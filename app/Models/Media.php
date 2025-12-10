<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class Media
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAll($filters = [], $page = 1, $perPage = 20)
    {
        $offset = ($page - 1) * $perPage;
        $params = [];
        $whereSql = "WHERE 1=1";

        if (!empty($filters['type'])) {
            $whereSql .= " AND file_type = ?";
            $params[] = $filters['type'];
        }

        if (!empty($filters['search'])) {
            $whereSql .= " AND (filename LIKE ? OR original_filename LIKE ?)";
            $searchTerm = "%" . $filters['search'] . "%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        if (isset($filters['folder'])) {
            $whereSql .= " AND folder = ?";
            $params[] = $filters['folder'];
        }

        // Count total
        $countSql = "SELECT COUNT(*) as total FROM media $whereSql";
        $stmt = $this->db->getPdo()->prepare($countSql);
        $stmt->execute($params);
        $total = $stmt->fetch()['total'];

        // Get results
        $sql = "SELECT * FROM media $whereSql ORDER BY created_at DESC LIMIT $perPage OFFSET $offset";
        $stmt = $this->db->getPdo()->prepare($sql);
        $stmt->execute($params);
        $media = $stmt->fetchAll();

        return [
            'data' => $media,
            'total' => $total,
            'current_page' => $page,
            'per_page' => $perPage,
            'last_page' => ceil($total / $perPage)
        ];
    }

    public function find($id)
    {
        $stmt = $this->db->getPdo()->prepare("SELECT * FROM media WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($data)
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));

        $sql = "INSERT INTO media ($columns) VALUES ($placeholders)";
        $stmt = $this->db->getPdo()->prepare($sql);

        if ($stmt->execute(array_values($data))) {
            return $this->db->lastInsertId();
        }

        return false;
    }

    public function update($id, $data)
    {
        $setParts = [];
        $values = [];

        foreach ($data as $key => $value) {
            $setParts[] = "$key = ?";
            $values[] = $value;
        }

        $values[] = $id;
        $setClause = implode(', ', $setParts);

        $sql = "UPDATE media SET $setClause WHERE id = ?";
        $stmt = $this->db->getPdo()->prepare($sql);

        return $stmt->execute($values);
    }

    public function delete($id)
    {
        $stmt = $this->db->getPdo()->prepare("DELETE FROM media WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getStats()
    {
        $stmt = $this->db->getPdo()->query("
            SELECT 
                COUNT(*) as total_files,
                SUM(file_size) as total_size,
                COUNT(DISTINCT file_type) as types_count
            FROM media
        ");
        return $stmt->fetch();
    }
}
