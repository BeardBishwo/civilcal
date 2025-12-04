<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Services\PremiumThemeManager;
use App\Services\FileUploadService;
use Exception;

/**
 * Premium Theme Controller
 * 
 * Handles admin interface for premium theme management
 * 
 * @package App\Controllers\Admin
 * @version 1.0.0
 */
class PremiumThemeController extends Controller
{
    private $themeManager;
    
    public function __construct()
    {
        parent::__construct();
        $this->themeManager = new PremiumThemeManager($this->db);
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
            
            $this->view->render('admin/premium-themes/index', [
                'title' => 'Premium Themes',
                'themes' => $themes['themes'] ?? [],
                'activeTheme' => $activeTheme,
                'userHasAccess' => $this->userHasPremiumAccess()
            ]);
            
        } catch (Exception $e) {
            $this->view->render('admin/error', [
                'title' => 'Error',
                'message' => 'Failed to load premium themes: ' . $e->getMessage()
            ]);
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
                $this->view->render('admin/error', [
                    'title' => 'Error',
                    'message' => $settings['message']
                ]);
                return;
            }
            
            // Get customization options
            $options = $this->themeManager->getCustomizationOptions($themeName);
            if (!$options['success']) {
                $this->view->render('admin/error', [
                    'title' => 'Error',
                    'message' => $options['message']
                ]);
                return;
            }
            
            $this->view->render('admin/premium-themes/customize', [
                'title' => 'Customize Theme: ' . ucfirst($themeName),
                'themeName' => $themeName,
                'settings' => $settings['settings'] ?? [],
                'options' => $options['options'] ?? []
            ]);
            
        } catch (Exception $e) {
            $this->view->render('admin/error', [
                'title' => 'Error',
                'message' => 'Failed to load customization: ' . $e->getMessage()
            ]);
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
            
            $this->view->render('admin/premium-themes/licenses', [
                'title' => 'Theme Licenses',
                'licenses' => $licenses
            ]);
            
        } catch (Exception $e) {
            $this->view->render('admin/error', [
                'title' => 'Error',
                'message' => 'Failed to load licenses: ' . $e->getMessage()
            ]);
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
        $this->view->render('admin/premium-themes/install', [
            'title' => 'Install Theme'
        ]);
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
            
            // Validate and stage upload via FileUploadService
            $uploader = new FileUploadService();
            $dest = (defined('STORAGE_PATH') ? STORAGE_PATH : sys_get_temp_dir()) . '/uploads/premium-themes';
            $upload = $uploader->uploadTheme($_FILES['theme_zip'], $dest);
            if (!($upload['success'] ?? false)) {
                $this->addFlashMessage('error', $upload['message'] ?? 'Upload failed');
                return $this->redirect('/admin/premium-themes/install');
            }
            $zipPath = $upload['file_path'];
            
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
     * Render view using the View system
     * 
     * @param string $view
     * @param array $data
     * @return string
     */
    private function renderView($view, $data)
    {
        // Use the View system to render the view
        $viewRenderer = new \App\Core\View();
        $viewRenderer->render($view, $data);
        return '';
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
    
    /**
     * Store new theme
     */
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/premium-themes/create');
        }
        
        try {
            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';
            $userId = $_SESSION['user_id'] ?? 'default';
            
            if (empty($name)) {
                $this->addFlashMessage('error', 'Theme name is required');
                $this->redirect('/admin/premium-themes/create');
            }
            
            $stmt = $this->db->prepare("
                INSERT INTO premium_themes (name, description, created_by, created_at)
                VALUES (?, ?, ?, NOW())
            ");
            $result = $stmt->execute([$name, $description, $userId]);
            
            if ($result) {
                $this->addFlashMessage('success', 'Theme created successfully');
                $this->logThemeEvent($name, 'creation', 'Theme created', $userId);
            } else {
                $this->addFlashMessage('error', 'Failed to create theme');
            }
            
            $this->redirect('/admin/premium-themes');
        } catch (Exception $e) {
            $this->addFlashMessage('error', 'Error: ' . $e->getMessage());
            $this->redirect('/admin/premium-themes/create');
        }
    }

    /**
     * Update theme
     */
    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
            $this->redirect('/admin/premium-themes/' . $id . '/edit');
        }
        
        try {
            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';
            
            if (empty($name)) {
                $this->addFlashMessage('error', 'Theme name is required');
                $this->redirect('/admin/premium-themes/' . $id . '/edit');
            }
            
            $stmt = $this->db->prepare("
                UPDATE premium_themes SET name = ?, description = ?, updated_at = NOW() WHERE id = ?
            ");
            $result = $stmt->execute([$name, $description, $id]);
            
            if ($result) {
                $this->addFlashMessage('success', 'Theme updated successfully');
            } else {
                $this->addFlashMessage('error', 'Failed to update theme');
            }
            
            $this->redirect('/admin/premium-themes/' . $id);
        } catch (Exception $e) {
            $this->addFlashMessage('error', 'Error: ' . $e->getMessage());
            $this->redirect('/admin/premium-themes/' . $id . '/edit');
        }
    }

    /**
     * Delete theme
     */
    public function destroy($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
            $this->redirect('/admin/premium-themes');
        }
        
        try {
            $stmt = $this->db->prepare("DELETE FROM premium_themes WHERE id = ?");
            $result = $stmt->execute([$id]);
            
            if ($result) {
                $this->addFlashMessage('success', 'Theme deleted successfully');
            } else {
                $this->addFlashMessage('error', 'Failed to delete theme');
            }
            
            $this->redirect('/admin/premium-themes');
        } catch (Exception $e) {
            $this->addFlashMessage('error', 'Error: ' . $e->getMessage());
            $this->redirect('/admin/premium-themes');
        }
    }

    /**
     * Deactivate theme
     */
    public function deactivate($id)
    {
        try {
            $stmt = $this->db->prepare("UPDATE premium_themes SET is_active = 0 WHERE id = ?");
            $stmt->execute([$id]);
            
            $this->addFlashMessage('success', 'Theme deactivated');
            $this->redirect('/admin/premium-themes');
        } catch (Exception $e) {
            $this->addFlashMessage('error', 'Error: ' . $e->getMessage());
            $this->redirect('/admin/premium-themes');
        }
    }

    /**
     * Show analytics (alias for showAnalytics)
     */
    public function analytics($id)
    {
        return $this->showAnalytics($id);
    }

    /**
     * Update customization
     */
    public function updateCustomization($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/premium-themes/' . $id . '/customize');
        }
        
        try {
            $customization = $_POST['customization'] ?? [];
            $userId = $_SESSION['user_id'] ?? 'default';
            
            $stmt = $this->db->prepare("
                UPDATE premium_themes SET customization = ?, updated_at = NOW() WHERE id = ?
            ");
            $result = $stmt->execute([json_encode($customization), $id]);
            
            if ($result) {
                $this->addFlashMessage('success', 'Customization saved');
                $this->logThemeEvent('theme_' . $id, 'customization_update', 'Theme customized', $userId);
            } else {
                $this->addFlashMessage('error', 'Failed to save customization');
            }
            
            $this->redirect('/admin/premium-themes/' . $id . '/customize');
        } catch (Exception $e) {
            $this->addFlashMessage('error', 'Error: ' . $e->getMessage());
            $this->redirect('/admin/premium-themes/' . $id . '/customize');
        }
    }

    /**
     * Export theme
     */
    public function export($id)
    {
        try {
            $theme = $this->getThemeById($id);
            if (!$theme) {
                $this->addFlashMessage('error', 'Theme not found');
                $this->redirect('/admin/premium-themes');
            }
            
            // Return JSON export
            header('Content-Type: application/json');
            header('Content-Disposition: attachment; filename="theme-' . $theme['id'] . '.json"');
            echo json_encode($theme, JSON_PRETTY_PRINT);
            exit;
        } catch (Exception $e) {
            $this->addFlashMessage('error', 'Error: ' . $e->getMessage());
            $this->redirect('/admin/premium-themes');
        }
    }

    // Missing controller methods for routes
    
    /**
     * Show create theme form
     * 
     * @return string
     */
    public function create()
    {
        try {
            $this->view->render('admin/premium-themes/create', [
                'title' => 'Create Premium Theme'
            ]);
            
        } catch (Exception $e) {
            $this->view->render('admin/error', [
                'title' => 'Error',
                'message' => 'Failed to load create form: ' . $e->getMessage()
            ]);
        }
    }
    
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
                $this->view->render('admin/error', [
                    'title' => 'Error',
                    'message' => 'Theme not found'
                ]);
                return;
            }
            
            $this->view->render('admin/premium-themes/show', [
                'title' => 'Theme Details: ' . ($theme['name'] ?? 'Unknown'),
                'theme' => $theme
            ]);
            
        } catch (Exception $e) {
            $this->view->render('admin/error', [
                'title' => 'Error',
                'message' => 'Failed to load theme: ' . $e->getMessage()
            ]);
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
                $this->view->render('admin/error', [
                    'title' => 'Error',
                    'message' => 'Theme not found'
                ]);
                return;
            }
            
            $this->view->render('admin/premium-themes/edit', [
                'title' => 'Edit Theme: ' . ($theme['name'] ?? 'Unknown'),
                'theme' => $theme
            ]);
            
        } catch (Exception $e) {
            $this->view->render('admin/error', [
                'title' => 'Error',
                'message' => 'Failed to load theme: ' . $e->getMessage()
            ]);
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
                $this->view->render('admin/error', [
                    'title' => 'Error',
                    'message' => 'Theme not found'
                ]);
                return;
            }
            
            $settings = $this->themeManager->getThemeSettings($theme['name']);
            
            $this->view->render('admin/premium-themes/settings', [
                'title' => 'Theme Settings: ' . ($theme['name'] ?? 'Unknown'),
                'theme' => $theme,
                'settings' => $settings['settings'] ?? []
            ]);
            
        } catch (Exception $e) {
            $this->view->render('admin/error', [
                'title' => 'Error',
                'message' => 'Failed to load settings: ' . $e->getMessage()
            ]);
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
                $this->view->render('admin/error', [
                    'title' => 'Error',
                    'message' => 'Theme not found'
                ]);
                return;
            }
            
            $analytics = $this->getThemeAnalyticsById($id);
            
            $this->view->render('admin/premium-themes/analytics', [
                'title' => 'Theme Analytics: ' . ($theme['name'] ?? 'Unknown'),
                'theme' => $theme,
                'analytics' => $analytics
            ]);
            
        } catch (Exception $e) {
            $this->view->render('admin/error', [
                'title' => 'Error',
                'message' => 'Failed to load analytics: ' . $e->getMessage()
            ]);
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
                $this->view->render('admin/error', [
                    'title' => 'Error',
                    'message' => 'Theme not found'
                ]);
                return;
            }
            
            $this->view->render('admin/premium-themes/preview', [
                'title' => 'Preview: ' . ($theme['name'] ?? 'Unknown'),
                'theme' => $theme,
                'preview' => true
            ]);
            
        } catch (Exception $e) {
            $this->view->render('admin/error', [
                'title' => 'Error',
                'message' => 'Failed to load preview: ' . $e->getMessage()
            ]);
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
            
            // Validate and stage upload via FileUploadService
            $uploader = new FileUploadService();
            $dest = (defined('STORAGE_PATH') ? STORAGE_PATH : sys_get_temp_dir()) . '/uploads/premium-themes';
            $upload = $uploader->uploadTheme($_FILES['theme_zip'], $dest);
            if (!($upload['success'] ?? false)) {
                $this->addFlashMessage('error', $upload['message'] ?? 'Upload failed');
                return $this->redirect('/admin/premium-themes');
            }
            $zipPath = $upload['file_path'];
            
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
            
            $this->view->render('admin/premium-themes/marketplace', [
                'title' => 'Theme Marketplace',
                'themes' => $marketplaceThemes
            ]);
            
        } catch (Exception $e) {
            $this->view->render('admin/error', [
                'title' => 'Error',
                'message' => 'Failed to load marketplace: ' . $e->getMessage()
            ]);
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

    /**
     * Get active theme
     */
    private function getActiveTheme()
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM premium_themes WHERE is_active = 1 LIMIT 1");
            $stmt->execute();
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Check if user has premium access
     */
    private function userHasPremiumAccess()
    {
        // Check if user has active subscription or is admin
        if ($this->auth->isAdmin()) {
            return true;
        }
        
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            return false;
        }

        try {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as count FROM subscriptions 
                WHERE user_id = ? AND status = 'active' AND expires_at > NOW()
            ");
            $stmt->execute([$userId]);
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            return ($result['count'] ?? 0) > 0;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Redirect to URL
     */
    private function redirect($url)
    {
        header('Location: ' . $url);
        exit;
    }

    /**
     * Add flash message
     */
    private function addFlashMessage($type, $message)
    {
        if (!isset($_SESSION['flash_messages'])) {
            $_SESSION['flash_messages'] = [];
        }
        $_SESSION['flash_messages'][] = [
            'type' => $type,
            'message' => $message
        ];
    }

    /**
     * Log theme event
     */
    private function logThemeEvent($themeName, $eventType, $description, $userId)
    {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO theme_analytics (theme_name, event_type, description, user_id, created_at)
                VALUES (?, ?, ?, ?, NOW())
            ");
            $stmt->execute([$themeName, $eventType, $description, $userId]);
        } catch (Exception $e) {
            // Silently fail
        }
    }
}
