<?php
$page_title = 'Create Account - EngiCal Pro';
require_once dirname(__DIR__, 4) . '/includes/header.php';
require_once dirname(__DIR__, 4) . '/includes/Security.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$csrf_token = Security::generateCsrfToken();
?>

<div class="auth-container">
    <div class="auth-card">
        <!-- Header Section -->
        <div class="auth-header">
            <h1>Create Professional Account</h1>
            <p class="auth-subtitle">Join thousands of engineers worldwide</p>
        </div>

        <form id="registerForm" class="auth-form">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            
            <!-- Basic Information -->
            <div class="form-section">
                <h3><i class="fas fa-user"></i> Basic Information</h3>
                
                <!-- Username with Real-time Check -->
                <div class="form-group">
                    <label for="username">Username *</label>
                    <div class="input-with-status">
                        <input type="text" 
                               id="username" 
                               name="username" 
                               class="form-control" 
                               placeholder="Choose a unique username"
                               pattern="[a-zA-Z0-9_]{3,20}"
                               title="3-20 characters (letters, numbers, underscore only)"
                               required>
                        <div class="input-status" id="usernameStatus">
                            <span class="status-icon"></span>
                            <span class="status-text">Enter username</span>
                        </div>
                    </div>
                    <div id="usernameMsg" class="field-message"></div>
                </div>

                <!-- Email -->
                <div class="form-group">
                    <label for="email">Email Address *</label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           class="form-control" 
                           placeholder="your.email@company.com"
                           required>
                    <div class="field-message">We'll send a verification email to this address</div>
                </div>

                <!-- Enhanced Password with Strength Meter -->
                <div class="form-group">
                    <label for="password">Password *</label>
                    <div class="password-field">
                        <input type="password" 
                               id="password" 
                               name="password" 
                               class="form-control" 
                               placeholder="Minimum 8 characters"
                               minlength="8"
                               required>
                        <button type="button" class="password-toggle" id="togglePassword">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Professional Information (Required) -->
            <div class="form-section required-section" id="professionalSection">
                <h3><i class="fas fa-hard-hat"></i> Professional Information <span class="required-badge">Required</span></h3>
                
                <div class="section-content">
                    <!-- Engineer Roles -->
                    <div class="form-group">
                        <label>Engineering Specialties</label>
                        <div class="checkbox-grid">
                            <?php
                            $specialties = [
                                'civil' => 'Civil Engineer',
                                'structural' => 'Structural Engineer',
                                'electrical' => 'Electrical Engineer',
                                'hvac' => 'HVAC Engineer',
                                'plumbing' => 'Plumbing Engineer',
                                'fire' => 'Fire Protection Engineer',
                                'surveying' => 'Surveyor',
                                'architectural' => 'Architect',
                                'mechanical' => 'Mechanical Engineer',
                                'environmental' => 'Environmental Engineer',
                                'geotechnical' => 'Geotechnical Engineer',
                                'student' => 'Engineering Student',
                                'contractor' => 'Contractor',
                                'consultant' => 'Consultant',
                                'other' => 'Other'
                            ];
                            
                            foreach ($specialties as $value => $label) {
                                echo '<label class="checkbox-item">
                                        <input type="checkbox" name="engineer_roles[]" value="' . $value . '">
                                        <span class="checkmark"></span>
                                        <span class="checkbox-text">' . $label . '</span>
                                      </label>';
                            }
                            ?>
                        </div>
                    </div>

                    <!-- Professional Details -->
                    <div class="form-row">
                        <div class="form-group half-width">
                            <label for="full_name">Full Name</label>
                            <input type="text" id="full_name" name="full_name" class="form-control" placeholder="Your full name">
                        </div>
                        
                        <div class="form-group half-width">
                            <label for="role">Professional Role</label>
                            <select id="role" name="role" class="form-control">
                                <option value="">Select your role</option>
                                <option value="engineer">Engineer</option>
                                <option value="senior_engineer">Senior Engineer</option>
                                <option value="project_manager">Project Manager</option>
                                <option value="consultant">Consultant</option>
                                <option value="contractor">Contractor</option>
                                <option value="student">Student</option>
                                <option value="educator">Educator</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group half-width">
                            <label for="company">Company/Organization</label>
                            <input type="text" id="company" name="company" class="form-control" placeholder="Your company name">
                        </div>
                        
                        <div class="form-group half-width">
                            <label for="preferred_units">Preferred Units</label>
                            <select id="preferred_units" name="preferred_units" class="form-control">
                                <option value="metric">Metric (SI)</option>
                                <option value="imperial">Imperial (US)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="form-row">
                        <div class="form-group half-width">
                            <label for="phone">Phone Number</label>
                            <input type="tel" id="phone" name="phone" class="form-control" placeholder="+1 (555) 123-4567">
                            <div class="field-message">Optional - for phone verification</div>
                        </div>
                        
                        <div class="form-group half-width">
                            <label class="checkbox-item" for="phone_verification">
                                <input type="checkbox" id="phone_verification" name="phone_verification">
                                <span class="checkmark"></span>
                                <span class="checkbox-text">Enable phone verification</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Location Auto-detection -->
            <div class="form-section expandable" id="locationSection">
                <div class="section-header" onclick="toggleSection('location')">
                    <h3><i class="fas fa-globe"></i> Location Information <span class="auto-badge">Auto-detected</span></h3>
                    <i class="fas fa-chevron-down section-toggle"></i>
                </div>
                
                <div class="section-content" id="locationContent">
                    <div class="form-row">
                        <div class="form-group half-width">
                            <label for="country">Country</label>
                            <input type="text" id="country" name="country" class="form-control" placeholder="Auto-detected from IP">
                        </div>
                        
                        <div class="form-group half-width">
                            <label for="region">State/Region</label>
                            <input type="text" id="region" name="region" class="form-control" placeholder="Auto-detected">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group half-width">
                            <label for="city">City</label>
                            <input type="text" id="city" name="city" class="form-control" placeholder="Auto-detected">
                        </div>
                        
                        <div class="form-group half-width">
                            <label for="timezone">Timezone</label>
                            <input type="text" id="timezone" name="timezone" class="form-control" placeholder="Auto-detected">
                        </div>
                    </div>

                    <div class="address-group">
                        <label for="address" class="address-label">Address</label>
                        <div class="address-input">
                            <input type="text" id="address" name="address" class="form-control" placeholder="Enter your address">
                            <button type="button" class="btn btn-detect-location" id="detectLocation">
                                <i class="fas fa-map-marker-alt"></i> Detect My Location
                            </button>
                        </div>
                        <div class="address-coordinates" id="addressCoordinates" style="display: none;">
                            <div class="coordinates-display">
                                <span class="coordinate-label">Coordinates:</span>
                                <span class="coordinate-value" id="coordinatesText"></span>
                                <button type="button" class="btn btn-sm btn-outline" id="copyCoordinates">
                                    <i class="fas fa-copy"></i> Copy
                                </button>
                            </div>
                        </div>
                        <div class="field-message">
                            <i class="fas fa-info-circle"></i> 
                            Location will be auto-detected from your IP address or GPS. You can update it manually.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Terms and Privacy -->
            <div class="form-section agreement-wrapper">
                <div class="form-group">
                    <label class="checkbox-item">
                        <input type="checkbox" id="terms_agree" name="terms_agree" required>
                        <span class="checkbox-text">
                            I agree to the <a href="terms.php" target="_blank">Terms of Service</a> 
                            and <a href="privacy.php" target="_blank">Privacy Policy</a> *
                        </span>
                    </label>
                </div>
                
                <div class="form-group">
                    <label class="checkbox-item">
                        <input type="checkbox" id="marketing_agree" name="marketing_agree">
                        <span class="checkbox-text">
                            I would like to receive engineering tips and product updates via email (optional)
                        </span>
                    </label>
                </div>
            </div>

            <!-- Hidden fields for IP detection -->
            <input type="hidden" id="ip_detected" name="ip_detected">
            <input type="hidden" id="ip_geo_data" name="ip_geo_data">
            <input type="hidden" id="user_agent" name="user_agent">

            <!-- Submit Button -->
            <div class="form-actions">
                <button type="submit" class="btn btn-primary btn-lg" id="registerBtn">
                    <span class="btn-text">
                        <i class="fas fa-user-plus"></i>
                        Create Professional Account
                    </span>
                    <span class="btn-loading" style="display: none;">
                        <i class="fas fa-spinner fa-spin"></i>
                        Creating Account...
                    </span>
                </button>
            </div>

            <!-- Registration Result -->
            <div id="registrationResult" class="result-message" style="display: none;"></div>
        </form>

        <!-- Login Link -->
        <div class="auth-footer">
            <p>Already have an account? <a href="login.php" class="auth-link">Sign In</a></p>
        </div>
    </div>
</div>

<!-- CSS Styles -->
<style>
.auth-container {
    min-height: 100vh;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 32px;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.auth-card {
    background: #f9fafb;
    border-radius: 16px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    width: 100%;
    max-width: 800px;
    max-height: 90vh;
    overflow-y: auto;
    border: 1px solid rgba(255, 255, 255, 0.1);
    box-sizing: border-box;
}

.auth-card * {
    box-sizing: border-box;
}

.auth-header {
    background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
    color: white;
    text-align: center;
    padding: 40px 32px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.auth-header h1 {
    margin: 0;
    font-size: 2.25rem;
    font-weight: 700;
    letter-spacing: -0.025em;
}

.auth-subtitle {
    margin: 12px 0 0;
    font-size: 1.1rem;
    opacity: 0.9;
}

.auth-form {
    padding: 32px;
    display: flex;
    flex-direction: column;
    gap: 32px;
    max-width: 100%;
    margin: 0 auto;
    box-sizing: border-box;
}

.form-section {
    margin-bottom: 32px;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    padding: 24px;
    background: white;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    width: 100%;
    box-sizing: border-box;
    max-width: 100%;
    margin-left: auto;
    margin-right: auto;
}

.form-section:last-child {
    margin-bottom: 0;
}

.form-section h3 {
    margin: 0 0 24px;
    color: #1f2937;
    font-size: 1.25rem;
    display: flex;
    align-items: center;
    gap: 12px;
    padding-bottom: 16px;
    border-bottom: 2px solid #e5e7eb;
}

.form-section.required-section {
    border-color: #4f46e5;
    background: linear-gradient(to bottom, #ffffff, #f8faff);
}

.form-section.required-section h3 {
    color: #4f46e5;
    border-bottom: 2px solid #4f46e5;
}

.form-group {
    margin-bottom: 24px;
}

.agreement-wrapper .form-group {
    margin-bottom: 16px;
}

.agreement-wrapper .form-group:last-child {
    margin-bottom: 0;
}

.form-row {
    display: flex;
    gap: 24px;
    margin-bottom: 24px;
    width: 100%;
    max-width: 100%;
}

.half-width {
    flex: 1;
    min-width: 0; /* Prevents flex items from overflowing */
    max-width: calc(50% - 12px); /* Accounts for gap */
}

.form-group label {
    display: block;
    margin-bottom: 10px;
    font-weight: 600;
    color: #1f2937;
    font-size: 0.95rem;
}

/* Ensure select boxes match regular inputs */
select.form-control {
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%234f46e5' viewBox='0 0 16 16'%3E%3Cpath d='M8 10.5l-4-4h8l-4 4z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 16px center;
    padding-right: 48px;
}

.form-control {
    width: 100%;
    height: 48px;
    padding: 12px 16px;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    font-size: 1rem;
    transition: all 0.2s ease;
    box-sizing: border-box;
    background: white;
    color: #374151;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.form-control:hover {
    border-color: #4f46e5;
    box-shadow: 0 4px 8px rgba(79, 70, 229, 0.15);
}

.form-control:focus {
    outline: none;
    border-color: #4f46e5;
    box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.2);
}

.input-with-status {
    position: relative;
}

.input-status {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    display: flex;
    align-items: center;
    gap: 5px;
    font-size: 0.875rem;
}

.status-icon {
    width: 16px;
    height: 16px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 10px;
    color: white;
}

.status-icon.available {
    background-color: #10b981;
}

.status-icon.taken {
    background-color: #ef4444;
}

.status-icon.checking {
    background-color: #3b82f6;
}

.field-message {
    font-size: 0.875rem;
    color: #6b7280;
    margin-top: 5px;
}

.password-field {
    position: relative;
}

.password-toggle {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: #6b7280;
    cursor: pointer;
    font-size: 1rem;
}



.expandable .section-header {
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 0;
    border-bottom: 2px solid #f3f4f6;
    transition: all 0.2s ease;
}

.expandable .section-header:hover {
    color: #4f46e5;
}

.section-toggle {
    transition: transform 0.3s ease;
}

.expandable.expanded .section-toggle {
    transform: rotate(180deg);
}

.section-content {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease;
}

.expandable.expanded .section-content {
    max-height: 1000px;
    padding-top: 20px;
}

.optional-badge, .auto-badge {
    background-color: #e0e7ff;
    color: #4f46e5;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 500;
}

.auto-badge {
    background-color: #d1fae5;
    color: #059669;
}

.checkbox-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
    gap: 12px;
    margin: 20px auto;
    padding: 20px;
    background: #f9fafb;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
    opacity: 1 !important;
    visibility: visible !important;
    height: auto !important;
    overflow: visible !important;
    max-width: 90%;
    justify-content: center;
}

.required-section {
    background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
    border: 2px solid #4f46e5;
    border-radius: 12px;
    padding: 25px;
    margin-bottom: 30px;
    display: block !important;
    opacity: 1 !important;
    visibility: visible !important;
}

.section-content {
    display: block !important;
    max-height: none !important;
    overflow: visible !important;
}

.required-badge {
    background-color: #dc2626;
    color: white;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 500;
}

.checkbox-item {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    white-space: nowrap;
    gap: 12px;
    cursor: pointer;
    padding: 8px 20px;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    transition: all 0.2s ease;
    position: relative;
    background: white;
    margin-bottom: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    height: 48px;
    width: 100%;
    max-width: 280px;
    margin: 0 auto;
}

.checkbox-item:hover {
    border-color: #4f46e5;
    background-color: #f8fafc;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(79, 70, 229, 0.15);
}

.checkbox-item input[type="checkbox"] {
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    width: 20px;
    height: 20px;
    border: 2px solid #4f46e5;
    border-radius: 4px;
    margin: 0;
    cursor: pointer;
    position: relative;
    background: white;
    flex-shrink: 0;
    transition: all 0.2s ease;
    vertical-align: middle;
}

.checkbox-text {
    display: inline-block;
    font-size: 0.95rem;
    color: #374151;
    font-weight: 500;
    text-align: left;
    line-height: 1.2;
    padding-top: 2px;
}

.checkbox-grid .checkbox-item {
    justify-content: center;
}

.checkbox-item input[type="checkbox"]:checked {
    background-color: #4f46e5;
    border-color: #4f46e5;
}

.checkbox-item input[type="checkbox"]:checked::after {
    content: '✓';
    position: absolute;
    color: white;
    font-size: 14px;
    font-weight: bold;
    left: 50%;
    top: 50%;
    transform: translate(-50%, -50%);
}

.checkbox-item input[type="checkbox"]:hover {
    border-color: #4338ca;
    box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.2);
}

.checkbox-item input[type="checkbox"]:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.3);
}

.checkbox-text {
    display: inline-block;
    font-size: 0.95rem;
    color: #374151;
    font-weight: 500;
    vertical-align: middle;
    line-height: 1.5;
    padding: 8px 0;
    white-space: normal;
    overflow: visible;
    flex: 1;
}

.checkbox-item input[type="checkbox"]:checked ~ .checkbox-text {
    color: #4f46e5;
    font-weight: 600;
}

.checkbox-text {
    font-size: 0.9rem;
    color: #374151;
    flex: 1;
}

.agreement-wrapper .checkbox-item {
    width: 100%;
    height: 60px;
    margin-bottom: 16px;
    padding: 0 16px;
    display: flex;
    align-items: center;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    background: white;
    transition: all 0.2s ease;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    box-sizing: border-box;
    max-width: 100%;
}

.agreement-wrapper .checkbox-item:hover {
    border-color: #4f46e5;
    background-color: #f8fafc;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(79, 70, 229, 0.15);
}

.agreement-wrapper .checkbox-item input[type="checkbox"] {
    width: 24px;
    height: 24px;
    margin-right: 12px;
}

.agreement-wrapper .checkbox-text {
    font-size: 0.95rem;
    line-height: 1.5;
    color: #374151;
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    padding: 8px 0;
}

.agreement-wrapper .checkbox-text a {
    color: #4f46e5;
    text-decoration: none;
    margin: 0 4px;
    font-weight: 600;
}

.agreement-wrapper .checkbox-text a:hover {
    text-decoration: underline;
}

.address-input {
    display: flex;
    gap: 10px;
}

.address-input .form-control {
    flex: 1;
}

.address-group .field-message {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px;
    background-color: #eff6ff;
    border-radius: 6px;
    margin-top: 8px;
}

.address-coordinates {
    background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
    border: 2px solid #3b82f6;
    border-radius: 8px;
    padding: 15px;
    margin-top: 10px;
    animation: slideIn 0.3s ease-out;
}

.coordinates-display {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
}

.coordinate-label {
    font-weight: 600;
    color: #1e40af;
    display: flex;
    align-items: center;
    gap: 6px;
}

.coordinate-label::before {
    content: "📍";
    font-size: 1rem;
}

.coordinate-value {
    background: white;
    padding: 8px 12px;
    border-radius: 6px;
    border: 1px solid #d1d5db;
    font-family: 'Courier New', monospace;
    font-size: 0.9rem;
    color: #374151;
    font-weight: 500;
}

.btn-sm {
    padding: 6px 12px;
    font-size: 0.875rem;
}

.btn-outline {
    background: transparent;
    border: 2px solid #3b82f6;
    color: #3b82f6;
}

.btn-outline:hover {
    background: #3b82f6;
    color: white;
}

.btn-outline:active {
    background: #2563eb;
    border-color: #2563eb;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.form-actions {
    text-align: center;
    margin: 30px 0;
}

.btn {
    padding: 12px 24px;
    border: none;
    border-radius: 8px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.btn-primary {
    background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
    color: white;
    padding: 15px 40px;
    font-size: 1.1rem;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(79, 70, 229, 0.3);
}

.btn-primary:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
}

.btn-secondary {
    background-color: #6b7280;
    color: white;
}

.btn-secondary:hover {
    background-color: #4b5563;
}

.result-message {
    margin: 20px 0;
    padding: 15px;
    border-radius: 8px;
    text-align: center;
    font-weight: 500;
}

.result-message.success {
    background-color: #d1fae5;
    color: #065f46;
    border: 1px solid #10b981;
}

.result-message.error {
    background-color: #fee2e2;
    color: #991b1b;
    border: 1px solid #ef4444;
}

.auth-footer {
    text-align: center;
    padding: 30px;
    background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
    border-top: 1px solid #e5e7eb;
    color: white;
}

/* Address field styling */
.address-group {
    margin-bottom: 1.5rem;
}

.address-label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #1f2937;
    font-size: 0.95rem;
}

.address-input {
    display: flex;
    align-items: stretch;
    gap: 12px;
    margin-bottom: 12px;
}

.address-input .form-control {
    flex: 1;
    height: 48px;
    padding: 8px 16px;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    font-size: 1rem;
}

.btn-detect-location {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 20px;
    background: linear-gradient(to bottom, #4f46e5 0%, #3c2f99 100%);
    border: none;
    border-radius: 8px;
    color: white;
    font-weight: 500;
    font-size: 0.95rem;
    transition: all 0.2s;
    white-space: nowrap;
    cursor: pointer;
}

.btn-detect-location:hover {
    background: linear-gradient(to bottom, #3c2f99 0%, #2b1c80 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.btn-detect-location:active {
    transform: translateY(0);
}

.btn-detect-location i {
    font-size: 1rem;
}

.auth-link {
    color: white;
    text-decoration: none;
    font-weight: 600;
    padding: 8px 16px;
    border: 2px solid white;
    border-radius: 8px;
    transition: all 0.3s ease;
    display: inline-block;
    margin-left: 8px;
}

.auth-link:hover {
    background: white;
    color: #4f46e5;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(255, 255, 255, 0.3);
}

@media (max-width: 768px) {
    .auth-container {
        padding: 16px;
    }
    
    .auth-card {
        margin: 0;
        border-radius: 12px;
    }
    
    .auth-form {
        padding: 20px;
    }
    
    .form-section {
        padding: 20px;
        margin-bottom: 24px;
    }
    
    .form-row {
        flex-direction: column;
        gap: 16px;
    }
    
    .half-width {
        max-width: 100%;
    }
    
    .checkbox-grid {
        grid-template-columns: 1fr;
        gap: 12px;
        max-width: 100%;
    }
    
    .checkbox-grid .checkbox-item {
        max-width: 100%;
        justify-content: center;
    }
    
    .agreement-wrapper .checkbox-item {
        height: auto;
        min-height: 60px;
        padding: 12px 16px;
    }
    
    .auth-header {
        padding: 32px 20px;
    }
    
    .auth-header h1 {
        font-size: 1.875rem;
    }
}
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
<script>
// Expose PHP specialties to JS so we can build a fallback control if checkboxes are missing
const SPECIALTIES = <?php echo json_encode($specialties); ?>;
// Use server-aware URL for username check (works when APP_BASE changes)
const CHECK_USERNAME_URL = '<?php echo app_base_url('api/check_username.php'); ?>';

document.addEventListener('DOMContentLoaded', function() {
    // Initialize form elements
    initializeForm();
    
    // Ensure specialty selection is visible (fallback for CSS/display issues)
    ensureSpecialtyField();

    // Auto-detect location on page load
    detectUserLocation();
    
    // Set user agent
    document.getElementById('user_agent').value = navigator.userAgent;
});

/**
 * If the checkbox grid is empty or hidden (some deployments/CSS may hide custom checkboxes),
 * create a visible <select multiple> fallback so users can choose specialties.
 */
function ensureSpecialtyField() {
    try {
        const checkboxGrid = document.querySelector('.checkbox-grid');
        if (!checkboxGrid) return;

        const checkboxes = checkboxGrid.querySelectorAll('input[name="engineer_roles[]"]');

        // Helper: checks if any checkbox is visible
        const anyVisible = Array.from(checkboxes).some(cb => {
            const style = window.getComputedStyle(cb);
            return style && style.display !== 'none' && style.visibility !== 'hidden' && cb.offsetParent !== null;
        });

        if (checkboxes.length === 0 || !anyVisible) {
            // Build a multiple select fallback
            const select = document.createElement('select');
            select.name = 'engineer_roles[]';
            select.id = 'engineer_roles_select_fallback';
            select.className = 'form-control';
            select.multiple = true;
            select.size = Math.min(8, Object.keys(SPECIALTIES).length || 5);

            for (const [value, label] of Object.entries(SPECIALTIES)) {
                const opt = document.createElement('option');
                opt.value = value;
                opt.textContent = label;
                select.appendChild(opt);
            }

            // Insert a label and the select into the grid container
            checkboxGrid.innerHTML = '';
            const label = document.createElement('label');
            label.textContent = 'Engineering Specialties (select one or more)';
            label.style.display = 'block';
            label.style.marginBottom = '8px';
            checkboxGrid.appendChild(label);
            checkboxGrid.appendChild(select);
        }
    } catch (e) {
        console.warn('ensureSpecialtyField error:', e);
    }
}

function initializeForm() {
    // Password visibility toggle
    document.getElementById('togglePassword').addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        this.innerHTML = type === 'password' ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
    });
    
    // Username availability check
    const usernameInput = document.getElementById('username');
    let usernameTimeout;

    usernameInput.addEventListener('input', function() {
        clearTimeout(usernameTimeout);
        const username = this.value.trim();

        if (username.length < 3) {
            updateUsernameStatus('Enter username', 'neutral');
            return;
        }

        if (username.length > 20 || !/^[a-zA-Z0-9_]+$/.test(username)) {
            updateUsernameStatus('Invalid format', 'error');
            return;
        }

        updateUsernameStatus('Checking...', 'checking');
        usernameTimeout = setTimeout(() => checkUsernameAvailability(username), 500);
    });

    // Copy coordinates click wired after DOM ready
    document.getElementById('copyCoordinates').addEventListener('click', copyCoordinates);
}


function updateUsernameStatus(message, type) {
    const statusElement = document.getElementById('usernameStatus');
    const textElement = statusElement.querySelector('.status-text');
    const iconElement = statusElement.querySelector('.status-icon');
    const messageElement = document.getElementById('usernameMsg');
    
    textElement.textContent = message;
    iconElement.className = 'status-icon';
    
    messageElement.innerHTML = '';
    
    switch(type) {
        case 'available':
            iconElement.classList.add('available');
            iconElement.innerHTML = '<i class="fas fa-check"></i>';
            messageElement.innerHTML = '<div class="field-message" style="color: #10b981;"><i class="fas fa-check-circle"></i> Username is available!</div>';
            break;
        case 'taken':
            iconElement.classList.add('taken');
            iconElement.innerHTML = '<i class="fas fa-times"></i>';
            messageElement.innerHTML = '<div class="field-message" style="color: #ef4444;"><i class="fas fa-exclamation-circle"></i> Username is already taken</div>';
            break;
        case 'checking':
            iconElement.classList.add('checking');
            iconElement.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            break;
        case 'error':
            iconElement.classList.add('taken');
            iconElement.innerHTML = '<i class="fas fa-exclamation-triangle"></i>';
            messageElement.innerHTML = '<div class="field-message" style="color: #ef4444;"><i class="fas fa-exclamation-triangle"></i> ' + message + '</div>';
            break;
        default:
            iconElement.innerHTML = '';
    }
}

async function checkUsernameAvailability(username) {
    try {
        const response = await fetch(CHECK_USERNAME_URL + '?username=' + encodeURIComponent(username), { credentials: 'same-origin' });
        const data = await response.json();

        if (data.error) {
            // API-side error (DB down, connection issues, validation error)
            updateUsernameStatus(data.error, 'error');
            return;
        }

        if (data.available) {
            updateUsernameStatus('Available', 'available');
        } else {
            updateUsernameStatus('Taken', 'taken');
            if (data.suggestions && data.suggestions.length > 0) {
                const suggestionsHtml = '<div class="field-message" style="margin-top: 8px;">Try: ' + 
                    data.suggestions.map(s => `<span style="color: #4f46e5; cursor: pointer; text-decoration: underline;" onclick="document.getElementById('username').value = '${s}'; checkUsernameAvailability('${s}');">${s}</span>`).join(', ') + 
                    '</div>';
                document.getElementById('usernameMsg').innerHTML += suggestionsHtml;
            }
        }
    } catch (error) {
        updateUsernameStatus('Error checking', 'error');
        console.error('Username check error:', error);
    }
}

async function detectUserLocation() {
    const detectBtn = document.getElementById('detectLocation');
    const originalText = detectBtn.innerHTML;
    
    detectBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Detecting...';
    detectBtn.disabled = true;
    
    try {
        // Try multiple IP geolocation services
        let geoData = null;
        
        try {
            const response = await fetch('https://ipapi.co/json/');
            if (response.ok) {
                geoData = await response.json();
            }
        } catch (e) {
            console.warn('ipapi.co failed, trying ip-api.com');
        }
        
        if (!geoData) {
            try {
                const response = await fetch('http://ip-api.com/json/');
                if (response.ok) {
                    const data = await response.json();
                    geoData = {
                        country: data.country,
                        region: data.regionName,
                        city: data.city,
                        timezone: data.timezone,
                        lat: data.lat,
                        lon: data.lon,
                        ip: data.query
                    };
                }
            } catch (e) {
                console.warn('ip-api.com failed');
            }
        }
        
        if (geoData) {
            // Update form fields
            if (geoData.country_name || geoData.country) {
                document.getElementById('country').value = geoData.country_name || geoData.country;
            }
            if (geoData.region || geoData.regionName) {
                document.getElementById('region').value = geoData.region || geoData.regionName;
            }
            if (geoData.city) {
                document.getElementById('city').value = geoData.city;
            }
            if (geoData.timezone) {
                document.getElementById('timezone').value = geoData.timezone;
            }
            
            // Set hidden fields
            document.getElementById('ip_detected').value = geoData.ip || '';
            document.getElementById('ip_geo_data').value = JSON.stringify(geoData);
            
            // Display coordinates if available
            if (geoData.lat && geoData.lon) {
                displayCoordinates(geoData.lat, geoData.lon);
            }
            
            // Expand location section if not already expanded
            const locationSection = document.getElementById('locationSection');
            if (!locationSection.classList.contains('expanded')) {
                toggleSection('location');
            }
            
            // Show success message
            const fieldMessage = document.querySelector('#locationSection .field-message');
            fieldMessage.innerHTML = '<i class="fas fa-check-circle" style="color: #10b981;"></i> Location detected successfully. You can update it if needed.';
        } else {
            // Try GPS location detection as fallback
            tryGPSLocation();
        }
        
    } catch (error) {
        console.error('Location detection failed:', error);
        alert('Could not detect your location. Please fill in your address manually.');
    } finally {
        detectBtn.innerHTML = originalText;
        detectBtn.disabled = false;
    }
}

function toggleSection(sectionName) {
    const section = document.getElementById(sectionName + 'Section');
    // Don't toggle if it's the professional section (required section)
    if (section && !section.classList.contains('required-section')) {
        section.classList.toggle('expanded');
    }
}

async function handleFormSubmission(e) {
    e.preventDefault();
    
    const form = e.target;
    const submitBtn = document.getElementById('registerBtn');
    const btnText = submitBtn.querySelector('.btn-text');
    const btnLoading = submitBtn.querySelector('.btn-loading');
    const resultDiv = document.getElementById('registrationResult');

    // Custom validation
    const specialties = form.querySelectorAll('input[name="engineer_roles[]"]:checked');
    if (specialties.length === 0) {
        resultDiv.className = 'result-message error';
        resultDiv.innerHTML = '<p>Please select at least one engineering specialty.</p>';
        resultDiv.style.display = 'block';
        return;
    }

    const termsAgree = form.querySelector('#terms_agree:checked');
    if (!termsAgree) {
        resultDiv.className = 'result-message error';
        resultDiv.innerHTML = '<p>You must agree to the Terms of Service and Privacy Policy.</p>';
        resultDiv.style.display = 'block';
        return;
    }
    
    // Show loading state
    submitBtn.disabled = true;
    btnText.style.display = 'none';
    btnLoading.style.display = 'inline-flex';
    resultDiv.style.display = 'none';
    
    try {
        // Collect form data
        const formData = new FormData(form);
        const payload = {};
        
        // Convert form data to object
        for (let [key, value] of formData.entries()) {
            if (key === 'engineer_roles[]') {
                if (!payload.engineer_roles) payload.engineer_roles = [];
                payload.engineer_roles.push(value);
            } else {
                payload[key] = value;
            }
        }
        
        // Send registration request
        const response = await fetch('/aec-calculator/api/register_enhanced.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': formData.get('csrf_token')
            },
            body: JSON.stringify(payload)
        });
        
        const result = await response.json();
        
        if (result.success) {
            // Success
            resultDiv.className = 'result-message success';
            resultDiv.innerHTML = `
                <i class="fas fa-check-circle" style="font-size: 2rem; margin-bottom: 10px;"></i>
                <h3>Account Created Successfully!</h3>
                <p>${result.message || 'Please check your email to verify your account before signing in.'}</p>
                <p><a href="login.php" class="auth-link">Go to Login</a></p>
            `;
            
            // Reset form
            form.reset();
            
            // Scroll to result
            resultDiv.scrollIntoView({ behavior: 'smooth' });
            
        } else {
            // Error
            throw new Error(result.error || result.message || 'Registration failed');
        }
        
    } catch (error) {
        // Show error
        resultDiv.className = 'result-message error';
        resultDiv.innerHTML = `
            <i class="fas fa-exclamation-circle" style="font-size: 2rem; margin-bottom: 10px;"></i>
            <h3>Registration Failed</h3>
            <p>${error.message}</p>
        `;
        
        resultDiv.style.display = 'block';
        resultDiv.scrollIntoView({ behavior: 'smooth' });
        
    } finally {
        // Reset button state
        submitBtn.disabled = false;
        btnText.style.display = 'inline-flex';
        btnLoading.style.display = 'none';
    }
}

// GPS Location Detection Function
function tryGPSLocation() {
    if (!navigator.geolocation) {
        throw new Error('Geolocation is not supported by this browser.');
    }

    const detectBtn = document.getElementById('detectLocation');
    
    navigator.geolocation.getCurrentPosition(
        function(position) {
            const lat = position.coords.latitude;
            const lon = position.coords.longitude;
            
            // Display coordinates
            displayCoordinates(lat, lon);
            
            // Update form fields with GPS coordinates
            document.getElementById('country').value = 'GPS Detected';
            document.getElementById('region').value = 'GPS Location';
            document.getElementById('city').value = 'GPS Coordinates';
            document.getElementById('timezone').value = Intl.DateTimeFormat().resolvedOptions().timeZone;
            
            // Set hidden fields
            const geoData = {
                lat: lat,
                lon: lon,
                accuracy: position.coords.accuracy,
                source: 'gps'
            };
            document.getElementById('ip_detected').value = 'gps-' + Date.now();
            document.getElementById('ip_geo_data').value = JSON.stringify(geoData);
            
            // Expand location section if not already expanded
            const locationSection = document.getElementById('locationSection');
            if (!locationSection.classList.contains('expanded')) {
                toggleSection('location');
            }
            
            // Show success message
            const fieldMessage = document.querySelector('#locationSection .field-message');
            fieldMessage.innerHTML = '<i class="fas fa-check-circle" style="color: #10b981;"></i> GPS location detected successfully!';
        },
        function(error) {
            console.error('GPS location error:', error);
            let errorMessage = 'Could not get GPS location. ';
            
            switch(error.code) {
                case error.PERMISSION_DENIED:
                    errorMessage += 'Please allow location access and try again.';
                    break;
                case error.POSITION_UNAVAILABLE:
                    errorMessage += 'Location information is unavailable.';
                    break;
                case error.TIMEOUT:
                    errorMessage += 'Location request timed out.';
                    break;
                default:
                    errorMessage += 'An unknown error occurred.';
                    break;
            }
            
            alert(errorMessage);
        },
        {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 300000 // 5 minutes
        }
    );
}

// Display Coordinates Function
function displayCoordinates(lat, lon) {
    const coordinatesDiv = document.getElementById('addressCoordinates');
    const coordinatesText = document.getElementById('coordinatesText');
    
    // Format coordinates to 6 decimal places
    const formattedCoords = `${lat.toFixed(6)}, ${lon.toFixed(6)}`;
    coordinatesText.textContent = formattedCoords;
    
    // Show coordinates display
    coordinatesDiv.style.display = 'block';
    
    // Add click event to coordinates for copying
    coordinatesText.style.cursor = 'pointer';
    coordinatesText.title = 'Click to copy coordinates';
    coordinatesText.onclick = function() {
        copyCoordinates();
    };
}

// Copy Coordinates Function
async function copyCoordinates() {
    const coordinatesText = document.getElementById('coordinatesText');
    const copyBtn = document.getElementById('copyCoordinates');
    
    try {
        await navigator.clipboard.writeText(coordinatesText.textContent);
        
        // Visual feedback
        const originalText = copyBtn.innerHTML;
        copyBtn.innerHTML = '<i class="fas fa-check"></i> Copied!';
        copyBtn.style.background = '#10b981';
        copyBtn.style.color = 'white';
        copyBtn.style.borderColor = '#10b981';
        
        setTimeout(() => {
            copyBtn.innerHTML = originalText;
            copyBtn.style.background = '';
            copyBtn.style.color = '';
            copyBtn.style.borderColor = '';
        }, 2000);
        
    } catch (err) {
        // Fallback for older browsers
        console.warn('Clipboard API not supported, using fallback');
        
        const textArea = document.createElement('textarea');
        textArea.value = coordinatesText.textContent;
        document.body.appendChild(textArea);
        textArea.select();
        
        try {
            document.execCommand('copy');
            
            // Visual feedback
            const originalText = copyBtn.innerHTML;
            copyBtn.innerHTML = '<i class="fas fa-check"></i> Copied!';
            copyBtn.style.background = '#10b981';
            copyBtn.style.color = 'white';
            copyBtn.style.borderColor = '#10b981';
            
            setTimeout(() => {
                copyBtn.innerHTML = originalText;
                copyBtn.style.background = '';
                copyBtn.style.color = '';
                copyBtn.style.borderColor = '';
            }, 2000);
            
        } catch (fallbackErr) {
            alert('Could not copy coordinates. Please copy manually: ' + coordinatesText.textContent);
        }
        
        document.body.removeChild(textArea);
    }
}
</script>

<style>
/* Address Group Styles */
.address-group {
    margin-bottom: 1.5rem;
}

.address-input {
    display: flex;
    gap: 0.75rem;
    margin-bottom: 0.75rem;
    position: relative;
}

.address-input input.form-control {
    flex: 1;
    border: 1px solid var(--border-color, #dee2e6);
    border-radius: 6px;
    padding: 0.625rem 1rem;
    font-size: 0.95rem;
    transition: border-color 0.2s, box-shadow 0.2s;
}

.address-input input.form-control:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(var(--primary-rgb), 0.15);
}

.location-detect-btn {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.625rem 1.25rem;
    background: linear-gradient(to bottom, var(--secondary-color) 0%, var(--secondary-dark) 100%);
    border: none;
    border-radius: 6px;
    color: white;
    font-size: 0.95rem;
    font-weight: 500;
    white-space: nowrap;
    transition: all 0.2s;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.location-detect-btn:hover {
    background: linear-gradient(to bottom, var(--secondary-dark) 0%, var(--secondary-darker) 100%);
    transform: translateY(-1px);
    box-shadow: 0 3px 6px rgba(0, 0, 0, 0.15);
}

.location-detect-btn:active {
    transform: translateY(0);
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
}

.address-coordinates {
    background: #f8f9fa;
    padding: 0.75rem;
    border-radius: 6px;
    margin-bottom: 0.75rem;
    border: 1px solid var(--border-color, #dee2e6);
}

.coordinates-display {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.coordinate-label {
    font-weight: 600;
    color: var(--text-color-secondary, #6c757d);
}

.coordinate-value {
    font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace;
    color: var(--secondary-color);
    background: rgba(var(--secondary-rgb), 0.1);
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.9rem;
}

.field-message {
    color: var(--text-color-secondary, #6c757d);
    font-size: 0.9rem;
    margin-top: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
</style>
<?php
require_once dirname(__DIR__, 4) . '/includes/footer.php';
?>
