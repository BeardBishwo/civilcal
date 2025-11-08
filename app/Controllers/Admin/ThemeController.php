<?php
namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Services\ThemeManager;

class ThemeController extends Controller
{
    public function index()
    {
        $themes = ThemeManager::getAllThemes();
        $activeTheme = $this->getActiveTheme();
        
        // Prepare data for the view
        $this->data['currentPage'] = 'themes';
        $this->data['themes'] = $themes;
        $this->data['activeTheme'] = $activeTheme;
        $this->data['title'] = 'Themes Management - Admin Panel';
        
        // Load the view
        $this->loadView('admin/themes/index', $this->data);
    }

    public function activateTheme()
    {
        if ($_POST && isset($_POST['theme_name'])) {
            $themeName = $_POST['theme_name'];
            $result = ThemeManager::activateTheme($themeName);
            
            echo json_encode($result);
            return;
        }
        
        echo json_encode(['success' => false, 'message' => 'Invalid request']);
    }

    public function uploadTheme()
    {
        if ($_FILES && isset($_FILES['theme_zip'])) {
            $result = ThemeManager::installTheme($_FILES['theme_zip']);
            echo json_encode($result);
            return;
        }
        
        echo json_encode(['success' => false, 'message' => 'No file uploaded']);
    }

    private function getActiveTheme()
    {
        // Get active theme from database or config
        return 'default';
    }
}
?>
