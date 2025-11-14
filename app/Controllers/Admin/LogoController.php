<?php

namespace App\Controllers\Admin;

use App\Core\Controller;

class LogoController extends Controller
{
    public function index()
    {
        // Check admin authentication
        if (empty($_SESSION['is_admin'])) {
            header('Location: /login');
            exit;
        }
        
        // Load the logo settings view
        $viewPath = __DIR__ . '/../../../themes/admin/views/logo-settings.php';
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            // Fallback to default theme
            $fallbackPath = __DIR__ . '/../../../themes/default/views/admin/logo-settings.php';
            if (file_exists($fallbackPath)) {
                include $fallbackPath;
            } else {
                echo "Logo settings page not found.";
            }
        }
    }
    
    public function update()
    {
        // Check admin authentication
        if (empty($_SESSION['is_admin'])) {
            http_response_code(403);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            exit;
        }
        
        try {
            $site_meta = get_site_meta();
            
            // Update logo settings
            if (isset($_POST['logo_url'])) {
                $site_meta['logo'] = $this->sanitizeInput($_POST['logo_url']);
            }
            
            if (isset($_POST['logo_text'])) {
                $site_meta['logo_text'] = $this->sanitizeInput($_POST['logo_text']);
            }
            
            if (isset($_POST['header_style'])) {
                $site_meta['header_style'] = $this->sanitizeInput($_POST['header_style']);
            }
            
            // Logo settings
            $site_meta['logo_settings'] = [
                'show_logo' => isset($_POST['show_logo']),
                'show_text' => isset($_POST['show_text']),
                'text_position' => $this->sanitizeInput($_POST['text_position'] ?? 'right'),
                'logo_height' => $this->sanitizeInput($_POST['logo_height'] ?? '40px'),
                'text_size' => $this->sanitizeInput($_POST['text_size'] ?? '1.5rem'),
                'text_weight' => $this->sanitizeInput($_POST['text_weight'] ?? '700'),
                'spacing' => $this->sanitizeInput($_POST['spacing'] ?? '12px'),
                'border_radius' => $this->sanitizeInput($_POST['border_radius'] ?? '8px'),
                'shadow' => $this->sanitizeInput($_POST['shadow'] ?? 'subtle'),
                'hover_effect' => $this->sanitizeInput($_POST['hover_effect'] ?? 'scale'),
                'logo_style' => $this->sanitizeInput($_POST['logo_style'] ?? 'modern')
            ];
            
            // Brand colors
            $site_meta['brand_colors'] = [
                'primary' => $this->sanitizeInput($_POST['brand_primary'] ?? '#4f46e5'),
                'secondary' => $this->sanitizeInput($_POST['brand_secondary'] ?? '#10b981'),
                'accent' => $this->sanitizeInput($_POST['brand_accent'] ?? '#f59e0b')
            ];
            
            // Save to file
            $metaFile = __DIR__ . '/../../../app/db/site_meta.json';
            $success = file_put_contents($metaFile, json_encode($site_meta, JSON_PRETTY_PRINT));
            
            if ($success) {
                // If AJAX request, return JSON
                if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => true, 'message' => 'Logo settings updated successfully!']);
                } else {
                    // Redirect back with success message
                    $_SESSION['logo_success'] = 'Logo settings updated successfully!';
                    header('Location: /admin/logo-settings');
                }
            } else {
                throw new \Exception('Failed to save logo settings');
            }
            
        } catch (\Exception $e) {
            error_log("Logo settings update error: " . $e->getMessage());
            
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                header('Content-Type: application/json');
                http_response_code(500);
                echo json_encode(['error' => 'Failed to update logo settings: ' . $e->getMessage()]);
            } else {
                $_SESSION['logo_error'] = 'Failed to update logo settings: ' . $e->getMessage();
                header('Location: /admin/logo-settings');
            }
        }
        
        exit;
    }
    
    private function sanitizeInput($input)
    {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }
}
