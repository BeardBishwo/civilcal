

<div class="bp-page">
    <div class="bp-hero-shell">
        <div class="bp-hero-glow"></div>
        <div class="bp-hero container">
            <div class="row align-items-center">
                <div class="col-lg-7">
                    <div class="d-flex align-items-center mb-3">
                        <span class="bp-chip">Season 1</span>
                        <?php if (!$progress['is_premium_unlocked']): ?>
                            <span class="bp-chip premium ml-2"><i class="fas fa-star mr-1"></i>Premium Locked</span>
                        <?php else: ?>
                            <span class="bp-chip success ml-2"><i class="fas fa-crown mr-1"></i>Premium Active</span>
                        <?php endif; ?>
                    </div>
                    <h1 class="bp-title">Civil Uprising</h1>
                    <p class="bp-subtitle">Climb ranks, finish missions, and unlock architecture-grade rewards for your city.</p>
                    <div class="bp-progress-card">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="bp-meta">Level <?php echo $progress['current_level']; ?></span>
                            <span class="bp-meta"><?php echo ($progress['current_xp'] % 1000); ?> / 1000 XP</span>
                        </div>
                        <div class="bp-progress-bar">
                            <div class="bp-progress-fill" style="width: <?php echo ($progress['current_xp'] % 1000) / 10; ?>%"></div>
                        </div>
                        <div class="d-flex align-items-center mt-3">
                            <div class="bp-stat">
                                <span class="bp-stat-label">Current XP</span>
                                <span class="bp-stat-value"><?php echo number_format($progress['current_xp']); ?></span>
                            </div>
                            <div class="bp-divider"></div>
                            <div class="bp-stat">
                                <span class="bp-stat-label">Unlocked</span>
                                <span class="bp-stat-value"><?php echo count($progress['claimed_rewards']); ?> / <?php echo count($rewards); ?></span>
                            </div>
                            <?php if (!$progress['is_premium_unlocked']): ?>
                                <button class="bp-cta ml-auto">Unlock Premium</button>
                            <?php else: ?>
                                <div class="ml-auto bp-active-pill"><i class="fas fa-crown mr-1"></i>Premium Active</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="bp-glass">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="bp-meta">Season Boosts</span>
                            <span class="bp-chip soft"><i class="fas fa-fire-alt mr-1"></i>Live</span>
                        </div>
                        <ul class="bp-list">
                            <li><i class="fas fa-bolt text-warning mr-2"></i>2x XP on Premium track</li>
                            <li><i class="fas fa-gem text-info mr-2"></i>Exclusive construction blueprints</li>
                            <li><i class="fas fa-shield-alt text-success mr-2"></i>Anti-replay secured claims</li>
                        </ul>
                        <div class="bp-mini-grid">
                            <div>
                                <span class="bp-stat-label">Daily streak</span>
                                <div class="bp-stat-value">+<?php echo min(7, $progress['current_level']); ?> days</div>
                            </div>
                            <div>
                                <span class="bp-stat-label">Missions</span>
                                <div class="bp-stat-value">3 / 5</div>
                            </div>
                            <div>
                                <span class="bp-stat-label">Coins</span>
                                <div class="bp-stat-value">+250</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container bp-body">
        <div class="row">
            <div class="col-lg-8">
                <div class="bp-section-header">
                    <div>
                        <p class="bp-kicker"><i class="fas fa-road mr-2"></i>Progress Track</p>
                        <h3 class="bp-heading">Claimable rewards</h3>
                    </div>
                    <div class="bp-legend">
                        <span class="bp-dot free"></span> Free
                        <span class="ml-3 bp-dot premium"></span> Premium
                    </div>
                </div>
                <div class="bp-track">
                    <?php foreach ($rewards as $reward): 
                        $isClaimed = in_array($reward['id'], $progress['claimed_rewards']);
                        $isUnlocked = $progress['current_level'] >= $reward['level'];
                        $canClaim = $isUnlocked && !$isClaimed && (!$reward['is_premium'] || $progress['is_premium_unlocked']);
                    ?>
                    <div class="bp-reward-card <?php echo $isClaimed ? 'claimed' : ''; ?> <?php echo $reward['is_premium'] ? 'premium' : 'free'; ?>">
                        <div class="d-flex align-items-center w-100">
                            <div class="bp-level-chip">Lv <?php echo $reward['level']; ?></div>
                            <div class="bp-icon">
                                <?php if ($reward['reward_type'] == 'bricks'): ?>
                                    <i class="fas fa-cubes"></i>
                                <?php elseif ($reward['reward_type'] == 'coins'): ?>
                                    <i class="fas fa-coins"></i>
                                <?php elseif ($reward['reward_type'] == 'lifeline'): ?>
                                    <i class="fas fa-bolt"></i>
                                <?php else: ?>
                                    <i class="fas fa-building"></i>
                                <?php endif; ?>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center">
                                    <span class="bp-reward-title text-uppercase"><?php echo strtoupper($reward['reward_type']); ?></span>
                                    <?php if ($reward['is_premium']): ?>
                                        <span class="bp-tag ml-2">Premium</span>
                                    <?php endif; ?>
                                </div>
                                <?php if ($isClaimed): ?>
                                    <div class="bp-reward-meta text-success"><i class="fas fa-check-circle mr-1"></i>Claimed</div>
                                <?php elseif (!$isUnlocked): ?>
                                    <div class="bp-reward-meta"><i class="fas fa-lock mr-1"></i>Reach level <?php echo $reward['level']; ?> to unlock</div>
                                <?php else: ?>
                                    <div class="bp-reward-meta text-info"><i class="fas fa-unlock mr-1"></i>Ready to claim</div>
                                <?php endif; ?>
                            </div>
                            <div>
                                <?php if ($canClaim): ?>
                                    <button class="bp-claim-btn btn-claim" data-id="<?php echo $reward['id']; ?>">Claim</button>
                                <?php elseif ($isClaimed): ?>
                                    <button class="bp-ghost-btn" disabled>Collected</button>
                                <?php else: ?>
                                    <button class="bp-ghost-btn" disabled>Locked</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="bp-card mb-4">
                    <div class="d-flex align-items-center mb-3">
                        <span class="bp-kicker mb-0"><i class="fas fa-tasks mr-2"></i>Daily Missions</span>
                        <span class="bp-chip soft ml-auto">3 / 5</span>
                    </div>
                    <div class="bp-mission">
                        <div class="bp-mission-title">Solve 5 Civil Questions</div>
                        <div class="bp-mission-progress">
                            <div style="width: 60%;"></div>
                        </div>
                        <div class="bp-mission-meta">3 / 5 · +100 XP | +50 Bricks</div>
                    </div>
                    <div class="bp-mission">
                        <div class="bp-mission-title">Win a Battle Royale</div>
                        <div class="bp-mission-progress completed">
                            <div style="width: 100%;"></div>
                        </div>
                        <div class="bp-mission-meta text-success"><i class="fas fa-check mr-1"></i>Completed · +250 XP | +20 Coins</div>
                    </div>
                    <div class="bp-tip"><i class="fas fa-lightbulb mr-2"></i>Premium Pass doubles XP and unlocks exclusive blueprints.</div>
                </div>

                <div class="bp-card">
                    <div class="d-flex align-items-center mb-2">
                        <span class="bp-kicker mb-0"><i class="fas fa-bolt mr-2"></i>Season Boosts</span>
                        <span class="bp-chip premium ml-auto">Premium</span>
                    </div>
                    <ul class="bp-list tight">
                        <li>+25% XP for quiz streaks</li>
                        <li>Bonus lifeline drops each 5 levels</li>
                        <li>Early access to new calculators</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@import url('https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&display=swap');

:root {
    --bp-bg: #0b0f1a;
    --bp-card: rgba(255,255,255,0.04);
    --bp-border: rgba(255,255,255,0.08);
    --bp-primary: #7c5dff;
    --bp-accent: #00d1ff;
    --bp-premium: #f7c948;
    --bp-success: #2ee6a8;
    --bp-text: #e8ecf2;
    --bp-muted: #9aa4b5;
    --bp-glow: 0 20px 80px rgba(124,93,255,0.25);
    --bp-radius: 16px;
}

.bp-page {
    background: radial-gradient(circle at 20% 20%, rgba(124,93,255,0.08), transparent 35%),
                radial-gradient(circle at 80% 10%, rgba(0,209,255,0.08), transparent 30%),
                linear-gradient(180deg, #0b0f1a 0%, #0a0c14 100%);
    color: var(--bp-text);
    font-family: 'Space Grotesk', 'Inter', system-ui, -apple-system, sans-serif;
    min-height: 100vh;
    padding-bottom: 64px;
}

.bp-hero-shell {
    position: relative;
    overflow: hidden;
    padding: 60px 0 30px;
}
.bp-hero-glow {
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(124,93,255,0.2), rgba(0,209,255,0.08));
    filter: blur(60px);
    opacity: 0.8;
}
.bp-hero {
    position: relative;
}
.bp-title {
    font-weight: 700;
    font-size: 44px;
    margin-bottom: 8px;
    letter-spacing: -0.02em;
}
.bp-subtitle {
    color: var(--bp-muted);
    font-size: 17px;
    margin-bottom: 20px;
}
.bp-chip {
    display: inline-flex;
    align-items: center;
    padding: 6px 12px;
    border-radius: 999px;
    background: rgba(255,255,255,0.08);
    color: var(--bp-text);
    font-weight: 600;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.04em;
}
.bp-chip.premium { background: rgba(247,201,72,0.15); color: #f7c948; }
.bp-chip.success { background: rgba(46,230,168,0.12); color: var(--bp-success); }
.bp-chip.soft { background: rgba(255,255,255,0.06); color: var(--bp-muted); }

.bp-cta {
    background: linear-gradient(90deg, var(--bp-primary), var(--bp-accent));
    border: none;
    color: #0b0f1a;
    padding: 10px 18px;
    border-radius: 12px;
    font-weight: 700;
    text-transform: uppercase;
    box-shadow: var(--bp-glow);
}
.bp-active-pill {
    padding: 8px 14px;
    border-radius: 999px;
    background: rgba(46,230,168,0.1);
    color: var(--bp-success);
    font-weight: 600;
}

.bp-progress-card {
    background: var(--bp-card);
    border: 1px solid var(--bp-border);
    border-radius: var(--bp-radius);
    padding: 18px;
    box-shadow: var(--bp-glow);
}
.bp-progress-bar {
    width: 100%;
    height: 10px;
    background: rgba(255,255,255,0.08);
    border-radius: 999px;
    overflow: hidden;
}
.bp-progress-fill {
    height: 100%;
    background: linear-gradient(90deg, var(--bp-accent), var(--bp-primary));
    border-radius: 999px;
}
.bp-stat { margin-right: 18px; }
.bp-stat-label { color: var(--bp-muted); font-size: 12px; text-transform: uppercase; letter-spacing: 0.05em; }
.bp-stat-value { color: var(--bp-text); font-weight: 700; font-size: 18px; }
.bp-divider { width: 1px; height: 32px; background: var(--bp-border); margin-right: 18px; }

.bp-glass {
    background: rgba(255,255,255,0.04);
    border: 1px solid var(--bp-border);
    border-radius: var(--bp-radius);
    padding: 18px;
    backdrop-filter: blur(8px);
    box-shadow: var(--bp-glow);
}
.bp-list { list-style: none; padding-left: 0; margin-bottom: 16px; }
.bp-list li { color: var(--bp-text); padding: 6px 0; font-weight: 500; }
.bp-list.tight li { padding: 4px 0; color: var(--bp-muted); }
.bp-mini-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin-top: 12px; }
.bp-mini-grid div { background: rgba(255,255,255,0.03); border: 1px solid var(--bp-border); border-radius: 12px; padding: 10px; }

.bp-body { margin-top: 10px; }
.bp-section-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px; }
.bp-kicker { color: var(--bp-muted); text-transform: uppercase; letter-spacing: 0.08em; font-weight: 700; font-size: 12px; margin-bottom: 4px; }
.bp-heading { margin: 0; font-weight: 700; }
.bp-legend { color: var(--bp-muted); font-size: 13px; }
.bp-dot { width: 10px; height: 10px; display: inline-block; border-radius: 50%; background: var(--bp-accent); vertical-align: middle; }
.bp-dot.premium { background: var(--bp-premium); }
.bp-dot.free { background: var(--bp-accent); }

.bp-track { display: flex; flex-direction: column; gap: 14px; }
.bp-reward-card {
    border: 1px solid var(--bp-border);
    background: rgba(255,255,255,0.02);
    border-radius: var(--bp-radius);
    padding: 14px;
    transition: all 0.2s ease;
}
.bp-reward-card:hover { transform: translateY(-2px); border-color: rgba(124,93,255,0.5); box-shadow: var(--bp-glow); }
.bp-reward-card.premium { background: linear-gradient(90deg, rgba(247,201,72,0.08), rgba(255,255,255,0.02)); }
.bp-reward-card.free { background: linear-gradient(90deg, rgba(0,209,255,0.08), rgba(255,255,255,0.02)); }
.bp-reward-card.claimed { opacity: 0.7; }

.bp-level-chip {
    background: rgba(255,255,255,0.06);
    border: 1px solid var(--bp-border);
    border-radius: 10px;
    padding: 6px 10px;
    font-weight: 700;
    margin-right: 12px;
}
.bp-icon {
    width: 44px; height: 44px;
    border-radius: 12px;
    display: grid; place-items: center;
    background: rgba(255,255,255,0.06);
    color: var(--bp-premium);
    margin-right: 12px;
    font-size: 18px;
}
.bp-reward-title { font-weight: 700; letter-spacing: 0.02em; }
.bp-tag {
    background: rgba(247,201,72,0.15);
    color: #f7c948;
    border-radius: 8px;
    padding: 4px 8px;
    font-weight: 700;
    font-size: 11px;
    text-transform: uppercase;
}
.bp-reward-meta { color: var(--bp-muted); font-size: 13px; }
.bp-claim-btn {
    background: linear-gradient(90deg, var(--bp-primary), var(--bp-accent));
    color: #0b0f1a;
    font-weight: 700;
    border: none;
    padding: 8px 14px;
    border-radius: 10px;
    box-shadow: var(--bp-glow);
    text-transform: uppercase;
    font-size: 12px;
}
.bp-ghost-btn {
    background: transparent;
    border: 1px dashed var(--bp-border);
    color: var(--bp-muted);
    padding: 8px 12px;
    border-radius: 10px;
    font-weight: 600;
}

.bp-card {
    background: rgba(255,255,255,0.03);
    border: 1px solid var(--bp-border);
    border-radius: var(--bp-radius);
    padding: 16px;
    color: var(--bp-text);
}
.bp-mission { margin-bottom: 14px; }
.bp-mission-title { font-weight: 700; }
.bp-mission-progress {
    background: rgba(255,255,255,0.06);
    height: 8px;
    border-radius: 999px;
    overflow: hidden;
    margin: 8px 0;
}
.bp-mission-progress div {
    height: 100%;
    background: linear-gradient(90deg, var(--bp-accent), var(--bp-primary));
}
.bp-mission-progress.completed div {
    background: linear-gradient(90deg, var(--bp-success), #32d583);
}
.bp-mission-meta { color: var(--bp-muted); font-size: 13px; }
.bp-tip {
    margin-top: 8px;
    background: rgba(255,255,255,0.04);
    border: 1px dashed var(--bp-border);
    border-radius: 12px;
    padding: 10px 12px;
    color: var(--bp-muted);
}

@media (max-width: 992px) {
    .bp-hero-shell { padding: 36px 0 20px; }
    .bp-title { font-size: 32px; }
    .bp-subtitle { font-size: 15px; }
    .bp-progress-card { margin-top: 12px; }
    .bp-mini-grid { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 768px) {
    .bp-page { padding-bottom: 32px; }
    .bp-section-header { flex-direction: column; align-items: flex-start; gap: 6px; }
    .bp-legend { margin-top: 0; }
    .bp-reward-card { padding: 12px; }
    .bp-icon { margin-right: 10px; }
    .bp-hero-glow { filter: blur(80px); }
}
</style>

<script>
let claimNonce = '<?php echo htmlspecialchars($claimNonce ?? '', ENT_QUOTES, 'UTF-8'); ?>';
const bpTrap = document.createElement('input');
bpTrap.type = 'text';
bpTrap.name = 'trap_answer';
bpTrap.id = 'bp_trap';
bpTrap.autocomplete = 'off';
bpTrap.style.display = 'none';
document.body.appendChild(bpTrap);

document.querySelectorAll('.btn-claim').forEach(btn => {
    btn.addEventListener('click', async function() {
        const rewardId = this.dataset.id;
        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        this.disabled = true;

        try {
            const formData = new FormData();
            formData.append('reward_id', rewardId);
            formData.append('nonce', claimNonce);
            formData.append('trap_answer', document.getElementById('bp_trap').value || '');
            
            const response = await fetch('/api/battle-pass/claim', {
                method: 'POST',
                body: formData
            });
            const data = await response.json();

            if (data.success) {
                if (data.nonce) claimNonce = data.nonce;
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
