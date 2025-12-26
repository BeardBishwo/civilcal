<?php
/**
 * Bulk Sync Calculator URLs V2
 * Synchronizes all calculator IDs from config files to database tables.
 * Repairs/Overwrites physical frontend files to ensure they use the new engine.
 */

require_once __DIR__ . '/app/bootstrap.php';

use App\Core\Database;

$db = Database::getInstance()->getPdo();

$categories = [
    'structural',
    'site',
    'plumbing',
    'electrical',
    'hvac',
    'fire',
    'civil',
    'estimation',
    'mep',
    'management'
];

echo "Starting bulk synchronization V2...\n\n";

$total_synced = 0;
$files_repaired = 0;

foreach ($categories as $category) {
    $configFile = __DIR__ . "/app/Config/Calculators/{$category}.php";
    
    if (!file_exists($configFile)) {
        echo "⚠️  Skip {$category}: Config file not found.\n";
        continue;
    }
    
    $config = require $configFile;
    echo "Processing {$category} (" . count($config) . " calculators)...\n";
    
    foreach ($config as $id => $data) {
        $name = $data['name'] ?? $id;
        $subcategory = $data['subcategory'] ?? '';
        $description = $data['description'] ?? '';
        $subDir = str_replace(' ', '_', strtolower($subcategory));
        
        $path = "modules/{$category}";
        if ($subDir) { $path .= "/{$subDir}"; }
        $path .= "/{$id}.php";
        
        try {
            // 1. Sync calculator_urls
            $stmt = $db->prepare("INSERT INTO calculator_urls (calculator_id, category, subcategory, slug, full_path, created_at, updated_at)
                VALUES (:id, :cat, :sub, :slug, :path, NOW(), NOW())
                ON DUPLICATE KEY UPDATE category = VALUES(category), subcategory = VALUES(subcategory), slug = VALUES(slug), full_path = VALUES(full_path), updated_at = NOW()");
            $stmt->execute([':id' => $id, ':cat' => $category, ':sub' => $subDir, ':slug' => $id, ':path' => $path]);
            
            // 2. Sync calculators
            $stmt2 = $db->prepare("INSERT INTO calculators (calculator_id, name, description, category, subcategory, version, is_active, config_json, created_at)
                VALUES (:id, :name, :desc, :cat, :sub, '1.0', 1, :json, NOW())
                ON DUPLICATE KEY UPDATE name = VALUES(name), description = VALUES(description), category = VALUES(category), subcategory = VALUES(subcategory), config_json = VALUES(config_json)");
            $stmt2->execute([':id' => $id, ':name' => $name, ':desc' => $description, ':cat' => $category, ':sub' => $subDir, ':json' => json_encode($data)]);
            
            // 3. Repair/Create physical file
            $fullDir = __DIR__ . "/modules/{$category}";
            if ($subDir) { $fullDir .= "/{$subDir}"; }
            if (!is_dir($fullDir)) { mkdir($fullDir, 0755, true); }
            
            $filePath = $fullDir . "/{$id}.php";
            $shouldOverwrite = true;
            
            if (file_exists($filePath)) {
                $existingContent = file_get_contents($filePath);
                // If it already uses renderCalculator, we might still want to refresh it to ensure correct relative paths
                if (strpos($existingContent, 'renderCalculator') !== false && strpos($existingContent, 'aec-calculator') === false) {
                    $shouldOverwrite = false; 
                }
            }
            
            if ($shouldOverwrite) {
                $content = "<?php\n/**\n * {$name} - Wrapper\n */\nrequire_once dirname(__DIR__, " . ($subDir ? 3 : 2) . ") . '/app/bootstrap.php';\nrequire_once dirname(__DIR__, " . ($subDir ? 3 : 2) . ") . '/themes/default/views/shared/calculator-template.php';\nrenderCalculator('{$id}');\n";
                file_put_contents($filePath, $content);
                $files_repaired++;
            }
            
            echo "  ✅ {$id}\n";
            $total_synced++;
        } catch (Exception $e) {
            echo "  ❌ {$id}: " . $e->getMessage() . "\n";
        }
    }
}

echo "\nSynchronization complete.\n";
echo "Total synced: {$total_synced}\n";
echo "Files repaired/created: {$files_repaired}\n";
