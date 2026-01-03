<div class="quiz-portal-wrapper py-5">
    <div class="container">
        <!-- Modern Hero Section -->
        <div class="hero-section mb-5 position-relative overflow-hidden rounded-4 p-5 bg-primary text-white shadow-lg">
            <div class="hero-bg-accent"></div>
            <div class="row align-items-center position-relative">
                <div class="col-lg-7">
                    <span class="badge bg-white text-primary px-3 py-2 rounded-pill mb-3 fw-bold">PRO LEARNING</span>
                    <h1 class="display-4 fw-bold mb-3">Master Your Engineering Exams</h1>
                    <p class="lead opacity-75 mb-4">The ultimate preparation platform for Loksewa, Engineering License, and Technical Entrance exams. Enterprise-grade practice in a gamified environment.</p>
                    <div class="d-flex gap-3">
                        <a href="#popular-exams" class="btn btn-light btn-lg px-4 fw-bold rounded-3">Get Started</a>
                        <a href="#" class="btn btn-outline-light btn-lg px-4 fw-bold rounded-3">How it Works</a>
                    </div>
                </div>
                <div class="col-lg-5 d-none d-lg-block">
                    <div class="hero-illustration text-center">
                        <i class="fas fa-microchip fa-10x opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats HUD -->
        <div class="row mb-5">
            <div class="col-md-3">
                <div class="stat-glass-card rounded-3 p-3 text-center transition-hover">
                    <div class="h3 fw-bold text-primary mb-1"><?php echo count($exams); ?>+</div>
                    <div class="small text-muted text-uppercase">Active Exams</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-glass-card rounded-3 p-3 text-center transition-hover">
                    <div class="h3 fw-bold text-success mb-1">5k+</div>
                    <div class="small text-muted text-uppercase">Questions</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-glass-card rounded-3 p-3 text-center transition-hover">
                    <div class="h3 fw-bold text-warning mb-1">10k+</div>
                    <div class="small text-muted text-uppercase">Mock Taken</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-glass-card rounded-3 p-3 text-center transition-hover">
                    <div class="h3 fw-bold text-info mb-1">98%</div>
                    <div class="small text-muted text-uppercase">Success Rate</div>
                </div>
            </div>
        </div>

        <!-- Categories / Streams -->
        <?php if (!empty($categories)): ?>
        <div class="mb-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bold m-0 section-title">Choose Your Stream</h3>
                <div class="section-line flex-grow-1 mx-4 d-none d-md-block"></div>
            </div>
            <div class="row g-4">
                <?php foreach ($categories as $cat): ?>
                <div class="col-md-3">
                    <div class="stream-card p-4 text-center rounded-4 border-0 shadow-sm transition-hover h-100">
                        <div class="stream-icon mb-3 bg-light-subtle rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                            <i class="fas fa-university fa-2x text-primary"></i>
                        </div>
                        <h5 class="fw-bold mb-0"><?php echo htmlspecialchars($cat['name']); ?></h5>
                        <p class="small text-muted mt-2 mb-0">Prepare for various technical levels</p>
                        <a href="<?php echo app_base_url('quiz?category=' . $cat['slug']); ?>" class="stretched-link"></a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Featured Exams -->
        <div id="popular-exams" class="mb-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bold m-0 section-title">Popular Mock Tests</h3>
                <div class="section-line flex-grow-1 mx-4 d-none d-md-block"></div>
            </div>
            
            <?php if (empty($exams)): ?>
                <div class="glass-alert p-4 rounded-3 text-center">
                    <i class="fas fa-info-circle fa-2x mb-2 text-info"></i>
                    <p class="mb-0">No exams available at the moment. Check back soon!</p>
                </div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($exams as $exam): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="exam-glass-card h-100 p-4 border-0 rounded-4 shadow-sm transition-hover d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="badge <?php echo $exam['type'] == 'mock_test' ? 'bg-danger-subtle text-danger' : 'bg-success-subtle text-success'; ?> px-3 py-2 rounded-pill">
                                    <?php echo strtoupper(str_replace('_', ' ', $exam['type'])); ?>
                                </span>
                                <?php if($exam['is_premium']): ?>
                                    <span class="premium-badge"><i class="fas fa-crown text-warning"></i></span>
                                <?php endif; ?>
                            </div>
                            
                            <h5 class="fw-bold text-dark mb-2">
                                <?php echo htmlspecialchars($exam['title']); ?>
                            </h5>
                            <p class="small text-muted flex-grow-1 mb-4">
                                <?php echo htmlspecialchars(substr($exam['description'], 0, 100)) . '...'; ?>
                            </p>

                            <div class="exam-footer border-top pt-3 mt-auto">
                                <div class="row g-0 text-center mb-3">
                                    <div class="col-4 border-end">
                                        <div class="small fw-bold text-dark"><?php echo $exam['duration_minutes'] > 0 ? $exam['duration_minutes'] : '∞'; ?></div>
                                        <div class="x-small text-muted">MINS</div>
                                    </div>
                                    <div class="col-4 border-end">
                                        <div class="small fw-bold text-dark"><?php echo $exam['question_count']; ?></div>
                                        <div class="x-small text-muted">QUES</div>
                                    </div>
                                    <div class="col-4">
                                        <div class="small fw-bold text-dark"><?php echo $exam['total_marks']; ?></div>
                                        <div class="x-small text-muted">MARKS</div>
                                    </div>
                                </div>
                                <a href="<?php echo app_base_url('quiz/overview/' . $exam['slug']); ?>" class="btn btn-primary w-100 rounded-3 py-2 fw-bold shadow-sm">
                                    START EXAM <i class="fas fa-bolt ms-2"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Recent Activity -->
        <?php if (!empty($recentAttempts)): ?>
        <div class="mb-5">
            <h3 class="fw-bold mb-4">Your Recent Performance</h3>
            <div class="activity-glass-list rounded-4 p-2 shadow-sm">
                <?php foreach ($recentAttempts as $attempt): ?>
                <div class="activity-item d-flex justify-content-between align-items-center p-3 rounded-3 mb-2 transition-hover">
                    <div class="d-flex align-items-center">
                        <div class="activity-status-icon me-3 rounded-circle bg-light d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <?php if($attempt['status'] == 'completed'): ?>
                                <i class="fas fa-check-circle text-success fs-4"></i>
                            <?php else: ?>
                                <i class="fas fa-spinner fa-spin text-warning fs-4"></i>
                            <?php endif; ?>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0"><?php echo htmlspecialchars($attempt['exam_title']); ?></h6>
                            <small class="text-muted">Analyzed <?php echo date('M d, H:i', strtotime($attempt['started_at'])); ?></small>
                        </div>
                    </div>
                    <div class="text-end">
                        <?php if($attempt['status'] == 'completed'): ?>
                            <div class="badge bg-success px-3 py-2 rounded-pill me-2">SCORE: <?php echo number_format($attempt['score'], 1); ?>%</div>
                            <a href="<?php echo app_base_url('quiz/result/' . $attempt['id']); ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3">Review</a>
                        <?php else: ?>
                            <div class="badge bg-warning text-dark px-3 py-2 rounded-pill me-2">IN PROGRESS</div>
                            <a href="<?php echo app_base_url('quiz/room/' . $attempt['id']); ?>" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm">Resume Test</a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

    </div>
</div>

<style>
    .section-line { height: 1px; background: linear-gradient(to right, #dee2e6, transparent); }
    .hero-bg-accent { position: absolute; top: -50%; right: -10%; width: 60%; height: 200%; background: rgba(255,255,255,0.05); transform: rotate(15deg); }
    .transition-hover { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
    .transition-hover:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important; }
    
    .stat-glass-card { background: #fff; border: 1px solid rgba(0,0,0,0.05); box-shadow: 0 4px 6px rgba(0,0,0,0.02); }
    .stream-card { background: #fff; border: 1px solid rgba(0,0,0,0.05); }
    .exam-glass-card { background: #fff; border: 1px solid rgba(0,0,0,0.05); }
    .activity-glass-list { background: rgba(255,255,255,0.7); backdrop-filter: blur(10px); border: 1px solid rgba(0,0,0,0.05); }
    .activity-item { background: #fff; border: 1px solid transparent; }
    .activity-item:hover { border-color: var(--bs-primary); }
    
    .bg-danger-subtle { background-color: #fce8e8 !important; }
    .bg-success-subtle { background-color: #e8f5e9 !important; }
    .x-small { font-size: 0.7rem; letter-spacing: 0.5px; margin-top: 2px; }
    
    @media (max-width: 768px) {
        .hero-section { padding: 2rem !important; text-align: center; }
        .hero-section .d-flex { justify-content: center; }
    }
</style>
