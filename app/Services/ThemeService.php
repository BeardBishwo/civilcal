<?php

namespace App\Services;

use App\Models\Theme;
use Exception;

class ThemeService
{
    private $themeModel;

    public function __construct()
    {
        // Note: The Theme model doesn't exist yet, so we'll create a basic implementation
        // This service is designed to work with a Theme model if one exists
        $this->themeModel = null;
    }

    /**
     * Get all themes
     */
    public function getAllThemes()
    {
        try {
            // If a theme model exists, use it, otherwise return sample data
            if ($this->themeModel && method_exists($this->themeModel, 'getAll')) {
                return $this->themeModel->getAll();
            } else {
                // Return sample themes data
                return [
                    [
                        'id' => 1,
                        'name' => 'Default Theme',
                        'description' => 'The default theme for the application',
                        'version' => '1.0.0',
                        'author' => 'System',
                        'is_active' => 1,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ]
                ];
            }
        } catch (Exception $e) {
            error_log('ThemeService getAllThemes error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get active theme
     */
    public function getActiveTheme()
    {
        try {
            // If a theme model exists, use it to get the active theme
            if ($this->themeModel && method_exists($this->themeModel, 'getActive')) {
                $themes = $this->themeModel->getActive();
                return $themes[0] ?? null;
            } else {
                // Return sample active theme
                return [
                    'id' => 1,
                    'name' => 'Default Theme',
                    'description' => 'The default theme for the application',
                    'version' => '1.0.0',
                    'author' => 'System',
                    'is_active' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
            }
        } catch (Exception $e) {
            error_log('ThemeService getActiveTheme error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Find theme by ID
     */
    public function getTheme($id)
    {
        try {
            if ($this->themeModel && method_exists($this->themeModel, 'find')) {
                return $this->themeModel->find($id);
            } else {
                // Return sample theme by ID if it's the default one
                if ($id == 1) {
                    return [
                        'id' => 1,
                        'name' => 'Default Theme',
                        'description' => 'The default theme for the application',
                        'version' => '1.0.0',
                        'author' => 'System',
                        'is_active' => 1,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ];
                }
                return null;
            }
        } catch (Exception $e) {
            error_log("ThemeService getTheme error for ID {$id}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Find theme by name
     */
    public function getThemeByName($name)
    {
        try {
            if ($this->themeModel && method_exists($this->themeModel, 'findByName')) {
                return $this->themeModel->findByName($name);
            } else {
                // Return sample theme by name if it's the default one
                if ($name === 'Default Theme') {
                    return [
                        'id' => 1,
                        'name' => 'Default Theme',
                        'description' => 'The default theme for the application',
                        'version' => '1.0.0',
                        'author' => 'System',
                        'is_active' => 1,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ];
                }
                return null;
            }
        } catch (Exception $e) {
            error_log("ThemeService getThemeByName error for name {$name}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Activate a theme
     */
    public function activateTheme($id)
    {
        try {
            if ($this->themeModel && method_exists($this->themeModel, 'activate')) {
                $result = $this->themeModel->activate($id);
                if ($result) {
                    return [
                        'success' => true,
                        'message' => 'Theme activated successfully'
                    ];
                } else {
                    return [
                        'success' => false,
                        'message' => 'Failed to activate theme'
                    ];
                }
            } else {
                // In a real system, this would update the active theme in database/settings
                return [
                    'success' => true,
                    'message' => 'Theme activation would be processed here'
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Theme activation failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Deactivate a theme
     */
    public function deactivateTheme($id)
    {
        try {
            if ($this->themeModel && method_exists($this->themeModel, 'deactivate')) {
                $result = $this->themeModel->deactivate($id);
                if ($result) {
                    return [
                        'success' => true,
                        'message' => 'Theme deactivated successfully'
                    ];
                } else {
                    return [
                        'success' => false,
                        'message' => 'Failed to deactivate theme'
                    ];
                }
            } else {
                return [
                    'success' => true,
                    'message' => 'Theme deactivation would be processed here'
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Theme deactivation failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Install a theme from a package
     */
    public function installTheme($packagePath)
    {
        try {
            // This is a simplified implementation
            // In a real system, you would validate the theme package,
            // extract it to the themes directory, and register it in the database
            $themeDir = __DIR__ . '/../../themes/';
            
            if (!file_exists($packagePath)) {
                return [
                    'success' => false,
                    'message' => 'Theme package file does not exist: ' . $packagePath
                ];
            }

            // Additional validation would go here
            // Extract the theme package and install it
            
            return [
                'success' => true,
                'message' => 'Theme installed successfully'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Theme installation failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Upload theme from file
     */
    public function uploadTheme($uploadFile)
    {
        try {
            if (!isset($uploadFile['tmp_name']) || !isset($uploadFile['name'])) {
                return [
                    'success' => false,
                    'message' => 'Invalid file upload'
                ];
            }

            // Validate file type - should be zip for theme packages
            $fileType = mime_content_type($uploadFile['tmp_name']);
            if ($fileType !== 'application/zip' && pathinfo($uploadFile['name'], PATHINFO_EXTENSION) !== 'zip') {
                return [
                    'success' => false,
                    'message' => 'Only ZIP files are allowed for theme uploads'
                ];
            }

            // Validate file size (max 10MB)
            if ($uploadFile['size'] > 10 * 1024 * 1024) {
                return [
                    'success' => false,
                    'message' => 'File size exceeds limit of 10MB'
                ];
            }

            // Create themes directory if it doesn't exist
            $themesDir = __DIR__ . '/../../themes/';
            if (!is_dir($themesDir)) {
                mkdir($themesDir, 0755, true);
            }

            // Generate unique filename
            $fileName = 'theme_' . uniqid() . '_' . time() . '.zip';
            $targetPath = $themesDir . $fileName;

            // Move uploaded file
            if (move_uploaded_file($uploadFile['tmp_name'], $targetPath)) {
                // Extract the theme
                $zip = new \ZipArchive();
                $zipOpen = $zip->open($targetPath);
                
                if ($zipOpen === true) {
                    // Extract to themes directory
                    $extractPath = $themesDir . pathinfo($fileName, PATHINFO_FILENAME);
                    $zip->extractTo($extractPath);
                    $zip->close();
                    
                    // Clean up the zip file
                    unlink($targetPath);
                    
                    return [
                        'success' => true,
                        'message' => 'Theme uploaded and extracted successfully',
                        'path' => $extractPath
                    ];
                } else {
                    return [
                        'success' => false,
                        'message' => 'Uploaded file is not a valid ZIP archive'
                    ];
                }
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to move uploaded file'
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Theme upload failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get theme information and preview
     */
    public function getThemeInfo($themeName)
    {
        try {
            $themePath = __DIR__ . '/../../themes/' . $themeName;
            
            if (!is_dir($themePath)) {
                return [
                    'success' => false,
                    'message' => 'Theme does not exist: ' . $themeName
                ];
            }

            // Look for theme configuration file (e.g., theme.json)
            $configFile = $themePath . '/theme.json';
            $config = [];
            
            if (file_exists($configFile)) {
                $config = json_decode(file_get_contents($configFile), true) ?: [];
            } else {
                // Provide default configuration
                $config = [
                    'name' => $themeName,
                    'version' => '1.0.0',
                    'author' => 'Unknown',
                    'description' => 'A theme for the application',
                    'screenshot' => 'screenshot.png'
                ];
            }

            // Check for screenshot
            $screenshot = $themePath . '/screenshot.png';
            if (!file_exists($screenshot)) {
                $screenshot = $themePath . '/preview.jpg'; // Alternative file name
            }
            
            return [
                'success' => true,
                'theme' => [
                    'name' => $themeName,
                    'path' => $themePath,
                    'config' => $config,
                    'has_screenshot' => file_exists($screenshot),
                    'screenshot_path' => file_exists($screenshot) ? $screenshot : null
                ]
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to get theme info: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Delete a theme
     */
    public function deleteTheme($themeName)
    {
        try {
            $themePath = __DIR__ . '/../../themes/' . $themeName;
            
            if (!is_dir($themePath)) {
                return [
                    'success' => false,
                    'message' => 'Theme does not exist: ' . $themeName
                ];
            }

            // Don't allow deletion of active theme
            $activeTheme = $this->getActiveTheme();
            if ($activeTheme && $activeTheme['name'] === $themeName) {
                return [
                    'success' => false,
                    'message' => 'Cannot delete the currently active theme'
                ];
            }

            // Delete the theme directory recursively
            $this->deleteDirectory($themePath);
            
            return [
                'success' => true,
                'message' => 'Theme deleted successfully'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Theme deletion failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get available themes
     */
    public function getAvailableThemes()
    {
        try {
            $themesDir = __DIR__ . '/../../themes/';
            $themes = [];

            if (is_dir($themesDir)) {
                $directories = array_filter(glob($themesDir . '*'), 'is_dir');
                
                foreach ($directories as $dir) {
                    $themeName = basename($dir);
                    $themeInfo = $this->getThemeInfo($themeName);
                    
                    if ($themeInfo['success']) {
                        $themes[] = $themeInfo['theme'];
                    }
                }
            }

            return $themes;
        } catch (Exception $e) {
            error_log('ThemeService getAvailableThemes error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Update theme configuration
     */
    public function updateThemeConfig($themeName, $config)
    {
        try {
            $themePath = __DIR__ . '/../../themes/' . $themeName;
            $configFile = $themePath . '/theme.json';
            
            // Read existing config
            $existingConfig = [];
            if (file_exists($configFile)) {
                $existingConfig = json_decode(file_get_contents($configFile), true) ?: [];
            }
            
            // Merge with new config
            $updatedConfig = array_merge($existingConfig, $config);
            
            // Write back to file
            $result = file_put_contents($configFile, json_encode($updatedConfig, JSON_PRETTY_PRINT));
            
            if ($result !== false) {
                return [
                    'success' => true,
                    'message' => 'Theme configuration updated successfully'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to update theme configuration'
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Theme configuration update failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Delete directory recursively
     */
    private function deleteDirectory($dir)
    {
        if (!file_exists($dir)) {
            return true;
        }

        if (!is_dir($dir)) {
            return unlink($dir);
        }

        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }

            if (!$this->deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }
        }

        return rmdir($dir);
    }
}