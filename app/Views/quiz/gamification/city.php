<div class="container-fluid py-4 min-vh-100 bg-light">
    <!-- Resource HUD -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0 bg-white">
                <div class="card-body py-3 d-flex justify-content-around align-items-center flex-wrap gap-3">
                    <div class="d-flex align-items-center" title="Coins (Currency)">
                        <div class="avatar bg-warning bg-opacity-10 text-warning rounded-circle me-2 p-2">
                            <i class="fas fa-coins"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 10px;">Coins</small>
                            <span class="fs-5 fw-bold" id="res-coins"><?= number_format($wallet['coins'] ?? 0) ?></span>
                        </div>
                    </div>
                    <div class="vr mx-2 text-muted opacity-25"></div>
                    <div class="d-flex align-items-center" title="Bricks">
                        <div class="avatar bg-danger bg-opacity-10 text-danger rounded-circle me-2 p-2">
                            <i class="fas fa-cube"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 10px;">Bricks</small>
                            <span class="fs-5 fw-bold" id="res-bricks"><?= number_format($wallet['bricks'] ?? 0) ?></span>
                        </div>
                    </div>
                    <div class="d-flex align-items-center" title="Steel">
                        <div class="avatar bg-secondary bg-opacity-10 text-secondary rounded-circle me-2 p-2">
                            <i class="fas fa-layer-group"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 10px;">Steel</small>
                            <span class="fs-5 fw-bold" id="res-steel"><?= number_format($wallet['steel'] ?? 0) ?></span>
                        </div>
                    </div>
                    <div class="d-flex align-items-center" title="Wood Planks">
                        <div class="avatar bg-success bg-opacity-10 text-success rounded-circle me-2 p-2">
                            <i class="fas fa-tree"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 10px;">Planks</small>
                            <span class="fs-5 fw-bold" id="res-wood_planks"><?= number_format($wallet['wood_planks'] ?? 0) ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- City View (Left) -->
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-bottom-0 py-3 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold text-dark mb-0"><i class="fas fa-city text-primary me-2"></i> My City Layout</h5>
                    <span class="badge bg-primary rounded-pill"><?= count($buildings) ?> Buildings</span>
                </div>
                <div class="card-body bg-light p-4 position-relative" style="background-image: radial-gradient(#cbd5e1 1px, transparent 1px); background-size: 20px 20px; min-height: 400px;">
                    <?php if(empty($buildings)): ?>
                        <div class="text-center text-muted position-absolute top-50 start-50 translate-middle">
                            <i class="fas fa-hard-hat fa-3x mb-3 opacity-25"></i>
                            <p class="mb-0">Your site is empty. Start building!</p>
                        </div>
                    <?php else: ?>
                        <div class="row g-3">
                            <?php foreach($buildings as $b): ?>
                                <div class="col-md-3 col-6">
                                    <div class="card border-0 shadow-sm text-center p-3 h-100">
                                        <div class="mb-2">
                                            <?php 
                                                $icon = 'home';
                                                if($b['building_type'] == 'road') $icon = 'road';
                                                if($b['building_type'] == 'bridge') $icon = 'archway';
                                                if($b['building_type'] == 'tower') $icon = 'building';
                                            ?>
                                            <i class="fas fa-<?= $icon ?> fa-2x text-primary"></i>
                                        </div>
                                        <div class="fw-bold small text-uppercase"><?= $b['building_type'] ?></div>
                                        <small class="text-muted" style="font-size: 10px;">Lvl <?= $b['level'] ?></small>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Build Menu (Right) -->
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-bottom-0 py-3">
                    <h5 class="fw-bold text-dark mb-0"><i class="fas fa-tools text-warning me-2"></i> Blueprints</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <!-- House -->
                        <div class="list-group-item p-3 border-0 border-bottom">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="fw-bold mb-0"><i class="fas fa-home me-2 text-info"></i> Residential House</h6>
                                <button onclick="CityManager.build('house')" class="btn btn-sm btn-primary rounded-pill px-3 fw-bold">Build</button>
                            </div>
                            <div class="d-flex gap-3 small text-muted">
                                <span><i class="fas fa-cube text-danger me-1"></i> 100</span>
                                <span><i class="fas fa-tree text-success me-1"></i> 20</span>
                                <span><i class="fas fa-glass-martini text-secondary me-1"></i> 10</span>
                            </div>
                        </div>

                        <!-- Road -->
                        <div class="list-group-item p-3 border-0 border-bottom">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="fw-bold mb-0"><i class="fas fa-road me-2 text-dark"></i> Paved Road</h6>
                                <button onclick="CityManager.build('road')" class="btn btn-sm btn-primary rounded-pill px-3 fw-bold">Build</button>
                            </div>
                            <div class="d-flex gap-3 small text-muted">
                                <span><i class="fas fa-glass-martini text-secondary me-1"></i> 50</span>
                                <span><i class="fas fa-hourglass text-warning me-1"></i> 200</span>
                            </div>
                        </div>

                        <!-- Bridge -->
                        <div class="list-group-item p-3 border-0 border-bottom">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="fw-bold mb-0"><i class="fas fa-archway me-2 text-danger"></i> Steel Bridge</h6>
                                <button onclick="CityManager.build('bridge')" class="btn btn-sm btn-primary rounded-pill px-3 fw-bold">Build</button>
                            </div>
                            <div class="d-flex gap-3 small text-muted">
                                <span><i class="fas fa-cube text-danger me-1"></i> 500</span>
                                <span><i class="fas fa-layer-group text-secondary me-1"></i> 200</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const CityManager = {
    build: function(type) {
        if(!confirm(`Construct a ${type}? Resources will be deducted.`)) return;
        
        const btn = event.target;
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        btn.disabled = true;

        const formData = new FormData();
        formData.append('type', type);
        formData.append('csrf_token', "<?= \App\Services\Security::generateCsrfToken() ?>");

        fetch('/api/city/build', {
            method: 'POST',
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            if(data.success) {
                alert(data.message);
                location.reload(); // Refresh to update wallet/ui
            } else {
                alert("Construction Failed: " + data.message);
            }
        })
        .catch(e => alert("Network Error"))
        .finally(() => {
            btn.innerHTML = originalText;
            btn.disabled = false;
        });
    }
};
</script>
