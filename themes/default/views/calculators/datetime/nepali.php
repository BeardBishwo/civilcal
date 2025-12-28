<?php include_once __DIR__ . '/../../../../partials/calculator_sidebar.php'; ?>

<main class="main-content">
    <div class="row g-4 mb-4">
        <div class="col-md-8 mx-auto">
            <div class="glass-card">
                <div class="d-flex align-items-center mb-4">
                    <div class="icon-square bg-danger-gradient text-white me-3">
                        <i class="bi bi-calendar-event fs-4"></i>
                    </div>
                    <h2 class="mb-0 fw-bold position-relative z-1"><?php echo htmlspecialchars($title); ?></h2>
                </div>

                <!-- Tabs -->
                <ul class="nav nav-pills mb-4 nav-justified bg-white-5 p-2 rounded-4" id="pills-tab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active rounded-pill fw-bold" id="pills-ad-bs-tab" data-bs-toggle="pill" data-bs-target="#pills-ad-bs" type="button">
                            English to Nepali (AD → BS)
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link rounded-pill fw-bold" id="pills-bs-ad-tab" data-bs-toggle="pill" data-bs-target="#pills-bs-ad" type="button">
                            Nepali to English (BS → AD)
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="pills-tabContent">
                    <!-- AD to BS -->
                    <div class="tab-pane fade show active" id="pills-ad-bs" role="tabpanel">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label text-secondary small text-uppercase fw-bold ls-1">English Date (AD)</label>
                                <input type="date" class="form-control form-control-lg bg-dark text-white border-glass" id="ad_input_date">
                            </div>
                            <div class="col-12">
                                <button class="btn btn-primary w-100 py-3 fw-bold rounded-pill shadow-primary" onclick="convertAdToBs()">
                                    Convert to Nepali
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- BS to AD -->
                    <div class="tab-pane fade" id="pills-bs-ad" role="tabpanel">
                        <div class="row g-3">
                            <div class="col-4">
                                <label class="form-label text-secondary small text-uppercase fw-bold ls-1">Year (BS)</label>
                                <input type="number" class="form-control form-control-lg bg-dark text-white border-glass" id="bs_year" placeholder="2080">
                            </div>
                            <div class="col-4">
                                <label class="form-label text-secondary small text-uppercase fw-bold ls-1">Month</label>
                                <select class="form-select form-select-lg bg-dark text-white border-glass" id="bs_month">
                                    <option value="1">Baishakh</option>
                                    <option value="2">Jestha</option>
                                    <option value="3">Ashad</option>
                                    <option value="4">Shrawan</option>
                                    <option value="5">Bhadra</option>
                                    <option value="6">Ashwin</option>
                                    <option value="7">Kartik</option>
                                    <option value="8">Mangsir</option>
                                    <option value="9">Poush</option>
                                    <option value="10">Magh</option>
                                    <option value="11">Falgun</option>
                                    <option value="12">Chaitra</option>
                                </select>
                            </div>
                            <div class="col-4">
                                <label class="form-label text-secondary small text-uppercase fw-bold ls-1">Day</label>
                                <input type="number" class="form-control form-control-lg bg-dark text-white border-glass" id="bs_day" placeholder="1" min="1" max="32">
                            </div>
                            <div class="col-12">
                                <button class="btn btn-primary w-100 py-3 fw-bold rounded-pill shadow-primary" onclick="convertBsToAd()">
                                    Convert to English
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="result-section" class="mt-4" style="display: none;">
                    <div class="border-glass rounded-4 p-4 text-center bg-white-5">
                         <h5 class="text-secondary mb-3">Converted Date</h5>
                         <div class="display-3 fw-bold text-white mb-2" id="result-date"></div>
                         <div class="text-muted h4" id="result-dayname"></div>
                         <div class="text-muted small mt-2 d-none" id="result-error"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('ad_input_date').value = today;
});

async function convertAdToBs() {
    const date = document.getElementById('ad_input_date').value;
    if (!date) return;
    
    const [y, m, d] = date.split('-');
    
    await performConversion('ad_to_bs', y, m, d);
}

async function convertBsToAd() {
    const y = document.getElementById('bs_year').value;
    const m = document.getElementById('bs_month').value;
    const d = document.getElementById('bs_day').value;
    
    if (!y || !m || !d) return;
    
    await performConversion('bs_to_ad', y, m, d);
}

async function performConversion(type, y, m, d) {
    try {
        const response = await fetch('<?php echo app_base_url("/calculator/api/datetime/nepali"); ?>', {
            method: 'POST',
            body: JSON.stringify({ 
                type: type,
                year: y,
                month: m,
                day: d
            })
        });
        
        const data = await response.json();
        const resultSection = document.getElementById('result-section');
        const resultDate = document.getElementById('result-date');
        const resultDay = document.getElementById('result-dayname');
        
        if (data.error) {
            resultDate.textContent = "Error";
            resultDay.textContent = data.error;
            resultDay.classList.add('text-danger');
            resultSection.style.display = 'block';
            return;
        }
        
        resultDay.classList.remove('text-danger');
        
        if (type === 'ad_to_bs') {
            resultDate.textContent = `${data.month_name} ${data.day}, ${data.year}`;
            resultDay.textContent = data.day_name;
        } else {
            resultDate.textContent = data.formatted;
            resultDay.textContent = `${data.day_name}`;
        }
        
        resultSection.style.display = 'block';
    } catch (e) {
        console.error(e);
    }
}
</script>
