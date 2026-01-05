<?php
/**
 * PREMIUM QUIZ PORTAL - SAAS EDITION
 * Ultra-fast, fully dynamic, next-level animations
 * All resources controlled from admin panel
 */
?>

<!-- Daily Bonus Toast (Dynamic) -->
<?php if (isset($dailyBonus) && $dailyBonus && $dailyBonus['success']): ?>
<div class="daily-bonus-toast" id="bonusToast">
    <div class="bonus-icon"><?php echo isset($dailyBonus['rewards']['steel']) ? '🛡️' : '🪵'; ?></div>
    <div class="bonus-details">
        <h5>Daily Reward! (Day <?php echo $dailyBonus['rewards']['streak']; ?>)</h5>
        <p>
            <?php if (isset($dailyBonus['rewards']['steel'])): ?>
                You received a <strong>Steel Bundle (+10 Steel)</strong> for your 7-day streak!
            <?php else: ?>
                You received <strong>1 Timber Log</strong> for logging in today.
            <?php endif; ?>
        </p>
    </div>
    <button class="close-toast" onclick="document.getElementById('bonusToast').remove()">×</button>
</div>
<?php endif; ?>

<div class="quiz-portal-premium">
    
    <!-- HERO SECTION - Ultra Premium -->
    <section class="hero-section">
        <div class="hero-bg-gradient"></div>
        <div class="hero-particles" id="particles"></div>
        
        <div class="hero-content-wrapper">
            <div class="hero-text">
                <div class="hero-badge">
                    <i class="fas fa-bolt"></i>
                    <span>50,000+ Active Learners</span>
                </div>
                <h1 class="hero-title">
                    Master Your <span class="gradient-text">Engineering Dreams</span>
                </h1>
                <p class="hero-subtitle">
                    AI-powered mock tests, real-time analytics, and gamified learning for Loksewa, License & Entrance exams.
                </p>
                <div class="hero-cta">
                    <a href="#categories" class="btn-hero-primary">
                        <i class="fas fa-rocket"></i>
                        <span>Start Learning</span>
                        <div class="btn-glow"></div>
                    </a>
                    <a href="<?php echo app_base_url('quiz/leaderboard'); ?>" class="btn-hero-secondary">
                        <i class="fas fa-trophy"></i>
                        <span>Leaderboard</span>
                    </a>
                </div>
                
                <!-- Live Stats Counter -->
                <div class="hero-stats">
                    <div class="stat-item">
                        <div class="stat-value" data-count="50000">0</div>
                        <div class="stat-label">Students</div>
                    </div>
                    <div class="stat-divider"></div>
                    <div class="stat-item">
                        <div class="stat-value" data-count="1200">0</div>
                        <div class="stat-label">Mock Tests</div>
                    </div>
                    <div class="stat-divider"></div>
                    <div class="stat-item">
                        <div class="stat-value" data-count="98">0</div>
                        <div class="stat-label">% Success</div>
                    </div>
                </div>
            </div>
            
            <div class="hero-visual">
                <div class="floating-card card-1">
                    <i class="fas fa-graduation-cap"></i>
                    <span>Live Exams</span>
                </div>
                <div class="floating-card card-2">
                    <i class="fas fa-chart-line"></i>
                    <span>Analytics</span>
                </div>
                <div class="floating-card card-3">
                    <i class="fas fa-users"></i>
                    <span>Multiplayer</span>
                </div>
                <div class="hero-glow-orb"></div>
            </div>
        </div>
    </section>

    <!-- CATEGORIES SECTION - Dynamic from Admin -->
    <?php if (!empty($categories)): ?>
    <section id="categories" class="categories-section">
        <div class="section-header">
            <div class="section-badge">
                <i class="fas fa-layer-group"></i>
                <span>Explore Streams</span>
            </div>
            <h2 class="section-title">Choose Your <span class="gradient-text">Engineering Path</span></h2>
            <p class="section-subtitle">Select your specialization and start practicing with curated mock tests</p>
        </div>

        <div class="categories-grid">
            <?php foreach ($categories as $index => $cat): 
                // Dynamic icon from admin or fallback
                $icon = !empty($cat['icon']) ? $cat['icon'] : 'fa-folder';
                // Dynamic image from admin
                $bgImage = !empty($cat['image_path']) ? $cat['image_path'] : '';
            ?>
            <a href="<?php echo app_base_url('quiz?category=' . $cat['slug']); ?>" class="category-card" data-index="<?php echo $index; ?>">
                <?php if ($bgImage): ?>
                <div class="category-bg" style="background-image: url('<?php echo htmlspecialchars($bgImage); ?>')"></div>
                <?php endif; ?>
                <div class="category-overlay"></div>
                <div class="category-content">
                    <div class="category-icon">
                        <i class="fas <?php echo htmlspecialchars($icon); ?>"></i>
                    </div>
                    <h3 class="category-name"><?php echo htmlspecialchars($cat['name']); ?></h3>
                    <?php if (!empty($cat['description'])): ?>
                    <p class="category-desc"><?php echo htmlspecialchars(substr($cat['description'], 0, 60)) . '...'; ?></p>
                    <?php endif; ?>
                    <div class="category-arrow">
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </div>
                <?php if (!empty($cat['is_premium'])): ?>
                <div class="category-premium-badge">
                    <i class="fas fa-crown"></i>
                </div>
                <?php endif; ?>
            </a>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- FEATURED EXAMS - Dynamic -->
    <section class="exams-section">
        <div class="section-header">
            <div class="header-left">
                <div class="section-badge">
                    <i class="fas fa-fire"></i>
                    <span>Trending Now</span>
                </div>
                <h2 class="section-title">Popular <span class="gradient-text">Mock Tests</span></h2>
                <p class="section-subtitle">Most attempted exams this week</p>
            </div>
            <a href="#" class="view-all-btn">
                View All <i class="fas fa-arrow-right"></i>
            </a>
        </div>

        <?php if (empty($exams)): ?>
        <div class="empty-state">
            <div class="empty-icon">
                <i class="fas fa-inbox"></i>
            </div>
            <h3>No Exams Available</h3>
            <p>Check back soon for new mock tests!</p>
        </div>
        <?php else: ?>
        <div class="exams-grid">
            <?php foreach ($exams as $exam): ?>
            <div class="exam-card">
                <!-- Type Badge -->
                <div class="exam-type-badge <?php echo $exam['type']; ?>">
                    <?php echo ucfirst(str_replace('_', ' ', $exam['type'])); ?>
                </div>
                
                <!-- Premium Icon -->
                <?php if(!empty($exam['is_premium'])): ?>
                <div class="exam-premium-icon">
                    <i class="fas fa-crown"></i>
                </div>
                <?php endif; ?>

                <div class="exam-header">
                    <h3 class="exam-title"><?php echo htmlspecialchars($exam['title']); ?></h3>
                    <p class="exam-description"><?php echo htmlspecialchars(substr($exam['description'], 0, 100)) . '...'; ?></p>
                </div>

                <div class="exam-stats">
                    <div class="stat-pill">
                        <i class="fas fa-clock"></i>
                        <span><?php echo $exam['duration_minutes'] > 0 ? $exam['duration_minutes'] . ' min' : 'Unlimited'; ?></span>
                    </div>
                    <div class="stat-pill">
                        <i class="fas fa-list-check"></i>
                        <span><?php echo $exam['question_count']; ?> Questions</span>
                    </div>
                    <div class="stat-pill">
                        <i class="fas fa-star"></i>
                        <span><?php echo $exam['total_marks']; ?> Marks</span>
                    </div>
                </div>

                <a href="<?php echo app_base_url('quiz/overview/' . $exam['slug']); ?>" class="exam-start-btn">
                    <span>Start Practice</span>
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </section>

    <!-- RECENT ACTIVITY - Dynamic -->
    <?php if (!empty($recentAttempts)): ?>
    <section class="activity-section">
        <div class="section-header">
            <div class="section-badge">
                <i class="fas fa-history"></i>
                <span>Your Progress</span>
            </div>
            <h2 class="section-title">Recent <span class="gradient-text">Activity</span></h2>
        </div>

        <div class="activity-timeline">
            <?php foreach ($recentAttempts as $attempt): ?>
            <div class="activity-item">
                <div class="activity-status <?php echo $attempt['status']; ?>">
                    <i class="fas <?php echo $attempt['status'] == 'completed' ? 'fa-check-circle' : 'fa-spinner fa-pulse'; ?>"></i>
                </div>
                <div class="activity-content">
                    <h4 class="activity-title"><?php echo htmlspecialchars($attempt['exam_title']); ?></h4>
                    <p class="activity-time">
                        <i class="fas fa-clock"></i>
                        <?php echo date('M d, Y • H:i', strtotime($attempt['started_at'])); ?>
                    </p>
                </div>
                <div class="activity-action">
                    <?php if($attempt['status'] == 'completed'): ?>
                        <div class="activity-score">
                            <span class="score-label">Score</span>
                            <span class="score-value"><?php echo number_format($attempt['score'], 1); ?>%</span>
                        </div>
                        <a href="<?php echo app_base_url('quiz/result/' . $attempt['id']); ?>" class="btn-activity">
                            View Result
                        </a>
                    <?php else: ?>
                        <span class="status-badge in-progress">In Progress</span>
                        <a href="<?php echo app_base_url('quiz/room/' . $attempt['id']); ?>" class="btn-activity resume">
                            Resume
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- FEATURES SHOWCASE -->
    <section class="features-section">
        <div class="section-header">
            <div class="section-badge">
                <i class="fas fa-sparkles"></i>
                <span>Why Choose Us</span>
            </div>
            <h2 class="section-title">Premium <span class="gradient-text">Learning Features</span></h2>
        </div>

        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-brain"></i>
                </div>
                <h3>AI-Powered Analytics</h3>
                <p>Get personalized insights and performance tracking with advanced AI algorithms</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-gamepad"></i>
                </div>
                <h3>Gamified Learning</h3>
                <p>Earn coins, unlock achievements, and compete on global leaderboards</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-users-line"></i>
                </div>
                <h3>Multiplayer Battles</h3>
                <p>Challenge friends in real-time quiz battles with live rankings</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-mobile-screen"></i>
                </div>
                <h3>Mobile App</h3>
                <p>Practice offline with our iOS and Android apps anytime, anywhere</p>
            </div>
        </div>
    </section>

</div>

<style>
/* ============================================
   PREMIUM QUIZ PORTAL - ULTRA OPTIMIZED
   ============================================ */

:root {
    --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    --success-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    --dark-bg: #0f172a;
    --card-bg: rgba(255, 255, 255, 0.03);
    --card-border: rgba(255, 255, 255, 0.08);
    --text-primary: #ffffff;
    --text-secondary: #94a3b8;
    --radius-lg: 24px;
    --radius-md: 16px;
    --transition-smooth: cubic-bezier(0.4, 0, 0.2, 1);
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

.quiz-portal-premium {
    background: var(--dark-bg);
    color: var(--text-primary);
    overflow-x: hidden;
}

/* ============================================
   HERO SECTION - ULTRA PREMIUM
   ============================================ */

.hero-section {
    position: relative;
    min-height: 100vh;
    display: flex;
    align-items: center;
    padding: 2rem 5%;
    overflow: hidden;
}

.hero-bg-gradient {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: radial-gradient(circle at 20% 50%, rgba(102, 126, 234, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(168, 85, 247, 0.15) 0%, transparent 50%);
    z-index: 0;
}

.hero-particles {
    position: absolute;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    z-index: 1;
}

.hero-content-wrapper {
    position: relative;
    z-index: 2;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 4rem;
    max-width: 1400px;
    margin: 0 auto;
    width: 100%;
}

.hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1.25rem;
    background: rgba(102, 126, 234, 0.1);
    border: 1px solid rgba(102, 126, 234, 0.3);
    border-radius: 50px;
    font-size: 0.85rem;
    font-weight: 600;
    color: #a78bfa;
    margin-bottom: 2rem;
    animation: fadeInUp 0.6s ease-out;
}

.hero-title {
    font-size: clamp(2.5rem, 5vw, 4rem);
    font-weight: 900;
    line-height: 1.1;
    margin-bottom: 1.5rem;
    animation: fadeInUp 0.6s ease-out 0.1s both;
}

.gradient-text {
    background: var(--primary-gradient);
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
}

.hero-subtitle {
    font-size: 1.25rem;
    color: var(--text-secondary);
    line-height: 1.6;
    margin-bottom: 2.5rem;
    animation: fadeInUp 0.6s ease-out 0.2s both;
}

.hero-cta {
    display: flex;
    gap: 1rem;
    margin-bottom: 3rem;
    animation: fadeInUp 0.6s ease-out 0.3s both;
}

.btn-hero-primary,
.btn-hero-secondary {
    position: relative;
    padding: 1.25rem 2.5rem;
    border-radius: var(--radius-md);
    font-weight: 700;
    font-size: 1rem;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.75rem;
    transition: all 0.3s var(--transition-smooth);
    overflow: hidden;
}

.btn-hero-primary {
    background: var(--primary-gradient);
    color: white;
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
}

.btn-hero-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 15px 40px rgba(102, 126, 234, 0.4);
}

.btn-glow {
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    transition: left 0.5s;
}

.btn-hero-primary:hover .btn-glow {
    left: 100%;
}

.btn-hero-secondary {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    color: white;
    backdrop-filter: blur(10px);
}

.btn-hero-secondary:hover {
    background: rgba(255, 255, 255, 0.1);
    border-color: rgba(102, 126, 234, 0.5);
}

/* Hero Stats Counter */
.hero-stats {
    display: flex;
    gap: 2rem;
    animation: fadeInUp 0.6s ease-out 0.4s both;
}

.stat-item {
    text-align: center;
}

.stat-value {
    font-size: 2.5rem;
    font-weight: 900;
    background: var(--primary-gradient);
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
}

.stat-label {
    font-size: 0.85rem;
    color: var(--text-secondary);
    margin-top: 0.25rem;
}

.stat-divider {
    width: 1px;
    background: rgba(255, 255, 255, 0.1);
}

/* Hero Visual */
.hero-visual {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
}

.hero-glow-orb {
    position: absolute;
    width: 400px;
    height: 400px;
    background: radial-gradient(circle, rgba(102, 126, 234, 0.3) 0%, transparent 70%);
    filter: blur(80px);
    animation: pulse 4s ease-in-out infinite;
}

.floating-card {
    position: absolute;
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: var(--radius-md);
    padding: 1.5rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    animation: float 6s ease-in-out infinite;
}

.floating-card i {
    font-size: 2rem;
    background: var(--primary-gradient);
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
}

.floating-card span {
    font-size: 0.85rem;
    font-weight: 600;
}

.card-1 {
    top: 10%;
    left: 10%;
    animation-delay: 0s;
}

.card-2 {
    top: 50%;
    right: 10%;
    animation-delay: 2s;
}

.card-3 {
    bottom: 15%;
    left: 20%;
    animation-delay: 4s;
}

/* ============================================
   CATEGORIES SECTION
   ============================================ */

.categories-section,
.exams-section,
.activity-section,
.features-section {
    padding: 5rem 5%;
    max-width: 1400px;
    margin: 0 auto;
}

.section-header {
    text-align: center;
    margin-bottom: 4rem;
}

.section-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1.25rem;
    background: rgba(102, 126, 234, 0.1);
    border: 1px solid rgba(102, 126, 234, 0.3);
    border-radius: 50px;
    font-size: 0.85rem;
    font-weight: 600;
    color: #a78bfa;
    margin-bottom: 1rem;
}

.section-title {
    font-size: clamp(2rem, 4vw, 3rem);
    font-weight: 900;
    margin-bottom: 1rem;
}

.section-subtitle {
    font-size: 1.1rem;
    color: var(--text-secondary);
}

.categories-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 2rem;
}

.category-card {
    position: relative;
    background: var(--card-bg);
    border: 1px solid var(--card-border);
    border-radius: var(--radius-lg);
    padding: 2.5rem;
    text-decoration: none;
    overflow: hidden;
    transition: all 0.4s var(--transition-smooth);
    cursor: pointer;
}

.category-bg {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-size: cover;
    background-position: center;
    opacity: 0.1;
    transition: opacity 0.4s, transform 0.4s;
}

.category-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(168, 85, 247, 0.1) 100%);
    opacity: 0;
    transition: opacity 0.4s;
}

.category-card:hover {
    transform: translateY(-8px);
    border-color: rgba(102, 126, 234, 0.5);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
}

.category-card:hover .category-bg {
    opacity: 0.2;
    transform: scale(1.1);
}

.category-card:hover .category-overlay {
    opacity: 1;
}

.category-content {
    position: relative;
    z-index: 2;
}

.category-icon {
    width: 70px;
    height: 70px;
    background: var(--primary-gradient);
    border-radius: var(--radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: white;
    margin-bottom: 1.5rem;
    box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
}

.category-name {
    font-size: 1.5rem;
    font-weight: 700;
    color: white;
    margin-bottom: 0.75rem;
}

.category-desc {
    font-size: 0.9rem;
    color: var(--text-secondary);
    margin-bottom: 1rem;
}

.category-arrow {
    color: #667eea;
    font-size: 1.25rem;
    opacity: 0;
    transform: translateX(-10px);
    transition: all 0.3s;
}

.category-card:hover .category-arrow {
    opacity: 1;
    transform: translateX(0);
}

.category-premium-badge {
    position: absolute;
    top: 1.5rem;
    right: 1.5rem;
    color: #f59e0b;
    font-size: 1.5rem;
    z-index: 3;
}

/* ============================================
   EXAMS GRID
   ============================================ */

.exams-section .section-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    text-align: left;
}

.view-all-btn {
    color: #667eea;
    text-decoration: none;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: gap 0.3s;
}

.view-all-btn:hover {
    gap: 1rem;
}

.exams-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 2rem;
}

.exam-card {
    position: relative;
    background: var(--card-bg);
    border: 1px solid var(--card-border);
    border-radius: var(--radius-lg);
    padding: 2rem;
    transition: all 0.3s var(--transition-smooth);
}

.exam-card:hover {
    background: rgba(255, 255, 255, 0.05);
    border-color: rgba(102, 126, 234, 0.5);
    transform: translateY(-4px);
}

.exam-type-badge {
    position: absolute;
    top: 1.5rem;
    right: 1.5rem;
    padding: 0.4rem 1rem;
    border-radius: 50px;
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
}

.exam-type-badge.practice_set {
    background: rgba(16, 185, 129, 0.1);
    color: #10b981;
    border: 1px solid rgba(16, 185, 129, 0.3);
}

.exam-type-badge.mock_test {
    background: rgba(239, 68, 68, 0.1);
    color: #ef4444;
    border: 1px solid rgba(239, 68, 68, 0.3);
}

.exam-premium-icon {
    position: absolute;
    top: 1.5rem;
    left: 1.5rem;
    color: #f59e0b;
    font-size: 1.25rem;
}

.exam-header {
    margin-bottom: 1.5rem;
}

.exam-title {
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 0.75rem;
    color: white;
}

.exam-description {
    font-size: 0.95rem;
    color: var(--text-secondary);
    line-height: 1.5;
}

.exam-stats {
    display: flex;
    gap: 1rem;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
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

.exam-start-btn {
    width: 100%;
    padding: 1rem;
    background: transparent;
    border: 2px solid #667eea;
    color: #667eea;
    border-radius: var(--radius-md);
    font-weight: 700;
    text-decoration: none;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
    transition: all 0.3s var(--transition-smooth);
}

.exam-start-btn:hover {
    background: #667eea;
    color: white;
    box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
}

/* ============================================
   ACTIVITY TIMELINE
   ============================================ */

.activity-timeline {
    background: var(--card-bg);
    border: 1px solid var(--card-border);
    border-radius: var(--radius-lg);
    overflow: hidden;
}

.activity-item {
    padding: 1.5rem 2rem;
    display: flex;
    align-items: center;
    gap: 1.5rem;
    border-bottom: 1px solid var(--card-border);
    transition: background 0.2s;
}

.activity-item:last-child {
    border-bottom: none;
}

.activity-item:hover {
    background: rgba(255, 255, 255, 0.02);
}

.activity-status {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

.activity-status.completed {
    background: rgba(16, 185, 129, 0.1);
    color: #10b981;
}

.activity-status.in_progress {
    background: rgba(245, 158, 11, 0.1);
    color: #f59e0b;
}

.activity-content {
    flex: 1;
}

.activity-title {
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.activity-time {
    font-size: 0.85rem;
    color: var(--text-secondary);
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.activity-action {
    display: flex;
    align-items: center;
    gap: 1.5rem;
}

.activity-score {
    text-align: center;
}

.score-label {
    display: block;
    font-size: 0.75rem;
    color: var(--text-secondary);
    margin-bottom: 0.25rem;
}

.score-value {
    font-size: 1.5rem;
    font-weight: 900;
    background: var(--success-gradient);
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
}

.status-badge {
    padding: 0.5rem 1rem;
    border-radius: 50px;
    font-size: 0.85rem;
    font-weight: 600;
}

.status-badge.in-progress {
    background: rgba(245, 158, 11, 0.1);
    color: #f59e0b;
}

.btn-activity {
    padding: 0.75rem 1.5rem;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid var(--card-border);
    color: white;
    border-radius: 10px;
    text-decoration: none;
    font-weight: 600;
    font-size: 0.9rem;
    transition: all 0.3s;
}

.btn-activity:hover {
    background: #667eea;
    border-color: #667eea;
}

.btn-activity.resume {
    background: #667eea;
    border-color: #667eea;
}

/* ============================================
   FEATURES GRID
   ============================================ */

.features-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 2rem;
}

.feature-card {
    background: var(--card-bg);
    border: 1px solid var(--card-border);
    border-radius: var(--radius-lg);
    padding: 2.5rem;
    text-align: center;
    transition: all 0.3s var(--transition-smooth);
}

.feature-card:hover {
    transform: translateY(-8px);
    border-color: rgba(102, 126, 234, 0.5);
}

.feature-icon {
    width: 80px;
    height: 80px;
    background: var(--primary-gradient);
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.5rem;
    color: white;
    margin: 0 auto 1.5rem;
    box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
}

.feature-card h3 {
    font-size: 1.25rem;
    font-weight: 700;
    margin-bottom: 1rem;
}

.feature-card p {
    color: var(--text-secondary);
    line-height: 1.6;
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

@keyframes float {
    0%, 100% {
        transform: translateY(0) rotate(0deg);
    }
    50% {
        transform: translateY(-20px) rotate(5deg);
    }
}

@keyframes pulse {
    0%, 100% {
        transform: scale(1);
        opacity: 0.3;
    }
    50% {
        transform: scale(1.1);
        opacity: 0.5;
    }
}

/* ============================================
   DAILY BONUS TOAST
   ============================================ */

.daily-bonus-toast {
    position: fixed;
    top: 2rem;
    right: 2rem;
    z-index: 9999;
    background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%);
    color: white;
    padding: 1.5rem;
    border-radius: var(--radius-md);
    display: flex;
    align-items: center;
    gap: 1rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
    border: 1px solid rgba(255, 255, 255, 0.1);
    animation: slideInRight 0.5s ease-out;
}

.bonus-icon {
    font-size: 2.5rem;
}

.bonus-details h5 {
    margin: 0 0 0.5rem;
    font-weight: 800;
    color: #f59e0b;
}

.bonus-details p {
    margin: 0;
    font-size: 0.9rem;
    opacity: 0.9;
}

.close-toast {
    background: none;
    border: none;
    color: white;
    font-size: 1.5rem;
    cursor: pointer;
    opacity: 0.7;
    transition: opacity 0.2s;
}

.close-toast:hover {
    opacity: 1;
}

@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

/* ============================================
   EMPTY STATE
   ============================================ */

.empty-state {
    text-align: center;
    padding: 5rem 2rem;
    background: var(--card-bg);
    border: 1px dashed var(--card-border);
    border-radius: var(--radius-lg);
}

.empty-icon {
    font-size: 4rem;
    color: var(--text-secondary);
    margin-bottom: 1.5rem;
    opacity: 0.5;
}

.empty-state h3 {
    font-size: 1.5rem;
    margin-bottom: 0.75rem;
}

.empty-state p {
    color: var(--text-secondary);
    font-size: 1.1rem;
}

/* ============================================
   RESPONSIVE DESIGN
   ============================================ */

@media (max-width: 1024px) {
    .hero-content-wrapper {
        grid-template-columns: 1fr;
        text-align: center;
    }
    
    .hero-visual {
        display: none;
    }
    
    .hero-cta {
        justify-content: center;
    }
    
    .hero-stats {
        justify-content: center;
    }
}

@media (max-width: 768px) {
    .hero-section {
        padding: 2rem 1rem;
    }
    
    .categories-section,
    .exams-section,
    .activity-section,
    .features-section {
        padding: 3rem 1rem;
    }
    
    .categories-grid,
    .exams-grid {
        grid-template-columns: 1fr;
    }
    
    .hero-cta {
        flex-direction: column;
    }
    
    .btn-hero-primary,
    .btn-hero-secondary {
        width: 100%;
        justify-content: center;
    }
    
    .activity-item {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .activity-action {
        width: 100%;
        justify-content: space-between;
    }
    
    .daily-bonus-toast {
        top: 1rem;
        right: 1rem;
        left: 1rem;
    }
}
</style>

<script>
// ============================================
// ULTRA-FAST ANIMATIONS & INTERACTIONS
// ============================================

document.addEventListener('DOMContentLoaded', function() {
    
    // Animated Counter for Hero Stats
    const counters = document.querySelectorAll('.stat-value');
    counters.forEach(counter => {
        const target = parseInt(counter.getAttribute('data-count'));
        const duration = 2000;
        const increment = target / (duration / 16);
        let current = 0;
        
        const updateCounter = () => {
            current += increment;
            if (current < target) {
                counter.textContent = Math.floor(current).toLocaleString();
                requestAnimationFrame(updateCounter);
            } else {
                counter.textContent = target.toLocaleString();
            }
        };
        
        // Trigger when in viewport
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    updateCounter();
                    observer.unobserve(entry.target);
                }
            });
        });
        
        observer.observe(counter);
    });
    
    // Particle Animation for Hero
    const particlesContainer = document.getElementById('particles');
    if (particlesContainer) {
        for (let i = 0; i < 50; i++) {
            const particle = document.createElement('div');
            particle.style.cssText = `
                position: absolute;
                width: 2px;
                height: 2px;
                background: rgba(102, 126, 234, 0.5);
                border-radius: 50%;
                top: ${Math.random() * 100}%;
                left: ${Math.random() * 100}%;
                animation: particleFloat ${5 + Math.random() * 10}s linear infinite;
            `;
            particlesContainer.appendChild(particle);
        }
    }
    
    // Smooth Scroll for Anchor Links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // Stagger Animation for Cards
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -100px 0px'
    };
    
    const cardObserver = new IntersectionObserver((entries) => {
        entries.forEach((entry, index) => {
            if (entry.isIntersecting) {
                setTimeout(() => {
                    entry.target.style.animation = `fadeInUp 0.6s ease-out forwards`;
                }, index * 100);
                cardObserver.unobserve(entry.target);
            }
        });
    }, observerOptions);
    
    document.querySelectorAll('.category-card, .exam-card, .feature-card').forEach(card => {
        card.style.opacity = '0';
        cardObserver.observe(card);
    });
    
    // Auto-dismiss bonus toast after 5 seconds
    const bonusToast = document.getElementById('bonusToast');
    if (bonusToast) {
        setTimeout(() => {
            bonusToast.style.animation = 'slideOutRight 0.5s ease-out forwards';
            setTimeout(() => bonusToast.remove(), 500);
        }, 5000);
    }
});

// Particle Float Animation
const style = document.createElement('style');
style.textContent = `
    @keyframes particleFloat {
        0% {
            transform: translateY(0) translateX(0);
            opacity: 0;
        }
        10% {
            opacity: 1;
        }
        90% {
            opacity: 1;
        }
        100% {
            transform: translateY(-100vh) translateX(${Math.random() * 100 - 50}px);
            opacity: 0;
        }
    }
    
    @keyframes slideOutRight {
        to {
            transform: translateX(120%);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);
</script>
