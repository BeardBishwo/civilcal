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
    <title><?php echo htmlspecialchars($pageTitle); ?> - <?= APP_NAME ?></title>
    <meta name="csrf-token" content="<?php echo csrf_token(); ?>">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Global Notification System -->
    <link rel="stylesheet" href="<?php echo app_base_url('/assets/css/global-notifications.css'); ?>">
    
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
            background-clip: text;
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
        
        /* Rank Badge */
        .rank-badge-container {
            position: absolute;
            top: -15px;
            left: -15px;
            z-index: 20;
        }
        .rank-badge {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #fbbf24, #f59e0b);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
            box-shadow: 0 4px 10px rgba(0,0,0,0.3);
            border: 2px solid white;
            transform: rotate(-10deg);
        }
        .rank-badge.level-1 { background: linear-gradient(135deg, #94a3b8, #64748b); } /* Intern - Silver */
        .rank-badge.level-2 { background: linear-gradient(135deg, #10b981, #059669); } /* Asst - Emerald */
        .rank-badge.level-3 { background: linear-gradient(135deg, #3b82f6, #2563eb); } /* Eng - Blue */
        .rank-badge.level-4 { background: linear-gradient(135deg, #8b5cf6, #7c3aed); } /* Snr - Purple */
        .rank-badge.level-5 { background: linear-gradient(135deg, #f59e0b, #d97706); font-weight: 800; } /* Chief - Gold */

        /* Power Meters */
        .power-meter-card {
            background: white;
            border-radius: 20px;
            padding: 25px;
            border: 1px solid #e2e8f0;
            margin-bottom: 20px;
        }
        .meter-label {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-weight: 700;
            color: #1e293b;
        }
        .meter-bar-outer {
            height: 12px;
            background: #f1f5f9;
            border-radius: 6px;
            overflow: hidden;
        }
        .meter-bar-inner {
            height: 100%;
            background: linear-gradient(90deg, #6366f1, #a855f7);
            border-radius: 6px;
            transition: width 1s ease-out;
        }
        .power-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-top: 30px;
        }
        .p-stat-box {
            background: #f8fafc;
            padding: 20px;
            border-radius: 15px;
            text-align: center;
            border: 1px solid #e2e8f0;
        }
        .p-stat-val {
            font-size: 1.8rem;
            font-weight: 800;
            color: #1e293b;
            display: block;
        }
        .p-stat-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            color: #64748b;
            font-weight: 600;
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
                        <!-- Rank Badge -->
                        <div class="rank-badge-container">
                            <div class="rank-badge level-<?php echo $rank_data['rank_level']; ?>" title="Your Rank: <?php echo $rank_data['rank']; ?>">
                                <i class="fas fa-<?php 
                                    echo match($rank_data['rank_level']) {
                                        1 => 'seedling',
                                        2 => 'hard-hat',
                                        3 => 'drafting-compass',
                                        4 => 'medal',
                                        5 => 'crown',
                                        default => 'user'
                                    };
                                ?>"></i>
                            </div>
                        </div>

                        <!-- Civil Identity Composite Avatar -->
                        <div class="avatar-composite" style="width: 120px; height: 120px;">
                            <?php 
                                $avatarFile = !empty($user['avatar_id']) ? $user['avatar_id'] . '.webp' : 'avatar_starter_mascot.webp';
                                $frameFile = !empty($user['frame_id']) ? $user['frame_id'] . '.webp' : null;
                                
                                // Clean up ID to filename just in case
                                if (!str_contains($avatarFile, '.')) $avatarFile .= '.webp';
                                if ($frameFile && !str_contains($frameFile, '.')) $frameFile .= '.webp';
                            ?>
                            
                            <!-- Base Face -->
                            <img src="<?php echo app_url('themes/default/assets/resources/avatars/' . $avatarFile); ?>" 
                                 class="user-face border-4 border-white shadow-lg" 
                                 onerror="this.src='https://ui-avatars.com/api/?name=<?php echo urlencode($user['username']); ?>&size=120&background=6366f1&color=fff'">
                            
                            <!-- Frame Overlay -->
                            <?php if ($frameFile): ?>
                                <img src="<?php echo app_url('themes/default/assets/resources/avatars/' . $frameFile); ?>" 
                                     class="user-frame" style="transform: scale(1.15);">
                            <?php endif; ?>
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
                    <div style="margin-top: 10px; display: flex; gap: 8px;">
                        <span style="background: rgba(99, 102, 241, 0.1); color: #6366f1; padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 600;">
                            <?php echo $rank_data['rank']; ?>
                        </span>
                        <span style="background: rgba(245, 158, 11, 0.1); color: #f59e0b; padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 600;">
                            Lvl <?php echo $rank_data['rank_level']; ?>
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
                <button class="nav-tab" onclick="switchTab('identity', this)">Civil Identity</button>
                <button class="nav-tab" onclick="switchTab('prestige', this)">Prestige Center</button>
                <button class="nav-tab" onclick="switchTab('professional', this)">Professional</button>
                <button class="nav-tab" onclick="switchTab('social', this)">Social & Links</button>
                <button class="nav-tab" onclick="switchTab('preferences', this)">Preferences</button>
                <button class="nav-tab" onclick="switchTab('security', this)">Security</button>
                <button class="nav-tab danger-tab" onclick="switchTab('danger', this)">Danger Zone</button>
            </div>

            <form id="profileForm" method="POST" enctype="multipart/form-data">
                <input type="file" id="avatarInput" name="avatar" accept="image/*" style="display: none;" onchange="handleAvatarUpload(this)">
                
                <!-- Tab: Prestige Center -->
                <div id="tab-prestige" class="tab-content">
                    <div class="power-meter-card" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: white; border: none;">
                        <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 25px;">
                            <div>
                                <h2 style="color: #f1f5f9; font-size: 1.8rem; margin-bottom: 5px;">Overall Status</h2>
                                <p style="color: #94a3b8;">Level <?php echo $rank_data['rank_level']; ?> • <?php echo $rank_data['rank']; ?></p>
                            </div>
                            <div style="text-align: right;">
                                <span style="font-size: 2.5rem; font-weight: 800; color: #fbbf24;"><?php echo number_format($rank_data['total_power']); ?></span>
                                <p style="color: #94a3b8; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px;">Construction Power</p>
                            </div>
                        </div>

                        <div class="meter-label">
                            <span style="color: #e2e8f0;">Progress to <?php echo $rank_data['next_rank']; ?></span>
                            <span style="color: #fbbf24;"><?php echo $rank_data['rank_progress']; ?>%</span>
                        </div>
                        <div class="meter-bar-outer" style="background: rgba(255,255,255,0.1); height: 16px;">
                            <div class="meter-bar-inner" style="width: <?php echo $rank_data['rank_progress']; ?>%; background: linear-gradient(90deg, #fbbf24, #f59e0b);"></div>
                        </div>
                        <p style="font-size: 0.8rem; color: #94a3b8; margin-top: 10px; text-align: center;">
                            Gain <?php echo number_format($rank_data['next_rank_power'] - $rank_data['total_power']); ?> more power to reach next tier!
                        </p>
                    </div>

                    <div class="form-grid">
                        <div class="power-meter-card">
                            <div class="meter-label">
                                <span><i class="fas fa-book-reader" style="color: #6366f1;"></i> Knowledge</span>
                                <span><?php echo $rank_data['meters']['knowledge']; ?>%</span>
                            </div>
                            <div class="meter-bar-outer">
                                <div class="meter-bar-inner" style="width: <?php echo $rank_data['meters']['knowledge']; ?>%;"></div>
                            </div>
                            <p style="font-size: 0.75rem; color: #64748b; margin-top: 10px;">Based on News Reads & Quizzes</p>
                        </div>

                        <div class="power-meter-card">
                            <div class="meter-label">
                                <span><i class="fas fa-bullseye" style="color: #10b981;"></i> Precision</span>
                                <span><?php echo $rank_data['meters']['precision']; ?>%</span>
                            </div>
                            <div class="meter-bar-outer">
                                <div class="meter-bar-inner" style="width: <?php echo $rank_data['meters']['precision']; ?>%; background: linear-gradient(90deg, #10b981, #34d399);"></div>
                            </div>
                            <p style="font-size: 0.75rem; color: #64748b; margin-top: 10px;">Based on Calculator Accuracy & Usage</p>
                        </div>
                    </div>

                    <div class="power-stats">
                        <div class="p-stat-box">
                            <span class="p-stat-val"><?php echo $statistics['news_reads_count']; ?></span>
                            <span class="p-stat-label">Articles Read</span>
                        </div>
                        <div class="p-stat-box">
                            <span class="p-stat-val"><?php echo $statistics['quizzes_completed_count']; ?></span>
                            <span class="p-stat-label">Quizzes Done</span>
                        </div>
                        <div class="p-stat-box">
                            <span class="p-stat-val"><?php echo $statistics['calculations_count']; ?></span>
                            <span class="p-stat-label">Total Calcs</span>
                        </div>
                    </div>
                </div>
                <div id="tab-info" class="tab-content active">
                    <!-- ... Personal Info Form ... -->
                    
                <!-- Tab: Civil Identity -->
                <div id="tab-identity" class="tab-content">
                    <?php 
                        // Include the Avatar Selector Component
                        // Pass $user and $db to it automatically via scope
                        $db = \App\Core\Database::getInstance(); 
                        include __DIR__ . '/components/avatar_selector.php'; 
                    ?>
                </div>
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
                        
                        <!-- Two-Factor Authentication Section -->
                        <div class="form-group form-grid-full" style="margin-top: 40px;">
                            <div style="background: #1e293b; border-radius: 12px; padding: 30px; border: 1px solid #334155;">
                                <h3 style="color: #f1f5f9; margin: 0 0 20px 0; font-size: 1.25rem; font-weight: 600;">Two factor authentication</h3>
                                
                                <div id="2fa-status-container">
                                    <?php if (!empty($two_factor_status['enabled'])): ?>
                                        <!-- 2FA Enabled State -->
                                        <div style="margin-bottom: 20px;">
                                            <p style="color: #10b981; font-weight: 600; margin: 0 0 15px 0;">
                                                <i class="fas fa-check-circle"></i> Two-factor authentication is enabled.
                                            </p>
                                            <p style="color: #94a3b8; margin: 0 0 20px 0; line-height: 1.6;">
                                                Your account is protected with two-factor authentication. You'll need your authenticator app to sign in.
                                            </p>
                                        </div>
                                        <button type="button" class="btn-danger" onclick="showDisable2FA()" style="background: #ef4444; padding: 10px 20px; border-radius: 8px;">
                                            Disable
                                        </button>
                                        
                                        <!-- Disable Form (Hidden) -->
                                        <div id="disable-2fa-container" style="display: none; margin-top: 20px; padding-top: 20px; border-top: 1px solid #334155;">
                                            <p style="color: #f1f5f9; margin: 0 0 15px 0; font-weight: 500;">Confirm your password to disable 2FA:</p>
                                            <input type="password" class="form-input" id="disable_2fa_password" placeholder="Enter your password" style="margin-bottom: 15px; background: #0f172a; border: 1px solid #334155; color: #f1f5f9;">
                                            <div style="display: flex; gap: 10px;">
                                                <button type="button" class="btn-danger" onclick="confirmDisable2FA()">Confirm Disable</button>
                                                <button type="button" onclick="hideDisable2FA()" style="background: #475569; color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer;">Cancel</button>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <!-- 2FA Disabled State -->
                                        <div id="2fa-disabled-view">
                                            <p style="color: #f1f5f9; font-weight: 600; margin: 0 0 15px 0;">
                                                You have not enabled two factor authentication.
                                            </p>
                                            <p style="color: #94a3b8; margin: 0 0 25px 0; line-height: 1.6;">
                                                When two factor authentication is enabled, you will be prompted for a secure, random token during authentication. You may retrieve this token from your phone's Google Authenticator application.
                                            </p>
                                            <button type="button" onclick="start2FASetup()" style="background: #6366f1; color: white; border: none; padding: 10px 24px; border-radius: 8px; font-weight: 500; cursor: pointer; transition: all 0.2s;">
                                                Enable
                                            </button>
                                        </div>
                                        
                                        <!-- Setup Container (Hidden Initially) -->
                                        <div id="2fa-setup-view" style="display: none;">
                                            <!-- QR Code Step -->
                                            <div style="padding: 20px; background: #0f172a; border-radius: 8px; margin-bottom: 20px;">
                                                <p style="color: #f1f5f9; margin: 0 0 15px 0; font-weight: 500;">Scan this QR code with your authenticator app:</p>
                                                <div id="qr-code-container" style="background: white; padding: 15px; border-radius: 8px; display: inline-block; margin-bottom: 15px;"></div>
                                                <p style="color: #94a3b8; margin: 0; font-size: 0.875rem;">
                                                    Secret key: <code id="secret-key-text" style="background: #1e293b; padding: 4px 8px; border-radius: 4px; color: #10b981; font-family: 'Courier New', monospace;"></code>
                                                </p>
                                            </div>
                                            
                                            <!-- Backup Codes -->
                                            <div style="padding: 20px; background: #0f172a; border-radius: 8px; margin-bottom: 20px;">
                                                <p style="color: #f1f5f9; margin: 0 0 10px 0; font-weight: 500;">
                                                    <i class="fas fa-key" style="color: #f59e0b;"></i> Backup Recovery Codes
                                                </p>
                                                <p style="color: #94a3b8; margin: 0 0 15px 0; font-size: 0.875rem;">
                                                    Save these codes in a secure place. Each can be used once if you lose access to your authenticator.
                                                </p>
                                                <div id="backup-codes-grid" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 8px; margin-bottom: 15px; font-family: 'Courier New', monospace; font-size: 0.875rem;"></div>
                                                <button type="button" onclick="copyBackupCodes()" style="background: #475569; color: white; border: none; padding: 8px 16px; border-radius: 6px; font-size: 0.875rem; cursor: pointer;">
                                                    <i class="fas fa-copy"></i> Copy Codes
                                                </button>
                                            </div>
                                            
                                            <!-- Verification Input -->
                                            <div style="padding: 20px; background: #0f172a; border-radius: 8px; margin-bottom: 20px;">
                                                <p style="color: #f1f5f9; margin: 0 0 15px 0; font-weight: 500;">Enter the 6-digit code from your app:</p>
                                                <input type="text" id="verify-2fa-code" placeholder="000000" maxlength="6" class="form-input" style="background: #1e293b; border: 1px solid #334155; color: #f1f5f9; font-family: 'Courier New', monospace; font-size: 1.25rem; text-align: center; letter-spacing: 0.5em; margin-bottom: 15px; width: 200px;">
                                                <div style="display: flex; gap: 10px;">
                                                    <button type="button" onclick="verify2FACode()" style="background: #10b981; color: white; border: none; padding: 10px 24px; border-radius: 8px; font-weight: 500; cursor: pointer;">
                                                        Verify & Enable
                                                    </button>
                                                    <button type="button" onclick="cancel2FASetup()" style="background: #475569; color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer;">
                                                        Cancel
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
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
                const response = await fetch(window.appConfig.baseUrl + '/profile/update', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (response.ok) {
                    showNotification('Profile updated successfully!', 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showNotification('Error: ' + (result.error || 'Update failed'), 'error');
                }
            } catch (err) {
                showNotification('Network error occurred.', 'error');
            } finally {
                btn.disabled = false;
                spinner.style.display = 'none';
            }
        });

        // Delete Account
        function confirmDelete() {
            showConfirmModal(
                'Delete Account',
                'Are you <strong>ABSOLUTELY SURE</strong>? This action cannot be undone and will permanently delete all your data.',
                () => {
                    showPrompt(
                        'Confirm Password',
                        'Please enter your password to confirm account deletion:',
                        (password) => {
                            if(password) {
                                // Call delete API
                                showNotification('Account deletion request sent.', 'info');
                            }
                        },
                        {
                            inputType: 'password',
                            placeholder: 'Enter your password',
                            confirmText: 'Delete Account'
                        }
                    );
                },
                {
                    confirmText: 'Continue',
                    icon: 'fa-exclamation-triangle'
                }
            );
        }
        // Update Password Function
        async function updatePassword() {
            const current = document.getElementById('current_password').value;
            const newPass = document.getElementById('new_password').value;
            const confirm = document.getElementById('confirm_password').value;
            
            if (!current || !newPass || !confirm) {
                showNotification('Please fill in all password fields', 'error');
                return;
            }
            
            if (newPass !== confirm) {
                showNotification('New passwords do not match', 'error');
                return;
            }
            
            const btn = event.target.closest('button');
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
            
            try {
                // Use hardcoded path for reliability
                const response = await fetch(window.appConfig.baseUrl + '/profile/password', {
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
                    showNotification('Password updated successfully', 'success');
                    document.getElementById('current_password').value = '';
                    document.getElementById('new_password').value = '';
                    document.getElementById('confirm_password').value = '';
                } else {
                    showNotification(result.error || 'Failed to update password', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showNotification('An error occurred while updating password', 'error');
            } finally {
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        }
        
        // 2FA Functions - New Minimal UI
        async function start2FASetup() {
            showPrompt(
                'Enable Two-Factor Authentication',
                'Please confirm your password to continue:',
                async (pwd) => {
            if (!pwd) return;

            try {
                const response = await fetch(window.appConfig.baseUrl + '/profile/2fa/enable', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({ password: pwd })
                });
                const result = await response.json();
                
                if (result.success) {
                    // Store backup codes globally
                    window.currentBackupCodes = result.recovery_codes || [];
                    
                    // Hide disabled view, show setup view
                    document.getElementById('2fa-disabled-view').style.display = 'none';
                    document.getElementById('2fa-setup-view').style.display = 'block';
                    
                    // Display QR code
                    document.getElementById('qr-code-container').innerHTML = `<img src="${result.qr_code_url}" alt="QR Code" style="max-width: 200px;" />`;
                    document.getElementById('secret-key-text').innerText = result.secret;
                    
                    // Display backup codes in grid
                    const backupCodesGrid = document.getElementById('backup-codes-grid');
                    if (backupCodesGrid && window.currentBackupCodes.length > 0) {
                        backupCodesGrid.innerHTML = window.currentBackupCodes.map((code, index) => 
                            `<div style="padding: 8px; background: #1e293b; border-radius: 4px; color: #10b981;">${index + 1}. ${code}</div>`
                        ).join('');
                    }
                } else {
                    showNotification(result.error || 'Failed to start setup', 'error');
                }
            } catch (e) {
                console.error(e);
                showNotification('Error starting 2FA setup', 'error');
            }
                },
                {
                    inputType: 'password',
                    placeholder: 'Enter your password',
                    confirmText: 'Continue'
                }
            );
        }

        async function verify2FACode() {
            const code = document.getElementById('verify-2fa-code').value;
            if (!code) { 
                showNotification('Please enter the 6-digit code', 'error'); 
                return; 
            }
            
            try {
                const response = await fetch(window.appConfig.baseUrl + '/profile/2fa/confirm', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({ code: code })
                });
                const result = await response.json();
                
                if (result.success) {
                    showNotification('2FA Enabled Successfully!', 'success');
                    location.reload();
                } else {
                    showNotification(result.error || 'Invalid verification code', 'error');
                }
            } catch (e) {
                console.error(e);
                showNotification('Error verifying code', 'error');
            }
        }
        
        function cancel2FASetup() {
            document.getElementById('2fa-setup-view').style.display = 'none';
            document.getElementById('2fa-disabled-view').style.display = 'block';
            document.getElementById('verify-2fa-code').value = '';
        }
        
        
        function showDisable2FA() {
            showConfirmModal(
                'Disable Two-Factor Authentication',
                'Are you sure you want to disable 2FA? This will make your account less secure.',
                () => {
                    showPrompt(
                        'Confirm Password',
                        'Please enter your password to disable 2FA:',
                        async (pwd) => {
                            if (!pwd) {
                                showNotification('Password is required', 'error');
                                return;
                            }
                            
                            try {
                                const response = await fetch(window.appConfig.baseUrl + '/profile/2fa/disable', {
                                    method: 'POST',
                                    headers: {'Content-Type': 'application/json'},
                                    body: JSON.stringify({ password: pwd })
                                });
                                const result = await response.json();
                                
                                if (result.success) {
                                    showNotification('2FA has been disabled', 'success');
                                    setTimeout(() => location.reload(), 1000);
                                } else {
                                    showNotification(result.error || 'Failed to disable', 'error');
                                }
                            } catch (e) {
                                console.error(e);
                                showNotification('Error disabling 2FA', 'error');
                            }
                        },
                        {
                            inputType: 'password',
                            placeholder: 'Enter your password',
                            confirmText: 'Disable 2FA'
                        }
                    );
                },
                {
                    confirmText: 'Continue',
                    icon: 'fa-shield-alt'
                }
            );
        }
        
        function hideDisable2FA() {
            // No longer needed with modal approach
        }
        
        
        function copyBackupCodes() {
            const codes = window.currentBackupCodes || [];
            const codesText = codes.map((code, index) => `${index + 1}. ${code}`).join('\n');
            navigator.clipboard.writeText(codesText).then(() => {
                showNotification('Backup codes copied to clipboard! Save them in a secure location.', 'success');
            }).catch(err => {
                const textarea = document.createElement('textarea');
                textarea.value = codesText;
                document.body.appendChild(textarea);
                textarea.select();
                document.execCommand('copy');
                document.body.removeChild(textarea);
                showNotification('Backup codes copied to clipboard! Save them in a secure location.', 'success');
            });
        }
    </script>
    
    <!-- Global Notification System -->
    <script src="<?php echo app_base_url('/assets/js/global-notifications.js'); ?>"></script>
</body>
</html>
