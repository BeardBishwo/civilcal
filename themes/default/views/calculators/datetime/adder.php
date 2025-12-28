<?php include_once __DIR__ . '/../../../../partials/calculator_sidebar.php'; ?>

<main class="main-content">
    <div class="row g-4 mb-4">
        <div class="col-md-8 mx-auto">
            <div class="glass-card">
                <div class="d-flex align-items-center mb-4">
                    <div class="icon-square bg-primary-gradient text-white me-3">
                        <i class="bi bi-calendar-plus fs-4"></i>
                    </div>
                    <h2 class="mb-0 fw-bold position-relative z-1"><?php echo htmlspecialchars($title); ?></h2>
                </div>

                <div class="row g-4">
                    <div class="col-md-12">
                        <label class="form-label text-secondary small text-uppercase fw-bold ls-1">Start Date</label>
                        <input type="date" class="form-control form-control-lg bg-dark text-white border-glass" id="start_date">
                    </div>

                    <div class="col-12">
                        <div class="d-flex gap-2 justify-content-center my-2">
                             <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="operation" id="op_add" value="add" checked>
                                <label class="form-check-label text-white" for="op_add">Add</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="operation" id="op_sub" value="sub">
                                <label class="form-check-label text-white" for="op_sub">Subtract</label>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label text-secondary small text-uppercase fw-bold ls-1">Years</label>
                        <input type="number" class="form-control form-control-lg bg-dark text-white border-glass" id="years" value="0">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-secondary small text-uppercase fw-bold ls-1">Months</label>
                        <input type="number" class="form-control form-control-lg bg-dark text-white border-glass" id="months" value="0">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-secondary small text-uppercase fw-bold ls-1">Days</label>
                        <input type="number" class="form-control form-control-lg bg-dark text-white border-glass" id="days" value="0">
                    </div>
                    
                    <div class="col-12 mt-4">
                        <button class="btn btn-primary w-100 py-3 fw-bold rounded-pill shadow-primary" onclick="calculateDate()">
                            Calculate New Date
                        </button>
                    </div>
                </div>

                <div id="result-section" class="mt-4" style="display: none;">
                    <div class="border-glass rounded-4 p-4 text-center bg-white-5">
                         <h5 class="text-secondary mb-3">Resulting Date</h5>
                         <div class="display-3 fw-bold text-white mb-2" id="result-date"></div>
                         <div class="text-muted h5" id="result-weekday"></div>
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
});

async function calculateDate() {
    const start = document.getElementById('start_date').value;
    const op = document.querySelector('input[name="operation"]:checked').value;
    const y = document.getElementById('years').value;
    const m = document.getElementById('months').value;
    const d = document.getElementById('days').value;
    
    try {
        const response = await fetch('<?php echo app_base_url("/calculator/api/datetime/adder"); ?>', {
            method: 'POST',
            body: JSON.stringify({ 
                start_date: start, 
                operation: op,
                years: y,
                months: m,
                days: d
            })
        });
        
        const data = await response.json();
        const fullDate = new Date(data.result_date);
        
        document.getElementById('result-date').textContent = data.result_date;
        document.getElementById('result-weekday').textContent = data.result_formatted;
        
        document.getElementById('result-section').style.display = 'block';
    } catch (e) {
        console.error(e);
    }
}
</script>
