<?php
// themes/default/views/quiz/gamification/city.php
?>

<style>
@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&display=swap');

:root {
    --city-bg: #0a0e1a;
    --city-card: rgba(255, 255, 255, 0.03);
    --city-border: rgba(255, 255, 255, 0.08);
    --city-primary: #7c5dff;
    --city-accent: #00d1ff;
    --city-success: #2ee6a8;
    --city-danger: #ff4d4d;
    --city-text: #e8ecf2;
    --city-muted: #9aa4b5;
    --city-glow: 0 8px 32px rgba(124, 93, 255, 0.2);
}

.city-wrapper {
    background: radial-gradient(circle at top right, #1a1f3c, #0a0e1a 60%);
    min-height: 100vh;
    color: var(--city-text);
    font-family: 'Outfit', sans-serif;
    padding: 40px 20px;
}

.premium-card {
    background: var(--city-card);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid var(--city-border);
    border-radius: 24px;
    padding: 24px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.premium-card:hover {
    border-color: rgba(124, 93, 255, 0.3);
    transform: translateY(-5px);
    box-shadow: var(--city-glow);
}

.wallet-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 40px;
}

.resource-card {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 20px;
}

.resource-icon-shell {
    width: 50px;
    height: 50px;
    background: rgba(0, 0, 0, 0.2);
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid var(--city-border);
}

.resource-icon-shell img {
    width: 32px;
    height: 32px;
    object-fit: contain;
}

.resource-info h4 {
    margin: 0;
    font-size: 14px;
    color: var(--city-muted);
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.resource-info .amount {
    font-size: 24px;
    font-weight: 800;
    color: #fff;
}

.city-header {
    text-align: center;
    margin-bottom: 50px;
}

.city-title {
    font-size: 42px;
    font-weight: 800;
    margin-bottom: 10px;
    background: linear-gradient(135deg, #fff, #9aa4b5);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.city-subtitle {
    font-size: 18px;
    color: var(--city-muted);
}

.construction-grid {
    display: grid;
    grid-template-columns: 1fr 2fr;
    gap: 30px;
}

@media (max-width: 992px) {
    .construction-grid { grid-template-columns: 1fr; }
}

.build-list {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.build-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px;
    background: rgba(255, 255, 255, 0.02);
    border-radius: 16px;
    border: 1px solid var(--city-border);
}

.build-icon {
    width: 45px;
    height: 45px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
}

.cost-tag {
    font-size: 12px;
    font-weight: 600;
    color: var(--city-accent);
    background: rgba(0, 209, 255, 0.1);
    padding: 4px 10px;
    border-radius: 20px;
    margin-top: 4px;
    display: inline-block;
}

.btn-build {
    background: var(--city-primary);
    border: none;
    color: white;
    padding: 8px 20px;
    border-radius: 12px;
    font-weight: 700;
    transition: all 0.2s;
}

.btn-build:hover:not(:disabled) {
    filter: brightness(1.2);
    transform: scale(1.05);
}

.btn-build:disabled {
    background: var(--city-muted);
    opacity: 0.5;
}

.map-container {
    min-height: 500px;
    position: relative;
    background-image: 
        linear-gradient(rgba(255, 255, 255, 0.02) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255, 255, 255, 0.02) 1px, transparent 1px);
    background-size: 40px 40px;
    border-radius: 24px;
    overflow: auto;
    padding: 30px;
}

.building-card {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 16px;
    padding: 20px;
    text-align: center;
    transition: all 0.3s;
    animation: fadeIn 0.5s ease-out backwards;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.building-card:hover {
    background: rgba(124, 93, 255, 0.1);
    border-color: var(--city-primary);
}

.building-icon {
    font-size: 32px;
    margin-bottom: 12px;
    display: block;
}

.building-name {
    display: block;
    font-weight: 700;
    text-transform: capitalize;
}

.building-level {
    font-size: 12px;
    color: var(--city-muted);
}
</style>

<div class="city-wrapper">
    <div class="container">
        <header class="city-header">
            <h1 class="city-title">Civil Empire Designer</h1>
            <p class="city-subtitle">Convert your engineering brilliance into the city of the future.</p>
        </header>

        <!-- Premium Resource Wallet -->
        <div class="wallet-grid">
            <div class="premium-card resource-card">
                <div class="resource-icon-shell">
                    <img src="<?php echo app_base_url('themes/default/assets/resources/currency/coin.webp'); ?>" alt="Coins">
                </div>
                <div class="resource-info">
                    <h4>Gold Coins</h4>
                    <div class="amount" id="res-coins"><?php echo $wallet['coins'] ?? 0; ?></div>
                </div>
            </div>
            <div class="premium-card resource-card">
                <div class="resource-icon-shell">
                    <img src="<?php echo app_base_url('themes/default/assets/resources/materials/brick_single.webp'); ?>" alt="Bricks">
                </div>
                <div class="resource-info">
                    <h4>Bricks</h4>
                    <div class="amount" id="res-bricks"><?php echo $wallet['bricks'] ?? 0; ?></div>
                </div>
            </div>
            <div class="premium-card resource-card">
                <div class="resource-icon-shell">
                    <img src="<?php echo app_base_url('themes/default/assets/resources/materials/bbcement.webp'); ?>" alt="Cement">
                </div>
                <div class="resource-info">
                    <h4>Cement</h4>
                    <div class="amount" id="res-cement"><?php echo $wallet['cement'] ?? 0; ?></div>
                </div>
            </div>
            <div class="premium-card resource-card">
                <div class="resource-icon-shell">
                    <img src="<?php echo app_base_url('themes/default/assets/resources/materials/steel.webp'); ?>" alt="Steel">
                </div>
                <div class="resource-info">
                    <h4>Steel</h4>
                    <div class="amount" id="res-steel"><?php echo $wallet['steel'] ?? 0; ?></div>
                </div>
            </div>
        </div>

        <div class="construction-grid">
            <!-- Sidebar: Construction Menu -->
            <div class="construction-menu">
                <div class="premium-card">
                    <h3 class="mb-4" style="font-weight: 800; font-size: 20px;">
                        <i class="fas fa-hammer me-2 text-primary"></i> BLUEPRINT CATALOG
                    </h3>
                    <div class="build-list">
                        <!-- House -->
                        <div class="build-item">
                            <div class="d-flex align-items-center gap-3">
                                <div class="build-icon bg-success bg-opacity-10 text-success">
                                    <i class="fas fa-home"></i>
                                </div>
                                <div>
                                    <div style="font-weight: 700;">Residential Unit</div>
                                    <div class="cost-tag">100 Bricks</div>
                                </div>
                            </div>
                            <button class="btn-build" data-type="house">BUILD</button>
                        </div>
                        
                        <!-- Road -->
                        <div class="build-item">
                            <div class="d-flex align-items-center gap-3">
                                <div class="build-icon bg-secondary bg-opacity-10 text-secondary">
                                    <i class="fas fa-road"></i>
                                </div>
                                <div>
                                    <div style="font-weight: 700;">Asphalt Road</div>
                                    <div class="cost-tag">50 Cement</div>
                                </div>
                            </div>
                            <button class="btn-build" data-type="road">BUILD</button>
                        </div>

                        <!-- Bridge -->
                        <div class="build-item">
                            <div class="d-flex align-items-center gap-3">
                                <div class="build-icon bg-danger bg-opacity-10 text-danger">
                                    <i class="fas fa-archway"></i>
                                </div>
                                <div>
                                    <div style="font-weight: 700;">Arch Bridge</div>
                                    <div class="cost-tag">500 Bricks + 200 Steel</div>
                                </div>
                            </div>
                            <button class="btn-build" data-type="bridge">BUILD</button>
                        </div>

                        <!-- Skyscraper -->
                        <div class="build-item">
                            <div class="d-flex align-items-center gap-3">
                                <div class="build-icon bg-info bg-opacity-10 text-info">
                                    <i class="fas fa-building"></i>
                                </div>
                                <div>
                                    <div style="font-weight: 700;">Corporate Tower</div>
                                    <div class="cost-tag">1k Bricks + 500 Steel</div>
                                </div>
                            </div>
                            <button class="btn-build" data-type="tower">BUILD</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main: Map Overview -->
            <div class="map-view">
                <div class="premium-card map-container">
                    <?php if (empty($buildings)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-hard-hat fa-4x mb-4 text-muted opacity-20"></i>
                            <h3>Virgin Terraforming Site</h3>
                            <p class="text-muted">Solve quiz challenges to earn materials and begin colonization.</p>
                        </div>
                    <?php else: ?>
                        <div class="row g-4">
                            <?php foreach($buildings as $idx => $b): ?>
                                <div class="col-xl-3 col-lg-4 col-sm-6">
                                    <div class="building-card" style="animation-delay: <?php echo $idx * 0.1; ?>s">
                                        <?php 
                                            $icon = 'home'; $class = 'text-success';
                                            if($b['building_type'] == 'road') { $icon = 'road'; $class = 'text-secondary'; }
                                            if($b['building_type'] == 'bridge') { $icon = 'archway'; $class = 'text-danger'; }
                                            if($b['building_type'] == 'tower') { $icon = 'building'; $class = 'text-info'; }
                                        ?>
                                        <i class="fas fa-<?php echo $icon; ?> building-icon <?php echo $class; ?>"></i>
                                        <span class="building-name"><?php echo $b['building_type']; ?></span>
                                        <span class="building-level">Iteration 0<?php echo $b['level']; ?></span>
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
document.querySelectorAll('.btn-build').forEach(btn => {
    btn.addEventListener('click', async function() {
        const type = this.dataset.type;
        const originalText = this.innerHTML;
        
        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        this.disabled = true;
        
        try {
            const fd = new FormData();
            fd.append('type', type);
            fd.append('csrf_token', '<?php echo csrf_token(); ?>');
            
            const res = await fetch('<?php echo app_base_url("api/city/build"); ?>', {
                method: 'POST',
                body: fd
            });
            const data = await res.json();
            
            if (res.ok) {
                this.innerHTML = '<i class="fas fa-check"></i>';
                this.style.background = 'var(--city-success)';
                
                setTimeout(() => {
                    location.reload(); 
                }, 800);
            } else {
                alert('Structural Failure: ' + data.message);
                this.innerHTML = originalText;
                this.disabled = false;
            }
        } catch (e) {
            alert('Signal Lost: Check connectivity.');
            this.innerHTML = originalText;
            this.disabled = false;
        }
    });
});
</script>
