<?php
/**
 * Theme Builder Service
 * Handles visual theme creation, file generation, and live preview
 * PHP 7.4 Compatible Version
 */

namespace App\Services;

use App\Core\Database;

class ThemeBuilder
{
    // Define missing theme constants
    const TYPOGRAPHY_DEFAULT_FAMILY = 'Inter';
    const TYPOGRAPHY_SECONDARY_FAMILY = 'Roboto';
    const FONT_FAMILY_PRIMARY = 'Inter';
    const FONT_FAMILY_SECONDARY = 'Roboto';
    
    private $db;
    private $themesDir;
    private $storageDir;
    private $previewDir;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->themesDir = BASE_PATH . '/themes/';
        $this->storageDir = BASE_PATH . '/storage/editor/';
        $this->previewDir = $this->storageDir . 'previews/';
        
        // Ensure directories exist
        $this->ensureDirectories();
    }

    /**
     * Ensure required directories exist
     */
    private function ensureDirectories()
    {
        $directories = [
            $this->storageDir,
            $this->previewDir,
            $this->storageDir . 'themes/',
            $this->storageDir . 'backups/',
            $this->storageDir . 'exports/'
        ];

        foreach ($directories as $dir) {
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
        }
    }

    /**
     * Create new theme from visual configuration
     */
    public function createTheme($themeData, $userId)
    {
        try {
            $themeData['slug'] = $this->generateSlug($themeData['name']);
            $themeData['created_by'] = $userId;
            $themeData['updated_by'] = $userId;
            
            // Validate theme data
            $this->validateThemeData($themeData);
            
            // Generate theme files
            $themePath = $this->generateThemeFiles($themeData);
            
            // Save to database
            $themeId = $this->saveThemeToDatabase($themeData);
            
            // Log creation
            $this->logAction($userId, 'created', 'theme', $themeId, $themeData);
            
            return [
                'success' => true,
                'theme_id' => $themeId,
                'theme_slug' => $themeData['slug'],
                'theme_path' => $themePath,
                'message' => 'Theme created successfully'
            ];
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Theme creation failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Update existing theme
     */
    public function updateTheme($themeId, $themeData, $userId)
    {
        try {
            $themeData['updated_by'] = $userId;
            
            // Get current theme data for change tracking
            $currentTheme = $this->getThemeById($themeId);
            
            // Validate theme data
            $this->validateThemeData($themeData);
            
            // Generate updated theme files
            $themeData['slug'] = $currentTheme['slug']; // Keep original slug
            $themePath = $this->generateThemeFiles($themeData);
            
            // Update database
            $this->updateThemeInDatabase($themeId, $themeData);
            
            // Create version entry
            $this->createVersionEntry($themeId, $currentTheme, $themeData, $userId);
            
            // Log update
            $this->logAction($userId, 'updated', 'theme', $themeId, $themeData, $currentTheme);
            
            return [
                'success' => true,
                'message' => 'Theme updated successfully'
            ];
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Theme update failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Generate live preview data for real-time editing
     */
    public function generateLivePreview($themeData)
    {
        try {
            // Generate CSS from theme configuration
            $css = $this->generateThemeCSS($themeData);
            
            // Generate JavaScript
            $js = $this->generateThemeJS($themeData);
            
            // Generate preview HTML
            $html = $this->generatePreviewHTML($themeData);
            
            // Generate assets
            $assets = $this->generateThemeAssets($themeData);
            
            return [
                'css' => $css,
                'js' => $js,
                'html' => $html,
                'assets' => $assets,
                'config' => $themeData
            ];
            
        } catch (\Exception $e) {
            throw new \Exception('Preview generation failed: ' . $e->getMessage());
        }
    }

    /**
     * Generate theme files in the themes directory
     */
    private function generateThemeFiles($themeData)
    {
        $themeSlug = $themeData['slug'];
        $themePath = $this->themesDir . $themeSlug;
        
        // Create theme directory structure
        $this->createThemeDirectoryStructure($themePath);
        
        // Generate main CSS file
        $cssContent = $this->generateThemeCSS($themeData);
        file_put_contents($themePath . '/assets/css/theme.css', $cssContent);
        
        // Generate main layout file
        $layoutContent = $this->generateThemeLayout($themeData);
        file_put_contents($themePath . '/views/layouts/main.php', $layoutContent);
        
        // Generate theme configuration JSON
        $configContent = $this->generateThemeConfig($themeData);
        file_put_contents($themePath . '/theme.json', $configContent);
        
        // Generate JavaScript
        $jsContent = $this->generateThemeJS($themeData);
        file_put_contents($themePath . '/assets/js/theme.js', $jsContent);
        
        // Generate custom components if specified
        if (isset($themeData['components']) && !empty($themeData['components'])) {
            $this->generateCustomComponents($themePath, $themeData['components']);
        }
        
        return $themePath;
    }

    /**
     * Create theme directory structure
     */
    private function createThemeDirectoryStructure($themePath)
    {
        $directories = [
            '/assets/css',
            '/assets/js',
            '/assets/images',
            '/assets/fonts',
            '/views/layouts',
            '/views/partials',
            '/views/calculators',
            '/views/components'
        ];

        foreach ($directories as $dir) {
            $fullPath = $themePath . $dir;
            if (!is_dir($fullPath)) {
                mkdir($fullPath, 0755, true);
            }
        }
    }

    /**
     * Generate CSS from theme configuration
     */
    private function generateThemeCSS($themeData)
    {
        $colors = $themeData['color_palette'];
        $typography = $themeData['typography'];
        $layout = $themeData['layout_config'];
        $components = isset($themeData['components']) ? $themeData['components'] : [];
        
        // Handle gradient colors with proper isset checks
        $gradientPrimary = isset($colors['gradient_primary']) 
            ? $colors['gradient_primary'] 
            : 'linear-gradient(135deg, ' . $colors['primary'] . ', ' . $colors['secondary'] . ')';
            
        $gradientSurface = isset($colors['gradient_surface']) 
            ? $colors['gradient_surface'] 
            : 'linear-gradient(135deg, ' . $colors['surface'] . ', #ffffff)';
        
        // Pre-calculate responsive font sizes to avoid mathematical operations in string concatenation
        $responsiveBaseSize = intval($typography['base_size'] * 0.9);
        $responsiveSmallSize = intval($typography['small_size'] * 0.9);
        $responsiveLargeSize = intval($typography['large_size'] * 0.9);
        $responsiveH1Size = intval($typography['h1_size'] * 0.8);
        $responsiveH2Size = intval($typography['h2_size'] * 0.85);
        $responsiveH3Size = intval($typography['h3_size'] * 0.9);
        $responsiveH4Size = intval($typography['h4_size'] * 0.9);
        
        $css = "/* Auto-generated Theme: {$themeData['name']} */
/* Generated: " . date('Y-m-d H:i:s') . " */

:root {
    /* Color System */
    --color-primary: {$colors['primary']};
    --color-secondary: {$colors['secondary']};
    --color-accent: {$colors['accent']};
    --color-background: {$colors['background']};
    --color-surface: {$colors['surface']};
    --color-text: {$colors['text']};
    --color-text-secondary: {$colors['text_secondary']};
    --color-border: {$colors['border']};
    --color-success: {$colors['success']};
    --color-warning: {$colors['warning']};
    --color-danger: {$colors['danger']};
    --color-info: {$colors['info']};
    
    /* Gradients */
    --gradient-primary: {$gradientPrimary};
    --gradient-surface: {$gradientSurface};
    
    /* Typography */
    --font-family-primary: '{$typography['font_family']}', sans-serif;
    --font-family-secondary: '{$typography['font_family_secondary']}', sans-serif;
    --font-size-base: {$typography['base_size']}px;
    --font-size-small: {$typography['small_size']}px;
    --font-size-large: {$typography['large_size']}px;
    --font-size-h1: {$typography['h1_size']}px;
    --font-size-h2: {$typography['h2_size']}px;
    --font-size-h3: {$typography['h3_size']}px;
    --font-size-h4: {$typography['h4_size']}px;
    --font-weight-normal: {$typography['normal_weight']};
    --font-weight-bold: {$typography['bold_weight']};
    --line-height-base: {$typography['line_height']};
    
    /* Spacing */
    --spacing-xs: {$layout['spacing_xs']}px;
    --spacing-sm: {$layout['spacing_sm']}px;
    --spacing-md: {$layout['spacing_md']}px;
    --spacing-lg: {$layout['spacing_lg']}px;
    --spacing-xl: {$layout['spacing_xl']}px;
    
    /* Border Radius */
    --border-radius-sm: {$layout['border_radius_sm']}px;
    --border-radius-md: {$layout['border_radius_md']}px;
    --border-radius-lg: {$layout['border_radius_lg']}px;
    --border-radius-xl: {$layout['border_radius_xl']}px;
    
    /* Shadows */
    --shadow-sm: {$layout['shadow_sm']};
    --shadow-md: {$layout['shadow_md']};
    --shadow-lg: {$layout['shadow_lg']};
    --shadow-xl: {$layout['shadow_xl']};
    
    /* Transitions */
    --transition-fast: {$layout['transition_fast']};
    --transition-normal: {$layout['transition_normal']};
    --transition-slow: {$layout['transition_slow']};
}

/* Base Styles */
* {
    box-sizing: border-box;
}

body {
    font-family: var(--font-family-primary);
    font-size: var(--font-size-base);
    line-height: var(--line-height-base);
    color: var(--color-text);
    background: var(--color-background);
    margin: 0;
    padding: 0;
}

/* Typography */
h1, h2, h3, h4, h5, h6 {
    font-family: var(--font-family-primary);
    font-weight: var(--font-weight-bold);
    line-height: 1.2;
    margin-bottom: var(--spacing-md);
}

h1 { font-size: var(--font-size-h1); }
h2 { font-size: var(--font-size-h2); }
h3 { font-size: var(--font-size-h3); }
h4 { font-size: var(--font-size-h4); }

p {
    margin-bottom: var(--spacing-md);
}

a {
    color: var(--color-primary);
    text-decoration: none;
    transition: color var(--transition-fast);
}

a:hover {
    color: var(--color-secondary);
}

/* Layout Components */
.main-container {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
}

.navbar {
    background: var(--color-surface);
    border-bottom: 1px solid var(--color-border);
    padding: var(--spacing-sm) 0;
    box-shadow: var(--shadow-sm);
}

.sidebar {
    background: var(--color-surface);
    border-right: 1px solid var(--color-border);
    width: 250px;
    min-height: calc(100vh - 60px);
}

.content-area {
    flex: 1;
    padding: var(--spacing-lg);
    background: var(--color-background);
}

/* Card Components */
.card {
    background: var(--color-surface);
    border: 1px solid var(--color-border);
    border-radius: var(--border-radius-md);
    box-shadow: var(--shadow-sm);
    transition: all var(--transition-normal);
    margin-bottom: var(--spacing-md);
    overflow: hidden;
}

.card:hover {
    box-shadow: var(--shadow-md);
    transform: translateY(-2px);
}

.card-header {
    padding: var(--spacing-md);
    background: var(--gradient-surface);
    border-bottom: 1px solid var(--color-border);
    font-weight: var(--font-weight-bold);
}

.card-body {
    padding: var(--spacing-md);
}

.card-footer {
    padding: var(--spacing-md);
    background: var(--color-background);
    border-top: 1px solid var(--color-border);
}

/* Button Components */
.btn {
    display: inline-block;
    padding: var(--spacing-sm) var(--spacing-md);
    font-size: var(--font-size-base);
    font-weight: var(--font-weight-normal);
    text-align: center;
    text-decoration: none;
    border: 1px solid transparent;
    border-radius: var(--border-radius-sm);
    cursor: pointer;
    transition: all var(--transition-fast);
    user-select: none;
}

.btn-primary {
    background: var(--color-primary);
    color: white;
    border-color: var(--color-primary);
}

.btn-primary:hover {
    background: var(--color-secondary);
    border-color: var(--color-secondary);
    color: white;
}

.btn-secondary {
    background: var(--color-secondary);
    color: white;
    border-color: var(--color-secondary);
}

.btn-outline {
    background: transparent;
    color: var(--color-primary);
    border-color: var(--color-primary);
}

.btn-outline:hover {
    background: var(--color-primary);
    color: white;
}

/* Form Components */
.form-control {
    display: block;
    width: 100%;
    padding: var(--spacing-sm);
    font-size: var(--font-size-base);
    line-height: var(--line-height-base);
    color: var(--color-text);
    background: var(--color-surface);
    border: 1px solid var(--color-border);
    border-radius: var(--border-radius-sm);
    transition: all var(--transition-fast);
}

.form-control:focus {
    color: var(--color-text);
    background: var(--color-surface);
    border-color: var(--color-primary);
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.form-label {
    display: block;
    margin-bottom: var(--spacing-xs);
    font-weight: var(--font-weight-bold);
}

/* Calculator Specific */
.calculator-card {
    background: var(--color-surface);
    border: 1px solid var(--color-border);
    border-radius: var(--border-radius-lg);
    padding: var(--spacing-lg);
    margin-bottom: var(--spacing-lg);
    box-shadow: var(--shadow-md);
}

.calculator-form {
    background: var(--color-background);
    border: 1px solid var(--color-border);
    border-radius: var(--border-radius-md);
    padding: var(--spacing-md);
}

.calculator-result {
    background: var(--gradient-primary);
    color: white;
    border-radius: var(--border-radius-md);
    padding: var(--spacing-lg);
    margin-top: var(--spacing-md);
    text-align: center;
}

.calculator-result h3 {
    margin: 0 0 var(--spacing-sm) 0;
    font-size: var(--font-size-h2);
}

/* Utility Classes */
.text-primary { color: var(--color-primary) !important; }
.text-secondary { color: var(--color-secondary) !important; }
.text-success { color: var(--color-success) !important; }
.text-warning { color: var(--color-warning) !important; }
.text-danger { color: var(--color-danger) !important; }
.text-info { color: var(--color-info) !important; }
.text-muted { color: var(--color-text-secondary) !important; }

.bg-primary { background: var(--color-primary) !important; }
.bg-secondary { background: var(--color-secondary) !important; }
.bg-surface { background: var(--color-surface) !important; }
.bg-background { background: var(--color-background) !important; }

.rounded { border-radius: var(--border-radius-md) !important; }
.rounded-lg { border-radius: var(--border-radius-lg) !important; }

.shadow-sm { box-shadow: var(--shadow-sm) !important; }
.shadow-md { box-shadow: var(--shadow-md) !important; }
.shadow-lg { box-shadow: var(--shadow-lg) !important; }

/* Responsive Design */
@media (max-width: 768px) {
    :root {
        --font-size-base: ' . $responsiveBaseSize . 'px;
        --font-size-small: ' . $responsiveSmallSize . 'px;
        --font-size-large: ' . $responsiveLargeSize . 'px;
        --font-size-h1: ' . $responsiveH1Size . 'px;
        --font-size-h2: ' . $responsiveH2Size . 'px;
        --font-size-h3: ' . $responsiveH3Size . 'px;
        --font-size-h4: ' . $responsiveH4Size . 'px;
    }
    
    .sidebar {
        width: 100%;
        min-height: auto;
        border-right: none;
        border-bottom: 1px solid var(--color-border);
    }
    
    .content-area {
        padding: var(--spacing-md);
    }
    
    .card {
        margin-bottom: var(--spacing-md);
    }
}

/* Custom Component Styles */";

        // Add custom component styles
        foreach ($components as $component) {
            $css .= $this->generateComponentCSS($component);
        }
        
        // Add custom CSS overrides
        if (isset($themeData['custom_css']) && !empty($themeData['custom_css'])) {
            $css .= "\n\n/* Custom CSS */\n" . $themeData['custom_css'];
        }
        
        $css .= "\n";
        
        return $css;
    }

    /**
     * Generate component-specific CSS
     */
    private function generateComponentCSS($component)
    {
        $componentName = isset($component['name']) ? $component['name'] : 'custom';
        $styles = isset($component['styles']) ? $component['styles'] : [];
        
        $css = "\n\n/* {$componentName} Component */\n";
        $css .= ".component-{$componentName} {\n";
        
        foreach ($styles as $property => $value) {
            $css .= "    {$property}: {$value};\n";
        }
        
        $css .= "}\n";
        
        return $css;
    }

    /**
     * Generate theme layout file
     */
    private function generateThemeLayout($themeData)
    {
        $layout = $themeData['layout_config'];
        $colors = $themeData['color_palette'];
        
        // Handle nullable values
        $themeSlug = isset($themeData['slug']) ? $themeData['slug'] : 'custom';
        $themeName = $themeData['name'];
        $pageTitle = 'Bishwo Calculator';
        
        $layoutContent = "<?php
/**
 * Auto-generated Theme Layout: {$themeName}
 * Generated: " . date('Y-m-d H:i:s') . "
 */
?>
<!DOCTYPE html>
<html lang=\"en\" data-theme=\"{$themeSlug}\">
<head>
    <meta charset=\"UTF-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <title>{$pageTitle}</title>
    
    <!-- Theme CSS -->
    <link href=\"https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css\" rel=\"stylesheet\">
    <link href=\"https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css\" rel=\"stylesheet\">
    
    <!-- Google Fonts -->
    <link href=\"{$this->getGoogleFontsUrl($themeData['typography'])}\" rel=\"stylesheet\">
    
    <!-- Theme Custom CSS -->
    <link href=\"/themes/{$themeSlug}/assets/css/theme.css\" rel=\"stylesheet\">
    
    <!-- Page-specific CSS -->
    <?php if (isset(\$additionalCSS)): ?>
        <?php foreach (\$additionalCSS as \$css): ?>
            <link href=\"<?= htmlspecialchars(\$css) ?>\" rel=\"stylesheet\">
        <?php endforeach; ?>
    <?php endif; ?>
    
    <style>
        :root {
            --bs-primary: {$colors['primary']};
            --bs-secondary: {$colors['secondary']};
            --bs-success: {$colors['success']};
            --bs-info: {$colors['info']};
            --bs-warning: {$colors['warning']};
            --bs-danger: {$colors['danger']};
            --bs-light: {$colors['surface']};
            --bs-dark: {$colors['text']};
        }
        
        body {
            font-family: '{$themeData['typography']['font_family']}', sans-serif;
            background: {$colors['background']};
            color: {$colors['text']};
        }
        
        .navbar {
            background: {$colors['surface']} !important;
            border-bottom: 1px solid {$colors['border']};
        }
        
        .sidebar {
            background: {$colors['surface']};
            border-right: 1px solid {$colors['border']};
        }
        
        .card {
            background: {$colors['surface']};
            border: 1px solid {$colors['border']};
            color: {$colors['text']};
        }
        
        .btn-primary {
            background: {$colors['primary']};
            border-color: {$colors['primary']};
        }
        
        .btn-primary:hover {
            background: {$colors['secondary']};
            border-color: {$colors['secondary']};
        }
        
        .content-area {
            background: {$colors['background']};
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class=\"navbar navbar-expand-lg navbar-dark\">
        <div class=\"container-fluid\">
            <a class=\"navbar-brand\" href=\"/\">
                <i class=\"fas fa-calculator me-2\"></i>
                Bishwo Calculator
            </a>
            
            <button class=\"navbar-toggler\" type=\"button\" data-bs-toggle=\"collapse\" data-bs-target=\"#navbarNav\">
                <span class=\"navbar-toggler-icon\"></span>
            </button>
            
            <div class=\"collapse navbar-collapse\" id=\"navbarNav\">
                <ul class=\"navbar-nav me-auto\">
                    <li class=\"nav-item\">
                        <a class=\"nav-link\" href=\"/calculators\">
                            <i class=\"fas fa-calculator me-1\"></i>Calculators
                        </a>
                    </li>
                    <?php if (isset(\$_SESSION['user']) && \$_SESSION['user']['role'] === 'admin'): ?>
                        <li class=\"nav-item dropdown\">
                            <a class=\"nav-link dropdown-toggle\" href=\"#\" role=\"button\" data-bs-toggle=\"dropdown\">
                                <i class=\"fas fa-cog me-1\"></i>Admin
                            </a>
                            <ul class=\"dropdown-menu\">
                                <li><a class=\"dropdown-item\" href=\"/admin\"><i class=\"fas fa-tachometer-alt me-2\"></i>Dashboard</a></li>
                                <li><a class=\"dropdown-item\" href=\"/admin/plugins\"><i class=\"fas fa-puzzle-piece me-2\"></i>Plugins</a></li>
                                <li><a class=\"dropdown-item\" href=\"/admin/themes\"><i class=\"fas fa-palette me-2\"></i>Themes</a></li>
                                <li><a class=\"dropdown-item\" href=\"/admin/theme-editor\"><i class=\"fas fa-edit me-2\"></i>Theme Editor</a></li>
                            </ul>
                        </li>
                    <?php endif; ?>
                </ul>
                
                <ul class=\"navbar-nav\">
                    <?php if (isset(\$_SESSION['user'])): ?>
                        <li class=\"nav-item dropdown\">
                            <a class=\"nav-link dropdown-toggle\" href=\"#\" role=\"button\" data-bs-toggle=\"dropdown\">
                                <i class=\"fas fa-user me-1\"></i><?= htmlspecialchars(\$_SESSION['user']['name']) ?>
                            </a>
                            <ul class=\"dropdown-menu\">
                                <li><a class=\"dropdown-item\" href=\"/profile\"><i class=\"fas fa-user me-2\"></i>Profile</a></li>
                                <li><hr class=\"dropdown-divider\"></li>
                                <li>
                                    <form method=\"POST\" action=\"/logout\" class=\"d-inline\">
                                        <button type=\"submit\" class=\"dropdown-item\">
                                            <i class=\"fas fa-sign-out-alt me-2\"></i>Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class=\"nav-item\">
                            <a class=\"nav-link\" href=\"/login\">
                                <i class=\"fas fa-sign-in-alt me-1\"></i>Login
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class=\"container-fluid\">
        <div class=\"row\">
            <?php if (isset(\$_SESSION['user'])): ?>
                <!-- Sidebar -->
                <div class=\"col-md-3 col-lg-2 sidebar d-md-block\">
                    <div class=\"p-3\">
                        <h6 class=\"text-muted mb-3\">Navigation</h6>
                        <ul class=\"nav flex-column\">
                            <li class=\"nav-item\">
                                <a class=\"nav-link\" href=\"/\">
                                    <i class=\"fas fa-tachometer-alt me-2\"></i>Dashboard
                                </a>
                            </li>
                            <li class=\"nav-item\">
                                <a class=\"nav-link\" href=\"/calculators\">
                                    <i class=\"fas fa-calculator me-2\"></i>Calculators
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <!-- Main Content -->
                <div class=\"col-md-9 col-lg-10\">
                    <div class=\"content-area\">
            <?php else: ?>
                <div class=\"col-12\">
                    <div class=\"content-area\">
            <?php endif; ?>
                        
                        <!-- Page Content -->
                        <?= \$content ?>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class=\"bg-light mt-5 py-4\">
        <div class=\"container-fluid\">
            <div class=\"row\">
                <div class=\"col-md-6\">
                    <p class=\"mb-0 text-muted\">
                        &copy; <?= date('Y') ?> Bishwo Calculator. 
                        <span class=\"ms-2\">Theme: {$themeName}</span>
                    </p>
                </div>
                <div class=\"col-md-6 text-end\">
                    <p class=\"mb-0 text-muted\">
                        Built with <i class=\"fas fa-heart text-danger\"></i> for Engineers
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src=\"https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js\"></script>
    
    <!-- Theme JavaScript -->
    <script src=\"/themes/{$themeSlug}/assets/js/theme.js\"></script>
    
    <!-- Page-specific JavaScript -->
    <?php if (isset(\$additionalJS)): ?>
        <?php foreach (\$additionalJS as \$js): ?>
            <script src=\"<?= htmlspecialchars(\$js) ?>\"></script>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <!-- Inline JavaScript -->
    <?php if (isset(\$inlineJS)): ?>
        <script>
            <?= \$inlineJS ?>
        </script>
    <?php endif; ?>
</body>
</html>";
        
        return $layoutContent;
    }

    /**
     * Generate theme configuration JSON
     */
    private function generateThemeConfig($themeData)
    {
        $description = isset($themeData['description']) ? $themeData['description'] : 'Custom theme created with Theme Editor';
        $category = isset($themeData['category']) ? $themeData['category'] : 'custom';
        $features = isset($themeData['features']) ? $themeData['features'] : [];
        
        $secondaryFont = isset($themeData['typography']['font_family_secondary']) 
            ? $themeData['typography']['font_family_secondary'] 
            : $themeData['typography']['font_family'];
        
        $config = [
            'name' => $themeData['name'],
            'slug' => $themeData['slug'],
            'version' => '1.0.0',
            'author' => 'Bishwo Calculator',
            'description' => $description,
            'type' => 'custom',
            'category' => $category,
            'colors' => $themeData['color_palette'],
            'fonts' => [
                'primary' => $themeData['typography']['font_family'],
                'secondary' => $secondaryFont
            ],
            'settings' => $themeData['layout_config'],
            'features' => $features,
            'compatibility' => [
                'php_version' => '>=7.4',
                'calculator_modules' => 'all'
            ],
            'created_at' => date('Y-m-d\TH:i:s\Z'),
            'updated_at' => date('Y-m-d\TH:i:s\Z'),
            'generated_by' => 'Theme Editor',
            'generator_version' => '1.0.0'
        ];
        
        return json_encode($config, JSON_PRETTY_PRINT);
    }

    /**
     * Generate theme JavaScript
     */
    private function generateThemeJS($themeData)
    {
        $js = "/**
 * Theme JavaScript: {$themeData['name']}
 * Generated: " . date('Y-m-d H:i:s') . "
 */

(function() {
    'use strict';
    
    // Theme configuration
    const themeConfig = " . json_encode($themeData, JSON_PRETTY_PRINT) . ";
    
    // Initialize theme functionality
    document.addEventListener('DOMContentLoaded', function() {
        initializeTheme();
        setupInteractions();
        setupAnimations();
    });
    
    function initializeTheme() {
        // Set theme CSS variables
        setCSSVariables();
        
        // Initialize components
        initializeComponents();
    }
    
    function setCSSVariables() {
        const root = document.documentElement;
        const colors = themeConfig.color_palette;
        const typography = themeConfig.typography;
        const layout = themeConfig.layout_config;
        
        // Set color variables
        Object.keys(colors).forEach(key => {
            root.style.setProperty(`--color-${key}`, colors[key]);
        });
        
        // Set typography variables
        root.style.setProperty('--font-size-base', typography.base_size + 'px');
        root.style.setProperty('--font-family-primary', `'${typography.font_family}', sans-serif`);
        
        // Set layout variables
        root.style.setProperty('--border-radius-md', layout.border_radius_md + 'px');
        root.style.setProperty('--spacing-md', layout.spacing_md + 'px');
    }
    
    function setupInteractions() {
        // Button hover effects
        const buttons = document.querySelectorAll('.btn');
        buttons.forEach(button => {
            button.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-1px)';
            });
            
            button.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
        
        // Card hover effects
        const cards = document.querySelectorAll('.card');
        cards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-2px)';
                this.style.boxShadow = 'var(--shadow-md)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
                this.style.boxShadow = 'var(--shadow-sm)';
            });
        });
    }
    
    function setupAnimations() {
        // Fade in animation for cards
        const cards = document.querySelectorAll('.card');
        cards.forEach((card, index) => {
            setTimeout(() => {
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, index * 100);
        });
        
        // Loading states
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            form.addEventListener('submit', function() {
                const submitBtn = this.querySelector('button[type=\"submit\"]');
                if (submitBtn) {
                    submitBtn.innerHTML = '<i class=\"fas fa-spinner fa-spin me-1\"></i>Loading...';
                    submitBtn.disabled = true;
                }
            });
        });
    }
    
    function initializeComponents() {
        // Initialize tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle=\"tooltip\"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
        
        // Initialize modals
        const modals = document.querySelectorAll('.modal');
        modals.forEach(modal => {
            new bootstrap.Modal(modal);
        });
    }
    
    // Theme utilities
    window.ThemeUtils = {
        config: themeConfig,
        
        // Update theme dynamically
        updateTheme: function(newConfig) {
            Object.assign(themeConfig, newConfig);
            setCSSVariables();
        },
        
        // Get theme color
        getColor: function(colorName) {
            return themeConfig.color_palette[colorName];
        },
        
        // Get typography setting
        getTypography: function(setting) {
            return themeConfig.typography[setting];
        }
    };
    
})();";
        
        return $js;
    }

    /**
     * Generate preview HTML for live editing
     */
    private function generatePreviewHTML($themeData)
    {
        $html = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Theme Preview</title>
    <style>
        /* Generated CSS will be injected here */
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <h1>Theme Preview: ' . htmlspecialchars($themeData['name']) . '</h1>
                <p>This is a preview of how your theme will look with various UI components.</p>
                
                <!-- Sample Cards -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5>Sample Card</h5>
                            </div>
                            <div class="card-body">
                                <p>This is a sample card component showing your theme\'s styling.</p>
                                <button class="btn btn-primary">Primary Button</button>
                                <button class="btn btn-outline">Outline Button</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5>Calculator Preview</h5>
                            </div>
                            <div class="card-body">
                                <div class="calculator-form">
                                    <div class="mb-3">
                                        <label class="form-label">Input Field</label>
                                        <input type="text" class="form-control" placeholder="Sample input">
                                    </div>
                                    <button class="btn btn-primary">Calculate</button>
                                </div>
                                <div class="calculator-result">
                                    <h3>Result: 123.45</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Typography Sample -->
                <div class="card">
                    <div class="card-header">
                        <h5>Typography Sample</h5>
                    </div>
                    <div class="card-body">
                        <h1>Heading 1</h1>
                        <h2>Heading 2</h2>
                        <h3>Heading 3</h3>
                        <h4>Heading 4</h4>
                        <p>This is a paragraph showing how your theme\'s typography will look. Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                        <p class="text-muted">This is muted text.</p>
                        <p class="text-primary">This is primary colored text.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>';
        
        return $html;
    }

    /**
     * Generate theme assets
     */
    private function generateThemeAssets($themeData)
    {
        $secondaryFont = isset($themeData['typography']['font_family_secondary']) 
            ? $themeData['typography']['font_family_secondary'] 
            : $themeData['typography']['font_family'];
            
        $assets = [
            'css_files' => [
                '/themes/' . $themeData['slug'] . '/assets/css/theme.css'
            ],
            'js_files' => [
                '/themes/' . $themeData['slug'] . '/assets/js/theme.js'
            ],
            'font_families' => [
                $themeData['typography']['font_family'],
                $secondaryFont
            ],
            'google_fonts_url' => $this->getGoogleFontsUrl($themeData['typography'])
        ];
        
        return $assets;
    }

    /**
     * Get Google Fonts URL from typography settings
     */
    private function getGoogleFontsUrl($typography)
    {
        $primaryFont = str_replace(' ', '+', $typography['font_family']);
        $secondaryFont = isset($typography['font_family_secondary']) ? $typography['font_family_secondary'] : null;
        
        $url = "https://fonts.googleapis.com/css2?family={$primaryFont}";
        
        if ($secondaryFont && $secondaryFont !== $primaryFont) {
            $secondaryFont = str_replace(' ', '+', $secondaryFont);
            $url .= "&family={$secondaryFont}";
        }
        
        return $url;
    }

    /**
     * Generate slug from theme name
     */
    private function generateSlug($name)
    {
        $slug = strtolower(trim($name));
        $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        $slug = trim($slug, '-');
        
        // Ensure uniqueness
        $originalSlug = $slug;
        $counter = 1;
        
        while ($this->themeSlugExists($slug)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }

    /**
     * Check if theme slug exists
     */
    private function themeSlugExists($slug)
    {
        $sql = "SELECT id FROM theme_templates WHERE slug = ?";
        $result = $this->db->query($sql, [$slug]);
        return !empty($result);
    }

    /**
     * Validate theme data
     */
    private function validateThemeData($themeData)
    {
        $requiredFields = ['name', 'color_palette', 'typography', 'layout_config'];
        
        foreach ($requiredFields as $field) {
            if (empty($themeData[$field])) {
                throw new \Exception("Required field missing: {$field}");
            }
        }
        
        // Validate color palette
        $requiredColors = ['primary', 'secondary', 'background', 'surface', 'text'];
        foreach ($requiredColors as $color) {
            if (empty($themeData['color_palette'][$color])) {
                throw new \Exception("Required color missing: {$color}");
            }
        }
        
        // Validate typography
        $requiredTypography = ['font_family', 'base_size'];
        foreach ($requiredTypography as $typography) {
            if (empty($themeData['typography'][$typography])) {
                throw new \Exception("Required typography setting missing: {$typography}");
            }
        }
    }

    /**
     * Save theme to database
     */
    private function saveThemeToDatabase($themeData)
    {
        $sql = "INSERT INTO theme_templates (
            name, slug, category, description, layout_config, color_palette, 
            typography, components, custom_css, custom_js, assets, is_active, 
            is_custom, created_by, updated_by
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $category = isset($themeData['category']) ? $themeData['category'] : 'custom';
        $description = isset($themeData['description']) ? $themeData['description'] : null;
        $components = isset($themeData['components']) ? json_encode($themeData['components']) : json_encode([]);
        $customCSS = isset($themeData['custom_css']) ? json_encode($themeData['custom_css']) : null;
        $customJS = isset($themeData['custom_js']) ? json_encode($themeData['custom_js']) : null;
        $assets = isset($themeData['assets']) ? json_encode($themeData['assets']) : json_encode([]);
        $isActive = isset($themeData['is_active']) ? $themeData['is_active'] : false;
        $isCustom = isset($themeData['is_custom']) ? $themeData['is_custom'] : true;
        
        $params = [
            $themeData['name'],
            $themeData['slug'],
            $category,
            $description,
            json_encode($themeData['layout_config']),
            json_encode($themeData['color_palette']),
            json_encode($themeData['typography']),
            $components,
            $customCSS,
            $customJS,
            $assets,
            $isActive,
            $isCustom,
            $themeData['created_by'],
            $themeData['updated_by']
        ];
        
        return $this->db->insert($sql, $params);
    }

    /**
     * Update theme in database
     */
    private function updateThemeInDatabase($themeId, $themeData)
    {
        $sql = "UPDATE theme_templates SET 
            name = ?, category = ?, description = ?, layout_config = ?, 
            color_palette = ?, typography = ?, components = ?, 
            custom_css = ?, custom_js = ?, assets = ?, updated_by = ?
            WHERE id = ?";
        
        $category = isset($themeData['category']) ? $themeData['category'] : 'custom';
        $description = isset($themeData['description']) ? $themeData['description'] : null;
        $components = isset($themeData['components']) ? json_encode($themeData['components']) : json_encode([]);
        $customCSS = isset($themeData['custom_css']) ? json_encode($themeData['custom_css']) : null;
        $customJS = isset($themeData['custom_js']) ? json_encode($themeData['custom_js']) : null;
        $assets = isset($themeData['assets']) ? json_encode($themeData['assets']) : json_encode([]);
        
        $params = [
            $themeData['name'],
            $category,
            $description,
            json_encode($themeData['layout_config']),
            json_encode($themeData['color_palette']),
            json_encode($themeData['typography']),
            $components,
            $customCSS,
            $customJS,
            $assets,
            $themeData['updated_by'],
            $themeId
        ];
        
        return $this->db->update($sql, $params);
    }

    /**
     * Get theme by ID
     */
    private function getThemeById($themeId)
    {
        $sql = "SELECT * FROM theme_templates WHERE id = ?";
        $result = $this->db->query($sql, [$themeId]);
        return isset($result[0]) ? $result[0] : null;
    }

    /**
     * Create version entry for theme changes
     */
    private function createVersionEntry($themeId, $oldData, $newData, $userId)
    {
        $changes = $this->calculateChanges($oldData, $newData);
        $version = $this->getNextVersion($themeId);
        
        $sql = "INSERT INTO theme_versions (theme_id, version, changes, changelog, created_by) VALUES (?, ?, ?, ?, ?)";
        $params = [
            $themeId,
            $version,
            json_encode($changes),
            $this->generateChangelog($changes),
            $userId
        ];
        
        return $this->db->insert($sql, $params);
    }

    /**
     * Calculate changes between old and new data
     */
    private function calculateChanges($oldData, $newData)
    {
        $changes = [];
        
        foreach ($newData as $key => $value) {
            if (isset($oldData[$key]) && $oldData[$key] !== $value) {
                $changes[$key] = [
                    'old' => $oldData[$key],
                    'new' => $value
                ];
            }
        }
        
        return $changes;
    }

    /**
     * Generate changelog text
     */
    private function generateChangelog($changes)
    {
        $changelog = [];
        
        foreach ($changes as $key => $change) {
            $changelog[] = "Updated {$key}";
        }
        
        return implode(', ', $changelog);
    }

    /**
     * Get next version number for theme
     */
    private function getNextVersion($themeId)
    {
        $sql = "SELECT version FROM theme_versions WHERE theme_id = ? ORDER BY created_at DESC LIMIT 1";
        $result = $this->db->query($sql, [$themeId]);
        
        if (empty($result)) {
            return '1.0.0';
        }
        
        $currentVersion = $result[0]['version'];
        $parts = explode('.', $currentVersion);
        
        // Increment patch version
        $parts[2] = intval($parts[2]) + 1;
        
        return implode('.', $parts);
    }

    /**
     * Log action for audit trail
     */
    private function logAction($userId, $action, $entityType, $entityId, $newData, $oldData = null)
    {
        $sql = "INSERT INTO editor_audit_log (user_id, action, entity_type, entity_id, changes, old_data, new_data, ip_address, user_agent) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $oldDataForChanges = $oldData !== null ? $oldData : [];
        $ipAddress = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null;
        $userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : null;
        
        $params = [
            $userId,
            $action,
            $entityType,
            $entityId,
            json_encode($this->calculateChanges($oldDataForChanges, $newData)),
            $oldData !== null ? json_encode($oldData) : null,
            json_encode($newData),
            $ipAddress,
            $userAgent
        ];
        
        return $this->db->insert($sql, $params);
    }

    /**
     * Generate custom components
     */
    private function generateCustomComponents($themePath, $components)
    {
        foreach ($components as $component) {
            $componentName = isset($component['name']) ? $component['name'] : 'custom';
            $componentPath = $themePath . '/views/components/' . $componentName . '.php';
            
            $componentContent = "<?php
/**
 * Custom Component: {$componentName}
 * Generated: " . date('Y-m-d H:i:s') . "
 */

\$componentData = \$componentData ?? [];
?>
<div class=\"component-{$componentName}\">
    <!-- Component content will be generated here -->
    <p>Custom component: {$componentName}</p>
</div>";
            
            file_put_contents($componentPath, $componentContent);
        }
    }
}
?>
