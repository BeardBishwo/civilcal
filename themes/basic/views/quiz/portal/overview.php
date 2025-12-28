<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb bg-transparent p-0">
                    <li class="breadcrumb-item"><a href="<?php echo app_base_url('quiz'); ?>">Quiz Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($exam['title']); ?></li>
                </ol>
            </nav>

            <div class="card border-0 shadow-lg overflow-hidden">
                <div class="card-header bg-primary text-white p-4 position-relative overflow-hidden">
                    <div class="position-relative z-1">
                        <h2 class="font-weight-bold mb-2"><?php echo htmlspecialchars($exam['title']); ?></h2>
                        <span class="badge badge-light text-primary px-3 py-1 font-weight-bold shadow-sm">
                            <?php echo ucfirst(str_replace('_', ' ', $exam['type'])); ?>
                        </span>
                    </div>
                </div>
                <div class="card-body p-5">
                    
                    <div class="row mb-5 text-center">
                        <div class="col-4 border-right">
                            <h3 class="font-weight-bold text-primary mb-1"><?php echo $exam['duration_minutes'] > 0 ? $exam['duration_minutes'] : '∞'; ?></h3>
                            <small class="text-muted text-uppercase font-weight-bold">Minutes</small>
                        </div>
                        <div class="col-4 border-right">
                            <h3 class="font-weight-bold text-primary mb-1"><?php echo $question_count; ?></h3>
                            <small class="text-muted text-uppercase font-weight-bold">Questions</small>
                        </div>
                        <div class="col-4">
                            <h3 class="font-weight-bold text-primary mb-1"><?php echo $exam['total_marks']; ?></h3>
                            <small class="text-muted text-uppercase font-weight-bold">Total Marks</small>
                        </div>
                    </div>

                    <div class="mb-5">
                        <h5 class="font-weight-bold border-bottom pb-2 mb-3">Instructions</h5>
                        <ul class="text-muted" style="line-height: 1.8;">
                            <li><i class="fas fa-check-circle text-success mr-2"></i> This is a <strong><?php echo $exam['mode']; ?></strong> mode test.</li>
                            <?php if ($exam['negative_marking_rate'] > 0): ?>
                                <li><i class="fas fa-exclamation-circle text-danger mr-2"></i> <strong>Negative Marking:</strong> <?php echo $exam['negative_marking_rate']; ?> marks will be deducted for each wrong answer.</li>
                            <?php else: ?>
                                <li><i class="fas fa-check-circle text-success mr-2"></i> There is no negative marking for this test.</li>
                            <?php endif; ?>
                            <li><i class="fas fa-clock text-info mr-2"></i> The timer will start immediately after you click "Start Exam".</li>
                            <?php if ($exam['mode'] == 'exam'): ?>
                                <li><i class="fas fa-eye-slash text-warning mr-2"></i> Answers and explanations will be shown only after submission.</li>
                            <?php else: ?>
                                <li><i class="fas fa-eye text-success mr-2"></i> You can view answers instantly after attempting each question.</li>
                            <?php endif; ?>
                            <li><i class="fas fa-laptop text-primary mr-2"></i> Please ensure a stable internet connection.</li>
                        </ul>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="<?php echo app_base_url('quiz'); ?>" class="btn btn-outline-secondary px-4">Cancel</a>
                        
                        <?php if(!empty($exam['is_premium']) && false): // TODO: Check if purchased ?>
                            <a href="#" class="btn btn-success btn-lg px-5 shadow-sm">
                                <i class="fas fa-shopping-cart mr-2"></i> Buy Now (Rs. <?php echo $exam['price']; ?>)
                            </a>
                        <?php else: ?>
                            <a href="<?php echo app_base_url('quiz/start/' . $exam['slug']); ?>" class="btn btn-primary btn-lg px-5 shadow-lg transform-scale">
                                Start Exam <i class="fas fa-play ml-2"></i>
                            </a>
                        <?php endif; ?>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
