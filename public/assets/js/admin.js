/**
 * BISHWO CALCULATOR - ADMIN PANEL JAVASCRIPT
 * Premium Admin Dashboard Interactions
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // ===== SIDEBAR TOGGLE ===== //
    
    const sidebar = document.getElementById('adminSidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const mobileSidebarToggle = document.getElementById('mobileSidebarToggle');
    const adminMain = document.querySelector('.admin-main');
    
    // Desktop sidebar collapse
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
            localStorage.setItem('adminSidebarCollapsed', sidebar.classList.contains('collapsed'));
        });
    }
    
    // Mobile sidebar toggle
    if (mobileSidebarToggle) {
        mobileSidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('show');
        });
    }
    
    // Close sidebar on mobile when clicking outside
    document.addEventListener('click', function(e) {
        if (window.innerWidth <= 768) {
            if (!sidebar.contains(e.target) && !mobileSidebarToggle.contains(e.target)) {
                sidebar.classList.remove('show');
            }
        }
    });
    
    // Restore sidebar state from localStorage
    if (localStorage.getItem('adminSidebarCollapsed') === 'true') {
        sidebar.classList.add('collapsed');
    }
    
    // ===== SUBMENU TOGGLE ===== //
    
    // Use event delegation for submenu toggles
    if (sidebar) {
        sidebar.addEventListener('click', function(e) {
            const toggle = e.target.closest('.submenu-toggle');
            if (!toggle) return;
            
            e.preventDefault();
            const parent = toggle.closest('.has-submenu');
            if (!parent) return;
            
            const submenu = parent.querySelector('.submenu');
            const arrow = toggle.querySelector('.submenu-arrow');
            
            // Close other submenus
            document.querySelectorAll('.has-submenu').forEach(item => {
                if (item !== parent && item.classList.contains('active')) {
                    item.classList.remove('active');
                    const otherSubmenu = item.querySelector('.submenu');
                    const otherArrow = item.querySelector('.submenu-arrow');
                    if (otherSubmenu) otherSubmenu.style.maxHeight = null;
                    if (otherArrow) otherArrow.style.transform = 'rotate(0deg)';
                }
            });
            
            // Toggle current submenu
            parent.classList.toggle('active');
            
            if (parent.classList.contains('active')) {
                if (submenu) submenu.style.maxHeight = submenu.scrollHeight + 'px';
                if (arrow) arrow.style.transform = 'rotate(90deg)';
            } else {
                if (submenu) submenu.style.maxHeight = null;
                if (arrow) arrow.style.transform = 'rotate(0deg)';
            }
        });
    }
    
    // Auto-expand active submenu
    const activeSubmenu = document.querySelector('.has-submenu.active');
    if (activeSubmenu) {
        const submenu = activeSubmenu.querySelector('.submenu');
        const arrow = activeSubmenu.querySelector('.submenu-arrow');
        if (submenu) submenu.style.maxHeight = submenu.scrollHeight + 'px';
        if (arrow) arrow.style.transform = 'rotate(90deg)';
    }
    
    // ===== DROPDOWN TOGGLES ===== //
    
    // Notification dropdown
    const notificationToggle = document.getElementById('notificationToggle');
    const notificationDropdown = document.getElementById('notificationDropdown');
    
    if (notificationToggle && notificationDropdown) {
        notificationToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            notificationDropdown.classList.toggle('show');
            // Close profile dropdown if open
            const profileDropdown = document.getElementById('profileDropdown');
            if (profileDropdown) {
                profileDropdown.classList.remove('show');
            }
        });
    }
    
    // Profile dropdown
    const profileToggle = document.getElementById('profileToggle');
    const profileDropdown = document.getElementById('profileDropdown');
    
    if (profileToggle && profileDropdown) {
        profileToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            profileDropdown.classList.toggle('show');
            // Close notification dropdown if open
            const notificationDropdown = document.getElementById('notificationDropdown');
            if (notificationDropdown) {
                notificationDropdown.classList.remove('show');
            }
        });
    }
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', function() {
        const notificationDropdown = document.getElementById('notificationDropdown');
        const profileDropdown = document.getElementById('profileDropdown');
        
        if (notificationDropdown) {
            notificationDropdown.classList.remove('show');
        }
        if (profileDropdown) {
            profileDropdown.classList.remove('show');
        }
    });
    
    // ===== ADMIN SEARCH ===== //
    
    const adminSearch = document.getElementById('adminSearch');
    
    if (adminSearch) {
        adminSearch.addEventListener('input', function(e) {
            const query = e.target.value.toLowerCase();
            
            // Search through sidebar menu items
            const menuItems = document.querySelectorAll('.sidebar-menu li a');
            
            menuItems.forEach(item => {
                const text = item.textContent.toLowerCase();
                const li = item.closest('li');
                
                if (text.includes(query) || query === '') {
                    li.style.display = '';
                } else {
                    li.style.display = 'none';
                }
            });
        });
    }
    
    // ===== AUTO-DISMISS ALERTS ===== //
    
    // Select alerts that are NOT marked as static or permanent
    const alerts = document.querySelectorAll('.alert:not(.alert-static):not(.alert-permanent)');
    
    alerts.forEach(alert => {
        // Auto-dismiss after 5 seconds
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => {
                alert.remove();
            }, 300);
        }, 5000);
    });
    
    // ===== RESPONSIVE HANDLING ===== //
    
    function handleResize() {
        if (window.innerWidth > 768) {
            const sidebar = document.getElementById('adminSidebar');
            if (sidebar) {
                sidebar.classList.remove('show');
            }
        }
    }
    
    window.addEventListener('resize', handleResize);
    
    // ===== FORM AUTO-SAVE (for settings pages) ===== //
    
    const autoSaveForms = document.querySelectorAll('[data-autosave]');
    
    autoSaveForms.forEach(form => {
        const inputs = form.querySelectorAll('input, select, textarea');
        
        inputs.forEach(input => {
            input.addEventListener('change', function() {
                // Show saving indicator
                const formId = form.getAttribute('id');
                console.log('Auto-saving form:', formId);
                
                // You can implement AJAX save here
                // Example:
                // saveFormData(form);
            });
        });
    });
    
    // ===== UTILITY FUNCTIONS ===== //
    
    // Show toast notification
    window.showToast = function(message, type = 'success') {
        const alert = document.createElement('div');
        alert.className = `alert alert-${type}`;
        alert.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
            ${message}
            <button type="button" class="alert-close" onclick="this.parentElement.remove()">&times;</button>
        `;
        
        const content = document.querySelector('.admin-content');
        if (content) {
            content.insertBefore(alert, content.firstChild);
            
            // Auto-dismiss
            setTimeout(() => {
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 300);
            }, 3000);
        }
    };
    
    // Confirm dialog
    window.confirmAction = function(message, callback) {
        if (confirm(message)) {
            callback();
        }
    };
    
    // ===== CONSOLE LOG ===== //
    
    console.log('%c Bishwo Calculator Admin Panel ', 'background: #4361ee; color: #fff; font-size: 14px; padding: 5px 10px; border-radius: 4px;');
    console.log('Admin panel initialized successfully ✓');
    
});