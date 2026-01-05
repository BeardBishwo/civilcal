

<div class="leaderboard-premium">
    
    <!-- Header Section -->
    <section class="leaderboard-header">
        <div class="header-bg-gradient"></div>
        <div class="header-content">
            <div class="header-badge">
                <i class="fas fa-trophy"></i>
                <span>Hall of Fame</span>
            </div>
            <h1 class="header-title">
                Top <span class="gradient-text">Performers</span>
            </h1>
            <p class="header-subtitle">Compete with the best and climb the ranks</p>
            
            <!-- Period Filters -->
            <div class="period-filters">
                <a href="?period=weekly" class="filter-btn <?php echo $current_period == 'weekly' ? 'active' : ''; ?>">
                    <i class="fas fa-calendar-week"></i>
                    <span>Weekly</span>
                </a>
                <a href="?period=monthly" class="filter-btn <?php echo $current_period == 'monthly' ? 'active' : ''; ?>">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Monthly</span>
                </a>
                <a href="?period=yearly" class="filter-btn <?php echo $current_period == 'yearly' ? 'active' : ''; ?>">
                    <i class="fas fa-calendar"></i>
                    <span>Yearly</span>
                </a>
            </div>
        </div>
    </section>

    <!-- Rankings Section -->
    <section class="rankings-section">
        <div class="rankings-container">
            
            <?php if (empty($rankings)): ?>
            <!-- Empty State -->
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-trophy"></i>
                </div>
                <h3>No Rankings Yet</h3>
                <p>Be the first to take a test and claim the top spot!</p>
                <a href="<?php echo app_base_url('quiz'); ?>" class="btn-start">
                    <i class="fas fa-play-circle"></i>
                    <span>Start Learning</span>
                </a>
            </div>
            
            <?php else: ?>
            
            <!-- Top 3 Podium -->
            <?php 
            $topThree = array_slice($rankings, 0, 3);
            $restRankings = array_slice($rankings, 3);
            ?>
            
            <div class="podium-section">
                <?php foreach ($topThree as $index => $rank): ?>
                <?php
                    $position = $rank['calculated_rank'];
                    $podiumClass = '';
                    $medal = '';
                    if ($position == 1) {
                        $podiumClass = 'first';
                        $medal = '🥇';
                    } elseif ($position == 2) {
                        $podiumClass = 'second';
                        $medal = '🥈';
                    } elseif ($position == 3) {
                        $podiumClass = 'third';
                        $medal = '🥉';
                    }
                ?>
                <div class="podium-card <?php echo $podiumClass; ?>" data-rank="<?php echo $position; ?>">
                    <div class="podium-medal"><?php echo $medal; ?></div>
                    <div class="podium-avatar">
                        <div class="avatar-circle">
                            <?php echo strtoupper(substr($rank['full_name'], 0, 1)); ?>
                        </div>
                        <div class="rank-badge">#<?php echo $position; ?></div>
                    </div>
                    <h3 class="podium-name"><?php echo htmlspecialchars($rank['full_name']); ?></h3>
                    <p class="podium-username">@<?php echo htmlspecialchars($rank['username']); ?></p>
                    <div class="podium-stats">
                        <div class="stat-item">
                            <div class="stat-value"><?php echo number_format($rank['total_score'], 0); ?></div>
                            <div class="stat-label">Score</div>
                        </div>
                        <div class="stat-divider"></div>
                        <div class="stat-item">
                            <div class="stat-value"><?php echo number_format($rank['accuracy_avg'], 1); ?>%</div>
                            <div class="stat-label">Accuracy</div>
                        </div>
                        <div class="stat-divider"></div>
                        <div class="stat-item">
                            <div class="stat-value"><?php echo $rank['tests_taken']; ?></div>
                            <div class="stat-label">Tests</div>
                        </div>
                    </div>
                    <?php if ($rank['trend'] != 0): ?>
                    <div class="trend-badge <?php echo $rank['trend'] > 0 ? 'up' : 'down'; ?>">
                        <i class="fas fa-arrow-<?php echo $rank['trend'] > 0 ? 'up' : 'down'; ?>"></i>
                        <?php echo abs($rank['trend']); ?>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Rest of Rankings -->
            <?php if (!empty($restRankings)): ?>
            <div class="rankings-list">
                <h2 class="list-title">All Rankings</h2>
                
                <?php foreach ($restRankings as $rank): ?>
                <?php $isCurrentUser = ($rank['user_id'] == ($_SESSION['user_id'] ?? 0)); ?>
                <div class="ranking-card <?php echo $isCurrentUser ? 'current-user' : ''; ?>">
                    <div class="rank-number">
                        <span>#<?php echo $rank['calculated_rank']; ?></span>
                    </div>
                    <div class="rank-avatar">
                        <div class="avatar-circle-small">
                            <?php echo strtoupper(substr($rank['full_name'], 0, 1)); ?>
                        </div>
                    </div>
                    <div class="rank-info">
                        <h4 class="rank-name"><?php echo htmlspecialchars($rank['full_name']); ?></h4>
                        <p class="rank-username">@<?php echo htmlspecialchars($rank['username']); ?></p>
                    </div>
                    <div class="rank-stats">
                        <div class="stat-pill">
                            <i class="fas fa-star"></i>
                            <span><?php echo number_format($rank['total_score'], 0); ?></span>
                        </div>
                        <div class="stat-pill">
                            <i class="fas fa-bullseye"></i>
                            <span><?php echo number_format($rank['accuracy_avg'], 1); ?>%</span>
                        </div>
                        <div class="stat-pill">
                            <i class="fas fa-list-check"></i>
                            <span><?php echo $rank['tests_taken']; ?> tests</span>
                        </div>
                    </div>
                    <?php if ($rank['trend'] != 0): ?>
                    <div class="rank-trend <?php echo $rank['trend'] > 0 ? 'up' : 'down'; ?>">
                        <i class="fas fa-arrow-<?php echo $rank['trend'] > 0 ? 'up' : 'down'; ?>"></i>
                        <span><?php echo abs($rank['trend']); ?></span>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            
            <?php endif; ?>
        </div>
    </section>
</div>

<style>
/* ============================================
   PREMIUM LEADERBOARD - ULTRA OPTIMIZED
   ============================================ */

:root {
    --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --gold-gradient: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    --silver-gradient: linear-gradient(135deg, #94a3b8 0%, #64748b 100%);
    --bronze-gradient: linear-gradient(135deg, #ea580c 0%, #c2410c 100%);
    --dark-bg: #0f172a;
    --card-bg: rgba(255, 255, 255, 0.03);
    --card-border: rgba(255, 255, 255, 0.08);
    --text-primary: #ffffff;
    --text-secondary: #94a3b8;
}

.leaderboard-premium {
    background: var(--dark-bg);
    color: var(--text-primary);
    min-height: 100vh;
}

/* ============================================
   HEADER SECTION
   ============================================ */

.leaderboard-header {
    position: relative;
    padding: 5rem 5% 3rem;
    text-align: center;
    overflow: hidden;
}

.header-bg-gradient {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: radial-gradient(circle at 50% 0%, rgba(102, 126, 234, 0.15) 0%, transparent 70%);
    z-index: 0;
}

.header-content {
    position: relative;
    z-index: 2;
    max-width: 800px;
    margin: 0 auto;
}

.header-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1.25rem;
    background: rgba(245, 158, 11, 0.1);
    border: 1px solid rgba(245, 158, 11, 0.3);
    border-radius: 50px;
    font-size: 0.85rem;
    font-weight: 600;
    color: #f59e0b;
    margin-bottom: 1.5rem;
    animation: fadeInUp 0.6s ease-out;
}

.header-title {
    font-size: clamp(2.5rem, 5vw, 4rem);
    font-weight: 900;
    line-height: 1.1;
    margin-bottom: 1rem;
    animation: fadeInUp 0.6s ease-out 0.1s both;
}

.gradient-text {
    background: var(--primary-gradient);
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
}

.header-subtitle {
    font-size: 1.25rem;
    color: var(--text-secondary);
    margin-bottom: 2.5rem;
    animation: fadeInUp 0.6s ease-out 0.2s both;
}

/* Period Filters */
.period-filters {
    display: flex;
    gap: 1rem;
    justify-content: center;
    animation: fadeInUp 0.6s ease-out 0.3s both;
}

.filter-btn {
    padding: 0.75rem 1.5rem;
    background: var(--card-bg);
    border: 1px solid var(--card-border);
    border-radius: 12px;
    color: var(--text-secondary);
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 600;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.filter-btn:hover {
    background: rgba(255, 255, 255, 0.05);
    border-color: rgba(102, 126, 234, 0.5);
    color: white;
    transform: translateY(-2px);
}

.filter-btn.active {
    background: var(--primary-gradient);
    border-color: transparent;
    color: white;
    box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
}

/* ============================================
   RANKINGS SECTION
   ============================================ */

.rankings-section {
    padding: 0 5% 5rem;
}

.rankings-container {
    max-width: 1400px;
    margin: 0 auto;
}

/* ============================================
   PODIUM SECTION
   ============================================ */

.podium-section {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 2rem;
    margin-bottom: 4rem;
}

.podium-card {
    position: relative;
    background: var(--card-bg);
    border: 1px solid var(--card-border);
    border-radius: 24px;
    padding: 2.5rem 2rem;
    text-align: center;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    animation: fadeInUp 0.6s ease-out both;
}

.podium-card.first {
    animation-delay: 0.4s;
    border-color: rgba(245, 158, 11, 0.5);
    background: linear-gradient(135deg, rgba(245, 158, 11, 0.05) 0%, rgba(217, 119, 6, 0.05) 100%);
}

.podium-card.second {
    animation-delay: 0.5s;
    border-color: rgba(148, 163, 184, 0.5);
}

.podium-card.third {
    animation-delay: 0.6s;
    border-color: rgba(234, 88, 12, 0.5);
}

.podium-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
}

.podium-medal {
    font-size: 3rem;
    margin-bottom: 1rem;
}

.podium-avatar {
    position: relative;
    display: inline-block;
    margin-bottom: 1.5rem;
}

.avatar-circle {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    background: var(--primary-gradient);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.5rem;
    font-weight: 900;
    color: white;
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
}

.podium-card.first .avatar-circle {
    background: var(--gold-gradient);
}

.podium-card.second .avatar-circle {
    background: var(--silver-gradient);
}

.podium-card.third .avatar-circle {
    background: var(--bronze-gradient);
}

.rank-badge {
    position: absolute;
    bottom: -5px;
    right: -5px;
    background: var(--dark-bg);
    border: 2px solid var(--card-border);
    border-radius: 50px;
    padding: 0.25rem 0.75rem;
    font-size: 0.85rem;
    font-weight: 700;
    color: white;
}

.podium-name {
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 0.25rem;
}

.podium-username {
    font-size: 0.9rem;
    color: var(--text-secondary);
    margin-bottom: 1.5rem;
}

.podium-stats {
    display: flex;
    justify-content: center;
    gap: 1rem;
    padding: 1.5rem 0;
    border-top: 1px solid var(--card-border);
}

.stat-item {
    text-align: center;
}

.stat-value {
    font-size: 1.5rem;
    font-weight: 900;
    background: var(--primary-gradient);
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
}

.stat-label {
    font-size: 0.75rem;
    color: var(--text-secondary);
    margin-top: 0.25rem;
}

.stat-divider {
    width: 1px;
    background: var(--card-border);
}

.trend-badge {
    position: absolute;
    top: 1.5rem;
    right: 1.5rem;
    padding: 0.4rem 0.75rem;
    border-radius: 50px;
    font-size: 0.85rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.trend-badge.up {
    background: rgba(16, 185, 129, 0.1);
    color: #10b981;
    border: 1px solid rgba(16, 185, 129, 0.3);
}

.trend-badge.down {
    background: rgba(239, 68, 68, 0.1);
    color: #ef4444;
    border: 1px solid rgba(239, 68, 68, 0.3);
}

/* ============================================
   RANKINGS LIST
   ============================================ */

.rankings-list {
    animation: fadeInUp 0.6s ease-out 0.7s both;
}

.list-title {
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 2rem;
    text-align: center;
}

.ranking-card {
    background: var(--card-bg);
    border: 1px solid var(--card-border);
    border-radius: 16px;
    padding: 1.5rem;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 1.5rem;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.ranking-card:hover {
    background: rgba(255, 255, 255, 0.05);
    border-color: rgba(102, 126, 234, 0.5);
    transform: translateX(8px);
}

.ranking-card.current-user {
    background: rgba(102, 126, 234, 0.1);
    border-color: rgba(102, 126, 234, 0.5);
}

.rank-number {
    font-size: 1.5rem;
    font-weight: 900;
    color: var(--text-secondary);
    min-width: 50px;
    text-align: center;
}

.avatar-circle-small {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: var(--primary-gradient);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    font-weight: 900;
    color: white;
}

.rank-info {
    flex: 1;
}

.rank-name {
    font-size: 1.1rem;
    font-weight: 700;
    margin-bottom: 0.25rem;
}

.rank-username {
    font-size: 0.85rem;
    color: var(--text-secondary);
    margin: 0;
}

.rank-stats {
    display: flex;
    gap: 1rem;
}

.stat-pill {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 50px;
    font-size: 0.85rem;
    color: var(--text-secondary);
}

.stat-pill i {
    color: #667eea;
}

.rank-trend {
    padding: 0.5rem 1rem;
    border-radius: 50px;
    font-size: 0.85rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.rank-trend.up {
    background: rgba(16, 185, 129, 0.1);
    color: #10b981;
}

.rank-trend.down {
    background: rgba(239, 68, 68, 0.1);
    color: #ef4444;
}

/* ============================================
   EMPTY STATE
   ============================================ */

.empty-state {
    text-align: center;
    padding: 5rem 2rem;
    background: var(--card-bg);
    border: 1px dashed var(--card-border);
    border-radius: 24px;
    animation: fadeInUp 0.6s ease-out 0.4s both;
}

.empty-icon {
    font-size: 5rem;
    color: #f59e0b;
    margin-bottom: 1.5rem;
}

.empty-state h3 {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 1rem;
}

.empty-state p {
    color: var(--text-secondary);
    font-size: 1.1rem;
    margin-bottom: 2rem;
}

.btn-start {
    display: inline-flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem 2rem;
    background: var(--primary-gradient);
    color: white;
    border-radius: 12px;
    text-decoration: none;
    font-weight: 700;
    transition: all 0.3s;
}

.btn-start:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
    color: white;
}

/* ============================================
   ANIMATIONS
   ============================================ */

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* ============================================
   RESPONSIVE
   ============================================ */

@media (max-width: 1024px) {
    .podium-section {
        grid-template-columns: 1fr;
        max-width: 500px;
        margin: 0 auto 4rem;
    }
    
    .rank-stats {
        flex-direction: column;
        gap: 0.5rem;
    }
}

@media (max-width: 768px) {
    .leaderboard-header {
        padding: 3rem 1rem 2rem;
    }
    
    .rankings-section {
        padding: 0 1rem 3rem;
    }
    
    .period-filters {
        flex-direction: column;
    }
    
    .filter-btn {
        width: 100%;
        justify-content: center;
    }
    
    .ranking-card {
        flex-direction: column;
        text-align: center;
    }
    
    .rank-number {
        min-width: auto;
    }
    
    .rank-stats {
        width: 100%;
        justify-content: center;
    }
}
</style>
