<?php
/**
 * Module Database Sync Script
 * Syncs the modules table with actual file system structure
 */

require_once __DIR__ . '/../app/bootstrap.php';

// Get database connection
$config = require CONFIG_PATH . '/database.php';
$db = new PDO(
    "mysql:host={$config['host']};dbname={$config['database']};charset=utf8mb4",
    $config['username'],
    $config['password']
);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Module descriptions
$descriptions = [
    'civil' => 'Civil engineering calculations and tools',
    'electrical' => 'Electrical calculations and circuit design',
    'hvac' => 'HVAC load calculations and duct sizing',
    'plumbing' => 'Plumbing and drainage calculations',
    'structural' => 'Structural analysis and design',
    'fire' => 'Fire protection system calculations',
    'estimation' => 'Cost estimation and project budgeting',
    'project-management' => 'Project planning and management tools',
    'mep' => 'MEP coordination and integration tools',
    'site' => 'Site work and construction tools'
];

// Category mappings
$categories = [
    'civil' => 'Civil Engineering',
    'electrical' => 'Electrical Engineering',
    'hvac' => 'HVAC & Cooling',
    'plumbing' => 'Plumbing & Sanitary',
    'structural' => 'Structural Analysis',
    'fire' => 'Fire Safety',
    'estimation' => 'Cost Estimation',
    'project-management' => 'Project Mgmt',
    'mep' => 'MEP Systems',
    'site' => 'Site Operations'
];

// Scan modules directory
$modulesPath = dirname(__DIR__) . '/modules/';
$modules = [];

if (is_dir($modulesPath)) {
    $dirs = scandir($modulesPath);
    
    foreach ($dirs as $dir) {
        if ($dir === '.' || $dir === '..') continue;
        
        $fullPath = $modulesPath . $dir;
        if (is_dir($fullPath)) {
            // Count calculators and sub-categories
            $stats = [
                'calculators' => 0,
                'subcategories' => 0
            ];
            
            $items = scandir($fullPath);
            foreach ($items as $item) {
                if ($item === '.' || $item === '..') continue;
                
                $itemPath = $fullPath . '/' . $item;
                
                if (is_dir($itemPath)) {
                    $stats['subcategories']++;
                    
                    // Count PHP files in sub-category
                    $files = scandir($itemPath);
                    foreach ($files as $file) {
                        if (pathinfo($file, PATHINFO_EXTENSION) === 'php' && $file !== 'index.php') {
                            $stats['calculators']++;
                        }
                    }
                } elseif (pathinfo($item, PATHINFO_EXTENSION) === 'php' && $item !== 'index.php') {
                    $stats['calculators']++;
                }
            }
            
            $modules[] = [
                'slug' => $dir,  // Keep for reference
                'name' => $dir,  // Database 'name' field = slug (directory name)
                'display_name' => ucwords(str_replace(['-', '_'], ' ', $dir)), // Formatted display name
                'description' => $descriptions[$dir] ?? 'Module for ' . ucwords(str_replace(['-', '_'], ' ', $dir)),
                'category' => $categories[$dir] ?? ucwords(str_replace(['-', '_'], ' ', $dir)),
                'calculators_count' => $stats['calculators'],
                'subcategories_count' => $stats['subcategories'],
                'status' => 'active',
                'version' => '1.0.0'
            ];
        }
    }
}

echo "Found " . count($modules) . " modules in file system\n\n";

// Clear existing modules table
try {
    $db->exec("TRUNCATE TABLE modules");
    echo "✓ Cleared existing modules table\n";
} catch (Exception $e) {
    echo "Note: Could not truncate table (might not exist yet)\n";
}

// Insert modules
$stmt = $db->prepare("
    INSERT INTO modules (name, description, category, is_active, version, created_at, updated_at)
    VALUES (:name, :description, :category, :is_active, :version, NOW(), NOW())
    ON DUPLICATE KEY UPDATE
        description = VALUES(description),
        category = VALUES(category),
        is_active = VALUES(is_active),
        version = VALUES(version),
        updated_at = NOW()
");

$inserted = 0;
foreach ($modules as $module) {
    try {
        $stmt->execute([
            'name' => $module['name'], // Store slug in 'name' field
            'description' => $module['description'],
            'category' => $module['category'],
            'is_active' => 1, // Active by default
            'version' => $module['version']
        ]);
        $inserted++;
        echo "✓ Synced: {$module['display_name']} (slug: {$module['name']}, {$module['calculators_count']} tools, {$module['subcategories_count']} categories)\n";
    } catch (Exception $e) {
        echo "✗ Error syncing {$module['display_name']}: " . $e->getMessage() . "\n";
    }
}

echo "\n========================================\n";
echo "Database sync complete!\n";
echo "Synced: $inserted modules\n";
echo "========================================\n";
echo "\nIMPORTANT: The 'name' column now contains the directory slug (e.g., 'civil', 'electrical')\n";
echo "This ensures the module scanner can find the correct directories.\n";

