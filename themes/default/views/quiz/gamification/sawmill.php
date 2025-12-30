
<div class="container py-5">
    <div class="row mb-5">
        <div class="col-12 text-center">
            <h1 class="display-4 font-weight-bold" style="color: #5d4037;"><img src="<?php echo app_base_url('themes/default/assets/resources/buildings/saw_farm.webp'); ?>" width="64" class="mr-3 rounded shadow-sm"> The BB Sawmill</h1>
            <p class="lead text-muted">A profit-making factory. Process raw Timber Logs into Polished Planks for a net gain.</p>
        </div>
    </div>

    <!-- Inventory Section -->
    <div class="row mb-5 justify-content-center">
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm border-0 text-center p-3 rounded-lg overflow-hidden">
                <img src="<?php echo app_base_url('themes/default/assets/resources/materials/log.webp'); ?>" class="mx-auto" style="height: 60px;">
                <h6 class="text-muted mt-2">Raw Logs</h6>
                <h2 class="font-weight-bold" id="res-logs"><?php echo number_format($wallet['wood_logs']); ?></h2>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm border-0 text-center p-3 rounded-lg overflow-hidden">
                <img src="<?php echo app_base_url('themes/default/assets/resources/currency/coin.webp'); ?>" class="mx-auto" style="height: 60px;">
                <h6 class="text-muted mt-2">BB Coins</h6>
                <h2 class="font-weight-bold text-warning" id="res-coins"><?php echo number_format($wallet['coins']); ?></h2>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm border-0 text-center p-3 rounded-lg overflow-hidden border-brown">
                <img src="<?php echo app_base_url('themes/default/assets/resources/materials/plank.webp'); ?>" class="mx-auto" style="height: 60px;">
                <h6 class="text-muted mt-2">Owned Planks</h6>
                <h2 class="font-weight-bold" id="res-planks" style="color: #8d6e63;"><?php echo number_format($wallet['wood_planks']); ?></h2>
            </div>
        </div>
    </div>

    <!-- Crafting Station -->
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-lg border-0 rounded-xl overflow-hidden glass-card">
                <div class="card-header bg-dark text-white p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0 font-weight-bold">BB Advanced Wood Processor</h4>
                        <span class="badge badge-warning p-2 px-3 badge-pill shadow-sm">1 Log + 10 Coins ⮕ 4 Planks</span>
                    </div>
                </div>
                <div class="card-body p-5">
                    <div class="row align-items-center">
                        <!-- Input -->
                        <div class="col-md-4 text-center">
                            <div class="p-4 border border-dashed rounded-lg bg-light-brown animation-float">
                                <img src="<?php echo app_base_url('themes/default/assets/resources/materials/log.webp'); ?>" class="img-fluid mb-3" style="max-height: 120px;">
                                <h6 class="font-weight-bold text-uppercase">Input</h6>
                            </div>
                        </div>

                        <!-- Action -->
                        <div class="col-md-4 text-center">
                            <i class="fas fa-chevron-right fa-3x text-muted mb-4 d-none d-md-inline"></i>
                            <div class="form-group mb-4">
                                <label class="font-weight-bold text-muted small text-uppercase letter-spacing-1">Quantity to Process</label>
                                <input type="number" id="craft-qty" class="form-control form-control-lg text-center font-weight-bold rounded-pill border-2" value="1" min="1">
                            </div>
                            <button class="btn btn-brown btn-block btn-lg p-3 rounded-pill shadow-lg font-weight-bold mt-2" id="btn-craft" style="background: linear-gradient(135deg, #8b4513, #5d4037); color: white; border: none;">
                                RUN SAWMILL <i class="fas fa-cog fa-spin ml-2"></i>
                            </button>
                        </div>

                        <!-- Output -->
                        <div class="col-md-4 text-center">
                            <div class="p-4 border border-dashed rounded-lg bg-light-brown animation-float-reverse">
                                <img src="<?php echo app_base_url('themes/default/assets/resources/materials/plank_bundle.webp'); ?>" class="img-fluid mb-3" style="max-height: 120px;">
                                <h6 class="font-weight-bold text-uppercase">Output</h6>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light p-4 text-center border-0">
                    <div class="d-inline-flex align-items-center p-2 px-4 bg-white rounded-pill shadow-sm">
                        <i class="fas fa-chart-line text-success mr-2"></i>
                        <span class="text-dark small font-weight-bold">PROFIT CALCULATION:</span>
                        <span class="badge badge-success ml-2 p-2 px-3 shadow-xs">Earn +10 Coins per Log!</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.bg-light-brown { background: rgba(139, 69, 19, 0.05); }
.border-brown { border: 2px solid #8d6e63 !important; }
.rounded-xl { border-radius: 25px; }
.border-2 { border-width: 2px; }
.letter-spacing-1 { letter-spacing: 1px; }
.badge-pill { font-size: 0.9rem; }
.animation-float { animation: float 3s ease-in-out infinite; }
.animation-float-reverse { animation: float-reverse 3s ease-in-out infinite; }
@keyframes float { 0% { transform: translateY(0px); } 50% { transform: translateY(-10px); } 100% { transform: translateY(0px); } }
@keyframes float-reverse { 0% { transform: translateY(0px); } 50% { transform: translateY(10px); } 100% { transform: translateY(0px); } }
.glass-card { background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); }
.shadow-lg { box-shadow: 0 1rem 3rem rgba(0,0,0,.1) !important; }
.btn-brown:hover { transform: scale(1.02); box-shadow: 0 10px 20px rgba(93, 64, 55, 0.3); }
</style>

<script>
document.getElementById('btn-craft').addEventListener('click', async function() {
    const qty = document.getElementById('craft-qty').value;
    const btn = this;
    const originalContent = btn.innerHTML;
    
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
    btn.disabled = true;
    
    try {
        const fd = new FormData();
        fd.append('quantity', qty);
        fd.append('csrf_token', '<?php echo csrf_token(); ?>');
        
        const res = await fetch('/api/city/craft', {
            method: 'POST',
            body: fd
        });
        const data = await res.json();
        
        if (res.ok) {
            alert(data.message);
            location.reload();
        } else {
            alert('Crafting Error: ' + data.message);
            btn.innerHTML = originalContent;
            btn.disabled = false;
        }
    } catch (e) {
        alert('Could not connect to the Sawmill.');
        btn.innerHTML = originalContent;
        btn.disabled = false;
    }
});
</script>
