<div class="contest-portal py-5" style="background: radial-gradient(circle at top center, #0f172a, #000000); min-height: 100vh;">
    <div class="container">
        <!-- Header -->
        <div class="text-center mb-5">
            <h1 class="display-4 fw-bold text-white mb-2" style="letter-spacing: -1px;">
                <i class="fas fa-bolt text-warning me-2 animate-pulse"></i>BATTLE <span class="text-warning">ROYALE</span>
            </h1>
            <p class="text-info opacity-75 fw-light">Only the smartest survive the Lucky Draw.</p>
        </div>

        <!-- Stats Row -->
        <div class="row g-4 mb-5 justify-content-center">
            <div class="col-md-4">
                <div class="p-4 rounded-4 border border-light border-opacity-10 bg-white bg-opacity-5 backdrop-blur text-center h-100">
                    <small class="text-muted d-block mb-1 uppercase tracking-wider">Your Balance</small>
                    <h3 class="text-warning mb-0 display-6 fw-bold"><i class="fas fa-coins me-2"></i><?= number_format($user['coins']) ?></h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-4 rounded-4 border border-warning border-opacity-50 bg-warning bg-opacity-10 backdrop-blur text-center h-100 position-relative overflow-hidden">
                    <div class="position-absolute top-0 start-0 w-100 h-100 bg-warning opacity-10 blur-xl"></div>
                    <small class="text-warning d-block mb-1 uppercase tracking-wider fw-bold">Active Warriors</small>
                    <h3 class="text-white mb-0 display-6 fw-bold">1,245</h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-4 rounded-4 border border-light border-opacity-10 bg-white bg-opacity-5 backdrop-blur text-center h-100">
                    <small class="text-muted d-block mb-1 uppercase tracking-wider">Total Prize Pool</small>
                    <h3 class="text-success mb-0 display-6 fw-bold"><i class="fas fa-trophy me-2"></i>50k+</h3>
                </div>
            </div>
        </div>

        <!-- Contests Grid -->
        <div class="row g-4">
            <?php if(empty($contests)): ?>
                <div class="col-12 text-center py-5">
                    <div class="p-5 rounded-5 border border-white border-opacity-10 bg-white bg-opacity-5 mx-auto" style="max-width: 600px;">
                        <div class="opacity-50 mb-4">
                            <i class="fas fa-ghost display-1 text-white"></i>
                        </div>
                        <h3 class="text-white fw-bold mb-3">The Arena is Quiet</h3>
                        <p class="text-muted mb-4 lead">No active battles right now. Sharpen your skills in the Training Grounds while you wait.</p>
                        <a href="<?= app_base_url('/blueprint') ?>" class="btn btn-outline-light rounded-pill px-4 py-2 fw-bold hover-scale">
                            <i class="fas fa-drafting-compass me-2"></i> Visit Blueprint Studio
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach($contests as $contest): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="contest-card h-100 p-4 rounded-4 border border-light border-opacity-10 bg-white bg-opacity-5 backdrop-blur transition-all hover-translate-y">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <span class="badge rounded-pill bg-warning text-dark px-3 py-2 fw-bold shadow-sm animate-pulse">
                                    LIVE NOW
                                </span>
                                <div class="text-end">
                                    <small class="text-muted d-block">Entry Fee</small>
                                    <span class="text-warning fw-bold"><?= $contest['entry_fee'] ?> Coins</span>
                                </div>
                            </div>
                            
                            <h4 class="text-white mb-3"><?= htmlspecialchars($contest['title']) ?></h4>
                            <p class="text-muted small mb-4 line-clamp-2"><?= htmlspecialchars($contest['description']) ?></p>

                            <div class="row g-2 mb-4">
                                <div class="col-6">
                                    <div class="p-2 rounded-3 bg-white bg-opacity-5 text-center border border-white border-opacity-10">
                                        <small class="text-muted d-block font-xs uppercase">Prize Pool</small>
                                        <span class="text-success fw-bold"><?= $contest['prize_pool'] ?> Coins</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-2 rounded-3 bg-white bg-opacity-5 text-center border border-white border-opacity-10">
                                        <small class="text-muted d-block font-xs uppercase">Winners</small>
                                        <span class="text-white fw-bold"><?= $contest['winner_count'] ?> Slot<?= $contest['winner_count'] > 1 ? 's' : '' ?></span>
                                    </div>
                                </div>
                            </div>

                            <form action="<?= app_base_url('contest/join/'.$contest['id']) ?>" method="POST">
                                <button type="submit" class="btn btn-primary w-100 rounded-pill py-3 fw-bold text-uppercase tracking-wider shadow-lg hover-glow">
                                    Enter Battle <i class="fas fa-rocket ms-2"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.backdrop-blur { backdrop-filter: blur(10px); }
.hover-translate-y:hover { transform: translateY(-10px); border-color: rgba(245, 158, 11, 0.5) !important; } /* Warning color border on hover */
.transition-all { transition: all 0.3s ease; }
.contest-card { position: relative; overflow: hidden; }
.contest-card::before { 
    content: ''; position: absolute; top: 0; left: 0; 
    width: 100%; height: 4px; background: linear-gradient(90deg, #f59e0b, #fbbf24); 
    opacity: 0.8;
}
.font-xs { font-size: 0.65rem; }
.animate-pulse { animation: pulse 2s infinite; }
.hover-scale:hover { transform: scale(1.05); }
.hover-glow:hover { box-shadow: 0 0 20px rgba(245, 158, 11, 0.4); }
.blur-xl { filter: blur(40px); }

@keyframes pulse {
    0% { transform: scale(1); opacity: 1; }
    50% { transform: scale(1.05); opacity: 0.8; }
    100% { transform: scale(1); opacity: 1; }
}
</style>
