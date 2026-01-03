<div class="contest-result py-5" style="background: radial-gradient(circle at bottom left, #0f0c29, #302b63, #24243e); min-height: 100vh;">
    <div class="container py-5 text-center">
        <!-- Status Card -->
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="p-5 rounded-5 border border-white border-opacity-10 bg-white bg-opacity-5 backdrop-blur shadow-2xl">
                    
                    <?php if($participant['is_winner'] === 1): ?>
                        <!-- Winner State -->
                        <div class="mb-4 animate-bounce">
                            <i class="fas fa-trophy text-warning display-1"></i>
                        </div>
                        <h1 class="text-white fw-bold mb-3">VICTORY SECURED!</h1>
                        <p class="text-info mb-4">You survived the Lucky Draw. Your rewards have been added to your bank.</p>
                        
                        <div class="bg-success bg-opacity-10 p-4 rounded-4 border border-success border-opacity-20 mb-4">
                            <h2 class="text-success mb-0">+<?= $contest['prize_pool'] / $contest['winner_count'] ?> Coins</h2>
                            <small class="text-success text-uppercase">Champion Reward</small>
                        </div>
                    <?php elseif($participant['is_winner'] === 0): ?>
                        <!-- Loser State -->
                        <div class="mb-4 opacity-50">
                            <i class="fas fa-skull-crossbones text-danger display-1"></i>
                        </div>
                        <h1 class="text-white fw-bold mb-3">BATTLE ENDED</h1>
                        <p class="text-muted mb-4">You fought well, but the Lucky Draw favored others this time.</p>
                        <div class="p-4 rounded-4 bg-white bg-opacity-5 border border-white border-opacity-10 opacity-75">
                            <h3 class="text-white mb-0"><?= $participant['score'] ?> / 10</h3>
                            <small class="text-muted uppercase">Survival Score</small>
                        </div>
                    <?php else: ?>
                        <!-- Waiting State -->
                        <div class="mb-4">
                            <div class="spinner-border text-info" role="status" style="width: 4rem; height: 4rem;">
                                <span class="visually-hidden">Judging...</span>
                            </div>
                        </div>
                        <h2 class="text-white fw-bold mb-3">JUDGING IN PROGRESS</h2>
                        <p class="text-info opacity-75 mb-5">Admin is reviewing battle logs. Stay tuned for the Lucky Draw results!</p>

                        <div class="row g-3 mb-5">
                            <div class="col-6">
                                <div class="p-3 rounded-4 bg-white bg-opacity-5 border border-white border-opacity-10">
                                    <small class="text-muted d-block font-xs text-uppercase">Your Score</small>
                                    <span class="text-white fw-bold fs-4"><?= $participant['score'] ?></span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 rounded-4 bg-white bg-opacity-5 border border-white border-opacity-10">
                                    <small class="text-muted d-block font-xs text-uppercase">Time Taken</small>
                                    <span class="text-warning fw-bold fs-4"><?= gmdate("i:s", $participant['time_taken']) ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="d-grid gap-2">
                        <a href="<?= app_base_url('contests') ?>" class="btn btn-outline-light rounded-pill py-3 fw-bold">
                            <i class="fas fa-arrow-left me-2"></i> RETURN TO LOBBY
                        </a>
                        <a href="<?= app_base_url('quiz/city') ?>" class="btn btn-primary rounded-pill py-3 fw-bold shadow-lg">
                            <i class="fas fa-city me-2"></i> VISIT YOUR CITY
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Meta Info -->
        <div class="mt-5 text-white opacity-25 small">
            <p>Battle Hash: <?= md5($participant['id'] . $contest['id']) ?></p>
        </div>
    </div>
</div>

<style>
.backdrop-blur { backdrop-filter: blur(20px); }
.shadow-2xl { box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); }
.font-xs { font-size: 0.65rem; }
.animate-bounce { animation: bounce 2s infinite; }
@keyframes bounce {
    0%, 100% { transform: translateY(-5%); animation-timing-function: cubic-bezier(0.8, 0, 1, 1); }
    50% { transform: translateY(0); animation-timing-function: cubic-bezier(0, 0, 0.2, 1); }
}
</style>
