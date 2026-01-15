<div class="min-vh-100 d-flex flex-column" style="background: radial-gradient(circle at center, #1b0a2a 0%, #000000 100%); font-family: 'Outfit', sans-serif; overflow: hidden;" x-data="mathGame()">

    <!-- Background Particles (CSS only for perf) -->
    <div class="position-absolute w-100 h-100" style="z-index: 0; pointer-events: none;">
        <div class="stars"></div>
    </div>

    <!-- HUD -->
    <header class="p-4 d-flex justify-content-between align-items-center position-relative" style="z-index: 10;">
        <a href="<?= app_base_url('/quiz') ?>" class="btn btn-outline-light btn-sm rounded-pill px-4 border-opacity-25 hover-scale">
            <i class="fas fa-arrow-left me-2"></i> EXIT
        </a>

        <div class="d-flex align-items-center gap-4">
            <div class="text-center">
                <div class="text-xs text-uppercase tracking-wider opacity-50">Score</div>
                <div class="h4 mb-0 fw-bold text-warning" x-text="score">0</div>
            </div>
            <div class="text-center">
                <div class="text-xs text-uppercase tracking-wider opacity-50">Time</div>
                <div class="h4 mb-0 fw-bold" :class="timeLeft <= 5 ? 'text-danger animate-pulse' : 'text-white'" x-text="timeLeft.toFixed(1) + 's'"></div>
            </div>
        </div>
    </header>

    <!-- Game Area -->
    <main class="flex-grow-1 d-flex align-items-center justify-content-center position-relative" style="z-index: 10;">

        <!-- START SCREEN -->
        <div x-show="!isPlaying && !gameOver" class="text-center animate-slide-up">
            <h1 class="display-3 fw-bold bg-clip-text text-transparent bg-gradient-to-r from-purple-400 to-pink-600 mb-4">MATH MANIA</h1>
            <p class="text-gray-400 mb-8 fs-5">Solve as many equations as you can before time runs out!</p>
            <button @click="startGame()" class="btn btn-primary btn-lg rounded-pill px-5 py-3 fs-5 shadow-lg shadow-purple-500/30 hover-scale fw-bold">
                <i class="fas fa-play me-2"></i> START GAME
            </button>
        </div>

        <!-- GAMEPLAY -->
        <div x-show="isPlaying" class="text-center w-100 max-w-md" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100">
            <div class="display-1 fw-bold text-white mb-5 animate-float" x-text="currentQuestion.text"></div>

            <div class="row g-3 px-3">
                <template x-for="(option, index) in currentQuestion.options" :key="index">
                    <div class="col-6">
                        <button @click="checkAnswer(option)"
                            class="btn btn-dark w-100 py-4 rounded-4 fs-3 fw-bold border border-white border-opacity-10 hover-border-primary transition-all shadow-lg"
                            :class="answered && option === currentQuestion.answer ? 'bg-success border-success' : (answered && option !== currentQuestion.answer && selectedArg === option ? 'bg-danger border-danger' : 'bg-gray-800/50')">
                            <span x-text="option"></span>
                        </button>
                    </div>
                </template>
            </div>
        </div>

        <!-- GAME OVER -->
        <div x-show="gameOver" class="text-center animate-zoom-in" style="display: none;">
            <div class="mb-4 display-1">🏆</div>
            <h2 class="display-4 fw-bold text-white mb-2">Game Over!</h2>
            <p class="text-gray-400 fs-4 mb-4">Final Score: <strong class="text-warning" x-text="score"></strong></p>

            <div class="d-flex justify-content-center gap-3 mt-5">
                <button @click="startGame()" class="btn btn-primary rounded-pill px-4 py-2 hover-scale">
                    <i class="fas fa-redo me-2"></i> Play Again
                </button>
                <a href="<?= app_base_url('/quiz') ?>" class="btn btn-outline-light rounded-pill px-4 py-2 hover-scale">
                    Dashboard
                </a>
            </div>
        </div>

    </main>
</div>

<style>
    /* Custom animations */
    .tracking-wider {
        letter-spacing: 0.1em;
    }

    .hover-scale {
        transition: transform 0.2s;
    }

    .hover-scale:hover {
        transform: scale(1.05);
    }

    .hover-border-primary:hover {
        border-color: #a855f7 !important;
        background: rgba(168, 85, 247, 0.1);
    }

    .animate-float {
        animation: float 3s ease-in-out infinite;
    }

    @keyframes float {
        0% {
            transform: translateY(0px);
        }

        50% {
            transform: translateY(-10px);
        }

        100% {
            transform: translateY(0px);
        }
    }

    .animate-pulse {
        animation: pulse 0.5s infinite;
    }

    @keyframes pulse {

        0%,
        100% {
            opacity: 1;
        }

        50% {
            opacity: 0.5;
        }
    }
</style>

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script>
    function mathGame() {
        return {
            isPlaying: false,
            gameOver: false,
            score: 0,
            timeLeft: 0.0,
            timer: null,
            currentQuestion: {
                text: '2 + 2',
                options: [3, 4, 5, 6],
                answer: 4
            },
            answered: false,
            selectedArg: null,
            difficulty: 1,

            startGame() {
                this.isPlaying = true;
                this.gameOver = false;
                this.score = 0;
                this.difficulty = 1;
                this.nextQuestion();
                this.startTimer(10); // Start with 10 seconds
            },

            startTimer(seconds) {
                this.timeLeft = seconds;
                if (this.timer) clearInterval(this.timer);
                this.timer = setInterval(() => {
                    this.timeLeft -= 0.1;
                    if (this.timeLeft <= 0) {
                        this.endGame();
                    }
                }, 100);
            },

            generateQuestion() {
                const ops = ['+', '-', '*'];
                const op = ops[Math.floor(Math.random() * Math.min(this.difficulty, 3))]; // Unlock operators

                let a, b, ans;
                let range = 10 * this.difficulty;

                if (op === '+') {
                    a = Math.floor(Math.random() * range) + 1;
                    b = Math.floor(Math.random() * range) + 1;
                    ans = a + b;
                } else if (op === '-') {
                    a = Math.floor(Math.random() * range) + 1;
                    b = Math.floor(Math.random() * a); // Ensure positive result
                    ans = a - b;
                } else if (op === '*') {
                    a = Math.floor(Math.random() * range / 2) + 2;
                    b = Math.floor(Math.random() * 10) + 1;
                    ans = a * b;
                }

                // Generate options
                let options = new Set([ans]);
                while (options.size < 4) {
                    let offset = Math.floor(Math.random() * 10) - 5;
                    let wrong = ans + offset;
                    if (wrong !== ans && wrong >= 0) options.add(wrong);
                }

                return {
                    text: `${a} ${op} ${b}`,
                    options: Array.from(options).sort(() => Math.random() - 0.5),
                    answer: ans
                };
            },

            nextQuestion() {
                this.answered = false;
                this.currentQuestion = this.generateQuestion();
            },

            checkAnswer(choice) {
                if (this.answered || this.gameOver) return;
                this.answered = true;
                this.selectedArg = choice;

                if (choice === this.currentQuestion.answer) {
                    // Correct
                    this.score += 10 * this.difficulty;
                    this.difficulty += 0.1; // Increase difficulty

                    // Add bonus time (max 15s)
                    this.timeLeft = Math.min(this.timeLeft + 2, 15);

                    setTimeout(() => this.nextQuestion(), 500);
                } else {
                    // Wrong
                    setTimeout(() => this.endGame(), 800);
                }
            },

            endGame() {
                this.isPlaying = false;
                this.gameOver = true;
                clearInterval(this.timer);
            }
        }
    }
</script>