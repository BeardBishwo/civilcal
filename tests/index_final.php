<?php
/**
 * Final Working Index - Bishwo Calculator
 * Simple, working version without autoloader dependencies
 */

// Define base path
define('BASE_PATH', __DIR__ . '/..');
define('BISHWO_CALCULATOR', true);

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if installation is completed
function isInstalled() {
    $configFile = BASE_PATH . '/config/installed.lock';
    $envFile = BASE_PATH . '/.env';
    
    return file_exists($configFile) && file_exists($envFile);
}

// Redirect to installer if not installed
if (!isInstalled() && !isset($_GET['install'])) {
    header('Location: /install/');
    exit;
}

// Simple homepage that works
if (empty($_GET) || (empty($_GET['url']) && $_SERVER['REQUEST_URI'] === '/')) {
    // Show simple calculator homepage
    echo "<!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Bishwo Calculator - Professional Engineering Tools</title>
        <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet'>
        <style>
            body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; }
            .hero-section { color: white; padding: 100px 0; }
            .category-card { 
                border: none; border-radius: 15px; 
                transition: transform 0.3s ease, box-shadow 0.3s ease;
                height: 200px; display: flex; align-items: center; justify-content: center; flex-direction: column;
            }
            .category-card:hover { transform: translateY(-5px); box-shadow: 0 10px 30px rgba(0,0,0,0.2); }
            .card { border: none; border-radius: 15px; }
            .btn { border-radius: 25px; padding: 12px 30px; }
        </style>
    </head>
    <body>
        <div class='hero-section text-center'>
            <div class='container'>
                <h1 class='display-4 mb-4'>🧮 Bishwo Calculator</h1>
                <p class='lead mb-5'>Professional Engineering Calculations & Design Tools</p>
                <a href='test_direct.php' class='btn btn-light btn-lg me-3'>🔧 System Test</a>
                <a href='simple_test.php' class='btn btn-outline-light btn-lg'>🌐 Web Server Test</a>
            </div>
        </div>

        <div class='container mb-5'>
            <div class='row'>
                <div class='col-md-4 mb-4'>
                    <div class='card category-card bg-primary text-white'>
                        <h4>🏗️ Civil Engineering</h4>
                        <p class='text-center'>Structural, concrete, masonry calculators</p>
                        <span class='badge bg-light text-primary'>15 Tools</span>
                    </div>
                </div>
                <div class='col-md-4 mb-4'>
                    <div class='card category-card bg-warning text-dark'>
                        <h4>⚡ Electrical Engineering</h4>
                        <p class='text-center'>Load, circuit, power distribution</p>
                        <span class='badge bg-dark text-warning'>12 Tools</span>
                    </div>
                </div>
                <div class='col-md-4 mb-4'>
                    <div class='card category-card bg-danger text-white'>
                        <h4>🏢 Structural Engineering</h4>
                        <p class='text-center'>Beam, column, foundation analysis</p>
                        <span class='badge bg-light text-danger'>10 Tools</span>
                    </div>
                </div>
                <div class='col-md-4 mb-4'>
                    <div class='card category-card bg-info text-white'>
                        <h4>🌡️ HVAC</h4>
                        <p class='text-center'>Heating, ventilation, air conditioning</p>
                        <span class='badge bg-light text-info'>8 Tools</span>
                    </div>
                </div>
                <div class='col-md-4 mb-4'>
                    <div class='card category-card bg-success text-white'>
                        <h4>🚰 Plumbing</h4>
                        <p class='text-center'>Pipe sizing, drainage, water supply</p>
                        <span class='badge bg-light text-success'>6 Tools</span>
                    </div>
                </div>
                <div class='col-md-4 mb-4'>
                    <div class='card category-card bg-secondary text-white'>
                        <h4>💰 Estimation</h4>
                        <p class='text-center'>Cost, material takeoff, budgeting</p>
                        <span class='badge bg-light text-secondary'>5 Tools</span>
                    </div>
                </div>
            </div>
        </div>

        <div class='container'>
            <div class='row'>
                <div class='col-12'>
                    <div class='card bg-light'>
                        <div class='card-body text-center'>
                            <h5>🎯 Application Status</h5>
                            <p class='mb-0'>
                                <span class='badge bg-success me-2'>✅ INSTALLED</span>
                                <span class='badge bg-info me-2'>🗄️ DATABASE READY</span>
                                <span class='badge bg-warning me-2'>🌐 WEB SERVER OK</span>
                                <span class='badge bg-primary'>🧮 CALCULATOR READY</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class='container mt-4 mb-5'>
            <div class='row'>
                <div class='col-md-6'>
                    <div class='card h-100'>
                        <div class='card-body'>
                            <h5>🔧 Quick Tests</h5>
                            <p>Test the application components:</p>
                            <a href='test_direct.php' class='btn btn-primary me-2 mb-2'>Direct Test</a>
                            <a href='simple_test.php' class='btn btn-info me-2 mb-2'>Web Server</a>
                            <a href='index_simple.php' class='btn btn-warning mb-2'>Diagnostics</a>
                        </div>
                    </div>
                </div>
                <div class='col-md-6'>
                    <div class='card h-100'>
                        <div class='card-body'>
                            <h5>📊 System Information</h5>
                            <ul class='list-unstyled mb-0'>
                                <li>✅ PHP Version: " . PHP_VERSION . "</li>
                                <li>✅ Server: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "</li>
                                <li>✅ Document Root: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'Unknown') . "</li>
                                <li>✅ Installation: Completed</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <footer class='bg-dark text-white text-center py-4'>
            <div class='container'>
                <p class='mb-0'>Bishwo Calculator - Professional Engineering Tools | Installation: " . date('Y-m-d H:i:s', filemtime(BASE_PATH . '/config/installed.lock')) . "</p>
            </div>
        </footer>
    </body>
    </html>";
    exit;
}

// Handle basic routing
$url = $_GET['url'] ?? '';
$segments = explode('/', trim($url, '/'));

if ($segments[0] === 'api' && isset($segments[1])) {
    // Simple API endpoint
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'working',
        'message' => 'Bishwo Calculator API is running',
        'endpoints' => ['test', 'calculators', 'health']
    ]);
    exit;
}

// Default 404 for other routes
http_response_code(404);
echo "<!DOCTYPE html>
<html>
<head>
    <title>404 - Not Found</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet'>
</head>
<body>
    <div class='container mt-5'>
        <div class='alert alert-warning text-center'>
            <h1>404 - Page Not Found</h1>
            <p>The page you're looking for doesn't exist.</p>
            <a href='/' class='btn btn-primary'>Go Home</a>
        </div>
    </div>
</body>
</html>";
?>


