<div id="blueprint-arena" class="min-vh-100" style="background: #1e293b; color: #fff; font-family: 'Outfit', sans-serif;">
    <!-- HUD -->
    <header class="p-3 border-bottom border-white border-opacity-10 bg-dark bg-opacity-50 backdrop-blur sticky-top">
        <div class="container d-flex justify-content-between align-items-center">
            <a href="<?= app_base_url('/blueprint') ?>" class="btn btn-outline-light btn-sm rounded-pill px-3">
                <i class="fas fa-arrow-left me-1"></i> EXIT STUDIO
            </a>
            <h5 class="mb-0 fw-bold">BLUEPRINT: <span id="reveal-percent">0%</span> REVEALED</h5>
            <div id="score-badge" class="badge bg-primary rounded-pill px-4 py-2 fs-6 shadow">0 / 5 Matched</div>
        </div>
    </header>

    <main class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <!-- Goal -->
                <div class="text-center mb-5 animate-slide-up">
                    <h1 class="display-5 fw-bold mb-3">Terminology Match</h1>
                    <p class="text-info opacity-75 lead">Match each engineering term with its correct definition to uncover the blueprint.</p>
                </div>

                <div class="row g-4 align-items-center">
                    <!-- Terms Side -->
                    <div class="col-md-4">
                        <div id="terms-column" class="d-grid gap-3">
                            <?php foreach($terms as $idx => $word): ?>
                                <div class="term-card p-3 rounded-4 bg-white bg-opacity-10 border border-white border-opacity-10 cursor-pointer transition-all" 
                                     draggable="true" 
                                     data-id="<?= $word['id'] ?>"
                                     id="term-<?= $word['id'] ?>">
                                    <div class="fw-bold fs-5"><?= htmlspecialchars($word['term']) ?></div>
                                    <small class="text-muted uppercase font-xs tracking-wider">Engineering Term</small>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Defintions Side -->
                    <div class="col-md-8">
                        <div id="defs-column" class="d-grid gap-3">
                            <?php 
                            $shuffledDefs = $terms;
                            shuffle($shuffledDefs);
                            foreach($shuffledDefs as $word): ?>
                                <div class="def-card p-4 rounded-4 border-2 border-dashed border-white border-opacity-10 bg-white bg-opacity-5 min-height-100 transition-all"
                                     data-id="<?= $word['id'] ?>"
                                     ondragover="allowDrop(event)"
                                     ondrop="drop(event, <?= $word['id'] ?>)">
                                    <div class="def-text opacity-75 mb-0 fs-6"><?= htmlspecialchars($word['definition']) ?></div>
                                    <div class="success-mark mt-3 d-none animate-bounce">
                                        <span class="badge bg-success rounded-pill px-3 py-2">
                                            <i class="fas fa-check-circle me-1"></i> MATCHED
                                        </span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Submit Button (Hidden until all matched) -->
                <div id="submit-section" class="text-center mt-5 d-none">
                    <button class="btn btn-warning btn-lg rounded-pill px-5 fw-bold shadow-lg" onclick="finishProject()">
                        REVEAL FULL BLUEPRINT <i class="fas fa-magic ms-2"></i>
                    </button>
                </div>
            </div>
        </div>
    </main>
</div>

<style>
.term-card:hover { transform: translateX(5px); border-color: rgba(255,255,255,0.4) !important; background: rgba(255,255,255,0.15); }
.term-card.dragging { opacity: 0.5; }
.def-card.over { background: rgba(255,255,255,0.15); border-color: #4facfe !important; border-style: solid; }
.def-card.matched { border-style: solid; border-color: #10b981 !important; background: rgba(16, 185, 129, 0.1); }
.success-mark { color: #10b981; }
.font-xs { font-size: 0.65rem; }
.backdrop-blur { backdrop-filter: blur(10px); }
.min-height-100 { min-height: 100px; display: flex; flex-direction: column; justify-content: center; position: relative; }

@keyframes bounce {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.1); }
}
</style>

<script>
let matches = 0;
const total = <?= count($terms) ?>;
let draggedId = null;

document.querySelectorAll('.term-card').forEach(card => {
    card.addEventListener('dragstart', (e) => {
        draggedId = card.dataset.id;
        card.classList.add('dragging');
        e.dataTransfer.setData('text/plain', draggedId);
    });
    card.addEventListener('dragend', () => card.classList.remove('dragging'));
});

function allowDrop(e) {
    e.preventDefault();
    if (!e.target.closest('.def-card').classList.contains('matched')) {
        e.target.closest('.def-card').classList.add('over');
    }
}

document.querySelectorAll('.def-card').forEach(card => {
    card.addEventListener('dragleave', () => card.classList.remove('over'));
});

async function drop(e, defId) {
    e.preventDefault();
    const card = e.target.closest('.def-card');
    card.classList.remove('over');
    
    if (card.classList.contains('matched')) return;

    const termId = e.dataTransfer.getData('text/plain');
    
    if (termId == defId) {
        // Correct Match
        matches++;
        card.classList.add('matched');
        card.querySelector('.def-text').classList.add('text-success', 'fw-bold');
        card.querySelector('.success-mark').classList.remove('d-none');
        
        // Hide the term card
        document.getElementById('term-' + termId).style.visibility = 'hidden';
        
        // Update Stats
        updateStats();

        if (matches === total) {
            document.getElementById('submit-section').classList.remove('d-none');
        }
    } else {
        // Wrong Match - Visual Feedback
        card.classList.add('border-danger');
        setTimeout(() => card.classList.remove('border-danger'), 500);
    }
}

function updateStats() {
    const pct = Math.floor((matches / total) * 100);
    document.getElementById('reveal-percent').innerText = pct + '%';
    document.getElementById('score-badge').innerText = `${matches} / ${total} Matched`;
}

async function finishProject() {
    const resp = await fetch('<?= app_base_url("/blueprint/submit") ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `blueprint_id=<?= $blueprint_id ?>&correct_matches=${matches}&total_terms=${total}`
    });
    const data = await resp.json();
    if(data.success) {
        alert('Blueprint Captured! Reward added to your bank.');
        window.location.href = '<?= app_base_url("/blueprint") ?>';
    }
}
</script>
