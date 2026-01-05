<div class="bp-page">
    <!-- Header -->
    <header class="gamification-header" style="padding: 20px 0; border-bottom: 1px solid rgba(255,255,255,0.08); background: rgba(10, 14, 26, 0.8); backdrop-filter: blur(10px); position: sticky; top: 0; z-index: 100;">
        <div class="container d-flex align-items-center justify-content-between">
            <a href="<?php echo app_base_url('quiz'); ?>" style="color: #94a3b8; text-decoration: none; font-weight: 600; font-size: 0.9rem; display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-arrow-left"></i> <span>Back to Portal</span>
            </a>
            <div style="text-align: right;">
                <h2 style="font-size: 1.25rem; font-weight: 800; margin: 0; background: linear-gradient(135deg, #ffffff 0%, #a8b3cf 100%); -webkit-background-clip: text; background-clip: text; -webkit-text-fill-color: transparent;">Season 1</h2>
            </div>
        </div>
    </header>

    <!-- Compact Hero Section -->
    <div class="bp-hero-shell">
        <div class="bp-hero-glow"></div>
        <div class="bp-animated-bg">
            <div class="bp-orb bp-orb-1"></div>
            <div class="bp-orb bp-orb-2"></div>
        </div>
        <div class="bp-hero container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <div class="d-flex align-items-center mb-2">
                        <span class="bp-chip bp-chip-sm">
                            <i class="fas fa-calendar-alt mr-1"></i>Season 1
                        </span>
                        <?php if (!$progress['is_premium_unlocked']): ?>
                            <span class="bp-chip bp-chip-sm premium ml-2">
                                <i class="fas fa-star mr-1"></i>Premium Locked
                            </span>
                        <?php else: ?>
                            <span class="bp-chip bp-chip-sm success ml-2">
                                <i class="fas fa-crown mr-1"></i>Premium
                            </span>
                        <?php endif; ?>
                    </div>
                    <h1 class="bp-title">Civil Uprising</h1>
                    <p class="bp-subtitle">Climb ranks, finish missions, unlock rewards</p>
                    
                    <!-- Compact Progress Card -->
                    <div class="bp-progress-card">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bp-stat-inline">
                                    <span class="bp-stat-label">Level</span>
                                    <span class="bp-stat-value"><?php echo $progress['current_level']; ?></span>
                                </div>
                                <div class="bp-divider-sm"></div>
                                <div class="bp-stat-inline">
                                    <span class="bp-stat-label">Progress</span>
                                    <span class="bp-stat-value"><?php echo ($progress['current_xp'] % 1000); ?>/1000</span>
                                </div>
                                <div class="bp-divider-sm"></div>
                                <div class="bp-stat-inline">
                                    <span class="bp-stat-label">Unlocked</span>
                                    <span class="bp-stat-value"><?php echo count($progress['claimed_rewards']); ?>/<?php echo count($rewards); ?></span>
                                </div>
                            </div>
                            <?php if (!$progress['is_premium_unlocked']): ?>
                                <button class="bp-cta-sm">
                                    <i class="fas fa-crown mr-1"></i>Unlock
                                </button>
                            <?php endif; ?>
                        </div>
                        <div class="bp-progress-bar-sm">
                            <div class="bp-progress-fill" style="width: <?php echo ($progress['current_xp'] % 1000) / 10; ?>%">
                                <div class="bp-progress-shine"></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Compact Stats -->
                <div class="col-lg-4">
                    <div class="bp-stats-grid">
                        <div class="bp-stat-card">
                            <i class="fas fa-fire text-warning"></i>
                            <div>
                                <div class="bp-stat-num">+<?php echo min(7, $progress['current_level']); ?></div>
                                <div class="bp-stat-txt">Day Streak</div>
                            </div>
                        </div>
                        <div class="bp-stat-card">
                            <i class="fas fa-tasks text-info"></i>
                            <div>
                                <div class="bp-stat-num">3/5</div>
                                <div class="bp-stat-txt">Missions</div>
                            </div>
                        </div>
                        <div class="bp-stat-card">
                            <img src="<?php echo app_base_url('themes/default/assets/resources/currency/coin.webp'); ?>" style="width: 24px; height: 24px; object-fit: contain;">
                            <div>
                                <div class="bp-stat-num">+250</div>
                                <div class="bp-stat-txt">Coins</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Area - Compact -->
    <div class="container bp-body">
        <div class="row">
            <div class="col-lg-9">
                <!-- Compact Section Header -->
                <div class="bp-section-header">
                    <div>
                        <h3 class="bp-heading"><i class="fas fa-trophy mr-2"></i>Claimable Rewards</h3>
                    </div>
                    <div class="bp-legend">
                        <span class="bp-dot free"></span> Free
                        <span class="ml-2 bp-dot premium"></span> Premium
                    </div>
                </div>
                
                <!-- Compact Rewards Track -->
                <div class="bp-track">
                    <?php foreach ($rewards as $index => $reward): 
                        $isClaimed = in_array($reward['id'], $progress['claimed_rewards']);
                        $isUnlocked = $progress['current_level'] >= $reward['level'];
                        $canClaim = $isUnlocked && !$isClaimed && (!$reward['is_premium'] || $progress['is_premium_unlocked']);
                    ?>
                    <div class="bp-reward-card-compact <?php echo $isClaimed ? 'claimed' : ''; ?> <?php echo $reward['is_premium'] ? 'premium' : 'free'; ?> <?php echo $canClaim ? 'claimable' : ''; ?>">
                        <div class="bp-level-badge"><?php echo $reward['level']; ?></div>
                        <div class="bp-icon-sm">
                            <?php if ($reward['reward_type'] == 'bricks'): ?>
                                <img src="<?php echo app_base_url('themes/default/assets/resources/materials/brick_single.webp'); ?>" style="width: 20px; height: 20px; object-fit: contain;">
                            <?php elseif ($reward['reward_type'] == 'coins'): ?>
                                <img src="<?php echo app_base_url('themes/default/assets/resources/currency/coin.webp'); ?>" style="width: 20px; height: 20px; object-fit: contain;">
                            <?php elseif ($reward['reward_type'] == 'lifeline'): ?>
                                <i class="fas fa-bolt"></i>
                            <?php else: ?>
                                <img src="<?php echo app_base_url('themes/default/assets/resources/materials/plank.webp'); ?>" style="width: 20px; height: 20px; object-fit: contain;">
                            <?php endif; ?>
                        </div>
                        <div class="bp-reward-info">
                            <div class="bp-reward-name">
                                <?php echo strtoupper($reward['reward_type']); ?>
                                <?php if ($reward['is_premium']): ?>
                                    <span class="bp-tag-sm"><i class="fas fa-crown"></i></span>
                                <?php endif; ?>
                            </div>
                            <div class="bp-reward-status">
                                <?php if ($isClaimed): ?>
                                    <i class="fas fa-check-circle text-success"></i> Claimed
                                <?php elseif (!$isUnlocked): ?>
                                    <i class="fas fa-lock"></i> Level <?php echo $reward['level']; ?>
                                <?php else: ?>
                                    <i class="fas fa-unlock text-info"></i> Ready
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="bp-reward-action">
                            <?php if ($canClaim): ?>
                                <button class="bp-btn-claim btn-claim" data-id="<?php echo $reward['id']; ?>">
                                    <i class="fas fa-gift"></i> Claim
                                </button>
                            <?php elseif ($isClaimed): ?>
                                <button class="bp-btn-disabled" disabled>
                                    <i class="fas fa-check"></i>
                                </button>
                            <?php else: ?>
                                <button class="bp-btn-disabled" disabled>
                                    <i class="fas fa-lock"></i>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Compact Sidebar -->
            <div class="col-lg-3">
                <!-- Missions Card -->
                <div class="bp-sidebar-card">
                    <div class="bp-sidebar-header">
                        <i class="fas fa-tasks"></i>
                        <span>Missions</span>
                        <span class="bp-badge">3/5</span>
                    </div>
                    <div class="bp-mission-compact">
                        <div class="bp-mission-info">
                            <div class="bp-mission-name">Solve 5 Civil Questions</div>
                            <div class="bp-mission-reward">+100 XP · +50 Bricks</div>
                        </div>
                        <div class="bp-mission-bar">
                            <div class="bp-mission-fill" style="width: 60%;"></div>
                        </div>
                    </div>
                    <div class="bp-mission-compact completed">
                        <div class="bp-mission-info">
                            <div class="bp-mission-name">Win a Battle Royale</div>
                            <div class="bp-mission-reward text-success"><i class="fas fa-check"></i> +250 XP</div>
                        </div>
                        <div class="bp-mission-bar">
                            <div class="bp-mission-fill" style="width: 100%;"></div>
                        </div>
                    </div>
                </div>

                <!-- Boosts Card -->
                <div class="bp-sidebar-card">
                    <div class="bp-sidebar-header">
                        <i class="fas fa-bolt"></i>
                        <span>Boosts</span>
                        <span class="bp-badge premium">Premium</span>
                    </div>
                    <ul class="bp-boost-list">
                        <li><i class="fas fa-check"></i> +25% XP for streaks</li>
                        <li><i class="fas fa-check"></i> Bonus lifeline drops</li>
                        <li><i class="fas fa-check"></i> Early calculator access</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

:root {
    --bp-bg: #0a0e1a;
    --bp-card: rgba(255,255,255,0.03);
    --bp-border: rgba(255,255,255,0.08);
    --bp-primary: #7c5dff;
    --bp-accent: #00d1ff;
    --bp-premium: #f7c948;
    --bp-success: #2ee6a8;
    --bp-text: #e8ecf2;
    --bp-muted: #9aa4b5;
    --bp-glow: 0 8px 24px rgba(124,93,255,0.2);
    --bp-radius: 12px;
}

/* Compact Page Base */
.bp-page {
    background: linear-gradient(180deg, #0a0e1a 0%, #060810 100%);
    color: var(--bp-text);
    font-family: 'Inter', system-ui, -apple-system, sans-serif;
    min-height: 100vh;
    padding-bottom: 32px;
    position: relative;
}

/* Compact Animated Background */
.bp-animated-bg {
    position: absolute;
    inset: 0;
    overflow: hidden;
    pointer-events: none;
    z-index: 0;
}

.bp-orb {
    position: absolute;
    border-radius: 50%;
    filter: blur(60px);
    opacity: 0.3;
    animation: float-orb 20s ease-in-out infinite;
}

.bp-orb-1 {
    width: 300px;
    height: 300px;
    background: radial-gradient(circle, rgba(124,93,255,0.3), transparent);
    top: -80px;
    left: -80px;
}

.bp-orb-2 {
    width: 250px;
    height: 250px;
    background: radial-gradient(circle, rgba(0,209,255,0.25), transparent);
    top: 40%;
    right: -60px;
    animation-delay: 7s;
}

@keyframes float-orb {
    0%, 100% { transform: translate(0, 0); }
    50% { transform: translate(30px, -20px); }
}

/* Compact Hero Section */
.bp-hero-shell {
    position: relative;
    overflow: hidden;
    padding: 40px 0 24px;
    z-index: 1;
}

.bp-hero-glow {
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(124,93,255,0.12), rgba(0,209,255,0.06));
    filter: blur(60px);
    opacity: 0.5;
}

.bp-hero {
    position: relative;
    z-index: 2;
}

.bp-title {
    font-weight: 700;
    font-size: 32px;
    margin-bottom: 6px;
    letter-spacing: -0.02em;
    background: linear-gradient(135deg, #ffffff 0%, #a8b3cf 100%);
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
}

.bp-subtitle {
    color: var(--bp-muted);
    font-size: 14px;
    margin-bottom: 16px;
    line-height: 1.5;
}

/* Compact Chips */
.bp-chip {
    display: inline-flex;
    align-items: center;
    padding: 5px 10px;
    border-radius: 999px;
    background: rgba(255,255,255,0.06);
    backdrop-filter: blur(10px);
    color: var(--bp-text);
    font-weight: 600;
    font-size: 10px;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    border: 1px solid rgba(255,255,255,0.08);
}

.bp-chip-sm {
    padding: 4px 8px;
    font-size: 9px;
}

.bp-chip.premium { 
    background: linear-gradient(135deg, rgba(247,201,72,0.15), rgba(247,201,72,0.08)); 
    color: #f7c948; 
    border-color: rgba(247,201,72,0.2);
}

.bp-chip.success { 
    background: linear-gradient(135deg, rgba(46,230,168,0.12), rgba(46,230,168,0.06)); 
    color: var(--bp-success); 
    border-color: rgba(46,230,168,0.15);
}

/* Compact Progress Card */
.bp-progress-card {
    background: rgba(255,255,255,0.04);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: var(--bp-radius);
    padding: 14px;
    backdrop-filter: blur(10px);
}

.bp-progress-bar-sm {
    width: 100%;
    height: 6px;
    background: rgba(255,255,255,0.06);
    border-radius: 999px;
    overflow: hidden;
    margin-top: 8px;
}

.bp-progress-fill {
    height: 100%;
    background: linear-gradient(90deg, var(--bp-accent), var(--bp-primary));
    border-radius: 999px;
    position: relative;
    transition: width 0.6s ease;
}

.bp-progress-shine {
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    animation: shine 2s ease-in-out infinite;
}

@keyframes shine {
    0% { left: -100%; }
    100% { left: 200%; }
}

/* Compact Stats */
.bp-stat-inline {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.bp-stat-label { 
    color: var(--bp-muted); 
    font-size: 9px; 
    text-transform: uppercase; 
    letter-spacing: 0.05em; 
    font-weight: 600;
}

.bp-stat-value { 
    color: var(--bp-text); 
    font-weight: 700; 
    font-size: 14px; 
}

.bp-divider-sm { 
    width: 1px; 
    height: 24px; 
    background: rgba(255,255,255,0.1); 
    margin: 0 12px; 
}

/* Compact CTA Button */
.bp-cta-sm {
    background: linear-gradient(135deg, var(--bp-primary), var(--bp-accent));
    border: none;
    color: #0a0e1a;
    padding: 8px 16px;
    border-radius: 8px;
    font-weight: 700;
    font-size: 11px;
    letter-spacing: 0.03em;
    box-shadow: var(--bp-glow);
    transition: all 0.3s ease;
    cursor: pointer;
}

.bp-cta-sm:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 32px rgba(124,93,255,0.3);
}

/* Compact Stats Grid */
.bp-stats-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 8px;
}

.bp-stat-card {
    background: rgba(255,255,255,0.04);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 10px;
    padding: 10px;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
}

.bp-stat-card:hover {
    background: rgba(255,255,255,0.06);
    transform: translateY(-2px);
}

.bp-stat-card i {
    font-size: 18px;
}

.bp-stat-num {
    font-weight: 700;
    font-size: 16px;
    color: var(--bp-text);
}

.bp-stat-txt {
    font-size: 10px;
    color: var(--bp-muted);
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

/* Main Content */
.bp-body { 
    margin-top: 24px; 
}

.bp-section-header { 
    display: flex; 
    align-items: center; 
    justify-content: space-between; 
    margin-bottom: 16px; 
}

.bp-heading { 
    margin: 0; 
    font-weight: 700; 
    font-size: 18px;
    color: var(--bp-text);
}

.bp-legend { 
    color: var(--bp-muted); 
    font-size: 11px; 
    display: flex;
    align-items: center;
}

.bp-dot { 
    width: 8px; 
    height: 8px; 
    display: inline-block; 
    border-radius: 50%; 
    background: var(--bp-accent); 
    vertical-align: middle; 
    margin-right: 4px;
}

.bp-dot.premium { background: var(--bp-premium); }
.bp-dot.free { background: var(--bp-accent); }

/* Compact Reward Cards */
.bp-track { 
    display: flex; 
    flex-direction: column; 
    gap: 8px; 
}

.bp-reward-card-compact {
    border: 1px solid var(--bp-border);
    background: rgba(255,255,255,0.02);
    border-radius: 10px;
    padding: 10px 12px;
    display: flex;
    align-items: center;
    gap: 10px;
    transition: all 0.3s ease;
}

.bp-reward-card-compact:hover {
    transform: translateX(4px);
    border-color: rgba(124,93,255,0.3);
    background: rgba(255,255,255,0.04);
}

.bp-reward-card-compact.premium {
    border-left: 2px solid rgba(247,201,72,0.4);
}

.bp-reward-card-compact.free {
    border-left: 2px solid rgba(0,209,255,0.4);
}

.bp-reward-card-compact.claimed {
    opacity: 0.5;
}

.bp-reward-card-compact.claimable {
    animation: pulse-border 2s ease-in-out infinite;
}

@keyframes pulse-border {
    0%, 100% { border-color: rgba(124,93,255,0.2); }
    50% { border-color: rgba(0,209,255,0.5); }
}

.bp-level-badge {
    background: rgba(255,255,255,0.06);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 6px;
    padding: 4px 8px;
    font-weight: 700;
    font-size: 11px;
    min-width: 32px;
    text-align: center;
}

.bp-icon-sm {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: grid;
    place-items: center;
    background: rgba(255,255,255,0.06);
    color: var(--bp-premium);
    font-size: 14px;
}

.bp-reward-info {
    flex: 1;
}

.bp-reward-name {
    font-weight: 600;
    font-size: 12px;
    color: var(--bp-text);
    display: flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 2px;
}

.bp-tag-sm {
    background: rgba(247,201,72,0.15);
    color: #f7c948;
    border-radius: 4px;
    padding: 2px 4px;
    font-size: 9px;
}

.bp-reward-status {
    color: var(--bp-muted);
    font-size: 10px;
}

.bp-reward-action {
    margin-left: auto;
}

.bp-btn-claim {
    background: linear-gradient(135deg, var(--bp-primary), var(--bp-accent));
    color: #0a0e1a;
    font-weight: 700;
    border: none;
    padding: 6px 12px;
    border-radius: 8px;
    font-size: 11px;
    cursor: pointer;
    transition: all 0.3s ease;
    white-space: nowrap;
}

.bp-btn-claim:hover {
    transform: translateY(-2px);
    box-shadow: var(--bp-glow);
}

.bp-btn-disabled {
    background: rgba(255,255,255,0.04);
    border: 1px dashed rgba(255,255,255,0.1);
    color: var(--bp-muted);
    padding: 6px 10px;
    border-radius: 8px;
    font-size: 12px;
    cursor: not-allowed;
}

/* Compact Sidebar */
.bp-sidebar-card {
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: var(--bp-radius);
    padding: 12px;
    margin-bottom: 12px;
}

.bp-sidebar-header {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 12px;
    font-weight: 600;
    font-size: 12px;
    color: var(--bp-text);
}

.bp-sidebar-header i {
    font-size: 14px;
}

.bp-badge {
    background: rgba(255,255,255,0.06);
    padding: 3px 8px;
    border-radius: 999px;
    font-size: 9px;
    font-weight: 600;
    margin-left: auto;
}

.bp-badge.premium {
    background: rgba(247,201,72,0.15);
    color: #f7c948;
}

/* Compact Missions */
.bp-mission-compact {
    margin-bottom: 10px;
    padding-bottom: 10px;
    border-bottom: 1px solid rgba(255,255,255,0.05);
}

.bp-mission-compact:last-child {
    margin-bottom: 0;
    padding-bottom: 0;
    border-bottom: none;
}

.bp-mission-info {
    margin-bottom: 6px;
}

.bp-mission-name {
    font-weight: 600;
    font-size: 11px;
    color: var(--bp-text);
    margin-bottom: 2px;
}

.bp-mission-reward {
    color: var(--bp-muted);
    font-size: 9px;
}

.bp-mission-bar {
    background: rgba(255,255,255,0.06);
    height: 4px;
    border-radius: 999px;
    overflow: hidden;
}

.bp-mission-fill {
    height: 100%;
    background: linear-gradient(90deg, var(--bp-accent), var(--bp-primary));
    border-radius: 999px;
    transition: width 0.6s ease;
}

.bp-mission-compact.completed .bp-mission-fill {
    background: linear-gradient(90deg, var(--bp-success), #32d583);
}

/* Compact Boost List */
.bp-boost-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.bp-boost-list li {
    color: var(--bp-muted);
    font-size: 11px;
    padding: 6px 0;
    display: flex;
    align-items: center;
    gap: 6px;
}

.bp-boost-list li i {
    color: var(--bp-success);
    font-size: 10px;
}

/* Responsive */
@media (max-width: 992px) {
    .bp-hero-shell { padding: 32px 0 20px; }
    .bp-title { font-size: 28px; }
    .bp-stats-grid { grid-template-columns: repeat(3, 1fr); }
}

@media (max-width: 768px) {
    .bp-page { padding-bottom: 24px; }
    .bp-title { font-size: 24px; }
    .bp-section-header { flex-direction: column; align-items: flex-start; gap: 8px; }
    .bp-reward-card-compact { padding: 8px 10px; }
    .bp-stats-grid { grid-template-columns: 1fr; }
}

.bp-reward-card {
    border: 1px solid var(--bp-border);
    background: rgba(255,255,255,0.02);
    border-radius: var(--bp-radius);
    padding: 18px;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.bp-reward-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.05), transparent);
    transition: left 0.6s;
}

.bp-reward-card:hover::before {
    left: 100%;
}

.bp-reward-card:hover { 
    transform: translateX(8px); 
    border-color: rgba(124,93,255,0.4); 
    box-shadow: -4px 0 20px rgba(124,93,255,0.2);
}

.bp-reward-card.premium { 
    background: linear-gradient(90deg, rgba(247,201,72,0.08), rgba(255,255,255,0.02)); 
    border-left: 3px solid rgba(247,201,72,0.5);
}

.bp-reward-card.free { 
    background: linear-gradient(90deg, rgba(0,209,255,0.08), rgba(255,255,255,0.02)); 
    border-left: 3px solid rgba(0,209,255,0.5);
}

.bp-reward-card.claimed { 
    opacity: 0.5; 
    filter: grayscale(0.5);
}

.bp-reward-card.claimable {
    animation: pulse-border 2s ease-in-out infinite;
}

@keyframes pulse-border {
    0%, 100% { border-color: rgba(124,93,255,0.3); }
    50% { border-color: rgba(0,209,255,0.6); }
}

.bp-claim-glow {
    position: absolute;
    top: 50%;
    right: 0;
    width: 100px;
    height: 100px;
    background: radial-gradient(circle, rgba(0,209,255,0.3), transparent);
    filter: blur(30px);
    transform: translateY(-50%);
    animation: pulse-glow-effect 2s ease-in-out infinite;
}

@keyframes pulse-glow-effect {
    0%, 100% { opacity: 0.3; }
    50% { opacity: 0.6; }
}

/* Enhanced Icons */
.bp-icon {
    width: 48px; 
    height: 48px;
    border-radius: 12px;
    display: grid; 
    place-items: center;
    background: rgba(255,255,255,0.06);
    color: var(--bp-premium);
    margin-right: 14px;
    font-size: 20px;
    transition: all 0.3s ease;
}

.bp-icon-animated:hover {
    transform: rotate(10deg) scale(1.1);
    background: rgba(247,201,72,0.15);
}

/* Enhanced Claim Button */
.bp-claim-btn {
    background: linear-gradient(135deg, var(--bp-primary), var(--bp-accent));
    color: #0a0e1a;
    font-weight: 700;
    border: none;
    padding: 10px 18px;
    border-radius: 12px;
    box-shadow: var(--bp-glow);
    text-transform: uppercase;
    font-size: 12px;
    letter-spacing: 0.05em;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.bp-claim-btn::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    border-radius: 50%;
    background: rgba(255,255,255,0.5);
    transform: translate(-50%, -50%);
    transition: width 0.6s, height 0.6s;
}

.bp-claim-btn:hover::after {
    width: 300px;
    height: 300px;
}

.bp-claim-btn:active {
    transform: scale(0.95);
}

/* Mission Progress */
.bp-mission { 
    margin-bottom: 18px; 
}

.bp-mission-title { 
    font-weight: 600; 
    margin-bottom: 8px;
    color: var(--bp-text);
}

.bp-mission-progress {
    background: rgba(255,255,255,0.06);
    height: 10px;
    border-radius: 999px;
    overflow: hidden;
    margin: 10px 0;
    position: relative;
    box-shadow: inset 0 2px 4px rgba(0,0,0,0.2);
}

.bp-mission-progress-fill {
    height: 100%;
    background: linear-gradient(90deg, var(--bp-accent), var(--bp-primary));
    border-radius: 999px;
    position: relative;
    transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}

.bp-mission-progress.completed .bp-mission-progress-fill {
    background: linear-gradient(90deg, var(--bp-success), #32d583);
}

/* Animations */
.bp-fade-in {
    animation: fadeIn 0.6s ease-out;
}

.bp-slide-up {
    animation: slideUp 0.8s cubic-bezier(0.4, 0, 0.2, 1);
}

.bp-fade-in-up {
    animation: fadeInUp 0.6s ease-out backwards;
}

.bp-delay-1 { animation-delay: 0.1s; }
.bp-delay-2 { animation-delay: 0.2s; }
.bp-delay-3 { animation-delay: 0.3s; }

.bp-pulse {
    animation: pulse 2s ease-in-out infinite;
}

.bp-pulse-soft {
    animation: pulse-soft 3s ease-in-out infinite;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slideUp {
    from { 
        opacity: 0; 
        transform: translateY(30px); 
    }
    to { 
        opacity: 1; 
        transform: translateY(0); 
    }
}

@keyframes fadeInUp {
    from { 
        opacity: 0; 
        transform: translateY(20px); 
    }
    to { 
        opacity: 1; 
        transform: translateY(0); 
    }
}

@keyframes pulse {
    0%, 100% { 
        transform: scale(1); 
        opacity: 1; 
    }
    50% { 
        transform: scale(1.05); 
        opacity: 0.9; 
    }
}

@keyframes pulse-soft {
    0%, 100% { opacity: 0.8; }
    50% { opacity: 1; }
}

/* Responsive Design */
@media (max-width: 992px) {
    .bp-hero-shell { padding: 60px 0 30px; }
    .bp-title { font-size: 40px; }
    .bp-subtitle { font-size: 16px; }
    .bp-mini-grid { grid-template-columns: repeat(2, 1fr); }
    .bp-stat { margin-right: 16px; }
}

@media (max-width: 768px) {
    .bp-page { padding-bottom: 40px; }
    .bp-title { font-size: 32px; }
    .bp-subtitle { font-size: 15px; }
    .bp-section-header { 
        flex-direction: column; 
        align-items: flex-start; 
        gap: 8px; 
    }
    .bp-reward-card { padding: 14px; }
    .bp-icon { margin-right: 10px; width: 40px; height: 40px; }
    .bp-mini-grid { grid-template-columns: 1fr; }
    .bp-cta { width: 100%; margin-top: 12px; }
}

@media (max-width: 576px) {
    .bp-hero-shell { padding: 40px 0 20px; }
    .bp-glass-premium { padding: 16px; }
    .bp-reward-card:hover { transform: translateX(4px); }
}
</style>

<script>
// Enhanced claim functionality with animations
let claimNonce = '<?php echo htmlspecialchars($claimNonce ?? '', ENT_QUOTES, 'UTF-8'); ?>';

// Honeypot trap
const bpTrap = document.createElement('input');
bpTrap.type = 'text';
bpTrap.name = 'trap_answer';
bpTrap.id = 'bp_trap';
bpTrap.autocomplete = 'off';
bpTrap.style.display = 'none';
document.body.appendChild(bpTrap);

// Enhanced claim button functionality
document.querySelectorAll('.btn-claim').forEach(btn => {
    btn.addEventListener('click', async function() {
        const rewardId = this.dataset.id;
        const originalHTML = this.innerHTML;
        
        // Animated loading state
        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        this.disabled = true;
        this.style.pointerEvents = 'none';

        try {
            const formData = new FormData();
            formData.append('reward_id', rewardId);
            formData.append('nonce', claimNonce);
            formData.append('trap_answer', document.getElementById('bp_trap').value || '');
            
            const response = await fetch(app_base_url('api/battle-pass/claim'), {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();

            if (data.success) {
                // Update nonce
                if (data.nonce) claimNonce = data.nonce;
                
                // Success animation
                this.innerHTML = '<i class="fas fa-check"></i> Claimed!';
                this.style.background = 'linear-gradient(135deg, #2ee6a8, #32d583)';
                
                // Play success sound if available
                if (window.playSfx) playSfx('win');
                
                // Reload after animation
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                // Error state
                alert(data.message || 'Failed to claim reward');
                this.innerHTML = originalHTML;
                this.disabled = false;
                this.style.pointerEvents = 'auto';
            }
        } catch (e) {
            console.error('Claim error:', e);
            alert('Error claiming reward. Please try again.');
            this.innerHTML = originalHTML;
            this.disabled = false;
            this.style.pointerEvents = 'auto';
        }
    });
});

// Smooth scroll reveal for reward cards
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
        }
    });
}, observerOptions);

document.querySelectorAll('.bp-reward-card').forEach(card => {
    observer.observe(card);
});
</script>
