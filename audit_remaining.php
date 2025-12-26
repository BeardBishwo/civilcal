<?php
$files = [
    'plumbing' => 'app/Config/Calculators/plumbing.php',
    'electrical' => 'app/Config/Calculators/electrical.php',
    'hvac' => 'app/Config/Calculators/hvac.php',
    'fire' => 'app/Config/Calculators/fire.php'
];

foreach ($files as $name => $file) {
    if (file_exists($file)) {
        $config = require $file;
        echo "[$name]: " . implode(', ', array_keys($config)) . "\n\n";
    }
}
