<?php

echo "=== Testing Dashboard Layout ===\n\n";

// Test 1: Check dashboard view file
echo "1. Testing Dashboard View File:\n";
echo "============================================================\n";

$dashboardView = 'themes/admin/views/dashboard.php';
if (file_exists($dashboardView)) {
    $dashboardContent = file_get_contents($dashboardView);
    
    // Check for dashboard-left class
    if (strpos($dashboardContent, 'class="dashboard-left"') !== false) {
        echo "✅ Dashboard left column found\n";
    } else {
        echo "❌ Dashboard left column missing\n";
    }
    
    // Check for dashboard-right class
    if (strpos($dashboardContent, 'class="dashboard-right"') !== false) {
        echo "✅ Dashboard right column found\n";
    } else {
        echo "❌ Dashboard right column missing\n";
    }
    
    // Check for dashboard-grid wrapper
    if (strpos($dashboardContent, 'class="dashboard-grid"') !== false) {
        echo "✅ Dashboard grid wrapper found\n";
    } else {
        echo "❌ Dashboard grid wrapper missing\n";
    }
    
    // Check for admin-content wrapper
    if (strpos($dashboardContent, 'class="admin-content"') !== false) {
        echo "✅ Admin content wrapper found\n";
    } else {
        echo "❌ Admin content wrapper missing\n";
    }
    
} else {
    echo "❌ Dashboard view file not found\n";
}

echo "\n";

// Test 2: Check CSS for dashboard layout
echo "2. Testing CSS Dashboard Layout:\n";
echo "============================================================\n";

$cssFile = 'themes/admin/assets/css/admin.css';
if (file_exists($cssFile)) {
    $cssContent = file_get_contents($cssFile);
    
    // Check for dashboard-grid CSS
    if (strpos($cssContent, '.dashboard-grid {') !== false) {
        echo "✅ Dashboard grid CSS defined\n";
    } else {
        echo "❌ Dashboard grid CSS missing\n";
    }
    
    // Check for dashboard-left CSS
    if (strpos($cssContent, '.dashboard-left') !== false) {
        echo "✅ Dashboard left column CSS defined\n";
    } else {
        echo "❌ Dashboard left column CSS missing\n";
    }
    
    // Check for dashboard-right CSS
    if (strpos($cssContent, '.dashboard-right') !== false) {
        echo "✅ Dashboard right column CSS defined\n";
    } else {
        echo "❌ Dashboard right column CSS missing\n";
    }
    
    // Check for grid-template-columns
    if (strpos($cssContent, 'grid-template-columns: 2fr 1fr') !== false) {
        echo "✅ Dashboard grid columns properly defined (2fr 1fr)\n";
    } else {
        echo "❌ Dashboard grid columns not properly defined\n";
    }
    
    // Check for responsive breakpoints
    if (strpos($cssContent, '@media (max-width: 1024px)') !== false) {
        echo "✅ Responsive breakpoints defined\n";
    } else {
        echo "❌ Responsive breakpoints missing\n";
    }
    
    // Check for sidebar responsiveness
    if (strpos($cssContent, '.admin-main.sidebar-collapsed .dashboard-grid') !== false) {
        echo "✅ Sidebar responsiveness for dashboard defined\n";
    } else {
        echo "❌ Sidebar responsiveness for dashboard missing\n";
    }
    
} else {
    echo "❌ Admin CSS file not found\n";
}

echo "\n";

// Test 3: Check layout structure
echo "3. Testing Layout Structure:\n";
echo "============================================================\n";

$layoutFile = 'themes/admin/layouts/main.php';
if (file_exists($layoutFile)) {
    $layoutContent = file_get_contents($layoutFile);
    
    // Check for main content element
    if (strpos($layoutContent, 'id="admin-main"') !== false) {
        echo "✅ Main content element has proper ID\n";
    } else {
        echo "❌ Main content element missing proper ID\n";
    }
    
    // Check for sidebar element
    if (strpos($layoutContent, 'id="admin-sidebar"') !== false) {
        echo "✅ Sidebar element has proper ID\n";
    } else {
        echo "❌ Sidebar element missing proper ID\n";
    }
    
    // Check for sidebar toggle
    if (strpos($layoutContent, 'id="sidebar-toggle"') !== false) {
        echo "✅ Sidebar toggle button present\n";
    } else {
        echo "❌ Sidebar toggle button missing\n";
    }
    
} else {
    echo "❌ Admin layout file not found\n";
}

echo "\n";

// Test 4: Check JavaScript for dashboard
echo "4. Testing JavaScript Dashboard Support:\n";
echo "============================================================\n";

$jsFile = 'themes/admin/assets/js/admin.js';
if (file_exists($jsFile)) {
    $jsContent = file_get_contents($jsFile);
    
    // Check for sidebar state synchronization
    if (strpos($jsContent, 'syncSidebarState') !== false) {
        echo "✅ Sidebar state synchronization function exists\n";
    } else {
        echo "❌ Sidebar state synchronization missing\n";
    }
    
    // Check for chart initialization
    if (strpos($jsContent, 'initializeDashboardCharts') !== false) {
        echo "✅ Dashboard chart initialization function exists\n";
    } else {
        echo "❌ Dashboard chart initialization missing\n";
    }
    
    // Check for forced reflow
    if (strpos($jsContent, 'mainContent.style.display') !== false) {
        echo "✅ Forced reflow for sidebar changes implemented\n";
    } else {
        echo "❌ Forced reflow for sidebar changes missing\n";
    }
    
} else {
    echo "❌ Admin JavaScript file not found\n";
}

echo "\n=== Dashboard Layout Test Complete ===\n";

// Summary
$allTestsPassed = (
    strpos($dashboardContent, 'class="dashboard-left"') !== false &&
    strpos($dashboardContent, 'class="dashboard-right"') !== false &&
    strpos($dashboardContent, 'class="dashboard-grid"') !== false &&
    strpos($dashboardContent, 'class="admin-content"') !== false &&
    strpos($cssContent, '.dashboard-grid {') !== false &&
    strpos($cssContent, '.dashboard-left') !== false &&
    strpos($cssContent, '.dashboard-right') !== false &&
    strpos($cssContent, 'grid-template-columns: 2fr 1fr') !== false &&
    strpos($cssContent, '@media (max-width: 1024px)') !== false &&
    strpos($cssContent, '.admin-main.sidebar-collapsed .dashboard-grid') !== false
);

if ($allTestsPassed) {
    echo "\n🎉 ALL DASHBOARD TESTS PASSED! Dashboard layout is properly implemented.\n";
} else {
    echo "\n⚠️  Some dashboard tests failed. Please review the issues above.\n";
}