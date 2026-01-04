<?php

namespace App\Core;

use PDO;
use PDOException;

class Database {
    private static $instance = null;
    private $pdo;
    
    private function __construct() {
        $configFile = __DIR__ . '/../../config/database.php';
        
        if (!file_exists($configFile)) {
            throw new \Exception("Database configuration file not found: $configFile");
        }
        
        // Include the file to get the config array
        $config = include $configFile;
        
        if (!is_array($config)) {
            throw new \Exception("Database configuration is not an array. Got: " . gettype($config));
        }
        
        // Validate required configuration keys
        $requiredKeys = ['host', 'database', 'username'];
        foreach ($requiredKeys as $key) {
            if (!isset($config[$key])) {
                throw new \Exception("Missing required database configuration key: $key");
            }
        }
        
        try {
            $dsn = "mysql:host={$config['host']};dbname={$config['database']}";
            if (isset($config['charset'])) {
                $dsn .= ";charset={$config['charset']}";
            }
            
            $password = $config['password'] ?? '';
            
            $this->pdo = new PDO(
                $dsn,
                $config['username'],
                $password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
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
        /**
         * Execute a prepared statement and return a PDOCompat wrapper around
         * the PDOStatement so legacy code can use fetch_assoc() while modern
         * code can still call fetchAll()/fetch() via delegation.
         *
         * @param string $sql
         * @param array $params
         * @return \App\Core\PDOCompat|\PDOStatement
         */
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        // Return a compatibility wrapper that exposes fetch_assoc()
        if (class_exists('\App\Core\PDOCompat')) {
            return new \App\Core\PDOCompat($stmt);
        }

        return $stmt;
    }
    
    public function prepare($sql) {
        return $this->pdo->prepare($sql);
    }
    
    public function insert($table, $data) {
        $keys = array_keys($data);
        $columns = implode(', ', array_map(function($key) {
            return "`$key`";
        }, $keys));
        
        $placeholders = ':' . implode(', :', $keys);
        
        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
        $stmt = $this->prepare($sql);
        
        return $stmt->execute($data);
    }
    
    public function update($table, $data, $where, $whereParams = []) {
        $set = [];
        foreach ($data as $key => $value) {
            $set[] = "`{$key}` = :{$key}";
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
    
    /**
     * @return array
     */
    public function find($table, $conditions = [], $orderBy = '', $limit = null) {
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
        
        $sql = "SELECT * FROM {$table} {$where}";
        
        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy}";
        }
        
        if ($limit) {
            $sql .= " LIMIT {$limit}";
        }
        
        $stmt = $this->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll();
    }
    
    
    public function findOne($table, $conditions = []) {
        $results = $this->find($table, $conditions, '', 1);
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
    
    /**
     * Get the ID of the last inserted row
     * @return string The last inserted ID
     */
    public function lastInsertId() {
        return $this->pdo->lastInsertId();
    }

    public function beginTransaction() {
        return $this->pdo->beginTransaction();
    }

    public function commit() {
        return $this->pdo->commit();
    }

    public function rollBack() {
        return $this->pdo->rollBack();
    }
}
?>
