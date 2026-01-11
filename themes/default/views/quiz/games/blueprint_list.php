<div class="blueprint-studio py-5" style="background: radial-gradient(circle at bottom center, #0f172a, #1e293b); min-height: 100vh;">
    <div class="container">
        <div class="text-center mb-5">
            <h1 class="display-4 fw-bold text-white">Architect<span class="text-info animate-pulse">'s</span> Studio</h1>
            <p class="text-secondary lead">Match technical terms to reveal detailed engineering blueprints.</p>
        </div>

        <div class="row g-4">
            <?php foreach($blueprints as $bp): ?>
                <?php $pct = $progress[$bp['id']] ?? 0; ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card border border-white border-opacity-10 shadow-lg rounded-4 overflow-hidden h-100 transition-all hover-translate-y bg-dark bg-opacity-25 backdrop-blur">
                        <div class="position-relative" style="height: 200px; background: rgba(0,0,0,0.3);">
                             <!-- Blueprint Placeholder -->
                             <div class="w-100 h-100 d-flex align-items-center justify-content-center">
                                 <i class="fas fa-pencil-ruler text-info display-4 opacity-50"></i>
                             </div>
                             <?php if($pct >= 100): ?>
                                <div class="position-absolute top-0 end-0 m-3 px-3 py-1 bg-success text-white rounded-pill small fw-bold shadow-sm">
                                    <i class="fas fa-check-circle me-1"></i> COMPLETED
                                </div>
                             <?php endif; ?>
                             
                             <!-- Overlay Gradient -->
                             <div class="position-absolute bottom-0 start-0 w-100 p-3" style="background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);">
                                 <h4 class="fw-bold text-white mb-0"><?= $bp['title'] ?></h4>
                             </div>
                        </div>
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <span class="badge bg-white bg-opacity-10 text-light border border-white border-opacity-10">
                                    <?= $bp['difficulty'] == 1 ? 'Foundational' : 'Advanced' ?>
                                </span>
                                <span class="text-success fw-bold">+<?= $bp['reward'] ?> Coins</span>
                            </div>

                            <div class="mb-3">
                                <div class="d-flex justify-content-between small text-muted mb-1">
                                    <span class="text-secondary">Blueprint Reveal</span>
                                    <span class="text-white"><?= $pct ?>%</span>
                                </div>
                                <div class="progress rounded-pill shadow-none bg-white bg-opacity-10" style="height: 6px;">
                                    <div class="progress-bar bg-info" style="width: <?= $pct ?>%"></div>
                                </div>
                            </div>
                            
                            <a href="<?= app_base_url('/blueprint/arena/'.$bp['id']) ?>" class="btn btn-primary w-100 rounded-pill py-2 fw-bold shadow-lg border-0 bg-gradient-primary text-dark">
                                <?= $pct > 0 ? 'Continue Drafting' : 'Start Drafting' ?>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<style>
.bg-gradient-primary { background: linear-gradient(135deg, #e2e8f0 0%, #cbd5e1 100%); }
.bg-gradient-primary:hover { background: linear-gradient(135deg, #ffffff 0%, #e2e8f0 100%); transform: translateY(-2px); }
.backdrop-blur { backdrop-filter: blur(10px); }
.hover-translate-y:hover { transform: translateY(-10px); border-color: rgba(56, 189, 248, 0.5) !important; }
.transition-all { transition: all 0.3s ease; }
.animate-pulse { animation: pulse 2s infinite; }
@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.5; }
    100% { opacity: 1; }
}
</style>
