<div class="container py-5">
    <div class="row mb-5 align-items-center">
        <div class="col-lg-6">
            <h1 class="display-4 font-weight-bold text-primary">Master Your Exams</h1>
            <p class="lead text-muted">Join thousands of students preparing for Loksewa, Engineering License, and Entrance exams with our enterprise-grade quiz platform.</p>
        </div>
        <div class="col-lg-6 text-right">
             <!-- Ideally an illustration here -->
             <div class="d-inline-block p-4 rounded-circle bg-light shadow-sm">
                <i class="fas fa-graduation-cap fa-5x text-primary"></i>
             </div>
        </div>
    </div>

    <!-- Categories / Streams -->
    <?php if (!empty($categories)): ?>
    <div class="mb-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="font-weight-bold">Browse by Stream</h4>
            <!-- <a href="#" class="text-primary">View All</a> -->
        </div>
        <div class="row">
            <?php foreach ($categories as $cat): ?>
            <div class="col-md-3 mb-4">
                <div class="card border-0 shadow-sm hover-lift h-100">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="fas fa-layer-group fa-3x text-info opacity-50"></i>
                        </div>
                        <h5 class="font-weight-bold"><?php echo htmlspecialchars($cat['name']); ?></h5>
                        <a href="<?php echo app_base_url('quiz?category=' . $cat['slug']); ?>" class="stretched-link"></a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Featured Exams -->
    <div class="mb-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="font-weight-bold">Popular Exams & Mocks</h4>
            <!-- <a href="#" class="text-primary">View All</a> -->
        </div>
        
        <?php if (empty($exams)): ?>
            <div class="alert alert-info">No exams available at the moment. Check back soon!</div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($exams as $exam): ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card border-0 shadow-sm h-100 hover-shadow transition-3d-hover">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <span class="badge badge-<?php echo $exam['type'] == 'mock_test' ? 'danger' : 'success'; ?> badge-pill px-3 py-2">
                                    <?php echo ucfirst(str_replace('_', ' ', $exam['type'])); ?>
                                </span>
                                <?php if($exam['is_premium']): ?>
                                    <span class="text-warning" title="Premium Content"><i class="fas fa-crown"></i></span>
                                <?php endif; ?>
                            </div>
                            
                            <h5 class="card-title font-weight-bold mb-1">
                                <a href="<?php echo app_base_url('quiz/overview/' . $exam['slug']); ?>" class="text-dark text-decoration-none">
                                    <?php echo htmlspecialchars($exam['title']); ?>
                                </a>
                            </h5>
                            <p class="small text-muted mb-3">
                                <?php echo htmlspecialchars(substr($exam['description'], 0, 80)) . '...'; ?>
                            </p>

                            <div class="mt-auto">
                                <div class="d-flex justify-content-between text-muted small mb-3">
                                    <span><i class="fas fa-clock mr-1"></i> <?php echo $exam['duration_minutes'] > 0 ? $exam['duration_minutes'] . ' mins' : 'Unlimited'; ?></span>
                                    <span><i class="fas fa-question-circle mr-1"></i> <?php echo $exam['question_count']; ?> Qs</span>
                                    <span><i class="fas fa-star mr-1"></i> <?php echo $exam['total_marks']; ?> Marks</span>
                                </div>
                                
                                <a href="<?php echo app_base_url('quiz/overview/' . $exam['slug']); ?>" class="btn btn-outline-primary btn-block font-weight-bold">
                                    Start Now <i class="fas fa-arrow-right ml-2"></i>
                                </a>
                            </div>
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
        <h4 class="font-weight-bold mb-3">Your Recent Activity</h4>
        <div class="card shadow-sm border-0">
            <div class="list-group list-group-flush">
                <?php foreach ($recentAttempts as $attempt): ?>
                <div class="list-group-item d-flex justify-content-between align-items-center p-3">
                    <div>
                        <h6 class="mb-0 font-weight-bold"><?php echo htmlspecialchars($attempt['exam_title']); ?></h6>
                        <small class="text-muted">Started: <?php echo date('M d, H:i', strtotime($attempt['started_at'])); ?></small>
                    </div>
                    <div>
                        <?php if($attempt['status'] == 'completed'): ?>
                            <span class="badge badge-success px-3 py-2">Score: <?php echo number_format($attempt['score'], 1); ?></span>
                            <a href="<?php echo app_base_url('quiz/result/' . $attempt['id']); ?>" class="btn btn-sm btn-light ml-2">Result</a>
                        <?php else: ?>
                            <span class="badge badge-warning px-3 py-2">In Progress</span>
                            <a href="<?php echo app_base_url('quiz/room/' . $attempt['id']); ?>" class="btn btn-sm btn-primary ml-2">Resume</a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

</div>

<style>
    .hover-lift { transition: transform 0.2s; }
    .hover-lift:hover { transform: translateY(-5px); }
    .hover-shadow:hover { box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important; }
</style>
