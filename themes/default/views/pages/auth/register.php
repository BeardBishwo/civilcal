<?php
/**
 * Registration Page Content
 * Integrates with the existing auth.php layout
 */

// Set page metadata
$page_title = 'Create Account';
$auth_title = 'Join Bishwo Calculator';
$auth_subtitle = 'Create your account to access all engineering tools';
$show_login_link = true;
$show_forgot_password = false;
?>

<div class="register-form">
    <form id="registerForm" method="POST" action="/auth/register" autocomplete="on">
        <div class="form-section">
            <h3>Personal Information</h3>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="firstName" class="form-label">First Name</label>
                    <input 
                        type="text" 
                        id="firstName" 
                        name="first_name" 
                        class="form-input" 
                        placeholder="John" 
                        required 
                        autocomplete="given-name"
                    >
                </div>
                <div class="form-group">
                    <label for="lastName" class="form-label">Last Name</label>
                    <input 
                        type="text" 
                        id="lastName" 
                        name="last_name" 
                        class="form-input" 
                        placeholder="Doe" 
                        required 
                        autocomplete="family-name"
                    >
                </div>
            </div>

            <div class="form-group">
                <label for="registerEmail" class="form-label">Email Address</label>
                <input 
                    type="email" 
                    id="registerEmail" 
                    name="email" 
                    class="form-input" 
                    placeholder="engineer@example.com" 
                    required 
                    autocomplete="email"
                >
                <div class="field-help">We\'ll never share your email with anyone else</div>
            </div>

            <div class="form-group">
                <label for="profession" class="form-label">Profession</label>
                <select 
                    id="profession" 
                    name="profession" 
                    class="form-input" 
                    required
                >
                    <option value="">Select your profession</option>
                    <option value="civil_engineer">Civil Engineer</option>
                    <option value="electrical_engineer">Electrical Engineer</option>
                    <option value="structural_engineer">Structural Engineer</option>
                    <option value="mechanical_engineer">Mechanical Engineer</option>
                    <option value="hvac_engineer">HVAC Engineer</option>
                    <option value="plumbing_engineer">Plumbing Engineer</option>
                    <option value="fire_protection_engineer">Fire Protection Engineer</option>
                    <option value="project_manager">Project Manager</option>
                    <option value="contractor">Contractor</option>
                    <option value="architect">Architect</option>
                    <option value="student">Student</option>
                    <option value="consultant">Consultant</option>
                    <option value="other">Other</option>
                </select>
            </div>
        </div>

        <div class="form-section">
            <h3>Security</h3>
            
            <div class="form-group">
                <label for="registerPassword" class="form-label">Password</label>
                <div class="password-field">
                    <input 
                        type="password" 
                        id="registerPassword" 
                        name="password" 
                        class="form-input" 
                        placeholder="Create a strong password" 
                        required 
                        autocomplete="new-password"
                    >
                    <button type="button" class="password-toggle" onclick="togglePassword('registerPassword')">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <div id="passwordStrength" class="password-strength">
                    <div class="strength-meter">
                        <div class="strength-bar"></div>
                    </div>
                    <div class="strength-text">Password strength: Not set</div>
                </div>
                <div class="field-help">Minimum 8 characters with letters and numbers</div>
            </div>

            <div class="form-group">
                <label for="confirmPassword" class="form-label">Confirm Password</label>
                <div class="password-field">
                    <input 
                        type="password" 
                        id="confirmPassword" 
                        name="password_confirmation" 
                        class="form-input" 
                        placeholder="Confirm your password" 
                        required 
                        autocomplete="new-password"
                    >
                    <button type="button" class="password-toggle" onclick="togglePassword('confirmPassword')">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <div id="passwordMatch" class="field-help"></div>
            </div>
        </div>

        <div class="form-section required-section">
            <h3>Terms & Conditions</h3>
            
            <div class="form-group">
                <div class="agreement-wrapper">
                    <div class="checkbox-item">
                        <input 
                            type="checkbox" 
                            id="agreeTerms" 
                            name="agree_terms" 
                            class="checkbox-input" 
                            required
                        >
                        <label for="agreeTerms" class="checkbox-label">
                            I agree to the <a href="/terms" class="auth-link">Terms of Service</a> 
                            and <a href="/privacy" class="auth-link">Privacy Policy</a>
                        </label>
                    </div>
                    
                    <div class="checkbox-item">
                        <input 
                            type="checkbox" 
                            id="newsletter" 
                            name="newsletter" 
                            class="checkbox-input"
                        >
                        <label for="newsletter" class="checkbox-label">
                            Send me engineering tips and calculator updates
                        </label>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-user-plus"></i>
                Create Account
            </button>
        </div>
    </form>
</div>

<!-- Registration Benefits -->
<div class="register-benefits">
    <h4>Why Join Bishwo Calculator?</h4>
    <div class="benefit-items">
        <div class="benefit-item">
            <div class="benefit-icon">
                <i class="fas fa-calculator"></i>
            </div>
            <div class="benefit-text">
                <h5>Advanced Calculators</h5>
                <p>Access to 50+ professional engineering calculators</p>
            </div>
        </div>
        
        <div class="benefit-item">
            <div class="benefit-icon">
                <i class="fas fa-save"></i>
            </div>
            <div class="benefit-text">
                <h5>Save Your Work</h5>
                <p>Keep track of your calculations and projects</p>
            </div>
        </div>
        
        <div class="benefit-item">
            <div class="benefit-icon">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="benefit-text">
                <h5>Project Analytics</h5>
                <p>Detailed reports and analysis of your calculations</p>
            </div>
        </div>
        
        <div class="benefit-item">
            <div class="benefit-icon">
                <i class="fas fa-headset"></i>
            </div>
            <div class="benefit-text">
                <h5>Priority Support</h5>
                <p>Get help from our engineering experts</p>
            </div>
        </div>
    </div>
</div>

<?php
// Include the auth layout
include 'themes/default/views/layouts/auth.php';
?>
