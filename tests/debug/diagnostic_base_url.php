<?php

/**
 * Diagnostic page to check APP_BASE detection
 */
require_once __DIR__ . '/app/Config/config.php';
require_once __DIR__ . '/app/Helpers/functions.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Base URL Diagnostic</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
            background: #f5f5f5;
        }

        .card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #333;
        }

        h2 {
            color: #666;
            margin-top: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background: #f8f8f8;
            font-weight: 600;
        }

        code {
            background: #f4f4f4;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
        }

        .success {
            color: #28a745;
        }

        .info {
            color: #17a2b8;
        }
    </style>
</head>

<body>
    <h1>🔍 Base URL Auto-Detection Diagnostic</h1>

    <div class="card">
        <h2>Server Variables</h2>
        <table>
            <tr>
                <th>Variable</th>
                <th>Value</th>
            </tr>
            <tr>
                <td><code>$_SERVER['SCRIPT_NAME']</code></td>
                <td><code><?php echo htmlspecialchars($_SERVER['SCRIPT_NAME'] ?? 'NOT SET'); ?></code></td>
            </tr>
            <tr>
                <td><code>$_SERVER['PHP_SELF']</code></td>
                <td><code><?php echo htmlspecialchars($_SERVER['PHP_SELF'] ?? 'NOT SET'); ?></code></td>
            </tr>
            <tr>
                <td><code>$_SERVER['REQUEST_URI']</code></td>
                <td><code><?php echo htmlspecialchars($_SERVER['REQUEST_URI'] ?? 'NOT SET'); ?></code></td>
            </tr>
            <tr>
                <td><code>$_SERVER['HTTP_HOST']</code></td>
                <td><code><?php echo htmlspecialchars($_SERVER['HTTP_HOST'] ?? 'NOT SET'); ?></code></td>
            </tr>
            <tr>
                <td><code>$_SERVER['DOCUMENT_ROOT']</code></td>
                <td><code><?php echo htmlspecialchars($_SERVER['DOCUMENT_ROOT'] ?? 'NOT SET'); ?></code></td>
            </tr>
        </table>
    </div>

    <div class="card">
        <h2>Detected Configuration</h2>
        <table>
            <tr>
                <th>Constant</th>
                <th>Value</th>
            </tr>
            <tr>
                <td><code>APP_BASE</code></td>
                <td><code class="success"><?php echo htmlspecialchars(APP_BASE); ?></code></td>
            </tr>
            <tr>
                <td><code>APP_URL</code></td>
                <td><code class="success"><?php echo htmlspecialchars(APP_URL); ?></code></td>
            </tr>
            <tr>
                <td><code>APP_NAME</code></td>
                <td><code><?php echo htmlspecialchars(APP_NAME); ?></code></td>
            </tr>
        </table>
    </div>

    <div class="card">
        <h2>URL Helper Functions Test</h2>
        <table>
            <tr>
                <th>Function Call</th>
                <th>Result</th>
            </tr>
            <tr>
                <td><code>app_base_url('/')</code></td>
                <td><code class="info"><?php echo htmlspecialchars(app_base_url('/')); ?></code></td>
            </tr>
            <tr>
                <td><code>app_base_url('/admin')</code></td>
                <td><code class="info"><?php echo htmlspecialchars(app_base_url('/admin')); ?></code></td>
            </tr>
            <tr>
                <td><code>app_base_url('/admin/dashboard')</code></td>
                <td><code class="info"><?php echo htmlspecialchars(app_base_url('/admin/dashboard')); ?></code></td>
            </tr>
            <tr>
                <td><code>app_base_url('/admin/users')</code></td>
                <td><code class="info"><?php echo htmlspecialchars(app_base_url('/admin/users')); ?></code></td>
            </tr>
            <tr>
                <td><code>asset_url('css/admin.css')</code></td>
                <td><code class="info"><?php echo htmlspecialchars(asset_url('css/admin.css')); ?></code></td>
            </tr>
            <tr>
                <td><code>asset_url('js/app.js')</code></td>
                <td><code class="info"><?php echo htmlspecialchars(asset_url('js/app.js')); ?></code></td>
            </tr>
        </table>
    </div>

    <div class="card">
        <h2>Test Links</h2>
        <p>Click these links to verify they work correctly:</p>
        <ul>
            <li><a href="<?php echo app_base_url('/'); ?>">Home</a></li>
            <li><a href="<?php echo app_base_url('/admin'); ?>">Admin Panel</a></li>
            <li><a href="<?php echo app_base_url('/admin/dashboard'); ?>">Admin Dashboard</a></li>
            <li><a href="<?php echo app_base_url('/admin/users'); ?>">Admin Users</a></li>
            <li><a href="<?php echo app_base_url('/admin/settings'); ?>">Admin Settings</a></li>
            <li><a href="<?php echo app_base_url('/login'); ?>">Login</a></li>
        </ul>
    </div>

    <div class="card">
        <h2>Detection Logic Explanation</h2>
        <p>The auto-detection works as follows:</p>
        <ol>
            <li>Get <code>SCRIPT_NAME</code> from server variables</li>
            <li>Extract directory path using <code>dirname()</code></li>
            <li>Remove <code>/public</code> suffix if present</li>
            <li>Normalize root paths (<code>/</code>, <code>\</code>, <code>.</code>) to empty string</li>
            <li>Set as <code>APP_BASE</code></li>
        </ol>
        <p><strong>Current Script:</strong> <code><?php echo htmlspecialchars($_SERVER['SCRIPT_NAME'] ?? 'NOT SET'); ?></code></p>
        <p><strong>Detected Base:</strong> <code><?php echo htmlspecialchars(APP_BASE); ?></code></p>
    </div>
</body>

</html>