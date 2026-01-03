<?php

namespace App\Models;

use App\Core\Database;

class ContestParticipant {
    protected $db;
    protected $table = 'contest_participants';

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

    public function where($conditions) {
        $whereClause = implode(' AND ', array_map(fn($key) => "`$key` = ?", array_keys($conditions)));
        $sql = "SELECT * FROM `{$this->table}` WHERE {$whereClause}";
        $stmt = $this->db->getPdo()->prepare($sql);
        $stmt->execute(array_values($conditions));
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getMaxScore($contestId) {
        $stmt = $this->db->getPdo()->prepare("SELECT MAX(score) as max_score FROM {$this->table} WHERE contest_id = ?");
        $stmt->execute([$contestId]);
        $res = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $res ? $res['max_score'] : 0;
    }
}
