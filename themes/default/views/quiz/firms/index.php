<div class="firm-page">
    <div class="firm-hero container">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <div class="firm-pill">Alliances</div>
                <h1 class="firm-title">Engineering Firms</h1>
                <p class="firm-subtitle">Form elite crews, pool resources, and unlock mega projects with premium-grade collaboration.</p>
                <div class="firm-stats">
                    <div><span class="firm-stat-label">Security</span><span class="firm-stat-value"><i class="fas fa-lock mr-1"></i>Nonce + Honeypot</span></div>
                    <div><span class="firm-stat-label">Onboarding</span><span class="firm-stat-value"><i class="fas fa-user-plus mr-1"></i>Request-based</span></div>
                    <div><span class="firm-stat-label">Economy</span><span class="firm-stat-value"><i class="fas fa-coins mr-1"></i>Server-validated</span></div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="firm-card glass">
                    <div class="d-flex align-items-center mb-3">
                        <div class="firm-icon-circle"><i class="fas fa-plus"></i></div>
                        <div class="ml-3">
                            <div class="firm-kicker">Create a Firm</div>
                            <div class="firm-card-title">Founder's License</div>
                        </div>
                    </div>
                    <p class="firm-muted mb-3">Cost: 5,000 Coins · Launch a secure firm and start building together.</p>
                    <form action="/api/firms/create" method="POST">
                        <input type="hidden" name="nonce" value="<?php echo htmlspecialchars($createNonce ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        <input type="text" name="trap_answer" id="firm_create_trap" style="display:none" autocomplete="off">
                        <div class="form-group mb-3">
                            <input type="text" name="name" class="form-control firm-input" placeholder="Firm Name" required>
                        </div>
                        <div class="form-group mb-4">
                            <textarea name="description" class="form-control firm-input" placeholder="Description..." rows="2"></textarea>
                        </div>
                        <button type="submit" class="firm-btn gradient w-100">Create Firm</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="container firm-body">
        <div class="firm-section-header">
            <div>
                <p class="firm-kicker mb-1"><i class="fas fa-shield-alt mr-2"></i>Active Firms</p>
                <h3 class="firm-heading">Discover alliances to join</h3>
            </div>
            <div class="firm-legend">Secure join flow with nonce + honeypot checks.</div>
        </div>

        <div class="row">
            <?php if (empty($firms)): ?>
                <div class="col-12">
                    <div class="firm-empty text-center">
                        <div class="firm-icon-circle"><i class="fas fa-city"></i></div>
                        <h5 class="mt-3">No firms yet</h5>
                        <p class="firm-muted mb-0">Be the pioneer—create the first firm and start recruiting.</p>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($firms as $f): ?>
                <div class="col-lg-6 mb-4">
                    <div class="firm-card h-100">
                        <div class="d-flex align-items-center mb-3">
                            <div class="firm-logo mr-3">
                                <img src="<?php echo $f['logo_url'] ?: 'https://cdn-icons-png.flaticon.com/512/1063/1063376.png'; ?>" alt="logo">
                            </div>
                            <div>
                                <div class="d-flex align-items-center">
                                    <h5 class="mb-0 firm-title-sm"><?php echo htmlspecialchars($f['name']); ?></h5>
                                    <span class="firm-tag ml-2">Lvl <?php echo $f['level']; ?></span>
                                </div>
                                <div class="firm-muted small"><?php echo $f['member_count']; ?> members</div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="firm-badges">
                                <span class="firm-pill soft"><i class="fas fa-users mr-1"></i>Teamplay</span>
                                <span class="firm-pill soft"><i class="fas fa-briefcase mr-1"></i>Projects</span>
                            </div>
                            <button class="firm-btn ghost" onclick="requestJoin(<?php echo $f['id']; ?>)">Request Join</button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    const joinNonce = '<?php echo htmlspecialchars($joinNonce ?? '', ENT_QUOTES, 'UTF-8'); ?>';
    function getTrap() {
        return document.getElementById('firm_create_trap') ? document.getElementById('firm_create_trap').value : '';
    }

    async function requestJoin(guildId) {
        const fd = new FormData();
        fd.append('guild_id', guildId);
        fd.append('nonce', joinNonce);
        fd.append('trap_answer', getTrap());

        try {
            const res = await fetch('/api/firms/join', { method: 'POST', body: fd });
            const data = await res.json();
            alert(data.message);
        } catch (e) {
            alert('Request failed.');
        }
    }
</script>

<style>
@import url('https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&display=swap');
:root {
    --firm-bg: #0b0f1a;
    --firm-card: rgba(255,255,255,0.04);
    --firm-border: rgba(255,255,255,0.08);
    --firm-primary: #7c5dff;
    --firm-accent: #00d1ff;
    --firm-text: #e8ecf2;
    --firm-muted: #9aa4b5;
    --firm-glow: 0 12px 60px rgba(124,93,255,0.25);
    --firm-radius: 16px;
}
.firm-page {
    background: radial-gradient(circle at 15% 10%, rgba(124,93,255,0.08), transparent 30%),
                radial-gradient(circle at 85% 5%, rgba(0,209,255,0.08), transparent 25%),
                linear-gradient(180deg, #0b0f1a 0%, #0a0c14 100%);
    color: var(--firm-text);
    font-family: 'Space Grotesk', 'Inter', system-ui, -apple-system, sans-serif;
    min-height: 100vh;
    padding: 40px 0 70px;
}
.firm-hero { margin-bottom: 30px; }
.firm-pill {
    display: inline-flex;
    padding: 6px 12px;
    border-radius: 999px;
    background: rgba(255,255,255,0.08);
    font-weight: 700;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    font-size: 12px;
}
.firm-title { font-size: 40px; font-weight: 700; margin: 10px 0 6px; letter-spacing: -0.02em; }
.firm-subtitle { color: var(--firm-muted); max-width: 560px; font-size: 16px; }
.firm-stats { display: flex; gap: 16px; flex-wrap: wrap; margin-top: 14px; }
.firm-stat-label { display: block; color: var(--firm-muted); font-size: 12px; letter-spacing: 0.05em; text-transform: uppercase; }
.firm-stat-value { color: var(--firm-text); font-weight: 700; }

.firm-card {
    background: var(--firm-card);
    border: 1px solid var(--firm-border);
    border-radius: var(--firm-radius);
    padding: 18px;
    box-shadow: var(--firm-glow);
    color: var(--firm-text);
}
.firm-card.glass { backdrop-filter: blur(10px); }
.firm-kicker { color: var(--firm-muted); text-transform: uppercase; font-size: 12px; letter-spacing: 0.06em; font-weight: 700; }
.firm-card-title { font-weight: 700; font-size: 22px; }
.firm-muted { color: var(--firm-muted); }
.firm-input {
    background: rgba(255,255,255,0.06);
    border: 1px solid var(--firm-border);
    color: var(--firm-text);
    border-radius: 12px;
    padding: 12px 14px;
}
.firm-input::placeholder { color: rgba(255,255,255,0.5); }

.firm-btn {
    border: none;
    border-radius: 12px;
    padding: 12px 16px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    transition: transform 0.15s ease, box-shadow 0.2s ease;
}
.firm-btn.gradient { background: linear-gradient(90deg, var(--firm-primary), var(--firm-accent)); color: #0b0f1a; box-shadow: var(--firm-glow); }
.firm-btn.ghost { background: transparent; border: 1px solid var(--firm-border); color: var(--firm-text); }
.firm-btn:hover { transform: translateY(-1px); }
.firm-btn:active { transform: translateY(0); }

.firm-icon-circle {
    width: 44px; height: 44px;
    border-radius: 50%;
    background: rgba(255,255,255,0.08);
    display: grid; place-items: center;
    color: var(--firm-text);
    font-size: 18px;
}
.firm-logo img { width: 52px; height: 52px; object-fit: cover; border-radius: 12px; border: 1px solid var(--firm-border); }
.firm-title-sm { font-weight: 700; }
.firm-tag { background: rgba(255,255,255,0.08); color: var(--firm-text); border-radius: 8px; padding: 4px 8px; font-weight: 700; font-size: 12px; }
.firm-pill.soft { background: rgba(255,255,255,0.06); color: var(--firm-muted); padding: 6px 10px; border-radius: 999px; margin-right: 6px; font-weight: 600; font-size: 12px; }
.firm-badges { display: flex; flex-wrap: wrap; gap: 6px; }

.firm-body { margin-top: 10px; }
.firm-section-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px; }
.firm-heading { margin: 0; font-weight: 700; }
.firm-legend { color: var(--firm-muted); font-size: 13px; }
.firm-empty { padding: 40px; border: 1px dashed var(--firm-border); border-radius: var(--firm-radius); }

@media (max-width: 992px) {
    .firm-title { font-size: 32px; }
    .firm-subtitle { font-size: 15px; }
    .firm-page { padding: 30px 0 50px; }
}
@media (max-width: 768px) {
    .firm-section-header { flex-direction: column; align-items: flex-start; gap: 6px; }
    .firm-stats { flex-direction: column; gap: 10px; }
    .firm-card { padding: 16px; }
    .firm-btn { width: 100%; }
}
</style>
