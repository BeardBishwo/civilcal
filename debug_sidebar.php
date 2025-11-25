<?php
// Debug sidebar functionality
require_once __DIR__ . '/app/bootstrap.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debug Sidebar</title>
    <link rel="stylesheet" href="<?php echo app_base_url('themes/admin/assets/css/admin.css'); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }
        .debug-info {
            position: fixed;
            top: 10px;
            right: 10px;
            background: #f0f0f0;
            padding: 10px;
            border: 1px solid #ccc;
            z-index: 9999;
            font-size: 12px;
        }
        .debug-button {
            margin: 5px;
            padding: 5px 10px;
            background: #007cba;
            color: white;
            border: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="debug-info">
        <h3>Debug Info</h3>
        <div id="debug-output">Waiting for DOM...</div>
        <button class="debug-button" onclick="testElements()">Test Elements</button>
        <button class="debug-button" onclick="toggleSidebar()">Toggle Sidebar</button>
        <button class="debug-button" onclick="showClasses()">Show Classes</button>
        <button class="debug-button" onclick="testCSS()">Test CSS</button>
    </div>

    <div class="admin-wrapper">
        <!-- Sidebar -->
        <aside id="admin-sidebar" class="admin-sidebar">
            <div class="sidebar-header">
                <div class="admin-logo">
                    <i class="fas fa-calculator"></i>
                    <span class="logo-text">Admin Panel</span>
                </div>
                <button id="sidebar-toggle" class="sidebar-toggle">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
            
            <nav class="sidebar-nav">
                <ul class="nav-menu">
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <span class="nav-text">Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-users"></i>
                            <span class="nav-text">Users</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main id="admin-main" class="admin-main">
            <header class="admin-header">
                <div class="header-left">
                    <h1>Debug Page</h1>
                </div>
            </header>
            
            <div class="admin-content">
                <h2>Debug Content</h2>
                <p>This is a debug page to test sidebar functionality.</p>
                <div id="result"></div>
            </div>
        </main>
    </div>

    <script>
        let sidebar, sidebarToggle, mainContent;
        
        function updateDebug(message) {
            document.getElementById('debug-output').innerHTML += '<br>' + message;
            console.log(message);
        }
        
        function testElements() {
            sidebar = document.getElementById('admin-sidebar');
            sidebarToggle = document.getElementById('sidebar-toggle');
            mainContent = document.getElementById('admin-main');
            
            updateDebug('Sidebar: ' + (sidebar ? 'Found' : 'NOT FOUND'));
            updateDebug('Toggle: ' + (sidebarToggle ? 'Found' : 'NOT FOUND'));
            updateDebug('Main: ' + (mainContent ? 'Found' : 'NOT FOUND'));
            
            if (sidebarToggle) {
                updateDebug('Toggle has click listeners: ' + (sidebarToggle.onclick ? 'YES' : 'NO'));
            }
        }
        
        function toggleSidebar() {
            if (!sidebar) {
                testElements();
            }
            
            if (sidebar) {
                sidebar.classList.toggle('collapsed');
                updateDebug('Sidebar toggled, collapsed: ' + sidebar.classList.contains('collapsed'));
            } else {
                updateDebug('Sidebar not found!');
            }
            
            if (mainContent) {
                mainContent.classList.toggle('sidebar-collapsed');
                updateDebug('Main content toggled, collapsed: ' + mainContent.classList.contains('sidebar-collapsed'));
            }
        }
        
        function showClasses() {
            if (sidebar) {
                updateDebug('Sidebar classes: ' + sidebar.className);
            }
            if (mainContent) {
                updateDebug('Main classes: ' + mainContent.className);
            }
        }
        
        function testCSS() {
            if (sidebar) {
                const computedStyle = window.getComputedStyle(sidebar);
                updateDebug('Sidebar width: ' + computedStyle.width);
                updateDebug('Sidebar display: ' + computedStyle.display);
            }
            
            // Test if collapsed class works
            if (sidebar) {
                sidebar.classList.add('collapsed');
                setTimeout(() => {
                    const computedStyleCollapsed = window.getComputedStyle(sidebar);
                    updateDebug('Collapsed width: ' + computedStyleCollapsed.width);
                    sidebar.classList.remove('collapsed');
                }, 100);
            }
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            updateDebug('DOM Loaded');
            testElements();
            
            // Add event listener
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    updateDebug('Toggle button clicked!');
                    toggleSidebar();
                });
                updateDebug('Click listener added to toggle');
            }
        });
    </script>
</body>
</html>