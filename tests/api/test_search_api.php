<?php
/**
 * Search API Testing
 * Tests the search functionality for calculator tools
 */

echo "🔍 SEARCH API TESTING\n";
echo "====================\n\n";

// Test search API endpoint
$baseUrl = 'http://localhost/Bishwo_Calculator';
$searchUrl = $baseUrl . '/api/search.php';

// Test cases
$testCases = [
    [
        'name' => 'Empty Search (Popular Items)',
        'query' => '',
        'expected' => 'Should return popular/recent items'
    ],
    [
        'name' => 'Search for "concrete"',
        'query' => 'concrete',
        'expected' => 'Should return concrete-related calculators'
    ],
    [
        'name' => 'Search for "volume"',
        'query' => 'volume',
        'expected' => 'Should return volume calculation tools'
    ],
    [
        'name' => 'Search for "civil"',
        'query' => 'civil',
        'expected' => 'Should return civil engineering tools'
    ],
    [
        'name' => 'Search for "rebar"',
        'query' => 'rebar',
        'expected' => 'Should return rebar calculation tools'
    ],
    [
        'name' => 'Search for non-existent term',
        'query' => 'nonexistent',
        'expected' => 'Should return empty results'
    ]
];

foreach ($testCases as $index => $testCase) {
    echo "🧪 Test " . ($index + 1) . ": {$testCase['name']}\n";
    echo str_repeat("-", 50) . "\n";
    
    $url = $searchUrl . '?q=' . urlencode($testCase['query']);
    echo "   URL: $url\n";
    
    $result = testSearchEndpoint($url);
    
    if ($result['success']) {
        echo "   ✅ Status: {$result['http_code']}\n";
        echo "   📊 Results: " . count($result['data']) . " items\n";
        
        if (!empty($result['data'])) {
            echo "   📝 Sample Results:\n";
            foreach (array_slice($result['data'], 0, 3) as $i => $item) {
                echo "      " . ($i + 1) . ". {$item['name']} ({$item['category']})\n";
                echo "         URL: {$item['url']}\n";
            }
        } else {
            echo "   ℹ️ No results found\n";
        }
    } else {
        echo "   ❌ Error: {$result['error']}\n";
        echo "   📄 Response: {$result['raw_response']}\n";
    }
    
    echo "\n";
}

// Test modules directory discovery
echo "📁 MODULES DIRECTORY TESTING\n";
echo str_repeat("-", 30) . "\n";

$modulesDir = __DIR__ . '/../../modules';
if (is_dir($modulesDir)) {
    echo "✅ Modules directory exists: $modulesDir\n";
    
    $categories = scandir($modulesDir);
    $categoryCount = 0;
    $subcategoryCount = 0;
    $toolCount = 0;
    
    foreach ($categories as $category) {
        if ($category === '.' || $category === '..' || !is_dir($modulesDir . '/' . $category)) continue;
        $categoryCount++;
        
        echo "   📂 $category/\n";
        
        $subcategories = scandir($modulesDir . '/' . $category);
        foreach ($subcategories as $subcategory) {
            if ($subcategory === '.' || $subcategory === '..' || !is_dir($modulesDir . '/' . $category . '/' . $subcategory)) continue;
            $subcategoryCount++;
            
            $tools = scandir($modulesDir . '/' . $category . '/' . $subcategory);
            $toolsInSubcat = 0;
            foreach ($tools as $tool) {
                if (pathinfo($tool, PATHINFO_EXTENSION) === 'php') {
                    $toolsInSubcat++;
                    $toolCount++;
                }
            }
            
            echo "      └── $subcategory/ ($toolsInSubcat tools)\n";
        }
    }
    
    echo "\n📊 Summary:\n";
    echo "   Categories: $categoryCount\n";
    echo "   Subcategories: $subcategoryCount\n";
    echo "   Tools: $toolCount\n";
    
} else {
    echo "❌ Modules directory not found: $modulesDir\n";
}

// Test direct API access
echo "\n🌐 DIRECT API ACCESS TEST\n";
echo str_repeat("-", 25) . "\n";

$directTest = testSearchEndpoint($searchUrl . '?q=concrete');
if ($directTest['success']) {
    echo "✅ Direct API access working\n";
    echo "   Response time: {$directTest['response_time']}ms\n";
} else {
    echo "❌ Direct API access failed\n";
    echo "   Error: {$directTest['error']}\n";
}

echo "\n✨ Search API testing complete!\n";

function testSearchEndpoint($url) {
    $startTime = microtime(true);
    
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTPHEADER => [
            'Accept: application/json',
            'User-Agent: SearchAPITest/1.0'
        ]
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    $endTime = microtime(true);
    $responseTime = round(($endTime - $startTime) * 1000, 2);
    
    if ($error) {
        return [
            'success' => false,
            'error' => $error,
            'http_code' => $httpCode,
            'response_time' => $responseTime
        ];
    }
    
    $data = json_decode($response, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        return [
            'success' => false,
            'error' => 'Invalid JSON response: ' . json_last_error_msg(),
            'http_code' => $httpCode,
            'raw_response' => substr($response, 0, 500),
            'response_time' => $responseTime
        ];
    }
    
    return [
        'success' => true,
        'data' => $data,
        'http_code' => $httpCode,
        'response_time' => $responseTime
    ];
}
?>
