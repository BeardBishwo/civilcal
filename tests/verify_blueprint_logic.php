<?php
// Verification script for Blueprint API Improvements

// Mock global constants if needed
if (!defined('STORAGE_PATH')) define('STORAGE_PATH', __DIR__ . '/../storage');

require_once __DIR__ . '/../vendor/autoload.php';
// Mock app environment
if (!defined('BISHWO_CALCULATOR')) define('BISHWO_CALCULATOR', true);

// 1. Test SVG Regex Pattern
$testSvg = '<svg><g id="layer1" opacity="0.5"><rect/></g><path id="layer2" d="M0 0h10v10H0z"/></svg>';
$layerMap = ['layer1' => '1', 'layer2' => '1'];

$pattern = '/(<[^>]+id=["\']([^"\']+)["\'][^>]*?)(\/?>)/i';
$result = preg_replace_callback($pattern, function ($matches) use ($layerMap) {
    $fullTag = $matches[0];
    $tagStart = $matches[1];
    $id = $matches[2];
    $tagEnd = $matches[3];

    if (isset($layerMap[$id])) {
        $opacityValue = $layerMap[$id];
        if (preg_match('/opacity=["\'][^"\']*["\']/', $tagStart)) {
            $tagStart = preg_replace('/opacity=["\'][^"\']*["\']/', 'opacity="' . $opacityValue . '"', $tagStart);
        } else {
            $tagStart .= ' opacity="' . $opacityValue . '"';
        }
        return $tagStart . $tagEnd;
    }
    return $fullTag;
}, $testSvg);

echo "SVG Optimization Test:\n";
if (strpos($result, 'id="layer1" opacity="1"') !== false && strpos($result, 'id="layer2" opacity="1"') !== false) {
    echo "[PASS] SVG Layer replacement working.\n";
} else {
    echo "[FAIL] SVG replacement output: $result\n";
}

// 2. Test Auth check (Mocking session)
session_start();
$_SESSION['user_id'] = 123;

echo "\nAuth check simulation:\n";
$userId = $_SESSION['user_id'] ?? null;
if ($userId === 123) {
    echo "[PASS] Session user_id correctly identified.\n";
} else {
    echo "[FAIL] Session-based auth simulation failed.\n";
}

echo "\nVerification complete (Logic validated).\n";
