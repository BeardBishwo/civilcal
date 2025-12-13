<?php
$page_title = 'Create Account - Civil Cal';
require_once dirname(__DIR__, 4) . '/themes/default/views/partials/header.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$csrf_token = \App\Services\Security::generateCsrfToken();

// Engineering specialties
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
?>

<style>
    /* Ultra-Premium Register Page Styles */
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

    :root {
        --primary: #6366f1;
        --primary-dark: #4f46e5;
        --secondary: #8b5cf6;
        --accent: #dc2626;
        --success: #10b981;
        --error: #ef4444;
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        overflow-x: hidden;
    }

    /* Ultra-Premium Container */
    .ultra-premium-container {
        min-height: 100vh;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #dc2626 100%);
        position: relative;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px 20px;
    }

    /* Animated Particle Background */
    .particles {
        position: absolute;
        width: 100%;
        height: 100%;
        overflow: hidden;
        z-index: 0;
    }

    .particle {
        position: absolute;
        width: 4px;
        height: 4px;
        background: rgba(255, 255, 255, 0.5);
        border-radius: 50%;
        animation: float-particle 20s infinite;
    }

    @keyframes float-particle {
        0%, 100% {
            transform: translate(0, 0) scale(1);
            opacity: 0;
        }
        10% {
            opacity: 1;
        }
        90% {
            opacity: 1;
        }
        100% {
            transform: translate(var(--tx), var(--ty)) scale(0);
            opacity: 0;
        }
    }

    /* Mesh Gradient Overlay */
    .mesh-gradient {
        position: absolute;
        width: 100%;
        height: 100%;
        background: 
            radial-gradient(at 20% 30%, rgba(99, 102, 241, 0.3) 0px, transparent 50%),
            radial-gradient(at 80% 70%, rgba(139, 92, 246, 0.3) 0px, transparent 50%),
            radial-gradient(at 50% 50%, rgba(220, 38, 38, 0.2) 0px, transparent 50%);
        filter: blur(60px);
        z-index: 0;
    }

    /* Main Content */
    .register-wrapper {
        position: relative;
        z-index: 10;
        width: 100%;
        max-width: 900px;
        animation: fadeInUp 0.8s ease;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .ultra-card {
        background: rgba(255, 255, 255, 0.98);
        backdrop-filter: blur(30px);
        border-radius: 32px;
        padding: 50px;
        color: #0f172a;
        box-shadow: 
            0 50px 100px rgba(0, 0, 0, 0.2),
            0 0 0 1px rgba(255, 255, 255, 0.3),
            inset 0 1px 0 rgba(255, 255, 255, 0.8);
        position: relative;
        overflow: hidden;
        max-height: 85vh;
        overflow-y: auto;
    }

    .ultra-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 6px;
        background: linear-gradient(90deg, #6366f1, #8b5cf6, #dc2626, #6366f1);
        background-size: 200% 100%;
        animation: shimmer 3s linear infinite;
    }

    @keyframes shimmer {
        0% { background-position: 0% 0%; }
        100% { background-position: 200% 0%; }
    }

    .card-header {
        text-align: center;
        margin-bottom: 40px;
    }

    .header-icon {
        width: 100px;
        height: 100px;
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #dc2626 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 25px;
        font-size: 3rem;
        color: white;
        box-shadow: 
            0 20px 40px rgba(99, 102, 241, 0.4),
            0 0 0 8px rgba(99, 102, 241, 0.1);
        animation: pulse 2s ease-in-out infinite;
    }

    @keyframes pulse {
        0%, 100% {
            transform: scale(1);
            box-shadow: 
                0 20px 40px rgba(99, 102, 241, 0.4),
                0 0 0 8px rgba(99, 102, 241, 0.1);
        }
        50% {
            transform: scale(1.05);
            box-shadow: 
                0 25px 50px rgba(99, 102, 241, 0.5),
                0 0 0 12px rgba(99, 102, 241, 0.15);
        }
    }

    .card-header h1 {
        font-size: 2.5rem;
        font-weight: 800;
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #dc2626 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 12px;
    }

    .card-header p {
        color: #64748b;
        font-size: 1rem;
        line-height: 1.6;
    }

    /* Progress Indicator */
    .progress-indicator {
        display: flex;
        justify-content: space-between;
        margin-bottom: 40px;
        position: relative;
    }

    .progress-line {
        position: absolute;
        top: 20px;
        left: 0;
        right: 0;
        height: 4px;
        background: #e2e8f0;
        z-index: 0;
    }

    .progress-line-fill {
        height: 100%;
        background: linear-gradient(90deg, #6366f1, #8b5cf6, #dc2626);
        width: 0%;
        transition: width 0.5s ease;
    }

    .progress-step {
        position: relative;
        z-index: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        flex: 1;
    }

    .step-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: white;
        border: 4px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        color: #94a3b8;
        transition: all 0.3s ease;
        margin-bottom: 8px;
    }

    .progress-step.active .step-circle {
        border-color: #6366f1;
        background: #6366f1;
        color: white;
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.2);
    }

    .progress-step.completed .step-circle {
        border-color: #10b981;
        background: #10b981;
        color: white;
    }

    .step-label {
        font-size: 0.75rem;
        color: #94a3b8;
        font-weight: 600;
        text-align: center;
    }

    .progress-step.active .step-label {
        color: #6366f1;
    }

    /* Form Steps */
    .form-step {
        display: none;
        animation: fadeIn 0.5s ease;
    }

    .form-step.active {
        display: block;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateX(20px); }
        to { opacity: 1; transform: translateX(0); }
    }

    /* Form Groups */
    .form-group {
        margin-bottom: 24px;
    }

    #registerForm .form-label {
        display: block;
        font-weight: 700;
        color: #0f172a !important; /* Override global header styles */
        margin-bottom: 12px;
        font-size: 0.875rem;
        letter-spacing: 0.3px;
    }

    .input-group {
        position: relative;
    }

    .form-input {
        width: 100%;
        padding: 16px 20px 16px 55px;
        border: 2px solid #e2e8f0;
        border-radius: 16px;
        font-size: 1rem;
        font-family: inherit;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        background: white;
        color: #0f172a;
        font-weight: 500;
    }

    .form-input::placeholder {
        color: #64748b;
        font-weight: 400;
    }

    .form-input:focus {
        outline: none;
        border-color: #6366f1;
        background: white;
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
        transform: translateY(-2px);
    }

    .input-icon {
        position: absolute;
        left: 20px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: 1.2rem;
        transition: all 0.3s ease;
        pointer-events: none;
    }

    .form-input:focus ~ .input-icon {
        color: #6366f1;
        transform: translateY(-50%) scale(1.1);
    }

    .password-toggle {
        position: absolute;
        right: 20px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #94a3b8;
        cursor: pointer;
        font-size: 1.2rem;
        padding: 8px;
        border-radius: 8px;
        transition: all 0.2s ease;
    }

    .password-toggle:hover {
        color: #6366f1;
        background: #f1f5f9;
    }

    /* Password Strength */
    .password-strength {
        margin-top: 15px;
        padding: 15px;
        background: #f8f9fa;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
    }

    .strength-bar {
        height: 6px;
        background: #e2e8f0;
        border-radius: 3px;
        overflow: hidden;
        margin-bottom: 10px;
    }

    .strength-progress {
        height: 100%;
        width: 0%;
        border-radius: 3px;
        transition: all 0.3s ease;
        background: #ef4444;
    }

    .strength-text {
        font-size: 0.9rem;
        font-weight: 600;
        margin-bottom: 10px;
        color: #374151;
    }

    .password-requirements {
        display: grid;
        gap: 8px;
    }

    .requirement {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.85rem;
        color: #6b7280;
    }

    .requirement.met {
        color: #10b981;
    }

    .requirement-icon {
        font-size: 0.9rem;
    }

    /* Specialty Grid */
    .specialty-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 12px;
        margin: 20px 0;
    }

    .specialty-card {
        padding: 16px;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.3s ease;
        background: white;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .specialty-card:hover {
        border-color: #6366f1;
        background: #f8fafc;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.15);
    }

    .specialty-card.selected {
        border-color: #6366f1;
        background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
    }

    .specialty-checkbox {
        width: 20px;
        height: 20px;
        border: 2px solid #6366f1;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .specialty-card.selected .specialty-checkbox {
        background: #6366f1;
    }

    .specialty-card.selected .specialty-checkbox::after {
        content: '✓';
        color: white;
        font-size: 14px;
        font-weight: bold;
    }

    .specialty-label {
        font-size: 0.95rem;
        font-weight: 500;
        color: #374151;
    }

    /* Form Row */
    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 24px;
    }

    /* Select */
    select.form-input {
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%236366f1' viewBox='0 0 16 16'%3E%3Cpath d='M8 10.5l-4-4h8l-4 4z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 20px center;
        padding-right: 50px;
    }

    /* Navigation Buttons */
    .step-navigation {
        display: flex;
        justify-content: space-between;
        margin-top: 30px;
        gap: 15px;
    }

    .btn {
        padding: 16px 32px;
        border-radius: 12px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 10px;
    }

    .btn-secondary {
        background: #e2e8f0;
        color: #475569;
    }

    .btn-secondary:hover {
        background: #cbd5e1;
        transform: translateY(-2px);
    }

    .btn-primary {
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #dc2626 100%);
        background-size: 200% 100%;
        color: white;
        box-shadow: 0 10px 30px rgba(99, 102, 241, 0.4);
    }

    .btn-primary:hover {
        background-position: 100% 0;
        transform: translateY(-3px);
        box-shadow: 0 15px 40px rgba(99, 102, 241, 0.5);
    }

    .btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }

    /* Checkbox */
    .checkbox-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 16px;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-bottom: 12px;
    }

    .checkbox-item:hover {
        border-color: #6366f1;
        background: #f8fafc;
    }

    .checkbox-item input[type="checkbox"] {
        width: 20px;
        height: 20px;
        cursor: pointer;
        accent-color: #6366f1;
    }

    .checkbox-text {
        font-size: 0.95rem;
        color: #374151;
        line-height: 1.5;
    }

    .checkbox-text a {
        color: #6366f1;
        text-decoration: none;
        font-weight: 600;
    }

    .checkbox-text a:hover {
        text-decoration: underline;
    }

    /* Field Hint */
    .field-hint {
        font-size: 0.85rem;
        color: #6b7280;
        margin-top: 6px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    /* Username Status */
    .username-status {
        position: absolute;
        right: 20px;
        top: 50%;
        transform: translateY(-50%);
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .username-status.checking {
        color: #3b82f6;
    }

    .username-status.available {
        color: #10b981;
    }

    .username-status.taken {
        color: #ef4444;
    }

    .username-status i {
        font-size: 1rem;
    }

    .username-status.checking i {
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    /* Username Suggestions */
    .username-suggestions {
        margin-top: 12px;
        padding: 15px;
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        border: 2px solid #f59e0b;
        border-radius: 12px;
        animation: slideDown 0.3s ease;
    }

    .suggestions-label {
        font-size: 0.9rem;
        color: #92400e;
        font-weight: 600;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .suggestions-list {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .suggestion-item {
        background: #6366f1;
        color: white;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 0.875rem;
        cursor: pointer;
        transition: all 0.2s ease;
        font-weight: 500;
        border: 2px solid transparent;
    }

    .suggestion-item:hover {
        background: #4f46e5;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.4);
        border-color: #dc2626;
    }

    .suggestion-item:active {
        transform: translateY(0);
    }


    /* Result Message */
    .result-message {
        margin: 20px 0;
        padding: 16px;
        border-radius: 12px;
        font-size: 0.95rem;
        animation: slideDown 0.3s ease;
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .result-message.success {
        background: #d1fae5;
        color: #065f46;
        border: 2px solid #10b981;
    }

    .result-message.error {
        background: #fee2e2;
        color: #991b1b;
        border: 2px solid #ef4444;
    }

    /* Footer */
    .card-footer {
        text-align: center;
        margin-top: 30px;
        padding-top: 25px;
        border-top: 1px solid #e2e8f0;
        color: #64748b;
        font-size: 0.95rem;
    }

    .card-footer a {
        color: #6366f1;
        text-decoration: none;
        font-weight: 600;
        transition: color 0.2s ease;
    }

    .card-footer a:hover {
        color: #4f46e5;
        text-decoration: underline;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .ultra-card {
            padding: 35px 25px;
        }

        .card-header h1 {
            font-size: 2rem;
        }

        .header-icon {
            width: 80px;
            height: 80px;
            font-size: 2.5rem;
        }

        .form-row {
            grid-template-columns: 1fr;
        }

        .specialty-grid {
            grid-template-columns: 1fr;
        }

        .progress-indicator {
            overflow-x: auto;
        }

        .step-label {
            font-size: 0.65rem;
        }
    }
</style>

<div class="ultra-premium-container">
    <!-- Particle Background -->
    <div class="particles" id="particles"></div>
    
    <!-- Mesh Gradient -->
    <div class="mesh-gradient"></div>

    <div class="register-wrapper">
        <div class="ultra-card">
            <div class="card-header">
                <div class="header-icon">
                    <i class="fas fa-user-plus"></i>
                </div>
                <h1>Create Account</h1>
                <p>Join thousands of engineers using Civil Calculator</p>
            </div>

            <!-- Progress Indicator -->
            <div class="progress-indicator">
                <div class="progress-line">
                    <div class="progress-line-fill" id="progressFill"></div>
                </div>
                <div class="progress-step active" data-step="1">
                    <div class="step-circle">1</div>
                    <div class="step-label">Account</div>
                </div>
                <div class="progress-step" data-step="2">
                    <div class="step-circle">2</div>
                    <div class="step-label">Profile</div>
                </div>
                <div class="progress-step" data-step="3">
                    <div class="step-circle">3</div>
                    <div class="step-label">Details</div>
                </div>
                <div class="progress-step" data-step="4">
                    <div class="step-circle">4</div>
                    <div class="step-label">Confirm</div>
                </div>
            </div>

            <form id="registerForm">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">

                <!-- Step 1: Account Basics -->
                <div class="form-step active" data-step="1">
                    <div class="form-group">
                        <label class="form-label" for="username">Username</label>
                        <div class="input-group">
                            <input 
                                type="text" 
                                id="username" 
                                name="username" 
                                class="form-input" 
                                placeholder="Choose a unique username"
                                pattern="[a-zA-Z0-9_]{3,20}"
                                required>
                            <i class="fas fa-user input-icon"></i>
                            <div class="username-status" id="usernameStatus" style="display: none;">
                                <i class="fas fa-circle-notch"></i>
                                <span id="statusText">Checking...</span>
                            </div>
                        </div>
                        <div class="field-hint">
                            <i class="fas fa-info-circle"></i>
                            <span>3-20 characters (letters, numbers, underscore)</span>
                        </div>
                        <div id="usernameSuggestions" style="display: none;"></div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="email">Email Address</label>
                        <div class="input-group">
                            <input 
                                type="email" 
                                id="email" 
                                name="email" 
                                class="form-input" 
                                placeholder="your.email@company.com"
                                required>
                            <i class="fas fa-envelope input-icon"></i>
                        </div>
                        <div class="field-hint">
                            <i class="fas fa-info-circle"></i>
                            <span>We'll send a verification email</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="password">Password</label>
                        <div class="input-group">
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                class="form-input" 
                                placeholder="Create a strong password"
                                minlength="8"
                                required>
                            <i class="fas fa-lock input-icon"></i>
                            <button type="button" class="password-toggle" id="togglePassword">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        
                        <div id="passwordStrength" class="password-strength" style="display: none;">
                            <div class="strength-bar">
                                <div class="strength-progress" id="strengthProgress"></div>
                            </div>
                            <div class="strength-text" id="strengthText">Password strength</div>
                            <div class="password-requirements">
                                <div class="requirement" data-requirement="length">
                                    <i class="fas fa-circle requirement-icon"></i>
                                    <span>At least 8 characters</span>
                                </div>
                                <div class="requirement" data-requirement="uppercase">
                                    <i class="fas fa-circle requirement-icon"></i>
                                    <span>One uppercase letter</span>
                                </div>
                                <div class="requirement" data-requirement="lowercase">
                                    <i class="fas fa-circle requirement-icon"></i>
                                    <span>One lowercase letter</span>
                                </div>
                                <div class="requirement" data-requirement="number">
                                    <i class="fas fa-circle requirement-icon"></i>
                                    <span>One number</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Professional Profile -->
                <div class="form-step" data-step="2">
                    <div class="form-group">
                        <label class="form-label">Engineering Specialties</label>
                        <div class="specialty-grid">
                            <?php foreach ($specialties as $value => $label): ?>
                                <div class="specialty-card" data-value="<?php echo $value; ?>">
                                    <div class="specialty-checkbox"></div>
                                    <div class="specialty-label"><?php echo $label; ?></div>
                                    <input type="checkbox" name="engineer_roles[]" value="<?php echo $value; ?>" style="display: none;">
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="field-hint">
                            <i class="fas fa-info-circle"></i>
                            <span>Select all that apply</span>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="first_name">First Name</label>
                            <div class="input-group">
                                <input 
                                    type="text" 
                                    id="first_name" 
                                    name="first_name" 
                                    class="form-input" 
                                    placeholder="John"
                                    required>
                                <i class="fas fa-user input-icon"></i>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="last_name">Last Name</label>
                            <div class="input-group">
                                <input 
                                    type="text" 
                                    id="last_name" 
                                    name="last_name" 
                                    class="form-input" 
                                    placeholder="Smith"
                                    required>
                                <i class="fas fa-user input-icon"></i>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="role">Professional Role</label>
                        <div class="input-group">
                            <select id="role" name="role" class="form-input">
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
                            <i class="fas fa-briefcase input-icon"></i>
                        </div>
                    </div>
                </div>

                <!-- Step 3: Additional Details -->
                <div class="form-step" data-step="3">
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="phone_number">Phone Number</label>
                            <div class="input-group">
                                <input 
                                    type="tel" 
                                    id="phone_number" 
                                    name="phone_number" 
                                    class="form-input" 
                                    placeholder="+1 (555) 123-4567">
                                <i class="fas fa-phone input-icon"></i>
                            </div>
                            <div class="field-hint">
                                <i class="fas fa-info-circle"></i>
                                <span>Optional</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="company">Company</label>
                            <div class="input-group">
                                <input 
                                    type="text" 
                                    id="company" 
                                    name="company" 
                                    class="form-input" 
                                    placeholder="Your company">
                                <i class="fas fa-building input-icon"></i>
                            </div>
                            <div class="field-hint">
                                <i class="fas fa-info-circle"></i>
                                <span>Optional</span>
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="country">Country</label>
                            <div class="input-group">
                                <input 
                                    type="text" 
                                    id="country" 
                                    name="country" 
                                    class="form-input" 
                                    placeholder="Auto-detected">
                                <i class="fas fa-globe input-icon"></i>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="preferred_units">Preferred Units</label>
                            <div class="input-group">
                                <select id="preferred_units" name="preferred_units" class="form-input">
                                    <option value="metric">Metric (SI)</option>
                                    <option value="imperial">Imperial (US)</option>
                                </select>
                                <i class="fas fa-ruler input-icon"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 4: Review & Confirm -->
                <div class="form-step" data-step="4">
                    <div class="checkbox-item">
                        <input type="checkbox" id="terms_agree" name="terms_agree" required>
                        <div class="checkbox-text">
                            I agree to the <a href="<?php echo app_base_url('terms'); ?>" target="_blank">Terms of Service</a> 
                            and <a href="<?php echo app_base_url('privacy'); ?>" target="_blank">Privacy Policy</a>
                        </div>
                    </div>

                    <div class="checkbox-item">
                        <input type="checkbox" id="marketing_agree" name="marketing_agree">
                        <div class="checkbox-text">
                            📧 Send me engineering tips and product updates
                        </div>
                    </div>

                    <div id="registerResult" class="result-message" style="display: none;"></div>
                </div>

                <!-- Navigation Buttons -->
                <div class="step-navigation">
                    <button type="button" class="btn btn-secondary" id="prevBtn" style="display: none;">
                        <i class="fas fa-arrow-left"></i>
                        Previous
                    </button>
                    <button type="button" class="btn btn-primary" id="nextBtn">
                        Next
                        <i class="fas fa-arrow-right"></i>
                    </button>
                    <button type="submit" class="btn btn-primary" id="submitBtn" style="display: none;">
                        <i class="fas fa-check"></i>
                        Create Account
                    </button>
                </div>
            </form>

            <div class="card-footer">
                <p>Already have an account? <a href="<?php echo app_base_url('login'); ?>">Sign In</a></p>
            </div>
        </div>
    </div>
</div>

<script>
// Create particle animation
function createParticles() {
    const particlesContainer = document.getElementById('particles');
    const particleCount = 50;
    
    for (let i = 0; i < particleCount; i++) {
        const particle = document.createElement('div');
        particle.className = 'particle';
        
        const startX = Math.random() * 100;
        const startY = Math.random() * 100;
        const endX = startX + (Math.random() - 0.5) * 100;
        const endY = startY - Math.random() * 100;
        
        particle.style.left = startX + '%';
        particle.style.top = startY + '%';
        particle.style.setProperty('--tx', endX + 'vw');
        particle.style.setProperty('--ty', endY + 'vh');
        particle.style.animationDelay = Math.random() * 20 + 's';
        particle.style.animationDuration = (15 + Math.random() * 10) + 's';
        
        particlesContainer.appendChild(particle);
    }
}

let currentStep = 1;
const totalSteps = 4;

document.addEventListener('DOMContentLoaded', function() {
    createParticles();
    
    // Password toggle
    document.getElementById('togglePassword').addEventListener('click', function() {
        const passwordInput = document.getElementById('password');
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        this.innerHTML = type === 'password' ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
    });
    
    // Password strength
    document.getElementById('password').addEventListener('input', function() {
        checkPasswordStrength(this.value);
    });
    
    document.getElementById('password').addEventListener('focus', function() {
        document.getElementById('passwordStrength').style.display = 'block';
    });
    
    // Username availability check
    let usernameTimeout;
    const usernameInput = document.getElementById('username');
    
    usernameInput.addEventListener('input', function() {
        clearTimeout(usernameTimeout);
        const username = this.value.trim();
        
        if (username.length < 3) {
            hideUsernameStatus();
            return;
        }
        
        if (username.length > 20 || !/^[a-zA-Z0-9_]+$/.test(username)) {
            showUsernameStatus('Invalid format', 'taken');
            return;
        }
        
        showUsernameStatus('Checking...', 'checking');
        usernameTimeout = setTimeout(() => checkUsernameAvailability(username), 500);
    });
    
    // Specialty cards
    document.querySelectorAll('.specialty-card').forEach(card => {
        card.addEventListener('click', function() {
            this.classList.toggle('selected');
            const checkbox = this.querySelector('input[type="checkbox"]');
            checkbox.checked = !checkbox.checked;
        });
    });
    
    // Navigation
    document.getElementById('prevBtn').addEventListener('click', () => changeStep(-1));
    document.getElementById('nextBtn').addEventListener('click', () => changeStep(1));
    document.getElementById('registerForm').addEventListener('submit', handleSubmit);
});

function changeStep(direction) {
    const newStep = currentStep + direction;
    
    if (newStep < 1 || newStep > totalSteps) return;
    
    // Validate current step before proceeding
    if (direction > 0 && !validateStep(currentStep)) return;
    
    // Hide current step
    document.querySelector(`.form-step[data-step="${currentStep}"]`).classList.remove('active');
    document.querySelector(`.progress-step[data-step="${currentStep}"]`).classList.remove('active');
    if (direction > 0) {
        document.querySelector(`.progress-step[data-step="${currentStep}"]`).classList.add('completed');
    }
    
    // Show new step
    currentStep = newStep;
    document.querySelector(`.form-step[data-step="${currentStep}"]`).classList.add('active');
    document.querySelector(`.progress-step[data-step="${currentStep}"]`).classList.add('active');
    
    // Update progress bar
    const progress = ((currentStep - 1) / (totalSteps - 1)) * 100;
    document.getElementById('progressFill').style.width = progress + '%';
    
    // Update buttons
    document.getElementById('prevBtn').style.display = currentStep === 1 ? 'none' : 'inline-flex';
    document.getElementById('nextBtn').style.display = currentStep === totalSteps ? 'none' : 'inline-flex';
    document.getElementById('submitBtn').style.display = currentStep === totalSteps ? 'inline-flex' : 'none';
    
    // Scroll to top
    document.querySelector('.ultra-card').scrollTop = 0;
}

function validateStep(step) {
    const stepElement = document.querySelector(`.form-step[data-step="${step}"]`);
    const inputs = stepElement.querySelectorAll('input[required], select[required]');
    
    for (let input of inputs) {
        if (!input.value.trim()) {
            input.focus();
            return false;
        }
    }
    
    // Step 2: Check at least one specialty selected
    if (step === 2) {
        const specialties = document.querySelectorAll('input[name="engineer_roles[]"]:checked');
        if (specialties.length === 0) {
            alert('Please select at least one engineering specialty');
            return false;
        }
    }
    
    return true;
}

function checkPasswordStrength(password) {
    const strengthMeter = document.getElementById('passwordStrength');
    const strengthProgress = document.getElementById('strengthProgress');
    const strengthText = document.getElementById('strengthText');
    const requirements = document.querySelectorAll('.requirement');
    
    if (!password) {
        strengthMeter.style.display = 'none';
        return;
    }
    
    strengthMeter.style.display = 'block';
    
    const checks = {
        length: password.length >= 8,
        uppercase: /[A-Z]/.test(password),
        lowercase: /[a-z]/.test(password),
        number: /[0-9]/.test(password)
    };
    
    requirements.forEach(req => {
        const type = req.dataset.requirement;
        if (checks[type]) {
            req.classList.add('met');
            req.querySelector('.requirement-icon').className = 'fas fa-check-circle requirement-icon';
        } else {
            req.classList.remove('met');
            req.querySelector('.requirement-icon').className = 'fas fa-circle requirement-icon';
        }
    });
    
    const score = Object.values(checks).filter(Boolean).length;
    const percentage = (score / 4) * 100;
    
    strengthProgress.style.width = percentage + '%';
    
    let strengthLevel = '';
    let color = '';
    
    if (score <= 1) {
        strengthLevel = 'Weak';
        color = '#ef4444';
    } else if (score <= 2) {
        strengthLevel = 'Fair';
        color = '#f59e0b';
    } else if (score <= 3) {
        strengthLevel = 'Good';
        color = '#22c55e';
    } else {
        strengthLevel = 'Excellent';
        color = '#10b981';
    }
    
    strengthText.textContent = `Password strength: ${strengthLevel}`;
    strengthText.style.color = color;
    strengthProgress.style.background = color;
}

async function handleSubmit(e) {
    e.preventDefault();
    
    if (!validateStep(4)) return;
    
    const submitBtn = document.getElementById('submitBtn');
    const resultDiv = document.getElementById('registerResult');
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating Account...';
    
    try {
        const formData = new FormData(e.target);
        const payload = {};
        
        for (let [key, value] of formData.entries()) {
            if (key === 'engineer_roles[]') {
                if (!payload.engineer_roles) payload.engineer_roles = [];
                payload.engineer_roles.push(value);
            } else {
                payload[key] = value;
            }
        }
        
        payload.terms_agree = document.getElementById('terms_agree').checked;
        payload.marketing_agree = document.getElementById('marketing_agree').checked;
        
        const response = await fetch('<?php echo app_base_url('api/register'); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': formData.get('csrf_token')
            },
            body: JSON.stringify(payload)
        });
        
        const result = await response.json();
        
        if (result.success) {
            resultDiv.className = 'result-message success';
            resultDiv.innerHTML = `
                <i class="fas fa-check-circle" style="font-size: 1.5rem;"></i>
                <div>
                    <strong>Account Created!</strong><br>
                    <small>${result.message || 'Please check your email to verify your account.'}</small>
                </div>
            `;
            resultDiv.style.display = 'flex';
            
            setTimeout(() => {
                window.location.href = '<?php echo app_base_url('login'); ?>';
            }, 2000);
        } else {
            throw new Error(result.error || 'Registration failed');
        }
    } catch (error) {
        resultDiv.className = 'result-message error';
        resultDiv.innerHTML = `
            <i class="fas fa-exclamation-circle" style="font-size: 1.5rem;"></i>
            <div>
                <strong>Registration Failed</strong><br>
                <small>${error.message}</small>
            </div>
        `;
        resultDiv.style.display = 'flex';
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-check"></i> Create Account';
    }
}

// Username checking helper functions
function showUsernameStatus(text, status) {
    const statusDiv = document.getElementById('usernameStatus');
    const statusText = document.getElementById('statusText');
    const statusIcon = statusDiv.querySelector('i');
    
    statusDiv.style.display = 'flex';
    statusDiv.className = 'username-status ' + status;
    statusText.textContent = text;
    
    if (status === 'checking') {
        statusIcon.className = 'fas fa-spinner';
    } else if (status === 'available') {
        statusIcon.className = 'fas fa-check-circle';
    } else if (status === 'taken') {
        statusIcon.className = 'fas fa-times-circle';
    }
}

function hideUsernameStatus() {
    document.getElementById('usernameStatus').style.display = 'none';
    document.getElementById('usernameSuggestions').style.display = 'none';
}

async function checkUsernameAvailability(username) {
    try {
        const response = await fetch('<?php echo app_base_url('api/check-username'); ?>?username=' + encodeURIComponent(username));
        
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        
        const data = await response.json();
        
        if (data.error) {
            showUsernameStatus('Error checking', 'taken');
            return;
        }
        
        if (data.available) {
            showUsernameStatus('Available', 'available');
            document.getElementById('usernameSuggestions').style.display = 'none';
        } else {
            showUsernameStatus('Taken', 'taken');
            
            if (data.suggestions && data.suggestions.length > 0) {
                showUsernameSuggestions(data.suggestions);
            }
        }
    } catch (error) {
        console.error('Username check error:', error);
        showUsernameStatus('Check failed', 'taken');
    }
}

function showUsernameSuggestions(suggestions) {
    const suggestionsDiv = document.getElementById('usernameSuggestions');
    
    const html = `
        <div class="username-suggestions">
            <div class="suggestions-label">
                <i class="fas fa-lightbulb"></i>
                Try these available usernames:
            </div>
            <div class="suggestions-list">
                ${suggestions.map(s => `
                    <span class="suggestion-item" onclick="selectUsername('${s}')">
                        ${s}
                    </span>
                `).join('')}
            </div>
        </div>
    `;
    
    suggestionsDiv.innerHTML = html;
    suggestionsDiv.style.display = 'block';
}

function selectUsername(username) {
    const usernameInput = document.getElementById('username');
    usernameInput.value = username;
    usernameInput.focus();
    checkUsernameAvailability(username);
}

</script>

<?php
require_once dirname(__DIR__, 4) . '/themes/default/views/partials/footer.php';
?>
