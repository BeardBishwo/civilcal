<?php
// themes/admin/views/quiz/daily/index.php
?>
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4 align-items-center">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="bi bi-calendar-check-fill text-primary me-2"></i> Daily Quest Scheduler
            </h1>
            <p class="text-muted small mb-0">Monitor and manage the Auto-Pilot Quiz System.</p>
        </div>
        <div class="col-auto">
            <button class="btn btn-primary rounded-pill shadow-sm" onclick="generateQuizzes()">
                <i class="bi bi-lightning-charge-fill me-1"></i> Generate Next 7 Days
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm border-start border-4 border-primary h-100">
                <div class="card-body">
                    <div class="text-uppercase small fw-bold text-primary mb-1">Scheduled Days</div>
                    <div class="h3 fw-bold text-gray-800"><?= count($calendar) ?></div>
                    <div class="small text-muted">Upcoming unique quiz days</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm border-start border-4 border-success h-100">
                <div class="card-body">
                    <div class="text-uppercase small fw-bold text-success mb-1">Active Streaks</div>
                    <div class="h3 fw-bold text-gray-800">--</div>
                    <div class="small text-muted">Users engaging daily</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Calendar List -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 fw-bold text-primary">Upcoming Schedule</h6>
        </div>
        <div class="card-body p-0">
            <div class="list-group list-group-flush">
                <?php 
                $daysToShow = 14;
                for($i=0; $i<$daysToShow; $i++): 
                    $date = date('Y-m-d', strtotime("+$i days"));
                    $items = $calendar[$date] ?? [];
                ?>
                <div class="list-group-item p-3">
                    <div class="row align-items-center">
                        <div class="col-md-3 border-end">
                            <div class="h5 mb-0 fw-bold <?= $i==0 ? 'text-primary' : '' ?>">
                                <?= date('D, M j', strtotime($date)) ?>
                            </div>
                            <?php if($i==0): ?>
                                <span class="badge bg-primary-subtle text-primary small">Today</span>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-9">
                            <?php if(empty($items)): ?>
                                <div class="text-muted small fst-italic">
                                    <i class="bi bi-exclamation-circle me-1"></i> No quizzes scheduled. 
                                    <span class="text-info cursor-pointer" onclick="generateQuizzes()">Auto-generate?</span>
                                </div>
                            <?php else: ?>
                                <div class="d-flex flex-wrap gap-2">
                                    <?php foreach($items as $quiz): ?>
                                        <div class="badge bg-light text-dark border p-2 d-flex align-items-center">
                                            <span class="me-2">
                                                <?= $quiz['stream_title'] ? htmlspecialchars($quiz['stream_title']) : '🌍 General' ?>
                                            </span>
                                            <span class="badge bg-success rounded-pill" title="Questions">
                                                <?= count(json_decode($quiz['questions'], true)) ?> Qs
                                            </span>
                                            <span class="badge bg-warning text-dark ms-1 rounded-pill">
                                                <?= $quiz['reward_coins'] ?> 🪙
                                            </span>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endfor; ?>
            </div>
        </div>
    </div>
</div>

<script>
function generateQuizzes() {
    if(!confirm('This will auto-generate quizzes for the next 7 days based on available questions. Proceed?')) return;
    
    const btn = document.querySelector('button[onclick="generateQuizzes()"]');
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="spinner-border spinner-border-sm me-1"></i> Generating...';

    fetch('<?= app_base_url('admin/quiz/daily/generate') ?>', {
        method: 'POST'
    })
    .then(response => response.json())
    .then(data => {
        if(data.status === 'success') {
            alert('Generation Complete! The calendar will now refresh.');
            location.reload();
        } else {
            alert('Error: ' + data.message);
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    })
    .catch(err => {
        alert('Network Error');
        btn.disabled = false;
        btn.innerHTML = originalText;
    });
}
</script>
