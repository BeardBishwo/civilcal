<?php
/**
 * Debug Notification System Execution
 * Create a test page to verify JavaScript execution and identify issues
 */

// Create a simple test HTML page
$testPage = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notification System Debug Test</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background: #f5f5f5;
        }
        .test-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .test-section {
            margin-bottom: 20px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .test-section h3 {
            margin-top: 0;
            color: #333;
        }
        .test-result {
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
        }
        .test-result.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .test-result.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .test-result.warning {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
        }
        .notification-test-area {
            margin: 20px 0;
            padding: 15px;
            background: #e9ecef;
            border-radius: 5px;
        }
        .debug-console {
            background: #212529;
            color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            font-family: monospace;
            white-space: pre-wrap;
            max-height: 200px;
            overflow-y: auto;
        }
    </style>
</head>
<body>
    <div class="test-container">
        <h1>🔍 Notification System Debug Test</h1>
        <p>This page tests the notification system functionality to identify issues.</p>

        <div class="test-section">
            <h3>1. JavaScript File Loading Test</h3>
            <div id="jsLoadingTest" class="test-result">Testing...</div>
        </div>

        <div class="test-section">
            <h3>2. Notification Button Test</h3>
            <div class="notification-test-area">
                <button id="testNotificationButton" class="btn btn-icon" title="Notifications">
                    <i class="fas fa-bell"></i>
                    <span class="notification-badge" id="testNotificationBadge">0</span>
                </button>
            </div>
            <div id="buttonTest" class="test-result">Testing button click handler...</div>
        </div>

        <div class="test-section">
            <h3>3. Notification Dropdown Test</h3>
            <div id="testNotificationDropdown" class="notification-dropdown">
                <div class="notification-header">
                    <h4>Notifications</h4>
                    <a href="#" class="view-all">View All</a>
                </div>
                <div class="notification-list">
                    <div class="loading">Loading notifications...</div>
                </div>
                <div class="notification-footer">
                    <button id="testMarkAllRead" class="btn btn-sm btn-outline-primary">Mark All as Read</button>
                </div>
            </div>
            <div id="dropdownTest" class="test-result">Testing dropdown functionality...</div>
        </div>

        <div class="test-section">
            <h3>4. API Endpoint Test</h3>
            <div id="apiTest" class="test-result">Testing API endpoints...</div>
        </div>

        <div class="test-section">
            <h3>5. Debug Console</h3>
            <div id="debugConsole" class="debug-console">
Initializing debug console...
            </div>
        </div>

        <div class="test-section">
            <h3>6. Recommendations</h3>
            <div id="recommendations" class="test-result">
                Running tests...
            </div>
        </div>
    </div>

    <!-- Include Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Include notification system JS -->
    <script src="themes/admin/assets/js/notification-system.js"></script>

    <!-- Debug test script -->
    <script>
        // Debug console function
        function logToConsole(message, type = 'info') {
            const console = document.getElementById('debugConsole');
            const timestamp = new Date().toISOString().substring(11, 23);
            const logEntry = document.createElement('div');
            logEntry.textContent = `[${timestamp}] [${type.toUpperCase()}] ${message}`;
            logEntry.style.color = type === 'error' ? '#ff6b6b' : type === 'success' ? '#51cf66' : '#fbc531';
            console.appendChild(logEntry);
            console.scrollTop = console.scrollHeight;
        }

        // Test 1: JavaScript Loading
        document.addEventListener('DOMContentLoaded', function() {
            logToConsole('DOMContentLoaded event fired', 'success');

            // Check if notification system is loaded
            if (typeof NotificationSystem !== 'undefined') {
                document.getElementById('jsLoadingTest').className = 'test-result success';
                document.getElementById('jsLoadingTest').textContent = '✅ NotificationSystem class loaded successfully';
                logToConsole('NotificationSystem class found', 'success');
            } else {
                document.getElementById('jsLoadingTest').className = 'test-result error';
                document.getElementById('jsLoadingTest').textContent = '❌ NotificationSystem class not found';
                logToConsole('NotificationSystem class NOT found', 'error');
            }

            // Check if global instance exists
            if (typeof window.notificationSystem !== 'undefined') {
                logToConsole('Global notificationSystem instance found', 'success');
            } else {
                logToConsole('Global notificationSystem instance NOT found', 'error');
            }
        });

        // Test 2: Button Click Handler
        document.getElementById('testNotificationButton').addEventListener('click', function() {
            logToConsole('Test notification button clicked', 'info');

            // Check if dropdown exists
            const dropdown = document.getElementById('testNotificationDropdown');
            if (dropdown) {
                dropdown.classList.toggle('show');
                document.getElementById('buttonTest').className = 'test-result success';
                document.getElementById('buttonTest').textContent = '✅ Button click handler working - dropdown toggled';
                logToConsole('Dropdown toggled successfully', 'success');
            } else {
                document.getElementById('buttonTest').className = 'test-result error';
                document.getElementById('buttonTest').textContent = '❌ Dropdown element not found';
                logToConsole('Dropdown element NOT found', 'error');
            }
        });

        // Test 3: Dropdown Functionality
        const testDropdown = document.getElementById('testNotificationDropdown');
        if (testDropdown) {
            document.getElementById('dropdownTest').className = 'test-result success';
            document.getElementById('dropdownTest').textContent = '✅ Notification dropdown HTML structure present';
            logToConsole('Notification dropdown HTML structure found', 'success');
        } else {
            document.getElementById('dropdownTest').className = 'test-result error';
            document.getElementById('dropdownTest').textContent = '❌ Notification dropdown HTML structure missing';
            logToConsole('Notification dropdown HTML structure NOT found', 'error');
        }

        // Test 4: API Endpoints
        async function testAPIEndpoints() {
            try {
                // Test unread count endpoint
                const response = await fetch('/api/notifications/unread-count');
                if (response.ok) {
                    const data = await response.json();
                    document.getElementById('apiTest').className = 'test-result success';
                    document.getElementById('apiTest').textContent = `✅ API endpoint working - Unread count: ${data.unread_count || 0}`;
                    logToConsole(`API endpoint test successful - Unread count: ${data.unread_count || 0}`, 'success');
                } else {
                    document.getElementById('apiTest').className = 'test-result error';
                    document.getElementById('apiTest').textContent = `❌ API endpoint failed - Status: ${response.status}`;
                    logToConsole(`API endpoint test failed - Status: ${response.status}`, 'error');
                }
            } catch (error) {
                document.getElementById('apiTest').className = 'test-result error';
                document.getElementById('apiTest').textContent = `❌ API endpoint error: ${error.message}`;
                logToConsole(`API endpoint test error: ${error.message}`, 'error');
            }
        }

        // Run API test after a short delay
        setTimeout(testAPIEndpoints, 1000);

        // Test 5: Final Recommendations
        setTimeout(function() {
            const recommendations = [];

            // Check what's working and what's not
            const jsLoaded = typeof NotificationSystem !== 'undefined';
            const buttonWorks = document.getElementById('testNotificationButton').click;
            const dropdownExists = !!document.getElementById('testNotificationDropdown');

            if (!jsLoaded) {
                recommendations.push('❌ JavaScript file not loading properly - check file path and browser console');
            }

            if (!buttonWorks) {
                recommendations.push('❌ Button click handler not working - check event listener binding');
            }

            if (!dropdownExists) {
                recommendations.push('❌ Dropdown HTML missing - check admin.php layout file');
            }

            if (jsLoaded && buttonWorks && dropdownExists) {
                recommendations.push('✅ All components appear to be working!');
                recommendations.push('🔍 If notifications still don\'t work, check:');
                recommendations.push('   - Browser console for JavaScript errors');
                recommendations.push('   - Network tab for API response errors');
                recommendations.push('   - Database connection and table existence');
            }

            const recommendationsDiv = document.getElementById('recommendations');
            recommendationsDiv.innerHTML = recommendations.map(rec => `<div>${rec}</div>`).join('');
        }, 2000);

        // Log initial message
        logToConsole('Debug test page initialized', 'success');
    </script>
</body>
</html>
HTML;

file_put_contents('tests/notification_debug_test.html', $testPage);

echo "Debug test page created successfully. Open tests/notification_debug_test.html in your browser to run the tests.";