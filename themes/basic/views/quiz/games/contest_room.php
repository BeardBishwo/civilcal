<div id="contest-arena" class="min-vh-100 position-relative overflow-hidden" style="background: #0d0d1a; color: #fff; font-family: 'Inter', sans-serif;">
    <!-- Cyberpunk Overlay -->
    <div class="position-absolute top-0 start-0 w-100 h-100 opacity-10" style="background-image: radial-gradient(#4facfe 1px, transparent 1px); background-size: 30px 30px;"></div>

    <!-- Top Navigation HUD -->
    <header class="position-sticky top-0 start-0 w-100 z-index-10 border-bottom border-white border-opacity-10 backdrop-blur-md" style="background: rgba(13, 13, 26, 0.8);">
        <div class="container-fluid px-4 py-3 d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <div class="btn-group me-3">
                    <button class="btn btn-outline-danger btn-sm rounded-pill px-3" onclick="confirmExit()">
                        <i class="fas fa-times-circle me-1"></i> SURRENDER
                    </button>
                </div>
                <h5 class="mb-0 fw-bold text-truncate" style="max-width: 250px;"><?= htmlspecialchars($contest['title']) ?></h5>
            </div>

            <!-- Stats Bar -->
            <div class="d-flex align-items-center gap-4">
                <div class="text-center">
                    <small class="text-muted d-block font-xs text-uppercase">Time Elapsed</small>
                    <span id="timer" class="fw-mono text-warning fs-5">00:00</span>
                </div>
                <div class="text-center border-start border-white border-opacity-10 ps-4">
                    <small class="text-muted d-block font-xs text-uppercase">Progress</small>
                    <span id="progress-text" class="fw-bold fs-5">0 / 0</span>
                </div>
            </div>

            <button class="btn btn-warning btn-sm rounded-pill px-4 fw-bold shadow-sm" id="btn-submit" onclick="submitBattle()">
                <i class="fas fa-flag-checkered me-1"></i> FINISH
            </button>
        </div>
        <!-- Progress Bar -->
        <div class="progress rounded-0 bg-transparent" style="height: 3px;">
            <div id="p-bar" class="progress-bar bg-warning shadow-warning" role="progressbar" style="width: 0%; transition: width 0.5s ease;"></div>
        </div>
    </header>

    <main class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-9 col-xl-8">
                <!-- Question Container -->
                <div id="question-container" class="position-relative">
                    <!-- Questions will be injected here via JS -->
                </div>

                <!-- HUD Controls -->
                <div class="d-flex justify-content-between align-items-center mt-5 p-4 rounded-4 bg-white bg-opacity-5 border border-white border-opacity-10">
                    <button class="btn btn-link text-white text-decoration-none opacity-50 hover-opacity-100" onclick="prevQuestion()" id="btn-prev">
                        <i class="fas fa-chevron-left me-2"></i> PREVIOUS
                    </button>
                    
                    <div class="d-flex gap-2" id="pagination-dots">
                        <!-- Dots injected here -->
                    </div>

                    <button class="btn btn-white rounded-pill px-5 py-2 fw-bold text-dark shadow-sm" onclick="nextQuestion()" id="btn-next">
                        NEXT <i class="fas fa-chevron-right ms-2"></i>
                    </button>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Question Template (Hidden) -->
<template id="question-tpl">
    <div class="question-card animate-fade-in">
        <div class="d-flex align-items-center mb-4">
             <span class="badge bg-warning text-dark rounded-pill px-3 py-2 me-3 fw-bold q-index">Q 1</span>
             <span class="text-muted small uppercase tracking-wider difficulty-badge">Medium Difficulty</span>
        </div>
        <h3 class="fw-normal mb-5 lh-base q-text"></h3>
        
        <div class="options-grid row g-3">
            <!-- Options injected here -->
        </div>
    </div>
</template>

<style>
#contest-arena { font-family: 'Outfit', sans-serif; cursor: default; user-select: none; }
.backdrop-blur-md { backdrop-filter: blur(12px); }
.fw-mono { font-family: 'JetBrains Mono', monospace; }
.font-xs { font-size: 0.7rem; }
.shadow-warning { box-shadow: 0 0 15px rgba(255, 193, 7, 0.4); }

.question-card { animation: slideIn 0.4s ease-out; }
@keyframes slideIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.option-btn {
    text-align: left;
    padding: 1.5rem;
    border: 2px solid rgba(255,255,255,0.1);
    background: rgba(255,255,255,0.03);
    color: #fff;
    border-radius: 1.25rem;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}
.option-btn:hover {
    background: rgba(255,255,255,0.08);
    border-color: rgba(255,255,255,0.2);
    transform: scale(1.01);
}
.option-btn.selected {
    background: rgba(79, 172, 254, 0.15);
    border-color: #4facfe;
    box-shadow: 0 0 20px rgba(79, 172, 254, 0.25);
}
.option-btn .label {
    width: 32px; height: 32px;
    display: flex; align-items: center; justify-content: center;
    background: rgba(255,255,255,0.1);
    border-radius: 8px;
    margin-right: 1rem;
    font-weight: bold;
    font-size: 0.9rem;
}
.option-btn.selected .label {
    background: #4facfe;
    color: #fff;
}

#pagination-dots .dot {
    width: 10px; height: 10px;
    border-radius: 50%;
    background: rgba(255,255,255,0.1);
    transition: all 0.3s ease;
}
#pagination-dots .dot.active { background: #ffc107; transform: scale(1.3); box-shadow: 0 0 10px rgba(255, 193, 7, 0.5); }
#pagination-dots .dot.answered { background: rgba(255,255,255,0.5); }
</style>

<script>
const QUESTIONS = <?= json_encode($questions) ?>;
const CONTEST_ID = <?= $contest['id'] ?>;
let currentIndex = 0;
let answers = {};
let startTime = Date.now();

function init() {
    renderQuestion();
    renderDots();
    startTimer();
}

function startTimer() {
    setInterval(() => {
        const elapsed = Math.floor((Date.now() - startTime) / 1000);
        const m = Math.floor(elapsed / 60).toString().padStart(2, '0');
        const s = (elapsed % 60).toString().padStart(2, '0');
        document.getElementById('timer').innerText = `${m}:${s}`;
    }, 1000);
}

function renderQuestion() {
    const container = document.getElementById('question-container');
    const tpl = document.getElementById('question-tpl').content.cloneNode(true);
    const q = QUESTIONS[currentIndex];

    tpl.querySelector('.q-index').innerText = `Q ${currentIndex + 1}`;
    tpl.querySelector('.q-text').innerText = q.content.text;
    tpl.querySelector('.difficulty-badge').innerText = q.difficulty_level == 1 ? 'EASY' : (q.difficulty_level == 2 ? 'MEDIUM' : 'ELITE BATTLE');
    
    const optionsGrid = tpl.querySelector('.options-grid');
    const letters = ['A', 'B', 'C', 'D', 'E'];
    
    q.options.forEach((opt, idx) => {
        const btn = document.createElement('button');
        btn.className = `option-btn w-100 d-flex align-items-center mb-2 ${answers[q.id] == idx ? 'selected' : ''}`;
        btn.innerHTML = `<span class="label">${letters[idx]}</span><span class="text">${opt.text}</span>`;
        btn.onclick = () => selectOption(q.id, idx);
        
        const col = document.createElement('div');
        col.className = 'col-sm-6';
        col.appendChild(btn);
        optionsGrid.appendChild(col);
    });

    container.innerHTML = '';
    container.appendChild(tpl);

    // Update Progress
    const pct = ((currentIndex + 1) / QUESTIONS.length) * 100;
    document.getElementById('p-bar').style.width = pct + '%';
    document.getElementById('progress-text').innerText = `${currentIndex + 1} / ${QUESTIONS.length}`;

    // Controls
    document.getElementById('btn-prev').disabled = currentIndex === 0;
    document.getElementById('btn-next').innerText = currentIndex === QUESTIONS.length - 1 ? 'FINISH BATTLE' : 'NEXT';
}

function selectOption(qId, idx) {
    answers[qId] = idx;
    renderQuestion();
    renderDots();
}

function renderDots() {
    const dotContainer = document.getElementById('pagination-dots');
    dotContainer.innerHTML = '';
    QUESTIONS.forEach((q, idx) => {
        const dot = document.createElement('div');
        dot.className = `dot ${idx === currentIndex ? 'active' : ''} ${answers[q.id] !== undefined ? 'answered' : ''}`;
        dotContainer.appendChild(dot);
    });
}

function nextQuestion() {
    if (currentIndex < QUESTIONS.length - 1) {
        currentIndex++;
        renderQuestion();
        renderDots();
    } else {
        submitBattle();
    }
}

function prevQuestion() {
    if (currentIndex > 0) {
        currentIndex--;
        renderQuestion();
        renderDots();
    }
}

async function submitBattle() {
    const elapsed = Math.floor((Date.now() - startTime) / 1000);
    
    // Quick local score calc for instant feedback (server validates)
    let score = 0;
    QUESTIONS.forEach(q => {
        if (answers[q.id] != undefined && answers[q.id] == q.correct_answer) {
            score++;
        }
    });

    const btn = document.getElementById('btn-submit');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> SEALING LOGS...';

    const resp = await fetch('<?= app_base_url("contest/submit/".$contest["id"]) ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `score=${score}&time_taken=${elapsed}`
    });

    window.location.href = '<?= app_base_url("contest/result/".$contest["id"]) ?>';
}

function confirmExit() {
    if(confirm("Are you sure you want to surrender? Entry fee will not be refunded.")) {
        window.location.href = '<?= app_base_url("contests") ?>';
    }
}

init();
</script>
