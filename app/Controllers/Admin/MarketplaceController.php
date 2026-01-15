<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Services\FileService;
use Exception;

/**
 * Marketplace Controller
 * 
 * Handles unified marketplace for scripts, plugins, and themes
 * 
 * @package App\Controllers\Admin
 * @version 1.1.0
 */
class MarketplaceController extends Controller
{
    private $themeManager;

    public function __construct()
    {
        parent::__construct();
        // PremiumThemeManager is in App\Services namespace
        $this->themeManager = new \App\Services\PremiumThemeManager($this->db);
    }

    /**
     * Display unified marketplace
     * 
     * @return void
     */
    public function index()
    {
        try {
            $items = $this->getMarketplaceItems();

            $this->view->render('admin/marketplace/index', [
                'title' => 'Marketplace',
                'items' => $items,
                'categories' => $this->getMarketplaceCategories()
            ]);
        } catch (Exception $e) {
            $this->view->render('admin/error', [
                'title' => 'Error',
                'message' => 'Failed to load marketplace: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Handle license validation for purchased items
     * 
     * @return void
     */
    public function validateLicense()
    {
        try {
            // CSRF Protection
            if (!verify_csrf($_POST['csrf_token'] ?? '')) {
                $this->addFlashMessage('error', 'CSRF verification failed');
                $this->redirect('/admin/marketplace');
                return;
            }

            $licenseKey = $_POST['license_key'] ?? '';
            $domain = $_SERVER['HTTP_HOST'] ?? '';
            $userId = $_SESSION['user_id'] ?? 'default';

            if (empty($licenseKey)) {
                $this->addFlashMessage('error', 'License key is required');
                $this->redirect('/admin/marketplace');
                return;
            }

            $result = $this->themeManager->validateLicense($licenseKey, $domain, $userId);

            if ($result['valid']) {
                $this->addFlashMessage('success', 'License validated successfully');
            } else {
                $this->addFlashMessage('error', $result['message']);
            }

            $this->redirect('/admin/marketplace');
            return;
        } catch (Exception $e) {
            $this->addFlashMessage('error', 'License validation failed: ' . $e->getMessage());
            $this->redirect('/admin/marketplace');
            return;
        }
    }

    /**
     * Handle item installation from ZIP
     * 
     * @return void
     */
    public function install()
    {
        try {
            // CSRF Protection
            if (!verify_csrf($_POST['csrf_token'] ?? '')) {
                $this->addFlashMessage('error', 'CSRF verification failed');
                $this->redirect('/admin/marketplace');
                return;
            }

            $licenseKey = $_POST['license_key'] ?? '';
            $type = $_POST['type'] ?? 'theme'; // theme, plugin, script
            $userId = $_SESSION['user_id'] ?? 'default';

            if (empty($licenseKey)) {
                $this->addFlashMessage('error', 'License key is required for installation');
                $this->redirect('/admin/marketplace');
                return;
            }

            if (!isset($_FILES['package_zip']) || $_FILES['package_zip']['error'] !== UPLOAD_ERR_OK) {
                $this->addFlashMessage('error', 'Please select a valid package ZIP file');
                $this->redirect('/admin/marketplace');
                return;
            }

            // Use FileService for secure Marketplace upload (Binary Scanning + Entropy Filenames)
            $upload = FileService::uploadAdminFile($_FILES['package_zip'], 'marketplace');

            if (!$upload['success']) {
                $this->addFlashMessage('error', $upload['error'] ?? 'Upload failed');
                $this->redirect('/admin/marketplace');
                return;
            }

            $zipPath = $upload['path'];

            // Dispatch to correct manager based on type
            if ($type === 'plugin') {
                $pluginManager = new \App\Services\PluginManager();
                // Assuming PluginManager has a similar sync install method
                $result = $pluginManager->installPlugin($zipPath);
            } else {
                // Default to Theme for 'theme' or 'script' (scripts are often bundled as themes or handled by theme manager in this architecture)
                $result = $this->themeManager->installThemeFromZip($zipPath, $licenseKey, $userId);
            }

            if ($result['success']) {
                $this->addFlashMessage('success', ucfirst($type) . ' installed successfully');
            } else {
                $this->addFlashMessage('error', $result['message']);
            }

            $this->redirect('/admin/marketplace');
            return;
        } catch (Exception $e) {
            $this->addFlashMessage('error', 'Installation failed: ' . $e->getMessage());
            $this->redirect('/admin/marketplace');
            return;
        }
    }
    
    // Private helper methods

    /**
     * Get items for the marketplace
     * 
     * @return array
     */
    private function getMarketplaceItems()
    {
        // Sample data for Scripts, Plugins, and Themes
        return [
            [
                'id' => 1,
                'type' => 'theme',
                'name' => 'Professional Dark',
                'description' => 'A sleek dark theme for professional use',
                'price' => 29.99,
                'rating' => 4.8,
                'preview_image' => '/assets/themes/professional-dark-preview.jpg'
            ],
            [
                'id' => 101,
                'type' => 'script',
                'name' => 'Advanced Finance Pack',
                'description' => '15+ advanced financial calculators',
                'price' => 49.99,
                'rating' => 4.9,
                'preview_image' => '/assets/scripts/finance-pack.jpg'
            ],
            [
                'id' => 201,
                'type' => 'plugin',
                'name' => 'SEO Booster',
                'description' => 'Automatically optimize your calculators for search engines',
                'price' => 19.99,
                'rating' => 4.7,
                'preview_image' => '/assets/plugins/seo-booster.jpg'
            ]
        ];
    }

    /**
     * Get marketplace categories
     * 
     * @return array
     */
    private function getMarketplaceCategories()
    {
        return [
            ['id' => 'scripts', 'name' => 'Scripts', 'icon' => 'fas fa-code'],
            ['id' => 'plugins', 'name' => 'Plugins', 'icon' => 'fas fa-plug'],
            ['id' => 'themes', 'name' => 'Themes', 'icon' => 'fas fa-palette']
        ];
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
}
