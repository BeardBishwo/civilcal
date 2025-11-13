<div class="step-content">
    <div class="step-icon">
        <i class="fas fa-database"></i>
    </div>
    <h2 class="step-heading">Database Configuration</h2>
    <p class="step-description">
        Configure your MySQL database connection. Make sure you have created a database and have the connection details ready.
    </p>
    
    <form method="POST" style="text-align: left;">
        <input type="hidden" name="action" value="save_database">
        
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Database Host</label>
                <input type="text" name="db_host" class="form-control" 
                       value="<?php echo htmlspecialchars($_SESSION['db_config']['db_host'] ?? 'localhost'); ?>" 
                       placeholder="localhost" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Database Name</label>
                <input type="text" name="db_name" class="form-control" 
                       value="<?php echo htmlspecialchars($_SESSION['db_config']['db_name'] ?? 'bishwo_calculator'); ?>" 
                       placeholder="bishwo_calculator" required>
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Database Username</label>
                <input type="text" name="db_user" class="form-control" 
                       value="<?php echo htmlspecialchars($_SESSION['db_config']['db_user'] ?? 'root'); ?>" 
                       placeholder="root" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Database Password</label>
                <input type="password" name="db_pass" class="form-control" 
                       value="<?php echo htmlspecialchars($_SESSION['db_config']['db_pass'] ?? ''); ?>" 
                       placeholder="Enter database password">
            </div>
        </div>
        
        <!-- Auto-Delete Setting -->
        <div style="background: var(--gray-50); padding: 20px; border-radius: 8px; margin: 24px 0;">
            <h3 style="margin-bottom: 12px; color: var(--gray-800);">
                <i class="fas fa-trash-alt"></i>
                Auto-Delete Installer
            </h3>
            
            <div class="checkbox-group">
                <input type="checkbox" id="auto_delete" name="auto_delete" style="width: 18px; height: 18px;">
                <label for="auto_delete" style="margin: 0; font-weight: 500;">
                    Automatically delete installer folder after successful installation
                </label>
            </div>
            
            <div class="auto-delete-warning" style="margin-top: 12px; font-size: 13px;">
                <i class="fas fa-info-circle"></i>
                <strong>Development:</strong> Keep unchecked for development/testing.<br>
                <strong>Production:</strong> Check this box when deploying to production or selling the script.
            </div>
        </div>
        
        <div class="btn-actions" style="margin-top: 32px;">
            <a href="?step=requirements" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Back
            </a>
            
            <div style="display: flex; gap: 12px;">
                <button type="button" onclick="testConnection()" class="btn btn-secondary">
                    <i class="fas fa-plug"></i>
                    Test Connection
                </button>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-arrow-right"></i>
                    Save & Continue
                </button>
            </div>
        </div>
    </form>
</div>

<script>
async function testConnection() {
    const form = event.target.form;
    const formData = new FormData(form);
    formData.set('action', 'test_database');
    
    const button = event.target;
    const originalText = button.innerHTML;
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Testing...';
    
    try {
        const response = await fetch('', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.text();
        
        // Show result
        let alertClass = result.includes('alert-success') ? 'success' : 'error';
        let message = result.includes('successful') ? 'Database connection successful!' : 'Connection failed. Check your database settings.';
        
        showAlert(message, alertClass);
        
    } catch (error) {
        showAlert('Network error occurred', 'error');
    } finally {
        button.disabled = false;
        button.innerHTML = originalText;
    }
}

function showAlert(message, type) {
    // Remove existing alerts
    const existing = document.querySelector('.temp-alert');
    if (existing) existing.remove();
    
    const alert = document.createElement('div');
    alert.className = `alert alert-${type} temp-alert`;
    alert.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'}"></i> ${message}`;
    
    const form = document.querySelector('form');
    form.parentNode.insertBefore(alert, form);
    
    setTimeout(() => {
        if (alert.parentNode) {
            alert.remove();
        }
    }, 5000);
}
</script>
