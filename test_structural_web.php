<?php
require 'app/bootstrap.php';
use App\Engine\CalculatorEngine;

echo "<h1>Web Test Structural</h1>";

$engine = new CalculatorEngine();
$id = 'beam-design';
echo "<p>Testing ID: $id</p>";

try {
    $meta = $engine->getMetadata($id);
    if (isset($meta['error'])) {
        echo "<h2 style='color:red'>Error: " . $meta['error'] . "</h2>";
    } else {
        echo "<h2 style='color:green'>Success! found " . $meta['name'] . "</h2>";
        print_r($meta);
    }
} catch (Exception $e) {
    echo "<h2 style='color:red'>Exception: " . $e->getMessage() . "</h2>";
}
