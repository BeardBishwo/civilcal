<?php
/**
 * Enhanced Theme Manager Service with Database Integration
 * 
 * Complete modular theme management system with CRUD operations
 * Database-driven theme management with backup and security features
 * 
 * @version 2.0.0
 * @author Bishwo Calculator Team
 * @package App\Services
 */

namespace App\Services;

use App\Models\Theme;
use App\Core\View;
use PDOException;
use Exception;

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
     * Works in both subdirectory and document root installations
     */
    private function getBaseUrl()
    {
        if (defined('APP_URL') && APP_URL) {
            return rtrim(APP_URL, '/');
        }

        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        $baseDir = dirname($scriptName);

        if (defined('APP_BASE')) {
            $baseDir = APP_BASE;
        }

        if (substr($baseDir, -7) === '/public') {
            $baseDir = substr($baseDir, 0, -7);
        }

        if ($baseDir === '/' || $baseDir === '\\' || $baseDir === '.') {
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
            
            // Load theme.json if it exists
            $configPath = $this->themesPath . $this->activeTheme . '/theme.json';
            if (file_exists($configPath)) {
                $themeConfig = json_decode(file_get_contents($configPath), true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $this->currentTheme = $themeConfig;
                }
            }
            
            // Update session
            $_SESSION['active_theme'] = $this->activeTheme;
            $_SESSION['active_theme_id'] = $activeThemeData['id'];
        } else {
            // Fallback to default
            $this->activeTheme = 'default';
            $this->currentTheme = $this->getDefaultThemeConfig();
            
            // Load default theme.json if exists
            $configPath = $this->themesPath . 'default/theme.json';
            if (file_exists($configPath)) {
                $themeConfig = json_decode(file_get_contents($configPath), true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $this->currentTheme = $themeConfig;
                }
            }
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

        } catch (Exception $e) {
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

        } catch (Exception $e) {
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

        } catch (Exception $e) {
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

        } catch (Exception $e) {
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

        } catch (Exception $e) {
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

        } catch (Exception $e) {
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

        } catch (Exception $e) {
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

            // Secure extraction: extract to temp directory first and validate paths
            $tempBase = (defined('STORAGE_PATH') ? STORAGE_PATH : sys_get_temp_dir()) . '/tmp_theme_' . bin2hex(random_bytes(8));
            if (!is_dir($tempBase)) {
                mkdir($tempBase, 0755, true);
            }

            $tempExtract = $tempBase . '/' . $themeName;
            if (!is_dir($tempExtract)) {
                mkdir($tempExtract, 0755, true);
            }

            for ($i = 0; $i < $zip->numFiles; $i++) {
                $entry = $zip->getNameIndex($i);
                if ($entry === false) { continue; }
                // Normalize entry path and prevent traversal
                $entry = str_replace('..', '', $entry);
                $entry = ltrim($entry, '/\\');
                if ($entry === '' || substr($entry, -1) === '/') {
                    // Directory entry
                    $dirPath = $tempExtract . '/' . $entry;
                    if (!is_dir($dirPath)) { mkdir($dirPath, 0755, true); }
                    continue;
                }
                $dest = $tempExtract . '/' . $entry;
                $destDir = dirname($dest);
                if (!is_dir($destDir)) { mkdir($destDir, 0755, true); }
                $contents = $zip->getFromIndex($i);
                if ($contents === false) { continue; }
                file_put_contents($dest, $contents);
                // Verify path stays within tempExtract
                $realTemp = realpath($tempExtract);
                $realDest = realpath($dest);
                if ($realTemp === false || $realDest === false || strpos($realDest, $realTemp) !== 0) {
                    @unlink($dest);
                    $zip->close();
                    $this->rrmdir($tempBase);
                    return [
                        'success' => false,
                        'message' => 'Invalid archive path detected'
                    ];
                }
            }
            $zip->close();

            // Post-extract limits
            list($totalBytes, $fileCount) = $this->dirStats($tempExtract);
            $maxBytes = 100 * 1024 * 1024; // 100MB
            $maxFiles = 10000;
            if ($totalBytes > $maxBytes || $fileCount > $maxFiles) {
                $this->rrmdir($tempBase);
                return [
                    'success' => false,
                    'message' => 'Theme package too large'
                ];
            }

            // Validate manifest and structure
            $val = $this->validateThemeManifest($tempExtract, $themeConfig);
            if (!($val['success'] ?? false)) {
                $this->rrmdir($tempBase);
                return $val;
            }

            // Move extracted theme into themes directory
            $extractPath = $this->themesPath . $themeName;
            if (is_dir($extractPath)) {
                $this->rrmdir($extractPath);
            }
            $this->rcopy($tempExtract, $extractPath);
            $this->rrmdir($tempBase);

            $screenshotPath = null;
            $candidates = ['screenshot.png','screenshot.jpg','screenshot.jpeg','preview.png','preview.jpg','preview.jpeg'];
            foreach ($candidates as $cand) {
                $p = $extractPath . '/' . $cand;
                if (file_exists($p)) { $screenshotPath = $p; break; }
            }
            if ($screenshotPath === null) {
                foreach ($candidates as $cand) {
                    $p = $extractPath . '/assets/' . $cand;
                    if (file_exists($p)) { $screenshotPath = $p; break; }
                }
            }
            $publicScreenshotUrl = null;
            if ($screenshotPath) {
                $ext = strtolower(pathinfo($screenshotPath, PATHINFO_EXTENSION));
                $publicDir = BASE_PATH . '/public/assets/theme-previews';
                if (!is_dir($publicDir)) { @mkdir($publicDir, 0755, true); }
                $publicFile = $publicDir . '/' . $themeName . '.' . $ext;
                @copy($screenshotPath, $publicFile);
                $publicScreenshotUrl = '/assets/theme-previews/' . $themeName . '.' . $ext;
            }

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
                'checksum' => hash_file('sha256', $zipFile),
                'screenshot_path' => $publicScreenshotUrl
            ];

            $themeId = $this->themeModel->create($themeData);
            
            if ($themeId) {
                return [
                    'success' => true,
                    'message' => 'Theme installed successfully',
                    'theme_id' => $themeId,
                    'theme_name' => $themeName,
                    'checksum' => $themeData['checksum'],
                    'file_size' => $themeData['file_size'],
                    'screenshot_path' => $themeData['screenshot_path']
                ];
            }

            return [
                'success' => false,
                'message' => 'Theme installed but database record creation failed'
            ];

        } catch (Exception $e) {
            error_log("Theme Installation Error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error installing theme: ' . $e->getMessage()
            ];
        }
    }

    private function rcopy($src, $dst) {
        $dir = opendir($src);
        @mkdir($dst, 0755, true);
        while(false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . '/' . $file)) {
                    $this->rcopy($src . '/' . $file, $dst . '/' . $file);
                } else {
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }

    private function rrmdir($dir) {
        if (!is_dir($dir)) return;
        $items = scandir($dir);
        foreach ($items as $item) {
            if ($item == '.' || $item == '..') continue;
            $path = $dir . DIRECTORY_SEPARATOR . $item;
            if (is_dir($path)) {
                $this->rrmdir($path);
            } else {
                @unlink($path);
            }
        }
        @rmdir($dir);
    }

    private function dirStats(string $dir): array
    {
        $bytes = 0; $count = 0;
        $it = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS)
        );
        foreach ($it as $file) {
            if ($file->isFile()) { $bytes += $file->getSize(); $count++; }
        }
        return [$bytes, $count];
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

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error validating theme: ' . $e->getMessage(),
                'issues' => []
            ];
        }
    }

    private function validateThemeManifest(string $themeDir, array $config): array
    {
        $baseReal = realpath($themeDir) ?: $themeDir;
        // Required fields
        $name = $config['slug'] ?? ($config['name'] ?? null);
        $version = $config['version'] ?? null;
        if (!$name || !$version) {
            return ['success' => false, 'message' => 'theme.json missing name/slug or version'];
        }
        // Required directories
        $requiredDirs = ['assets','views'];
        foreach ($requiredDirs as $d) {
            if (!is_dir($themeDir . '/' . $d)) {
                return ['success' => false, 'message' => "Required directory '{$d}' not found"];
            }
        }
        // Validate asset lists if present
        foreach (['styles','scripts'] as $key) {
            if (isset($config[$key])) {
                if (!is_array($config[$key])) {
                    return ['success' => false, 'message' => "$key must be an array"];
                }
                foreach ($config[$key] as $rel) {
                    if (!is_string($rel) || $rel === '') {
                        return ['success' => false, 'message' => "$key entries must be non-empty strings"];
                    }
                    $full = rtrim($themeDir, '/\\') . '/' . ltrim($rel, '/\\');
                    $real = realpath($full);
                    if ($real === false || !is_file($real)) {
                        return ['success' => false, 'message' => "Referenced asset not found: $rel"];
                    }
                    if (strpos($real, $baseReal) !== 0) {
                        return ['success' => false, 'message' => 'Asset path traversal detected'];
                    }
                }
            }
        }
        return ['success' => true, 'message' => 'Manifest validated'];
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
        // Remove 'assets/' prefix if present since we're serving from /assets/themes/
        $cleanPath = ltrim($assetPath, '/');
        if (strpos($cleanPath, 'assets/') === 0) {
            $cleanPath = substr($cleanPath, 7); // Remove 'assets/' prefix
        }
        
        return $this->baseUrl . '/assets/themes/' . $this->activeTheme . '/' . $cleanPath;
    }

    public function loadThemeStyles()
    {
        $styles = $this->currentTheme['styles'] ?? [];
        foreach ($styles as $style) {
            $url = $this->getThemeAssetUrl($style);
            echo '<link rel="stylesheet" href="' . htmlspecialchars($url) . '">' . PHP_EOL;
        }
    }


    public function loadThemeScripts()
    {
        $scripts = $this->currentTheme['scripts'] ?? [];
        foreach ($scripts as $script) {
            $url = $this->getThemeAssetUrl($script);
            echo '<script src="' . htmlspecialchars($url) . '"></script>' . PHP_EOL;
        }
    }

    public function getActiveTheme()
    {
        return $this->activeTheme;
    }

    public function getThemeConfig()
    {
        return $this->currentTheme;
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
                'settings' => $activeThemeData['settings'] ?? [],
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
     * Update theme settings (settings_json) only
     */
    public function updateThemeSettings(int $id, array $settings): array
    {
        try {
            $ok = $this->themeModel->updateSettings($id, $settings);
            if ($ok) {
                // If the updated theme is active, reload
                $active = $this->themeModel->getActive();
                if ($active && (int)$active['id'] === (int)$id) {
                    $this->loadActiveTheme();
                }
                return ['success' => true, 'message' => 'Theme settings updated'];
            }
            return ['success' => false, 'message' => 'Failed to update settings'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    public function themeUrl($path = '')
    {
        $path = ltrim($path, '/');
        $extraQuery = '';

        if (strpos($path, '?') !== false) {
            [$pathOnly, $query] = explode('?', $path, 2);
            $path = $pathOnly;
            $extraQuery = $query ? '&' . $query : '';
        }

        $params = http_build_query([
            'path' => $this->activeTheme . '/' . $path,
        ]);

        // Default assumption: the public/ directory is the web root
        $proxy = '/theme-assets.php';

        // If the application's public directory is NOT the web root (for example,
        // when running under Laragon at http://localhost/Bishwo_Calculator where
        // DOCUMENT_ROOT is c:\laragon\www), then theme-assets.php actually lives
        // under /public and must be referenced with that prefix.
        $publicDir = BASE_PATH . '/public';
        $themesProxyInPublic = $publicDir . $proxy;
        $docRoot = isset($_SERVER['DOCUMENT_ROOT']) ? realpath($_SERVER['DOCUMENT_ROOT']) : null;
        $publicReal = realpath($publicDir);

        if ($publicReal && $docRoot && $docRoot !== $publicReal) {
            if (file_exists($themesProxyInPublic)) {
                $proxy = '/public' . $proxy;
            }
        }

        return $this->baseUrl . $proxy . '?' . $params . $extraQuery;
    }

    public function assetsUrl($path = '')
    {
        return $this->baseUrl . '/themes/' . $this->activeTheme . '/' . ltrim($path, '/');
    }


    /**
     * Render a partial template
     */
    public function renderPartial($template, $data = [])
    {
        // Remove .php extension if present
        $template = str_replace('.php', '', $template);
        
        $templateFile = $this->themesPath . $this->activeTheme . '/views/' . $template . '.php';
        
        if (file_exists($templateFile)) {
            extract($data);
            // Make $viewHelper available in partials
            $viewHelper = new View();
            include $templateFile;
        } else {
            // Fallback to default theme
            $defaultFile = BASE_PATH . '/themes/default/views/' . $template . '.php';
            if (file_exists($defaultFile)) {
                extract($data);
                $viewHelper = new View();
                include $defaultFile;
            } else {
                // Log missing template
                error_log("Theme template not found: $template in theme: " . $this->activeTheme . " (looked in: $templateFile)");
            }
        }
    }

    /**
     * Render a view template
     */
    public function renderView($template, $data = [])
    {
        $templateFile = $this->themesPath . $this->activeTheme . '/views/' . $template . '.php';
        
        if (file_exists($templateFile)) {
            extract($data);
            include $templateFile;
        } else {
            // Fallback to default theme
            $defaultFile = BASE_PATH . '/themes/default/views/' . $template . '.php';
            if (file_exists($defaultFile)) {
                extract($data);
                include $defaultFile;
            } else {
                // Log missing template
                error_log("Theme template not found: $template in theme: " . $this->activeTheme);
            }
        }
    }

    /**
     * Load category-specific styles
     */
    public function loadCategoryStyle($category)
    {
        $categoryStyles = $this->currentTheme['category_styles'] ?? [];
        if (isset($categoryStyles[$category])) {
            $url = $this->getThemeAssetUrl($categoryStyles[$category]);
            echo '<link rel="stylesheet" href="' . htmlspecialchars($url) . '">' . PHP_EOL;
        }
    }

    /**
     * Set theme (for switching themes)
     */
    public function setTheme($themeName)
    {
        $theme = $this->themeModel->getByName($themeName);
        
        if ($theme && $theme['status'] === 'active') {
            $this->activeTheme = $themeName;
            $this->currentTheme = $theme['config'] ?? [];
            return true;
        }
        
        return false;
    }

    /**
     * Load theme.json configuration file
     * 
     * @param string $themeName
     * @return array
     */
    public function loadThemeConfig($themeName)
    {
        $configPath = $this->themesPath . $themeName . '/theme.json';
        
        if (file_exists($configPath)) {
            $config = json_decode(file_get_contents($configPath), true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $config;
            }
        }
        
        // Return default config if theme.json doesn't exist
        return $this->getDefaultThemeConfig();
    }

    /**
     * Get theme assets (CSS and JS files)
     * 
     * @param string $themeName
     * @return array
     */
    public function getThemeAssets($themeName)
    {
        $config = $this->loadThemeConfig($themeName);
        
        return [
            'styles' => $config['styles'] ?? [],
            'scripts' => $config['scripts'] ?? [],
            'category_styles' => $config['category_styles'] ?? [],
            'colors' => $config['config']['colors'] ?? [],
            'typography' => $config['config']['typography'] ?? [],
            'features' => $config['config']['features'] ?? []
        ];
    }

    /**
     * Get active theme assets
     * 
     * @return array
     */
    public function getActiveThemeAssets()
    {
        return $this->getThemeAssets($this->activeTheme);
    }

    /**
     * Get theme asset URL with cache busting
     * Uses file modification time for cache busting
     * 
     * @param string $assetPath
     * @return string
     */
    public function getThemeAssetUrl($assetPath)
    {
        $fullPath = $this->themesPath . $this->activeTheme . '/' . ltrim($assetPath, '/');
        
        if (file_exists($fullPath)) {
            $mtime = filemtime($fullPath);
            return $this->themeUrl($assetPath . '?v=' . $mtime);
        }
        
        return $this->themeUrl($assetPath);
    }

    /**
     * Get all CSS files for active theme
     * 
     * @return array
     */
    public function getThemeStyles()
    {
        $assets = $this->getActiveThemeAssets();
        $styles = [];
        
        foreach ($assets['styles'] as $style) {
            $styles[] = $this->getThemeAssetUrl($style);
        }
        
        return $styles;
    }

    /**
     * Get all JS files for active theme
     * 
     * @return array
     */
    public function getThemeScripts()
    {
        $assets = $this->getActiveThemeAssets();
        $scripts = [];
        
        foreach ($assets['scripts'] as $script) {
            $scripts[] = $this->getThemeAssetUrl($script);
        }
        
        return $scripts;
    }

    /**
     * Get category-specific CSS file
     * 
     * @param string $category
     * @return string|null
     */
    public function getCategoryStyleUrl($category)
    {
        $assets = $this->getActiveThemeAssets();
        
        if (isset($assets['category_styles'][$category])) {
            return $this->getThemeAssetUrl($assets['category_styles'][$category]);
        }
        
        return null;
    }
}
?>
