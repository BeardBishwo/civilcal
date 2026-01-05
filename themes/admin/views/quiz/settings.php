<?php
// themes/admin/views/quiz/settings.php
$settings = $settings ?? [];
?>

<div class="admin-wrapper-container">
    <div class="admin-content-wrapper">

        <!-- Premium Gradient Header -->
        <div class="compact-header">
            <div class="header-left">
                <div class="header-title">
                    <i class="fas fa-cogs"></i>
                    <h1>Quiz Settings</h1>
                </div>
                <div class="header-subtitle">Global configuration for economy, rewards, and system modules.</div>
            </div>
            
            <!-- Quick Stats / Actions -->
            <div class="header-actions" style="display:flex; gap:15px; align-items:center;">
                 <div class="stat-pill">
                    <span class="label">XP RATE</span>
                    <span class="value">x<?php echo $settings['quiz_xp_multiplier'] ?? '1.0'; ?></span>
                </div>
                <button type="submit" form="quizSettingsForm" class="btn-create-premium">
                    <i class="fas fa-save"></i> SAVE CHANGES
                </button>
            </div>
        </div>

        <form id="quizSettingsForm" action="<?php echo app_base_url('admin/settings/save'); ?>" method="POST">
            <input type="hidden" name="group" value="quiz">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">

            <!-- SECTION 1: ECONOMY -->
            <div class="creation-toolbar">
                <h5 class="toolbar-title"><i class="fas fa-coins mr-2"></i> Economy Configuration</h5>
            </div>
            
            <div style="padding: 1.5rem 2rem;">
                <div style="display: flex; gap: 2rem; flex-wrap: wrap;">
                    <!-- XP Multiplier -->
                    <div style="flex: 1; min-width: 250px;">
                        <label class="setting-label">XP Multiplier</label>
                        <div class="input-group-premium">
                            <i class="fas fa-times icon"></i>
                            <input type="number" step="0.1" name="quiz_xp_multiplier" class="form-input-premium" value="<?php echo $settings['quiz_xp_multiplier'] ?? '1.0'; ?>">
                        </div>
                        <div class="setting-help">Global multiplier for all XP earned.</div>
                    </div>

                    <!-- Coins Per Answer -->
                    <div style="flex: 1; min-width: 250px;">
                        <label class="setting-label">Coins Per Answer</label>
                        <div class="input-group-premium">
                            <i class="fas fa-copyright icon text-amber-500"></i>
                            <input type="number" name="quiz_coins_per_answer" class="form-input-premium" value="<?php echo $settings['quiz_coins_per_answer'] ?? '5'; ?>">
                        </div>
                        <div class="setting-help">Base currency reward for correct answers.</div>
                    </div>
                </div>
            </div>

            <!-- SECTION 2: DAILY MISSIONS -->
             <div class="creation-toolbar" style="border-top: 1px solid var(--admin-gray-200);">
                <h5 class="toolbar-title"><i class="fas fa-bullseye mr-2"></i> Daily Missions</h5>
            </div>

            <div style="padding: 1.5rem 2rem;">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 2rem;">
                    <div>
                        <label class="setting-label">Missions Per Day</label>
                        <div class="input-group-premium">
                            <i class="fas fa-list-ol icon"></i>
                            <input type="number" name="quiz_daily_missions_count" class="form-input-premium" value="<?php echo $settings['quiz_daily_missions_count'] ?? '3'; ?>">
                        </div>
                    </div>
                    <div>
                        <label class="setting-label">Bonus Coin Goal</label>
                        <div class="input-group-premium">
                            <i class="fas fa-coins icon"></i>
                            <input type="number" name="quiz_daily_bonus_coins" class="form-input-premium" value="<?php echo $settings['quiz_daily_bonus_coins'] ?? '50'; ?>">
                        </div>
                    </div>
                    <div>
                        <label class="setting-label">Bonus XP Goal</label>
                        <div class="input-group-premium">
                            <i class="fas fa-star icon"></i>
                            <input type="number" name="quiz_daily_bonus_xp" class="form-input-premium" value="<?php echo $settings['quiz_daily_bonus_xp'] ?? '100'; ?>">
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION 3: FEATURES -->
            <div class="creation-toolbar" style="border-top: 1px solid var(--admin-gray-200);">
                <h5 class="toolbar-title"><i class="fas fa-toggle-on mr-2"></i> System Features</h5>
            </div>

            <div style="padding: 1.5rem 2rem;">
                 <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
                    
                    <!-- Leaderboard Toggle -->
                    <div class="feature-card">
                        <div class="feature-info">
                            <div class="feature-title">Global Leaderboard</div>
                            <div class="feature-desc">Enable public ranking and competitive standings.</div>
                        </div>
                        <div class="premium-toggle-group" style="border:none; height:auto; padding:0; background:transparent;">
                            <label class="switch">
                                <input type="checkbox" name="quiz_enable_leaderboard" <?php echo ($settings['quiz_enable_leaderboard'] ?? '1') == '1' ? 'checked' : ''; ?>>
                                <span class="slider round"></span>
                            </label>
                        </div>
                    </div>

                    <!-- Rewards Toggle -->
                    <div class="feature-card">
                        <div class="feature-info">
                            <div class="feature-title">Resource Rewards</div>
                            <div class="feature-desc">Grant XP and Coins for completed quizzes.</div>
                        </div>
                        <div class="premium-toggle-group" style="border:none; height:auto; padding:0; background:transparent;">
                            <label class="switch">
                                <input type="checkbox" name="quiz_enable_rewards" <?php echo ($settings['quiz_enable_rewards'] ?? '1') == '1' ? 'checked' : ''; ?>>
                                <span class="slider round"></span>
                            </label>
                        </div>
                    </div>

                 </div>
            </div>

        </form>
    </div>
</div>

<script>
document.getElementById('quizSettingsForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const btn = document.querySelector('button[type="submit"]');
    const originalHTML = btn.innerHTML;
    
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> SAVING...';
    btn.style.opacity = '0.8';
    btn.style.pointerEvents = 'none';

    try {
        const formData = new FormData(this);
        const response = await fetch(this.action, { method: 'POST', body: formData });
        const result = await response.json();
        
        if (result.success) {
            btn.innerHTML = '<i class="fas fa-check"></i> SAVED';
            btn.style.background = '#10b981'; // Success Green
             const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 1500, timerProgressBar: true });
            Toast.fire({ icon: 'success', title: 'Settings Saved' });

            setTimeout(() => {
                btn.innerHTML = originalHTML;
                btn.style.background = ''; // Revert gradient
                btn.style.opacity = '1';
                btn.style.pointerEvents = 'auto';
            }, 2000);
        } else {
            Swal.fire('Error', result.message, 'error');
            btn.innerHTML = originalHTML;
            btn.style.opacity = '1';
            btn.style.pointerEvents = 'auto';
        }
    } catch (error) {
        Swal.fire('Error', 'Server Connection Failed', 'error');
        btn.innerHTML = originalHTML;
        btn.style.opacity = '1';
        btn.style.pointerEvents = 'auto';
    }
});
</script>

<style>
/* ========================================
   PREMIUM CORE STYLES (MATCHING CATEGORY UI)
   ======================================== */
:root {
    --admin-primary: #667eea;
    --admin-secondary: #764ba2;
    --admin-gray-50: #f8f9fa;
    --admin-gray-200: #e5e7eb;
    --admin-gray-300: #d1d5db;
    --admin-gray-400: #9ca3af;
    --admin-gray-600: #4b5563;
    --admin-gray-800: #1f2937;
}

.admin-wrapper-container {
    padding: 1rem;
    background: var(--admin-gray-50);
    min-height: calc(100vh - 70px);
}

.admin-content-wrapper {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
    overflow: hidden;
    padding-bottom: 2rem;
    max-width: 1200px;
    margin: 0 auto;
}

/* Header */
.compact-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem 2rem;
    background: linear-gradient(135deg, var(--admin-primary) 0%, var(--admin-secondary) 100%);
    color: white;
}
.header-title { display: flex; align-items: center; gap: 0.75rem; }
.header-title h1 { margin: 0; font-size: 1.5rem; font-weight: 700; color: white; }
.header-title i { font-size: 1.25rem; opacity: 0.9; }
.header-subtitle { font-size: 0.85rem; opacity: 0.8; margin-top: 4px; font-weight: 500; }

.stat-pill {
    background: rgba(255,255,255,0.15);
    border: 1px solid rgba(255,255,255,0.2);
    border-radius: 8px;
    padding: 0.5rem 1rem;
    display: flex; flex-direction: column; align-items: center;
    min-width: 80px;
}
.stat-pill .label { font-size: 0.65rem; font-weight: 700; letter-spacing: 0.5px; opacity: 0.9; }
.stat-pill .value { font-size: 1.1rem; font-weight: 800; line-height: 1.1; }

/* Creation Toolbar (Section Headers) */
.creation-toolbar {
    padding: 1rem 2rem;
    background: #f8fafc;
    border-bottom: 1px solid var(--admin-gray-200);
}
.toolbar-title {
    font-size: 0.85rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin: 0;
}

/* Form Styles */
.setting-label {
    display: block; font-size: 0.85rem; font-weight: 700; color: #475569; margin-bottom: 0.5rem;
}
.setting-help {
    font-size: 0.75rem; color: #94a3b8; margin-top: 0.4rem;
}

.input-group-premium { position: relative; }
.input-group-premium .icon { position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 0.85rem; pointer-events: none; }
.form-input-premium {
    width: 100%; height: 42px; padding: 0 0.75rem 0 2.25rem; font-size: 0.9rem; font-weight: 600;
    border: 1px solid #cbd5e1; border-radius: 8px; outline: none; transition: all 0.2s;
    background: white; color: #334155;
}
.form-input-premium:focus { border-color: #667eea; box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1); }

/* Buttons */
.btn-create-premium {
    height: 40px; padding: 0 1.5rem; background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);
    color: white; font-weight: 700; font-size: 0.85rem; border: none; border-radius: 8px; cursor: pointer;
    display: inline-flex; align-items: center; gap: 0.5rem; transition: 0.2s;
    box-shadow: 0 2px 4px rgba(79, 70, 229, 0.2); white-space: nowrap; letter-spacing: 0.5px; text-transform: uppercase;
}
.btn-create-premium:hover { transform: translateY(-1px); box-shadow: 0 4px 6px rgba(79, 70, 229, 0.3); }

/* Switch Toggle & Feature Cards */
.feature-card {
    border: 1px solid #e2e8f0; border-radius: 10px; padding: 1rem 1.25rem;
    display: flex; justify-content: space-between; align-items: center;
    transition: 0.2s; background: #fff;
}
.feature-card:hover { border-color: #cbd5e1; background: #f8fafc; }

.feature-title { font-weight: 700; color: #334155; font-size: 0.9rem; }
.feature-desc { font-size: 0.75rem; color: #94a3b8; margin-top: 2px; }

.switch { position: relative; display: inline-block; width: 44px; height: 24px; margin: 0; }
.switch input { opacity: 0; width: 0; height: 0; }
.slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #cbd5e1; transition: .4s; }
.slider:before { position: absolute; content: ""; height: 18px; width: 18px; left: 3px; bottom: 3px; background-color: white; transition: .4s; }
input:checked + .slider { background-color: #4f46e5; }
input:checked + .slider:before { transform: translateX(20px); }
.slider.round { border-radius: 34px; }
.slider.round:before { border-radius: 50%; }

</style>
