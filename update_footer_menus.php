<?php
require_once 'app/bootstrap.php';
$db = \App\Core\Database::getInstance();

echo "--- Updating Footer Menu Names ---\n";

// Update footer_1 to "Policy"
$stmt1 = $db->prepare("UPDATE menus SET name = 'Policy' WHERE location = 'footer_1'");
$result1 = $stmt1->execute();
echo "footer_1 → Policy: " . ($result1 ? "SUCCESS" : "FAILED") . "\n";

// Update footer_2 to "Company"
$stmt2 = $db->prepare("UPDATE menus SET name = 'Company' WHERE location = 'footer_2'");
$result2 = $stmt2->execute();
echo "footer_2 → Company: " . ($result2 ? "SUCCESS" : "FAILED") . "\n";

// Update footer_3 to "Find Us Here"
$stmt3 = $db->prepare("UPDATE menus SET name = 'Find Us Here' WHERE location = 'footer_3'");
$result3 = $stmt3->execute();
echo "footer_3 → Find Us Here: " . ($result3 ? "SUCCESS" : "FAILED") . "\n";

// Disable footer_4
$stmt4 = $db->prepare("UPDATE menus SET is_active = 0 WHERE location = 'footer_4'");
$result4 = $stmt4->execute();
echo "footer_4 → Disabled: " . ($result4 ? "SUCCESS" : "FAILED") . "\n";

echo "\n--- Verification ---\n";
$stmt = $db->query("SELECT location, name, is_active FROM menus WHERE location LIKE 'footer_%' ORDER BY location");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $active = $row['is_active'] ? 'ACTIVE' : 'INACTIVE';
    echo "{$row['location']}: {$row['name']} [{$active}]\n";
}
