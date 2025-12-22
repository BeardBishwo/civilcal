<?php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=bishwo_calculator', 'root', '');
$stmt = $pdo->query('SHOW COLUMNS FROM email_templates LIKE "category"');
$row = $stmt->fetch(PDO::FETCH_ASSOC);
echo "Category type: " . $row['Type'] . PHP_EOL;
