<?php
/**
 * Run Database Migrations
 * Execute this file via: php run_migrations.php
 */

// Step 1: Create database connection
try {
    $pdo = new PDO('mysql:host=localhost;dbname=bishwo_calculator', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Connected to database\n\n";
} catch (PDOException $e) {
    die("❌ Database connection failed: " . $e->getMessage() . "\n");
}

// Step 2: Run schema migration
echo "=== Creating Database Tables ===\n";
$sqlFile = __DIR__ . '/calculator_management_system.sql';

if (!file_exists($sqlFile)) {
    die("❌ SQL file not found: $sqlFile\n");
}

$sql = file_get_contents($sqlFile);
$statements = array_filter(array_map('trim', explode(';', $sql)));

$success = 0;
$errors = 0;

foreach ($statements as $stmt) {
    if (empty($stmt) || strpos($stmt, '--') === 0) continue;
    
    try {
        $pdo->exec($stmt);
        $success++;
    } catch (PDOException $e) {
        // Ignore "already exists" errors
        if (strpos($e->getMessage(), 'already exists') === false && 
            strpos($e->getMessage(), 'Duplicate') === false) {
            echo "⚠️  Error: " . $e->getMessage() . "\n";
            $errors++;
        }
    }
}

echo "✅ Schema migration complete: $success statements, $errors errors\n\n";

// Step 3: Load civil configuration
require_once dirname(__DIR__, 2) . '/bootstrap.php';
$civilConfig = require dirname(__DIR__) . '/Config/Calculators/civil.php';

echo "=== Migrating Civil Calculators ===\n";
$migrated = 0;
$skipped = 0;

foreach ($civilConfig as $calculatorId => $config) {
    try {
        // Check if already exists
        $stmt = $pdo->prepare("SELECT id FROM calculators WHERE calculator_id = ?");
        $stmt->execute([$calculatorId]);
        
        if ($stmt->fetch()) {
            echo "⚠️  Skipped: {$config['name']} (already exists)\n";
            $skipped++;
            continue;
        }
        
        // Insert calculator
        $stmt = $pdo->prepare("
            INSERT INTO calculators (
                calculator_id, name, description, category, subcategory,
                version, is_active, config_json, created_at
            ) VALUES (?, ?, ?, ?, ?, ?, 1, ?, NOW())
        ");
        
        $stmt->execute([
            $calculatorId,
            $config['name'],
            $config['description'],
            $config['category'],
            $config['subcategory'] ?? null,
            $config['version'] ?? '1.0',
            json_encode($config)
        ]);
        
        echo "✅ Migrated: {$config['name']}\n";
        $migrated++;
        
    } catch (PDOException $e) {
        echo "❌ Error with {$calculatorId}: " . $e->getMessage() . "\n";
    }
}

echo "\n=== Migration Summary ===\n";
echo "Migrated: $migrated\n";
echo "Skipped: $skipped\n";
echo "Total: " . count($civilConfig) . "\n";

// Step 4: Verify
echo "\n=== Verification ===\n";
$stmt = $pdo->query("SELECT COUNT(*) as count FROM calculators");
$count = $stmt->fetchColumn();
echo "📊 Total calculators in database: $count\n";

if ($count > 0) {
    echo "\n✅ Database Ready!\n";
    echo "Calculators are now stored in database and can be managed via admin panel.\n";
}
