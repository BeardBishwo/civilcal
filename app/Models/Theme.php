<?php
/**
 * Theme Model - FIXED VERSION
 * 
 * Handles all database operations for theme management
 * 
 * @package App\Models
 * @version 1.0.1
 * @author Bishwo Calculator Team
 */

namespace App\Models;

use PDO;
use PDOException;

class Theme
{
    private $db;
    private $table = 'themes';
    
    public function __construct()
    {
        // Include the database configuration
        require_once __DIR__ . '/../Config/config.php';
        require_once __DIR__ . '/../Config/db.php';
        
        // Get database connection using the global get_db() function
        $this->db = get_db();
        
        // If database connection failed, log the error
        if (!$this->db) {
            error_log("Theme Model Error: Failed to establish database connection");
        }
    }
    
    /**
     * Get all themes with optional filtering
     */
    public function getAll($status = null, $isPremium = null, $limit = null, $offset = 0)
    {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE 1=1";
            $params = [];
            
            if ($status) {
                $sql .= " AND status = :status";
                $params[':status'] = $status;
            }
            
            if ($isPremium !== null) {
                $sql .= " AND is_premium = :is_premium";
                $params[':is_premium'] = $isPremium;
            }
            
            $sql .= " ORDER BY 
                        CASE WHEN status = 'active' THEN 1 
                             WHEN status = 'inactive' THEN 2 
                             WHEN status = 'deleted' THEN 3 
                        END, 
                        is_premium DESC, display_name ASC";
            
            if ($limit) {
                $sql .= " LIMIT :limit OFFSET :offset";
                $params[':limit'] = $limit;
                $params[':offset'] = $offset;
            }
            
            $stmt = $this->db->prepare($sql);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
            }
            
            $stmt->execute();
            $themes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Decode JSON fields with null checks
            foreach ($themes as &$theme) {
                $theme['config'] = $theme['config_json'] ? json_decode($theme['config_json'], true) : [];
                $theme['settings'] = $theme['settings_json'] ? json_decode($theme['settings_json'], true) : [];
                unset($theme['config_json'], $theme['settings_json']);
            }
            
            return $themes;
            
        } catch (PDOException $e) {
            error_log("Theme Model Error (getAll): " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get theme by ID
     */
    public function getById($id)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            $theme = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($theme) {
                $theme['config'] = $theme['config_json'] ? json_decode($theme['config_json'], true) : [];
                $theme['settings'] = $theme['settings_json'] ? json_decode($theme['settings_json'], true) : [];
                unset($theme['config_json'], $theme['settings_json']);
            }
            
            return $theme;
            
        } catch (PDOException $e) {
            error_log("Theme Model Error (getById): " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Get theme by name
     */
    public function getByName($name)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE name = :name");
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->execute();
            
            $theme = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($theme) {
                $theme['config'] = $theme['config_json'] ? json_decode($theme['config_json'], true) : [];
                $theme['settings'] = $theme['settings_json'] ? json_decode($theme['settings_json'], true) : [];
                unset($theme['config_json'], $theme['settings_json']);
            }
            
            return $theme;
            
        } catch (PDOException $e) {
            error_log("Theme Model Error (getByName): " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Get active theme
     */
    public function getActive()
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE status = 'active' LIMIT 1");
            $stmt->execute();
            
            $theme = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($theme) {
                $theme['config'] = $theme['config_json'] ? json_decode($theme['config_json'], true) : [];
                $theme['settings'] = $theme['settings_json'] ? json_decode($theme['settings_json'], true) : [];
                unset($theme['config_json'], $theme['settings_json']);
            }
            
            return $theme;
            
        } catch (PDOException $e) {
            error_log("Theme Model Error (getActive): " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Create new theme
     */
    public function create($data)
    {
        try {
            $sql = "INSERT INTO {$this->table} 
                    (name, display_name, version, author, description, status, is_premium, price, config_json, file_size, checksum, screenshot_path, settings_json, created_at) 
                    VALUES 
                    (:name, :display_name, :version, :author, :description, :status, :is_premium, :price, :config_json, :file_size, :checksum, :screenshot_path, :settings_json, NOW())";
            
            $stmt = $this->db->prepare($sql);
            
            $result = $stmt->execute([
                ':name' => $data['name'],
                ':display_name' => $data['display_name'],
                ':version' => $data['version'] ?? '1.0.0',
                ':author' => $data['author'] ?? 'Unknown',
                ':description' => $data['description'] ?? '',
                ':status' => $data['status'] ?? 'inactive',
                ':is_premium' => $data['is_premium'] ?? 0,
                ':price' => $data['price'] ?? 0.00,
                ':config_json' => json_encode($data['config'] ?? []),
                ':file_size' => $data['file_size'] ?? null,
                ':checksum' => $data['checksum'] ?? null,
                ':screenshot_path' => $data['screenshot_path'] ?? null,
                ':settings_json' => json_encode($data['settings'] ?? [])
            ]);
            
            if ($result) {
                return $this->db->lastInsertId();
            }
            return false;
            
        } catch (PDOException $e) {
            error_log("Theme Model Error (create): " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update theme
     */
    public function update($id, $data)
    {
        try {
            $sql = "UPDATE {$this->table} SET 
                    display_name = :display_name,
                    version = :version,
                    author = :author,
                    description = :description,
                    is_premium = :is_premium,
                    price = :price,
                    config_json = :config_json,
                    file_size = :file_size,
                    checksum = :checksum,
                    screenshot_path = :screenshot_path,
                    settings_json = :settings_json,
                    updated_at = NOW()
                    WHERE id = :id";
            
            $stmt = $this->db->prepare($sql);
            
            return $stmt->execute([
                ':id' => $id,
                ':display_name' => $data['display_name'],
                ':version' => $data['version'] ?? '1.0.0',
                ':author' => $data['author'] ?? 'Unknown',
                ':description' => $data['description'] ?? '',
                ':is_premium' => $data['is_premium'] ?? 0,
                ':price' => $data['price'] ?? 0.00,
                ':config_json' => json_encode($data['config'] ?? []),
                ':file_size' => $data['file_size'] ?? null,
                ':checksum' => $data['checksum'] ?? null,
                ':screenshot_path' => $data['screenshot_path'] ?? null,
                ':settings_json' => json_encode($data['settings'] ?? [])
            ]);
            
        } catch (PDOException $e) {
            error_log("Theme Model Error (update): " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete theme (soft delete)
     */
    public function delete($id, $createBackup = true)
    {
        try {
            if ($createBackup) {
                $this->createBackup($id);
            }
            
            $stmt = $this->db->prepare("UPDATE {$this->table} SET status = 'deleted', updated_at = NOW() WHERE id = :id");
            return $stmt->execute([':id' => $id]);
            
        } catch (PDOException $e) {
            error_log("Theme Model Error (delete): " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Hard delete theme
     */
    public function hardDelete($id)
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = :id");
            return $stmt->execute([':id' => $id]);
            
        } catch (PDOException $e) {
            error_log("Theme Model Error (hardDelete): " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Activate theme
     */
    public function activate($id)
    {
        try {
            $this->db->beginTransaction();
            
            // Deactivate all other themes
            $stmt1 = $this->db->prepare("UPDATE {$this->table} SET status = 'inactive' WHERE status = 'active'");
            $stmt1->execute();
            
            // Activate this theme
            $stmt2 = $this->db->prepare("UPDATE {$this->table} SET status = 'active', activated_at = NOW(), usage_count = usage_count + 1, updated_at = NOW() WHERE id = :id");
            $result = $stmt2->execute([':id' => $id]);
            
            if ($result) {
                $this->db->commit();
                return true;
            } else {
                $this->db->rollBack();
                return false;
            }
            
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("Theme Model Error (activate): " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Deactivate theme
     */
    public function deactivate($id)
    {
        try {
            $stmt = $this->db->prepare("UPDATE {$this->table} SET status = 'inactive', updated_at = NOW() WHERE id = :id");
            return $stmt->execute([':id' => $id]);
            
        } catch (PDOException $e) {
            error_log("Theme Model Error (deactivate): " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Restore deleted theme
     */
    public function restore($id)
    {
        try {
            $stmt = $this->db->prepare("UPDATE {$this->table} SET status = 'inactive', updated_at = NOW() WHERE id = :id AND status = 'deleted'");
            return $stmt->execute([':id' => $id]);
            
        } catch (PDOException $e) {
            error_log("Theme Model Error (restore): " . $e->getMessage());
            return false;
        }
    }
    
    public function updateSettings($id, array $settings)
    {
        try {
            $stmt = $this->db->prepare("UPDATE {$this->table} SET settings_json = :settings_json, updated_at = NOW() WHERE id = :id");
            return $stmt->execute([
                ':id' => $id,
                ':settings_json' => json_encode($settings)
            ]);
        } catch (PDOException $e) {
            error_log("Theme Model Error (updateSettings): " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get theme statistics
     */
    public function getStats()
    {
        try {
            $sql = "SELECT 
                        COUNT(*) as total,
                        SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active,
                        SUM(CASE WHEN status = 'inactive' THEN 1 ELSE 0 END) as inactive,
                        SUM(CASE WHEN status = 'deleted' THEN 1 ELSE 0 END) as deleted,
                        SUM(CASE WHEN is_premium = 1 THEN 1 ELSE 0 END) as premium
                    FROM {$this->table}";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Theme Model Error (getStats): " . $e->getMessage());
            return [
                'total' => 0,
                'active' => 0,
                'inactive' => 0,
                'deleted' => 0,
                'premium' => 0
            ];
        }
    }
    
    /**
     * Search themes
     */
    public function search($query, $limit = 20)
    {
        try {
            $sql = "SELECT * FROM {$this->table} 
                    WHERE (display_name LIKE :query OR description LIKE :query OR author LIKE :query) 
                    AND status != 'deleted'
                    ORDER BY 
                        CASE WHEN display_name LIKE :exact_match THEN 1 ELSE 2 END,
                        is_premium DESC,
                        display_name ASC
                    LIMIT :limit";
            
            $stmt = $this->db->prepare($sql);
            $searchTerm = "%{$query}%";
            $exactMatch = $query;
            $stmt->bindValue(':query', $searchTerm, PDO::PARAM_STR);
            $stmt->bindValue(':exact_match', $exactMatch, PDO::PARAM_STR);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            $themes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Decode JSON fields with null checks
            foreach ($themes as &$theme) {
                $theme['config'] = $theme['config_json'] ? json_decode($theme['config_json'], true) : [];
                $theme['settings'] = $theme['settings_json'] ? json_decode($theme['settings_json'], true) : [];
                unset($theme['config_json'], $theme['settings_json']);
            }
            
            return $themes;
            
        } catch (PDOException $e) {
            error_log("Theme Model Error (search): " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Check if theme name exists
     */
    public function nameExists($name, $excludeId = null)
    {
        try {
            $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE name = :name";
            $params = [':name' => $name];
            
            if ($excludeId) {
                $sql .= " AND id != :exclude_id";
                $params[':exclude_id'] = $excludeId;
            }
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $result['count'] > 0;
            
        } catch (PDOException $e) {
            error_log("Theme Model Error (nameExists): " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Create backup of theme
     */
    private function createBackup($id)
    {
        try {
            $theme = $this->getById($id);
            if (!$theme) return false;
            
            $backupDir = __DIR__ . '/../../storage/theme_backups/';
            if (!is_dir($backupDir)) {
                mkdir($backupDir, 0755, true);
            }
            
            $filename = "theme_backup_{$id}_" . date('Y-m-d_H-i-s') . '.json';
            $filepath = $backupDir . $filename;
            
            $backupData = [
                'theme_data' => $theme,
                'created_at' => date('Y-m-d H:i:s'),
                'backup_version' => '1.0.0'
            ];
            
            return file_put_contents($filepath, json_encode($backupData, JSON_PRETTY_PRINT)) !== false;
            
        } catch (\Exception $e) {
            error_log("Theme Model Error (createBackup): " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get theme backups
     */
    public function getBackups($id = null)
    {
        try {
            $backupDir = __DIR__ . '/../../storage/theme_backups/';
            if (!is_dir($backupDir)) {
                return [];
            }
            
            $files = glob($backupDir . 'theme_backup_*.json');
            $backups = [];
            
            foreach ($files as $file) {
                $content = file_get_contents($file);
                $data = json_decode($content, true);
                
                if ($data && (!$id || strpos($file, "theme_backup_{$id}_") !== false)) {
                    $backups[] = [
                        'filename' => basename($file),
                        'filepath' => $file,
                        'created_at' => $data['created_at'] ?? date('Y-m-d H:i:s', filemtime($file)),
                        'size' => filesize($file)
                    ];
                }
            }
            
            // Sort by creation date (newest first)
            usort($backups, function($a, $b) {
                return strtotime($b['created_at']) - strtotime($a['created_at']);
            });
            
            return $backups;
            
        } catch (\Exception $e) {
            error_log("Theme Model Error (getBackups): " . $e->getMessage());
            return [];
        }
    }
}
