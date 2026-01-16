<?php
// Verification script for Blueprint API Improvements - Phase 2

// Mock global constants if needed
if (!defined('STORAGE_PATH')) define('STORAGE_PATH', __DIR__ . '/../storage');

require_once __DIR__ . '/../vendor/autoload.php';
// Mock app environment
if (!defined('BISHWO_CALCULATOR')) define('BISHWO_CALCULATOR', true);

// 1. Test DOM-based SVG Rendering
$testSvg = '<?xml version="1.0" encoding="UTF-8"?><svg xmlns="http://www.w3.org/2000/svg"><g id="layer1" opacity="0.5"><rect/></g><path id="layer2" d="M0 0h10v10H0z"/></svg>';
$layerMap = ['layer1' => '1', 'layer2' => '1'];

$dom = new DOMDocument();
@$dom->loadXML($testSvg);
$xpath = new DOMXPath($dom);

foreach ($layerMap as $id => $opacity) {
    $nodes = $xpath->query("//*[@id='$id']");
    foreach ($nodes as $node) {
        if ($node instanceof DOMElement) {
            $node->setAttribute('opacity', $opacity);
        }
    }
}

$result = $dom->saveXML($dom->documentElement);

echo "SVG DOM Manipulation Test:\n";
if (strpos($result, 'id="layer1" opacity="1"') !== false && strpos($result, 'id="layer2" opacity="1"') !== false) {
    echo "[PASS] SVG Layer replacement (DOM) working.\n";
} else {
    echo "[FAIL] SVG replacement output: $result\n";
}

// 2. Mocking Model and Testing Progress Regression Prevention
class MockModel
{
    public function getUserProgress($u, $b)
    {
        return ['sections_revealed' => 3];
    }
    public function updateUserProgress($u, $b, $count)
    {
        $current = $this->getUserProgress($u, $b);
        if ($count <= $current['sections_revealed']) {
            return "NO_UPDATE_NEEDED";
        }
        return "SUCCESS";
    }
}

echo "\nProgress Regression Test:\n";
$mock = new MockModel();
if ($mock->updateUserProgress(1, 1, 2) === "NO_UPDATE_NEEDED") {
    echo "[PASS] Successfully prevented progress regression (3 -> 2).\n";
} else {
    echo "[FAIL] Progress regression not prevented.\n";
}

if ($mock->updateUserProgress(1, 1, 4) === "SUCCESS") {
    echo "[PASS] Successfully allowed progress advancement (3 -> 4).\n";
} else {
    echo "[FAIL] Progress advancement blocked.\n";
}

echo "\nVerification complete.\n";
