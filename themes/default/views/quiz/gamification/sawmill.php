
<style>
/* PREMIUM DESIGN SYSTEM - STANDARDIZED */
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;900&display=swap');

:root {
    --saw-bg: #0f172a;
    --saw-card: rgba(255, 255, 255, 0.03);
    --saw-border: rgba(255, 255, 255, 0.08);
    --saw-primary: #f59e0b; /* Amber 500 */
    --saw-text: #ffffff;
    --saw-muted: #94a3b8;
}

.premium-sawmill-wrapper {
    font-family: 'Inter', sans-serif;
    background: radial-gradient(circle at top right, #1e1b4b, #0f172a);
    min-height: 100vh;
    color: var(--saw-text);
    padding-bottom: 50px;
}

/* Header */
.gamification-header {
    padding: 20px 0;
    margin-bottom: 40px;
    border-bottom: 1px solid var(--saw-border);
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
    color: var(--saw-muted);
    text-decoration: none;
    font-weight: 600;
    font-size: 0.9rem;
    transition: color 0.2s;
}
.back-link:hover { color: white; }

.header-title-group { text-align: right; }
.saw-title {
    font-size: 1.5rem;
    font-weight: 800;
    margin: 0;
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
}
.saw-subtitle { font-size: 0.85rem; color: var(--saw-muted); margin: 0; }
</style>

<div class="premium-sawmill-wrapper">
    <!-- Header -->
    <header class="gamification-header">
        <div class="container header-inner">
            <a href="<?php echo app_base_url('quiz'); ?>" class="back-link">
                <i class="fas fa-arrow-left"></i> <span>Back to Portal</span>
            </a>
            <div class="header-title-group">
                <h1 class="saw-title">Industrial Estate</h1>
                <p class="saw-subtitle">The BB Sawmill</p>
            </div>
        </div>
    </header>

    <!-- Content -->
    <div class="container">
        <!-- Dashboard Header -->
        <div class="row align-items-center mb-5">
            <div class="col-lg-7">
                <div class="d-flex align-items-center mb-3">
                    <div class="icon-box-premium mr-3 animate-pulse-slow">
                        <img src="<?php echo app_base_url('themes/default/assets/resources/buildings/saw_farm.webp'); ?>" width="60">
                    </div>
                    <div>
                        <h6 class="text-amber mb-0 text-uppercase letter-spacing-2 font-weight-bold" style="font-size: 0.75rem;">Production Unit</h6>
                        <h2 class="font-weight-black text-white glow-text m-0">Sawmill Operations</h2>
                    </div>
                </div>
                <p class="text-platinum opacity-75 small" style="max-width: 500px;">Transform raw logs into high-grade polished planks using advanced industrial processing.</p>
            </div>
            <div class="col-lg-5 text-lg-right">
                <div class="p-3 glass-card-dark rounded-xl d-inline-block border-gold shadow-gold">
                    <div class="d-flex align-items-center text-left">
                        <div class="mr-3">
                            <span class="text-platinum small d-block mb-1 text-uppercase letter-spacing-1">Current Balance</span>
                            <div class="d-flex align-items-center">
                                <img src="<?php echo app_base_url('themes/default/assets/resources/currency/coin.webp'); ?>" width="24" class="mr-2">
                                <h3 class="mb-0 text-amber font-weight-black" id="res-coins"><?php echo number_format($wallet['coins']); ?></h3>
                            </div>
                        </div>
                        <div class="divider-vertical mx-3"></div>
                        <div>
                            <span class="text-platinum small d-block mb-1 text-uppercase letter-spacing-1">Net Gain</span>
                            <h3 class="mb-0 text-success font-weight-black">+10 <small class="text-platinum-50">/log</small></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Inventory & Processing Section -->
    <div class="container">
        <div class="row">
            <!-- Left: Resource Stockpile -->
            <div class="col-lg-4 mb-4">
                <div class="glass-card h-100 p-4 rounded-xl border-light overflow-hidden position-relative">
                    <div class="card-glow"></div>
                    <h5 class="text-white font-weight-bold mb-4 d-flex align-items-center">
                        <i class="fas fa-warehouse mr-2 text-amber"></i> Stockpile
                    </h5>
                    
                    <div class="resource-item mb-4 animate-slide-in-right">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-platinum-50 small text-uppercase">Raw Timber Logs</span>
                            <span class="badge badge-amber-soft" id="res-logs-badge"><?php echo number_format($wallet['wood_logs']); ?> Units</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="res-icon mr-3">
                                <img src="<?php echo app_base_url('themes/default/assets/resources/materials/log.webp'); ?>" width="40">
                            </div>
                            <h2 class="text-white font-weight-black mb-0" id="res-logs"><?php echo number_format($wallet['wood_logs']); ?></h2>
                        </div>
                    </div>

                    <div class="resource-item animate-slide-in-right" style="animation-delay: 0.1s;">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-platinum-50 small text-uppercase">Polished Planks</span>
                            <span class="badge badge-success-soft" id="res-planks-badge"><?php echo number_format($wallet['wood_planks']); ?> Units</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="res-icon mr-3">
                                <img src="<?php echo app_base_url('themes/default/assets/resources/materials/plank.webp'); ?>" width="40">
                            </div>
                            <h2 class="text-white font-weight-black mb-0 text-success-glow" id="res-planks"><?php echo number_format($wallet['wood_planks']); ?></h2>
                        </div>
                    </div>

                    <div class="mt-4 pt-4 border-top border-light-20">
                        <div class="alert alert-amber-glass small text-platinum-50 border-0 mb-0">
                            <i class="fas fa-info-circle mr-2"></i> Convert logs to planks to generate architectural materials and profit.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Processing Unit -->
            <div class="col-lg-8 mb-4">
                <div class="glass-card h-100 rounded-xl border-light overflow-hidden">
                    <div class="card-header-premium p-4 d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="text-white font-weight-bold mb-0">Wood Processing Terminal</h4>
                            <span class="text-platinum-50 small">Advanced Sawmill v4.2 &bull; Industrial Mode</span>
                        </div>
                        <div class="status-indicator d-flex align-items-center">
                            <span class="pulse-dot mr-2"></span>
                            <span class="text-success small font-weight-bold text-uppercase">Optimal</span>
                        </div>
                    </div>

                    <div class="p-5">
                        <div class="row align-items-center">
                            <div class="col-md-5">
                                <div class="processing-unit-box p-4 rounded-xl text-center animate-glow-wood">
                                    <div class="mb-3 position-relative">
                                        <div class="icon-ring"></div>
                                        <img src="<?php echo app_base_url('themes/default/assets/resources/materials/log.webp'); ?>" class="img-fluid resource-preview" style="max-height: 120px;">
                                    </div>
                                    <h6 class="text-white text-uppercase letter-spacing-1 font-weight-bold">Input Material</h6>
                                    <div class="text-platinum-50 small">Premium Timber</div>
                                </div>
                            </div>

                            <div class="col-md-2 text-center py-4">
                                <div class="action-chain">
                                    <div class="chain-dot"></div>
                                    <div class="chain-line"></div>
                                    <div class="chain-icon shadow-gold"><i class="fas fa-cog fa-spin text-amber"></i></div>
                                    <div class="chain-line"></div>
                                    <div class="chain-dot"></div>
                                </div>
                            </div>

                            <div class="col-md-5">
                                <div class="processing-unit-box p-4 rounded-xl text-center animate-glow-plank">
                                    <div class="mb-3 position-relative">
                                        <div class="icon-ring ring-success"></div>
                                        <img src="<?php echo app_base_url('themes/default/assets/resources/materials/plank_bundle.webp'); ?>" class="img-fluid resource-preview" style="max-height: 120px;">
                                    </div>
                                    <h6 class="text-white text-uppercase letter-spacing-1 font-weight-bold">Output Result</h6>
                                    <div class="text-success small font-weight-bold">High-Grade Planks (x4)</div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-5 text-center">
                            <div class="row justify-content-center">
                                <div class="col-md-8">
                                    <div class="quantity-selector-premium mb-4">
                                        <button class="qty-btn" id="qty-minus"><i class="fas fa-minus"></i></button>
                                        <div class="qty-input-wrapper">
                                            <input type="number" id="craft-qty" class="qty-input" value="1" min="1">
                                            <span class="qty-label text-platinum-50">Units</span>
                                        </div>
                                        <button class="qty-btn" id="qty-plus"><i class="fas fa-plus"></i></button>
                                    </div>

                                    <button class="btn btn-premium-amber w-100 py-4 rounded-pill shadow-gold animate-hover-scale" id="btn-craft">
                                        <span class="btn-text font-weight-black letter-spacing-2">INITIATE OPERATIONS</span>
                                        <i class="fas fa-industry ml-2"></i>
                                    </button>
                                    
                                    <div class="mt-3 text-platinum-50 small">
                                        Required: <span class="text-white" id="req-logs">1</span> Log, <span class="text-amber" id="req-coins">10</span> Coins
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* PREMIUM DESIGN SYSTEM */
@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800;900&display=swap');

.premium-sawmill-wrapper {
    font-family: 'Outfit', sans-serif;
    background: radial-gradient(circle at top right, #1a1c2c, #0d0e14);
    min-height: 100vh;
    color: #e2e8f0;
}

.text-platinum { color: #e2e8f0; }
.text-platinum-50 { color: rgba(226, 232, 240, 0.5); }
.text-amber { color: #ffbf00; }
.font-weight-black { font-weight: 900; }
.letter-spacing-1 { letter-spacing: 1px; }
.letter-spacing-2 { letter-spacing: 2px; }
.rounded-xl { border-radius: 24px; }

/* GLOBALS & UTILS */
.glass-card {
    background: rgba(255, 255, 255, 0.03);
    backdrop-filter: blur(12px);
    border: 1px solid rgba(255, 255, 255, 0.08);
}

.glass-card-dark {
    background: rgba(0, 0, 0, 0.4);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 191, 0, 0.3);
}

.glow-text {
    text-shadow: 0 0 20px rgba(255, 255, 255, 0.2);
}

.divider-vertical {
    width: 1px;
    height: 40px;
    background: rgba(255, 255, 255, 0.1);
}

/* CARDS & PANELS */
.icon-box-premium {
    background: linear-gradient(135deg, rgba(255,191,0,0.2), rgba(255,191,0,0.05));
    padding: 15px;
    border-radius: 20px;
    border: 1px solid rgba(255, 191, 0, 0.3);
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
}

.res-icon {
    background: rgba(0,0,0,0.3);
    padding: 10px;
    border-radius: 12px;
    border: 1px solid rgba(255,255,255,0.05);
}

.badge-amber-soft {
    background: rgba(255, 191, 0, 0.1);
    color: #ffbf00;
    border: 1px solid rgba(255, 191, 0, 0.2);
}

.badge-success-soft {
    background: rgba(40, 167, 69, 0.1);
    color: #28a745;
    border: 1px solid rgba(40, 167, 69, 0.2);
}

.text-success-glow {
    color: #4ade80;
    text-shadow: 0 0 15px rgba(74, 222, 128, 0.3);
}

.card-header-premium {
    background: rgba(255, 255, 255, 0.02);
    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
}

/* PROCESSING UNIT */
.processing-unit-box {
    background: rgba(0,0,0,0.2);
    border: 1px solid rgba(255,255,255,0.05);
    transition: all 0.4s ease;
}

.icon-ring {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 140px;
    height: 140px;
    border: 2px dashed rgba(255, 191, 0, 0.2);
    border-radius: 50%;
    animation: rotate 10s linear infinite;
}

.ring-success { border-color: rgba(40, 167, 69, 0.2); }

.resource-preview {
    position: relative;
    z-index: 2;
}

.action-chain {
    display: flex;
    flex-direction: column;
    align-items: center;
}

.chain-line {
    width: 2px;
    height: 30px;
    background: linear-gradient(to bottom, transparent, rgba(255,191,0,0.3), transparent);
}

.chain-dot {
    width: 6px;
    height: 6px;
    background: #ffbf00;
    border-radius: 50%;
    box-shadow: 0 0 10px #ffbf00;
}

.chain-icon {
    width: 50px;
    height: 50px;
    background: rgba(0,0,0,0.5);
    border: 1px solid #ffbf00;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
}

/* QUANTITY SELECTOR */
.quantity-selector-premium {
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(0,0,0,0.3);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 50px;
    padding: 10px;
    max-width: 300px;
    margin: 0 auto;
}

.qty-btn {
    background: rgba(255, 255, 255, 0.05);
    border: none;
    color: white;
    width: 45px;
    height: 45px;
    border-radius: 50%;
    transition: all 0.2s;
    cursor: pointer;
}

.qty-btn:hover { background: rgba(255, 191, 0, 0.2); color: #ffbf00; }

.qty-input-wrapper {
    flex: 1;
    text-align: center;
}

.qty-input {
    background: transparent;
    border: none;
    color: white;
    font-size: 1.8rem;
    font-weight: 800;
    width: 80px;
    text-align: center;
    outline: none;
}

.qty-label { font-size: 0.7rem; text-transform: uppercase; font-weight: 700; display: block; }

/* BUTTONS */
.btn-premium-amber {
    background: linear-gradient(135deg, #ffbf00, #d49c00);
    border: none;
    color: #000;
    font-size: 1.1rem;
    position: relative;
    overflow: hidden;
}

.btn-premium-amber:hover {
    background: linear-gradient(135deg, #ffcf33, #ffbf00);
    color: #000;
    box-shadow: 0 15px 40px rgba(255, 191, 0, 0.4);
}

.shadow-gold { box-shadow: 0 10px 30px rgba(255, 191, 0, 0.2); }

/* ANIMATIONS */
@keyframes rotate { from { transform: translate(-50%, -50%) rotate(0deg); } to { transform: translate(-50%, -50%) rotate(360deg); } }

.animate-pulse-slow { animation: pulse 4s infinite ease-in-out; }
@keyframes pulse { 0%, 100% { opacity: 1; transform: scale(1); } 50% { opacity: 0.8; transform: scale(0.98); } }

.animate-glow-wood { animation: glow-wood 4s infinite ease-in-out; }
@keyframes glow-wood { 0%, 100% { border-color: rgba(255, 191, 0, 0.1); box-shadow: none; } 50% { border-color: rgba(255, 191, 0, 0.3); box-shadow: inset 0 0 30px rgba(255, 191, 0, 0.1); } }

.animate-glow-plank { animation: glow-plank 4s infinite ease-in-out; animation-delay: 2s; }
@keyframes glow-plank { 0%, 100% { border-color: rgba(40, 167, 69, 0.1); box-shadow: none; } 50% { border-color: rgba(40, 167, 69, 0.3); box-shadow: inset 0 0 30px rgba(40, 167, 69, 0.1); } }

.animate-slide-in-right { animation: slideInRight 0.8s backwards ease-out; }
@keyframes slideInRight { from { transform: translateX(30px); opacity: 0; } to { transform: translateX(0); opacity: 1; } }

.animate-hover-scale { transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
.animate-hover-scale:hover { transform: scale(1.03) translateY(-5px); }

.pulse-dot {
    width: 8px;
    height: 8px;
    background: #28a745;
    border-radius: 50%;
    box-shadow: 0 0 8px #28a745;
    animation: pulse-dot 2s infinite;
}

@keyframes pulse-dot { 0% { transform: scale(1); opacity: 1; } 50% { transform: scale(1.5); opacity: 0.5; } 100% { transform: scale(1); opacity: 1; } }

/* Hide Scroll Arrows from Chrome/Safari */
input::-webkit-outer-spin-button, input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const qtyInput = document.getElementById('craft-qty');
    const btnPlus = document.getElementById('qty-plus');
    const btnMinus = document.getElementById('qty-minus');
    const reqLogs = document.getElementById('req-logs');
    const reqCoins = document.getElementById('req-coins');
    const btnCraft = document.getElementById('btn-craft');

    function updateRequirements() {
        const qty = parseInt(qtyInput.value) || 1;
        reqLogs.innerText = qty;
        reqCoins.innerText = qty * 10;
    }

    btnPlus.addEventListener('click', () => {
        qtyInput.value = parseInt(qtyInput.value) + 1;
        updateRequirements();
    });

    btnMinus.addEventListener('click', () => {
        if (parseInt(qtyInput.value) > 1) {
            qtyInput.value = parseInt(qtyInput.value) - 1;
            updateRequirements();
        }
    });

    qtyInput.addEventListener('input', updateRequirements);

    btnCraft.addEventListener('click', async function() {
        const qty = qtyInput.value;
        const btn = this;
        const originalText = btn.innerHTML;
        
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> PROCESSING...';
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
                // Trigger success visual effect if possible, or just reload
                Swal.fire({
                    title: 'Production Complete!',
                    text: data.message,
                    icon: 'success',
                    background: '#1a1c2c',
                    color: '#e2e8f0',
                    confirmButtonColor: '#ffbf00'
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    title: 'Refining Failed',
                    text: data.message,
                    icon: 'error',
                    background: '#1a1c2c',
                    color: '#e2e8f0'
                });
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        } catch (e) {
            Swal.fire({
                title: 'Operation Timeout',
                text: 'Sawmill nexus is unreachable.',
                icon: 'warning',
                background: '#1a1c2c',
                color: '#e2e8f0'
            });
            btn.innerHTML = originalText;
            btn.disabled = false;
        }
    });
});
</script>
