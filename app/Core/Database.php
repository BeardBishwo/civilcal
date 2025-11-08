<?php

namespace App\Core;

use PDO;
use PDOException;

class Database {
    private static $instance = null;
    private $pdo;
    
    private function __construct() {
        $config = require_once __DIR__ . '/../../config/database.php';
        
        try {
            $dsn = "mysql:host={$config['host']};dbname={$config['database']}";
            if (isset($config['charset'])) {
                $dsn .= ";charset={$config['charset']}";
            }
            
            $this->pdo = new PDO(
                $dsn,
                $config['username'],
                $config['password'],
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        } catch (PDOException $e) {
            throw new \Exception("Database connection failed: " . $e->getMessage());
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getPdo() {
        return $this->pdo;
    }
    
    public function query($sql, $params = []) {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
    
    public function prepare($sql) {
        return $this->pdo->prepare($sql);
    }
    
    public function insert($table, $data) {
        $columns = implode(',', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        
        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
        $stmt = $this->prepare($sql);
        
        return $stmt->execute($data);
    }
    
    public function update($table, $data, $where, $whereParams = []) {
        $set = [];
        foreach ($data as $key => $value) {
            $set[] = "{$key} = :{$key}";
        }
        $setClause = implode(', ', $set);
        
        $sql = "UPDATE {$table} SET {$setClause} WHERE {$where}";
        $stmt = $this->prepare($sql);
        
        $params = array_merge($data, $whereParams);
        return $stmt->execute($params);
    }
    
    public function delete($table, $where, $whereParams = []) {
        $sql = "DELETE FROM {$table} WHERE {$where}";
        $stmt = $this->prepare($sql);
        
        return $stmt->execute($whereParams);
    }
    
    public function find($table, $conditions = [], $columns = '*') {
        $where = '';
        $params = [];
        
        if (!empty($conditions)) {
            $whereConditions = [];
            foreach ($conditions as $key => $value) {
                $whereConditions[] = "{$key} = :{$key}";
                $params[$key] = $value;
            }
            $where = 'WHERE ' . implode(' AND ', $whereConditions);
        }
        
        $sql = "SELECT {$columns} FROM {$table} {$where}";
        $stmt = $this->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll();
    }
    
    public function findOne($table, $conditions = [], $columns = '*') {
        $results = $this->find($table, $conditions, $columns);
        return !empty($results) ? $results[0] : null;
    }
    
    public function count($table, $conditions = []) {
        $where = '';
        $params = [];
        
        if (!empty($conditions)) {
            $whereConditions = [];
            foreach ($conditions as $key => $value) {
                $whereConditions[] = "{$key} = :{$key}";
                $params[$key] = $value;
            }
            $where = 'WHERE ' . implode(' AND ', $whereConditions);
        }
        
        $sql = "SELECT COUNT(*) as count FROM {$table} {$where}";
        $stmt = $this->prepare($sql);
        $stmt->execute($params);
        
        $result = $stmt->fetch();
        return (int) $result['count'];
    }
}
?>
