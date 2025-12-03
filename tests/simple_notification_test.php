<?php
/**
 * Simple Notification Test
 * Minimal test to verify basic functionality
 */

// Create a simple test page
$testPage = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <title>Simple Notification Test</title>
    <style>
        body { font-family: Arial; padding: 20px; }
        .test-result { padding: 10px; margin: 10px 0; border-radius: 4px; }
        .success { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }
        .notification-badge { background: red; color: white; border-radius: 50%; padding: 2px 6px; font-size: 12px; }
        .notification-dropdown { position: absolute; right: 0; top: 100%; width: 300px; background: white; border: 1px solid #ddd; padding: 10px; display: none; }
        .notification-dropdown.show { display: block; }
    </style>
</head>
<body>
    <h1>Simple Notification Test</h1>

    <div class="test-section">
        <h3>Test Notification Button</h3>
        <button id="testNotificationBtn" title="Notifications">
            <i>🔔</i> <span class="notification-badge" id="testBadge">0</span>
        </button>

        <div id="testDropdown" class="notification-dropdown">
            <h4>Test Notifications</h4>
            <p>This is a test dropdown</p>
        </div>
    </div>

    <div id="testResult" class="test-result">Waiting for test...</div>

    <script>
        // Simple test script
        console.log('🔍 Simple notification test started');

        // Test 1: Check if button exists
        const button = document.getElementById('testNotificationBtn');
        if (button) {
            console.log('✅ Test button found');
        } else {
            console.log('❌ Test button NOT found');
        }

        // Test 2: Add click handler
        if (button) {
            button.addEventListener('click', function() {
                console.log('🔥 Button clicked!');

                const dropdown = document.getElementById('testDropdown');
                if (dropdown) {
                    dropdown.classList.toggle('show');
                    console.log('🎯 Dropdown toggled');

                    document.getElementById('testResult').className = 'test-result success';
                    document.getElementById('testResult').textContent = '✅ Click handler working! Dropdown toggled successfully.';
                } else {
                    console.log('❌ Dropdown not found');
                    document.getElementById('testResult').className = 'test-result error';
                    document.getElementById('testResult').textContent = '❌ Dropdown element missing';
                }
            });
        }

        // Test 3: Test selector that notification-system.js uses
        const titleButton = document.querySelector('button[title="Notifications"]');
        if (titleButton) {
            console.log('✅ Title selector works: button[title="Notifications"] found');
        } else {
            console.log('❌ Title selector FAILED: button[title="Notifications"] not found');
        }

        console.log('📋 Test complete - check browser console for details');
    </script>
</body>
</html>
HTML;

file_put_contents('tests/simple_notification_test.html', $testPage);
echo "Simple test page created. Open tests/simple_notification_test.html to verify basic functionality.";