<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Services\ThemeManager;
use App\Services\AuditLogger;
use Exception;

class ThemeCustomizeController extends Controller
{
    private $themeManager;

    public function __construct()
    {
        parent::__construct();
        $this->themeManager = new ThemeManager();
        
        if (!$this->auth->check() || !$this->auth->isAdmin()) {
            $this->redirect('/login');
        }
    }

    /**
     * Show theme customization page
     */
    public function index($id = null)
    {
        if (!$id) {
            $this->redirect('/admin/themes');
            return;
        }

        $theme = $this->themeManager->getThemeById($id);
        if (!$theme) {
            $this->redirect('/admin/themes');
            return;
        }

        $themeConfig = $this->themeManager->loadThemeConfig($theme['name']);
        $customizations = $this->getThemeCustomizations($id);

        $this->view->render('admin/themes/customize', [
            'theme' => $theme,
            'themeConfig' => $themeConfig,
            'customizations' => $customizations
        ]);
    }

    /**
     * Save color customizations
     */
    public function saveColors($id)
    {
        if (!$this->isAjax() || !verify_csrf($_POST['csrf_token'] ?? '')) {
            $this->error('Invalid request or CSRF token');
            return;
        }

        try {
            $theme = $this->themeManager->getThemeById($id);
            if (!$theme) {
                $this->error('Theme not found');
                return;
            }

            $colors = [
                'primary' => $this->sanitizeColor($_POST['primary_color'] ?? ''),
                'secondary' => $this->sanitizeColor($_POST['secondary_color'] ?? ''),
                'accent' => $this->sanitizeColor($_POST['accent_color'] ?? ''),
                'background' => $this->sanitizeColor($_POST['background_color'] ?? ''),
                'text' => $this->sanitizeColor($_POST['text_color'] ?? ''),
                'text_secondary' => $this->sanitizeColor($_POST['text_secondary_color'] ?? '')
            ];

            $customizations = $this->getThemeCustomizations($id);
            $customizations['colors'] = $colors;

            $this->saveThemeCustomizations($id, $customizations);

            AuditLogger::info('theme_colors_customized', ['theme_id' => $id, 'colors' => $colors]);
            $this->success('Colors updated successfully', ['colors' => $colors]);

        } catch (Exception $e) {
            error_log("Theme Color Customization Error: " . $e->getMessage());
            $this->error('Failed to save colors: ' . $e->getMessage());
        }
    }

    /**
     * Save typography customizations
     */
    public function saveTypography($id)
    {
        if (!$this->isAjax() || !verify_csrf($_POST['csrf_token'] ?? '')) {
            $this->error('Invalid request or CSRF token');
            return;
        }

        try {
            $theme = $this->themeManager->getThemeById($id);
            if (!$theme) {
                $this->error('Theme not found');
                return;
            }

            $typography = [
                'font_family' => sanitize_text_field($_POST['font_family'] ?? ''),
                'heading_size' => sanitize_text_field($_POST['heading_size'] ?? ''),
                'body_size' => sanitize_text_field($_POST['body_size'] ?? ''),
                'line_height' => sanitize_text_field($_POST['line_height'] ?? '')
            ];

            $customizations = $this->getThemeCustomizations($id);
            $customizations['typography'] = $typography;

            $this->saveThemeCustomizations($id, $customizations);

            AuditLogger::info('theme_typography_customized', ['theme_id' => $id, 'typography' => $typography]);
            $this->success('Typography updated successfully', ['typography' => $typography]);

        } catch (Exception $e) {
            error_log("Theme Typography Customization Error: " . $e->getMessage());
            $this->error('Failed to save typography: ' . $e->getMessage());
        }
    }

    /**
     * Save feature toggles
     */
    public function saveFeatures($id)
    {
        if (!$this->isAjax() || !verify_csrf($_POST['csrf_token'] ?? '')) {
            $this->error('Invalid request or CSRF token');
            return;
        }

        try {
            $theme = $this->themeManager->getThemeById($id);
            if (!$theme) {
                $this->error('Theme not found');
                return;
            }

            $features = [
                'dark_mode' => isset($_POST['dark_mode']) ? (bool)$_POST['dark_mode'] : false,
                'animations' => isset($_POST['animations']) ? (bool)$_POST['animations'] : false,
                'glassmorphism' => isset($_POST['glassmorphism']) ? (bool)$_POST['glassmorphism'] : false,
                '3d_effects' => isset($_POST['3d_effects']) ? (bool)$_POST['3d_effects'] : false
            ];

            $customizations = $this->getThemeCustomizations($id);
            $customizations['features'] = $features;

            $this->saveThemeCustomizations($id, $customizations);

            AuditLogger::info('theme_features_customized', ['theme_id' => $id, 'features' => $features]);
            $this->success('Features updated successfully', ['features' => $features]);

        } catch (Exception $e) {
            error_log("Theme Features Customization Error: " . $e->getMessage());
            $this->error('Failed to save features: ' . $e->getMessage());
        }
    }

    /**
     * Save layout customizations
     */
    public function saveLayout($id)
    {
        if (!$this->isAjax() || !verify_csrf($_POST['csrf_token'] ?? '')) {
            $this->error('Invalid request or CSRF token');
            return;
        }

        try {
            $theme = $this->themeManager->getThemeById($id);
            if (!$theme) {
                $this->error('Theme not found');
                return;
            }

            $layout = [
                'header_style' => sanitize_text_field($_POST['header_style'] ?? 'logo_text'),
                'footer_layout' => sanitize_text_field($_POST['footer_layout'] ?? 'standard'),
                'container_width' => sanitize_text_field($_POST['container_width'] ?? '1200px')
            ];

            $customizations = $this->getThemeCustomizations($id);
            $customizations['layout'] = $layout;

            $this->saveThemeCustomizations($id, $customizations);

            AuditLogger::info('theme_layout_customized', ['theme_id' => $id, 'layout' => $layout]);
            $this->success('Layout updated successfully', ['layout' => $layout]);

        } catch (Exception $e) {
            error_log("Theme Layout Customization Error: " . $e->getMessage());
            $this->error('Failed to save layout: ' . $e->getMessage());
        }
    }

    /**
     * Save custom CSS
     */
    public function saveCustomCSS($id)
    {
        if (!$this->isAjax() || !verify_csrf($_POST['csrf_token'] ?? '')) {
            $this->error('Invalid request or CSRF token');
            return;
        }

        try {
            $theme = $this->themeManager->getThemeById($id);
            if (!$theme) {
                $this->error('Theme not found');
                return;
            }

            $customCSS = $_POST['custom_css'] ?? '';
            
            // Basic validation - check for malicious content
            if (preg_match('/<script|javascript:|onerror|onclick/i', $customCSS)) {
                $this->error('Invalid CSS content detected');
                return;
            }

            $customizations = $this->getThemeCustomizations($id);
            $customizations['custom_css'] = $customCSS;

            $this->saveThemeCustomizations($id, $customizations);

            AuditLogger::info('theme_custom_css_updated', ['theme_id' => $id]);
            $this->success('Custom CSS updated successfully');

        } catch (Exception $e) {
            error_log("Theme Custom CSS Error: " . $e->getMessage());
            $this->error('Failed to save custom CSS: ' . $e->getMessage());
        }
    }

    /**
     * Get live preview
     */
    public function preview($id)
    {
        try {
            $theme = $this->themeManager->getThemeById($id);
            if (!$theme) {
                http_response_code(404);
                echo 'Theme not found';
                return;
            }

            $customizations = $this->getThemeCustomizations($id);
            $themeConfig = $this->themeManager->loadThemeConfig($theme['name']);

            // Merge customizations with theme config
            if (isset($customizations['colors'])) {
                $themeConfig['config']['colors'] = array_merge(
                    $themeConfig['config']['colors'] ?? [],
                    $customizations['colors']
                );
            }

            // Generate CSS from customizations
            $css = $this->generatePreviewCSS($themeConfig, $customizations);

            $this->view->render('admin/themes/preview', [
                'theme' => $theme,
                'themeConfig' => $themeConfig,
                'customizations' => $customizations,
                'previewCSS' => $css
            ]);

        } catch (Exception $e) {
            error_log("Theme Preview Error: " . $e->getMessage());
            http_response_code(500);
            echo 'Error loading preview';
        }
    }

    /**
     * Reset customizations to default
     */
    public function reset($id)
    {
        if (!$this->isAjax() || !verify_csrf($_POST['csrf_token'] ?? '')) {
            $this->error('Invalid request or CSRF token');
            return;
        }

        try {
            $theme = $this->themeManager->getThemeById($id);
            if (!$theme) {
                $this->error('Theme not found');
                return;
            }

            // Delete customizations
            $this->deleteThemeCustomizations($id);

            AuditLogger::info('theme_customizations_reset', ['theme_id' => $id]);
            $this->success('Theme customizations reset to default');

        } catch (Exception $e) {
            error_log("Theme Reset Error: " . $e->getMessage());
            $this->error('Failed to reset theme: ' . $e->getMessage());
        }
    }

    /**
     * Sanitize hex color
     */
    private function sanitizeColor($color)
    {
        $color = trim($color);
        if (preg_match('/^#[0-9A-Fa-f]{6}$/', $color)) {
            return $color;
        }
        return '#000000';
    }

    /**
     * Get theme customizations from database
     */
    private function getThemeCustomizations($themeId)
    {
        $stmt = $this->db->prepare("SELECT customizations_json FROM theme_customizations WHERE theme_id = ?");
        $stmt->execute([$themeId]);
        $result = $stmt->fetch();

        if ($result && $result['customizations_json']) {
            return json_decode($result['customizations_json'], true) ?? [];
        }

        return [];
    }

    /**
     * Save theme customizations to database
     */
    private function saveThemeCustomizations($themeId, $customizations)
    {
        $json = json_encode($customizations);
        
        $stmt = $this->db->prepare("
            INSERT INTO theme_customizations (theme_id, customizations_json, updated_at)
            VALUES (?, ?, NOW())
            ON DUPLICATE KEY UPDATE customizations_json = ?, updated_at = NOW()
        ");
        
        return $stmt->execute([$themeId, $json, $json]);
    }

    /**
     * Delete theme customizations
     */
    private function deleteThemeCustomizations($themeId)
    {
        $stmt = $this->db->prepare("DELETE FROM theme_customizations WHERE theme_id = ?");
        return $stmt->execute([$themeId]);
    }

    /**
     * Generate preview CSS from customizations
     */
    private function generatePreviewCSS($themeConfig, $customizations)
    {
        $css = ":root {\n";

        // Colors
        if (isset($customizations['colors'])) {
            foreach ($customizations['colors'] as $key => $value) {
                $css .= "  --color-{$key}: {$value};\n";
            }
        }

        // Typography
        if (isset($customizations['typography'])) {
            $typo = $customizations['typography'];
            $css .= "  --font-family: {$typo['font_family']};\n";
            $css .= "  --heading-size: {$typo['heading_size']};\n";
            $css .= "  --body-size: {$typo['body_size']};\n";
            $css .= "  --line-height: {$typo['line_height']};\n";
        }

        $css .= "}\n\n";

        // Custom CSS
        if (isset($customizations['custom_css'])) {
            $css .= $customizations['custom_css'];
        }

        return $css;
    }
}
?>
