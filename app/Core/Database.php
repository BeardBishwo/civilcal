<?php

namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private static $instance = null;
    private $pdoWrite;
    private $pdoRead;
    private $inTransaction = false;

    private function __construct()
    {
        $configFile = __DIR__ . '/../../config/database.php';

        if (!file_exists($configFile)) {
            throw new \Exception("Database configuration file not found: $configFile");
        }

        $config = include $configFile;

        // Connect to Write (Master)
        $this->pdoWrite = $this->createConnection($config, 'write');

        // Connect to Read (Replica) - fallback to write if not configured
        if (isset($config['read'])) {
            $this->pdoRead = $this->createConnection(array_merge($config, $config['read']), 'read');
        } else {
            $this->pdoRead = $this->pdoWrite;
        }
    }

    private function createConnection($config, $type)
    {
        try {
            $host = $config['host'] ?? '127.0.0.1';
            $dsn = "mysql:host={$host};dbname={$config['database']}";

            if (isset($config['charset'])) {
                $dsn .= ";charset={$config['charset']}";
            }

            $password = $config['password'] ?? '';

            return new PDO(
                $dsn,
                $config['username'],
                $password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4",
                    PDO::ATTR_TIMEOUT => 5 // Fail fast if DB is unreachable
                ]
            );
        } catch (PDOException $e) {
            throw new \Exception("Database ($type) connection failed: " . $e->getMessage());
        }
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getPdo()
    {
        return $this->pdoWrite; // Default to write connection for backward compatibility
    }

    public function getReadPdo()
    {
        return $this->pdoRead;
    }

    public function beginTransaction()
    {
        $this->inTransaction = true;
        return $this->pdoWrite->beginTransaction();
    }

    public function commit()
    {
        $this->inTransaction = false;
        return $this->pdoWrite->commit();
    }

    public function rollBack()
    {
        $this->inTransaction = false;
        return $this->pdoWrite->rollBack();
    }

    public function query($sql, $params = [])
    {
        // Determine connection
        // Use Write connection if:
        // 1. We are in a transaction (crucial for consistency)
        // 2. It's not a SELECT statement
        $isSelect = stripos(trim($sql), 'SELECT') === 0;

        $pdo = ($this->inTransaction || !$isSelect) ? $this->pdoWrite : $this->pdoRead;

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        if (class_exists('\App\Core\PDOCompat')) {
            return new \App\Core\PDOCompat($stmt);
        }

        return $stmt;
    }

    public function fetchAll($sql, $params = [])
    {
        $stmt = $this->query($sql, $params);
        return ($stmt && method_exists($stmt, 'fetchAll')) ? $stmt->fetchAll(\PDO::FETCH_ASSOC) : [];
    }

    public function fetch($sql, $params = [])
    {
        $stmt = $this->query($sql, $params);
        return ($stmt && method_exists($stmt, 'fetch')) ? $stmt->fetch(\PDO::FETCH_ASSOC) : false;
    }

    public function prepare($sql)
    {
        // For manual prepare, we default to Write to be safe,
        // or we could analyze the SQL. For now, safety first.
        return $this->pdoWrite->prepare($sql);
    }

    public function insert($table, $data)
    {
        $keys = array_keys($data);
        $columns = implode(', ', array_map(function ($key) {
            return "`$key`";
        }, $keys));

        $placeholders = ':' . implode(', :', $keys);

        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
        $stmt = $this->prepare($sql);

        return $stmt->execute($data);
    }

    public function update($table, $data, $where, $whereParams = [])
    {
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

    public function delete($table, $where, $whereParams = [])
    {
        $sql = "DELETE FROM {$table} WHERE {$where}";
        $stmt = $this->prepare($sql);

        return $stmt->execute($whereParams);
    }

    /**
     * @return array
     */
    public function find($table, $conditions = [], $orderBy = '', $limit = null)
    {
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
            $limit = (int)$limit; // Sanitize to prevent SQL injection
            $sql .= " LIMIT {$limit}";
        }

        $stmt = $this->pdoRead->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }


    public function findOne($table, $conditions = [])
    {
        $results = $this->find($table, $conditions, '', 1);
        return !empty($results) ? $results[0] : null;
    }

    public function count($table, $conditions = [])
    {
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
        $stmt = $this->pdoRead->prepare($sql);
        $stmt->execute($params);

        $result = $stmt->fetch();
        return (int) $result['count'];
    }

    /**
     * Get the ID of the last inserted row
     * @return string The last inserted ID
     */
    public function lastInsertId()
    {
        return $this->pdoWrite->lastInsertId();
    }
}
