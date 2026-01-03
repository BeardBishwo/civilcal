<?php

namespace App\Models;

use App\Core\Database;

class Contest {
    protected $db;
    protected $table = 'contests';

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function find($id) {
        $stmt = $this->db->getPdo()->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $fields = implode('`, `', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $sql = "INSERT INTO `{$this->table}` (`{$fields}`) VALUES ({$placeholders})";
        $stmt = $this->db->getPdo()->prepare($sql);
        $stmt->execute(array_values($data));
        return $this->db->getPdo()->lastInsertId();
    }

    public function update($id, $data) {
        $fields = implode('` = ?, `', array_keys($data)) . '` = ?';
        $sql = "UPDATE `{$this->table}` SET `{$fields}` WHERE id = ?";
        $stmt = $this->db->getPdo()->prepare($sql);
        return $stmt->execute([...array_values($data), $id]);
    }

    public function getAll($status = null) {
        $sql = "SELECT * FROM {$this->table}";
        $params = [];
        if ($status) {
            $sql .= " WHERE status = ?";
            $params[] = $status;
        }
        $sql .= " ORDER BY start_time DESC";
        $stmt = $this->db->getPdo()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
