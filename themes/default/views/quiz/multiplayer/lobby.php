
<div class="lobby-premium">
    <!-- Lobby Header -->
    <header class="lobby-header">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-6">
                    <a href="<?php echo app_base_url('quiz/multiplayer'); ?>" class="back-link">
                        <i class="fas fa-arrow-left"></i> <span>Exit Lobby</span>
                    </a>
                </div>
                <div class="col-6 text-right">
                    <div class="status-indicator">
                        <span class="status-dot"></span>
                        <span class="status-text" id="connection-status">Connected</span>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="container lobby-content">
        <!-- Room Info -->
        <div class="room-info-section text-center mb-5">
            <div class="room-label">ROOM CODE</div>
            <h1 class="room-code-display" id="room-code"><?php echo htmlspecialchars($code); ?></h1>
            <button class="btn-copy-code" onclick="copyRoomCode()">
                <i class="fas fa-copy"></i> Copy Code
            </button>
        </div>

        <!-- WAITING ROOM STATE -->
        <div id="waiting-room">
            <div class="row">
                <!-- Left Column: Participants -->
                <div class="col-lg-8">
                    <div class="glass-panel h-100">
                        <div class="panel-header">
                            <h3 class="panel-title">
                                <i class="fas fa-users text-primary-gradient"></i> 
                                Agents in Lobby (<span id="player-count">0</span>)
                            </h3>
                        </div>
                        <div class="panel-body">
                            <div class="participants-grid" id="participants-list">
                                <!-- Injected via JS -->
                                <div class="loading-state">
                                    <div class="spinner"></div>
                                    <p>Waiting for agents to join...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Game State & Actions -->
                <div class="col-lg-4">
                    <!-- TIMER / STATUS CARD -->
                    <div class="glass-panel mb-4 text-center highlight-panel">
                        <div class="panel-body">
                            <div class="timer-label">MISSION STARTS IN</div>
                            <div class="countdown-display" id="countdown-timer">--</div>
                            <div class="host-controls" id="host-controls" style="display:none;">
                                <button class="btn-premium btn-block" disabled>
                                    <span class="btn-content">
                                        <i class="fas fa-rocket"></i> Launching...
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- WAGER CARD -->
                    <div class="glass-panel">
                        <div class="panel-header">
                            <h4 class="panel-title"><i class="fas fa-coins text-warning"></i> Place Wager</h4>
                        </div>
                        <div class="panel-body">
                            <?php if (($participant['wager_amount'] ?? 0) <= 0): ?>
                            <div class="wager-options">
                                <p class="wager-hint">Double your coins if you place Top 3!</p>
                                <div class="wager-buttons">
                                    <button class="btn-wager" data-amt="50">
                                        <span class="coin-icon">🪙</span> 50
                                    </button>
                                    <button class="btn-wager" data-amt="100">
                                        <span class="coin-icon">💰</span> 100
                                    </button>
                                    <button class="btn-wager" data-amt="500">
                                        <span class="coin-icon">💎</span> 500
                                    </button>
                                </div>
                            </div>
                            <?php else: ?>
                            <div class="wager-active">
                                <div class="wager-amount">
                                    <span class="amount"><?php echo $participant['wager_amount']; ?></span>
                                    <span class="currency">COINS</span>
                                </div>
                                <div class="wager-status">
                                    <i class="fas fa-check-circle"></i> LOCKED IN
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- GAME ARENA STATE (Initially Hidden) -->
        <div id="game-arena" style="display:none;">
            <div class="row">
                <!-- Question Area -->
                <div class="col-lg-8">
                    <div class="question-card glass-panel">
                        <div class="question-header">
                            <div class="q-badge" id="q-number">Q1</div>
                            <div class="q-timer-bar">
                                <div class="q-progress" id="q-progress"></div>
                            </div>
                            <div class="q-time-text" id="q-timer">20s</div>
                        </div>
                        
                        <div class="question-content">
                            <h2 class="question-text" id="q-text">Decrypting mission objective...</h2>
                        </div>
                        
                        <div class="options-grid" id="q-options">
                            <!-- Options injected via JS -->
                        </div>
                        
                        <!-- Panic Buttons (Lifelines) -->
                        <div class="lifeline-bar" id="panic-buttons">
                            <button class="btn-lifeline" data-type="50_50" id="btn-50_50">
                                <div class="lifeline-icon"><i class="fas fa-divide"></i></div>
                                <div class="lifeline-info">
                                    <span class="name">50/50</span>
                                    <span class="count-badge">0</span>
                                </div>
                            </button>
                            <button class="btn-lifeline" data-type="ai_hint" id="btn-ai_hint">
                                <div class="lifeline-icon"><i class="fas fa-robot"></i></div>
                                <div class="lifeline-info">
                                    <span class="name">AI Hint</span>
                                    <span class="count-badge">0</span>
                                </div>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Live Leaderboard -->
                <div class="col-lg-4">
                    <div class="glass-panel h-100">
                        <div class="panel-header">
                            <h3 class="panel-title">
                                <i class="fas fa-trophy text-warning"></i> Live Rankings
                            </h3>
                        </div>
                        <div class="panel-body p-0">
                            <div class="live-rankings" id="live-leaderboard">
                                <!-- Injected via JS -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Event Feed Overlay -->
    <div class="event-feed-container" id="event-feed"></div>

    <!-- Background Elements -->
    <div class="bg-orb orb-1"></div>
    <div class="bg-orb orb-2"></div>
    <div class="grid-overlay"></div>
</div>

<style>
/* ============================================
   PREMIUM LOBBY STYLES
   ============================================ */
:root {
    --lobby-bg: #0f172a;
    --glass-bg: rgba(255, 255, 255, 0.03);
    --glass-border: rgba(255, 255, 255, 0.08);
    --primary-grad: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --accent-grad: linear-gradient(135deg, #00f2fe 0%, #4facfe 100%);
    --text-primary: #ffffff;
    --text-secondary: #94a3b8;
}

.lobby-premium {
    background: var(--lobby-bg);
    min-height: 100vh;
    color: var(--text-primary);
    font-family: 'Inter', system-ui, sans-serif;
    position: relative;
    overflow-x: hidden;
    padding-bottom: 50px;
}

/* Background Effects */
.bg-orb {
    position: fixed;
    border-radius: 50%;
    filter: blur(100px);
    z-index: 0;
    opacity: 0.3;
}
.orb-1 { width: 400px; height: 400px; background: #764ba2; top: -100px; left: -100px; animation: float 10s infinite alternate; }
.orb-2 { width: 300px; height: 300px; background: #4facfe; bottom: -50px; right: -50px; animation: float 8s infinite alternate-reverse; }
.grid-overlay {
    position: fixed;
    top: 0; left: 0; width: 100%; height: 100%;
    background-image: linear-gradient(var(--glass-border) 1px, transparent 1px),
    linear-gradient(90deg, var(--glass-border) 1px, transparent 1px);
    background-size: 50px 50px;
    opacity: 0.05;
    z-index: 0;
    pointer-events: none;
}

.lobby-content, .lobby-header { position: relative; z-index: 2; }

/* Header */
.lobby-header { padding: 20px 0; margin-bottom: 20px; }
.back-link {
    color: var(--text-secondary);
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-weight: 600;
    transition: color 0.3s;
}
.back-link:hover { color: white; }
.status-indicator {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: rgba(16, 185, 129, 0.1);
    padding: 6px 12px;
    border-radius: 20px;
    border: 1px solid rgba(16, 185, 129, 0.2);
}
.status-dot {
    width: 8px; height: 8px; background: #10b981; border-radius: 50%;
    box-shadow: 0 0 10px #10b981;
    animation: pulse-dot 2s infinite;
}
.status-text { color: #10b981; font-weight: 700; font-size: 0.85rem; text-transform: uppercase; }

/* Room Info */
.room-label {
    letter-spacing: 0.2em;
    font-size: 0.9rem;
    color: var(--text-secondary);
    margin-bottom: 10px;
    font-weight: 600;
}
.room-code-display {
    font-size: 5rem;
    font-weight: 900;
    background: var(--primary-grad);
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
    margin: 0;
    line-height: 1;
    text-shadow: 0 10px 30px rgba(118, 75, 162, 0.3);
}
.btn-copy-code {
    background: transparent;
    border: none;
    color: var(--text-secondary);
    font-size: 0.9rem;
    cursor: pointer;
    transition: color 0.3s;
    margin-top: 10px;
}
.btn-copy-code:hover { color: white; }

/* Glass Panels */
.glass-panel {
    background: var(--glass-bg);
    border: 1px solid var(--glass-border);
    backdrop-filter: blur(12px);
    border-radius: 24px;
    overflow: hidden;
    transition: transform 0.3s;
}
.highlight-panel {
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1));
    border-color: rgba(102, 126, 234, 0.3);
}
.panel-header {
    padding: 20px 25px;
    border-bottom: 1px solid var(--glass-border);
}
.panel-title { margin: 0; font-size: 1.1rem; font-weight: 700; display: flex; align-items: center; gap: 10px; }
.panel-body { padding: 25px; }

/* Participants */
.participants-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
    gap: 15px;
}
.player-card {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 16px;
    padding: 15px;
    text-align: center;
    transition: all 0.3s;
    border: 1px solid transparent;
}
.player-card:hover { transform: translateY(-5px); border-color: rgba(255,255,255,0.2); background: rgba(255,255,255,0.08); }
.player-avatar {
    width: 60px; height: 60px; border-radius: 50%; margin: 0 auto 10px;
    border: 2px solid rgba(255,255,255,0.1);
    object-fit: cover;
}
.player-name { font-weight: 600; font-size: 0.9rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.player-status { font-size: 0.75rem; color: #10b981; margin-top: 5px; }

/* Countdown */
.timer-label { font-size: 0.8rem; letter-spacing: 0.1em; color: var(--text-secondary); margin-bottom: 5px; }
.countdown-display { font-size: 4rem; font-weight: 700; line-height: 1; }

/* Wager */
.wager-options { text-align: center; }
.wager-hint { font-size: 0.9rem; color: var(--text-secondary); margin-bottom: 15px; }
.wager-buttons { display: flex; gap: 10px; justify-content: center; }
.btn-wager {
    background: rgba(255,255,255,0.05);
    border: 1px solid var(--glass-border);
    color: #f59e0b;
    padding: 10px 15px;
    border-radius: 12px;
    font-weight: 700;
    transition: all 0.2s;
    flex: 1;
}
.btn-wager:hover { background: rgba(245, 158, 11, 0.1); border-color: rgba(245, 158, 11, 0.4); transform: translateY(-2px); }
.wager-active { text-align: center; }
.wager-amount { font-size: 2.5rem; font-weight: 900; color: #f59e0b; line-height: 1; }
.wager-amount .currency { font-size: 0.8rem; display: block; color: var(--text-secondary); letter-spacing: 0.2em; font-weight: normal; margin-top: 5px; }
.wager-status { margin-top: 10px; color: #10b981; font-weight: 600; font-size: 0.9rem; }

/* Game Arena */
.question-card { min-height: 400px; display: flex; flex-direction: column; }
.question-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 30px; }
.q-badge { background: var(--primary-grad); padding: 5px 15px; border-radius: 20px; font-weight: 700; font-size: 0.85rem; }
.q-timer-bar { flex: 1; height: 6px; background: rgba(255,255,255,0.1); border-radius: 3px; margin: 0 20px; overflow: hidden; }
.q-progress { height: 100%; background: #f59e0b; width: 100%; transition: width 1s linear; }
.q-time-text { font-weight: 700; font-variant-numeric: tabular-nums; }
.question-content { flex: 1; margin-bottom: 30px; }
.question-text { font-size: 1.8rem; font-weight: 700; line-height: 1.4; }
.options-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }

/* Lifelines */
.lifeline-bar { display: flex; gap: 15px; padding-top: 20px; border-top: 1px solid var(--glass-border); margin-top: auto; }
.btn-lifeline {
    flex: 1;
    display: flex;
    align-items: center;
    gap: 15px;
    background: rgba(255,255,255,0.05);
    border: 1px solid var(--glass-border);
    border-radius: 12px;
    padding: 12px;
    color: white;
    text-align: left;
    transition: all 0.2s;
}
.btn-lifeline:hover:not(:disabled) { background: rgba(255,255,255,0.1); transform: translateY(-2px); }
.btn-lifeline:disabled { opacity: 0.5; cursor: not-allowed; }
.lifeline-icon { width: 40px; height: 40px; border-radius: 50%; background: rgba(255,255,255,0.1); display: grid; place-items: center; font-size: 1.2rem; }
.lifeline-info { display: flex; flex-direction: column; }
.lifeline-info .name { font-weight: 600; font-size: 0.9rem; }
.lifeline-info .count-badge { font-size: 0.75rem; color: var(--text-secondary); }

/* Live Rankings */
.live-rankings .ranking-item {
    display: flex; align-items: center; justify-content: space-between;
    padding: 15px 20px;
    border-bottom: 1px solid var(--glass-border);
    transition: background 0.2s;
}
.live-rankings .ranking-item:last-child { border-bottom: none; }
.live-rankings .ranking-item:hover { background: rgba(255,255,255,0.02); }
.rank-pos { width: 30px; font-weight: 900; color: var(--text-secondary); }
.rank-user { font-weight: 600; }
.rank-score { color: #10b981; font-weight: 700; }

/* Animations */
@keyframes float { 0% { transform: translateY(0); } 100% { transform: translateY(20px); } }
@keyframes pulse-dot { 0% { opacity: 0.5; box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.4); } 70% { opacity: 1; box-shadow: 0 0 0 10px rgba(16, 185, 129, 0); } 100% { opacity: 0.5; } }

/* Responsive */
@media (max-width: 991px) {
    .options-grid { grid-template-columns: 1fr; }
    .room-code-display { font-size: 3.5rem; }
}
</style>

<!-- JS Logic (Preserving Original Logic) -->
<script>
    const ROOM_CODE = "<?php echo $code; ?>";
    const API_URL = "/api/lobby/" + ROOM_CODE + "/status";
    let wagerNonce = '<?php echo htmlspecialchars($wagerNonce ?? '', ENT_QUOTES, 'UTF-8'); ?>';
    let lifelineNonce = '<?php echo htmlspecialchars($lifelineNonce ?? '', ENT_QUOTES, 'UTF-8'); ?>';
    const trapInput = document.createElement('input');
    trapInput.type = 'text';
    trapInput.name = 'trap_answer';
    trapInput.id = 'lobby_trap';
    trapInput.autocomplete = 'off';
    trapInput.style.display = 'none';
    document.body.appendChild(trapInput);
    
    let currentState = 'waiting';
    
    // Init Pulse
    setInterval(pulse, 1000);
    
    // Copy Code Function
    function copyRoomCode() {
        navigator.clipboard.writeText(ROOM_CODE).then(() => {
            const btn = document.querySelector('.btn-copy-code');
            const original = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-check"></i> Copied!';
            setTimeout(() => btn.innerHTML = original, 2000);
        });
    }

    async function pulse() {
        try {
            const response = await fetch(API_URL);
            const data = await response.json();
            
            if (!data || !data.lobby) return;
            
            updateUI(data);
            document.getElementById('connection-status').innerText = 'Connected';
            document.getElementById('connection-status').className = 'status-text text-success';
            document.querySelector('.status-dot').style.background = '#10b981';
        } catch (e) {
            document.getElementById('connection-status').innerText = 'Reconnecting...';
            document.getElementById('connection-status').className = 'status-text text-danger';
            document.querySelector('.status-dot').style.background = '#ef4444';
        }
    }
    
    function updateUI(data) {
        if (data.lobby.status !== currentState) {
            currentState = data.lobby.status;
            if (currentState === 'active') {
                document.getElementById('waiting-room').style.display = 'none';
                document.getElementById('game-arena').style.display = 'block';
                loadInventory();
            }
        }
        
        if (currentState === 'waiting') {
            updateParticipants(data.participants);
            updateTimer(data.time_remaining);
        } else if (currentState === 'active') {
            updateLeaderboard(data.participants);
            // Handle active game state updates (question display, etc.) here
        }
    }
    
    function updateParticipants(users) {
        const container = document.getElementById('participants-list');
        document.getElementById('player-count').innerText = users.length;
        
        let html = '';
        users.forEach(u => {
            html += `
                <div class="player-card">
                    <img src="${u.avatar}" class="player-avatar">
                    <div class="player-name">${u.name}</div>
                    <div class="player-status">${u.is_bot ? '🤖 Ready' : 'Ready'}</div>
                </div>
            `;
        });
        container.innerHTML = html;
    }
    
    function updateTimer(seconds) {
        const el = document.getElementById('countdown-timer');
        el.innerText = seconds > 0 ? seconds : "GO!";
    }
    
    function updateLeaderboard(users) {
        users.sort((a, b) => b.current_score - a.current_score);
        const list = document.getElementById('live-leaderboard');
        let html = '';
        users.forEach((u, index) => {
            html += `
                <div class="ranking-item">
                    <div class="rank-pos">#${index+1}</div>
                    <div class="rank-user">${u.name}</div>
                    <div class="rank-score">${u.current_score}</div>
                </div>
            `;
        });
        list.innerHTML = html;
    }

    // Wager Logic
    document.querySelectorAll('.btn-wager').forEach(btn => {
        btn.addEventListener('click', async function() {
            const amt = this.dataset.amt;
            if (!confirm(`Wager ${amt} coins?`)) return;

            const fd = new FormData();
            fd.append('amount', amt);
            fd.append('lobby_id', '<?php echo $participant["lobby_id"]; ?>');
            fd.append('nonce', wagerNonce);
            fd.append('trap_answer', document.getElementById('lobby_trap').value || '');

            try {
                const res = await fetch('/api/lobby/wager', { method: 'POST', body: fd });
                const data = await res.json();
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message);
                }
            } catch (e) { alert('Error placing wager'); }
        });
    });

    // Lifeline Logic
    async function loadInventory() {
        // Mock inventory loading
        // In real impl, fetch from API or use echo'd data
    }
    
    // ... Additional game logic can be preserved or enhanced ...
</script>
