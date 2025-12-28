

<div class="container py-5">
    <!-- Hero Banner -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="card shadow-lg border-0 bg-dark text-white p-5 bp-banner overflow-hidden">
                <div class="row align-items-center position-relative z-index-1">
                    <div class="col-md-7">
                        <span class="badge badge-warning mb-3 px-3 py-2">SEASON 1</span>
                        <h1 class="display-3 font-weight-bold mb-3">CIVIL UPRISING</h1>
                        <p class="lead opacity-75 mb-4">Complete daily missions and climb the ranks to unlock exclusive rewards for your city.</p>
                        
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1 mr-4">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="small font-weight-bold">LEVEL <?php echo $progress['current_level']; ?></span>
                                    <span class="small opacity-75"><?php echo ($progress['current_xp'] % 1000); ?> / 1000 XP</span>
                                </div>
                                <div class="progress bg-secondary" style="height: 12px; border-radius: 6px;">
                                    <div class="progress-bar bg-warning progress-bar-striped progress-bar-animated" 
                                         style="width: <?php echo ($progress['current_xp'] % 1000) / 10; ?>%"></div>
                                </div>
                            </div>
                            <?php if (!$progress['is_premium_unlocked']): ?>
                                <button class="btn btn-warning btn-lg font-weight-bold px-4">UNLOCK PREMIUM</button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <i class="fas fa-hammer bp-banner-icon opacity-10"></i>
            </div>
        </div>
    </div>

    <!-- Rewards Track -->
    <div class="row">
        <div class="col-lg-8">
            <h3 class="mb-4 font-weight-bold"><i class="fas fa-trophy text-warning mr-2"></i>Progress Rewards</h3>
            <div class="bp-track p-4 bg-light rounded-lg shadow-inner">
                <?php foreach ($rewards as $reward): 
                    $isClaimed = in_array($reward['id'], $progress['claimed_rewards']);
                    $isUnlocked = $progress['current_level'] >= $reward['level'];
                    $canClaim = $isUnlocked && !$isClaimed && (!$reward['is_premium'] || $progress['is_premium_unlocked']);
                ?>
                    <div class="bp-reward-item d-flex align-items-center p-3 mb-3 bg-white rounded-lg shadow-sm <?php echo $isClaimed ? 'claimed' : ''; ?>">
                        <div class="bp-level-badge bg-dark text-white rounded-circle mr-4 d-flex align-items-center justify-content-center">
                            <span>Lvl <?php echo $reward['level']; ?></span>
                        </div>
                        
                        <div class="mr-3">
                            <div class="reward-icon-box <?php echo $reward['is_premium'] ? 'premium' : 'free'; ?>">
                                <?php if ($reward['reward_type'] == 'bricks'): ?>
                                    <i class="fas fa-cubes text-brown"></i>
                                <?php elseif ($reward['reward_type'] == 'coins'): ?>
                                    <i class="fas fa-coins text-warning"></i>
                                <?php elseif ($reward['reward_type'] == 'lifeline'): ?>
                                    <i class="fas fa-bolt text-info"></i>
                                <?php else: ?>
                                    <i class="fas fa-building text-primary"></i>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="flex-grow-1">
                            <h5 class="mb-0 font-weight-bold">

                                <?php if ($reward['is_premium']): ?>
                                    <span class="badge badge-warning ml-2 small-text">PREMIUM</span>
                                <?php endif; ?>
                            </h5>
                            <?php if ($isClaimed): ?>
                                <span class="text-success small"><i class="fas fa-check-circle"></i> Claimed</span>
                            <?php elseif (!$isUnlocked): ?>
                                <span class="text-muted small"><i class="fas fa-lock"></i> Reach level <?php echo $reward['level']; ?> to unlock</span>
                            <?php endif; ?>
                        </div>

                        <div>
                            <?php if ($canClaim): ?>
                                <button class="btn btn-primary btn-sm btn-claim" data-id="<?php echo $reward['id']; ?>">CLAIM</button>
                            <?php elseif ($isClaimed): ?>
                                <button class="btn btn-light btn-sm" disabled>COLLECTED</button>
                            <?php else: ?>
                                <button class="btn btn-secondary btn-sm" disabled>LOCKED</button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Sidebar Missions -->
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-dark text-white font-weight-bold">
                    <i class="fas fa-tasks mr-2"></i> Daily Missions
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="font-weight-bold small">Solve 5 Civil Questions</span>
                                <span class="badge badge-light">3/5</span>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-info" style="width: 60%"></div>
                            </div>
                            <div class="mt-2 small text-muted">+100 XP | +50 Bricks</div>
                        </li>
                        <li class="list-group-item">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="font-weight-bold small">Win a Battle Royale</span>
                                <span class="badge badge-success">COMPLETED</span>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-success" style="width: 100%"></div>
                            </div>
                            <div class="mt-2 small text-muted">+250 XP | +20 Coins</div>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="alert alert-info shadow-sm border-0">
                <h5 class="font-weight-bold"><i class="fas fa-info-circle mr-2"></i>Pro Tip</h5>
                <p class="small mb-0">Premium Pass holders earn 2x XP and get exclusive construction blueprints for their city!</p>
            </div>
        </div>
    </div>
</div>

<style>
.bp-banner { background: linear-gradient(135deg, #1a1a1a 0%, #3d3d3d 100%); min-height: 250px; position: relative; }
.bp-banner-icon { position: absolute; right: -50px; bottom: -50px; font-size: 15rem; transform: rotate(-15deg); color: #fff; }
.bp-level-badge { width: 50px; height: 50px; font-weight: bold; font-size: 0.8rem; }
.reward-icon-box { width: 45px; height: 45px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }
.reward-icon-box.free { background: rgba(106, 17, 203, 0.1); }
.reward-icon-box.premium { background: rgba(255, 193, 7, 0.1); }
.bp-reward-item.claimed { opacity: 0.7; }
.bp-reward-item.claimed .bp-level-badge { background-color: #28a745 !important; }
.bp-track { max-height: 600px; overflow-y: auto; }
.small-text { font-size: 0.65rem; padding: 2px 5px; vertical-align: middle; }
.text-brown { color: #8B4513; }
</style>

<script>
document.querySelectorAll('.btn-claim').forEach(btn => {
    btn.addEventListener('click', async function() {
        const rewardId = this.dataset.id;
        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        this.disabled = true;

        try {
            const formData = new FormData();
            formData.append('reward_id', rewardId);
            
            const response = await fetch('/api/battle-pass/claim', {
                method: 'POST',
                body: formData
            });
            const data = await response.json();

            if (data.success) {
                this.innerHTML = '<i class="fas fa-check"></i>';
                this.className = 'btn btn-light btn-sm';
                if (window.playSfx) playSfx('win');
                setTimeout(() => location.reload(), 1500);
            } else {
                alert(data.message);
                this.innerHTML = 'CLAIM';
                this.disabled = false;
            }
        } catch (e) {
            alert('Error claiming reward.');
            this.innerHTML = 'CLAIM';
            this.disabled = false;
        }
    });
});
</script>
