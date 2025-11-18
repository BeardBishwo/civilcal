/**
 * Admin Settings Manager
 * Handles dynamic settings forms, real-time preview, and AJAX updates
 */

class SettingsManager {
    constructor() {
        this.currentGroup = 'general';
        this.unsavedChanges = false;
        this.init();
    }

    init() {
        this.bindEvents();
        this.initializeColorPickers();
        this.initializeImageUploaders();
        this.setupAutoSave();
        this.trackChanges();
    }

    bindEvents() {
        // Tab navigation
        document.querySelectorAll('.settings-tab').forEach(tab => {
            tab.addEventListener('click', (e) => {
                e.preventDefault();
                this.switchTab(tab.dataset.group);
            });
        });

        // Save button
        const saveBtn = document.getElementById('save-settings');
        if (saveBtn) {
            saveBtn.addEventListener('click', () => this.saveSettings());
        }

        // Form submission
        const form = document.getElementById('settings-form');
        if (form) {
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                this.saveSettings();
            });
        }

        // Reset button
        const resetBtn = document.getElementById('reset-settings');
        if (resetBtn) {
            resetBtn.addEventListener('click', () => this.resetSettings());
        }

        // Warn before leaving with unsaved changes
        window.addEventListener('beforeunload', (e) => {
            if (this.unsavedChanges) {
                e.preventDefault();
                e.returnValue = '';
            }
        });
    }

    switchTab(group) {
        // Update active tab
        document.querySelectorAll('.settings-tab').forEach(tab => {
            tab.classList.remove('active');
        });
        document.querySelector(`[data-group="${group}"]`).classList.add('active');

        // Update content
        document.querySelectorAll('.settings-section').forEach(section => {
            section.classList.remove('active');
        });
        document.getElementById(`${group}-settings`).classList.add('active');

        this.currentGroup = group;
        
        // Update URL hash
        window.location.hash = group;
    }

    saveSettings() {
        const form = document.getElementById('settings-form');
        const formData = new FormData(form);
        
        // Show loading
        this.showLoading('Saving settings...');

        fetch(window.APP_BASE_URL + 'admin/settings/save', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.showNotification('Settings saved successfully!', 'success');
                this.unsavedChanges = false;
                
                // Refresh preview if available
                this.refreshPreview();
            } else {
                this.showNotification(data.message || 'Failed to save settings', 'error');
            }
        })
        .catch(error => {
            this.showNotification('An error occurred while saving', 'error');
            console.error(error);
        })
        .finally(() => {
            this.hideLoading();
        });
    }

    resetSettings() {
        if (!confirm('Are you sure you want to reset all settings to default values?')) {
            return;
        }

        this.showLoading('Resetting settings...');

        fetch(window.APP_BASE_URL + 'admin/settings/reset', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ group: this.currentGroup })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.showNotification('Settings reset successfully!', 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                this.showNotification(data.message || 'Failed to reset settings', 'error');
            }
        })
        .catch(error => {
            this.showNotification('An error occurred', 'error');
            console.error(error);
        })
        .finally(() => {
            this.hideLoading();
        });
    }

    initializeColorPickers() {
        document.querySelectorAll('input[type="color"]').forEach(input => {
            // Create color preview
            const preview = document.createElement('div');
            preview.className = 'color-preview';
            preview.style.backgroundColor = input.value;
            
            input.parentNode.insertBefore(preview, input.nextSibling);
            
            input.addEventListener('change', (e) => {
                preview.style.backgroundColor = e.target.value;
                this.updateCSSVariable(input.name, e.target.value);
            });
        });
    }

    initializeImageUploaders() {
        document.querySelectorAll('.image-upload').forEach(uploader => {
            const input = uploader.querySelector('input[type="file"]');
            const preview = uploader.querySelector('.image-preview');
            
            if (input && preview) {
                input.addEventListener('change', (e) => {
                    const file = e.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            preview.src = e.target.result;
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }
        });
    }

    setupAutoSave() {
        let timeout;
        const form = document.getElementById('settings-form');
        
        if (form) {
            form.addEventListener('input', () => {
                clearTimeout(timeout);
                timeout = setTimeout(() => {
                    this.autoSave();
                }, 3000); // Auto-save after 3 seconds of inactivity
            });
        }
    }

    autoSave() {
        // Only auto-save if enabled in settings
        if (this.isAutoSaveEnabled()) {
            this.saveSettings();
        }
    }

    isAutoSaveEnabled() {
        // Check if auto-save is enabled (can be a setting)
        return false; // Disabled by default for safety
    }

    trackChanges() {
        const form = document.getElementById('settings-form');
        if (form) {
            form.addEventListener('input', () => {
                this.unsavedChanges = true;
                this.updateSaveButton();
            });
        }
    }

    updateSaveButton() {
        const saveBtn = document.getElementById('save-settings');
        if (saveBtn) {
            saveBtn.classList.toggle('has-changes', this.unsavedChanges);
        }
    }

    updateCSSVariable(name, value) {
        // Update CSS variable in real-time for live preview
        const varName = this.getCSSVariableName(name);
        if (varName) {
            document.documentElement.style.setProperty(varName, value);
        }
    }

    getCSSVariableName(settingName) {
        // Map setting names to CSS variable names
        const mapping = {
            'primary_color': '--color-primary',
            'secondary_color': '--color-secondary',
            'accent_color': '--color-accent',
            'success_color': '--color-success',
            'warning_color': '--color-warning',
            'danger_color': '--color-danger'
        };
        return mapping[settingName] || null;
    }

    refreshPreview() {
        // Refresh preview iframe if available
        const preview = document.getElementById('preview-frame');
        if (preview) {
            preview.src = preview.src;
        }
    }

    showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.innerHTML = `
            <span class="notification-icon">
                ${this.getNotificationIcon(type)}
            </span>
            <span class="notification-message">${message}</span>
            <button class="notification-close">&times;</button>
        `;
        
        document.body.appendChild(notification);
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
            notification.classList.add('fade-out');
            setTimeout(() => notification.remove(), 300);
        }, 5000);
        
        // Manual close
        notification.querySelector('.notification-close').addEventListener('click', () => {
            notification.classList.add('fade-out');
            setTimeout(() => notification.remove(), 300);
        });
    }

    getNotificationIcon(type) {
        const icons = {
            success: '<i class="fas fa-check-circle"></i>',
            error: '<i class="fas fa-exclamation-circle"></i>',
            warning: '<i class="fas fa-exclamation-triangle"></i>',
            info: '<i class="fas fa-info-circle"></i>'
        };
        return icons[type] || icons.info;
    }

    showLoading(message = 'Loading...') {
        const loading = document.getElementById('loading-overlay') || this.createLoadingOverlay();
        loading.querySelector('.loading-message').textContent = message;
        loading.classList.add('active');
    }

    hideLoading() {
        const loading = document.getElementById('loading-overlay');
        if (loading) {
            loading.classList.remove('active');
        }
    }

    createLoadingOverlay() {
        const overlay = document.createElement('div');
        overlay.id = 'loading-overlay';
        overlay.innerHTML = `
            <div class="loading-content">
                <div class="loading-spinner"></div>
                <div class="loading-message">Loading...</div>
            </div>
        `;
        document.body.appendChild(overlay);
        return overlay;
    }
}

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', () => {
    window.settingsManager = new SettingsManager();
    
    // Load tab from URL hash
    if (window.location.hash) {
        const group = window.location.hash.substring(1);
        if (document.querySelector(`[data-group="${group}"]`)) {
            window.settingsManager.switchTab(group);
        }
    }
});
