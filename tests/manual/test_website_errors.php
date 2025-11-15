<?php
/**
 * Website Error Testing Script
 * Tests all critical pages for errors
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define base URL for testing
$baseUrl = 'http://localhost:8000';
$testResults = [];

// Test pages to verify
$pagesToTest = [
    '/' => 'Homepage',
    '/help' => 'Help Center',
    '/developers' => 'Developer Documentation',
    '/login' => 'Login Page',
    '/register' => 'Registration Page',
    '/civil' => 'Civil Engineering Landing',
    '/electrical' => 'Electrical Engineering Landing',
];

// Function to test a page
function testPage($url, $name) {
    global $baseUrl;
    $fullUrl = $baseUrl . $url;
    
    $ch = curl_init($fullUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    return [
        'name' => $name,
        'url' => $url,
        'status' => $httpCode,
        'error' => $error,
        'success' => $httpCode === 200 || $httpCode === 302,
        'response_length' => strlen($response)
    ];
}

// Run tests
foreach ($pagesToTest as $url => $name) {
    $testResults[] = testPage($url, $name);
}

// Display results
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Website Error Test Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .test-card { background: white; border-radius: 8px; padding: 1.5rem; margin-bottom: 1rem; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .status-success { color: #28a745; font-weight: bold; }
        .status-error { color: #dc3545; font-weight: bold; }
        .status-warning { color: #ffc107; font-weight: bold; }
        .test-row { padding: 1rem; border-bottom: 1px solid #eee; }
        .test-row:last-child { border-bottom: none; }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="test-card">
            <h1><i class="fas fa-flask"></i> Website Error Test Report</h1>
            <p class="text-muted">Testing all critical pages for accessibility and errors</p>
            <hr>
            
            <div class="row mb-4">
                <div class="col-md-3 text-center">
                    <h3><?php echo count(array_filter($testResults, fn($r) => $r['success'])); ?></h3>
                    <p class="status-success">Passed</p>
                </div>
                <div class="col-md-3 text-center">
                    <h3><?php echo count(array_filter($testResults, fn($r) => !$r['success'])); ?></h3>
                    <p class="status-error">Failed</p>
                </div>
                <div class="col-md-3 text-center">
                    <h3><?php echo count($testResults); ?></h3>
                    <p>Total Tests</p>
                </div>
                <div class="col-md-3 text-center">
                    <h3><?php echo round((count(array_filter($testResults, fn($r) => $r['success'])) / count($testResults)) * 100); ?>%</h3>
                    <p>Success Rate</p>
                </div>
            </div>

            <h3 class="mt-4 mb-3">Test Results</h3>
            
            <?php foreach ($testResults as $result): ?>
                <div class="test-row">
                    <div class="row align-items-center">
                        <div class="col-md-4">
                            <strong><?php echo htmlspecialchars($result['name']); ?></strong>
                            <br>
                            <small class="text-muted"><?php echo htmlspecialchars($result['url']); ?></small>
                        </div>
                        <div class="col-md-3">
                            <code><?php echo $result['status']; ?></code>
                        </div>
                        <div class="col-md-3">
                            <?php if ($result['success']): ?>
                                <span class="status-success"><i class="fas fa-check-circle"></i> PASS</span>
                            <?php else: ?>
                                <span class="status-error"><i class="fas fa-times-circle"></i> FAIL</span>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-2 text-end">
                            <small><?php echo $result['response_length']; ?> bytes</small>
                        </div>
                    </div>
                    <?php if ($result['error']): ?>
                        <div class="alert alert-danger mt-2 mb-0">
                            <i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($result['error']); ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Summary -->
        <div class="test-card">
            <h3><i class="fas fa-clipboard-list"></i> Summary</h3>
            <?php if (count(array_filter($testResults, fn($r) => $r['success'])) === count($testResults)): ?>
                <div class="alert alert-success">
                    <h4><i class="fas fa-check-circle"></i> All Tests Passed!</h4>
                    <p>All critical pages are accessible and returning proper HTTP status codes.</p>
                </div>
            <?php else: ?>
                <div class="alert alert-warning">
                    <h4><i class="fas fa-exclamation-triangle"></i> Some Tests Failed</h4>
                    <p>Please review the failed tests above and fix the issues.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
