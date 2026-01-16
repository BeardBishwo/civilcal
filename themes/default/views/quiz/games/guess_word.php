<?php

/**
 * PROFESSIONAL TERMINOLOGY BLUEPRINT (GUESS WORD)
 * Optimized for Architectural & Engineering Precision
 */
$categories = $categories ?? [];
?>

<!-- Dependencies -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<div class="guess-word-blueprint"
    x-data="wordGuessStation()"
    x-init="init()"
    style="font-family: 'Inter', system-ui, sans-serif;">

    <!-- ARCHITECTURAL HEADER -->
    <header class="blueprint-header">
        <div class="header-container">
            <div class="brand-unit">
                <a href="<?= app_base_url('/quiz') ?>" class="nav-control">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div class="brand-text">
                    <h1>TERMINOLOGY <span class="accent">STATION</span></h1>
                    <p>ENGINEERING LOGIC MODULE</p>
                </div>
            </div>

            <div class="status-belt">
                <!-- Source Control -->
                <div class="control-node">
                    <label>UPLINK SOURCE</label>
                    <select x-model="selectedCategory" @change="loadLevel()" :disabled="loading" class="blueprint-select">
                        <option value="">Global Frequency</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['title']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="divider"></div>

                <!-- Performance Metrics -->
                <div class="metrics-grid">
                    <div class="metric">
                        <span class="m-label">CORRECT</span>
                        <span class="m-value success" x-text="stats.correct">0</span>
                    </div>
                    <div class="metric">
                        <span class="m-label">ACCURACY</span>
                        <span class="m-value primary" x-text="stats.accuracy + '%'">0%</span>
                    </div>
                    <div class="metric">
                        <span class="m-label">CREDITS</span>
                        <span class="m-value warning" x-text="coins">0</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Accuracy Sync Bar -->
        <div class="progress-track">
            <div class="progress-fill" :style="`width: ${timePercent}%`" :class="timePercent < 25 ? 'critical' : ''"></div>
        </div>
    </header>

    <!-- WORKSTATION CORE -->
    <main class="workstation-grid">
        <div class="main-canvas">
            <!-- Node Information -->
            <div class="canvas-header">
                <div class="node-badge">
                    <i class="fas fa-microchip"></i>
                    ACTIVE NODE: <span class="highlight" x-text="current.category">GENERAL</span>
                </div>
                <div class="timer-unit" :class="timeLeft < 10 ? 'urgent' : ''">
                    <i class="far fa-clock"></i>
                    <span x-text="timeLeft">60</span>s
                </div>
            </div>

            <!-- The Question Area -->
            <div class="concept-shroud">
                <div class="label">TECHNICAL SPECIFICATION:</div>
                <div class="definition-text" x-text="current.definition">SYNCHRONIZING WITH DATASET...</div>
            </div>

            <!-- Answer Input (The Logic Deck) -->
            <div class="logic-deck">
                <template x-for="(slot, i) in answerBuffer" :key="i">
                    <div class="deck-slot"
                        @click="removeChar(i)"
                        :class="{'active': slot.char, 'empty': !slot.char}">
                        <span x-text="slot.char || ''"></span>
                    </div>
                </template>
            </div>

            <!-- Letter Supply (The Buffer) -->
            <div class="letter-buffer">
                <template x-for="(letter, idx) in current.scrambled" :key="idx">
                    <button @click="typeChar(letter, idx)"
                        :disabled="isUsed(idx) || showResult"
                        class="letter-tile"
                        :class="{'used': isUsed(idx)}">
                        <span x-text="letter"></span>
                    </button>
                </template>
            </div>

            <!-- Control Actions -->
            <div class="canvas-footer">
                <div class="action-spread">
                    <button @click="resetBuffer()" class="btn-blueprint secondary" :disabled="loading || showResult">
                        <i class="fas fa-undo"></i> CLEAR DECK
                    </button>
                    <button @click="useHint()" :disabled="coins < 2 || hintUsed || showResult || loading" class="btn-blueprint warning">
                        <i class="fas fa-lightbulb"></i> HINT (2¢)
                    </button>
                    <button @click="checkAnswer()" :disabled="loading || showResult" class="btn-blueprint primary">
                        <span x-show="!loading"><i class="fas fa-check-double"></i> EXECUTE VERIFICATION</span>
                        <span x-show="loading"><i class="fas fa-circle-notch fa-spin"></i> VERIFYING...</span>
                    </button>
                </div>
            </div>

            <!-- Overlays -->
            <div class="result-overlay" x-show="showResult" x-transition>
                <div class="result-card" :class="resultSuccess ? 'win' : 'fail'">
                    <i :class="resultSuccess ? 'fas fa-check-circle' : 'fas fa-times-circle'"></i>
                    <h2 x-text="resultSuccess ? 'VERIFICATION SUCCESSFUL' : 'LOGICAL ERROR DETECTED'"></h2>
                    <p x-text="resultMessage"></p>
                    <button @click="nextLevel()" class="btn-continue">
                        INITIALIZE NEXT MODULE <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
    function wordGuessStation() {
        return {
            coins: 0,
            stats: {
                correct: 0,
                wrong: 0,
                total: 0,
                accuracy: 0
            },
            current: {
                id: null,
                definition: 'Awaiting Uplink...',
                scrambled: [],
                length: 0,
                category: 'SYSTEM'
            },
            answerBuffer: [],
            usedIndices: [],
            timeLeft: 60,
            timePercent: 100,
            timer: null,
            showResult: false,
            resultSuccess: false,
            resultMessage: '',
            loading: false,
            hintUsed: false,
            selectedCategory: '',

            async init() {
                await this.loadProgress();
                await this.loadLevel();
                this.startTimer();
            },

            async loadProgress() {
                try {
                    const res = await fetch('<?= app_base_url("/quiz/guess-word/progress") ?>');
                    const data = await res.json();
                    if (data.success) {
                        this.stats = data.stats;
                        this.coins = data.stats.points || 0; // The API returns points here
                    }
                } catch (e) {
                    console.error("Progression Sync Failed", e);
                }
            },

            async loadLevel() {
                this.showResult = false;
                this.answerBuffer = [];
                this.usedIndices = [];
                this.timeLeft = 60;
                this.timePercent = 100;
                this.hintUsed = false;
                this.loading = true;

                try {
                    const url = new URL('<?= app_base_url("/quiz/guess-word/data") ?>');
                    if (this.selectedCategory) url.searchParams.set('category_id', this.selectedCategory);

                    const res = await fetch(url.toString(), {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    const data = await res.json();

                    if (data.success) {
                        this.current = {
                            id: data.id,
                            definition: data.definition,
                            scrambled: data.scrambled,
                            length: data.length,
                            category: data.category || 'GENERAL'
                        };
                        this.answerBuffer = new Array(data.length).fill('').map(() => ({
                            char: '',
                            src: null
                        }));
                    } else {
                        Swal.fire({
                            title: 'Module Offline',
                            text: data.error || 'No valid terminology paths found.',
                            icon: 'info'
                        });
                    }
                } catch (e) {
                    console.error("Level Load Logic Error", e);
                    this.current.definition = "COMMUNICATION FAILURE. RELOAD STATION.";
                } finally {
                    this.loading = false;
                }
            },

            typeChar(char, index) {
                if (this.usedIndices.includes(index) || this.showResult) return;
                const emptyIdx = this.answerBuffer.findIndex(s => !s.char);
                if (emptyIdx !== -1) {
                    this.usedIndices.push(index);
                    this.answerBuffer[emptyIdx] = {
                        char,
                        src: index
                    };
                }
            },

            removeChar(bufferIdx) {
                if (this.showResult) return;
                const item = this.answerBuffer[bufferIdx];
                if (item && item.src !== null) {
                    this.usedIndices = this.usedIndices.filter(i => i !== item.src);
                    this.answerBuffer[bufferIdx] = {
                        char: '',
                        src: null
                    };
                }
            },

            isUsed(idx) {
                return this.usedIndices.includes(idx);
            },

            startTimer() {
                if (this.timer) clearInterval(this.timer);
                this.timer = setInterval(() => {
                    if (this.showResult || this.loading) return;
                    this.timeLeft--;
                    this.timePercent = (this.timeLeft / 60) * 100;
                    if (this.timeLeft <= 0) this.checkAnswer(true);
                }, 1000);
            },

            async checkAnswer(force = false) {
                const guess = this.answerBuffer.map(s => s.char || '').join('').trim();
                if (guess.length !== this.current.length && !force) {
                    Swal.fire({
                        title: 'Buffer Error',
                        text: 'All logic slots must be populated.',
                        icon: 'warning',
                        toast: true,
                        position: 'top-end',
                        timer: 3000
                    });
                    return;
                }

                this.loading = true;
                try {
                    const formData = new FormData();
                    formData.append('answer', guess);

                    const res = await fetch('<?= app_base_url("/quiz/guess-word/check") ?>', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    const data = await res.json();
                    this.resultSuccess = data.correct;
                    this.resultMessage = data.message;

                    if (data.correct) {
                        this.stats.correct++;
                        this.coins = data.points || this.coins;
                    } else {
                        this.stats.wrong++;
                    }

                    this.stats.total++;
                    this.stats.accuracy = Math.round((this.stats.correct / (this.stats.total || 1)) * 100);
                    this.showResult = true;
                } catch (e) {
                    Swal.fire('Processing Failure', 'The verification engine encountered a fatal error.', 'error');
                } finally {
                    this.loading = false;
                }
            },

            async useHint() {
                if (this.coins < 2 || this.hintUsed || this.loading) return;
                this.loading = true;
                try {
                    const res = await fetch('<?= app_base_url("/quiz/guess-word/hint") ?>', {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    const data = await res.json();
                    if (data.success) {
                        this.coins -= 2;
                        this.hintUsed = true;
                        Swal.fire({
                            title: 'Logic Decrypted',
                            text: data.message,
                            icon: 'info'
                        });
                    } else {
                        Swal.fire('Hint Error', data.error, 'error');
                    }
                } finally {
                    this.loading = false;
                }
            },

            async nextLevel() {
                await this.loadLevel();
                this.startTimer();
            },

            resetBuffer() {
                this.answerBuffer.forEach(s => {
                    s.char = '';
                    s.src = null;
                });
                this.usedIndices = [];
            }
        };
    }
</script>

<style>
    /* ========================================
   TECHNICAL BLUEPRINT DESIGN SYSTEM
   ======================================== */
    :root {
        --bp-bg: #f8fafc;
        --bp-slate-900: #0f172a;
        --bp-slate-700: #334155;
        --bp-slate-400: #94a3b8;
        --bp-slate-200: #e2e8f0;
        --bp-primary: #2563eb;
        --bp-primary-soft: #dbeafe;
        --bp-success: #059669;
        --bp-success-soft: #d1fae5;
        --bp-warning: #d97706;
        --bp-warning-soft: #fef3c7;
        --bp-danger: #dc2626;
        --bp-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }

    .guess-word-blueprint {
        min-height: 100vh;
        background: var(--bp-bg);
        color: var(--bp-slate-900);
        padding-bottom: 3rem;
    }

    /* Header */
    .blueprint-header {
        background: white;
        border-bottom: 2px solid var(--bp-slate-200);
        position: sticky;
        top: 0;
        z-index: 100;
    }

    .header-container {
        max-width: 1100px;
        margin: 0 auto;
        padding: 0.75rem 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .brand-unit {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .nav-control {
        width: 36px;
        height: 36px;
        border: 1px solid var(--bp-slate-200);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--bp-slate-700);
        text-decoration: none;
        transition: 0.2s;
    }

    .nav-control:hover {
        background: var(--bp-bg);
        border-color: var(--bp-slate-400);
    }

    .brand-text h1 {
        font-size: 1.1rem;
        font-weight: 900;
        margin: 0;
        letter-spacing: -0.01em;
    }

    .brand-text .accent {
        color: var(--bp-primary);
    }

    .brand-text p {
        font-size: 0.65rem;
        font-weight: 800;
        color: var(--bp-slate-400);
        margin: 0;
        letter-spacing: 0.05em;
    }

    .status-belt {
        display: flex;
        align-items: center;
        gap: 1.5rem;
    }

    .control-node label {
        display: block;
        font-size: 0.55rem;
        font-weight: 900;
        color: var(--bp-slate-400);
        margin-bottom: 2px;
    }

    .blueprint-select {
        border: 1px solid var(--bp-slate-200);
        background: var(--bp-bg);
        font-size: 0.8rem;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 6px;
        outline: none;
    }

    .divider {
        height: 30px;
        width: 1px;
        background: var(--bp-slate-200);
    }

    .metrics-grid {
        display: flex;
        gap: 1.25rem;
    }

    .metric {
        text-align: center;
    }

    .m-label {
        font-size: 0.55rem;
        font-weight: 800;
        display: block;
        color: var(--bp-slate-400);
    }

    .m-value {
        font-size: 0.95rem;
        font-weight: 900;
        font-variant-numeric: tabular-nums;
    }

    .m-value.success {
        color: var(--bp-success);
    }

    .m-value.primary {
        color: var(--bp-primary);
    }

    .m-value.warning {
        color: var(--bp-warning);
    }

    .progress-track {
        height: 3px;
        background: var(--bp-slate-200);
        width: 100%;
        position: relative;
    }

    .progress-fill {
        height: 100%;
        background: var(--bp-primary);
        transition: width 1s linear;
    }

    .progress-fill.critical {
        background: var(--bp-danger);
    }

    /* Main Workstation */
    .workstation-grid {
        max-width: 1000px;
        margin: 2rem auto;
        padding: 0 1.5rem;
    }

    .main-canvas {
        background: white;
        border: 2px solid var(--bp-slate-200);
        border-radius: 1.25rem;
        padding: 2.5rem;
        position: relative;
        box-shadow: var(--bp-shadow);
        min-height: 500px;
    }

    .canvas-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2.5rem;
    }

    .node-badge {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.75rem;
        font-weight: 800;
        color: var(--bp-slate-700);
        background: var(--bp-bg);
        padding: 4px 12px;
        border-radius: 8px;
        border: 1px solid var(--bp-slate-200);
    }

    .node-badge .highlight {
        color: var(--bp-primary);
    }

    .timer-unit {
        font-size: 1.25rem;
        font-weight: 900;
        color: var(--bp-slate-900);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .timer-unit.urgent {
        color: var(--bp-danger);
        animation: crunch 0.5s infinite alternate;
    }

    @keyframes crunch {
        from {
            opacity: 1;
        }

        to {
            opacity: 0.5;
        }
    }

    .concept-shroud {
        margin-bottom: 3rem;
    }

    .concept-shroud .label {
        font-size: 0.65rem;
        font-weight: 900;
        color: var(--bp-primary);
        border-left: 3px solid var(--bp-primary);
        padding-left: 10px;
        margin-bottom: 1rem;
    }

    .definition-text {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--bp-slate-900);
        line-height: 1.4;
        border-bottom: 1px dashed var(--bp-slate-200);
        padding-bottom: 1.5rem;
    }

    /* The Logic Deck */
    .logic-deck {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        margin-bottom: 3rem;
        justify-content: center;
    }

    .deck-slot {
        width: 50px;
        height: 60px;
        background: var(--bp-bg);
        border: 2px solid var(--bp-slate-200);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        font-weight: 900;
        font-family: 'JetBrains Mono', monospace;
        cursor: pointer;
        transition: 0.2s;
    }

    .deck-slot.active {
        border-color: var(--bp-primary);
        background: var(--bp-primary-soft);
        color: var(--bp-primary);
        transform: translateY(-4px);
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.1);
    }

    .deck-slot.empty {
        border-style: dashed;
    }

    /* The Buffer */
    .letter-buffer {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        margin-bottom: 3rem;
        justify-content: center;
    }

    .letter-tile {
        width: 44px;
        height: 44px;
        background: white;
        border: 1px solid var(--bp-slate-200);
        border-bottom: 3px solid var(--bp-slate-200);
        border-radius: 8px;
        font-size: 1.2rem;
        font-weight: 800;
        color: var(--bp-slate-700);
        cursor: pointer;
        transition: 0.1s;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .letter-tile:hover:not(:disabled) {
        border-color: var(--bp-slate-400);
        transform: translateY(-2px);
        border-bottom-width: 4px;
    }

    .letter-tile:active {
        transform: translateY(1px);
        border-bottom-width: 1px;
    }

    .letter-tile:disabled {
        opacity: 0.2;
        transform: scale(0.9);
        cursor: default;
    }

    /* Actions */
    .canvas-footer {
        border-top: 2px solid var(--bp-bg);
        padding-top: 2rem;
    }

    .action-spread {
        display: grid;
        grid-template-columns: 1fr 1fr 2fr;
        gap: 1rem;
    }

    .btn-blueprint {
        padding: 0.75rem;
        border-radius: 10px;
        font-weight: 800;
        font-size: 0.85rem;
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        transition: 0.2s;
    }

    .btn-blueprint.primary {
        background: var(--bp-primary);
        color: white;
    }

    .btn-blueprint.primary:hover:not(:disabled) {
        background: #1d4ed8;
        transform: translateY(-1px);
    }

    .btn-blueprint.secondary {
        background: var(--bp-slate-200);
        color: var(--bp-slate-700);
    }

    .btn-blueprint.warning {
        background: var(--bp-warning-soft);
        color: var(--bp-warning);
        border: 1px solid var(--bp-warning);
    }

    .btn-blueprint:disabled {
        opacity: 0.4;
        pointer-events: none;
    }

    /* Overlays */
    .result-overlay {
        position: absolute;
        inset: 0;
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(4px);
        z-index: 50;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: inherit;
    }

    .result-card {
        text-align: center;
        padding: 2rem;
        max-width: 400px;
    }

    .result-card i {
        font-size: 4rem;
        margin-bottom: 1.5rem;
    }

    .result-card.win i {
        color: var(--bp-success);
    }

    .result-card.fail i {
        color: var(--bp-danger);
    }

    .result-card h2 {
        font-size: 1.25rem;
        font-weight: 900;
        margin-bottom: 0.75rem;
    }

    .result-card p {
        font-size: 1rem;
        color: var(--bp-slate-700);
        margin-bottom: 2rem;
        font-weight: 500;
    }

    .btn-continue {
        width: 100%;
        padding: 0.85rem;
        background: var(--bp-slate-900);
        color: white;
        border: none;
        border-radius: 10px;
        font-weight: 800;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
    }

    .btn-continue:hover {
        background: #000;
    }

    @media (max-width: 768px) {
        .header-container {
            flex-direction: column;
            gap: 1rem;
        }

        .status-belt {
            width: 100%;
            justify-content: space-between;
            gap: 0.5rem;
        }

        .main-canvas {
            padding: 1.5rem;
        }

        .definition-text {
            font-size: 1.1rem;
        }

        .action-spread {
            grid-template-columns: 1fr;
        }

        .logic-deck {
            gap: 0.5rem;
        }

        .deck-slot {
            width: 40px;
            height: 50px;
            font-size: 1.1rem;
        }
    }
</style>