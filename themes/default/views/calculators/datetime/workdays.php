<?php include_once __DIR__ . '/../../../../partials/calculator_sidebar.php'; ?>

<main class="main-content">
    <div class="row g-4 mb-4">
        <div class="col-md-8 mx-auto">
            <div class="glass-card">
                <div class="d-flex align-items-center mb-4">
                    <div class="icon-square bg-primary-gradient text-white me-3">
                        <i class="bi bi-briefcase fs-4"></i>
                    </div>
                    <h2 class="mb-0 fw-bold position-relative z-1"><?php echo htmlspecialchars($title); ?></h2>
                </div>

                <div class="alert alert-info border-glass text-white bg-white-5">
                    <i class="bi bi-info-circle me-2"></i> This calculator excludes weekends (Saturday/Sunday) from the days count.
                </div>

                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label text-secondary small text-uppercase fw-bold ls-1">Start Date</label>
                        <input type="date" class="form-control form-control-lg bg-dark text-white border-glass" id="start_date">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-secondary small text-uppercase fw-bold ls-1">End Date</label>
                        <input type="date" class="form-control form-control-lg bg-dark text-white border-glass" id="end_date">
                    </div>
                    
                    <div class="col-12 mt-4">
                        <button class="btn btn-primary w-100 py-3 fw-bold rounded-pill shadow-primary" onclick="calculateWorkDays()">
                            Calculate Work Days
                        </button>
                    </div>
                </div>

                <div id="result-section" class="mt-4" style="display: none;">
                    <div class="border-glass rounded-4 p-4 text-center bg-white-5">
                         <h5 class="text-secondary mb-3">Business Days</h5>
                         <div class="display-3 fw-bold text-white mb-2" id="result-days"></div>
                         <div class="text-muted">excludes weekends</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('start_date').value = today;
    document.getElementById('end_date').value = today;
});

function calculateWorkDays() {
    const start = new Date(document.getElementById('start_date').value);
    const end = new Date(document.getElementById('end_date').value);
    
    if (isNaN(start) || isNaN(end)) return;
    
    // Ensure start is before end
    if (start > end) {
        alert('End date must be after start date');
        return;
    }

    let count = 0;
    const curDate = new Date(start.getTime());
    while (curDate <= end) {
        const dayOfWeek = curDate.getDay();
        if(dayOfWeek !== 0 && dayOfWeek !== 6) count++;
        curDate.setDate(curDate.getDate() + 1);
    }
    
    document.getElementById('result-days').textContent = count;
    document.getElementById('result-section').style.display = 'block';
}
</script>
