<?php
echo "SCRIPT_NAME: " . $_SERVER['SCRIPT_NAME'] . "<br>";
echo "DOCUMENT_ROOT: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
echo "REQUEST_URI: " . $_SERVER['REQUEST_URI'] . "<br>";
echo "Working Dir: " . getcwd() . "<br>";
echo "DIR: " . __DIR__ . "<br>";

$scriptName = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
$scriptDir = dirname($scriptName);
echo "Initial scriptDir: " . $scriptDir . "<br>";

if (substr($scriptDir, -7) === '/public') {
    $scriptDir = substr($scriptDir, 0, -7);
}
echo "Processed scriptDir (APP_BASE candidate): '" . $scriptDir . "'<br>";

if ($scriptDir === '/' || $scriptDir === '\\' || $scriptDir === '.') {
    $scriptDir = '';
}
echo "Final scriptDir: '" . $scriptDir . "'<br>";
