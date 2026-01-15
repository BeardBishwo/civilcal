<div class="min-vh-100 d-flex flex-column bg-dark" style="font-family: 'Outfit', sans-serif;" x-data="tfGame()">

    <!-- HUD -->
    <header class="p-3 d-flex justify-content-between align-items-center bg-gray-900 border-bottom border-gray-800">
        <a href="<?= app_base_url('/quiz') ?>" class="btn btn-sm btn-outline-secondary rounded-pill">
            <i class="fas fa-times me-1"></i> Exit
        </a>
        <div class="d-flex gap-3">
            <div class="badge bg-success rounded-pill px-3">
                <i class="fas fa-check me-1"></i> <span x-text="score">0</span>
            </div>
            <div class="badge bg-danger rounded-pill px-3">
                <i class="fas fa-heart me-1"></i> <span x-text="lives">3</span>
            </div>
        </div>
    </header>

    <!-- Main Game -->
    <main class="flex-grow-1 d-flex flex-column align-items-center justify-content-center p-4 position-relative overflow-hidden">

        <!-- Loading -->
        <div x-show="loading" class="text-white animate-pulse">
            Loading Questions...
        </div>

        <!-- Question Card -->
        <div x-show="!loading && !gameOver" class="card bg-gray-800 text-white border-0 shadow-2xl rounded-5 w-100 max-w-lg overflow-hidden position-relative"
            x-transition:enter="transition transform ease-out duration-300"
            x-transition:enter-start="translate-y-10 opacity-0"
            x-transition:enter-end="translate-y-0 opacity-100">

            <!-- Progress Bar -->
            <div class="position-absolute top-0 start-0 w-100 bg-gray-700" style="height: 4px;">
                <div class="bg-blue-500 h-100" :style="`width: ${(timeLeft/10)*100}%`"></div>
            </div>

            <div class="card-body p-5 text-center">
                <div class="text-uppercase text-gray-500 tracking-wider mb-4 font-sm">True or False?</div>
                <h3 class="fw-bold mb-5 lh-base" x-text="current?.text"></h3>

                <div class="d-grid gap-3 grid-cols-2 d-flex justify-content-center">
                    <button @click="answer(true)" class="btn btn-lg btn-success rounded-4 py-4 px-5 fs-4 fw-bold shadow-lg hover-scale">
                        <i class="fas fa-check-circle mb-1 d-block fs-2"></i> TRUE
                    </button>
                    <button @click="answer(false)" class="btn btn-lg btn-danger rounded-4 py-4 px-5 fs-4 fw-bold shadow-lg hover-scale">
                        <i class="fas fa-times-circle mb-1 d-block fs-2"></i> FALSE
                    </button>
                </div>
            </div>
        </div>

        <!-- Game Over -->
        <div x-show="gameOver" class="text-center text-white animate-zoom-in" style="display: none;">
            <h1 class="display-1 mb-3">🏁</h1>
            <h2 class="mb-2">Run Complete!</h2>
            <p class="text-gray-400 mb-4">You scored <strong class="text-success fs-3" x-text="score"></strong></p>
            <button @click="init()" class="btn btn-primary rounded-pill px-5 py-3 fw-bold">Try Again</button>
        </div>

    </main>
</div>

<style>
    .hover-scale:hover {
        transform: scale(1.05);
    }

    .animate-zoom-in {
        animation: zoomIn 0.5s;
    }

    @keyframes zoomIn {
        from {
            transform: scale(0.5);
            opacity: 0;
        }

        to {
            transform: scale(1);
            opacity: 1;
        }
    }
</style>

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script>
    function tfGame() {
        return {
            loading: true,
            gameOver: false,
            questions: [],
            current: null,
            index: 0,
            score: 0,
            lives: 3,
            timeLeft: 10,
            timer: null,

            async init() {
                this.loading = true;
                this.gameOver = false;
                this.score = 0;
                this.lives = 3;
                this.index = 0;

                // Allow adding route in next step for data
                // Or use existing controller if route added
                try {
                    // Assuming route added: /quiz/true-false/data
                    const res = await fetch('<?= app_base_url("/quiz/true-false/data") ?>');
                    const data = await res.json();
                    if (data.success && data.questions.length > 0) {
                        this.questions = data.questions;
                        this.nextQuestion();
                    } else {
                        alert('Game Error: ' + (data.error || 'No questions found!'));
                    }
                } catch (e) {
                    console.error(e);
                    alert('Connection Error: ' + e.message + ' URL: <?= app_base_url('/quiz/true-false/data') ?>');
                } finally {
                    this.loading = false;
                }
            },

            nextQuestion() {
                if (this.index >= this.questions.length || this.lives <= 0) {
                    this.endGame();
                    return;
                }
                this.current = this.questions[this.index];
                this.index++;
                this.startTimer();
            },

            startTimer() {
                this.timeLeft = 10;
                if (this.timer) clearInterval(this.timer);
                this.timer = setInterval(() => {
                    this.timeLeft -= 0.1;
                    if (this.timeLeft <= 0) {
                        this.handleWrong();
                    }
                }, 100);
            },

            answer(choice) {
                // Check answer
                // correct_answer in DB is usually '1' or '0', or 'true'/'false'
                // We need to normalize.
                let correct = this.current.correct_answer;
                let boolCorrect = (correct === '1' || correct === true || correct === 'true');

                if (choice === boolCorrect) {
                    this.score++;
                    // Slight Visual feedback?
                    this.nextQuestion();
                } else {
                    this.handleWrong();
                }
            },

            handleWrong() {
                this.lives--;
                // Shake effect?
                if (this.lives <= 0) {
                    this.endGame();
                } else {
                    this.nextQuestion();
                }
            },

            endGame() {
                this.gameOver = true;
                clearInterval(this.timer);
            }
        }
    }
</script>