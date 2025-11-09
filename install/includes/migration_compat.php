<?php
/**
 * Installation Migration Compatibility Layer
 * Makes Database class available during installation process
 */

class MigrationCompat {
    private static $pdo = null;
    
    public static function setPdo($pdo) {
        self::$pdo = $pdo;
    }
    
    public static function getPdo() {
        return self::$pdo;
    }
}

// Simple Database class wrapper for installation
if (!class_exists('\App\Core\Database')) {
    class AppCoreDatabase {
        private static $instance = null;
        private $pdo;
        
        public function __construct() {
            $this->pdo = MigrationCompat::getPdo();
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
        
        public function exec($sql) {
            return $this->pdo->exec($sql);
        }
        
        public function prepare($sql) {
            return $this->pdo->prepare($sql);
        }
        
        public function query($sql) {
            return $this->pdo->query($sql);
        }
    }
    
    // Create class alias
    class_alias('AppCoreDatabase', 'App\Core\Database');
}
?>
