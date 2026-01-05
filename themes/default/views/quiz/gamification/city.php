<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

:root {
    --city-bg: #0f172a;
    --city-card: rgba(255, 255, 255, 0.03);
    --city-border: rgba(255, 255, 255, 0.08);
    --city-primary: #667eea;
    --city-primary-grad: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --city-accent: #00d1ff;
    --city-success: #10b981;
    --city-danger: #ef4444;
    --city-text: #ffffff;
    --city-muted: #94a3b8;
    --city-glow: 0 8px 32px rgba(102, 126, 234, 0.15);
}

.city-wrapper {
    background: var(--city-bg);
    min-height: 100vh;
    color: var(--city-text);
    font-family: 'Inter', system-ui, sans-serif;
    padding: 0 0 40px;
    position: relative;
    overflow-x: hidden;
}

/* Background Effects */
.city-bg-glow {
    position: fixed;
    top: -10%; right: -10%;
    width: 600px; height: 600px;
    background: radial-gradient(circle, rgba(118, 75, 162, 0.15), transparent 70%);
    pointer-events: none;
    z-index: 0;
}

.city-content { position: relative; z-index: 1; }

.premium-card {
    background: var(--city-card);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border: 1px solid var(--city-border);
    border-radius: 20px;
    padding: 24px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.premium-card:hover {
    border-color: rgba(102, 126, 234, 0.3);
    transform: translateY(-4px);
    box-shadow: var(--city-glow);
}

.wallet-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.resource-card {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 20px;
}

.resource-icon-shell {
    width: 48px;
    height: 48px;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid var(--city-border);
}

.resource-icon-shell img {
    width: 28px;
    height: 28px;
    object-fit: contain;
}

.resource-info h4 {
    margin: 0;
    font-size: 13px;
    color: var(--city-muted);
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.resource-info .amount {
    font-size: 24px;
    font-weight: 700;
    color: #fff;
    line-height: 1.2;
}

/* Header */
.gamification-header {
    padding: 20px 0;
    margin-bottom: 30px;
    border-bottom: 1px solid var(--city-border);
    background: rgba(15, 23, 42, 0.8);
    backdrop-filter: blur(10px);
    position: sticky;
    top: 0;
    z-index: 100;
}

.header-inner { display: flex; align-items: center; justify-content: space-between; }

.back-link {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: var(--city-muted);
    text-decoration: none;
    font-weight: 600;
    font-size: 0.9rem;
    transition: color 0.2s;
}
.back-link:hover { color: white; }

.header-title-group { text-align: right; }
.city-title {
    font-size: 1.5rem;
    font-weight: 800;
    margin: 0;
    background: var(--city-primary-grad);
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
}
.city-subtitle { font-size: 0.85rem; color: var(--city-muted); margin: 0; }

.construction-grid {
    display: grid;
    grid-template-columns: 350px 1fr;
    gap: 25px;
}

@media (max-width: 992px) {
    .construction-grid { grid-template-columns: 1fr; }
}

.build-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.build-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px;
    background: rgba(255, 255, 255, 0.02);
    border-radius: 16px;
    border: 1px solid var(--city-border);
    transition: background 0.2s;
}
.build-item:hover { background: rgba(255, 255, 255, 0.04); }

.build-icon {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
}

.cost-tag {
    font-size: 11px;
    font-weight: 700;
    color: var(--city-accent);
    background: rgba(0, 209, 255, 0.1);
    padding: 3px 8px;
    border-radius: 20px;
    margin-top: 4px;
    display: inline-block;
}

.btn-build {
    background: var(--city-primary-grad);
    border: none;
    color: white;
    padding: 8px 16px;
    border-radius: 10px;
    font-weight: 600;
    font-size: 0.9rem;
    cursor: pointer;
    transition: all 0.2s;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.2);
}

.btn-build:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
}

.btn-build:disabled {
    background: var(--city-card);
    color: var(--city-muted);
    cursor: not-allowed;
    box-shadow: none;
    border: 1px solid var(--city-border);
}

.map-container {
    min-height: 600px;
    position: relative;
    background-image: 
        linear-gradient(rgba(255, 255, 255, 0.03) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
    background-size: 40px 40px;
    border-radius: 20px;
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
    background: rgba(102, 126, 234, 0.1);
    border-color: var(--city-primary);
    transform: translateY(-5px);
}

.building-icon {
    font-size: 32px;
    margin-bottom: 12px;
    display: block;
}

.building-name {
    display: block;
    font-weight: 600;
    font-size: 0.95rem;
    text-transform: capitalize;
}

.building-level {
    font-size: 11px;
    color: var(--city-muted);
}
</style>

<div class="city-wrapper">
    <div class="city-bg-glow"></div>
    
    <!-- Header -->
    <header class="gamification-header">
        <div class="container header-inner">
            <a href="<?php echo app_base_url('quiz'); ?>" class="back-link">
                <i class="fas fa-arrow-left"></i> <span>Back to Portal</span>
            </a>
            <div class="header-title-group">
                <h1 class="city-title">Architect's Studio</h1>
                <p class="city-subtitle">Design the future of civil engineering</p>
            </div>
        </div>
    </header>

    <div class="container city-content">

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
