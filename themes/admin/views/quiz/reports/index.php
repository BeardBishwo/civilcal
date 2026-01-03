<div class="content-wrapper p-4">

    <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px; overflow: hidden;">
        <div class="card-body p-4 text-white" style="background: linear-gradient(135deg, #e74a3b 0%, #c0392b 100%);"> <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="fw-bold mb-1 text-white">
                        <i class="fas fa-bug me-2"></i> Issue Reports
                    </h2>
                    <p class="mb-0 text-white-50">Students reported errors in these questions. Review required.</p>
                </div>
                <div class="bg-white bg-opacity-25 rounded-pill px-4 py-2">
                    <span class="fw-bold text-white"><?= $count ?> Pending</span>
                </div>
            </div>
        </div>
    </div>

    <?php if($count == 0): ?>
    <div class="text-center p-5 bg-white shadow-sm rounded-3">
        <div class="avatar avatar-xl bg-success bg-opacity-10 text-success rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width:80px; height:80px;">
            <i class="fas fa-check fa-3x"></i>
        </div>
        <h4 class="fw-bold text-gray-800">All Clear!</h4>
        <p class="text-muted">No pending issue reports. Your content is clean.</p>
    </div>
    <?php else: ?>

    <div class="row">
        <?php foreach($reports as $report): ?>
        <div class="col-12 mb-4" id="report-card-<?= $report->id ?>">
            <div class="card border-0 shadow-sm" style="border-radius: 15px; border-left: 5px solid #e74a3b;">
                <div class="card-body p-4">
                    <div class="row">
                        
                        <div class="col-md-4 border-end">
                            <div class="d-flex align-items-center mb-3">
                                <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3 me-2">
                                    <?= strtoupper($report->issue_type) ?> </span>
                                <small class="text-muted">Reported by <strong><?= $report->user->name ?></strong></small>
                            </div>
                            <div class="p-3 bg-light rounded text-dark fst-italic border">
                                <i class="fas fa-quote-left text-muted me-2"></i>
                                <?= $report->description ?>
                            </div>
                            <!-- Helper for time elapsed if available, else standard date -->
                            <small class="text-muted mt-2 d-block">
                                <i class="far fa-clock me-1"></i> <?= $report->created_at ?>
                            </small>
                        </div>

                        <div class="col-md-6 px-4">
                            <h6 class="fw-bold text-primary text-uppercase small">Affected Question (#<?= $report->question_id ?>)</h6>
                            <p class="fw-bold text-dark mb-2"><?= strip_tags($report->question->question) ?></p>
                            
                            <?php $opts = json_decode($report->question->options, true); ?>
                            <ul class="list-unstyled small text-muted">
                                <li><i class="far fa-circle"></i> A: <?= $opts['option_1'] ?? '' ?></li>
                                <li><i class="far fa-circle"></i> B: <?= $opts['option_2'] ?? '' ?></li>
                                <li class="text-success fw-bold"><i class="fas fa-check-circle"></i> Correct: Option <?= $report->question->correct_answer ?></li>
                            </ul>
                        </div>

                        <div class="col-md-2 d-flex flex-column justify-content-center gap-2">
                            <a href="/admin/quiz/question/edit/<?= $report->question_id ?>" target="_blank" class="btn btn-outline-primary fw-bold rounded-pill w-100">
                                <i class="fas fa-external-link-alt me-1"></i> Edit / Fix
                            </a>
                            
                            <button onclick="resolveReport(<?= $report->id ?>)" class="btn btn-success fw-bold rounded-pill w-100 shadow-sm">
                                <i class="fas fa-check me-1"></i> Fixed
                            </button>

                            <button onclick="ignoreReport(<?= $report->id ?>)" class="btn btn-light text-muted rounded-pill w-100">
                                Dismiss
                            </button>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

</div>

<script>
    function resolveReport(id) {
        if(!confirm("Did you fix the question? This will archive the report.")) return;
        
        const params = new URLSearchParams();
        params.append('id', id);

        fetch('/admin/quiz/report/resolve', {
            method: 'POST',
            body: params
        }).then(() => {
            // Animate removal
            const card = document.getElementById('report-card-' + id);
            card.style.transition = "all 0.5s";
            card.style.opacity = "0";
            card.style.transform = "translateX(50px)";
            setTimeout(() => card.remove(), 500);
        });
    }

    function ignoreReport(id) {
        if(!confirm("Ignore this report?")) return;
        const params = new URLSearchParams();
        params.append('id', id);

        fetch('/admin/quiz/report/ignore', { method: 'POST', body: params })
        .then(() => document.getElementById('report-card-' + id).remove());
    }
</script>
