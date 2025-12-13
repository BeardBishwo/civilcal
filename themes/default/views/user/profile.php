<?php
// Get flash messages
$flashMessages = $_SESSION['flash_messages'] ?? [];
unset($_SESSION['flash_messages']);

// Set page title
$pageTitle = $page_title ?? 'User Profile';
?>

<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?> - Bishwo Calculator</title>
    <meta name="csrf-token" content="<?php echo csrf_token(); ?>">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <style>
        /* ===== PREMIUM SAAS PROFILE UI - DARK NAVY BLUE ===== */
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: linear-gradient(135deg, #0a0e27 0%, #1a1a4d 50%, #0f0f2e 100%);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            color: #e5e7eb;
            line-height: 1.6;
            min-height: 100vh;
        }
        
        .profile-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem 1.5rem;
        }
        
        /* Page Header */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid rgba(102, 126, 234, 0.2);
        }
        
        .page-header h1 {
            font-size: 1.75rem;
            font-weight: 600;
            color: #f9fafb;
            margin: 0;
        }
        
        .btn-save {
            background: linear-gradient(135deg, #4361ee 0%, #4cc9f0 100%);
            color: white;
            border: none;
            padding: 0.625rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            font-size: 0.875rem;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .btn-save:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(67, 97, 238, 0.4);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #4361ee 0%, #4cc9f0 100%);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            font-size: 0.875rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            box-shadow: 0 2px 8px rgba(67, 97, 238, 0.3);
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(67, 97, 238, 0.5);
            background: linear-gradient(135deg, #4cc9f0 0%, #4361ee 100%);
        }
        
        .btn-primary:active {
            transform: translateY(0);
            box-shadow: 0 2px 8px rgba(67, 97, 238, 0.3);
        }
        
        .btn-primary i {
            font-size: 1rem;
        }
        
        /* Card Style - Clean White */
        .card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(102, 126, 234, 0.2);
            border-radius: 12px;
            padding: 1.75rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        
        /* Profile Overview Card */
        .profile-header-card {
            display: grid;
            grid-template-columns: auto 1fr auto;
            gap: 2rem;
            align-items: center;
        }
        
        .avatar-section {
            text-align: center;
        }
        
        .avatar {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid rgba(102, 126, 234, 0.3);
            margin-bottom: 0.75rem;
            transition: all 0.2s ease;
            cursor: pointer;
        }
        
        .avatar:hover {
            border-color: #4361ee;
        }
        
        .btn-upload {
            background: rgba(67, 97, 238, 0.1);
            color: #4cc9f0;
            border: 1px solid rgba(102, 126, 234, 0.3);
            padding: 0.375rem 1rem;
            border-radius: 6px;
            font-size: 0.8125rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .btn-upload:hover {
            background: rgba(67, 97, 238, 0.2);
            border-color: #4361ee;
        }
        
        .user-info h2 {
            font-size: 1.5rem;
            font-weight: 600;
            color: #f9fafb;
            margin: 0 0 0.25rem 0;
        }
        
        .user-info p {
            color: #9ca3af;
            margin: 0.25rem 0;
            font-size: 0.875rem;
        }
        
        .tag {
            display: inline-block;
            background: rgba(67, 97, 238, 0.15);
            color: #4cc9f0;
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-top: 0.5rem;
            border: 1px solid rgba(102, 126, 234, 0.3);
        }
        
        .email-verified {
            color: #10b981;
            font-size: 0.875rem;
        }
        
        /* Stats */
        .stats {
            display: flex;
            gap: 2rem;
        }
        
        .stat-box {
            text-align: center;
            min-width: 80px;
        }
        
        .stat-box h3 {
            font-size: 1.75rem;
            font-weight: 700;
            color: #4cc9f0;
            margin: 0 0 0.25rem 0;
        }
        
        .stat-box p {
            font-size: 0.75rem;
            color: #9ca3af;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        /* Tabs */
        .tabs {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
            border-bottom: 1px solid rgba(102, 126, 234, 0.2);
            padding-bottom: 0;
            flex-wrap: wrap;
        }
        
        .tab-btn {
            background: transparent;
            border: none;
            padding: 0.75rem 1.25rem;
            border-radius: 8px 8px 0 0;
            cursor: pointer;
            font-weight: 500;
            font-size: 0.875rem;
            color: #9ca3af;
            transition: all 0.2s ease;
            position: relative;
        }
        
        .tab-btn:hover {
            color: #e5e7eb;
            background: rgba(67, 97, 238, 0.05);
        }
        
        .tab-btn.active {
            background: rgba(67, 97, 238, 0.15);
            color: #4cc9f0;
            border-bottom: 2px solid #4361ee;
        }
        
        .tab-btn.danger-tab {
            margin-left: auto;
            color: #ef4444;
        }
        
        .tab-btn.danger-tab:hover {
            background: rgba(239, 68, 68, 0.1);
        }
        
        /* Tab Content */
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
        }
        
        /* Form Elements */
        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.25rem;
        }
        
        .form-grid-full {
            grid-column: 1 / -1;
        }
        
        .form-group {
            margin-bottom: 0;
        }
        
        .form-label {
            display: block;
            font-size: 0.8125rem;
            font-weight: 500;
            color: #e5e7eb;
            margin-bottom: 0.5rem;
        }
        
        .form-control,
        .form-select {
            width: 100%;
            padding: 0.625rem 0.875rem;
            background: rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(102, 126, 234, 0.3);
            border-radius: 8px;
            font-size: 0.875rem;
            color: #f9fafb;
            transition: all 0.2s ease;
        }
        
        .form-control:focus,
        .form-select:focus {
            outline: none;
            border-color: #4361ee;
            background: rgba(0, 0, 0, 0.3);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
        }
        
        .form-control::placeholder {
            color: #6b7280;
        }
        
        textarea.form-control {
            resize: vertical;
            min-height: 100px;
        }
        
        /* Alert Messages */
        .alert {
            padding: 1rem 1.25rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 0.875rem;
        }
        
        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: #34d399;
        }
        
        .alert-danger {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #f87171;
        }
        
        .alert-warning {
            background: rgba(245, 158, 11, 0.1);
            border: 1px solid rgba(245, 158, 11, 0.3);
            color: #fbbf24;
        }
        
        .btn-close {
            background: transparent;
            border: none;
            color: inherit;
            opacity: 0.6;
            cursor: pointer;
            font-size: 1.25rem;
            line-height: 1;
            margin-left: auto;
        }
        
        /* Danger Zone */
        .danger-zone {
            border: 1px solid rgba(239, 68, 68, 0.3) !important;
            background: rgba(239, 68, 68, 0.05) !important;
        }
        
        .danger-zone h5 {
            color: #ef4444;
            margin-bottom: 0.75rem;
        }
        
        .btn-danger {
            background: transparent;
            border: 1px solid #ef4444;
            color: #ef4444;
            padding: 0.5rem 1.25rem;
            border-radius: 6px;
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .btn-danger:hover {
            background: rgba(239, 68, 68, 0.1);
        }
        
        /* Modal Buttons */
        .btn-outline {
            background: transparent;
            border: 1px solid rgba(102, 126, 234, 0.3);
            color: #4cc9f0;
            padding: 0.5rem 1.25rem;
            border-radius: 6px;
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            margin-right: 0.5rem;
        }
        
        .btn-outline:hover {
            background: rgba(67, 97, 238, 0.1);
            border-color: #4361ee;
        }
        
        /* Loading Spinner */
        .loading-spinner {
            display: none;
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top: 2px solid white;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .profile-header-card {
                grid-template-columns: 1fr;
                text-align: center;
            }
            
            .stats {
                justify-content: center;
            }
            
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .tabs {
                overflow-x: auto;
                flex-wrap: nowrap;
            }
        }
        
        /* Hide file input */
        input[type="file"] {
            display: none;
        }
    </style>
</head>

<body data-theme="dark">
    <?php 
    // Save the $user variable before including header (header overwrites it with session data)
    $profileUser = $user;
    
    $headerPath = BASE_PATH . '/themes/default/views/partials/header.php';
    if (file_exists($headerPath)) {
        include $headerPath;
    }
    
    // Restore the profile user data (with all database fields)
    $user = $profileUser;
    unset($profileUser);
    ?>
    
    <div class="profile-container">
        <!-- Page Header -->
        <div class="page-header">
            <h1><i class="fas fa-user-circle" style="margin-right: 0.5rem; color: #4cc9f0;"></i>User Profile</h1>
            <button type="submit" form="profileForm" class="btn-save" id="saveProfileBtn">
                <span class="loading-spinner" id="profileLoading"></span>
                <i class="fas fa-save"></i> Save
            </button>
        </div>
        
        <!-- Flash Messages -->
        <?php if (!empty($flashMessages)): ?>
            <?php foreach ($flashMessages as $type => $message): ?>
                <div class="alert alert-<?php echo $type === 'error' ? 'danger' : $type; ?>">
                    <i class="fas fa-<?php echo $type === 'success' ? 'check-circle' : ($type === 'error' ? 'exclamation-circle' : 'info-circle'); ?>"></i>
                    <?php echo htmlspecialchars($message); ?>
                    <button type="button" class="btn-close" onclick="this.parentElement.remove()">&times;</button>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        
        <!-- Profile Overview Card -->
        <div class="card profile-header-card">
            <div class="avatar-section">
                <img src="<?php 
                    $avatar = $user['avatar'] ?? null;
                    $firstName = $user['first_name'] ?? '';
                    $lastName = $user['last_name'] ?? '';
                    echo !empty($avatar)
                        ? '/profile/avatar/' . htmlspecialchars($avatar)
                        : 'https://ui-avatars.com/api/?name=' . urlencode(trim($firstName . ' ' . $lastName)) . '&size=90&background=4361ee&color=fff';
                ?>" 
                alt="Profile Avatar" class="avatar" id="avatarPreview" onclick="document.getElementById('avatarInput').click()">
                <br>
                <button type="button" class="btn-upload" onclick="document.getElementById('avatarInput').click()">
                    <i class="fas fa-camera"></i> Change
                </button>
                <input type="file" id="avatarInput" accept="image/*" onchange="handleAvatarUpload(this)">
            </div>
            
            <div class="user-info">
                <h2><?php 
                    $firstName = $user['first_name'] ?? '';
                    $lastName  = $user['last_name'] ?? '';
                    echo htmlspecialchars(trim($firstName . ' ' . $lastName));
                ?></h2>
                <p>
                    <?php echo htmlspecialchars($user['email'] ?? ''); ?>
                    <?php if (!empty($user['email_verified_at'] ?? null)): ?>
                        <i class="fas fa-check-circle email-verified" title="Email verified"></i>
                    <?php else: ?>
                        <i class="fas fa-exclamation-triangle" style="color: #f59e0b;" title="Email not verified"></i>
                    <?php endif; ?>
                </p>
                <span class="tag">Member</span>
                <p style="margin-top: 0.75rem; font-size: 0.8125rem;">
                    <i class="fas fa-calendar" style="margin-right: 0.25rem;"></i>
                    Joined <?php echo isset($user['created_at']) ? date('M j, Y', strtotime($user['created_at'])) : 'Recently'; ?>
                </p>
            </div>
            
            <div class="stats">
                <?php if ($statistics): ?>
                    <div class="stat-box">
                        <h3><?php echo $statistics['calculations_count']; ?></h3>
                        <p>Calculations</p>
                    </div>
                    <div class="stat-box">
                        <h3><?php echo $statistics['favorites_count']; ?></h3>
                        <p>Favorites</p>
                    </div>
                    <div class="stat-box">
                        <h3><?php echo $statistics['last_login'] ? date('M j', strtotime($statistics['last_login'])) : 'Never'; ?></h3>
                        <p>Last Login</p>
                    </div>
                <?php else: ?>
                    <div class="stat-box">
                        <h3>0</h3>
                        <p>Calculations</p>
                    </div>
                    <div class="stat-box">
                        <h3>0</h3>
                        <p>Favorites</p>
                    </div>
                    <div class="stat-box">
                        <h3>Never</h3>
                        <p>Last Login</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Tabs -->
        <div class="tabs">
            <button class="tab-btn active" data-tab="profile">Profile Info</button>
            <button class="tab-btn" data-tab="professional">Professional</button>
            <button class="tab-btn" data-tab="social">Social Links</button>
            <button class="tab-btn" data-tab="preferences">Preferences</button>
            <button class="tab-btn" data-tab="security">Security</button>
            <button class="tab-btn danger-tab" data-tab="danger">Danger Zone</button>
        </div>
        
        <form id="profileForm" enctype="multipart/form-data">
            <?php if (function_exists('csrf_field')) csrf_field(); else echo '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">'; ?>
            <!-- Tab 1: Profile Info -->
            <div class="tab-content active" id="tab-profile">
                <div class="card">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="first_name" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="first_name" name="first_name" 
                                   value="<?php echo htmlspecialchars($user['first_name'] ?? ''); ?>" 
                                   placeholder="John">
                        </div>
                        <div class="form-group">
                            <label for="last_name" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="last_name" name="last_name" 
                                   value="<?php echo htmlspecialchars($user['last_name'] ?? ''); ?>" 
                                   placeholder="Doe">
                        </div>
                        <div class="form-group">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="phone" name="phone" 
                                   value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" 
                                   placeholder="+1 (555) 123-4567">
                        </div>
                        <div class="form-group">
                            <label for="location" class="form-label">Location</label>
                            <input type="text" class="form-control" id="location" name="location" 
                                   value="<?php echo htmlspecialchars($user['location'] ?? ''); ?>" 
                                   placeholder="City, Country">
                        </div>
                        <div class="form-group form-grid-full">
                            <label for="bio" class="form-label">Bio</label>
                            <textarea class="form-control" id="bio" name="bio" rows="4" 
                                      placeholder="Tell us about yourself, your experience, and expertise..."><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Tab 2: Professional -->
            <div class="tab-content" id="tab-professional">
                <div class="card">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="professional_title" class="form-label">Professional Title</label>
                            <input type="text" class="form-control" id="professional_title" name="professional_title" 
                                   value="<?php echo htmlspecialchars($user['professional_title'] ?? ''); ?>" 
                                   placeholder="e.g., Civil Engineer">
                        </div>
                        <div class="form-group">
                            <label for="company" class="form-label">Company</label>
                            <input type="text" class="form-control" id="company" name="company" 
                                   value="<?php echo htmlspecialchars($user['company'] ?? ''); ?>" 
                                   placeholder="Your company name">
                        </div>
                        <div class="form-group">
                            <label for="website" class="form-label">Website</label>
                            <input type="url" class="form-control" id="website" name="website" 
                                   value="<?php echo htmlspecialchars($user['website'] ?? ''); ?>" 
                                   placeholder="https://yourwebsite.com">
                        </div>
                        <div class="form-group">
                            <label for="timezone" class="form-label">Timezone</label>
                            <select class="form-select" id="timezone" name="timezone">
                                <option value="UTC" <?php echo ($user['timezone'] ?? 'UTC') === 'UTC' ? 'selected' : ''; ?>>UTC</option>
                                <option value="America/New_York" <?php echo ($user['timezone'] ?? 'UTC') === 'America/New_York' ? 'selected' : ''; ?>>Eastern Time</option>
                                <option value="America/Chicago" <?php echo ($user['timezone'] ?? 'UTC') === 'America/Chicago' ? 'selected' : ''; ?>>Central Time</option>
                                <option value="America/Denver" <?php echo ($user['timezone'] ?? 'UTC') === 'America/Denver' ? 'selected' : ''; ?>>Mountain Time</option>
                                <option value="America/Los_Angeles" <?php echo ($user['timezone'] ?? 'UTC') === 'America/Los_Angeles' ? 'selected' : ''; ?>>Pacific Time</option>
                                <option value="Europe/London" <?php echo ($user['timezone'] ?? 'UTC') === 'Europe/London' ? 'selected' : ''; ?>>London</option>
                                <option value="Europe/Paris" <?php echo ($user['timezone'] ?? 'UTC') === 'Europe/Paris' ? 'selected' : ''; ?>>Paris</option>
                                <option value="Asia/Katmandu" <?php echo ($user['timezone'] ?? 'UTC') === 'Asia/Katmandu' ? 'selected' : ''; ?>>Nepal (UTC+5:45)</option>
                                <option value="Asia/Tokyo" <?php echo ($user['timezone'] ?? 'UTC') === 'Asia/Tokyo' ? 'selected' : ''; ?>>Tokyo</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Tab 3: Social Links -->
            <div class="tab-content" id="tab-social">
                <div class="card">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="linkedin_url" class="form-label"><i class="fab fa-linkedin" style="margin-right: 0.5rem;"></i>LinkedIn</label>
                            <input type="url" class="form-control" id="linkedin_url" name="linkedin_url" 
                                   value="<?php echo htmlspecialchars($social_links['linkedin'] ?? ''); ?>" 
                                   placeholder="https://linkedin.com/in/yourprofile">
                        </div>
                        <div class="form-group">
                            <label for="twitter_url" class="form-label"><i class="fab fa-twitter" style="margin-right: 0.5rem;"></i>Twitter</label>
                            <input type="url" class="form-control" id="twitter_url" name="twitter_url" 
                                   value="<?php echo htmlspecialchars($social_links['twitter'] ?? ''); ?>" 
                                   placeholder="https://twitter.com/yourusername">
                        </div>
                        <div class="form-group">
                            <label for="github_url" class="form-label"><i class="fab fa-github" style="margin-right: 0.5rem;"></i>GitHub</label>
                            <input type="url" class="form-control" id="github_url" name="github_url" 
                                   value="<?php echo htmlspecialchars($social_links['github'] ?? ''); ?>" 
                                   placeholder="https://github.com/yourusername">
                        </div>
                        <div class="form-group">
                            <label for="facebook_url" class="form-label"><i class="fab fa-facebook" style="margin-right: 0.5rem;"></i>Facebook</label>
                            <input type="url" class="form-control" id="facebook_url" name="facebook_url" 
                                   value="<?php echo htmlspecialchars($social_links['facebook'] ?? ''); ?>" 
                                   placeholder="https://facebook.com/yourprofile">
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Tab 4: Preferences -->
            <div class="tab-content" id="tab-preferences">
                <div class="card">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="measurement_system" class="form-label">Measurement System</label>
                            <select class="form-select" id="measurement_system" name="measurement_system">
                                <option value="metric" <?php echo ($user['measurement_system'] ?? 'metric') === 'metric' ? 'selected' : ''; ?>>Metric (SI)</option>
                                <option value="imperial" <?php echo ($user['measurement_system'] ?? 'metric') === 'imperial' ? 'selected' : ''; ?>>Imperial (US)</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Tab 5: Security -->
            <div class="tab-content" id="tab-security">
                <!-- Two-Factor Authentication -->
                <div class="card" style="margin-bottom: 1.5rem;">
                    <h5 style="margin-bottom: 1rem; color: #f9fafb;">
                        <i class="fas fa-shield-alt" style="margin-right: 0.5rem; color: #4cc9f0;"></i>
                        Two-Factor Authentication (2FA)
                    </h5>
                    
                    <?php if ($two_factor_status && $two_factor_status['enabled']): ?>
                        <div class="alert" style="background: rgba(34, 197, 94, 0.1); border: 1px solid rgba(34, 197, 94, 0.3); color: #4ade80; margin-bottom: 1rem;">
                            <i class="fas fa-check-circle"></i> 2FA is currently <strong>enabled</strong>
                            <?php if ($two_factor_status['confirmed_at']): ?>
                                <br><small>Activated on <?php echo date('M j, Y', strtotime($two_factor_status['confirmed_at'])); ?></small>
                            <?php endif; ?>
                        </div>
                        
                        <p style="color: #9ca3af; margin-bottom: 1rem;">
                            Recovery codes remaining: <strong><?php echo $two_factor_status['recovery_codes_remaining']; ?></strong>
                        </p>
                        
                        <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                            <button type="button" class="btn-outline" onclick="regenerateRecoveryCodes()">
                                <i class="fas fa-sync-alt"></i> Regenerate Recovery Codes
                            </button>
                            <button type="button" class="btn-outline" style="border-color: #ef4444; color: #ef4444;" onclick="disable2FA()">
                                <i class="fas fa-times-circle"></i> Disable 2FA
                            </button>
                        </div>
                    <?php else: ?>
                        <div class="alert" style="background: rgba(251, 191, 36, 0.1); border: 1px solid rgba(251, 191, 36, 0.3); color: #fbbf24; margin-bottom: 1rem;">
                            <i class="fas fa-exclamation-triangle"></i> 2FA is currently <strong>disabled</strong>
                        </div>
                        
                        <p style="color: #9ca3af; margin-bottom: 1rem;">
                            Add an extra layer of security to your account with two-factor authentication using Google Authenticator or similar apps.
                        </p>
                        
                        <button type="button" class="btn-primary" onclick="window.location.href='<?php echo app_base_url('/2fa/setup'); ?>'">
                            <i class="fas fa-lock"></i> Enable 2FA
                        </button>
                    <?php endif; ?>
                </div>
                
                <!-- Data Export (GDPR) -->
                <div class="card">
                    <h5 style="margin-bottom: 1rem; color: #f9fafb;">
                        <i class="fas fa-download" style="margin-right: 0.5rem; color: #4cc9f0;"></i>
                        Data Export (GDPR)
                    </h5>
                    
                    <p style="color: #9ca3af; margin-bottom: 1rem;">
                        Request a copy of all your personal data stored in our system. You'll receive a downloadable ZIP file containing your profile, calculations, and activity history.
                    </p>
                    
                    <button type="button" class="btn-primary" onclick="requestDataExport()" style="margin-bottom: 1.5rem;">
                        <i class="fas fa-file-archive"></i> Request Data Export
                    </button>
                    
                    <?php if (!empty($export_requests)): ?>
                        <h6 style="color: #f9fafb; margin-bottom: 0.75rem;">Recent Export Requests</h6>
                        <div style="overflow-x: auto;">
                            <table style="width: 100%; color: #e5e7eb; border-collapse: collapse;">
                                <thead>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                                        <th style="padding: 0.5rem; text-align: left;">Status</th>
                                        <th style="padding: 0.5rem; text-align: left;">Requested</th>
                                        <th style="padding: 0.5rem; text-align: left;">Expires</th>
                                        <th style="padding: 0.5rem; text-align: left;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach (array_slice($export_requests, 0, 5) as $request): ?>
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                                            <td style="padding: 0.5rem;">
                                                <?php 
                                                $statusColors = [
                                                    'pending' => '#fbbf24',
                                                    'processing' => '#3b82f6',
                                                    'completed' => '#22c55e',
                                                    'failed' => '#ef4444'
                                                ];
                                                $color = $statusColors[$request['status']] ?? '#9ca3af';
                                                ?>
                                                <span style="color: <?php echo $color; ?>">
                                                    ● <?php echo ucfirst($request['status']); ?>
                                                </span>
                                            </td>
                                            <td style="padding: 0.5rem; color: #9ca3af;">
                                                <?php echo date('M j, Y', strtotime($request['requested_at'])); ?>
                                            </td>
                                            <td style="padding: 0.5rem; color: #9ca3af;">
                                                <?php echo $request['expires_at'] ? date('M j, Y', strtotime($request['expires_at'])) : '-'; ?>
                                            </td>
                                            <td style="padding: 0.5rem;">
                                                <?php if ($request['status'] === 'completed'): ?>
                                                    <a href="<?php echo app_base_url('/data-export/download/' . $request['id']); ?>" 
                                                       style="color: #4cc9f0; text-decoration: none;">
                                                        <i class="fas fa-download"></i> Download
                                                    </a>
                                                <?php else: ?>
                                                    -
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Tab 6: Danger Zone -->
            <div class="tab-content" id="tab-danger">
                <div class="card danger-zone">
                    <h5><i class="fas fa-exclamation-triangle" style="margin-right: 0.5rem;"></i>Danger Zone</h5>
                    <p style="color: #9ca3af; margin-bottom: 1.5rem;">These actions cannot be undone. Please be careful.</p>
                    <button type="button" class="btn-danger" onclick="alert('Delete Account confirmation would open here')">
                        <i class="fas fa-trash-alt"></i> Delete Account
                    </button>
                </div>
            </div>
        </form>
    </div>
    
    <script>
        // Tab switching
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const tabName = btn.dataset.tab;
                
                // Remove active class from all tabs and content
                document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
                document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
                
                // Add active class to clicked tab and corresponding content
                btn.classList.add('active');
                document.getElementById('tab-' + tabName).classList.add('active');
            });
        });
        
        // Avatar upload preview
        function handleAvatarUpload(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('avatarPreview').src = e.target.result;
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
        
        // Form submission
        document.getElementById('profileForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const spinner = document.getElementById('profileLoading');
            const btn = document.getElementById('saveProfileBtn');
            
            spinner.style.display = 'inline-block';
            btn.disabled = true;
            
            const formData = new FormData(e.target);
            
            // Add avatar if selected
            const avatarInput = document.getElementById('avatarInput');
            if (avatarInput.files[0]) {
                formData.append('avatar', avatarInput.files[0]);
            }
            
            // Add social links as JSON
            const socialLinks = {
                linkedin: document.getElementById('linkedin_url').value,
                twitter: document.getElementById('twitter_url').value,
                github: document.getElementById('github_url').value,
                facebook: document.getElementById('facebook_url').value
            };
            formData.append('social_links', JSON.stringify(socialLinks));
            
            try {
                const baseUrl = window.location.origin + '/Bishwo_Calculator';
                const response = await fetch(baseUrl + '/user/profile/update', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: formData,
                    credentials: 'include'
                });
                
                const data = await response.json();
                
                if (data.success) {
                    alert('✓ Profile updated successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + (data.error || 'Failed to update profile'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error: Failed to update profile');
            } finally {
                spinner.style.display = 'none';
                btn.disabled = false;
            }
        });
        
        // Two-Factor Authentication Functions
        async function disable2FA() {
            const password = prompt('Enter your password to disable 2FA:');
            if (!password) return;
            
            try {
                const baseUrl = window.location.origin + '/Bishwo_Calculator';
                const response = await fetch(baseUrl + '/2fa/disable', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: new URLSearchParams({password}),
                    credentials: 'include'
                });
                
                const data = await response.json();
                
                if (data.success) {
                    alert('✓ 2FA disabled successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + (data.error || 'Failed to disable 2FA'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error: Failed to disable 2FA');
            }
        }
        
        async function regenerateRecoveryCodes() {
            if (!confirm('This will invalidate all existing recovery codes. Continue?')) return;
            
            const password = prompt('Enter your password to regenerate recovery codes:');
            if (!password) return;
            
            try {
                const baseUrl = window.location.origin + '/Bishwo_Calculator';
                const response = await fetch(baseUrl + '/2fa/recovery-codes/regenerate', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: new URLSearchParams({password}),
                    credentials: 'include'
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Show recovery codes
                    const codes = data.recovery_codes.join('\n');
                    alert('New Recovery Codes:\n\n' + codes + '\n\nPlease save these codes in a secure location!');
                    location.reload();
                } else {
                    alert('Error: ' + (data.error || 'Failed to regenerate codes'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error: Failed to regenerate codes');
            }
        }
        
        // Data Export Functions
        async function requestDataExport() {
            if (!confirm('Request a complete export of your personal data? This may take a few minutes to process.')) return;
            
            try {
                const baseUrl = window.location.origin + '/Bishwo_Calculator';
                const response = await fetch(baseUrl + '/data-export/request', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    credentials: 'include'
                });
                
                const data = await response.json();
                
                if (data.success) {
                    alert('✓ ' + data.message);
                    location.reload();
                } else {
                    alert('Error: ' + (data.error || 'Failed to request export'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error: Failed to request export');
            }
        }
    </script>
    
    <?php 
    $footerPath = BASE_PATH . '/themes/default/views/partials/footer.php';
    if (file_exists($footerPath)) {
        include $footerPath;
    }
    ?>
</body>
</html>

