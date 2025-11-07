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
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="public/assets/css/main.css" rel="stylesheet">
    
    <style>
        .avatar-upload-area {
            border: 2px dashed #dee2e6;
            border-radius: 12px;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .avatar-upload-area:hover {
            border-color: #0d6efd;
            background-color: #f8f9fa;
        }
        
        .avatar-preview {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #e9ecef;
        }
        
        .profile-completion {
            position: relative;
            width: 120px;
            height: 120px;
        }
        
        .profile-completion svg {
            transform: rotate(-90deg);
        }
        
        .profile-completion .progress-text {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-weight: bold;
            color: #0d6efd;
        }
        
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 12px;
            color: white;
            padding: 1.5rem;
            margin-bottom: 1rem;
        }
        
        .social-link {
            display: inline-block;
            width: 40px;
            height: 40px;
            line-height: 40px;
            text-align: center;
            border-radius: 50%;
            color: white;
            margin: 0 0.5rem 0.5rem 0;
            text-decoration: none;
            transition: transform 0.2s ease;
        }
        
        .social-link:hover {
            transform: translateY(-2px);
            color: white;
        }
        
        .social-link.linkedin { background: #0077b5; }
        .social-link.twitter { background: #1da1f2; }
        .social-link.github { background: #333; }
        .social-link.facebook { background: #1877f2; }
        .social-link.instagram { background: #e4405f; }
        
        .section-card {
            border: 1px solid #dee2e6;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .form-control:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }
        
        .btn-profile {
            border-radius: 8px;
            padding: 0.5rem 1.5rem;
            font-weight: 500;
        }
        
        .preference-item {
            padding: 0.75rem;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            margin-bottom: 0.75rem;
        }
        
        .danger-zone {
            border: 1px solid #dc3545;
            border-radius: 12px;
            background: #fff5f5;
        }
        
        .loading-spinner {
            display: none;
            width: 20px;
            height: 20px;
            border: 2px solid #f3f3f3;
            border-top: 2px solid #0d6efd;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-right: 0.5rem;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>

<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3 mb-0">
                        <i class="fas fa-user-circle text-primary me-2"></i>
                        User Profile
                    </h1>
                    <div class="text-muted">
                        <i class="fas fa-calendar me-1"></i>
                        Last updated: <?php echo isset($user['updated_at']) ? date('M j, Y', strtotime($user['updated_at'])) : 'Never'; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Flash Messages -->
        <?php if (!empty($flashMessages)): ?>
            <?php foreach ($flashMessages as $type => $message): ?>
                <div class="alert alert-<?php echo $type === 'error' ? 'danger' : $type; ?> alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        
        <div class="row">
            <!-- Profile Overview -->
            <div class="col-lg-4 mb-4">
                <div class="section-card text-center">
                    <h5 class="mb-4">Profile Overview</h5>
                    
                    <!-- Avatar Upload -->
                    <div class="mb-4">
                        <div class="avatar-upload-area" id="avatarUploadArea" onclick="document.getElementById('avatarInput').click()">
                            <img src="<?php 
                                echo !empty($user['avatar']) 
                                    ? '/profile/avatar/' . htmlspecialchars($user['avatar'])
                                    : 'https://ui-avatars.com/api/?name=' . urlencode($user['first_name'] . ' ' . $user['last_name']) . '&size=120&background=0d6efd&color=fff';
                            ?>" 
                            alt="Profile Avatar" class="avatar-preview mb-2" id="avatarPreview">
                            <br>
                            <i class="fas fa-camera fa-2x text-muted mb-2"></i>
                            <p class="text-muted mb-0">Click to upload avatar</p>
                            <small class="text-muted">JPG, PNG or GIF (Max 2MB)</small>
                        </div>
                        <input type="file" id="avatarInput" accept="image/*" style="display: none;" onchange="handleAvatarUpload(this)">
                    </div>
                    
                    <!-- Profile Completion -->
                    <div class="mb-4">
                        <div class="profile-completion mx-auto">
                            <svg width="120" height="120">
                                <circle cx="60" cy="60" r="50" stroke="#e9ecef" stroke-width="10" fill="none"></circle>
                                <circle cx="60" cy="60" r="50" stroke="#0d6efd" stroke-width="10" fill="none" 
                                    stroke-dasharray="<?php echo 2 * M_PI * 50; ?>" 
                                    stroke-dashoffset="<?php echo (1 - $profile_completion / 100) * 2 * M_PI * 50; ?>"
                                    id="completionCircle"></circle>
                            </svg>
                            <div class="progress-text">
                                <span id="completionPercent"><?php echo $profile_completion; ?>%</span>
                            </div>
                        </div>
                        <p class="text-muted mt-2">Profile Completion</p>
                    </div>
                    
                    <!-- User Info -->
                    <div class="text-start">
                        <h6 class="mb-3"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h6>
                        <?php if (!empty($user['professional_title'])): ?>
                            <p class="text-muted mb-1">
                                <i class="fas fa-briefcase me-2"></i>
                                <?php echo htmlspecialchars($user['professional_title']); ?>
                            </p>
                        <?php endif; ?>
                        <?php if (!empty($user['company'])): ?>
                            <p class="text-muted mb-1">
                                <i class="fas fa-building me-2"></i>
                                <?php echo htmlspecialchars($user['company']); ?>
                            </p>
                        <?php endif; ?>
                        <?php if (!empty($user['location'])): ?>
                            <p class="text-muted mb-1">
                                <i class="fas fa-map-marker-alt me-2"></i>
                                <?php echo htmlspecialchars($user['location']); ?>
                            </p>
                        <?php endif; ?>
                        <p class="text-muted">
                            <i class="fas fa-envelope me-2"></i>
                            <?php echo htmlspecialchars($user['email']); ?>
                            <?php if (!empty($user['email_verified_at'])): ?>
                                <i class="fas fa-check-circle text-success ms-1" title="Email verified"></i>
                            <?php else: ?>
                                <i class="fas fa-exclamation-triangle text-warning ms-1" title="Email not verified"></i>
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
                
                <!-- Statistics -->
                <div class="section-card">
                    <h6 class="mb-3">Account Statistics</h6>
                    <?php if ($statistics): ?>
                        <div class="row g-2">
                            <div class="col-6">
                                <div class="stat-card text-center">
                                    <div class="h4 mb-0"><?php echo $statistics['calculations_count']; ?></div>
                                    <small>Calculations</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-card text-center">
                                    <div class="h4 mb-0"><?php echo $statistics['favorites_count']; ?></div>
                                    <small>Favorites</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-card text-center">
                                    <div class="h4 mb-0"><?php echo $statistics['login_count']; ?></div>
                                    <small>Logins</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-card text-center">
                                    <div class="h6 mb-0">
                                        <?php echo $statistics['last_login'] ? date('M j', strtotime($statistics['last_login'])) : 'Never'; ?>
                                    </div>
                                    <small>Last Login</small>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">No statistics available</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Profile Form -->
            <div class="col-lg-8">
                <form id="profileForm" enctype="multipart/form-data">
                    <!-- Professional Information -->
                    <div class="section-card">
                        <h5 class="mb-3">
                            <i class="fas fa-user-tie me-2"></i>
                            Professional Information
                        </h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="professional_title" class="form-label">Professional Title</label>
                                <input type="text" class="form-control" id="professional_title" name="professional_title" 
                                       value="<?php echo htmlspecialchars($user['professional_title'] ?? ''); ?>" 
                                       placeholder="e.g., Civil Engineer, Structural Designer">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="company" class="form-label">Company</label>
                                <input type="text" class="form-control" id="company" name="company" 
                                       value="<?php echo htmlspecialchars($user['company'] ?? ''); ?>" 
                                       placeholder="Your company name">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" class="form-control" id="phone" name="phone" 
                                       value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" 
                                       placeholder="+1 (555) 123-4567">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="location" class="form-label">Location</label>
                                <input type="text" class="form-control" id="location" name="location" 
                                       value="<?php echo htmlspecialchars($user['location'] ?? ''); ?>" 
                                       placeholder="City, Country">
                            </div>
                            <div class="col-12 mb-3">
                                <label for="bio" class="form-label">Bio</label>
                                <textarea class="form-control" id="bio" name="bio" rows="3" 
                                          placeholder="Tell us about yourself, your experience, and expertise..."><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="website" class="form-label">Website</label>
                                <input type="url" class="form-control" id="website" name="website" 
                                       value="<?php echo htmlspecialchars($user['website'] ?? ''); ?>" 
                                       placeholder="https://yourwebsite.com">
                            </div>
                            <div class="col-md-6 mb-3">
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
                    
                    <!-- Social Links -->
                    <div class="section-card">
                        <h5 class="mb-3">
                            <i class="fas fa-share-alt me-2"></i>
                            Social Media Links
                        </h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="linkedin_url" class="form-label">LinkedIn</label>
                                <input type="url" class="form-control" id="linkedin_url" name="linkedin_url" 
                                       value="<?php echo htmlspecialchars($social_links['linkedin'] ?? ''); ?>" 
                                       placeholder="https://linkedin.com/in/yourprofile">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="twitter_url" class="form-label">Twitter</label>
                                <input type="url" class="form-control" id="twitter_url" name="twitter_url" 
                                       value="<?php echo htmlspecialchars($social_links['twitter'] ?? ''); ?>" 
                                       placeholder="https://twitter.com/yourusername">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="github_url" class="form-label">GitHub</label>
                                <input type="url" class="form-control" id="github_url" name="github_url" 
                                       value="<?php echo htmlspecialchars($social_links['github'] ?? ''); ?>" 
                                       placeholder="https://github.com/yourusername">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="facebook_url" class="form-label">Facebook</label>
                                <input type="url" class="form-control" id="facebook_url" name="facebook_url" 
                                       value="<?php echo htmlspecialchars($social_links['facebook'] ?? ''); ?>" 
                                       placeholder="https://facebook.com/yourprofile">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Preferences -->
                    <div class="section-card">
                        <h5 class="mb-3">
                            <i class="fas fa-cog me-2"></i>
                            Preferences
                        </h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="measurement_system" class="form-label">Measurement System</label>
                                <select class="form-select" id="measurement_system" name="measurement_system">
                                    <option value="metric" <?php echo ($user['measurement_system'] ?? 'metric') === 'metric' ? 'selected' : ''; ?>>Metric (SI)</option>
                                    <option value="imperial" <?php echo ($user['measurement_system'] ?? 'metric') === 'imperial' ? 'selected' : ''; ?>>Imperial (US)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="section-card">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <button type="button" class="btn btn-outline-primary btn-profile me-2" data-bs-toggle="modal" data-bs-target="#notificationsModal">
                                    <i class="fas fa-bell me-2"></i>Notification Settings
                                </button>
                                <button type="button" class="btn btn-outline-info btn-profile me-2" data-bs-toggle="modal" data-bs-target="#privacyModal">
                                    <i class="fas fa-shield-alt me-2"></i>Privacy Settings
                                </button>
                                <button type="button" class="btn btn-outline-warning btn-profile me-2" data-bs-toggle="modal" data-bs-target="#passwordModal">
                                    <i class="fas fa-key me-2"></i>Change Password
                                </button>
                            </div>
                            <div>
                                <button type="submit" class="btn btn-primary btn-profile" id="saveProfileBtn">
                                    <span class="loading-spinner" id="profileLoading"></span>
                                    <i class="fas fa-save me-2"></i>Save Profile
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
                
                <!-- Danger Zone -->
                <div class="section-card danger-zone">
                    <h5 class="text-danger mb-3">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Danger Zone
                    </h5>
                    <p class="text-muted mb-3">These actions cannot be undone. Please be careful.</p>
                    <button type="button" class="btn btn-outline-danger btn-profile" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                        <i class="fas fa-trash-alt me-2"></i>Delete Account
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Profile Modals -->
    <?php include 'app/Views/user/modals/profile-modals.php'; ?>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Profile JavaScript -->
    <script src="public/assets/js/profile.js"></script>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>
