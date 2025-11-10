<?php

namespace App\Controllers\Admin;

use App\Services\PremiumThemeManager;
use Exception;

/**
 * Premium Theme Controller
 * 
 * Handles admin interface for premium theme management
 * 
 * @package App\Controllers\Admin
 * @version 1.0.0
 */
class PremiumThemeController
{
    private $themeManager;
    private $database;
    
    public function __construct()
    {
        $this->themeManager = new PremiumThemeManager($this->getDatabaseConnection());
    }
    
    /**
     * Display premium themes dashboard
     * 
     * @return string
     */
    public function index()
    {
        try {
            $themes = $this->themeManager->getAvailableThemes();
            $activeTheme = $this->getActiveTheme();
            
            $data = [
                'title' => 'Premium Themes',
                'themes' => $themes['themes'] ?? [],
                'activeTheme' => $activeTheme,
                'userHasAccess' => $this->userHasPremiumAccess()
            ];
            
            return $this->renderView('admin/premium-themes/index', $data);
            
        } catch (Exception $e) {
            return $this->renderError('Failed to load premium themes: ' . $e->getMessage());
        }
    }
    
    /**
     * Show theme customization interface
     * 
     * @param string $themeName
     * @return string
     */
    public function customize($themeName)
    {
        try {
            // Get theme settings
            $settings = $this->themeManager->getThemeSettings($themeName);
            if (!$settings['success']) {
                return $this->renderError($settings['message']);
            }
            
            // Get customization options
            $options = $this->themeManager->getCustomizationOptions($themeName);
            if (!$options['success']) {
                return $this->renderError($options['message']);
            }
            
            $data = [
                'title' => 'Customize Theme: ' . ucfirst($themeName),
                'themeName' => $themeName,
                'settings' => $settings['settings'],
                'options' => $options['options'],
                'hasFeature' => function($feature) {
                    return $this->themeManager->hasFeature($feature);
                }
            ];
            
            return $this->renderView('admin/premium-themes/customize', $data);
            
        } catch (Exception $e) {
            return $this->renderError('Failed to load customization: ' . $e->getMessage());
        }
    }
    
    /**
     * Handle theme settings update
     * 
     * @param string $themeName
     * @return string
     */
    public function updateSettings($themeName)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->redirect('/admin/premium-themes/customize/' . $themeName);
        }
        
        try {
            $settings = $_POST['settings'] ?? [];
            $userId = $_SESSION['user_id'] ?? 'default';
            
            $result = $this->themeManager->updateThemeSettings($themeName, $settings, $userId);
            
            if ($result['success']) {
                $this->logThemeEvent($themeName, 'customization', 'Settings updated', $userId);
                $this->addFlashMessage('success', 'Theme settings updated successfully');
            } else {
                $this->addFlashMessage('error', $result['message']);
            }
            
            return $this->redirect('/admin/premium-themes/customize/' . $themeName);
            
        } catch (Exception $e) {
            $this->addFlashMessage('error', 'Failed to update settings: ' . $e->getMessage());
            return $this->redirect('/admin/premium-themes/customize/' . $themeName);
        }
    }
    
    /**
     * Handle theme activation
     * 
     * @param string $themeName
     * @return string
     */
    public function activate($themeName)
    {
        try {
            $userId = $_SESSION['user_id'] ?? 'default';
            
            $result = $this->themeManager->activateTheme($themeName, $userId);
            
            if ($result['success']) {
                $this->logThemeEvent($themeName, 'activation', 'Theme activated', $userId);
                $this->addFlashMessage('success', 'Theme activated successfully');
            } else {
                $this->addFlashMessage('error', $result['message']);
            }
            
            return $this->redirect('/admin/premium-themes');
            
        } catch (Exception $e) {
            $this->addFlashMessage('error', 'Failed to activate theme: ' . $e->getMessage());
            return $this->redirect('/admin/premium-themes');
        }
    }
    
    /**
     * Show license management interface
     * 
     * @return string
     */
    public function licenses()
    {
        try {
            $licenses = $this->getAllLicenses();
            
            $data = [
                'title' => 'Theme Licenses',
                'licenses' => $licenses,
                'hasFeature' => function($feature) {
                    return $this->themeManager->hasFeature($feature);
                }
            ];
            
            return $this->renderView('admin/premium-themes/licenses', $data);
            
        } catch (Exception $e) {
            return $this->renderError('Failed to load licenses: ' . $e->getMessage());
        }
    }
    
    /**
     * Handle license validation
     * 
     * @return string
     */
    public function validateLicense()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->redirect('/admin/premium-themes/licenses');
        }
        
        try {
            $licenseKey = $_POST['license_key'] ?? '';
            $domain = $_SERVER['HTTP_HOST'] ?? '';
            $userId = $_SESSION['user_id'] ?? 'default';
            
            if (empty($licenseKey)) {
                $this->addFlashMessage('error', 'License key is required');
                return $this->redirect('/admin/premium-themes/licenses');
            }
            
            $result = $this->themeManager->validateLicense($licenseKey, $domain, $userId);
            
            if ($result['valid']) {
                $this->addFlashMessage('success', 'License validated successfully');
                $this->logThemeEvent('system', 'license_validation', 'License validated', $userId);
            } else {
                $this->addFlashMessage('error', $result['message']);
            }
            
            return $this->redirect('/admin/premium-themes/licenses');
            
        } catch (Exception $e) {
            $this->addFlashMessage('error', 'License validation failed: ' . $e->getMessage());
            return $this->redirect('/admin/premium-themes/licenses');
        }
    }
    
    /**
     * Show theme installation interface
     * 
     * @return string
     */
    public function install()
    {
        $data = [
            'title' => 'Install Theme',
            'hasFeature' => function($feature) {
                return $this->themeManager->hasFeature($feature);
            }
        ];
        
        return $this->renderView('admin/premium-themes/install', $data);
    }
    
    /**
     * Handle theme installation from ZIP
     * 
     * @return string
     */
    public function installFromZip()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->redirect('/admin/premium-themes/install');
        }
        
        try {
            $licenseKey = $_POST['license_key'] ?? '';
            $userId = $_SESSION['user_id'] ?? 'default';
            
            if (empty($licenseKey)) {
                $this->addFlashMessage('error', 'License key is required');
                return $this->redirect('/admin/premium-themes/install');
            }
            
            if (!isset($_FILES['theme_zip']) || $_FILES['theme_zip']['error'] !== UPLOAD_ERR_OK) {
                $this->addFlashMessage('error', 'Please select a valid theme ZIP file');
                return $this->redirect('/admin/premium-themes/install');
            }
            
            $zipPath = $_FILES['theme_zip']['tmp_name'];
            
            $result = $this->themeManager->installThemeFromZip($zipPath, $licenseKey, $userId);
            
            if ($result['success']) {
                $this->addFlashMessage('success', 'Theme installed successfully');
                $this->logThemeEvent($result['theme']['name'] ?? 'unknown', 'installation', 'Theme installed from ZIP', $userId);
            } else {
                $this->addFlashMessage('error', $result['message']);
            }
            
            return $this->redirect('/admin/premium-themes');
            
        } catch (Exception $e) {
            $this->addFlashMessage('error', 'Installation failed: ' . $e->getMessage());
            return $this->redirect('/admin/premium-themes/install');
        }
    }
    
    
    /**
     * API endpoint for theme settings
     * 
     * @return string
     */
    public function apiSettings()
    {
        header('Content-Type: application/json');
        
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $input = json_decode(file_get_contents('php://input'), true);
                
                $themeName = $input['theme'] ?? 'default';
                $settings = $input['settings'] ?? [];
                $userId = $_SESSION['user_id'] ?? 'default';
                
                $result = $this->themeManager->updateThemeSettings($themeName, $settings, $userId);
                
                echo json_encode($result);
            } else {
                $themeName = $_GET['theme'] ?? 'default';
                $userId = $_SESSION['user_id'] ?? 'default';
                
                $result = $this->themeManager->getThemeSettings($themeName, $userId);
                echo json_encode($result);
            }
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Get database connection
     * 
     * @return \PDO|null
     */
    private function getDatabaseConnection()
    {
        try {
            $configPath = dirname(__DIR__, 2) . '/config/database.php';
            if (file_exists($configPath)) {
                require_once $configPath;
                if (function_exists('getDatabaseConnection')) {
                    return getDatabaseConnection();
                }
            }
            return null;
        } catch (Exception $e) {
            return null;
        }
    }
    
    /**
     * Get active theme
     * 
     * @return string
     */
    private function getActiveTheme()
    {
        $userId = $_SESSION['user_id'] ?? 'default';
        
        // Get active theme from database or return default
        $db = $this->getDatabaseConnection();
        if (!$db) {
            return 'default';
        }
        
        $stmt = $db->prepare("SELECT setting_value FROM user_theme_settings WHERE user_id = ? AND setting_key = 'active_theme' ORDER BY updated_at DESC LIMIT 1");
        $stmt->execute([$userId]);
        
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result['setting_value'] ?? 'default';
    }
    
    /**
     * Check if user has premium access
     * 
     * @return bool
     */
    private function userHasPremiumAccess()
    {
        return $this->themeManager->hasFeature('premium_themes');
    }
    
    /**
     * Get all licenses
     * 
     * @return array
     */
    private function getAllLicenses()
    {
        $db = $this->getDatabaseConnection();
        if (!$db) {
            return [];
        }
        
        $stmt = $db->prepare("SELECT * FROM theme_licenses ORDER BY created_at DESC");
        $stmt->execute();
        
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    /**
     * Get theme analytics
     * 
     * @return array
     */
    private function getThemeAnalytics()
    {
        $db = $this->getDatabaseConnection();
        if (!$db) {
            return [];
        }
        
        $stmt = $db->prepare("
            SELECT 
                theme_name,
                event_type,
                COUNT(*) as count,
                DATE(created_at) as date
            FROM theme_analytics 
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            GROUP BY theme_name, event_type, DATE(created_at)
            ORDER BY date DESC, count DESC
        ");
        $stmt->execute();
        
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    /**
     * Log theme event
     * 
     * @param string $themeName
     * @param string $eventType
     * @param string $message
     * @param string $userId
     */
    private function logThemeEvent($themeName, $eventType, $message, $userId)
    {
        try {
            $db = $this->getDatabaseConnection();
            if (!$db) {
                return;
            }
            
            $stmt = $db->prepare("
                INSERT INTO theme_analytics (
                    theme_name, 
                    user_id, 
                    event_type, 
                    event_data, 
                    ip_address, 
                    user_agent, 
                    domain
                ) VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $themeName,
                $userId,
                $eventType,
                json_encode(['message' => $message]),
                $_SERVER['REMOTE_ADDR'] ?? '',
                $_SERVER['HTTP_USER_AGENT'] ?? '',
                $_SERVER['HTTP_HOST'] ?? ''
            ]);
            
        } catch (Exception $e) {
            error_log('Failed to log theme event: ' . $e->getMessage());
        }
    }
    
    /**
     * Add flash message
     * 
     * @param string $type
     * @param string $message
     */
    private function addFlashMessage($type, $message)
    {
        $_SESSION['flash_messages'][] = [
            'type' => $type,
            'message' => $message
        ];
    }
    
    /**
     * Render view
     * 
     * @param string $view
     * @param array $data
     * @return string
     */
    private function renderView($view, $data)
    {
        extract($data);
        
        ob_start();
        include __DIR__ . '/../../../themes/premium/views/admin/' . $view . '.php';
        return ob_get_clean();
    }
    
    /**
     * Render error
     * 
     * @param string $message
     * @return string
     */
    private function renderError($message)
    {
        return '<div class="error-message">Error: ' . htmlspecialchars($message) . '</div>';
    }
    
    /**
     * Redirect
     * 
     * @param string $url
     * @return string
     */
    private function redirect($url)
    {
        header('Location: ' . $url);
        exit;
    }
    
    // Missing controller methods for routes
    
    
    /**
     * Show theme details
     * 
     * @param int $id
     * @return string
     */
    public function show($id)
    {
        try {
            $theme = $this->getThemeById($id);
            if (!$theme) {
                return $this->renderError('Theme not found');
            }
            
            $data = [
                'title' => 'Theme Details: ' . $theme['name'],
                'theme' => $theme,
                'hasFeature' => function($feature) {
                    return $this->themeManager->hasFeature($feature);
                }
            ];
            
            return $this->renderView('admin/premium-themes/show', $data);
            
        } catch (Exception $e) {
            return $this->renderError('Failed to load theme: ' . $e->getMessage());
        }
    }
    
    /**
     * Show edit theme form
     * 
     * @param int $id
     * @return string
     */
    public function edit($id)
    {
        try {
            $theme = $this->getThemeById($id);
            if (!$theme) {
                return $this->renderError('Theme not found');
            }
            
            $data = [
                'title' => 'Edit Theme: ' . $theme['name'],
                'theme' => $theme,
                'hasFeature' => function($feature) {
                    return $this->themeManager->hasFeature($feature);
                }
            ];
            
            return $this->renderView('admin/premium-themes/edit', $data);
            
        } catch (Exception $e) {
            return $this->renderError('Failed to load theme: ' . $e->getMessage());
        }
    }
    
    
    
    
    /**
     * Show theme settings
     * 
     * @param int $id
     * @return string
     */
    public function settings($id)
    {
        try {
            $theme = $this->getThemeById($id);
            if (!$theme) {
                return $this->renderError('Theme not found');
            }
            
            $settings = $this->themeManager->getThemeSettings($theme['name']);
            
            $data = [
                'title' => 'Theme Settings: ' . $theme['name'],
                'theme' => $theme,
                'settings' => $settings['settings'] ?? [],
                'hasFeature' => function($feature) {
                    return $this->themeManager->hasFeature($feature);
                }
            ];
            
            return $this->renderView('admin/premium-themes/settings', $data);
            
        } catch (Exception $e) {
            return $this->renderError('Failed to load settings: ' . $e->getMessage());
        }
    }
    
    /**
     * Show theme analytics for specific theme
     * 
     * @param int $id
     * @return string
     */
    public function showAnalytics($id)
    {
        try {
            $theme = $this->getThemeById($id);
            if (!$theme) {
                return $this->renderError('Theme not found');
            }
            
            $analytics = $this->getThemeAnalyticsById($id);
            
            $data = [
                'title' => 'Theme Analytics: ' . $theme['name'],
                'theme' => $theme,
                'analytics' => $analytics,
                'hasFeature' => function($feature) {
                    return $this->themeManager->hasFeature($feature);
                }
            ];
            
            return $this->renderView('admin/premium-themes/analytics', $data);
            
        } catch (Exception $e) {
            return $this->renderError('Failed to load analytics: ' . $e->getMessage());
        }
    }
    
    
    /**
     * Preview theme
     * 
     * @param int $id
     * @return string
     */
    public function preview($id)
    {
        try {
            $theme = $this->getThemeById($id);
            if (!$theme) {
                return $this->renderError('Theme not found');
            }
            
            $data = [
                'title' => 'Preview: ' . $theme['name'],
                'theme' => $theme,
                'preview' => true,
                'hasFeature' => function($feature) {
                    return $this->themeManager->hasFeature($feature);
                }
            ];
            
            return $this->renderView('admin/premium-themes/preview', $data);
            
        } catch (Exception $e) {
            return $this->renderError('Failed to load preview: ' . $e->getMessage());
        }
    }
    
    /**
     * Install theme from ZIP
     * 
     * @return string
     */
    public function installTheme()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->redirect('/admin/premium-themes');
        }
        
        try {
            $licenseKey = $_POST['license_key'] ?? '';
            $userId = $_SESSION['user_id'] ?? 'default';
            
            if (empty($licenseKey)) {
                $this->addFlashMessage('error', 'License key is required');
                return $this->redirect('/admin/premium-themes');
            }
            
            if (!isset($_FILES['theme_zip']) || $_FILES['theme_zip']['error'] !== UPLOAD_ERR_OK) {
                $this->addFlashMessage('error', 'Please select a valid theme ZIP file');
                return $this->redirect('/admin/premium-themes');
            }
            
            $zipPath = $_FILES['theme_zip']['tmp_name'];
            
            $result = $this->themeManager->installThemeFromZip($zipPath, $licenseKey, $userId);
            
            if ($result['success']) {
                $this->addFlashMessage('success', 'Theme installed successfully');
                $this->logThemeEvent($result['theme']['name'] ?? 'unknown', 'installation', 'Theme installed from ZIP', $userId);
            } else {
                $this->addFlashMessage('error', $result['message']);
            }
            
            return $this->redirect('/admin/premium-themes');
            
        } catch (Exception $e) {
            $this->addFlashMessage('error', 'Installation failed: ' . $e->getMessage());
            return $this->redirect('/admin/premium-themes');
        }
    }
    
    /**
     * Upload ZIP file
     * 
     * @return string
     */
    public function uploadZip()
    {
        return $this->installTheme();
    }
    
    /**
     * Show marketplace
     * 
     * @return string
     */
    public function marketplace()
    {
        try {
            $marketplaceThemes = $this->getMarketplaceThemes();
            
            $data = [
                'title' => 'Theme Marketplace',
                'themes' => $marketplaceThemes,
                'hasFeature' => function($feature) {
                    return $this->themeManager->hasFeature($feature);
                }
            ];
            
            return $this->renderView('admin/premium-themes/marketplace', $data);
            
        } catch (Exception $e) {
            return $this->renderError('Failed to load marketplace: ' . $e->getMessage());
        }
    }
    
    
    // Helper methods
    
    /**
     * Get theme by ID
     * 
     * @param int $id
     * @return array|null
     */
    private function getThemeById($id)
    {
        $db = $this->getDatabaseConnection();
        if (!$db) {
            return null;
        }
        
        $stmt = $db->prepare("SELECT * FROM premium_themes WHERE id = ?");
        $stmt->execute([$id]);
        
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }
    
    /**
     * Get theme analytics by ID
     * 
     * @param int $themeId
     * @return array
     */
    private function getThemeAnalyticsById($themeId)
    {
        $db = $this->getDatabaseConnection();
        if (!$db) {
            return [];
        }
        
        $stmt = $db->prepare("
            SELECT 
                event_type,
                COUNT(*) as count,
                DATE(created_at) as date
            FROM theme_analytics 
            WHERE theme_id = ? AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            GROUP BY event_type, DATE(created_at)
            ORDER BY date DESC, count DESC
        ");
        $stmt->execute([$themeId]);
        
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    /**
     * Get marketplace themes
     * 
     * @return array
     */
    private function getMarketplaceThemes()
    {
        // This would typically connect to a marketplace API
        // For now, return sample data
        return [
            [
                'id' => 1,
                'name' => 'Professional Dark',
                'description' => 'A sleek dark theme for professional use',
                'price' => 29.99,
                'rating' => 4.8,
                'preview_image' => '/assets/themes/professional-dark-preview.jpg'
            ],
            [
                'id' => 2,
                'name' => 'Modern Light',
                'description' => 'Clean and modern light theme',
                'price' => 19.99,
                'rating' => 4.6,
                'preview_image' => '/assets/themes/modern-light-preview.jpg'
            ]
        ];
    }
}
