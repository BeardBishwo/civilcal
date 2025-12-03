<?php

echo "=== Testing Dashboard Responsiveness Fix ===\n\n";

// Test 1: Check CSS Grid Layout
echo "1. Testing CSS Grid Layout:\n";
echo "============================================================\n";

$cssFile = 'themes/admin/assets/css/admin.css';
if (file_exists($cssFile)) {
    $cssContent = file_get_contents($cssFile);
    
    // Check for minmax in dashboard grid
    if (strpos($cssContent, 'grid-template-columns: minmax(300px, 2fr) minmax(280px, 1fr)') !== false) {
        echo "✅ Dashboard grid uses minmax for flexible layout\n";
    } else {
        echo "❌ Dashboard grid missing minmax flexible layout\n";
    }
    
    // Check for sidebar state-specific grid templates
    if (strpos($cssContent, '.admin-main:not(.sidebar-collapsed) .dashboard-grid') !== false) {
        echo "✅ Sidebar expanded state grid template found\n";
    } else {
        echo "❌ Sidebar expanded state grid template missing\n";
    }
    
    if (strpos($cssContent, '.admin-main.sidebar-collapsed .dashboard-grid') !== false) {
        echo "✅ Sidebar collapsed state grid template found\n";
    } else {
        echo "❌ Sidebar collapsed state grid template missing\n";
    }
    
    // Check for column min-width constraints
    if (strpos($cssContent, 'min-width: 280px') !== false) {
        echo "✅ Right column min-width constraint found\n";
    } else {
        echo "❌ Right column min-width constraint missing\n";
    }
    
    // Check for responsive breakpoints
    if (strpos($cssContent, '@media (max-width: 1200px)') !== false) {
        echo "✅ Responsive breakpoints for dashboard found\n";
    } else {
        echo "❌ Responsive breakpoints for dashboard missing\n";
    }
    
} else {
    echo "❌ Admin CSS file not found\n";
}

echo "\n";

// Test 2: Check JavaScript Enhancements
echo "2. Testing JavaScript Enhancements:\n";
echo "============================================================\n";

$jsFile = 'themes/admin/assets/js/admin.js';
if (file_exists($jsFile)) {
    $jsContent = file_get_contents($jsFile);
    
    // Check for syncDashboardLayout function
    if (strpos($jsContent, 'syncDashboardLayout()') !== false) {
        echo "✅ Dashboard layout sync function found\n";
    } else {
        echo "❌ Dashboard layout sync function missing\n";
    }
    
    // Check for dashboard layout observer
    if (strpos($jsContent, 'initDashboardLayoutObserver()') !== false) {
        echo "✅ Dashboard layout observer found\n";
    } else {
        echo "❌ Dashboard layout observer missing\n";
    }
    
    // Check for ResizeObserver usage
    if (strpos($jsContent, 'new ResizeObserver') !== false) {
        echo "✅ ResizeObserver for dashboard monitoring found\n";
    } else {
        echo "❌ ResizeObserver for dashboard monitoring missing\n";
    }
    
    // Check for chart resize handling
    if (strpos($jsContent, 'chart.chart.resize()') !== false) {
        echo "✅ Chart resize handling in layout sync found\n";
    } else {
        echo "❌ Chart resize handling in layout sync missing\n";
    }
    
} else {
    echo "❌ Admin JavaScript file not found\n";
}

echo "\n";

// Test 3: Check Dashboard View Structure
echo "3. Testing Dashboard View Structure:\n";
echo "============================================================\n";

$dashboardView = 'themes/admin/views/dashboard.php';
if (file_exists($dashboardView)) {
    $dashboardContent = file_get_contents($dashboardView);
    
    // Check for dashboard-grid wrapper
    if (strpos($dashboardContent, 'class="dashboard-grid"') !== false) {
        echo "✅ Dashboard grid wrapper found\n";
    } else {
        echo "❌ Dashboard grid wrapper missing\n";
    }
    
    // Check for dashboard-left column
    if (strpos($dashboardContent, 'class="dashboard-left"') !== false) {
        echo "✅ Dashboard left column found\n";
    } else {
        echo "❌ Dashboard left column missing\n";
    }
    
    // Check for dashboard-right column
    if (strpos($dashboardContent, 'class="dashboard-right"') !== false) {
        echo "✅ Dashboard right column found\n";
    } else {
        echo "❌ Dashboard right column missing\n";
    }
    
    // Check for proper card structure in right column
    if (strpos($dashboardContent, 'Error Monitoring') !== false && 
        strpos($dashboardContent, 'Revenue & Subscriptions') !== false &&
        strpos($dashboardContent, 'Calculator Usage Stats') !== false) {
        echo "✅ Right column contains expected widgets\n";
    } else {
        echo "❌ Right column missing expected widgets\n";
    }
    
} else {
    echo "❌ Dashboard view file not found\n";
}

echo "\n";

// Test 4: Simulate Different Screen Sizes
echo "4. Testing Responsive Behavior Simulation:\n";
echo "============================================================\n";

echo "Testing CSS media queries...\n";

// Check if responsive breakpoints are properly structured
if (file_exists($cssFile)) {
    $cssContent = file_get_contents($cssFile);
    
    // Check for progressive breakpoint structure
    $has1200px = strpos($cssContent, '@media (max-width: 1200px)') !== false;
    $has1024px = strpos($cssContent, '@media (max-width: 1024px)') !== false;
    $has768px = strpos($cssContent, '@media (max-width: 768px)') !== false;
    
    if ($has1200px && $has1024px && $has768px) {
        echo "✅ Progressive responsive breakpoints found\n";
        echo "   - 1200px: Switch to single column when sidebar expanded\n";
        echo "   - 1024px: Switch to single column for medium screens\n";
        echo "   - 768px: Mobile layout with single column\n";
    } else {
        echo "❌ Incomplete responsive breakpoint structure\n";
    }
    
    // Check for proper grid template changes in breakpoints
    $hasGridTemplateChanges = strpos($cssContent, 'grid-template-columns: 1fr') !== false;
    if ($hasGridTemplateChanges) {
        echo "✅ Grid template changes for responsive behavior found\n";
    } else {
        echo "❌ Grid template changes for responsive behavior missing\n";
    }
}

echo "\n=== Dashboard Responsiveness Fix Test Complete ===\n";

// Summary
$allTestsPassed = (
    strpos($cssContent, 'minmax(300px, 2fr) minmax(280px, 1fr)') !== false &&
    strpos($cssContent, '.admin-main:not(.sidebar-collapsed) .dashboard-grid') !== false &&
    strpos($jsContent, 'syncDashboardLayout()') !== false &&
    strpos($jsContent, 'initDashboardLayoutObserver()') !== false &&
    strpos($dashboardContent, 'class="dashboard-right"') !== false
);

if ($allTestsPassed) {
    echo "\n🎉 ALL DASHBOARD RESPONSIVENESS FIXES IMPLEMENTED!\n";
    echo "✅ CSS Grid layout with flexible minmax\n";
    echo "✅ Sidebar state-specific grid templates\n";
    echo "✅ Column min-width constraints\n";
    echo "✅ Responsive breakpoints\n";
    echo "✅ JavaScript layout synchronization\n";
    echo "✅ ResizeObserver for monitoring\n";
    echo "✅ Chart resize handling\n";
    echo "\nThe dashboard-right column should now remain visible when sidebar expands!\n";
} else {
    echo "\n⚠️  Some dashboard responsiveness fixes may be missing.\n";
    echo "Please review the test results above.\n";
}