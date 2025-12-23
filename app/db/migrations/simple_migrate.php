<?php
/**
 * Simple Migration: Civil Calculators to Database
 * Only migrates data, assumes tables exist
 */

try {
    $pdo = new PDO('mysql:host=localhost;dbname=bishwo_calculator', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Connected to database\n\n";
} catch (PDOException $e) {
    die("❌ Connection failed: " . $e->getMessage() . "\n");
}

// Load config
$rootInfo = pathinfo(__DIR__); // migrations
$dbInfo = pathinfo($rootInfo['dirname']); // db
$appInfo = pathinfo($dbInfo['dirname']); // app

require_once __DIR__ . '/../../../app/bootstrap.php';
$civilConfig = require __DIR__ . '/../../../app/Config/Calculators/civil.php';

echo "=== Migrating " . count($civilConfig) . " Civil Calculators ===\n\n";

$migrated = 0;
$skipped = 0;
$errors = 0;

foreach ($civilConfig as $calculatorId => $config) {
    try {
        // Check existence
        $stmt = $pdo->prepare("SELECT id FROM calculators WHERE calculator_id = ?");
        $stmt->execute([$calculatorId]);
        
        if ($stmt->fetch()) {
            echo "⚠️  {$config['name']} - already exists\n";
            $skipped++;
            continue;
        }
        
        // Simple insert - just main record, no related tables for now
        $stmt = $pdo->prepare("
            INSERT INTO calculators (
                calculator_id, name, description, category, status, created_at
            ) VALUES (?, ?, ?, ?, 'active', NOW())
        ");
        
        $stmt->execute([
            $calculatorId,
            $config['name'],
            $config['description'],
            $config['category']
        ]);
        
        echo "✅ {$config['name']}\n";
        $migrated++;
        
    } catch (PDOException $e) {
        echo "❌ {$calculatorId}: " . $e->getMessage() . "\n";
        $errors++;
    }
}

echo "\n=== Summary ===\n";
echo "✅ Migrated: $migrated\n";
echo "⚠️  Skipped: $skipped\n";
echo "❌ Errors: $errors\n";

// Verify
$stmt = $pdo->query("SELECT COUNT(*) FROM calculators");
echo "\n📊 Total in database: " . $stmt->fetchColumn() . "\n";
