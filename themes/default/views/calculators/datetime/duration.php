<?php include_once __DIR__ . '/../../../../partials/calculator_sidebar.php'; ?>

<main class="main-content">
    <div class="row g-4 mb-4">
        <div class="col-md-8 mx-auto">
            <div class="glass-card">
                <div class="d-flex align-items-center mb-4">
                    <div class="icon-square bg-primary-gradient text-white me-3">
                        <i class="bi bi-calendar-range fs-4"></i>
                    </div>
                    <h2 class="mb-0 fw-bold position-relative z-1"><?php echo htmlspecialchars($title); ?></h2>
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
                        <button class="btn btn-primary w-100 py-3 fw-bold rounded-pill shadow-primary" onclick="calculateDuration()">
                            Calculate Duration
                        </button>
                    </div>
                </div>

                <div id="result-section" class="mt-4" style="display: none;">
                    <div class="border-glass rounded-4 p-4 text-center bg-white-5">
                         <h5 class="text-secondary mb-3">Time Difference</h5>
                         <div class="display-4 fw-bold text-white mb-2" id="result-duration"></div>
                         <div class="text-muted" id="result-details"></div>
                         
                         <div class="row mt-4 pt-3 border-top border-secondary">
                             <div class="col-4">
                                 <div class="h4 text-primary" id="total-days"></div>
                                 <div class="small text-muted">Total Days</div>
                             </div>
                             <div class="col-4">
                                 <div class="h4 text-info" id="total-hours"></div>
                                 <div class="small text-muted">Total Hours</div>
                             </div>
                             <div class="col-4">
                                 <div class="h4 text-success" id="total-minutes"></div>
                                 <div class="small text-muted">Total Minutes</div>
                             </div>
                         </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Set default dates (today)
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('start_date').value = today;
    document.getElementById('end_date').value = today;
});

async function calculateDuration() {
    const start = document.getElementById('start_date').value;
    const end = document.getElementById('end_date').value;
    
    if (!start || !end) return;
    
    try {
        const response = await fetch('<?php echo app_base_url("/calculator/api/datetime/duration"); ?>', {
            method: 'POST',
            body: JSON.stringify({ start_date: start, end_date: end })
        });
        
        const data = await response.json();
        
        let durationText = '';
        if (data.years > 0) durationText += `${data.years} years, `;
        if (data.months > 0) durationText += `${data.months} months, `;
        durationText += `${data.days} days`;
        
        document.getElementById('result-duration').textContent = durationText;
        document.getElementById('total-days').textContent = data.total_days.toLocaleString();
        document.getElementById('total-hours').textContent = data.hours.toLocaleString();
        document.getElementById('total-minutes').textContent = data.minutes.toLocaleString();
        
        document.getElementById('result-section').style.display = 'block';
    } catch (e) {
        console.error(e);
    }
}
</script>
