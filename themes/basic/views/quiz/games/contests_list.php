<div class="contest-portal py-5" style="background: radial-gradient(circle at top right, #1a1a2e, #16213e); min-height: 100vh;">
    <div class="container">
        <!-- Header -->
        <div class="text-center mb-5">
            <h1 class="display-4 fw-bold text-white mb-2" style="letter-spacing: -1px;">
                <i class="fas fa-bolt text-warning me-2 animate-pulse"></i>BATTLE <span class="text-warning">ROYALE</span>
            </h1>
            <p class="text-info opacity-75 fw-light">Only the smartest survive the Lucky Draw.</p>
        </div>

        <!-- Stats Row -->
        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="p-4 rounded-4 border border-light border-opacity-10 bg-white bg-opacity-5 backdrop-blur text-center">
                    <small class="text-muted d-block mb-1 uppercase">Your Balance</small>
                    <h3 class="text-warning mb-0"><i class="fas fa-coins me-2"></i><?= number_format($user['coins']) ?></h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-4 rounded-4 border border-light border-opacity-10 bg-white bg-opacity-5 backdrop-blur text-center border-warning">
                    <small class="text-muted d-block mb-1 uppercase">Active Warriors</small>
                    <h3 class="text-white mb-0">1,245</h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-4 rounded-4 border border-light border-opacity-10 bg-white bg-opacity-5 backdrop-blur text-center">
                    <small class="text-muted d-block mb-1 uppercase">Total Prize Pool</small>
                    <h3 class="text-success mb-0"><i class="fas fa-trophy me-2"></i>50k+</h3>
                </div>
            </div>
        </div>

        <!-- Contests Grid -->
        <div class="row g-4">
            <?php if(empty($contests)): ?>
                <div class="col-12 text-center py-5">
                    <div class="opacity-25 mb-4">
                        <i class="fas fa-ghost display-1 text-white"></i>
                    </div>
                    <h4 class="text-white opacity-50">Arena is quiet... for now.</h4>
                    <p class="text-muted">Check back soon for the next AI-generated battle.</p>
                </div>
            <?php else: ?>
                <?php foreach($contests as $contest): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="contest-card h-100 p-4 rounded-4 border border-light border-opacity-10 bg-white bg-opacity-5 backdrop-blur transition-all hover-translate-y">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <span class="badge rounded-pill bg-warning text-dark px-3 py-2 fw-bold shadow-sm">
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
                                    <div class="p-2 rounded-3 bg-white bg-opacity-5 text-center">
                                        <small class="text-muted d-block font-xs">PRIZE POOL</small>
                                        <span class="text-success fw-bold"><?= $contest['prize_pool'] ?> Coins</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-2 rounded-3 bg-white bg-opacity-5 text-center">
                                        <small class="text-muted d-block font-xs">WINNERS</small>
                                        <span class="text-white fw-bold"><?= $contest['winner_count'] ?> Slot<?= $contest['winner_count'] > 1 ? 's' : '' ?></span>
                                    </div>
                                </div>
                            </div>

                            <form action="<?= app_base_url('contest/join/'.$contest['id']) ?>" method="POST">
                                <button type="submit" class="btn btn-primary w-100 rounded-pill py-3 fw-bold text-uppercase tracking-wider shadow-lg">
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
.hover-translate-y:hover { transform: translateY(-10px); border-color: rgba(255,255,255,0.3) !important; }
.transition-all { transition: all 0.3s ease; }
.contest-card { position: relative; overflow: hidden; }
.contest-card::before { 
    content: ''; position: absolute; top: 0; left: 0; 
    width: 100%; height: 4px; background: linear-gradient(90deg, #ff9a9e, #fad0c4); 
    opacity: 0.5;
}
.font-xs { font-size: 0.65rem; }
.animate-pulse { animation: pulse 2s infinite; }
@keyframes pulse {
    0% { transform: scale(1); opacity: 1; }
    50% { transform: scale(1.1); opacity: 0.8; }
    100% { transform: scale(1); opacity: 1; }
}
</style>
