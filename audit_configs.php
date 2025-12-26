<?php
$files = [
    'app/Config/Calculators/civil.php',
    'app/Config/Calculators/plumbing.php',
    'app/Config/Calculators/electrical.php',
    'app/Config/Calculators/hvac.php',
    'app/Config/Calculators/fire.php',
    'app/Config/Calculators/structural.php',
    'app/Config/Calculators/site.php'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        $config = require $file;
        echo "\nKeys in $file:\n";
        foreach (array_keys($config) as $key) {
            echo " - $key\n";
        }
    } else {
        echo "\nFile NOT FOUND: $file\n";
    }
}
