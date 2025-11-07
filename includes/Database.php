<?php
/**
 * Database class for EngiCal Pro authentication system
 * Provides a secure connection to the MySQL database using PDO
 */
class Database {
    private $host;
    private $db_name;
    private $username;
    private $password;
    private $charset;
    public $conn;

    /**
     * Constructor - initialize database connection parameters
     */
    public function __construct() {
        // Load configuration from config.php
        $this->host = DB_HOST ?? '127.0.0.1';
        $this->db_name = DB_NAME ?? 'aec_calculator';
        $this->username = DB_USER ?? 'root';
        $this->password = DB_PASS ?? '';
        $this->charset = 'utf8mb4';
    }

    /**
     * Get database connection
     * @return PDO|null Database connection object or null on failure
     */
    public function getConnection() {
        $this->conn = null;
        
        try {
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=" . $this->charset;
            
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
            ];
            
            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
            
        } catch(PDOException $e) {
            // Log error but don't expose details to user
            error_log('Database connection error: ' . $e->getMessage());
            $this->conn = null;
        }
        
        return $this->conn;
    }

    /**
     * Test database connection
     * @return bool True if connection successful, false otherwise
     */
    public function testConnection() {
        try {
            $conn = $this->getConnection();
            if ($conn) {
                $stmt = $conn->query('SELECT 1');
                return $stmt !== false;
            }
        } catch (Exception $e) {
            error_log('Database test connection error: ' . $e->getMessage());
        }
        return false;
    }

    /**
     * Get the current connection
     * @return PDO|null
     */
    public function getConnectionObject() {
        if ($this->conn === null) {
            return $this->getConnection();
        }
        return $this->conn;
    }

    /**
     * Close database connection
     */
    public function closeConnection() {
        $this->conn = null;
    }

    /**
     * Execute a prepared statement with error handling
     * @param string $sql SQL query
     * @param array $params Query parameters
     * @return PDOStatement|false Statement object or false on failure
     */
    public function executeQuery($sql, $params = []) {
        try {
            $conn = $this->getConnectionObject();
            if (!$conn) {
                throw new Exception('Database connection failed');
            }
            
            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
            return $stmt;
            
        } catch (PDOException $e) {
            error_log('Query execution error: ' . $e->getMessage() . ' | SQL: ' . $sql);
            return false;
        } catch (Exception $e) {
            error_log('General query error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Begin a database transaction
     * @return bool True if transaction started successfully, false otherwise
     */
    public function beginTransaction() {
        try {
            $conn = $this->getConnectionObject();
            if ($conn) {
                return $conn->beginTransaction();
            }
        } catch (Exception $e) {
            error_log('Begin transaction error: ' . $e->getMessage());
        }
        return false;
    }

    /**
     * Commit a database transaction
     * @return bool True if transaction committed successfully, false otherwise
     */
    public function commit() {
        try {
            $conn = $this->getConnectionObject();
            if ($conn) {
                return $conn->commit();
            }
        } catch (Exception $e) {
            error_log('Commit transaction error: ' . $e->getMessage());
        }
        return false;
    }

    /**
     * Rollback a database transaction
     * @return bool True if transaction rolled back successfully, false otherwise
     */
    public function rollback() {
        try {
            $conn = $this->getConnectionObject();
            if ($conn) {
                return $conn->rollback();
            }
        } catch (Exception $e) {
            error_log('Rollback transaction error: ' . $e->getMessage());
        }
        return false;
    }

    /**
     * Get the last inserted ID
     * @return string Last inserted ID
     */
    public function lastInsertId() {
        try {
            $conn = $this->getConnectionObject();
            if ($conn) {
                return $conn->lastInsertId();
            }
        } catch (Exception $e) {
            error_log('Last insert ID error: ' . $e->getMessage());
        }
        return 0;
    }
}
?>
