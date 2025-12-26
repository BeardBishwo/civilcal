<?php
require 'app/bootstrap.php';
$db = App\Core\Database::getInstance()->getPdo();
$stmt = $db->prepare('SELECT * FROM calculator_urls WHERE slug = ?');
$stmt->execute(['beam-design']);
$result = $stmt->fetch(PDO::FETCH_ASSOC);
if ($result) {
    echo "✅ beam-design is in DB:\n";
    print_r($result);
} else {
    echo "❌ beam-design NOT found in DB.\n";
}
