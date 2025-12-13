<?php
require_once __DIR__ . "/../Config/config.php";

// Initialize secure session
// Initialize secure session
function init_secure_session()
{
    \App\Services\Security::startSession();
}

// CSRF helpers
function csrf_token()
{
    return \App\Services\Security::generateCsrfToken();
}

function verify_csrf($token)
{
    return \App\Services\Security::validateCsrfToken($token);
}

// HTML Entity Helper
function e($string)
{
    return \App\Services\Security::sanitize($string);
}

// Admin session helper
function require_admin_login()
{
    if (empty($_SESSION["admin_logged_in"])) {
        header(
            "Location: " .
                (defined("APP_BASE") ? APP_BASE : "") .
                "/admin/login.php",
        );
        exit();
    }
}

// Common calculation functions for the app. Keep formulas simple and deterministic.
/**
 * Concrete volume (m³)
 */
function concrete_volume(float $length, float $width, float $depth): float
{
    return $length * $width * $depth;
}
/**
 * Earthwork volumes (cut, fill, net) given areas (m²) and length (m)
 * Returns associative array with keys 'cut', 'fill', 'net'
 */
function earthwork_volumes(
    float $cutArea,
    float $fillArea,
    float $length,
): array {
    $cut = $cutArea * $length;
    $fill = $fillArea * $length;
    return ["cut" => $cut, "fill" => $fill, "net" => $cut - $fill];
}

/**
 * Beam load helper - returns moment (kN·m), section_modulus (m³), stress (kPa)
 */
function beam_load(
    float $length,
    float $width_m,
    float $depth_m,
    float $load,
): array {
    $moment = ($load * pow($length, 2)) / 8.0;
    $section_modulus = ($width_m * pow($depth_m, 2)) / 6.0;
    $stress = $section_modulus > 0 ? $moment / $section_modulus : 0;
    return [
        "moment" => $moment,
        "section_modulus" => $section_modulus,
        "stress" => $stress,
    ];
}

/**
 * Unit conversion helper (simple set)
 */
function convert_units(float $value, string $from, string $to): float
{
    $toMeter = [
        "meter" => 1.0,
        "foot" => 0.3048,
        "inch" => 0.0254,
        "cm" => 0.01,
    ];
    if (!isset($toMeter[$from]) || !isset($toMeter[$to])) {
        return 0.0;
    }
    $valueInMeters = $value * $toMeter[$from];
    return $valueInMeters / $toMeter[$to];
}

// Simple helper: redirect back to app base
function app_base_url($path = "")
{
    $base = defined("APP_BASE") ? rtrim(APP_BASE, "/") : "";
    return $base . ($path ? "/" . ltrim($path, "/") : "");
}

/**
 * Load site metadata from database settings and merge with sensible defaults.
 * Returns associative array with keys: title, description, keywords, logo, favicon, admin_name, admin_email
 */
function get_site_meta(): array
{
    $defaults = [
        "title" => "AEC Engineering Calculator",
        "description" =>
            "Professional engineering calculators for civil, electrical, HVAC, plumbing and fire protection.",
        "keywords" =>
            "engineering, calculator, civil, electrical, HVAC, plumbing, fire protection, AEC",
        "logo" => app_base_url("themes/default/assets/images/logo.png"),
        "favicon" => app_base_url("themes/default/assets/images/favicon.png"),
        "admin_name" => defined("ADMIN_USER") ? ADMIN_USER : "",
        "admin_email" => defined("MAIL_TO") ? MAIL_TO : "admin@example.com",
        "canonical" => null,
    ];

    try {
        // Try to get settings from database using SettingsService
        $site_name = \App\Services\SettingsService::get('site_name');
        $site_description = \App\Services\SettingsService::get('site_description');
        $site_logo = \App\Services\SettingsService::get('site_logo');
        $favicon = \App\Services\SettingsService::get('favicon');
        $header_style = \App\Services\SettingsService::get('header_style');

        // Build site meta from database settings
        $site_meta = [];
        
        if ($site_name) {
            $site_meta['title'] = $site_name;
        }
        
        if ($site_description) {
            $site_meta['description'] = $site_description;
        }
        
        if ($site_logo) {
            $site_meta['logo'] = app_base_url($site_logo);
        }
        
        if ($favicon) {
            $site_meta['favicon'] = app_base_url($favicon);
        }
        
        if ($header_style) {
            $site_meta['header_style'] = $header_style;
        }

        return array_merge($defaults, $site_meta);
    } catch (\Exception $e) {
        // Fallback to defaults if database access fails
        error_log("get_site_meta() database error: " . $e->getMessage());
        return $defaults;
    }
}

function get_site_settings(): array
{
    $defaults = [
        "use_banner_image" => true,
    ];

    $settingsFile = __DIR__ . "/../db/site_settings.json";
    if (file_exists($settingsFile)) {
        $raw = file_get_contents($settingsFile);
        $data = json_decode($raw, true);
        if (is_array($data)) {
            return array_merge($defaults, $data);
        }
    }
    return $defaults;
}

function save_site_settings(array $settings): bool
{
    $settingsFile = __DIR__ . "/../db/site_settings.json";
    $json = json_encode($settings, JSON_PRETTY_PRINT);
    return file_put_contents($settingsFile, $json) !== false;
}

function generate_breadcrumb($items)
{
    if (empty($items)) {
        return "";
    }

    $html = '<nav aria-label="breadcrumb"><ol class="breadcrumb">';
    foreach ($items as $i => $item) {
        if ($i === 0 && $item["name"] === "Home") {
            $html .=
                '<li class="breadcrumb-item"><a href="' .
                htmlspecialchars($item["url"]) .
                '"><i class="fas fa-home"></i></a></li>';
        } else {
            if ($i === count($items) - 1) {
                $html .=
                    '<li class="breadcrumb-item active" aria-current="page">' .
                    htmlspecialchars($item["name"]) .
                    "</li>";
            } else {
                $html .=
                    '<li class="breadcrumb-item"><a href="' .
                    htmlspecialchars($item["url"]) .
                    '">' .
                    htmlspecialchars($item["name"]) .
                    "</a></li>";
            }
        }
    }
    $html .= "</ol></nav>";
    return $html;
}

/**
 * Generate asset URL with proper base path
 */
function asset_url(string $path = ""): string
{
    $base = defined("APP_BASE") ? rtrim(APP_BASE, "/") : "";
    $path = ltrim($path, "/");
    return $base . "/assets/" . $path;
}

/**
 * Check if user is logged in
 */
function is_logged_in(): bool
{
    return isset($_SESSION["user_id"]) && !empty($_SESSION["user_id"]);
}

/**
 * Get current logged-in user data
 */
function current_user(): ?array
{
    if (!is_logged_in()) {
        return null;
    }

    // Return user data from session
    return [
        "id" => $_SESSION["user_id"] ?? null,
        "username" => $_SESSION["username"] ?? null,
        "email" => $_SESSION["email"] ?? null,
        "role" => $_SESSION["role"] ?? "user",
        "is_admin" => ($_SESSION["role"] ?? "") === "admin",
    ];
}

/**
 * Redirect to a URL
 */
function redirect(string $url, int $statusCode = 302): void
{
    // If URL doesn't start with http, assume it's a relative path
    if (!preg_match("#^https?://#i", $url)) {
        $url = app_base_url($url);
    }

    if (!headers_sent()) {
        header("Location: {$url}", true, $statusCode);
        exit();
    }

    // Fallback if headers already sent
    echo "<script>window.location.href='" .
        htmlspecialchars($url, ENT_QUOTES) .
        "';</script>";
    echo '<noscript><meta http-equiv="refresh" content="0;url=' .
        htmlspecialchars($url, ENT_QUOTES) .
        '"></noscript>';
    exit();
}

/**
 * Get old input value (for form repopulation after validation errors)
 */
function old(string $key, $default = "")
{
    return $_SESSION["_old_input"][$key] ?? $default;
}

/**
 * Set a flash message
 */
function flash(string $key, $value): void
{
    if (!isset($_SESSION["_flash"])) {
        $_SESSION["_flash"] = [];
    }
    $_SESSION["_flash"][$key] = $value;
}

/**
 * Get and clear a flash message
 */
function get_flash(string $key, $default = null)
{
    $value = $_SESSION["_flash"][$key] ?? $default;

    // Clear the flash message after retrieving
    if (isset($_SESSION["_flash"][$key])) {
        unset($_SESSION["_flash"][$key]);
    }

    return $value;
}

/**
 * Clear all flash messages
 */
function clear_flash(): void
{
    if (isset($_SESSION["_flash"])) {
        unset($_SESSION["_flash"]);
    }
}

/**
 * Store old input for form repopulation
 */
function store_old_input(array $data): void
{
    $_SESSION["_old_input"] = $data;
}

/**
 * Clear old input data
 */
function clear_old_input(): void
{
    if (isset($_SESSION["_old_input"])) {
        unset($_SESSION["_old_input"]);
    }
}

/**
 * Sanitize text field for security
 */
function sanitize_text_field($value)
{
    if (is_array($value)) {
        return array_map('sanitize_text_field', $value);
    }
    
    if (is_string($value)) {
        // Remove HTML tags and encode special characters
        $value = strip_tags($value);
        $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        // Trim whitespace
        $value = trim($value);
    }
    
    return $value;
}

if (!function_exists('csrf_field')) {
    function csrf_field()
    {
        return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
    }
}
?>