<?php
// themes/admin/views/quiz/settings.php
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-cog text-primary me-2"></i> Quiz Module Settings</h5>
                        <button type="submit" form="quizSettingsForm" class="btn btn-primary px-4">
                            <i class="fas fa-save me-2"></i> Save Changes
                        </button>
                    </div>
                </div>
                <div class="card-body p-4">
                    <form id="quizSettingsForm" action="<?php echo app_base_url('admin/settings/save'); ?>" method="POST">
                        <input type="hidden" name="group" value="quiz">
                        <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label font-weight-bold">XP Multiplier</label>
                                <input type="number" step="0.1" name="quiz_xp_multiplier" class="form-control" value="<?php echo $settings['quiz_xp_multiplier'] ?? '1.0'; ?>">
                                <small class="text-muted">Global multiplier for XP earned from quizzes.</small>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label font-weight-bold">Coins Per Correct Answer</label>
                                <input type="number" name="quiz_coins_per_answer" class="form-control" value="<?php echo $settings['quiz_coins_per_answer'] ?? '5'; ?>">
                                <small class="text-muted">Base currency amount rewarded for each correct answer.</small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <div class="form-check form-switch custom-switch">
                                    <input class="form-check-input" type="checkbox" name="quiz_enable_leaderboard" id="enableLeaderboard" <?php echo ($settings['quiz_enable_leaderboard'] ?? '1') == '1' ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="enableLeaderboard">Enable Global Leaderboard</label>
                                </div>
                                <small class="text-muted d-block mt-1">Allow users to see their rank and compete with others.</small>
                            </div>
                            <div class="col-md-6 mb-4">
                                <div class="form-check form-switch custom-switch">
                                    <input class="form-check-input" type="checkbox" name="quiz_enable_rewards" id="enableRewards" <?php echo ($settings['quiz_enable_rewards'] ?? '1') == '1' ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="enableRewards">Enable Resource Rewards</label>
                                </div>
                                <small class="text-muted d-block mt-1">If disabled, users will only see their scores without earning materials.</small>
                            </div>
                        </div>

                        <hr class="my-4">

                        <h6 class="mb-3 text-uppercase text-muted small font-weight-bold">Daily Mission Settings</h6>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Missions Per Day</label>
                                <input type="number" name="quiz_daily_missions_count" class="form-control" value="<?php echo $settings['quiz_daily_missions_count'] ?? '3'; ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Bonus Coin Goal</label>
                                <input type="number" name="quiz_daily_bonus_coins" class="form-control" value="<?php echo $settings['quiz_daily_bonus_coins'] ?? '50'; ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Bonus XP Goal</label>
                                <input type="number" name="quiz_daily_bonus_xp" class="form-control" value="<?php echo $settings['quiz_daily_bonus_xp'] ?? '100'; ?>">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.custom-switch .form-check-input {
    width: 3rem;
    height: 1.5rem;
    cursor: pointer;
}
.custom-switch .form-check-label {
    padding-left: 0.5rem;
    padding-top: 0.2rem;
    font-weight: 600;
}
</style>

<script>
document.getElementById('quizSettingsForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const btn = this.querySelector('button[type="submit"]');
    const originalContent = btn.innerHTML;
    
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Saving...';
    btn.disabled = true;
    
    try {
        const formData = new FormData(this);
        const response = await fetch(this.action, {
            method: 'POST',
            body: formData
        });
        const result = await response.json();
        
        if (result.success) {
            btn.innerHTML = '<i class="fas fa-check me-2"></i> Saved!';
            btn.classList.replace('btn-primary', 'btn-success');
            setTimeout(() => {
                btn.innerHTML = originalContent;
                btn.classList.replace('btn-success', 'btn-primary');
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
