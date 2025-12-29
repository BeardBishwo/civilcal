<?php
/**
 * INACTIVE USERS INTERFACE
 */

// Use the same structure as index.php but for inactive users
include __DIR__ . '/index.php';
?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Force the status filter to 'inactive' if not already set
        const statusFilter = document.getElementById('status-filter');
        if (statusFilter && !statusFilter.value) {
            statusFilter.value = 'inactive';
        }
        
        // Update header title
        const headerTitle = document.querySelector('.header-title h1');
        if (headerTitle) headerTitle.textContent = 'Inactive Users';
        
        const headerSubtitle = document.querySelector('.header-subtitle');
        if (headerSubtitle) headerSubtitle.textContent = 'Users requiring activation or pending approval';
    });
</script>
