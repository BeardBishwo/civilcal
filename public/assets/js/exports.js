/**
 * Export functionality for Bishwo Calculator
 * Handles export operations, template management, and file downloads
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize export functionality
    initializeExportFunctionality();
});

function initializeExportFunctionality() {
    // Quick export handlers
    document.querySelectorAll('.quick-export').forEach(button => {
        button.addEventListener('click', handleQuickExport);
    });

    // Custom export modal
    const customExportModal = document.getElementById('exportModal');
    if (customExportModal) {
        initializeCustomExportModal();
    }

    // Template management
    initializeTemplateManagement();

    // Export progress tracking
    initializeExportProgress();
}

/**
 * Handle quick export operations
 */
function handleQuickExport(event) {
    event.preventDefault();
    const format = event.currentTarget.getAttribute('data-format');

    if (!format) {
        showAlert('Error: Export format not specified', 'danger');
        return;
    }

    // Get selected items or all items
    const selectedItems = getSelectedItems();
    const exportData = {
        format: format,
        record_ids: selectedItems.length > 0 ? selectedItems : null
    };

    // Show loading state
    showExportLoading(event.currentTarget, format);

    // Perform export
    performExport(exportData, event.currentTarget);
}

/**
 * Get selected calculation IDs
 */
function getSelectedItems() {
    const selectedCheckboxes = document.querySelectorAll('.item-checkbox:checked');
    return Array.from(selectedCheckboxes).map(cb => cb.getAttribute('data-id')).filter(id => id);
}

/**
 * Show loading state for export button
 */
function showExportLoading(button, format) {
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Exporting...';
    button.disabled = true;

    // Store original content for restoration
    button.dataset.originalContent = originalText;

    // Revert after 30 seconds max (fallback)
    setTimeout(() => {
        if (button.disabled) {
            button.innerHTML = originalText;
            button.disabled = false;
        }
    }, 30000);
}

/**
 * Perform export operation
 */
function performExport(exportData, button) {
    fetch('/user/exports/export', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(exportData)
        })
        .then(response => response.json())
        .then(data => {
            restoreButtonState(button);

            if (data.success) {
                showAlert(`Export completed successfully! File size: ${formatFileSize(data.size)}`, 'success');

                // Auto-download if file was created
                if (data.download_url) {
                    setTimeout(() => {
                        window.open(data.download_url, '_blank');
                    }, 1000);
                }
            } else {
                showAlert(`Export failed: ${data.message}`, 'danger');
            }
        })
        .catch(error => {
            restoreButtonState(button);
            console.error('Export error:', error);
            showAlert('Export failed: Network error or server issue', 'danger');
        });
}

/**
 * Restore button to original state
 */
function restoreButtonState(button) {
    if (button && button.dataset.originalContent) {
        button.innerHTML = button.dataset.originalContent;
        button.disabled = false;
        delete button.dataset.originalContent;
    }
}

/**
 * Initialize custom export modal functionality
 */
function initializeCustomExportModal() {
    const modal = document.getElementById('exportModal');
    const exportButton = modal.querySelector('#performCustomExport');

    if (exportButton) {
        exportButton.addEventListener('click', performCustomExport);
    }

    // Template selection change
    const templateSelect = modal.querySelector('#exportTemplate');
    if (templateSelect) {
        templateSelect.addEventListener('change', loadTemplateConfig);
    }

    // Format change handler
    const formatSelect = modal.querySelector('#exportFormat');
    if (formatSelect) {
        formatSelect.addEventListener('change', updateFormatOptions);
    }
}

/**
 * Perform custom export with template
 */
function performCustomExport() {
    const modal = document.getElementById('exportModal');
    const form = modal.querySelector('#customExportForm');
    const exportButton = modal.querySelector('#performCustomExport');

    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    const formData = new FormData(form);
    const exportData = {
        format: formData.get('format'),
        template_id: formData.get('template_id'),
        start_date: formData.get('start_date'),
        end_date: formData.get('end_date'),
        calculator_type: formData.get('calculator_type'),
        record_ids: formData.get('record_ids') ? formData.get('record_ids').split(',') : null
    };

    // Show loading state
    const originalText = exportButton.innerHTML;
    exportButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Exporting...';
    exportButton.disabled = true;

    performExport(exportData, exportButton);

    // Close modal after short delay
    setTimeout(() => {
        bootstrap.Modal.getInstance(modal).hide();
    }, 2000);
}

/**
 * Load template configuration
 */
function loadTemplateConfig(event) {
    const templateId = event.target.value;
    const format = document.getElementById('exportFormat').value;

    if (!templateId) return;

    fetch(`/user/exports/template-config/${templateId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update form with template configuration
                updateFormWithTemplateConfig(data.config, format);
            } else {
                showAlert('Failed to load template configuration', 'warning');
            }
        })
        .catch(error => {
            console.error('Error loading template config:', error);
            showAlert('Failed to load template configuration', 'warning');
        });
}

/**
 * Update form with template configuration
 */
function updateFormWithTemplateConfig(config, format) {
    // Update format-specific options
    const formatOptions = document.querySelector('.format-options');
    if (formatOptions) {
        updateFormatSpecificOptions(format, config);
    }
}

/**
 * Update format-specific options
 */
function updateFormatSpecificOptions(format, config) {
    const container = document.querySelector(`[data-format-options="${format}"]`);
    if (!container) return;

    // Update PDF options
    if (format === 'pdf') {
        const pageSize = container.querySelector('#pdfPageSize');
        const orientation = container.querySelector('#pdfOrientation');
        const includeLogo = container.querySelector('#pdfIncludeLogo');
        const includeHeader = container.querySelector('#pdfIncludeHeader');
        const includeFooter = container.querySelector('#pdfIncludeFooter');

        if (pageSize && config.page_size) pageSize.value = config.page_size;
        if (orientation && config.orientation) orientation.value = config.orientation;
        if (includeLogo) includeLogo.checked = config.include_logo !== false;
        if (includeHeader) includeHeader.checked = config.include_header !== false;
        if (includeFooter) includeFooter.checked = config.include_footer !== false;
    }

    // Update Excel options
    if (format === 'excel' || format === 'xlsx') {
        const includeFormulas = container.querySelector('#excelIncludeFormulas');
        const freezePanes = container.querySelector('#excelFreezePanes');

        if (includeFormulas) includeFormulas.checked = config.include_formulas !== false;
        if (freezePanes) freezePanes.checked = config.freeze_panes !== false;
    }

    // Update CSV options
    if (format === 'csv') {
        const delimiter = container.querySelector('#csvDelimiter');
        const includeHeaders = container.querySelector('#csvIncludeHeaders');

        if (delimiter && config.delimiter) delimiter.value = config.delimiter;
        if (includeHeaders) includeHeaders.checked = config.include_headers !== false;
    }

    // Update JSON options
    if (format === 'json') {
        const includeMetadata = container.querySelector('#jsonIncludeMetadata');
        const prettyPrint = container.querySelector('#jsonPrettyPrint');

        if (includeMetadata) includeMetadata.checked = config.include_metadata !== false;
        if (prettyPrint) prettyPrint.checked = config.pretty_print !== false;
    }
}

/**
 * Initialize template management functionality
 */
function initializeTemplateManagement() {
    // Edit template buttons
    document.querySelectorAll('.edit-template-btn').forEach(btn => {
        btn.addEventListener('click', handleEditTemplate);
    });

    // Delete template buttons
    document.querySelectorAll('.delete-template-btn').forEach(btn => {
        btn.addEventListener('click', handleDeleteTemplate);
    });

    // Duplicate template buttons
    document.querySelectorAll('.duplicate-template-btn').forEach(btn => {
        btn.addEventListener('click', handleDuplicateTemplate);
    });

    // Use template buttons (for system templates)
    document.querySelectorAll('.use-template-btn').forEach(btn => {
        btn.addEventListener('click', handleUseTemplate);
    });
}

/**
 * Handle template editing
 */
function handleEditTemplate(event) {
    const templateId = event.currentTarget.getAttribute('data-template-id');

    fetch(`/user/exports/template-config/${templateId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showEditTemplateModal(templateId, data.config);
            } else {
                showAlert('Failed to load template for editing', 'danger');
            }
        })
        .catch(error => {
            console.error('Error loading template:', error);
            showAlert('Failed to load template for editing', 'danger');
        });
}

/**
 * Show edit template modal
 */
function showEditTemplateModal(templateId, config) {
    const modal = document.getElementById('editTemplateModal');
    const form = modal.querySelector('#editTemplateForm');
    const body = modal.querySelector('#editTemplateBody');

    // Load edit form content
    body.innerHTML = generateEditTemplateForm(templateId, config);

    // Show modal
    const bsModal = new bootstrap.Modal(modal);
    bsModal.show();

    // Handle form submission
    form.addEventListener('submit', (e) => {
        e.preventDefault();
        handleUpdateTemplate(templateId, form, bsModal);
    }, { once: true });
}

/**
 * Generate edit template form
 */
function generateEditTemplateForm(templateId, config) {
    return `
        <input type="hidden" name="template_id" value="${templateId}">
        <div class="mb-3">
            <label for="edit_template_name" class="form-label">Template Name</label>
            <input type="text" class="form-control" id="edit_template_name" name="template_name" required>
        </div>
        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" id="edit_template_public" name="is_public">
            <label class="form-check-label" for="edit_template_public">
                Make template public
            </label>
        </div>
    `;
}

/**
 * Handle template deletion
 */
function handleDeleteTemplate(event) {
    const templateId = event.currentTarget.getAttribute('data-template-id');

    showConfirmModal('Delete Template', 'Are you sure you want to delete this template? This action cannot be undone.', () => {
        fetch(`/user/exports/delete-template/${templateId}`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('Template deleted successfully', 'success');
                    // Remove template from DOM
                    event.currentTarget.closest('.template-item').remove();
                } else {
                    showAlert('Failed to delete template: ' + data.message, 'danger');
                }
            })
            .catch(error => {
                console.error('Error deleting template:', error);
                showAlert('Failed to delete template', 'danger');
            });
    });
}

/**
 * Handle template duplication
 */
function handleDuplicateTemplate(event) {
    const templateId = event.currentTarget.getAttribute('data-template-id');
    const modal = document.getElementById('duplicateTemplateModal');
    const form = modal.querySelector('#duplicateTemplateForm');

    // Show modal
    const bsModal = new bootstrap.Modal(modal);
    bsModal.show();

    // Handle form submission
    form.addEventListener('submit', (e) => {
        e.preventDefault();

        const formData = new FormData(form);
        const newName = formData.get('new_name');

        fetch(`/user/exports/duplicate-template/${templateId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ new_name: newName })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('Template duplicated successfully', 'success');
                    bsModal.hide();
                    // Reload page to show new template
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showAlert('Failed to duplicate template: ' + data.message, 'danger');
                }
            })
            .catch(error => {
                console.error('Error duplicating template:', error);
                showAlert('Failed to duplicate template', 'danger');
            });
    }, { once: true });
}

/**
 * Handle using system template
 */
function handleUseTemplate(event) {
    const templateId = event.currentTarget.getAttribute('data-template-id');

    // This would typically duplicate the system template for the user
    fetch(`/user/exports/use-template/${templateId}`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('Template added to your templates', 'success');
                // Could update the UI to show the new template
            } else {
                showAlert('Failed to use template: ' + data.message, 'danger');
            }
        })
        .catch(error => {
            console.error('Error using template:', error);
            showAlert('Failed to use template', 'danger');
        });
}

/**
 * Initialize export progress tracking
 */
function initializeExportProgress() {
    // This would track export progress for large datasets
    // Implementation depends on your export service architecture
}

/**
 * Utility Functions
 */

/**
 * Show alert message
 */
function showAlert(message, type = 'info') {
    const alertContainer = getOrCreateAlertContainer();
    const alertId = 'alert_' + Date.now();

    const alertHtml = `
        <div id="${alertId}" class="alert alert-${type} alert-dismissible fade show" role="alert">
            <i class="fas fa-${getAlertIcon(type)} me-2"></i>${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;

    alertContainer.insertAdjacentHTML('afterbegin', alertHtml);

    // Auto-dismiss after 5 seconds
    setTimeout(() => {
        const alert = document.getElementById(alertId);
        if (alert) {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }
    }, 5000);
}

/**
 * Get alert icon based on type
 */
function getAlertIcon(type) {
    const icons = {
        'success': 'check-circle',
        'danger': 'exclamation-circle',
        'warning': 'exclamation-triangle',
        'info': 'info-circle'
    };
    return icons[type] || 'info-circle';
}

/**
 * Get or create alert container
 */
function getOrCreateAlertContainer() {
    let container = document.querySelector('.alert-container');
    if (!container) {
        container = document.createElement('div');
        container.className = 'alert-container position-fixed top-0 end-0 p-3';
        container.style.zIndex = '9999';
        document.body.appendChild(container);
    }
    return container;
}

/**
 * Format file size
 */
function formatFileSize(bytes) {
    const sizes = ['B', 'KB', 'MB', 'GB'];
    if (bytes === 0) return '0 B';
    const i = Math.floor(Math.log(bytes) / Math.log(1024));
    return Math.round(bytes / Math.pow(1024, i) * 100) / 100 + ' ' + sizes[i];
}

/**
 * Debounce function for search
 */
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

/**
 * Format date for display
 */
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString() + ' ' + date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
}