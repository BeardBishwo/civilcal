<?php
/**
 * Database migration script for enhanced permalink settings
 * Adds new permalink structure options and settings
 */

// Bootstrap the application
require_once __DIR__ . '/../app/bootstrap.php';

use App\Core\Database;

// Get database connection
$db = Database::getInstance()->getPdo();

try {
    // Add new permalink structure options
    $newStructures = [
        'php-extension' => [
            'label' => 'With .php Extension',
            'example' => '/concrete-volume.php',
            'description' => 'Clean URLs with .php extension for better compatibility'
        ],
        'base-path' => [
            'label' => 'Custom Base Path',
            'example' => '/tools/concrete-volume',
            'description' => 'Use a custom base path for all calculators'
        ],
        'custom' => [
            'label' => 'Custom Pattern',
            'example' => '/calc/{category}/{slug}',
            'description' => 'Define your own URL pattern with placeholders'
        ]
    ];

    // Add new settings to the settings table
    $settings = [
        [
            'setting_key' => 'permalink_base_path',
            'setting_value' => 'tools',
            'setting_type' => 'string',
            'setting_group' => 'seo',
            'description' => 'Custom base path for calculators (e.g., tools, calculators, etc.)'
        ],
        [
            'setting_key' => 'permalink_php_extension',
            'setting_value' => '0',
            'setting_type' => 'boolean',
            'setting_group' => 'seo',
            'description' => 'Append .php extension to URLs for better compatibility'
        ],
        [
            'setting_key' => 'permalink_custom_pattern',
            'setting_value' => '',
            'setting_type' => 'string',
            'setting_group' => 'seo',
            'description' => 'Custom permalink pattern with placeholders: {category}, {subcategory}, {slug}'
        ],
        [
            'setting_key' => 'permalink_redirect_old_urls',
            'setting_value' => '1',
            'setting_type' => 'boolean',
            'setting_group' => 'seo',
            'description' => 'Enable 301 redirects for old URLs when permalink structure changes'
        ]
    ];

    // Insert new settings
    $stmt = $db->prepare("
        INSERT INTO settings (setting_key, setting_value, setting_type, setting_group, description, created_at, updated_at) 
        VALUES (:setting_key, :setting_value, :setting_type, :setting_group, :description, NOW(), NOW())
        ON DUPLICATE KEY UPDATE 
        setting_value = VALUES(setting_value),
        setting_type = VALUES(setting_type),
        setting_group = VALUES(setting_group),
        description = VALUES(description),
        updated_at = NOW()
    ");

    foreach ($settings as $setting) {
        $stmt->execute($setting);
    }

    // Create permalink mappings table for 301 redirects
    $db->exec("
        CREATE TABLE IF NOT EXISTS permalink_mappings (
            id INT AUTO_INCREMENT PRIMARY KEY,
            old_url VARCHAR(500) NOT NULL,
            new_url VARCHAR(500) NOT NULL,
            redirect_type ENUM('301', '302') DEFAULT '301',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_old_url (old_url),
            INDEX idx_new_url (new_url),
            INDEX idx_redirect_type (redirect_type)
        )
    ");

    // Create calculator slugs table for clean URL management
    $db->exec("
        CREATE TABLE IF NOT EXISTS calculator_slugs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            calculator_id VARCHAR(100) NOT NULL,
            slug VARCHAR(200) NOT NULL UNIQUE,
            category VARCHAR(100),
            subcategory VARCHAR(100),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_calculator_id (calculator_id),
            INDEX idx_slug (slug),
            INDEX idx_category (category),
            INDEX idx_subcategory (subcategory)
        )
    ");

    // Initialize calculator slugs from existing calculators
    $db->exec("
        INSERT IGNORE INTO calculator_slugs (calculator_id, slug, category, subcategory)
        SELECT 
            calculator_id,
            slug,
            category,
            subcategory
        FROM calculator_urls
        WHERE slug IS NOT NULL AND slug != ''
    ");

    echo "✅ Enhanced permalink settings migration completed successfully!\n";
    echo "   - Added new permalink structure options\n";
    echo "   - Created permalink_mappings table for 301 redirects\n";
    echo "   - Created calculator_slugs table for clean URL management\n";
    echo "   - Added new SEO settings to the database\n";

} catch (Exception $e) {
    echo "❌ Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}