<?php
/**
 * Bulk Sync Calculator URLs
 * Synchronizes all calculator IDs from config files to calculator_urls and calculators tables.
 * Also creates missing physical frontend files.
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
    'mep'
];

echo "Starting bulk synchronization...\n\n";

$total_synced = 0;
$files_created = 0;

foreach ($categories as $category) {
    $configFile = __DIR__ . "/app/Config/Calculators/{$category}.php";
    
    if (!file_exists($configFile)) {
        echo "⚠️  Skip {$category}: Config file not found.\n";
        continue;
    }
    
    $configContent = file_get_contents($configFile);
    // If it's a return array, we can use it.
    $config = require $configFile;
    
    echo "Processing {$category} (" . count($config) . " calculators)...\n";
    
    foreach ($config as $id => $data) {
        $name = $data['name'] ?? $id;
        $subcategory = $data['subcategory'] ?? '';
        $description = $data['description'] ?? '';
        
        // Normalize subcategory for directory name (replace space with underscore if any)
        $subDir = str_replace(' ', '_', strtolower($subcategory));
        
        // 1. Sync to calculator_urls (used by UrlHelper)
        $path = "modules/{$category}";
        if ($subDir) {
            $path .= "/{$subDir}";
        }
        $path .= "/{$id}.php";
        
        try {
            $stmt = $db->prepare("
                INSERT INTO calculator_urls (calculator_id, category, subcategory, slug, full_path, created_at, updated_at)
                VALUES (:id, :cat, :sub, :slug, :path, NOW(), NOW())
                ON DUPLICATE KEY UPDATE
                category = VALUES(category),
                subcategory = VALUES(subcategory),
                slug = VALUES(slug),
                full_path = VALUES(full_path),
                updated_at = NOW()
            ");
            
            $stmt->execute([
                ':id' => $id,
                ':cat' => $category,
                ':sub' => $subDir,
                ':slug' => $id,
                ':path' => $path
            ]);
            
            // 2. Sync to calculators (used by Management)
            $stmt2 = $db->prepare("
                INSERT INTO calculators (
                    calculator_id, name, description, category, subcategory,
                    version, is_active, config_json, created_at
                ) VALUES (:id, :name, :desc, :cat, :sub, '1.0', 1, :json, NOW())
                ON DUPLICATE KEY UPDATE
                name = VALUES(name),
                description = VALUES(description),
                category = VALUES(category),
                subcategory = VALUES(subcategory),
                config_json = VALUES(config_json)
            ");
            
            $stmt2->execute([
                ':id' => $id,
                ':name' => $name,
                ':desc' => $description,
                ':cat' => $category,
                ':sub' => $subDir,
                ':json' => json_encode($data)
            ]);
            
            // 3. Create physical file if missing
            $fullDir = __DIR__ . "/modules/{$category}";
            if ($subDir) {
                $fullDir .= "/{$subDir}";
            }
            
            if (!is_dir($fullDir)) {
                mkdir($fullDir, 0755, true);
            }
            
            $filePath = $fullDir . "/{$id}.php";
            // Check if file exists. If it exists but it's not a wrapper, we might want to backup or skip?
            // For now, if it doesn't exist, create it.
            if (!file_exists($filePath)) {
                $content = "<?php\n/**\n * {$name} - Migrated\n */\nrequire_once dirname(__DIR__, " . ($subDir ? 3 : 2) . ") . '/app/bootstrap.php';\nrequire_once dirname(__DIR__, " . ($subDir ? 3 : 2) . ") . '/themes/default/views/shared/calculator-template.php';\nrenderCalculator('{$id}');\n";
                file_put_contents($filePath, $content);
                $files_created++;
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
echo "Files created: {$files_created}\n";
