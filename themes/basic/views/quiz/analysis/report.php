<div class="result-analysis-wrapper py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <!-- Premium Header Card -->
                <div class="hero-stats-card rounded-5 overflow-hidden shadow-lg border-0 mb-5 position-relative">
                    <div class="hero-stats-overlay"></div>
                    <div class="card-body p-5 text-center position-relative text-white">
                        <span class="badge bg-white text-primary px-3 py-2 rounded-pill mb-3 fw-bold">PERFORMANCE REPORT</span>
                        <h1 class="display-4 fw-bold mb-2"><?php echo htmlspecialchars($attempt['title']); ?></h1>
                        <p class="lead opacity-75 mb-4">Assessment completed on <?php echo date('F j, Y, g:i a', strtotime($attempt['completed_at'])); ?></p>

                        <div class="score-circle-wrapper mb-4">
                            <div class="main-score-value display-1 fw-bold">
                                <?php echo number_format($attempt['score'], 1); ?>
                                <span class="fs-4 opacity-50">/ <?php echo $attempt['total_marks']; ?></span>
                            </div>
                        </div>

                        <?php 
                            $percentage = ($attempt['total_marks'] > 0) ? ($attempt['score'] / $attempt['total_marks']) * 100 : 0;
                            $passed = $percentage >= 40;
                        ?>
                        <div class="result-status-pill d-inline-flex align-items-center bg-white rounded-pill px-4 py-2 shadow-sm">
                            <i class="fas <?php echo $passed ? 'fa-check-circle text-success' : 'fa-exclamation-circle text-danger'; ?> fs-4 me-2"></i>
                            <span class="fw-bold fs-5 <?php echo $passed ? 'text-success' : 'text-danger'; ?>">
                                <?php echo $passed ? 'QUALIFIED / PASSED' : 'NEEDS IMPROVEMENT'; ?>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Stats HUD -->
                <div class="row g-4 mb-5">
                    <div class="col-md-4">
                        <div class="stat-glass-card rounded-4 p-4 text-center border-0 shadow-sm h-100 transition-hover">
                            <div class="icon-circle bg-info-subtle text-info mb-3 mx-auto">
                                <i class="fas fa-bullseye fs-4"></i>
                            </div>
                            <h6 class="text-uppercase fw-bold text-muted small letter-spacing-1">Accuracy</h6>
                            <h2 class="fw-bold text-dark mb-0"><?php echo number_format($percentage, 1); ?>%</h2>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-glass-card rounded-4 p-4 text-center border-0 shadow-sm h-100 transition-hover">
                            <div class="icon-circle bg-warning-subtle text-warning mb-3 mx-auto">
                                <i class="fas fa-stopwatch fs-4"></i>
                            </div>
                            <h6 class="text-uppercase fw-bold text-muted small letter-spacing-1">Time Invested</h6>
                            <h2 class="fw-bold text-dark mb-0">
                                <?php 
                                    $start = strtotime($attempt['started_at']);
                                    $end = strtotime($attempt['completed_at']);
                                    $diff = $end - $start;
                                    echo floor($diff / 60) . 'm ' . ($diff % 60) . 's';
                                ?>
                            </h2>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-glass-card rounded-4 p-4 text-center border-0 shadow-sm h-100 transition-hover">
                            <div class="icon-circle bg-success-subtle text-success mb-3 mx-auto">
                                <i class="fas fa-chart-line fs-4"></i>
                            </div>
                            <h6 class="text-uppercase fw-bold text-muted small letter-spacing-1">Competency</h6>
                            <h2 class="fw-bold text-dark mb-0"><?php echo $percentage > 70 ? 'Expert' : ($percentage > 40 ? 'Proficient' : 'Learning'); ?></h2>
                        </div>
                    </div>
                </div>

                <!-- Areas for Improvement -->
                <?php if (!empty($incorrect_answers)): ?>
                <div class="learning-path-section mb-5">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h3 class="fw-bold m-0"><i class="fas fa-graduation-cap text-primary me-2"></i> Learning Path</h3>
                        <div class="section-divider flex-grow-1 mx-4 d-none d-md-block"></div>
                    </div>
                    
                    <div class="row g-3">
                        <?php foreach ($incorrect_answers as $ans): ?>
                        <div class="col-12">
                            <div class="improvement-card pointer-hover rounded-4 p-4 d-flex align-items-center shadow-sm border-0 transition-hover bg-white">
                                <div class="topic-icon me-4 rounded-circle bg-danger-subtle text-danger d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; min-width: 60px;">
                                    <i class="fas fa-times fs-4"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="fw-bold text-dark mb-1"><?= strip_tags($ans['content']['text']) ?></h6>
                                    <p class="small text-muted mb-0"><?= $ans['explanation'] ?: 'Review the fundamentals of this topic in the syllabus module.' ?></p>
                                </div>
                                <?php if (!empty($ans['related_tool_link'])): ?>
                                <a href="<?= $ans['related_tool_link'] ?>" target="_blank" class="btn btn-sm btn-outline-primary px-3 rounded-pill fw-bold">
                                    OPEN TOOL <i class="fas fa-external-link-alt ms-1" style="font-size: 0.7rem;"></i>
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Final Actions -->
                <div class="actions-card rounded-5 p-5 bg-light-subtle text-center border shadow-sm">
                    <h4 class="fw-bold mb-3">Ready to push your limits further?</h4>
                    <p class="text-muted mb-4 mx-auto" style="max-width: 600px;">Continuous practice is the key to engineering mastery. Review your performance data and try again to unlock higher ranks in the city.</p>
                    
                    <div class="d-flex justify-content-center gap-3">
                        <a href="<?php echo app_base_url('quiz'); ?>" class="btn btn-outline-dark btn-lg px-5 rounded-3 fw-bold">
                            <i class="fas fa-home me-2"></i> PORTAL HUB
                        </a>
                        <a href="<?php echo app_base_url('quiz/start/' . $attempt['exam_id']); ?>" class="btn btn-primary btn-lg px-5 rounded-3 fw-bold shadow-sm">
                            RETAKE EXAM <i class="fas fa-redo ms-2"></i>
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<style>
    .hero-stats-card { background: linear-gradient(135deg, #4361ee 0%, #4cc9f0 100%); }
    .hero-stats-overlay { position: absolute; top:0; left:0; width:100%; height:100%; background: url('https://www.transparenttextures.com/patterns/cubes.png'); opacity: 0.1; }
    
    .stat-glass-card { background: #fff; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
    .stat-glass-card:hover { transform: translateY(-10px); }
    
    .icon-circle { width: 60px; height: 60px; border-radius: 20px; display: flex; align-items: center; justify-content: center; }
    
    .improvement-card { border: 1px solid rgba(0,0,0,0.05); }
    .improvement-card.pointer-hover:hover { border-color: #4361ee; background: rgba(67, 97, 238, 0.02); }
    
    .section-divider { height: 1px; background: linear-gradient(to right, #dee2e6, transparent); }
    
    .letter-spacing-1 { letter-spacing: 1px; }
    .transition-hover { transition: all 0.3s; }
    
    @media (max-width: 768px) {
        .hero-stats-card { border-radius: 2rem !important; }
        .hero-stats-card .display-1 { font-size: 4rem; }
    }
</style>
