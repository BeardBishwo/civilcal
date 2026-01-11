<?php
namespace App\Core;

abstract class Model {
    protected $db;
    protected $table;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function find($id) {
        $stmt = $this->db->getReadPdo()->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
    
    public function findAll() {
        $stmt = $this->db->getReadPdo()->prepare("SELECT * FROM {$this->table}");
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    public function where($conditions, $params = []) {
        $whereClause = implode(' AND ', array_map(fn($key) => "$key = ?", array_keys($conditions)));
        $sql = "SELECT * FROM {$this->table} WHERE {$whereClause}";
        $stmt = $this->db->getReadPdo()->prepare($sql);
        $stmt->execute(array_values($params ?: $conditions));
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    public function create($data) {
        $fields = implode(',', array_keys($data));
        $placeholders = implode(',', array_fill(0, count($data), '?'));
        $sql = "INSERT INTO {$this->table} ({$fields}) VALUES ({$placeholders})";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(array_values($data));
    }
    
    public function update($id, $data) {
        $fields = implode(' = ?, ', array_keys($data)) . ' = ?';
        $sql = "UPDATE {$this->table} SET {$fields} WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([...array_values($data), $id]);
    }
    
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
?>
