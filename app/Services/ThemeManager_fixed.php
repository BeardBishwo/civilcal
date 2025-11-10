<?php
/**
 * Enhanced Theme Manager Service with Database Integration - FIXED VERSION
 * 
 * Complete modular theme management system with CRUD operations
 * Database-driven theme management with backup and security features
 * 
 * @version 2.0.1
 * @author Bishwo Calculator Team
 * @package App\Services
 */

namespace App\Services;

use App\Models\Theme;
use PDOException;

class ThemeManager
{
    private $currentTheme;
    private $themesPath;
    private $activeTheme;
    private $baseUrl;
    private $assetsCache = [];
    private $themeModel;
    
    public function __construct()
    {
        $this->themesPath = BASE_PATH . '/themes/';
        $this->baseUrl = $this->getBaseUrl();
        $this->themeModel = new Theme();
        $this->loadActiveTheme();
    }

    /**
     * Get base URL
     */
    private function getBaseUrl()
    {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        $baseDir = dirname($scriptName);
        
        if ($baseDir === '/') {
            $baseDir = '';
        }
        
        return $protocol . '://' . $host . $baseDir;
    }

    /**
     * Load active theme from database
     */
    private function loadActiveTheme()
    {
        $activeThemeData = $this->themeModel->getActive();
        
        if ($activeThemeData) {
            $this->activeTheme = $activeThemeData['name'];
            $this->currentTheme = $activeThemeData['config'] ?? [];
            
            // Update session
            $_SESSION['active_theme'] = $this->activeTheme;
            $_SESSION['active_theme_id'] = $activeThemeData['id'];
        } else {
            // Fallback to default
            $this->activeTheme = 'default';
            $this->currentTheme = $this->getDefaultThemeConfig();
        }
    }

    /**
     * Get default theme configuration
     */
    private function getDefaultThemeConfig()
    {
        return [
            'name' => 'Default Theme',
            'version' => '1.0.0',
            'description' => 'Default theme for Bishwo Calculator',
            'author' => 'Bishwo Calculator',
            'styles' => [
                'css/theme.css',
                'css/header.css',
                'css/footer.css',
                'css/home.css',
                'css/back-to-top.css'
            ],
            'scripts' => [
                'js/main.js',
                'js/header.js',
                'js/home.js',
                'js/back-to-top.js'
            ],
            'category_styles' => [
                'civil' => 'css/civil.css',
                'electrical' => 'css/electrical.css',
                'plumbing' => 'css/plumbing.css',
                'hvac' => 'css/hvac.css',
                'fire' => 'css/fire.css',
                'structural' => 'css/structural.css',
                'site' => 'css/site.css',
                'estimation' => 'css/estimation.css',
                'management' => 'css/management.css',
                'mep' => 'css/mep.css'
            ]
        ];
    }

    /**
     * Get all themes from database
     */
    public function getAllThemes($status = null, $isPremium = null, $limit = null, $offset = 0)
    {
        return $this->themeModel->getAll($status, $isPremium, $limit, $offset);
    }

    /**
     * Get theme by ID
     */
    public function getThemeById($id)
    {
        return $this->themeModel->getById($id);
    }

    /**
     * Get theme by name
     */
    public function getThemeByName($name)
    {
        return $this->themeModel->getByName($name);
    }

    /**
     * Search themes
     */
    public function searchThemes($query, $limit = 20)
    {
        return $this->themeModel->search($query, $limit);
    }

    /**
     * Get theme statistics
     */
    public function getThemeStats()
    {
        return $this->themeModel->getStats();
    }

    /**
     * Create new theme
     */
    public function createTheme($data)
    {
        try {
            // Validate required fields
            if (empty($data['name']) || empty($data['display_name'])) {
                return [
                    'success' => false,
                    'message' => 'Theme name and display name are required'
                ];
            }

            // Check if theme name already exists
            if ($this->themeModel->nameExists($data['name'])) {
                return [
                    'success' => false,
                    'message' => 'Theme with this name already exists'
                ];
            }

            // Validate theme.json if provided
            if (isset($data['config_json'])) {
                $config = json_decode($data['config_json'], true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    return [
                        'success' => false,
                        'message' => 'Invalid JSON configuration'
                    ];
                }
                $data['config'] = $config;
            }

            // Calculate file size and checksum if theme directory exists
            $themePath = $this->themesPath . $data['name'];
            if (is_dir($themePath)) {
                $data['file_size'] = $this->calculateDirectorySize($themePath);
                $data['checksum'] = $this->generateThemeChecksum($themePath);
            }

            $themeId = $this->themeModel->create($data);
            
            if ($themeId) {
                return [
                    'success' => true,
                    'message' => 'Theme created successfully',
                    'theme_id' => $themeId
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to create theme'
            ];

        } catch (\Exception $e) {
            error_log("Theme Creation Error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error creating theme: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Update theme
     */
    public function updateTheme($id, $data)
    {
        try {
            $result = $this->themeModel->update($id, $data);
            
            if ($result) {
                // If updating active theme, reload configuration
                $activeTheme = $this->themeModel->getActive();
                if ($activeTheme && $activeTheme['id'] == $id) {
                    $this->loadActiveTheme();
                }

                return [
                    'success' => true,
                    'message' => 'Theme updated successfully'
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to update theme'
            ];

        } catch (\Exception $e) {
            error_log("Theme Update Error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error updating theme: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Activate theme
     */
    public function activateTheme($id)
    {
        try {
            $theme = $this->themeModel->getById($id);
            
            if (!$theme) {
                return [
                    'success' => false,
                    'message' => 'Theme not found'
                ];
            }

            if ($theme['status'] === 'deleted') {
                return [
                    'success' => false,
                    'message' => 'Cannot activate deleted theme'
                ];
            }

            $result = $this->themeModel->activate($id);
            
            if ($result) {
                // Update current theme
                $this->activeTheme = $theme['name'];
                $this->currentTheme = $theme['config'] ?? [];
                
                return [
                    'success' => true,
                    'message' => 'Theme activated successfully',
                    'theme_name' => $theme['name']
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to activate theme'
            ];

        } catch (\Exception $e) {
            error_log("Theme Activation Error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error activating theme: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Deactivate theme
     */
    public function deactivateTheme($id)
    {
        try {
            $result = $this->themeModel->deactivate($id);
            
            if ($result) {
                return [
                    'success' => true,
                    'message' => 'Theme deactivated successfully'
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to deactivate theme'
            ];

        } catch (\Exception $e) {
            error_log("Theme Deactivation Error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error deactivating theme: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Delete theme (soft delete with backup)
     */
    public function deleteTheme($id, $createBackup = true)
    {
        try {
            $theme = $this->themeModel->getById($id);
            
            if (!$theme) {
                return [
                    'success' => false,
                    'message' => 'Theme not found'
                ];
            }

            if ($theme['status'] === 'active') {
                return [
                    'success' => false,
                    'message' => 'Cannot delete active theme. Please activate another theme first.'
                ];
            }

            $result = $this->themeModel->delete($id, $createBackup);
            
            if ($result) {
                return [
                    'success' => true,
                    'message' => 'Theme deleted successfully' . ($createBackup ? ' (backup created)' : ''),
                    'backup_created' => $createBackup
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to delete theme'
            ];

        } catch (\Exception $e) {
            error_log("Theme Deletion Error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error deleting theme: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Restore deleted theme
     */
    public function restoreTheme($id)
    {
        try {
            $result = $this->themeModel->restore($id);
            
            if ($result) {
                return [
                    'success' => true,
                    'message' => 'Theme restored successfully'
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to restore theme'
            ];

        } catch (\Exception $e) {
            error_log("Theme Restoration Error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error restoring theme: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Hard delete theme
     */
    public function hardDeleteTheme($id)
    {
        try {
            $theme = $this->themeModel->getById($id);
            
            if (!$theme) {
                return [
                    'success' => false,
                    'message' => 'Theme not found'
                ];
            }

            if ($theme['status'] === 'active') {
                return [
                    'success' => false,
                    'message' => 'Cannot delete active theme'
                ];
            }

            $result = $this->themeModel->hardDelete($id);
            
            if ($result) {
                return [
                    'success' => true,
                    'message' => 'Theme permanently deleted'
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to delete theme'
            ];

        } catch (\Exception $e) {
            error_log("Theme Hard Delete Error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error deleting theme: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Install theme from ZIP file
     */
    public function installThemeFromZip($zipFile)
    {
        try {
            if (!file_exists($zipFile)) {
                return [
                    'success' => false,
                    'message' => 'Theme ZIP file not found'
                ];
            }

            // Validate ZIP file
            $zip = new \ZipArchive();
            if ($zip->open($zipFile) !== TRUE) {
                return [
                    'success' => false,
                    'message' => 'Cannot open ZIP file'
                ];
            }

            // Check for theme.json
            if ($zip->locateName('theme.json') === false) {
                $zip->close();
                return [
                    'success' => false,
                    'message' => 'Invalid theme: theme.json not found'
                ];
            }

            // Extract theme.json to read configuration
            $themeConfigJson = $zip->getFromName('theme.json');
            $themeConfig = json_decode($themeConfigJson, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                $zip->close();
                return [
                    'success' => false,
                    'message' => 'Invalid theme.json configuration'
                ];
            }

            $themeName = $themeConfig['slug'] ?? $themeConfig['name'] ?? '';
            if (empty($themeName)) {
                $zip->close();
                return [
                    'success' => false,
                    'message' => 'Theme name not found in configuration'
                ];
            }

            // Check if theme already exists
            if ($this->themeModel->nameExists($themeName)) {
                $zip->close();
                return [
                    'success' => false,
                    'message' => 'Theme with this name already exists'
                ];
            }

            // Extract to themes directory
            $extractPath = $this->themesPath . $themeName;
            $zip->extractTo($extractPath);
            $zip->close();

            // Create theme in database
            $themeData = [
                'name' => $themeName,
                'display_name' => $themeConfig['name'] ?? ucfirst($themeName),
                'version' => $themeConfig['version'] ?? '1.0.0',
                'author' => $themeConfig['author'] ?? 'Unknown',
                'description' => $themeConfig['description'] ?? '',
                'status' => 'inactive',
                'is_premium' => $themeConfig['premium'] ?? 0,
                'price' => $themeConfig['price'] ?? 0.00,
                'config' => $themeConfig,
                'file_size' => filesize($zipFile),
                'checksum' => hash_file('sha256', $zipFile)
            ];

            $themeId = $this->themeModel->create($themeData);
            
            if ($themeId) {
                return [
                    'success' => true,
                    'message' => 'Theme installed successfully',
                    'theme_id' => $themeId,
                    'theme_name' => $themeName
                ];
            }

            return [
                'success' => false,
                'message' => 'Theme installed but database record creation failed'
            ];

        } catch (\Exception $e) {
            error_log("Theme Installation Error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error installing theme: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get theme backups
     */
    public function getThemeBackups($themeId = null)
    {
        return $this->themeModel->getBackups($themeId);
    }

    /**
     * Validate theme integrity
     */
    public function validateTheme($themeName)
    {
        try {
            $themePath = $this->themesPath . $themeName;
            
            if (!is_dir($themePath)) {
                return [
                    'success' => false,
                    'message' => 'Theme directory not found'
                ];
            }

            $issues = [];
            
            // Check for theme.json
            if (!file_exists($themePath . '/theme.json')) {
                $issues[] = 'theme.json not found';
            } else {
                $config = json_decode(file_get_contents($themePath . '/theme.json'), true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    $issues[] = 'Invalid theme.json format';
                }
            }

            // Check for required directories
            $requiredDirs = ['assets', 'views'];
            foreach ($requiredDirs as $dir) {
                if (!is_dir($themePath . '/' . $dir)) {
                    $issues[] = "Required directory '{$dir}' not found";
                }
            }

            return [
                'success' => empty($issues),
                'message' => empty($issues) ? 'Theme validation passed' : 'Theme validation failed',
                'issues' => $issues
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error validating theme: ' . $e->getMessage(),
                'issues' => []
            ];
        }
    }

    /**
     * Calculate directory size
     */
    private function calculateDirectorySize($directory)
    {
        $size = 0;
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );
        
        foreach ($files as $file) {
            if ($file->isFile()) {
                $size += $file->getSize();
            }
        }
        
        return $size;
    }

    /**
     * Generate theme checksum
     */
    private function generateThemeChecksum($directory)
    {
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );
        
        $hashes = [];
        foreach ($files as $file) {
            if ($file->isFile()) {
                $hashes[] = hash_file('sha256', $file->getRealPath());
            }
        }
        
        sort($hashes);
        return hash('sha256', implode('', $hashes));
    }

    // Legacy methods for backward compatibility
    public function getThemeAsset($assetPath)
    {
        return $this->baseUrl . '/themes/' . $this->activeTheme . '/assets/' . ltrim($assetPath, '/');
    }

    public function loadThemeStyles()
    {
        if (isset($this->currentTheme['styles'])) {
            foreach ($this->currentTheme['styles'] as $style) {
                echo '<link rel="stylesheet" href="' . htmlspecialchars($this->getThemeAsset($style)) . '">' . PHP_EOL;
            }
        }
    }

    public function loadThemeScripts()
    {
        if (isset($this->currentTheme['scripts'])) {
            foreach ($this->currentTheme['scripts'] as $script) {
                echo '<script src="' . htmlspecialchars($this->getThemeAsset($script)) . '"></script>' . PHP_EOL;
            }
        }
    }

    /**
     * Get theme metadata for the current active theme
     */
    public function getThemeMetadata()
    {
        $activeThemeData = $this->themeModel->getActive();
        
        if ($activeThemeData) {
            return [
                'name' => $activeThemeData['name'],
                'display_name' => $activeThemeData['display_name'],
                'version' => $activeThemeData['version'],
                'author' => $activeThemeData['author'],
                'description' => $activeThemeData['description'],
                'is_premium' => (bool) $activeThemeData['is_premium'],
                'price' => (float) $activeThemeData['price'],
                'config' => $activeThemeData['config'] ?? [],
                'status' => $activeThemeData['status'],
                'activated_at' => $activeThemeData['activated_at'],
                'usage_count' => (int) $activeThemeData['usage_count']
            ];
        }
        
        return [
            'name' => $this->activeTheme,
            'display_name' => 'Default Theme',
            'version' => '1.0.0',
            'author' => 'Bishwo Calculator Team',
            'description' => 'Default theme configuration',
            'is_premium' => false,
            'price' => 0.00,
            'config' => $this->currentTheme,
            'status' => 'active'
        ];
    }

    /**
     * Get available themes list
     */
    public function getAvailableThemes()
    {
        $themes = $this->themeModel->getAll();
        $availableThemes = [];
        
        foreach ($themes as $theme) {
            if ($theme['status'] !== 'deleted') {
                $availableThemes[] = [
                    'id' => $theme['id'],
                    'name' => $theme['name'],
                    'display_name' => $theme['display_name'],
                    'version' => $theme['version'],
                    'author' => $theme['author'],
                    'description' => $theme['description'],
                    'is_premium' => (bool) $theme['is_premium'],
                    'price' => (float) $theme['price'],
                    'status' => $theme['status'],
                    'screenshot_path' => $theme['screenshot_path'],
                    'created_at' => $theme['created_at'],
                    'updated_at' => $theme['updated_at']
                ];
            }
        }
        
        return $availableThemes;
    }

    /**
     * Get active theme name - SINGLE INSTANCE (no duplicates)
     */
    public function getActiveTheme()
    {
        return $this->activeTheme;
    }

    public function getThemeConfig()
    {
        return $this->currentTheme;
    }

    public function themeUrl($path = '')
    {
        return $this->baseUrl . '/themes/' . $this->activeTheme . '/' . ltrim($path, '/');
    }

    public function assetsUrl($path = '')
    {
        return $this->baseUrl . '/themes/' . $this->activeTheme . '/assets/' . ltrim($path, '/');
    }
}
?>
