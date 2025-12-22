<?php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=bishwo_calculator', 'root', '');
$stmt = $pdo->query("SELECT setting_key, LENGTH(setting_value) as len FROM site_settings WHERE setting_key LIKE 'email%' ORDER BY setting_key");
while($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo $r['setting_key'] . ': ' . $r['len'] . ' chars' . PHP_EOL;
}
