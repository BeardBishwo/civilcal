<?php

echo "=== Testing Admin Sidebar Responsiveness ===\n\n";

// Test 1: Check if CSS has responsive rules
echo "1. Testing CSS Responsiveness Rules:\n";
echo "============================================================\n";

$cssFile = 'themes/admin/assets/css/admin.css';
if (file_exists($cssFile)) {
    $cssContent = file_get_contents($cssFile);
    
    // Check for main content width rules
    if (strpos($cssContent, 'width: calc(100% - var(--sidebar-width))') !== false) {
        echo "✅ Main content has dynamic width calculation\n";
    } else {
        echo "❌ Main content missing dynamic width calculation\n";
    }
    
    // Check for sidebar-collapsed width rules
    if (strpos($cssContent, 'width: calc(100% - 70px)') !== false) {
        echo "✅ Sidebar collapsed state has proper width calculation\n";
    } else {
        echo "❌ Sidebar collapsed state missing width calculation\n";
    }
    
    // Check for transition rules
    if (strpos($cssContent, 'transition: var(--transition)') !== false) {
        echo "✅ Content area has smooth transitions\n";
    } else {
        echo "❌ Content area missing transitions\n";
    }
    
    // Check for mobile responsiveness
    if (strpos($cssContent, '.admin-sidebar.show ~ .admin-main') !== false) {
        echo "✅ Mobile sidebar overlay handling present\n";
    } else {
        echo "❌ Mobile sidebar overlay handling missing\n";
    }
    
} else {
    echo "❌ Admin CSS file not found\n";
}

echo "\n";

// Test 2: Check JavaScript sidebar functionality
echo "2. Testing JavaScript Sidebar Functionality:\n";
echo "============================================================\n";

$jsFile = 'themes/admin/assets/js/admin.js';
if (file_exists($jsFile)) {
    $jsContent = file_get_contents($jsFile);
    
    // Check for sidebar toggle functionality
    if (strpos($jsContent, 'syncSidebarState') !== false) {
        echo "✅ Sidebar state synchronization function exists\n";
    } else {
        echo "❌ Sidebar state synchronization missing\n";
    }
    
    // Check for main content class toggle
    if (strpos($jsContent, "mainContent.classList.toggle('sidebar-collapsed'") !== false) {
        echo "✅ Main content class toggle implemented\n";
    } else {
        echo "❌ Main content class toggle missing\n";
    }
    
    // Check for localStorage persistence
    if (strpos($jsContent, 'localStorage.setItem(\'adminSidebarCollapsed\'') !== false) {
        echo "✅ Sidebar state persistence implemented\n";
    } else {
        echo "❌ Sidebar state persistence missing\n";
    }
    
    // Check for responsive submenu handling
    if (strpos($jsContent, 'positionFloatingSubmenu') !== false) {
        echo "✅ Floating submenu positioning implemented\n";
    } else {
        echo "❌ Floating submenu positioning missing\n";
    }
    
} else {
    echo "❌ Admin JavaScript file not found\n";
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
    
    // Check for content wrapper
    if (strpos($layoutContent, 'class="admin-content"') !== false) {
        echo "✅ Content wrapper has proper class\n";
    } else {
        echo "❌ Content wrapper missing proper class\n";
    }
    
    // Check for sidebar toggle button
    if (strpos($layoutContent, 'id="sidebar-toggle"') !== false) {
        echo "✅ Sidebar toggle button present\n";
    } else {
        echo "❌ Sidebar toggle button missing\n";
    }
    
} else {
    echo "❌ Admin layout file not found\n";
}

echo "\n";

// Test 4: Check view files for proper content structure
echo "4. Testing View Files Content Structure:\n";
echo "============================================================\n";

$viewFiles = [
    'themes/admin/views/dashboard.php',
    'themes/admin/views/configured-dashboard.php',
    'themes/admin/views/performance-dashboard.php',
    'themes/admin/views/dashboard_complex.php',
    'themes/admin/views/settings/general.php',
    'themes/admin/views/settings/email.php',
    'themes/admin/views/settings/security.php'
];

$properStructureCount = 0;
$totalViewFiles = count($viewFiles);

foreach ($viewFiles as $viewFile) {
    if (file_exists($viewFile)) {
        $viewContent = file_get_contents($viewFile);
        
        // Check if view has admin-content class wrapper
        if (strpos($viewContent, 'class="admin-content"') !== false) {
            $properStructureCount++;
        }
    }
}

echo "✅ Views with proper admin-content structure: {$properStructureCount}/{$totalViewFiles}\n";

if ($properStructureCount === $totalViewFiles) {
    echo "✅ All view files have proper content structure\n";
} else {
    echo "❌ Some view files missing proper content structure\n";
}

echo "\n=== Sidebar Responsiveness Test Complete ===\n";

// Summary
$allTestsPassed = (
    strpos($cssContent, 'width: calc(100% - var(--sidebar-width))') !== false &&
    strpos($cssContent, 'width: calc(100% - 70px)') !== false &&
    strpos($jsContent, 'syncSidebarState') !== false &&
    strpos($jsContent, "mainContent.classList.toggle('sidebar-collapsed'") !== false &&
    strpos($layoutContent, 'id="admin-main"') !== false &&
    $properStructureCount === $totalViewFiles
);

if ($allTestsPassed) {
    echo "\n🎉 ALL TESTS PASSED! Sidebar responsiveness is properly implemented.\n";
} else {
    echo "\n⚠️  Some tests failed. Please review the issues above.\n";
}