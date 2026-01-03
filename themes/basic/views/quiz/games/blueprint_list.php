<div class="blueprint-studio py-5" style="background: #f8fafc; min-height: 100vh;">
    <div class="container">
        <div class="text-center mb-5">
            <h1 class="display-4 fw-bold text-dark">Architect<span class="text-primary">'s</span> Studio</h1>
            <p class="text-muted lead">Match technical terms to reveal detailed engineering blueprints.</p>
        </div>

        <div class="row g-4">
            <?php foreach($blueprints as $bp): ?>
                <?php $pct = $progress[$bp['id']] ?? 0; ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100 transition-all hover-shadow">
                        <div class="position-relative bg-light" style="height: 200px;">
                             <!-- Blueprint Placeholder -->
                             <div class="w-100 h-100 d-flex align-items-center justify-content-center bg-primary bg-opacity-10">
                                 <i class="fas fa-pencil-ruler text-primary display-4 opacity-50"></i>
                             </div>
                             <?php if($pct >= 100): ?>
                                <div class="position-absolute top-0 end-0 m-3 px-3 py-1 bg-success text-white rounded-pill small fw-bold shadow-sm">
                                    <i class="fas fa-check-circle me-1"></i> COMPLETED
                                </div>
                             <?php endif; ?>
                        </div>
                        <div class="card-body p-4">
                            <h4 class="fw-bold mb-2"><?= $bp['title'] ?></h4>
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <span class="badge bg-light text-muted border"><?= $bp['difficulty'] == 1 ? 'Foundational' : 'Advanced' ?></span>
                                <span class="text-success fw-bold">+<?= $bp['reward'] ?> Coins</span>
                            </div>

                            <div class="mb-3">
                                <div class="d-flex justify-content-between small text-muted mb-1">
                                    <span>Blueprint Reveal</span>
                                    <span><?= $pct ?>%</span>
                                </div>
                                <div class="progress rounded-pill shadow-none" style="height: 6px;">
                                    <div class="progress-bar bg-primary" style="width: <?= $pct ?>%"></div>
                                </div>
                            </div>
                            
                            <a href="<?= app_base_url('/blueprint/arena/'.$bp['id']) ?>" class="btn btn-primary w-100 rounded-pill py-2 fw-bold shadow-sm">
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
.hover-shadow:hover { 
    transform: translateY(-5px); 
    box-shadow: 0 1rem 3rem rgba(0,0,0,0.1) !important;
}
</style>
