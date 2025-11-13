<div class="step-content">
    <div class="step-icon">
        <i class="fas fa-cogs"></i>
    </div>
    <h2 class="step-heading">Site Configuration</h2>
    <p class="step-description">
        Configure basic site settings. You can change these later from the admin dashboard.
    </p>
    
    <form method="POST" style="text-align: left;">
        <input type="hidden" name="action" value="save_settings">
        
        <div class="form-group">
            <label class="form-label">Site Name</label>
            <input type="text" name="site_name" class="form-control" 
                   value="<?php echo htmlspecialchars($_SESSION['site_settings']['site_name'] ?? 'Bishwo Calculator'); ?>" 
                   placeholder="Bishwo Calculator" required>
        </div>
        
        <div class="form-group">
            <label class="form-label">Site Description</label>
            <textarea name="site_description" class="form-control" rows="3" 
                      placeholder="Professional Engineering Calculator Platform"><?php echo htmlspecialchars($_SESSION['site_settings']['site_description'] ?? 'Professional Engineering Calculator Platform'); ?></textarea>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Site URL</label>
                <input type="url" name="site_url" class="form-control" 
                       value="<?php echo htmlspecialchars($_SESSION['site_settings']['site_url'] ?? 'http://' . $_SERVER['HTTP_HOST']); ?>" 
                       placeholder="https://yoursite.com" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Contact Email</label>
                <input type="email" name="contact_email" class="form-control" 
                       value="<?php echo htmlspecialchars($_SESSION['site_settings']['contact_email'] ?? ($_SESSION['admin_user']['admin_email'] ?? '')); ?>" 
                       placeholder="contact@yoursite.com" required>
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Default Language</label>
                <select name="default_language" class="form-control">
                    <option value="en">English</option>
                    <option value="es">Spanish</option>
                    <option value="fr">French</option>
                    <option value="de">German</option>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label">Timezone</label>
                <select name="timezone" class="form-control">
                    <option value="UTC">UTC</option>
                    <option value="America/New_York">Eastern Time</option>
                    <option value="America/Chicago">Central Time</option>
                    <option value="America/Denver">Mountain Time</option>
                    <option value="America/Los_Angeles">Pacific Time</option>
                    <option value="Europe/London">London</option>
                    <option value="Europe/Paris">Paris</option>
                    <option value="Asia/Tokyo">Tokyo</option>
                    <option value="Asia/Kolkata" selected>India (Kolkata)</option>
                    <option value="Australia/Sydney">Sydney</option>
                </select>
            </div>
        </div>
        
        <div style="background: var(--gray-50); padding: 20px; border-radius: 8px; margin: 24px 0;">
            <h4 style="margin-bottom: 16px; color: var(--gray-800);">
                <i class="fas fa-toggle-on"></i>
                Installation Options
            </h4>
            
            <div class="checkbox-group" style="margin-bottom: 12px;">
                <input type="checkbox" id="install_demo_data" name="install_demo_data" checked style="width: 18px; height: 18px;">
                <label for="install_demo_data" style="margin: 0; font-weight: 500;">
                    Install demo data (sample users, calculations)
                </label>
            </div>
            
            <div class="checkbox-group" style="margin-bottom: 12px;">
                <input type="checkbox" id="enable_registration" name="enable_registration" checked style="width: 18px; height: 18px;">
                <label for="enable_registration" style="margin: 0; font-weight: 500;">
                    Allow public user registration
                </label>
            </div>
            
            <div class="checkbox-group">
                <input type="checkbox" id="enable_analytics" name="enable_analytics" checked style="width: 18px; height: 18px;">
                <label for="enable_analytics" style="margin: 0; font-weight: 500;">
                    Enable usage analytics and tracking
                </label>
            </div>
        </div>
        
        <div class="btn-actions">
            <a href="?step=admin" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Back
            </a>
            
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-arrow-right"></i>
                Save & Continue
            </button>
        </div>
    </form>
</div>
