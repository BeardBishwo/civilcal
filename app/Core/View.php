<?php
namespace App\Core;

class View
{
    private $themeManager;
    private $basePath;

    public function __construct()
    {
        $this->themeManager = new \App\Services\ThemeManager();
        $this->basePath = $this->getBasePath();
    }

    /**
     * Get base path for URLs (handles subdirectory installations)
     */
    private function getBasePath()
    {
        $scriptName = $_SERVER["SCRIPT_NAME"] ?? "";
        $scriptDir = dirname($scriptName);

        // Remove /public from path
        if (substr($scriptDir, -7) === "/public") {
            $scriptDir = substr($scriptDir, 0, -7);
        }

        if ($scriptDir === "/" || $scriptDir === "") {
            return "";
        }

        return $scriptDir;
    }

    /**
     * Generate URL with base path
     */
    public function url($path = "")
    {
        $path = ltrim($path, "/");
        return $this->basePath . "/" . $path;
    }

    public function render($view, $data = [])
    {
        extract($data);
        $title = isset($title) ? $title : "Bishwo Calculator";
        ob_start();
        $viewPath = $this->themesPath() . $view . ".php";
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            $this->themeManager->renderView($view, $data);
            $altPath =
                BASE_PATH .
                "/app/Views/" .
                str_replace(".", "/", $view) .
                ".php";
            if (!file_exists($altPath)) {
                $altPath = BASE_PATH . "/app/Views/" . $view . ".php";
            }
            if (file_exists($altPath)) {
                include $altPath;
            }
        }
        $content = ob_get_clean();

        // Check if content is already a complete HTML document
        // If it contains DOCTYPE and both opening html/body tags, skip layout
        $isCompleteDocument =
            stripos($content, "<!DOCTYPE") !== false &&
            stripos($content, "<html") !== false &&
            stripos($content, "<body") !== false;

        if ($isCompleteDocument) {
            // View is already a complete HTML document, output as-is
            echo $content;
            return;
        }

        // Otherwise, wrap in layout
        $layoutPath = $this->themesPath() . "layouts/main.php";
        if (!file_exists($layoutPath)) {
            $layoutPath = BASE_PATH . "/app/Views/layouts/main.php";
            if (strpos($view, "admin/") === 0) {
                $layoutPath = BASE_PATH . "/app/Views/layouts/admin.php";
            } elseif (strpos($view, "auth/") === 0) {
                $layoutPath = BASE_PATH . "/app/Views/layouts/auth.php";
                // Check for theme-specific landing layout
                $themeLandingLayout =
                    $this->themesPath() . "layouts/landing.php";
                if (
                    strpos($view, "landing/") === 0 &&
                    file_exists($themeLandingLayout)
                ) {
                    $layoutPath = $themeLandingLayout;
                }
            } elseif (strpos($view, "landing/") === 0) {
                // Check for landing layout in theme first
                $themeLandingLayout =
                    $this->themesPath() . "layouts/landing.php";
                if (file_exists($themeLandingLayout)) {
                    $layoutPath = $themeLandingLayout;
                }
            }
        }
        if (file_exists($layoutPath)) {
            $data["content"] = $content;
            extract($data);
            ob_start();
            include $layoutPath;
            $finalOutput = ob_get_clean();
            echo $finalOutput;
        } else {
            echo $content;
        }
    }

    /**
     * Get themed asset URL
     */
    public function asset($assetPath)
    {
        return $this->themeManager->getThemeAsset($assetPath);
    }

    /**
     * Get theme assets URL
     */
    public function assetsUrl($path = "")
    {
        return $this->themeManager->assetsUrl($path);
    }

    /**
     * Get theme URL
     */
    public function themeUrl($path = "")
    {
        return $this->themeManager->themeUrl($path);
    }

    /**
     * Render a partial view
     */
    public function partial($partial, $data = [])
    {
        $this->themeManager->renderPartial($partial, $data);
    }

    /**
     * Load theme styles
     */
    public function loadStyles()
    {
        $this->themeManager->loadThemeStyles();
    }

    /**
     * Load theme scripts
     */
    public function loadScripts()
    {
        $this->themeManager->loadThemeScripts();
    }

    /**
     * Load category specific style
     */
    public function loadCategoryStyle($category)
    {
        $this->themeManager->loadCategoryStyle($category);
    }

    /**
     * Get theme metadata
     */
    public function getThemeMetadata()
    {
        return $this->themeManager->getThemeMetadata();
    }

    /**
     * Get current theme config
     */
    public function getThemeConfig()
    {
        return $this->themeManager->getThemeConfig();
    }

    /**
     * Get active theme name
     */
    public function getActiveTheme()
    {
        return $this->themeManager->getActiveTheme();
    }

    /**
     * Get available themes
     */
    public function getAvailableThemes()
    {
        return $this->themeManager->getAvailableThemes();
    }

    public function csrfToken()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (empty($_SESSION["csrf_token"])) {
            $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
            $_SESSION["csrf_expiry"] = time() + 3600;
        }
        return $_SESSION["csrf_token"];
    }

    public function csrfField()
    {
        $token = $this->csrfToken();
        echo '<input type="hidden" name="csrf_token" value="' .
            htmlspecialchars($token, ENT_QUOTES, "UTF-8") .
            '">';
    }

    public function csrfMetaTag()
    {
        $token = $this->csrfToken();
        echo '<meta name="csrf-token" content="' .
            htmlspecialchars($token, ENT_QUOTES, "UTF-8") .
            '">';
    }

    /**
     * Set active theme
     */
    public function setTheme($themeName)
    {
        return $this->themeManager->setTheme($themeName);
    }

    /**
     * Get themes path
     */
    private function themesPath()
    {
        return BASE_PATH .
            "/themes/" .
            $this->themeManager->getActiveTheme() .
            "/views/";
    }

    /**
     * Render JSON response
     */
    public function json($data, $status = 200)
    {
        http_response_code($status);
        header("Content-Type: application/json");
        echo json_encode($data);
        exit();
    }

    /**
     * Render plain text
     */
    public function plain($text, $status = 200)
    {
        http_response_code($status);
        header("Content-Type: text/plain");
        echo $text;
        exit();
    }
}
?>
