<?php
// themes/admin/views/quiz/settings.php
?>
<div class="admin-wrapper-container">
    <div class="compact-card">
        <div class="compact-card-header">
            <div class="header-title">
                <i class="fas fa-cog text-primary"></i>
                Quiz Module Settings
            </div>
            <div class="header-actions">
                <button type="submit" form="quizSettingsForm" class="btn btn-primary btn-compact">
                    <i class="fas fa-save"></i>
                    <span>Save Changes</span>
                </button>
            </div>
        </div>
        <div class="compact-card-body">
            <form id="quizSettingsForm" action="<?php echo app_base_url('admin/settings/save'); ?>" method="POST">
                <input type="hidden" name="group" value="quiz">
                <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">

                <h6 class="text-uppercase text-muted font-weight-bold mb-3" style="font-size: 0.75rem; letter-spacing: 0.5px;">Economy Configuration</h6>
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <div class="form-group-compact">
                            <label class="form-label font-weight-bold">XP Multiplier</label>
                            <input type="number" step="0.1" name="quiz_xp_multiplier" class="form-control" value="<?php echo $settings['quiz_xp_multiplier'] ?? '1.0'; ?>">
                            <small class="text-muted">Global multiplier for XP earned from quizzes.</small>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="form-group-compact">
                            <label class="form-label font-weight-bold">Coins Per Correct Answer</label>
                            <input type="number" name="quiz_coins_per_answer" class="form-control" value="<?php echo $settings['quiz_coins_per_answer'] ?? '5'; ?>">
                            <small class="text-muted">Base currency amount rewarded for each correct answer.</small>
                        </div>
                    </div>
                </div>

                <h6 class="text-uppercase text-muted font-weight-bold mb-3 mt-2" style="font-size: 0.75rem; letter-spacing: 0.5px;">Features Toggle</h6>
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <div class="d-flex align-items-center justify-content-between p-3 border rounded bg-light-subtle">
                            <div>
                                <label class="form-label font-weight-bold mb-1" for="enableLeaderboard">Enable Global Leaderboard</label>
                                <small class="text-muted d-block">Allow users to see their rank and compete with others.</small>
                            </div>
                            <div class="form-check form-switch custom-switch">
                                <input class="form-check-input" type="checkbox" name="quiz_enable_leaderboard" id="enableLeaderboard" <?php echo ($settings['quiz_enable_leaderboard'] ?? '1') == '1' ? 'checked' : ''; ?>>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                       <div class="d-flex align-items-center justify-content-between p-3 border rounded bg-light-subtle">
                            <div>
                                <label class="form-label font-weight-bold mb-1" for="enableRewards">Enable Resource Rewards</label>
                                <small class="text-muted d-block">If disabled, users will only see their scores without earning materials.</small>
                            </div>
                            <div class="form-check form-switch custom-switch">
                                <input class="form-check-input" type="checkbox" name="quiz_enable_rewards" id="enableRewards" <?php echo ($settings['quiz_enable_rewards'] ?? '1') == '1' ? 'checked' : ''; ?>>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="my-4 border-light">

                <h6 class="text-uppercase text-muted font-weight-bold mb-3" style="font-size: 0.75rem; letter-spacing: 0.5px;">Daily Mission Settings</h6>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <div class="form-group-compact">
                            <label class="form-label">Missions Per Day</label>
                            <input type="number" name="quiz_daily_missions_count" class="form-control" value="<?php echo $settings['quiz_daily_missions_count'] ?? '3'; ?>">
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="form-group-compact">
                            <label class="form-label">Bonus Coin Goal</label>
                            <input type="number" name="quiz_daily_bonus_coins" class="form-control" value="<?php echo $settings['quiz_daily_bonus_coins'] ?? '50'; ?>">
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                         <div class="form-group-compact">
                            <label class="form-label">Bonus XP Goal</label>
                            <input type="number" name="quiz_daily_bonus_xp" class="form-control" value="<?php echo $settings['quiz_daily_bonus_xp'] ?? '100'; ?>">
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* Premium Layout Overrides */
.admin-wrapper-container {
    padding: 1.5rem;
    max-width: 1600px;
    margin: 0 auto;
}

.compact-card {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.03);
    border: 1px solid rgba(0,0,0,0.04);
    overflow: hidden;
}

.compact-card-header {
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid rgba(0,0,0,0.05);
    background: #fff;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.header-title {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--bs-gray-900, #212529);
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.compact-card-body {
    padding: 1.5rem;
}

.form-group-compact label {
    font-size: 0.85rem;
    font-weight: 600;
    color: var(--bs-gray-700, #495057);
    margin-bottom: 0.4rem;
    display: block;
}

.form-control {
    border-radius: 8px;
    padding: 0.6rem 1rem;
    border: 1px solid #dee2e6;
    font-size: 0.95rem;
}

.form-control:focus {
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
}

.bg-light-subtle {
    background-color: #f8f9fa !important;
    border: 1px solid #e9ecef !important;
    border-radius: 10px !important;
}

/* Custom Toggle Switch */
.custom-switch {
    display: flex;
    align-items: center;
}

.custom-switch .form-check-input {
    width: 3rem;
    height: 1.5rem;
    background-color: #e9ecef;
    border-color: #dee2e6;
    border-radius: 1.5rem;
    transition: background-position .15s ease-in-out;
    cursor: pointer;
    appearance: none;
    -webkit-appearance: none;
    position: relative;
    outline: 0;
}

.custom-switch .form-check-input:before {
    content: "";
    position: absolute;
    top: 0.15rem;
    left: 0.15rem;
    width: 1.2rem;
    height: 1.2rem;
    border-radius: 50%;
    background-color: #fff;
    box-shadow: 0 1px 2px rgba(0,0,0,0.2);
    transition: transform .15s ease-in-out;
}

.custom-switch .form-check-input:checked {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.custom-switch .form-check-input:checked:before {
    transform: translateX(1.5rem);
}

.btn-compact {
    padding: 0.5rem 1.25rem;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.9rem;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.2s;
}

.btn-compact:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
</style>

<script>
document.getElementById('quizSettingsForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const btn = this.querySelector('button[type="submit"]');
    const originalContent = btn.innerHTML;
    
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i><span>Saving...</span>';
    btn.disabled = true;
    
    try {
        const formData = new FormData(this);
        const response = await fetch(this.action, {
            method: 'POST',
            body: formData
        });
        const result = await response.json();
        
        if (result.success) {
            btn.innerHTML = '<i class="fas fa-check"></i><span>Saved!</span>';
            btn.classList.add('btn-success');
            setTimeout(() => {
                btn.innerHTML = originalContent;
                btn.classList.remove('btn-success');
                btn.disabled = false;
            }, 2000);
        } else {
            alert('Error: ' + result.message);
            btn.innerHTML = originalContent;
            btn.disabled = false;
        }
    } catch (error) {
        console.error('Save failed:', error);
        btn.innerHTML = originalContent;
        btn.disabled = false;
    }
});
</script>
