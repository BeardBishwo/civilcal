<?php
require_once __DIR__ . '/app/bootstrap.php';
$db = App\Core\Database::getInstance()->getPdo();
$slugs = ['civil', 'structural', 'site', 'plumbing', 'electrical', 'hvac', 'fire'];
echo "Checking slugs in calculator_urls:\n";
foreach ($slugs as $slug) {
    $stmt = $db->prepare("SELECT * FROM calculator_urls WHERE slug = ?");
    $stmt->execute([$slug]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        echo "✅ {$slug}: found (ID: {$row['calculator_id']}, Path: {$row['full_path']})\n";
    } else {
        echo "❌ {$slug}: not found\n";
    }
}

echo "\nChecking pages table:\n";
try {
    $stmt = $db->query("SELECT * FROM pages");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "Page: {$row['slug']} ({$row['title']})\n";
    }
} catch (Exception $e) {
    echo "Error checking pages: " . $e->getMessage() . "\n";
}
