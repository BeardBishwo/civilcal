<?php
// Simple verification script for Word Bank improvements

$baseUrl = 'http://localhost/Bishwo_Calculator'; // Adjust if needed

function testEndpoint($url, $method = 'GET', $data = null)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        if ($data) curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return ['code' => $httpCode, 'body' => json_decode($response, true)];
}

echo "--- Testing Word Bank API ---\n";

// 1. Test Categories API
$res = testEndpoint("$baseUrl/quiz/guess-word/categories");
if ($res['code'] === 200 && ($res['body']['success'] ?? false)) {
    echo "[PASS] Categories API: Found " . count($res['body']['categories']) . " categories.\n";
} else {
    echo "[FAIL] Categories API: " . json_encode($res['body']) . "\n";
}

// 2. Test Word API (Invalid Category)
$res = testEndpoint("$baseUrl/quiz/guess-word/data?category_id=999999");
if ($res['code'] === 200 && !($res['body']['success'] ?? true)) {
    echo "[PASS] Invalid Category Handling: " . ($res['body']['error'] ?? 'Unknown Error') . "\n";
} else {
    echo "[FAIL] Invalid Category should fail gracefully.\n";
}

// 3. Test Word API (Success)
$res = testEndpoint("$baseUrl/quiz/guess-word/data");
if ($res['code'] === 200 && ($res['body']['success'] ?? false)) {
    echo "[PASS] Get Word API: Loaded '" . ($res['body']['category'] ?? 'General') . "' terminology.\n";
} else {
    echo "[FAIL] Get Word API: " . json_encode($res['body']) . "\n";
}

echo "\n--- Database Verification ---\n";
// Manual check for index existence via PHP (safe)
$pdo = new PDO('mysql:host=localhost;dbname=bishwo_calculator', 'root', '');
$indices = $pdo->query("SHOW INDEX FROM word_bank")->fetchAll(PDO::FETCH_ASSOC);
$indexNames = array_column($indices, 'Key_name');

if (in_array('idx_word_bank_category', $indexNames)) {
    echo "[PASS] Index 'idx_word_bank_category' exists.\n";
} else {
    echo "[FAIL] Index 'idx_word_bank_category' missing.\n";
}

if (in_array('fk_word_bank_category', $indexNames)) {
    echo "[PASS] Foreign Key index exists.\n";
}

echo "\nVerification Complete.\n";
