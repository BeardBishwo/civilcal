<?php
require_once 'app/Core/Database.php';
$db = App\Core\Database::getInstance();
$stmt = $db->getPdo()->query("DESCRIBE position_levels");
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo $row['Field'] . "\n";
}
