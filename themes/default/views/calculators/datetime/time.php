<?php include_once __DIR__ . '/../../../../partials/calculator_sidebar.php'; ?>

<main class="main-content">
    <div class="row g-4 mb-4">
        <div class="col-md-8 mx-auto">
            <div class="glass-card">
                <div class="d-flex align-items-center mb-4">
                    <div class="icon-square bg-primary-gradient text-white me-3">
                        <i class="bi bi-clock-history fs-4"></i>
                    </div>
                    <h2 class="mb-0 fw-bold position-relative z-1"><?php echo htmlspecialchars($title); ?></h2>
                </div>

                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label text-secondary small text-uppercase fw-bold ls-1">Start Time</label>
                        <input type="time" class="form-control form-control-lg bg-dark text-white border-glass" id="start_time" value="09:00">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-secondary small text-uppercase fw-bold ls-1">End Time</label>
                        <input type="time" class="form-control form-control-lg bg-dark text-white border-glass" id="end_time" value="17:00">
                    </div>
                    
                    <div class="col-12 mt-4">
                        <button class="btn btn-primary w-100 py-3 fw-bold rounded-pill shadow-primary" onclick="calculateTimeDifference()">
                            Calculate Difference
                        </button>
                    </div>
                </div>

                <div id="result-section" class="mt-4" style="display: none;">
                    <div class="border-glass rounded-4 p-4 text-center bg-white-5">
                         <h5 class="text-secondary mb-3">Duration</h5>
                         <div class="display-4 fw-bold text-white mb-2" id="result-time"></div>
                         <div class="row mt-4 pt-3 border-top border-secondary">
                             <div class="col-6">
                                 <div class="h4 text-info" id="total-hours"></div>
                                 <div class="small text-muted">Hours</div>
                             </div>
                             <div class="col-6">
                                 <div class="h4 text-success" id="total-minutes"></div>
                                 <div class="small text-muted">Minutes</div>
                             </div>
                         </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
function calculateTimeDifference() {
    const start = document.getElementById('start_time').value;
    const end = document.getElementById('end_time').value;
    
    if (!start || !end) return;
    
    const startDate = new Date(`2000-01-01T${start}`);
    const endDate = new Date(`2000-01-01T${end}`);
    
    // Handle overnight (if end is before start, assume next day)
    if (endDate < startDate) {
        endDate.setDate(endDate.getDate() + 1);
    }
    
    const diffMs = endDate - startDate;
    const diffHrs = Math.floor(diffMs / 3600000);
    const diffMins = Math.floor((diffMs % 3600000) / 60000);
    
    const totalMinutes = Math.floor(diffMs / 60000);
    const totalHours = (diffMs / 3600000).toFixed(2);
    
    document.getElementById('result-time').textContent = `${diffHrs}h ${diffMins}m`;
    document.getElementById('total-hours').textContent = totalHours;
    document.getElementById('total-minutes').textContent = totalMinutes;
    
    document.getElementById('result-section').style.display = 'block';
}
</script>
