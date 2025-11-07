<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Services\ThemeManager;

class ThemeController extends Controller {
    private $themeManager;
    
    public function __construct() {
        parent::__construct();
        $this->themeManager = new ThemeManager();
    }
    
    /**
     * Theme management dashboard
     */
    public function index() {
        $themes = $this->themeManager->getAllThemes();
        $activeTheme = $this->themeManager->getActiveTheme();
        
        $this->view('admin/themes/index', [
            'title' => 'Theme Management',
            'themes' => $themes,
            'activeTheme' => $activeTheme
        ]);
    }
    
    /**
     * Upload theme via admin
     */
    public function upload() {
        if ($_FILES['theme_zip']['error'] === UPLOAD_ERR_OK) {
            $uploadedFile = $_FILES['theme_zip']['tmp_name'];
            
            if ($this->themeManager->installTheme($uploadedFile)) {
                $this->json(['success' => true, 'message' => 'Theme installed successfully']);
            } else {
                $this->json(['success' => false, 'message' => 'Theme installation failed']);
            }
        } else {
            $this->json(['success' => false, 'message' => 'No file uploaded or upload error occurred']);
        }
    }
    
    /**
     * Activate a theme
     */
    public function activate($themeSlug) {
        $result = $this->themeManager->activateTheme($themeSlug);
        
        if ($result) {
            $this->json(['success' => true, 'message' => 'Theme activated successfully']);
        } else {
            $this->json(['success' => false, 'message' => 'Failed to activate theme']);
        }
    }
    
    /**
     * Delete a theme
     */
    public function delete($themeSlug) {
        $result = $this->themeManager->deleteTheme($themeSlug);
        
        if ($result) {
            $this->json(['success' => true, 'message' => 'Theme deleted successfully']);
        } else {
            $this->json(['success' => false, 'message' => 'Failed to delete theme (default themes cannot be deleted)']);
        }
    }
    
    /**
     * Get theme details
     */
    public function details($themeSlug) {
        $themes = $this->themeManager->getAllThemes();
        $theme = null;
        
        foreach ($themes as $t) {
            if ($t['slug'] === $themeSlug) {
                $theme = $t;
                break;
            }
        }
        
        if ($theme) {
            $this->json(['success' => true, 'theme' => $theme]);
        } else {
            $this->json(['success' => false, 'message' => 'Theme not found']);
        }
    }
}
?>
