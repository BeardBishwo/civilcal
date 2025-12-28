<?php
/**
 * Quiz Portal View - Premium "Quiz Gamenta" Design
 * 
 * Features:
 * - Modern 4-column responsive grid
 * - Glassmorphism effects with backdrop blur
 * - Vibrant gradient-styled cards
 * - High-quality FontAwesome 6 icons
 * - Hero section with dynamic typography
 * - Mobile app promotion footer
 */
?>

<div class="portal-container">
    <!-- Hero Section -->
    <section class="portal-hero">
        <div class="hero-content">
            <h1 class="hero-title">Master the <span class="gradient-text">Next Generation</span> of Learning</h1>
            <p class="hero-subtitle">Join 50,000+ engineers preparing for Loksewa, License, and Entrance exams with AI-powered analytics and gamified battles.</p>
            <div class="hero-actions">
                <a href="#streams" class="premium-btn btn-primary">
                    <i class="fas fa-play-circle"></i> Start Learning
                </a>
                <a href="<?php echo app_base_url('quiz/leaderboard'); ?>" class="premium-btn btn-secondary">
                    <i class="fas fa-trophy"></i> Global Ranking
                </a>
            </div>
        </div>
        <div class="hero-visual">
            <div class="floating-icons">
                <i class="fas fa-hard-hat icon-1"></i>
                <i class="fas fa-bolt icon-2"></i>
                <i class="fas fa-calculator icon-3"></i>
            </div>
            <div class="hero-glow"></div>
        </div>
    </section>

    <!-- Categories / Streams Grid -->
    <?php if (!empty($categories)): ?>
    <section id="streams" class="streams-section">
        <div class="section-header">
            <h2 class="section-title">Explore Your <span class="gradient-text">Stream</span></h2>
            <p class="section-subtitle">Select a category to view specialized mock tests and practice sets.</p>
        </div>
        <div class="streams-grid">
            <?php 
            $streamIcons = ['fa-hard-hat', 'fa-bolt', 'fa-faucet', 'fa-wind', 'fa-fire-extinguisher', 'fa-map-marked-alt', 'fa-building', 'fa-tools'];
            $streamGradients = [
                'linear-gradient(135deg, #6366f1 0%, #a855f7 100%)', // Indigo-Purple
                'linear-gradient(135deg, #3b82f6 0%, #2dd4bf 100%)', // Blue-Teal
                'linear-gradient(135deg, #f59e0b 0%, #ef4444 100%)', // Amber-Red
                'linear-gradient(135deg, #10b981 0%, #3b82f6 100%)'  // Emerald-Blue
            ];
            foreach ($categories as $index => $cat): 
                $icon = $streamIcons[$index % count($streamIcons)];
                $gradient = $streamGradients[$index % count($streamGradients)];
            ?>
            <div class="stream-card-wrapper">
                <a href="<?php echo app_base_url('quiz?category=' . $cat['slug']); ?>" class="stream-card">
                    <div class="stream-icon-box" style="background: <?php echo $gradient; ?>">
                        <i class="fas <?php echo $icon; ?>"></i>
                    </div>
                    <h3 class="stream-name"><?php echo htmlspecialchars($cat['name']); ?></h3>
                    <div class="stream-hover-arrow">
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- Popular Exams Section -->
    <section class="exams-section">
        <div class="section-header">
            <div class="header-left">
                <h2 class="section-title">Popular <span class="gradient-text">Exams</span></h2>
                <p class="section-subtitle">The most attempted mock tests this week.</p>
            </div>
            <div class="header-right">
                <a href="#" class="view-all-link">View All Exams <i class="fas fa-chevron-right"></i></a>
            </div>
        </div>

        <?php if (empty($exams)): ?>
            <div class="empty-state">
                <i class="fas fa-folder-open"></i>
                <p>No featured exams available right now. Check back soon!</p>
            </div>
        <?php else: ?>
            <div class="exams-grid">
                <?php foreach ($exams as $exam): ?>
                <div class="exam-card">
                    <div class="exam-card-badge <?php echo $exam['type']; ?>">
                        <?php echo ucfirst(str_replace('_', ' ', $exam['type'])); ?>
                    </div>
                    <?php if($exam['is_premium']): ?>
                        <div class="exam-premium-icon" title="Premium Access Required">
                            <i class="fas fa-crown"></i>
                        </div>
                    <?php endif; ?>

                    <div class="exam-info">
                        <h3 class="exam-title"><?php echo htmlspecialchars($exam['title']); ?></h3>
                        <p class="exam-desc"><?php echo htmlspecialchars(substr($exam['description'], 0, 90)) . (strlen($exam['description']) > 90 ? '...' : ''); ?></p>
                        
                        <div class="exam-meta">
                            <div class="meta-item"><i class="fas fa-clock"></i> <?php echo $exam['duration_minutes'] > 0 ? $exam['duration_minutes'] . ' min' : '∞'; ?></div>
                            <div class="meta-item"><i class="fas fa-list-check"></i> <?php echo $exam['question_count']; ?> Qs</div>
                            <div class="meta-item"><i class="fas fa-star"></i> <?php echo $exam['total_marks']; ?></div>
                        </div>
                    </div>

                    <div class="exam-footer">
                        <a href="<?php echo app_base_url('quiz/overview/' . $exam['slug']); ?>" class="btn-start-exam">
                            Practice Now <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

    <!-- Recent Activity Glassmorphism List -->
    <?php if (!empty($recentAttempts)): ?>
    <section class="activity-section">
        <div class="section-header">
            <h2 class="section-title">Your Recent <span class="gradient-text">Activity</span></h2>
        </div>
        <div class="activity-list-glass">
            <?php foreach ($recentAttempts as $attempt): ?>
            <div class="activity-item">
                <div class="activity-icon <?php echo $attempt['status']; ?>">
                    <i class="fas <?php echo $attempt['status'] == 'completed' ? 'fa-check-circle' : 'fa-spinner fa-spin'; ?>"></i>
                </div>
                <div class="activity-body">
                    <h4 class="activity-exam-title"><?php echo htmlspecialchars($attempt['exam_title']); ?></h4>
                    <span class="activity-time"><?php echo date('M d, Y • H:i', strtotime($attempt['started_at'])); ?></span>
                </div>
                <div class="activity-right">
                    <?php if($attempt['status'] == 'completed'): ?>
                        <div class="activity-score">Score: <strong><?php echo number_format($attempt['score'], 1); ?></strong></div>
                        <a href="<?php echo app_base_url('quiz/result/' . $attempt['id']); ?>" class="btn-activity-action">Result</a>
                    <?php else: ?>
                        <div class="activity-status-label">In Progress</div>
                        <a href="<?php echo app_base_url('quiz/room/' . $attempt['id']); ?>" class="btn-activity-action resume">Resume</a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- Mobile App Promotion -->
    <section class="app-promotion">
        <div class="promo-card">
            <div class="promo-content">
                <h2 class="promo-title">Learn on the Go</h2>
                <p class="promo-desc">Download the <strong>Bishwo Calculator</strong> mobile app for offline mock tests, daily engineering news, and instant rank notifications.</p>
                <div class="promo-badges">
                    <a href="#" class="store-badge"><img src="https://upload.wikimedia.org/wikipedia/commons/7/78/Google_Play_Store_badge_EN.svg" alt="Google Play"></a>
                    <a href="#" class="store-badge"><img src="https://upload.wikimedia.org/wikipedia/commons/3/3c/Download_on_the_App_Store_Badge.svg" alt="App Store"></a>
                </div>
            </div>
            <div class="promo-image">
                <!-- I'll use a placeholder icon/visual here if no real image -->
                <i class="fas fa-mobile-screen-button"></i>
            </div>
        </div>
    </section>
</div>

<style>
/* Portal Base Consistency */
:root {
    --portal-primary: #ffffff;
    --portal-accent: #6366f1;
    --portal-glass: rgba(255, 255, 255, 0.03);
    --portal-border: rgba(255, 255, 255, 0.08);
    --portal-radius: 20px;
    --portal-glow: 0 0 30px rgba(99, 102, 241, 0.2);
}

.portal-container {
    padding: 2rem 5%;
    max-width: 1400px;
    margin: 0 auto;
    color: #fff;
}

.gradient-text {
    background: linear-gradient(90deg, #6366f1, #a855f7);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    font-weight: 800;
}

/* Hero Section */
.portal-hero {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 4rem 0;
    gap: 4rem;
}

.hero-content {
    max-width: 600px;
}

.hero-title {
    font-size: 3.5rem;
    line-height: 1.1;
    font-weight: 800;
    margin-bottom: 1.5rem;
}

.hero-subtitle {
    font-size: 1.2rem;
    color: #94a3b8;
    margin-bottom: 2.5rem;
    line-height: 1.6;
}

.hero-actions {
    display: flex;
    gap: 1.5rem;
}

.premium-btn {
    padding: 1rem 2rem;
    border-radius: 14px;
    font-weight: 700;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.75rem;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.hero-actions .btn-primary {
    background: #fff;
    color: #000;
}

.hero-actions .btn-primary:hover {
    transform: translateY(-4px);
    box-shadow: 0 15px 30px rgba(255, 255, 255, 0.1);
}

.hero-actions .btn-secondary {
    background: rgba(255, 255, 255, 0.05);
    color: #fff;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.hero-actions .btn-secondary:hover {
    background: rgba(255, 255, 255, 0.1);
    border-color: #6366f1;
}

.hero-visual {
    position: relative;
    width: 400px;
    height: 400px;
}

.hero-glow {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 300px;
    height: 300px;
    background: radial-gradient(circle, rgba(99, 102, 241, 0.3) 0%, transparent 70%);
    filter: blur(50px);
    z-index: -1;
}

.floating-icons {
    width: 100%;
    height: 100%;
    position: relative;
}

.floating-icons i {
    position: absolute;
    font-size: 4rem;
    opacity: 0.6;
    animation: floating 6s infinite ease-in-out;
}

.icon-1 { top: 10%; left: 20%; color: #6366f1; animation-delay: 0s; }
.icon-2 { top: 60%; right: 10%; color: #f59e0b; animation-delay: 2s; }
.icon-3 { bottom: 10%; left: 30%; color: #ef4444; animation-delay: 4s; }

@keyframes floating {
    0%, 100% { transform: translateY(0) rotate(0); }
    50% { transform: translateY(-30px) rotate(10deg); }
}

/* Sections General */
.section-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    margin-bottom: 2.5rem;
}

.section-title {
    font-size: 2.5rem;
    font-weight: 800;
    margin-bottom: 0.5rem;
}

.section-subtitle {
    color: #64748b;
    font-size: 1.1rem;
}

.view-all-link {
    color: #6366f1;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.2s;
}

.view-all-link:hover {
    color: #fff;
    gap: 0.5rem;
}

/* Streams Grid */
.streams-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 2rem;
    margin-bottom: 5rem;
}

.stream-card {
    background: var(--portal-glass);
    border: 1px solid var(--portal-border);
    border-radius: var(--portal-radius);
    padding: 2.5rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    text-decoration: none;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.stream-card:hover {
    transform: translateY(-10px);
    background: rgba(255, 255, 255, 0.05);
    border-color: rgba(255, 255, 255, 0.2);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
}

.stream-icon-box {
    width: 80px;
    height: 80px;
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.5rem;
    margin-bottom: 1.5rem;
    color: #fff;
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
}

.stream-name {
    font-size: 1.4rem;
    font-weight: 700;
    color: #fff;
    margin-bottom: 1rem;
}

.stream-hover-arrow {
    opacity: 0;
    transform: translateX(-10px);
    transition: all 0.3s;
    color: #6366f1;
    font-size: 1.2rem;
}

.stream-card:hover .stream-hover-arrow {
    opacity: 1;
    transform: translateX(0);
}

/* Exams Grid */
.exams-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 2rem;
    margin-bottom: 5rem;
}

.exam-card {
    background: var(--portal-glass);
    border: 1px solid var(--portal-border);
    border-radius: var(--portal-radius);
    padding: 2rem;
    position: relative;
    display: flex;
    flex-direction: column;
    transition: all 0.3s;
}

.exam-card:hover {
    background: rgba(255, 255, 255, 0.05);
    border-color: #6366f1;
}

.exam-card-badge {
    position: absolute;
    top: 1.5rem;
    right: 1.5rem;
    padding: 0.4rem 1rem;
    border-radius: 50px;
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
}

.exam-card-badge.practice_set { background: rgba(16, 185, 129, 0.1); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.3); }
.exam-card-badge.mock_test { background: rgba(239, 68, 68, 0.1); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.3); }

.exam-premium-icon {
    position: absolute;
    top: 1.5rem;
    left: 1.5rem;
    color: #f59e0b;
    font-size: 1.2rem;
}

.exam-info {
    margin-top: 1rem;
}

.exam-title {
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 1rem;
    line-height: 1.2;
}

.exam-desc {
    color: #94a3b8;
    font-size: 0.95rem;
    margin-bottom: 1.5rem;
    min-height: 3rem;
}

.exam-meta {
    display: flex;
    padding-top: 1rem;
    gap: 1.5rem;
    border-top: 1px solid var(--portal-border);
}

.meta-item {
    font-size: 0.85rem;
    color: #64748b;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.meta-item i { color: #6366f1; }

.exam-footer {
    margin-top: 2rem;
}

.btn-start-exam {
    width: 100%;
    padding: 1rem;
    background: transparent;
    border: 1px solid #6366f1;
    color: #6366f1;
    border-radius: 12px;
    font-weight: 700;
    text-decoration: none;
    text-align: center;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
    transition: all 0.3s;
}

.btn-start-exam:hover {
    background: #6366f1;
    color: #fff;
    box-shadow: 0 10px 20px rgba(99, 102, 241, 0.3);
}

/* Activity List */
.activity-list-glass {
    background: var(--portal-glass);
    border: 1px solid var(--portal-border);
    backdrop-filter: blur(10px);
    border-radius: var(--portal-radius);
    overflow: hidden;
    margin-bottom: 5rem;
}

.activity-item {
    padding: 1.5rem 2rem;
    display: flex;
    align-items: center;
    gap: 1.5rem;
    border-bottom: 1px solid var(--portal-border);
    transition: background 0.2s;
}

.activity-item:last-child { border-bottom: none; }
.activity-item:hover { background: rgba(255, 255, 255, 0.02); }

.activity-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

.activity-icon.completed { background: rgba(16, 185, 129, 0.1); color: #10b981; }
.activity-icon.in_progress { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }

.activity-body { flex: 1; }
.activity-exam-title { font-size: 1.1rem; font-weight: 600; margin-bottom: 0.25rem; }
.activity-time { font-size: 0.85rem; color: #64748b; }

.activity-right { display: flex; align-items: center; gap: 2rem; }
.activity-score { font-size: 0.9rem; color: #94a3b8; }
.activity-score strong { color: #fff; font-size: 1.2rem; margin-left: 0.5rem; }
.activity-status-label { font-size: 0.85rem; color: #f59e0b; font-weight: 600; }

.btn-activity-action {
    padding: 0.6rem 1.2rem;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid var(--portal-border);
    color: #fff;
    border-radius: 8px;
    text-decoration: none;
    font-size: 0.85rem;
    font-weight: 600;
    transition: all 0.2s;
}

.btn-activity-action:hover {
    background: #6366f1;
    border-color: #6366f1;
}

.btn-activity-action.resume {
    background: #6366f1;
    border-color: #6366f1;
}

/* App Promotion */
.app-promotion {
    margin: 5rem 0;
}

.promo-card {
    background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%);
    border-radius: 24px;
    padding: 4rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    overflow: hidden;
    position: relative;
}

.promo-content {
    max-width: 500px;
}

.promo-title {
    font-size: 3rem;
    font-weight: 800;
    margin-bottom: 1.5rem;
}

.promo-desc {
    font-size: 1.2rem;
    color: #cbd5e1;
    margin-bottom: 2.5rem;
}

.promo-badges {
    display: flex;
    gap: 1rem;
}

.store-badge img {
    height: 48px;
    transition: transform 0.2s;
}

.store-badge:hover img {
    transform: scale(1.05);
}

.promo-image {
    font-size: 12rem;
    color: rgba(99, 102, 241, 0.2);
    position: absolute;
    right: -2rem;
    bottom: -2rem;
    transform: rotate(-15deg);
}

/* Responsive Cleanup */
@media (max-width: 992px) {
    .portal-hero { flex-direction: column; text-align: center; }
    .hero-content { margin: 0 auto; }
    .hero-actions { justify-content: center; }
    .hero-visual { display: none; }
    .promo-card { flex-direction: column; text-align: center; padding: 3rem; }
    .promo-content { margin-bottom: 2rem; }
    .promo-badges { justify-content: center; }
    .promo-image { display: none; }
}

@media (max-width: 600px) {
    .hero-title { font-size: 2.5rem; }
    .streams-grid { grid-template-columns: 1fr; }
    .exams-grid { grid-template-columns: 1fr; }
    .activity-item { flex-direction: column; align-items: flex-start; text-align: left; }
    .activity-right { width: 100%; justify-content: space-between; margin-top: 1rem; }
}

/* Empty State Styling */
.empty-state {
    text-align: center;
    padding: 5rem;
    background: var(--portal-glass);
    border-radius: var(--portal-radius);
    border: 1px dashed var(--portal-border);
}

.empty-state i {
    font-size: 4rem;
    color: #64748b;
    margin-bottom: 2rem;
}

.empty-state p {
    color: #94a3b8;
    font-size: 1.2rem;
}

</style>
