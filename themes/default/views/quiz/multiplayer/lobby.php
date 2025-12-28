

<div class="container py-4">
    <!-- Lobby Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <span class="badge badge-primary">ROOM CODE</span>
            <h1 class="display-4 font-weight-bold" id="room-code"><?php echo htmlspecialchars($code); ?></h1>
        </div>
        <div class="text-right">
            <div id="connection-status" class="text-success"><i class="fas fa-circle"></i> Connected</div>
            <div class="text-muted small">Updated <span id="last-update">0s</span> ago</div>
        </div>
    </div>

    <!-- WAITING ROOM SECTION -->
    <div id="waiting-room">
        <!-- Wager Section -->
        <?php if (($participant['wager_amount'] ?? 0) <= 0): ?>
        <div class="row mb-4" id="wager-section">
            <div class="col-12">
                <div class="card shadow-sm border-warning bg-light">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="font-weight-bold mb-1 text-warning"><i class="fas fa-hand-holding-usd mr-2"></i>Place Your Wager</h5>
                            <p class="text-muted small mb-0">Double your coins if you finish in the Top 3!</p>
                        </div>
                        <div class="d-flex">
                            <button class="btn btn-outline-warning mr-2 btn-wager" data-amt="50">50 <i class="fas fa-coins"></i></button>
                            <button class="btn btn-outline-warning mr-2 btn-wager" data-amt="100">100 <i class="fas fa-coins"></i></button>
                            <button class="btn btn-outline-warning btn-wager" data-amt="500">500 <i class="fas fa-coins"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="row mb-4">
            <div class="col-12 text-center">
                <div class="badge badge-warning p-2 px-3">
                    <i class="fas fa-check-circle mr-1"></i> WAGER PLACED: <?php echo $participant['wager_amount']; ?> COINS
                </div>
            </div>
        </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-8">
                <div class="card shadow-sm border-0 mb-3">
                    <div class="card-body">
                        <h4 class="mb-3">Participants (<span id="player-count">0</span>)</h4>
                        <div class="row" id="participants-list">
                            <!-- Players injected here via JS -->
                            <div class="col-12 text-center py-5 text-muted">
                                <i class="fas fa-spinner fa-spin fa-2x"></i><br>Waiting for players...
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card shadow-lg border-primary h-100">
                    <div class="card-body text-center d-flex flex-column justify-content-center">
                        <h5 class="text-muted mb-4">Game Starts In</h5>
                        <h2 class="display-3 font-weight-bold text-primary mb-4" id="countdown-timer">--</h2>
                        
                        <div class="alert alert-info small">
                            <i class="fas fa-info-circle"></i> Share code <strong><?php echo $code; ?></strong> with friends!
                        </div>
                        
                        <!-- Host Controls (hidden by default) -->
                        <div id="host-controls" style="display:none;">
                            <button class="btn btn-primary btn-block btn-lg" disabled>Auto-Starting...</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- GAME ARENA SECTION (Hidden initially) -->
    <div id="game-arena" style="display:none;">
        <div class="row">
            <!-- Question Area -->
            <div class="col-md-8">
                <div class="card shadow-lg border-0 mb-4">
                    <div class="card-body p-5">
                        <div class="d-flex justify-content-between mb-3">
                            <span class="badge badge-warning" id="q-number">QUESTION 1</span>
                            <span class="text-danger font-weight-bold" id="q-timer">00:20</span>
                        </div>
                        
                        <h3 class="mb-4" id="q-text">Loading question...</h3>
                        
                        <div class="options-grid" id="q-options">
                            <!-- Options injected via JS -->
                        </div>
                    </div>
                </div>
            </div>
            
                <!-- Panic Buttons (Lifelines) -->
                <div class="card shadow-sm border-0 mb-3" id="panic-buttons" style="display:none;">
                    <div class="card-header bg-danger text-white font-weight-bold">
                        <i class="fas fa-exclamation-triangle mr-2"></i> Panic Buttons
                    </div>
                    <div class="card-body p-2 d-flex justify-content-around">
                        <button class="btn btn-outline-primary btn-sm btn-lifeline" data-type="50_50" id="btn-50_50">
                            <i class="fas fa-divide"></i> 50/50 (<span class="count">0</span>)
                        </button>
                        <button class="btn btn-outline-success btn-sm btn-lifeline" data-type="ai_hint" id="btn-ai_hint">
                            <i class="fas fa-brain"></i> Hint (<span class="count">0</span>)
                        </button>
                    </div>
                </div>

                <!-- Live Leaderboard -->
                <div class="col-md-4">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white font-weight-bold">
                        🏆 Live Rankings
                    </div>
                    <ul class="list-group list-group-flush" id="live-leaderboard">
                        <!-- Rankings injected via JS -->
                    </ul>
                </div>
                
                <!-- Event Feed -->
                <div class="mt-3" id="event-feed">
                    <!-- Toasts injected here -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JS Logic for Pulse Architecture -->
<script>
    const ROOM_CODE = "<?php echo $code; ?>";
    const API_URL = "/api/lobby/" + ROOM_CODE + "/status";
    
    let currentState = 'waiting';
    let lastPulse = 0;
    
    // Start Pulse
    setInterval(pulse, 1000); // 1s sync
    
    async function pulse() {
        try {
            const response = await fetch(API_URL);
            const data = await response.json();
            
            if (!data || !data.lobby) return;
            
            updateUI(data);
            document.getElementById('connection-status').className = 'text-success';
            document.getElementById('last-update').innerText = '0s';
        } catch (e) {
            document.getElementById('connection-status').className = 'text-danger'; // Lost connection
        }
    }
    
    function updateUI(data) {
        // 1. Status Check
        if (data.lobby.status !== currentState) {
            currentState = data.lobby.status;
            if (currentState === 'active') {
                document.getElementById('waiting-room').style.display = 'none';
                document.getElementById('game-arena').style.display = 'block';
                document.getElementById('panic-buttons').style.display = 'block';
                loadInventory();
            }
        }
        
        // 2. Waiting Room Updates
        if (currentState === 'waiting') {
            updateParticipants(data.participants);
            updateTimer(data.time_remaining);
        }
        
        // 3. Game Updates
        if (currentState === 'active') {
            updateLeaderboard(data.participants);
            
            // 4. Bot Pressure (Events)
            if (data.events && data.events.length > 0) {
                data.events.forEach(ev => {
                    // Check if we already showed this toast recently to avoid spam
                    if (!window.recentToasts) window.recentToasts = [];
                    const toastId = ev.name + '_' + data.lobby.current_question_index;
                    
                    if (!window.recentToasts.includes(toastId)) {
                        showBotToast(ev.message);
                        window.recentToasts.push(toastId);
                        // Clean up old toasts
                        if (window.recentToasts.length > 10) window.recentToasts.shift();
                    }
                });
            }
        }
    }

    function showBotToast(msg) {
        const feed = document.getElementById('event-feed');
        const toast = document.createElement('div');
        toast.className = 'alert alert-info alert-dismissible fade show shadow-sm bot-toast';
        toast.innerHTML = `
            <i class="fas fa-bolt"></i> <strong>Pressure!</strong> ${msg}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        `;
        feed.prepend(toast);
        
        // Auto remove
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 500);
        }, 3000);

        // Play subtle sound if enabled
        playSfx('pop');
    }

    function playSfx(type) {
        // Placeholder for satisfying sounds
        const sounds = {
            'pop': 'https://assets.mixkit.co/sfx/preview/mixkit-positive-interface-click-1112.mp3',
            'win': 'https://assets.mixkit.co/sfx/preview/mixkit-winning-chimes-2015.mp3'
        };
        if (sounds[type]) {
            const audio = new Audio(sounds[type]);
            audio.volume = 0.3;
            audio.play().catch(e => {}); // Ignore Autoplay blocks
        }
    }
    
    function updateParticipants(users) {
        const container = document.getElementById('participants-list');
        document.getElementById('player-count').innerText = users.length;
        
        let html = '';
        users.forEach(u => {
            html += `
                <div class="col-6 col-md-3 mb-3">
                    <div class="card h-100 text-center p-3 player-card ${u.is_bot == 1 ? 'border-dashed' : ''}">
                        <img src="${u.avatar}" class="rounded-circle mb-2 mx-auto d-block" width="50" height="50">
                        <div class="font-weight-bold text-truncate">${u.name}</div>
                        ${u.is_bot == 1 ? '<span class="badge badge-light badge-pill mt-1">Ready</span>' : '<span class="badge badge-success badge-pill mt-1">Ready</span>'}
                    </div>
                </div>
            `;
        });
        container.innerHTML = html;
    }
    
    function updateTimer(seconds) {
        const el = document.getElementById('countdown-timer');
        if (seconds > 0) {
            el.innerText = seconds + "s";
        } else {
            el.innerText = "Starting...";
        }
    }
    
    function updateLeaderboard(users) {
        // Sort by score
        users.sort((a, b) => b.current_score - a.current_score);
        
        const list = document.getElementById('live-leaderboard');
        let html = '';
        users.forEach((u, index) => {
            html += `
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <span class="badge badge-secondary badge-pill mr-2">#${index+1}</span>
                        ${u.name}
                    </div>
                    <span class="font-weight-bold text-primary">${u.current_score}</span>
                </li>
            `;
        });
        list.innerHTML = html;
    }
    // Wager Logic
    document.querySelectorAll('.btn-wager').forEach(btn => {
        btn.addEventListener('click', async function() {
            const amt = this.dataset.amt;
            if (!confirm(`Wager ${amt} coins? Double up if you win!`)) return;

            const fd = new FormData();
            fd.append('amount', amt);
            fd.append('lobby_id', '<?php echo $participant["lobby_id"]; ?>');

            try {
                const res = await fetch('/api/lobby/wager', { method: 'POST', body: fd });
                const data = await res.json();
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message);
                }
            } catch (e) {
                alert('Connection error placing wager.');
            }
        });
    });
    async function loadInventory() {
        const res = await fetch('/quiz/shop'); // Simple way to get data if we had an API, but let's assume we need one or use global from PHP
        // For MVP, we pass it from PHP to JS in the view
        const inv = <?php echo json_encode((new \App\Services\LifelineService())->getInventory($_SESSION['user_id'])); ?>;
        Object.keys(inv).forEach(type => {
            const el = document.querySelector(`#btn-${type} .count`);
            if (el) el.innerText = inv[type];
            const btn = document.getElementById(`btn-${type}`);
            if (btn) btn.disabled = inv[type] <= 0;
        });
    }

    document.querySelectorAll('.btn-lifeline').forEach(btn => {
        btn.addEventListener('click', async function() {
            const type = this.dataset.type;
            const res = await fetch('/api/quiz/use-lifeline', {
                method: 'POST',
                body: new URLSearchParams({type})
            });
            const data = await res.json();
            if (data.success) {
                showBotToast("You used " + type);
                loadInventory();
                // Logic to actually apply the effect (e.g. 50/50 hides options)
                if (type === '50_50') apply5050();
            } else {
                alert(data.message);
            }
        });
    });

    function apply5050() {
        // Find 2 wrong options and hide them
        const options = document.querySelectorAll('.option-btn');
        let hidden = 0;
        options.forEach(opt => {
            if (!opt.dataset.correct && hidden < 2) {
                opt.style.visibility = 'hidden';
                hidden++;
            }
        });
    }
</script>

<style>
    .player-card { transition: all 0.3s; }
    .player-card:hover { transform: translateY(-5px); }
    .border-dashed { border-style: dashed !important; border-color: #ccc; }
</style>
