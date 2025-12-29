<?php
/**
 * ADMINISTRATORS INTERFACE
 */

// Use the same structure as index.php but for admins
include __DIR__ . '/index.php';
?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Force the role filter to 'admin' if not already set
        const roleFilter = document.getElementById('role-filter');
        if (roleFilter && !roleFilter.value) {
            roleFilter.value = 'admin';
        }
        
        // Update header title
        const headerTitle = document.querySelector('.header-title h1');
        if (headerTitle) headerTitle.textContent = 'Administrators';
        
        const headerSubtitle = document.querySelector('.header-subtitle');
        if (headerSubtitle) headerSubtitle.textContent = 'Users with administrative privileges';
    });
</script>
