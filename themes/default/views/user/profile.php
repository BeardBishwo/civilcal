<?php
// Get flash messages
$flashMessages = $_SESSION['flash_messages'] ?? [];
unset($_SESSION['flash_messages']);

// Set page title
$pageTitle = $page_title ?? 'User Profile';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?> - Bishwo Calculator</title>
    <meta name="csrf-token" content="<?php echo csrf_token(); ?>">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        /* ===== ULTRA-PREMIUM PROFILE UI ===== */
        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --secondary: #8b5cf6;
            --accent: #dc2626;
            --success: #10b981;
            --error: #ef4444;
            --warning: #f59e0b;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: #e2e8f0;
            line-height: 1.6;
            min-height: 100vh;
            overflow-x: hidden;
        }
        
        .ultra-premium-container {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #dc2626 100%);
            position: relative;
            padding: 40px 20px;
        }
        
        /* Particles */
        .particles {
            position: absolute;
            top: 0;
            left: 0;
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
            0%, 100% { transform: translate(0, 0) scale(1); opacity: 0; }
            10% { opacity: 1; }
            90% { opacity: 1; }
            100% { transform: translate(var(--tx), var(--ty)) scale(0); opacity: 0; }
        }
        
        /* Main Card */
        .ultra-card {
            background: #ffffff !important;
            backdrop-filter: blur(20px);
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            max-width: 1200px;
            margin: 0 auto;
            position: relative;
            z-index: 10;
            color: #000000 !important; /* Dark text for light card */
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
            border-radius: 24px 24px 0 0;
        }
        
        @keyframes shimmer {
            0% { background-position: 0% 0%; }
            100% { background-position: 200% 0%; }
        }

        /* Header Section */
        .profile-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 20px;
        }

        .profile-title {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .profile-title h1 {
            font-size: 2rem;
            font-weight: 800;
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #dc2626 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin: 0;
        }

        .btn-save {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 6px -1px rgba(99, 102, 241, 0.4);
        }

        .btn-save:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(99, 102, 241, 0.5);
        }

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

        /* Profile ID Card */
        .profile-id-card {
            display: grid;
            grid-template-columns: auto 1fr auto;
            gap: 30px;
            align-items: center;
            background: #f8fafc;
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            border: 1px solid #e2e8f0;
        }

        .avatar-wrapper {
            position: relative;
        }

        .avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid white;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .btn-upload-mini {
            position: absolute;
            bottom: 0;
            right: 0;
            background: #6366f1;
            color: white;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            border: 2px solid white;
            transition: all 0.2s;
        }

        .btn-upload-mini:hover {
            transform: scale(1.1);
        }

        .user-meta h2 {
            font-size: 1.5rem;
            color: #000000;
            margin-bottom: 5px;
        }

        .user-meta p {
            color: #64748b;
            font-size: 0.95rem;
        }

        .stats-grid {
            display: flex;
            gap: 30px;
        }

        .stat-item {
            text-align: center;
        }

        .stat-val {
            font-size: 1.5rem;
            font-weight: 700;
            color: #6366f1;
        }

        .stat-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #64748b;
        }

        /* Navigation Tabs */
        .nav-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 30px;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 0;
            overflow-x: auto;
        }

        .nav-tab {
            padding: 12px 20px;
            font-weight: 600;
            color: #64748b;
            background: none;
            border: none;
            cursor: pointer;
            border-bottom: 2px solid transparent;
            margin-bottom: -2px;
            transition: all 0.3s;
            white-space: nowrap;
        }

        .nav-tab:hover {
            color: #6366f1;
            background: rgba(99, 102, 241, 0.05);
            border-radius: 8px 8px 0 0;
        }

        .nav-tab.active {
            color: #6366f1;
            border-bottom-color: #6366f1;
        }

        .nav-tab.danger-tab {
            margin-left: auto;
            color: #ef4444;
        }
        
        .nav-tab.danger-tab:hover, .nav-tab.danger-tab.active {
            color: #dc2626;
            border-bottom-color: #dc2626;
            background: rgba(239, 68, 68, 0.05);
        }

        /* Form Layout */
        .tab-content {
            display: none;
            animation: fadeIn 0.4s ease;
        }

        .tab-content.active {
            display: block;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 24px;
        }

        .form-grid-full {
            grid-column: 1 / -1;
        }

        /* Ultra specific selector to override any dark mode defaults */
        body .ultra-premium-container .ultra-card .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #000000 !important;
            font-size: 0.9rem;
        }

        .form-input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            background: white;
            color: #0f172a;
        }

        .form-input:focus {
            outline: none;
            border-color: #6366f1;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
        }

        textarea.form-input {
            resize: vertical;
            min-height: 120px;
        }

        /* Alerts */
        .alert {
            padding: 16px;
            border-radius: 12px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 0.95rem;
        }

        .alert-success { background: #d1fae5; color: #065f46; border: 1px solid #10b981; }
        .alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #ef4444; }
        .alert-info { background: #dbeafe; color: #1e40af; border: 1px solid #3b82f6; }
        .alert-warning { background: #fef3c7; color: #92400e; border: 1px solid #f59e0b; }

        /* Danger Zone */
        .danger-card {
            border: 1px solid #fca5a5;
            background: #fef2f2;
            padding: 24px;
            border-radius: 16px;
        }
        
        .btn-danger {
            background: #ef4444;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .btn-danger:hover {
            background: #dc2626;
        }
        
        .verify-btn {
            margin-top: 5px;
            font-size: 0.8rem;
            padding: 4px 8px;
            background: #f59e0b;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        /* Input Group with Icon */
        .input-group {
            position: relative;
        }
        
        .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
        }
        
        .form-input.has-icon {
            padding-left: 44px;
        }
        
        @media (max-width: 768px) {
            .form-grid { grid-template-columns: 1fr; }
            .profile-id-card { grid-template-columns: 1fr; text-align: center; }
            .stats-grid { justify-content: center; }
            .nav-tabs { padding-bottom: 10px; }
            .profile-header { flex-direction: column; text-align: center; }
        }
    </style>
</head>

<body>
    <?php 
    $profileUser = $user;
    $headerPath = BASE_PATH . '/themes/default/views/partials/header.php';
    if (file_exists($headerPath)) include $headerPath;
    $user = $profileUser;
    ?>
    
    <div class="ultra-premium-container">
        <div class="particles" id="particles"></div>
        
        <div class="ultra-card">
            <!-- Header -->
            <div class="profile-header">
                <div class="profile-title">
                    <i class="fas fa-id-card-alt fa-2x" style="color: #6366f1;"></i>
                    <h1>My Profile</h1>
                </div>
                <button type="button" class="btn-save" onclick="document.getElementById('profileForm').requestSubmit()">
                    <span class="loading-spinner" id="profileLoading"></span>
                    <i class="fas fa-save"></i> Save Changes
                </button>
            </div>

            <!-- Flash Messages -->
            <?php if (!empty($flashMessages)): ?>
                <?php foreach ($flashMessages as $type => $message): ?>
                    <div class="alert alert-<?php echo $type === 'error' ? 'error' : $type; ?>">
                        <i class="fas fa-<?php echo $type === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <!-- Identity Card -->
            <div class="profile-id-card">
                <div class="avatar-wrapper">
                    <img src="<?php 
                        $avatar = $user['avatar'] ?? null;
                        $firstName = $user['first_name'] ?? '';
                        $lastName = $user['last_name'] ?? '';
                        echo !empty($avatar)
                            ? '/profile/avatar/' . htmlspecialchars($avatar)
                            : 'https://ui-avatars.com/api/?name=' . urlencode(trim($firstName . ' ' . $lastName)) . '&size=200&background=6366f1&color=fff&bold=true';
                    ?>" alt="Avatar" class="avatar" id="avatarPreview">
                    <div class="btn-upload-mini" onclick="document.getElementById('avatarInput').click()">
                        <i class="fas fa-camera"></i>
                    </div>
                </div>
                
                <div class="user-meta">
                    <h2><?php echo htmlspecialchars(trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''))); ?></h2>
                    <p>
                        <?php echo htmlspecialchars($user['email'] ?? ''); ?>
                        <?php if (!empty($user['email_verified_at'] ?? null)): ?>
                            <i class="fas fa-check-circle" style="color: #10b981; margin-left: 5px;" title="Verified"></i>
                        <?php else: ?>
                            <span class="badge" style="background: #fee2e2; color: #ef4444; padding: 2px 6px; border-radius: 4px; font-size: 0.75rem;">Unverified</span>
                        <?php endif; ?>
                    </p>
                    <div style="margin-top: 10px;">
                        <span style="background: rgba(99, 102, 241, 0.1); color: #6366f1; padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 600;">
                            <?php echo ucfirst($user['role'] ?? 'Member'); ?>
                        </span>
                    </div>
                </div>

                <div class="stats-grid">
                    <div class="stat-item">
                        <div class="stat-val"><?php echo $statistics['calculations_count'] ?? 0; ?></div>
                        <div class="stat-label">Calcs</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-val"><?php echo $statistics['favorites_count'] ?? 0; ?></div>
                        <div class="stat-label">Saved</div>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <div class="nav-tabs">
                <button class="nav-tab active" onclick="switchTab('info', this)">Personal Info</button>
                <button class="nav-tab" onclick="switchTab('professional', this)">Professional</button>
                <button class="nav-tab" onclick="switchTab('social', this)">Social & Links</button>
                <button class="nav-tab" onclick="switchTab('preferences', this)">Preferences</button>
                <button class="nav-tab" onclick="switchTab('security', this)">Security</button>
                <button class="nav-tab danger-tab" onclick="switchTab('danger', this)">Danger Zone</button>
            </div>

            <form id="profileForm" method="POST" enctype="multipart/form-data">
                <input type="file" id="avatarInput" name="avatar" accept="image/*" style="display: none;" onchange="handleAvatarUpload(this)">
                
                <!-- Tab: Personal Info -->
                <div id="tab-info" class="tab-content active">
                    <div class="form-grid">
                        <div class="form-group form-grid-full">
                            <h3 style="color: #1e293b; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px; margin-bottom: 20px;">Account Credentials</h3>
                        </div>
                        
                        <div class="form-group">
                            <label>Username</label>
                            <div class="input-group">
                                <i class="fas fa-at input-icon"></i>
                                <input type="text" class="form-input has-icon" name="username" value="<?php echo htmlspecialchars($user['username'] ?? ''); ?>" required minlength="3">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Email Address</label>
                            <div class="input-group">
                                <i class="fas fa-envelope input-icon"></i>
                                <input type="email" class="form-input has-icon" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
                            </div>
                            <?php if (empty($user['email_verified_at'])): ?>
                                <button type="button" class="verify-btn" onclick="resendVerification()">
                                    <i class="fas fa-paper-plane"></i> Send Verification Email
                                </button>
                            <?php endif; ?>
                        </div>

                        <div class="form-group form-grid-full">
                            <h3 style="color: #1e293b; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px; margin-bottom: 20px; margin-top: 10px;">Personal Details</h3>
                        </div>

                        <div class="form-group">
                            <label>First Name</label>
                            <input type="text" class="form-input" name="first_name" value="<?php echo htmlspecialchars($user['first_name'] ?? ''); ?>" placeholder="Jane">
                        </div>

                        <div class="form-group">
                            <label>Last Name</label>
                            <input type="text" class="form-input" name="last_name" value="<?php echo htmlspecialchars($user['last_name'] ?? ''); ?>" placeholder="Doe">
                        </div>

                        <div class="form-group">
                            <label>Phone Number</label>
                            <input type="tel" class="form-input" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" placeholder="+1 (555) 000-0000">
                        </div>

                        <div class="form-group">
                            <label>Location</label>
                            <input type="text" class="form-input" name="location" value="<?php echo htmlspecialchars($user['location'] ?? ''); ?>" placeholder="New York, USA">
                        </div>
                        
                        <div class="form-group form-grid-full">
                            <label>Bio</label>
                            <textarea class="form-input" name="bio" placeholder="Tell us about yourself..."><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Tab: Professional -->
                <div id="tab-professional" class="tab-content">
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Professional Title</label>
                            <input type="text" class="form-input" name="professional_title" value="<?php echo htmlspecialchars($user['professional_title'] ?? ''); ?>" placeholder="Civil Engineer">
                        </div>

                        <div class="form-group">
                            <label>Company / Organization</label>
                            <input type="text" class="form-input" name="company" value="<?php echo htmlspecialchars($user['company'] ?? ''); ?>" placeholder="Acme Corp">
                        </div>

                        <div class="form-group">
                            <label>Website</label>
                            <input type="url" class="form-input" name="website" value="<?php echo htmlspecialchars($user['website'] ?? ''); ?>" placeholder="https://example.com">
                        </div>
                    </div>
                </div>

                <!-- Tab: Social -->
                <div id="tab-social" class="tab-content">
                    <div class="form-grid">
                        <div class="form-group">
                            <label><i class="fab fa-linkedin text-blue-600"></i> LinkedIn</label>
                            <input type="url" class="form-input" id="linkedin_url" value="<?php echo htmlspecialchars($social_links['linkedin'] ?? ''); ?>" placeholder="LinkedIn URL">
                        </div>
                        <div class="form-group">
                            <label><i class="fab fa-twitter text-blue-400"></i> Twitter / X</label>
                            <input type="url" class="form-input" id="twitter_url" value="<?php echo htmlspecialchars($social_links['twitter'] ?? ''); ?>" placeholder="Twitter URL">
                        </div>
                        <div class="form-group">
                            <label><i class="fab fa-github"></i> GitHub</label>
                            <input type="url" class="form-input" id="github_url" value="<?php echo htmlspecialchars($social_links['github'] ?? ''); ?>" placeholder="GitHub URL">
                        </div>
                        <div class="form-group">
                            <label><i class="fab fa-facebook text-blue-700"></i> Facebook</label>
                            <input type="url" class="form-input" id="facebook_url" value="<?php echo htmlspecialchars($social_links['facebook'] ?? ''); ?>" placeholder="Facebook URL">
                        </div>
                    </div>
                </div>

                <!-- Tab: Preferences -->
                <div id="tab-preferences" class="tab-content">
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Measurement System</label>
                            <select class="form-input" name="measurement_system">
                                <option value="metric" <?php echo ($user['measurement_system'] ?? 'metric') === 'metric' ? 'selected' : ''; ?>>Metric (SI)</option>
                                <option value="imperial" <?php echo ($user['measurement_system'] ?? '') === 'imperial' ? 'selected' : ''; ?>>Imperial (US)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Timezone</label>
                            <select class="form-input" name="timezone">
                                <option value="UTC" <?php echo ($user['timezone'] ?? 'UTC') === 'UTC' ? 'selected' : ''; ?>>UTC</option>
                                <option value="Asia/Katmandu" <?php echo ($user['timezone'] ?? '') === 'Asia/Katmandu' ? 'selected' : ''; ?>>Nepal (UTC+5:45)</option>
                                <option value="America/New_York" <?php echo ($user['timezone'] ?? '') === 'America/New_York' ? 'selected' : ''; ?>>New York (EST)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Tab: Security -->
                <div id="tab-security" class="tab-content">
                    <div class="form-grid">
                        <div class="form-group form-grid-full">
                            <h3 style="color: #1e293b; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px; margin-bottom: 20px;">Change Password</h3>
                        </div>
                        
                        <div class="form-group form-grid-full">
                            <label>Current Password</label>
                            <input type="password" class="form-input" id="current_password" placeholder="Enter current password">
                        </div>

                        <div class="form-group">
                            <label>New Password</label>
                            <input type="password" class="form-input" id="new_password" placeholder="Enter new password">
                        </div>

                        <div class="form-group">
                            <label>Confirm New Password</label>
                            <input type="password" class="form-input" id="confirm_password" placeholder="Confirm new password">
                        </div>
                        
                        <div class="form-group form-grid-full">
                             <button type="button" class="btn-save" onclick="updatePassword()">
                                <i class="fas fa-key"></i> Update Password
                            </button>
                        </div>
                        
                        <div class="form-group form-grid-full">
                            <h3 style="color: #1e293b; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px; margin-bottom: 20px; margin-top: 30px;">Two-Factor Authentication</h3>
                        </div>

                        <div id="2fa-status-section" class="form-group form-grid-full">
                            <?php if (!empty($two_factor_status['enabled'])): ?>
                                <div class="alert alert-success">
                                    <i class="fas fa-shield-check"></i> Two-factor authentication is <strong>ENABLED</strong> on your account.
                                </div>
                                <button type="button" class="btn-danger" style="margin-top: 10px;" onclick="showDisableTwoFactor()">
                                    <i class="fas fa-lock-open"></i> Disable 2FA
                                </button>
                                <div id="disable-2fa-form" style="display:none; margin-top: 15px;">
                                    <label>Confirm Password to Disable:</label>
                                    <input type="password" class="form-input" id="disable_2fa_password" placeholder="Enter password" style="margin-bottom: 10px;">
                                    <button type="button" class="btn-danger" onclick="disableTwoFactor()">Confirm Disable</button>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i> Two-factor authentication is currently <strong>DISABLED</strong>.
                                </div>
                                <p>Add an extra layer of security to your account by enabling 2FA.</p>
                                <button type="button" class="btn-save" style="margin-top: 10px;" onclick="startTwoFactorSetup()">
                                    <i class="fas fa-qrcode"></i> Setup 2FA
                                </button>
                                
                                <!-- Setup UI (Initially Hidden) -->
                                <div id="2fa-setup-container" style="display:none; margin-top: 20px; border: 1px solid #e2e8f0; padding: 20px; border-radius: 8px; background: #f8fafc;">
                                    <h4 style="color: #1e293b; margin-bottom: 15px;">📱 Step 1: Scan QR Code</h4>
                                    <p style="color: #64748b; margin-bottom: 15px;">Scan this QR code with your authenticator app (Google Authenticator, Authy, Microsoft Authenticator, etc).</p>
                                    <div id="qr-code-display" style="margin: 15px 0; background: white; padding: 15px; display: inline-block; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);"></div>
                                    <p style="color: #64748b; margin: 15px 0;">Or enter this secret key manually: <strong id="secret-key-display" style="font-family: 'Courier New', monospace; background: #e2e8f0; padding: 4px 8px; border-radius: 4px; color: #1e293b; font-size: 14px;"></strong></p>
                                    
                                    <hr style="margin: 25px 0; border: 0; border-top: 1px solid #e2e8f0;">
                                    
                                    <h4 style="color: #1e293b; margin-bottom: 15px;">🔑 Step 2: Save Backup Codes</h4>
                                    <div class="alert alert-warning" style="margin-bottom: 15px;">
                                        <i class="fas fa-exclamation-triangle"></i> <strong>Important:</strong> Save these backup codes in a safe place. You can use them to access your account if you lose your phone.
                                    </div>
                                    <div id="backup-codes-display" style="background: white; padding: 15px; border-radius: 8px; border: 1px solid #e2e8f0; margin-bottom: 20px;">
                                        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; font-family: 'Courier New', monospace; font-size: 13px; color: #1e293b;"></div>
                                    </div>
                                    <button type="button" class="btn-save" onclick="copyBackupCodes()" style="margin-bottom: 20px;">
                                        <i class="fas fa-copy"></i> Copy Backup Codes
                                    </button>
                                    
                                    <hr style="margin: 25px 0; border: 0; border-top: 1px solid #e2e8f0;">
                                    
                                    <h4 style="color: #1e293b; margin-bottom: 15px;">✅ Step 3: Verify Code</h4>
                                    <div class="form-group">
                                        <label>Enter 6-digit Code from your app</label>
                                        <input type="text" class="form-input" id="verify_code_input" placeholder="000000" maxlength="6" style="width: 200px; font-family: 'Courier New', monospace; font-size: 18px; letter-spacing: 3px; text-align: center;">
                                    </div>
                                    <button type="button" class="btn-save" onclick="confirmTwoFactor()">
                                        <i class="fas fa-shield-check"></i> Verify & Activate 2FA
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Tab: Danger -->
                <div id="tab-danger" class="tab-content">
                    <div class="danger-card">
                        <h3 style="color: #b91c1c; margin-bottom: 10px;">Delete Account</h3>
                        <p style="color: #7f1d1d; margin-bottom: 20px;">Once you delete your account, there is no going back. Please be certain.</p>
                        <button type="button" class="btn-danger" onclick="confirmDelete()">Delete My Account</button>
                    </div>
                </div>

            </form>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        // Tab function
        function switchTab(tabName, clickedTab) {
            document.querySelectorAll('.nav-tab').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
            
            clickedTab.classList.add('active');
            document.getElementById('tab-' + tabName).classList.add('active');
        }

        // Particle Animation
        function createParticles() {
            const container = document.getElementById('particles');
            const particleCount = 50;
            
            for (let i = 0; i < particleCount; i++) {
                const particle = document.createElement('div');
                particle.className = 'particle';
                
                const x = Math.random() * 100;
                const y = Math.random() * 100;
                const duration = 15 + Math.random() * 20;
                const tx = (Math.random() - 0.5) * 200;
                const ty = (Math.random() - 0.5) * 200;
                
                particle.style.left = x + '%';
                particle.style.top = y + '%';
                particle.style.setProperty('--tx', tx + 'px');
                particle.style.setProperty('--ty', ty + 'px');
                particle.style.animationDuration = duration + 's';
                
                container.appendChild(particle);
            }
        }
        
        document.addEventListener('DOMContentLoaded', createParticles);

        // Avatar Preview
        function handleAvatarUpload(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('avatarPreview').src = e.target.result;
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Form Submit
        document.getElementById('profileForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = document.querySelector('.btn-save');
            const spinner = document.getElementById('profileLoading');
            const originalText = btn.innerHTML;
            
            btn.disabled = true;
            spinner.style.display = 'inline-block';
            
            const formData = new FormData(e.target);
            
            // Add social links
            const socialLinks = {
                linkedin: document.getElementById('linkedin_url').value,
                twitter: document.getElementById('twitter_url').value,
                github: document.getElementById('github_url').value,
                facebook: document.getElementById('facebook_url').value
            };
            formData.append('social_links', JSON.stringify(socialLinks));

            try {
                // Use hardcoded path for reliability in this environment
                const response = await fetch('/Bishwo_Calculator/profile/update', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (response.ok) {
                    alert('Profile updated successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + (result.error || 'Update failed'));
                }
            } catch (err) {
                alert('Network error occurred.');
            } finally {
                btn.disabled = false;
                spinner.style.display = 'none';
            }
        });

        // Delete Account
        function confirmDelete() {
            if (confirm('Are you ABSOLUTELY SURE? This action cannot be undone.')) {
                // Redirect to delete handler or show modal
                const password = prompt("Please enter your password to confirm:");
                if(password) {
                   // Call delete API
                   alert("Feature placeholder: Delete account request sent.");
                }
            }
        }
        // Update Password Function
        async function updatePassword() {
            const current = document.getElementById('current_password').value;
            const newPass = document.getElementById('new_password').value;
            const confirm = document.getElementById('confirm_password').value;
            
            if (!current || !newPass || !confirm) {
                alert('Please fill in all password fields');
                return;
            }
            
            if (newPass !== confirm) {
                alert('New passwords do not match');
                return;
            }
            
            const btn = event.target.closest('button');
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
            
            try {
                // Use hardcoded path for reliability
                const response = await fetch('/Bishwo_Calculator/profile/password', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        current_password: current,
                        new_password: newPass,
                        confirm_password: confirm
                    })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('Password updated successfully');
                    document.getElementById('current_password').value = '';
                    document.getElementById('new_password').value = '';
                    document.getElementById('confirm_password').value = '';
                } else {
                    alert(result.error || 'Failed to update password');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred while updating password');
            } finally {
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        }
        
        // 2FA Functions
        async function startTwoFactorSetup() {
            const pwd = prompt("Please confirm your password to start 2FA setup:");
            if (!pwd) return;

            try {
                const response = await fetch('/Bishwo_Calculator/profile/2fa/enable', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({ password: pwd })
                });
                const result = await response.json();
                
                if (result.success) {
                    // Store backup codes globally
                    window.currentBackupCodes = result.recovery_codes || [];
                    
                    document.getElementById('2fa-setup-container').style.display = 'block';
                    document.getElementById('qr-code-display').innerHTML = `<img src="${result.qr_code_url}" alt="QR Code" style="max-width: 200px;" />`;
                    document.getElementById('secret-key-display').innerText = result.secret;
                    
                    // Display backup codes
                    const backupCodesContainer = document.querySelector('#backup-codes-display > div');
                    if (backupCodesContainer && window.currentBackupCodes.length > 0) {
                        backupCodesContainer.innerHTML = window.currentBackupCodes.map((code, index) => 
                            `<div style="padding: 8px; background: #f1f5f9; border-radius: 4px;">${index + 1}. ${code}</div>`
                        ).join('');
                    }
                } else {
                    alert(result.error || 'Failed to start setup');
                }
            } catch (e) {
                console.error(e);
                alert('Error starting 2FA setup');
            }
        }

        async function confirmTwoFactor() {
            const code = document.getElementById('verify_code_input').value;
            if (!code) { alert('Please enter code'); return; }
            
            try {
                const response = await fetch('/Bishwo_Calculator/profile/2fa/confirm', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({ code: code })
                });
                const result = await response.json();
                
                if (result.success) {
                    alert('2FA Enabled Successfully!');
                    location.reload();
                } else {
                    alert(result.error || 'Verification failed');
                }
            } catch (e) {
                console.error(e);
                alert('Error verifying code');
            }
        }
        
        function showDisableTwoFactor() {
            document.getElementById('disable-2fa-form').style.display = 'block';
        }

        async function disableTwoFactor() {
            const pwd = document.getElementById('disable_2fa_password').value;
            if (!pwd) { alert('Password required'); return; }
            
            if(!confirm('Are you sure you want to disable 2FA? This will make your account less secure.')) return;

            try {
                const response = await fetch('/Bishwo_Calculator/profile/2fa/disable', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({ password: pwd })
                });
                const result = await response.json();
                
                if (result.success) {
                    alert('2FA Disabled.');
                    location.reload();
                } else {
                    alert(result.error || 'Failed to disable');
                }
            } catch (e) {
                console.error(e);
                alert('Error disabling 2FA');
            }
        }
        
        function copyBackupCodes() {
            const codes = window.currentBackupCodes || [];
            const codesText = codes.map((code, index) => `${index + 1}. ${code}`).join('\n');
            navigator.clipboard.writeText(codesText).then(() => {
                alert('✅ Backup codes copied to clipboard! Save them in a secure location.');
            }).catch(err => {
                const textarea = document.createElement('textarea');
                textarea.value = codesText;
                document.body.appendChild(textarea);
                textarea.select();
                document.execCommand('copy');
                document.body.removeChild(textarea);
                alert('✅ Backup codes copied to clipboard! Save them in a secure location.');
            });
        }
    </script>
</body>
</html>
