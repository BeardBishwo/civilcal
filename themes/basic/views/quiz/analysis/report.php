<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Header Card -->
            <div class="card border-0 shadow-lg mb-4 overflow-hidden">
                <div class="card-body p-5 text-center bg-primary text-white position-relative">
                    <h2 class="font-weight-bold mb-1">Result Analysis</h2>
                    <p class="mb-4 opacity-75"><?php echo htmlspecialchars($attempt['title']); ?></p>

                    <div class="display-3 font-weight-bold mb-2">
                        <?php echo number_format($attempt['score'], 1); ?>
                        <span class="h4 text-white-50">/ <?php echo $attempt['total_marks']; ?></span>
                    </div>

                    <?php 
                        $percentage = ($attempt['total_marks'] > 0) ? ($attempt['score'] / $attempt['total_marks']) * 100 : 0;
                        $statusClass = $percentage >= 40 ? 'success' : 'danger';
                        $statusText = $percentage >= 40 ? 'PASSED' : 'NEEDS IMPROVEMENT';
                    ?>
                    <span class="badge badge-light text-<?php echo $statusClass; ?> px-4 py-2 font-weight-bold" style="font-size: 1.2rem;">
                        <?php echo $statusText; ?>
                    </span>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center">
                            <h6 class="text-uppercase text-muted font-weight-bold small">Accuracy</h6>
                            <h3 class="text-info font-weight-bold mb-0">
                                <?php echo number_format($percentage, 1); ?>%
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center">
                            <h6 class="text-uppercase text-muted font-weight-bold small">Time Taken</h6>
                            <h3 class="text-warning font-weight-bold mb-0">
                                <?php 
                                    $start = strtotime($attempt['started_at']);
                                    $end = strtotime($attempt['completed_at']);
                                    $diff = $end - $start;
                                    echo floor($diff / 60) . 'm ' . ($diff % 60) . 's';
                                ?>
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center">
                            <h6 class="text-uppercase text-muted font-weight-bold small">Status</h6>
                            <h3 class="text-success font-weight-bold mb-0">Completed</h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detailed Analysis -->
            <!-- Note: In a real implementation, we would loop through questions here. 
                 Since the controller implementation for fetching detailed QA is heavy, 
                 I'll keep this a summary view for now, with a placeholder for detailed breakdown. 
                 Ideally, ExamEngineController::result() should pass $questions with user answers.
            -->
            
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="font-weight-bold m-0 text-primary">Next Steps</h5>
                </div>
                <div class="card-body">
                    <p>Great job completing the assessment. Review your weak areas and try again to improve your score.</p>
                    
                    <div class="d-flex justify-content-between mt-4">
                        <a href="<?php echo app_base_url('quiz'); ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left mr-2"></i> Back to Hub
                        </a>
                        <a href="<?php echo app_base_url('quiz/start/' . $attempt['exam_id']); // Ideally slug ?>" class="btn btn-primary">
                            Retake Exam <i class="fas fa-redo ml-2"></i>
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
