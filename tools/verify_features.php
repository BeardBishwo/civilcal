<?php
define('BISHWO_CALCULATOR', true);
require __DIR__ . '/../app/bootstrap.php';

use App\Models\Analytics;
use App\Models\Search;
use App\Services\ImageOptimizer;

echo "--- VERIFICATION REPORT ---\n";

// 1. Verify Analytics
echo "\n[1] Checking Analytics...\n";
try {
    $analytics = new Analytics();
    // Log a test event
    $analytics->track([
        'event_type' => 'test',
        'event_category' => 'verification',
        'page_url' => '/verify-test'
    ]);
    echo "  - Test event tracked.\n";
    
    // Check stats
    $stats = $analytics->getDashboardCounts();
    if (isset($stats['visitors_today'])) {
        echo "  - Analytics Stats Retrieval: SUCCESS (Visitors Today: {$stats['visitors_today']})\n";
    } else {
        echo "  - Analytics Stats Retrieval: FAILED\n";
    }
} catch (Exception $e) {
    echo "  - Analytics Error: " . $e->getMessage() . "\n";
}

// 2. Verify Search
echo "\n[2] Checking Global Search...\n";
try {
    $search = new Search();
    // We indexed 'Dashboard' and 'Settings' earlier
    $results = $search->search('dashboard', 5);
    
    if (count($results) > 0) {
        echo "  - Search Query 'dashboard': SUCCESS (Found " . count($results) . " results)\n";
        echo "    First result: " . $results[0]['title'] . "\n";
    } else {
        echo "  - Search Query 'dashboard': FAILED (No results)\n";
    }
} catch (Exception $e) {
    echo "  - Search Error: " . $e->getMessage() . "\n";
}

// 3. Verify Image Optimization Requirements
echo "\n[3] Checking Image Optimization...\n";
if (extension_loaded('gd')) {
    echo "  - GD Extension: LOADED\n";
    $info = gd_info();
    echo "  - WebP Support: " . ($info['WebP Support'] ? 'YES' : 'NO') . "\n";
} else {
    echo "  - GD Extension: NOT LOADED\n";
}

// 4. TimeHelper Check
echo "\n[4] Checking TimeHelper...\n";
if (class_exists('\App\Helpers\TimeHelper')) {
    echo "  - Class \App\Helpers\TimeHelper: EXISTS\n";
    echo "  - Time Ago Test: " . \App\Helpers\TimeHelper::timeAgo(date('Y-m-d H:i:s', time() - 3600)) . "\n";
} else {
    echo "  - Class \App\Helpers\TimeHelper: MISSING\n";
}

echo "\n--- END REPORT ---\n";
