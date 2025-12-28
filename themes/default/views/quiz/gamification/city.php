

<div class="container py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="display-4 text-primary font-weight-bold"><i class="fas fa-city"></i> My Civil City</h1>
            <p class="lead text-muted">Build your engineering empire using knowledge rewards!</p>
        </div>
    </div>

    <!-- Resource Wallet -->
    <div class="row mb-5">
        <div class="col-md-3">
            <div class="card bg-warning text-white shadow-sm">
                <div class="card-body text-center">
                    <h3><i class="fas fa-coins"></i> Coins</h3>
                    <h2 class="font-weight-bold" id="res-coins"><?php echo $wallet['coins'] ?? 0; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white shadow-sm">
                <div class="card-body text-center">
                    <h3><i class="fas fa-square"></i> Bricks</h3>
                    <h2 class="font-weight-bold" id="res-bricks"><?php echo $wallet['bricks'] ?? 0; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-secondary text-white shadow-sm">
                <div class="card-body text-center">
                    <h3><i class="fas fa-layer-group"></i> Cement</h3>
                    <h2 class="font-weight-bold" id="res-cement"><?php echo $wallet['cement'] ?? 0; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-dark text-white shadow-sm">
                <div class="card-body text-center">
                    <h3><i class="fas fa-i-cursor"></i> Steel</h3>
                    <h2 class="font-weight-bold" id="res-steel"><?php echo $wallet['steel'] ?? 0; ?></h2>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Construction Zone -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow-lg border-primary">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-hammer"></i> Construction Site</h4>
                </div>
                <div class="list-group list-group-flush">
                    <!-- House -->
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-1"><i class="fas fa-home text-success"></i> Residential House</h5>
                                <small class="text-danger font-weight-bold">100 Bricks</small>
                            </div>
                            <button class="btn btn-sm btn-outline-primary btn-build" data-type="house">
                                Build
                            </button>
                        </div>
                    </div>
                    <!-- Road -->
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-1"><i class="fas fa-road text-secondary"></i> Paved Road</h5>
                                <small class="text-secondary font-weight-bold">50 Cement</small>
                            </div>
                            <button class="btn btn-sm btn-outline-primary btn-build" data-type="road">
                                Build
                            </button>
                        </div>
                    </div>
                    <!-- Bridge -->
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-1"><i class="fas fa-archway text-danger"></i> Steel Bridge</h5>
                                <small class="text-dark font-weight-bold">500 Bricks + 200 Steel</small>
                            </div>
                            <button class="btn btn-sm btn-outline-primary btn-build" data-type="bridge">
                                Build
                            </button>
                        </div>
                    </div>
                    <!-- Tower -->
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-1"><i class="fas fa-building text-info"></i> Skyscraper</h5>
                                <small class="text-dark font-weight-bold">1k Bricks + 500 Steel + 500 Cem</small>
                            </div>
                            <button class="btn btn-sm btn-outline-primary btn-build" data-type="tower">
                                Build
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- City Map (Grid) -->
        <div class="col-lg-8">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">
                    <h4 class="mb-0">Map Overview</h4>
                </div>
                <div class="card-body bg-light" id="city-map" style="min-height: 400px; position: relative; overflow: hidden;">
                    <!-- Buildings Rendered Here -->
                    <?php if (empty($buildings)): ?>
                        <div class="text-center text-muted mt-5 pt-5">
                            <i class="fas fa-hard-hat fa-4x mb-3"></i>
                            <h3>Empty Lot</h3>
                            <p>Start solving quizzes to earn materials and build your city!</p>
                        </div>
                    <?php else: ?>
                        <div class="row">
                            <?php foreach($buildings as $b): ?>
                                <div class="col-md-3 col-6 mb-3 text-center">
                                    <div class="p-3 bg-white rounded shadow-sm border">
                                        <?php 
                                            $icon = 'home';
                                            $color = 'text-success';
                                            if($b['building_type'] == 'road') { $icon = 'road'; $color = 'text-secondary'; }
                                            if($b['building_type'] == 'bridge') { $icon = 'archway'; $color = 'text-danger'; }
                                            if($b['building_type'] == 'tower') { $icon = 'building'; $color = 'text-info'; }
                                        ?>
                                        <i class="fas fa-<?php echo $icon; ?> fa-3x <?php echo $color; ?> mb-2"></i>
                                        <h6 class="text-capitalize"><?php echo $b['building_type']; ?></h6>
                                        <small class="text-muted">Lvl <?php echo $b['level']; ?></small>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function playSfx(type) {
    const sounds = {
        'hammer': 'https://assets.mixkit.co/sfx/preview/mixkit-hammer-hit-on-wood-2144.mp3',
        'coins': 'https://assets.mixkit.co/sfx/preview/mixkit-money-bag-drop-1439.mp3'
    };
    if (sounds[type]) {
        const audio = new Audio(sounds[type]);
        audio.volume = 0.5;
        audio.play().catch(e => {});
    }
}

document.querySelectorAll('.btn-build').forEach(btn => {
    btn.addEventListener('click', async function() {
        const type = this.dataset.type;
        const originalText = this.innerHTML;
        
        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Building...';
        this.disabled = true;
        
        try {
            const fd = new FormData();
            fd.append('type', type);
            fd.append('csrf_token', '<?php echo csrf_token(); ?>');
            
            const res = await fetch('/api/city/build', {
                method: 'POST',
                body: fd
            });
            const data = await res.json();
            
            if (res.ok) {
                playSfx('hammer');
                // Visual feedback: simple scale effect on success
                this.classList.remove('btn-outline-primary');
                this.classList.add('btn-success');
                this.innerHTML = '<i class="fas fa-check"></i> Done!';
                
                setTimeout(() => {
                    alert(data.message);
                    location.reload(); 
                }, 1000);
            } else {
                alert('Construction Failed: ' + data.message);
                this.innerHTML = originalText;
                this.disabled = false;
            }
        } catch (e) {
            alert('Error connecting to construction crew.');
            this.innerHTML = originalText;
            this.disabled = false;
        }
    });
});
</script>
