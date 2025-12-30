

<div class="mp-page">
    <div class="mp-hero container">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <div class="mp-pill">Realtime Arena</div>
                <h1 class="mp-title">Engineering Battle Royale</h1>
                <p class="mp-subtitle">Join friends or create your own lobby. Server-locked scoring, anti-replay wagers, and premium-grade UX.</p>
                <div class="mp-stats">
                    <div><span class="mp-stat-label">Latency Guard</span><span class="mp-stat-value"><i class="fas fa-shield-alt mr-1"></i>On</span></div>
                    <div><span class="mp-stat-label">Security</span><span class="mp-stat-value"><i class="fas fa-lock mr-1"></i>Nonce + Honeypot</span></div>
                    <div><span class="mp-stat-label">Players</span><span class="mp-stat-value"><i class="fas fa-users mr-1"></i>Live</span></div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="mp-card glass">
                    <div class="d-flex align-items-center mb-3">
                        <div class="mp-icon-circle"><i class="fas fa-search"></i></div>
                        <div class="ml-3">
                            <div class="mp-kicker">Join a battle</div>
                            <div class="mp-card-title">Enter a code</div>
                        </div>
                    </div>
                    <form action="/quiz/lobby/join" method="POST">
                        <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                        <div class="form-group mb-3">
                            <input type="text" name="code" class="form-control mp-input text-uppercase" placeholder="ENTER CODE (e.g. A7X92)" required>
                        </div>
                        <button type="submit" class="mp-btn primary w-100">Join Room</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="container mp-grid">
        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="mp-card h-100">
                    <div class="d-flex align-items-center mb-3">
                        <div class="mp-icon-circle accent"><i class="fas fa-crown"></i></div>
                        <div class="ml-3">
                            <div class="mp-kicker">Host mode</div>
                            <div class="mp-card-title">Create a lobby</div>
                        </div>
                    </div>
                    <p class="mp-muted">Spin up a secure lobby, invite friends, and let the server handle fairness.</p>
                    <form action="/quiz/lobby/create" method="POST" class="mt-3">
                        <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                        <input type="hidden" name="exam_id" value="1">
                        <button type="submit" class="mp-btn gradient w-100">Create New Room</button>
                    </form>
                </div>
            </div>
            <div class="col-lg-6 mb-4">
                <div class="mp-card h-100">
                    <div class="d-flex align-items-center mb-3">
                        <div class="mp-icon-circle warning"><i class="fas fa-bolt"></i></div>
                        <div class="ml-3">
                            <div class="mp-kicker">What you get</div>
                            <div class="mp-card-title">Competitive stack</div>
                        </div>
                    </div>
                    <ul class="mp-list">
                        <li><i class="fas fa-check text-success mr-2"></i>Server-authoritative scoring & wagers</li>
                        <li><i class="fas fa-check text-success mr-2"></i>Nonce + honeypot defenses on wagers</li>
                        <li><i class="fas fa-check text-success mr-2"></i>Real-time pulse updates for lobby status</li>
                        <li><i class="fas fa-check text-success mr-2"></i>Premium glass UI aligned with home page</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@import url('https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&display=swap');
:root {
    --mp-bg: #0b0f1a;
    --mp-card: rgba(255,255,255,0.04);
    --mp-border: rgba(255,255,255,0.08);
    --mp-primary: #7c5dff;
    --mp-accent: #00d1ff;
    --mp-warning: #f7c948;
    --mp-text: #e8ecf2;
    --mp-muted: #9aa4b5;
    --mp-glow: 0 12px 60px rgba(124,93,255,0.25);
    --mp-radius: 16px;
}
.mp-page {
    background: radial-gradient(circle at 20% 20%, rgba(124,93,255,0.08), transparent 35%),
                radial-gradient(circle at 80% 10%, rgba(0,209,255,0.08), transparent 30%),
                linear-gradient(180deg, #0b0f1a 0%, #0a0c14 100%);
    color: var(--mp-text);
    font-family: 'Space Grotesk', 'Inter', system-ui, -apple-system, sans-serif;
    min-height: 100vh;
    padding: 40px 0 70px;
}
.mp-hero { position: relative; margin-bottom: 30px; }
.mp-pill {
    display: inline-flex;
    padding: 6px 12px;
    border-radius: 999px;
    background: rgba(255,255,255,0.08);
    color: var(--mp-text);
    font-weight: 700;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    font-size: 12px;
}
.mp-title { font-size: 42px; font-weight: 700; letter-spacing: -0.02em; margin: 10px 0 6px; }
.mp-subtitle { color: var(--mp-muted); font-size: 16px; max-width: 560px; }
.mp-stats { display: flex; gap: 16px; flex-wrap: wrap; margin-top: 16px; }
.mp-stat-label { display: block; color: var(--mp-muted); font-size: 12px; text-transform: uppercase; letter-spacing: 0.05em; }
.mp-stat-value { color: var(--mp-text); font-weight: 700; }

.mp-card {
    background: var(--mp-card);
    border: 1px solid var(--mp-border);
    border-radius: var(--mp-radius);
    padding: 18px;
    box-shadow: var(--mp-glow);
}
.mp-card.glass {
    backdrop-filter: blur(10px);
    background: rgba(255,255,255,0.04);
}
.mp-kicker { color: var(--mp-muted); text-transform: uppercase; font-size: 12px; letter-spacing: 0.06em; font-weight: 700; }
.mp-card-title { font-weight: 700; font-size: 22px; color: var(--mp-text); }
.mp-muted { color: var(--mp-muted); }
.mp-input {
    background: rgba(255,255,255,0.06);
    border: 1px solid var(--mp-border);
    color: var(--mp-text);
    border-radius: 12px;
    padding: 12px 14px;
}
.mp-input::placeholder { color: rgba(255,255,255,0.5); }

.mp-btn {
    border: none;
    border-radius: 12px;
    padding: 12px 16px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    transition: transform 0.15s ease, box-shadow 0.2s ease;
}
.mp-btn.primary { background: rgba(255,255,255,0.12); color: var(--mp-text); border: 1px solid var(--mp-border); }
.mp-btn.gradient { background: linear-gradient(90deg, var(--mp-primary), var(--mp-accent)); color: #0b0f1a; box-shadow: var(--mp-glow); }
.mp-btn:hover { transform: translateY(-1px); }
.mp-btn:active { transform: translateY(0); }

.mp-icon-circle {
    width: 44px; height: 44px;
    border-radius: 50%;
    background: rgba(255,255,255,0.08);
    display: grid; place-items: center;
    color: var(--mp-text);
    font-size: 18px;
}
.mp-icon-circle.accent { color: var(--mp-primary); }
.mp-icon-circle.warning { color: var(--mp-warning); }

.mp-grid { margin-top: 10px; }
.mp-list { list-style: none; padding-left: 0; margin: 0; }
.mp-list li { color: var(--mp-text); padding: 6px 0; }

@media (max-width: 992px) {
    .mp-title { font-size: 34px; }
    .mp-subtitle { font-size: 15px; }
    .mp-page { padding: 30px 0 50px; }
}
@media (max-width: 768px) {
    .mp-title { font-size: 28px; }
    .mp-stats { flex-direction: column; gap: 10px; }
    .mp-card { padding: 16px; }
}
</style>
