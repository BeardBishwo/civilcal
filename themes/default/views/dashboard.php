<?php
$page_title = 'User Dashboard - ' . \App\Services\SettingsService::get('site_name', 'Civil Cal');
?>

<style>
    .dashboard-hero {
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
        padding: 60px 20px;
        border-radius: 30px;
        color: white;
        text-align: center;
        margin-bottom: 40px;
        box-shadow: 0 20px 40px rgba(99, 102, 241, 0.2);
    }
    .dashboard-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 30px;
    }
    .quest-card {
        background: #1e293b;
        color: white;
        border-radius: 24px;
        padding: 30px;
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(255,255,255,0.1);
    }
    .quest-card::after {
        content: '';
        position: absolute;
        top: -50px;
        right: -50px;
        width: 150px;
        height: 150px;
        background: rgba(255,255,255,0.05);
        border-radius: 50%;
    }
    .rank-card {
        background: white;
        border-radius: 24px;
        padding: 30px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 10px 25px rgba(0,0,0,0.05);
    }
    .status-badge {
        padding: 6px 16px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 700;
        text-transform: uppercase;
    }
    .status-badge.pending { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
    .status-badge.completed { background: rgba(16, 185, 129, 0.1); color: #10b981; }
    
    .progress-track {
        height: 12px;
        background: #f1f5f9;
        border-radius: 6px;
        margin: 20px 0 10px;
        overflow: hidden;
    }
    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #6366f1, #8b5cf6);
        border-radius: 6px;
        transition: width 1s ease;
    }

    @media (max-width: 900px) {
        .dashboard-grid { grid-template-columns: 1fr; }
    }
</style>
<script>
function toggleStudyMode() {
    const currentMode = document.getElementById('mode-status').innerText.includes('LOKSEWA') ? 'psc' : 'world';
    const newMode = currentMode === 'psc' ? 'world' : 'psc';
    const slider = document.getElementById('mode-slider');
    const status = document.getElementById('mode-status');

    // Optimistic UI Update
    if (newMode === 'world') {
        slider.classList.remove('left-1', 'bg-green-600');
        slider.classList.add('left-1/2', 'bg-blue-600');
        status.innerHTML = '🏗️ WORLD';
        status.className = 'text-xs font-bold text-blue-400';
    } else {
        slider.classList.remove('left-1/2', 'bg-blue-600');
        slider.classList.add('left-1', 'bg-green-600');
        status.innerHTML = '🛡️ LOKSEWA';
        status.className = 'text-xs font-bold text-green-400';
    }

    fetch('<?= app_base_url("/api/career/mode") ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ mode: newMode })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            console.log('Mode updated to ' + data.mode);
            // Reload page to refresh content filters? Or just toast?
            // User might want to see changes immediately.
             window.location.reload(); 
        } else {
            console.error('Failed to update mode');
            // Revert UI?
        }
    });
}
</script>

<div class="container" style="padding: 40px 20px;">
    <div class="dashboard-hero">
        <h1 style="font-size: 2.5rem; font-weight: 800; margin-bottom: 10px;">नमस्ते, <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Engineer'); ?>!</h1>
        <p style="font-size: 1.1rem; opacity: 0.9;">Welcome to your professional engineering workspace.</p>
    </div>

    <div class="dashboard-grid">
        <!-- Rank Progress Card -->
        <div class="rank-card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
                <h3 style="font-weight: 800; color: #1e293b; margin: 0;">Professional Rank</h3>
                <span style="font-size: 1.2rem; font-weight: 800; color: #6366f1;">Level <?php echo $rank['rank_level']; ?></span>
            </div>
            
            <div style="display: flex; align-items: center; gap: 20px; margin-bottom: 20px;">
                <?php 
                    $level = str_pad($rank['rank_level'] ?? 1, 2, '0', STR_PAD_LEFT);
                    $rawTitle = strtolower($user['rank_title'] ?? 'intern');
                    $slug = 'intern';
                    if (strpos($rawTitle, 'surveyor') !== false) $slug = 'surveyor';
                    elseif (strpos($rawTitle, 'supervisor') !== false) $slug = 'supervisor';
                    elseif (strpos($rawTitle, 'assistant') !== false) $slug = 'assistant';
                    elseif (strpos($rawTitle, 'senior') !== false) $slug = 'senior';
                    elseif (strpos($rawTitle, 'manager') !== false) $slug = 'manager';
                    elseif (strpos($rawTitle, 'chief') !== false) $slug = 'chief';
                    
                    $badgeUrl = "/themes/default/assets/resources/ranks/rank_{$level}_{$slug}.webp";
                ?>
                <div style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center;">
                    <img src="<?= $badgeUrl ?>" onerror="this.src='/themes/default/assets/resources/ranks/rank_01_intern.webp'" class="w-full h-full object-contain filter drop-shadow-lg transition-transform hover:scale-110 duration-300">
                </div>
                <div>
                    <h4 style="margin: 0; color: #1e293b; font-size: 1.1rem;"><?php echo $rank['rank']; ?></h4>
                    <p style="margin: 0; color: #64748b; font-size: 0.85rem;">Next: <?php echo $rank['next_rank']; ?></p>
                </div>
            </div>

            <div class="progress-track">
                <div class="progress-fill" style="width: <?php echo $rank['rank_progress']; ?>%;"></div>
            </div>
            <div style="display: flex; justify-content: space-between; font-size: 0.75rem; color: #64748b; font-weight: 600;">
                <span><?php echo number_format($rank['total_power']); ?> pts</span>
                <span><?php echo number_format($rank['next_rank_power']); ?> pts</span>
            </div>

            <?php if ($rank['rank_progress'] >= 100): ?>
                <button onclick="triggerPromotion()" class="w-full mt-4 bg-gradient-to-r from-yellow-400 to-orange-500 hover:from-yellow-500 hover:to-orange-600 text-white font-black py-3 rounded-xl shadow-lg border-b-4 border-orange-600 active:border-b-0 active:translate-y-1 transition-all animate-pulse">
                    <i class="fas fa-crown mr-2"></i> PROMOTION AVAILABLE!
                </button>
            <?php endif; ?>

            <a href="<?php echo app_base_url('/profile'); ?>" style="display: block; text-align: center; margin-top: 25px; color: #6366f1; text-decoration: none; font-weight: 700; font-size: 0.9rem;">
                View Detailed Metrics <i class="fas fa-arrow-right" style="margin-left: 5px;"></i>
            </a>
        </div>

        <!-- Daily Mission Card -->
        <div class="quest-card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
                <h3 style="font-weight: 800; color: white; margin: 0;">Current Mission</h3>
                <span class="status-badge <?php echo $quest['completed'] ? 'completed' : 'pending'; ?>">
                    <?php echo $quest['completed'] ? '<i class="fas fa-check-circle"></i> Completed' : 'In Progress'; ?>
                </span>
            </div>

            <p style="color: #94a3b8; font-size: 0.9rem; margin-bottom: 20px;">Complete a calculation with the designated tool of the day to earn bonus coins!</p>
            
            <div class="p-6 bg-gradient-to-r from-blue-900 to-slate-900 border-b border-gray-700">
                    <div class="relative">
                        <img src="<?php echo !empty($user['avatar']) ? '/uploads/avatars/' . $user['avatar'] : 'https://ui-avatars.com/api/?name=' . urlencode($user['username']); ?>" 
                             class="w-16 h-16 rounded-full border-2 border-yellow-500 shadow-lg object-cover">
                        <!-- Rank Badge Overlay -->
                        <div class="absolute -bottom-2 -right-2 bg-white rounded-full p-1 shadow-md border border-gray-200" title="<?php echo htmlspecialchars($user['rank_title'] ?? 'Intern'); ?>">
                            <?php 
                                $level = str_pad($rank['rank_level'] ?? 1, 2, '0', STR_PAD_LEFT);
                                // Reuse logic or just use Intern as default if logic repeated
                                // Ideally use a helper function, but for view simplicity:
                                $uTitle = strtolower($user['rank_title'] ?? 'intern');
                                $uSlug = 'intern';
                                if (strpos($uTitle, 'surveyor') !== false) $uSlug = 'surveyor';
                                elseif (strpos($uTitle, 'supervisor') !== false) $uSlug = 'supervisor';
                                elseif (strpos($uTitle, 'assistant') !== false) $uSlug = 'assistant';
                                elseif (strpos($uTitle, 'senior') !== false) $uSlug = 'senior';
                                elseif (strpos($uTitle, 'manager') !== false) $uSlug = 'manager';
                                elseif (strpos($uTitle, 'chief') !== false) $uSlug = 'chief';
                                
                            $badgePath = "/themes/default/assets/resources/ranks/rank_{$level}_{$uSlug}.webp";
                            ?>
                            <img src="<?php echo $badgePath; ?>" onerror="this.src='/themes/default/assets/resources/ranks/rank_01_intern.webp'" class="w-8 h-8 object-contain filter drop-shadow">
                        </div>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-white"><?php echo htmlspecialchars($user['username']); ?></h2>
                        <div class="flex items-center gap-2 text-sm">
                            <span class="px-2 py-0.5 rounded bg-yellow-500/20 text-yellow-400 border border-yellow-500/30 font-bold">
                                <?php echo htmlspecialchars($user['rank_title'] ?? 'Intern'); ?>
                            </span>
                            <span class="text-gray-400">|</span>
                            <span class="text-gray-300"><?php echo number_format($user['xp'] ?? 0); ?> XP</span>
                        </div>
                    </div>
                </div>

                <!-- Study Mode Toggle -->
                <div class="bg-gray-800/50 rounded-lg p-3 border border-gray-700">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs text-gray-400 uppercase tracking-widest font-semibold">Active Mode</span>
                        <span id="mode-status" class="text-xs font-bold <?php echo ($user['study_mode'] ?? 'psc') === 'psc' ? 'text-green-400' : 'text-blue-400'; ?>">
                            <?php echo ($user['study_mode'] ?? 'psc') === 'psc' ? '🛡️ LOKSEWA' : '🏗️ WORLD'; ?>
                        </span>
                    </div>
                    <div class="flex bg-gray-900 rounded-full p-1 relative cursor-pointer" onclick="toggleStudyMode()">
                        <div id="mode-slider" class="w-1/2 h-8 rounded-full shadow-md transition-all duration-300 absolute top-1 <?php echo ($user['study_mode'] ?? 'psc') === 'psc' ? 'left-1 bg-green-600' : 'left-1/2 bg-blue-600'; ?>"></div>
                        <div class="w-1/2 text-center text-xs font-bold text-white z-10 py-2">PSC</div>
                        <div class="w-1/2 text-center text-xs font-bold text-white z-10 py-2">WORLD</div>
                    </div>
                </div>
            </div>

            <?php if (!$quest['completed']): ?>
        <a href="<?php echo get_tool_url($quest['tool']); ?>" class="btn" style="background: white; color: #1e293b; width: 100%; border-radius: 12px; font-weight: 700; padding: 12px; text-decoration: none; display: block; text-align: center; transition: all 0.2s;">
                    Launch Tool Now
        </a>
    <?php else: ?>
        <div style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2); color: #10b981; padding: 12px; border-radius: 12px; text-align: center; font-weight: 700;">
            Reward Collected <i class="fas fa-coins" style="margin-left: 5px;"></i>
        </div>
    <?php endif; ?>
</div>

<?php 
function get_tool_url($tool) {
    if (!$tool) return '#';
    // CMS Calculators use category and calculator_id
    if (isset($tool['category']) && isset($tool['calculator_id'])) {
        return app_base_url('/calculators/' . $tool['category'] . '/' . $tool['calculator_id'] . '/protected');
    }
    // Unit converters use slug
    if (isset($tool['slug'])) {
        return app_base_url('/' . $tool['slug']);
    }
    // Fallback to path if exists
    return app_base_url($tool['path'] ?? '#');
}
?>
        <!-- Referral Program Card -->
        <div class="rank-card" style="grid-column: 1 / -1; background: linear-gradient(to right, #f0f9ff, #e0f2fe); border-color: #bae6fd;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 style="font-weight: 800; color: #0369a1; margin: 0;">Invite & Earn</h3>
                <span class="status-badge" style="background: #0ea5e9; color: white;">+50 Coins / Friend</span>
            </div>
            <div style="display: flex; gap: 30px; align-items: center; flex-wrap: wrap;">
                <div style="flex: 1;">
                    <p style="color: #0c4a6e; margin-bottom: 15px;">Share your unique link. When they solve 5 quizzes, you both get paid!</p>
                    <?php 
                        $currUser = (new \App\Models\User())->find($_SESSION['user_id'] ?? 0);
                        $refCode = $currUser->referral_code ?? 'GENERATE'; 
                        if($refCode === 'GENERATE') {
                            // Auto-fix if missing
                            (new \App\Models\User())->incrementQuizCount($_SESSION['user_id'] ?? 0);
                            $refCode = (new \App\Models\User())->find($_SESSION['user_id'])->referral_code;
                        }
                        $refLink = app_base_url('/register?ref=' . $refCode);
                    ?>
                    <div style="background: white; padding: 10px 15px; border-radius: 12px; display: flex; gap: 10px; border: 1px dashed #7dd3fc; align-items: center;">
                        <code style="font-weight: bold; color: #0284c7; flex: 1;"><?= $refLink ?></code>
                        <button onclick="navigator.clipboard.writeText('<?= $refLink ?>'); alert('Copied!');" style="border: none; background: #0ea5e9; color: white; padding: 5px 12px; border-radius: 6px; cursor: pointer; font-weight: bold;">Copy</button>
                    </div>
                </div>
                <div style="text-align: center; min-width: 120px;">
                    <div style="font-size: 2rem; font-weight: 800; color: #0284c7;"><?= $currUser->quiz_solved_count ?? 0 ?></div>
                    <div style="font-size: 0.8rem; color: #0c4a6e; font-weight: 600;">Quizzes Solved</div>
                </div>
            </div>
        </div>

        <!-- App Shortcuts Card -->
        <div class="rank-card" style="grid-column: 1 / -1;">
            <h3 style="font-weight: 800; color: #1e293b; margin-bottom: 25px;">Quick Actions</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
                <a href="<?php echo app_base_url('/calculators'); ?>" style="text-decoration: none; background: #f8fafc; padding: 20px; border-radius: 16px; border: 1px solid #e2e8f0; display: flex; align-items: center; gap: 15px; transition: all 0.2s;">
                    <i class="fas fa-th-large" style="color: #6366f1; font-size: 1.2rem;"></i>
                    <span style="color: #1e293b; font-weight: 700;">All Tools</span>
                </a>
                <a href="<?php echo app_base_url('/blog'); ?>" style="text-decoration: none; background: #f8fafc; padding: 20px; border-radius: 16px; border: 1px solid #e2e8f0; display: flex; align-items: center; gap: 15px; transition: all 0.2s;">
                    <i class="fas fa-newspaper" style="color: #10b981; font-size: 1.2rem;"></i>
                    <span style="color: #1e293b; font-weight: 700;">Read & Earn</span>
                </a>
                <a href="<?php echo app_base_url('/quiz'); ?>" style="text-decoration: none; background: #f8fafc; padding: 20px; border-radius: 16px; border: 1px solid #e2e8f0; display: flex; align-items: center; gap: 15px; transition: all 0.2s;">
                    <i class="fas fa-brain" style="color: #f59e0b; font-size: 1.2rem;"></i>
                    <span style="color: #1e293b; font-weight: 700;">Take a Quiz</span>
                </a>
                <?php if (isset($_SESSION['user_role']) && in_array($_SESSION['user_role'], ['admin', 'superadmin'])): ?>
                <a href="<?php echo app_base_url('/admin/dashboard'); ?>" style="text-decoration: none; background: #fef2f2; padding: 20px; border-radius: 16px; border: 1px solid #fee2e2; display: flex; align-items: center; gap: 15px; transition: all 0.2s;">
                    <i class="fas fa-user-shield" style="color: #ef4444; font-size: 1.2rem;"></i>
                    <span style="color: #1e293b; font-weight: 700;">Admin Panel</span>
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Level Up Modal -->
<div id="level-up-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/80 backdrop-blur-sm transition-opacity duration-300">
    <div class="bg-white rounded-2xl p-8 max-w-sm w-full mx-4 text-center transform scale-90 opacity-0 transition-all duration-500 relative" id="level-up-card">
        <div class="absolute -top-16 left-1/2 transform -translate-x-1/2 w-32 h-32 bg-yellow-400 rounded-full flex items-center justify-center shadow-[0_0_50px_rgba(250,204,21,0.5)] animate-bounce-slow border-4 border-white">
             <!-- Modal Icon -->
             <img src="/themes/default/assets/resources/ranks/rank_<?= str_pad($rank['rank_level'] ?? 1, 2, '0', STR_PAD_LEFT) ?>_intern.webp" 
                  class="w-24 h-24 object-contain filter drop-shadow-xl" 
                  id="level-up-icon"
                  onerror="this.src='/themes/default/assets/resources/ranks/rank_01_intern.webp'">
        </div>
        
        <div class="mt-16">
            <h2 class="text-3xl font-black text-transparent bg-clip-text bg-gradient-to-r from-yellow-500 to-orange-500 mb-2">LEVEL UP!</h2>
            <p class="text-gray-500 font-medium mb-6">You have been promoted to</p>
            
            <div class="bg-gradient-to-r from-slate-900 to-slate-800 text-white rounded-xl p-4 mb-6 shadow-inner border border-slate-700">
                <div class="text-xs text-slate-400 uppercase tracking-widest mb-1">New Rank</div>
                <div class="text-2xl font-black text-yellow-400 tracking-wide" id="level-up-rank"><?= $rank['rank'] ?? 'Engineer' ?></div>
            </div>
            
            <!-- Rewards -->
            <div class="grid grid-cols-2 gap-4 mb-8">
                <div class="bg-blue-50 p-3 rounded-lg border border-blue-100">
                    <div class="text-blue-500 text-xs font-bold uppercase tracking-wider">Salary</div>
                    <div class="text-blue-900 font-bold text-lg">+15%</div>
                </div>
                <div class="bg-green-50 p-3 rounded-lg border border-green-100">
                    <div class="text-green-500 text-xs font-bold uppercase tracking-wider">Bonus</div>
                    <div class="text-green-900 font-bold text-lg">+50 <small class="font-normal text-xs">Coins</small></div>
                </div>
            </div>
            
            <button onclick="closeLevelUp()" class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold py-4 rounded-xl shadow-lg shadow-blue-500/30 transform transition active:scale-95 text-lg">
                Claim Rewards & Continue
            </button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Check for promotion session flag
    <?php if(isset($_SESSION['just_promoted'])): ?>
        const rankName = "<?= $slug ?? 'intern' ?>";
        const rankLevel = "<?= str_pad($rank['rank_level'] ?? 1, 2, '0', STR_PAD_LEFT) ?>";
        
        // Update Icon Dynamically
        document.getElementById('level-up-icon').src = `/themes/default/assets/resources/ranks/rank_${rankLevel}_${rankName}.webp`;
        
        showLevelUp();
        <?php unset($_SESSION['just_promoted']); ?>
    <?php endif; ?>
    
    // Add simple confetti function if not exists
    if(typeof confetti === 'undefined') {
        // Mock confetti
    }
});

function showLevelUp() {
    const modal = document.getElementById('level-up-modal');
    const card = document.getElementById('level-up-card');
    modal.classList.remove('hidden');
    // Animate in
    setTimeout(() => {
        card.classList.remove('scale-90', 'opacity-0');
        card.classList.add('scale-100', 'opacity-100');
        
        // Trigger generic confetti if available (e.g. from CDN)
        // For now, just a console log or simple CSS animation
    }, 50);
}

function closeLevelUp() {
    const modal = document.getElementById('level-up-modal');
    modal.classList.add('opacity-0'); // Fade out wrapper
    setTimeout(() => {
        modal.classList.add('hidden');
        modal.classList.remove('opacity-0');
    }, 300);
}
</script>
