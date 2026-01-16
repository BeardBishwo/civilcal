<div class="min-h-screen bg-gradient-to-br from-indigo-900 via-purple-900 to-pink-900 relative overflow-hidden" x-data="mathGame()">

    <!-- Premium Background Effects -->
    <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%239C92AC" fill-opacity="0.03"%3E%3Ccircle cx="30" cy="30" r="2"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-30"></div>

    <!-- Animated Background Orbs -->
    <div class="absolute top-1/4 left-1/4 w-64 h-64 bg-gradient-to-r from-blue-500/20 to-purple-500/20 rounded-full blur-3xl animate-pulse"></div>
    <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-gradient-to-r from-pink-500/20 to-orange-500/20 rounded-full blur-3xl animate-pulse delay-1000"></div>
    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-80 h-80 bg-gradient-to-r from-cyan-500/10 to-blue-500/10 rounded-full blur-3xl animate-pulse delay-2000"></div>

    <!-- Premium HUD -->
    <header class="relative z-20 p-6 flex justify-between items-center">
        <a href="<?= app_base_url('/quiz') ?>" class="inline-flex items-center px-6 py-3 bg-slate-800/80 backdrop-blur-xl border border-slate-700/50 rounded-xl text-white font-semibold hover:bg-slate-700/80 hover:border-slate-600/50 transition-all duration-300 hover:transform hover:scale-105 shadow-lg">
            <i class="fas fa-arrow-left mr-3"></i>
            EXIT
        </a>

        <div class="flex items-center gap-8">
            <!-- Score Card -->
            <div class="bg-slate-800/80 backdrop-blur-xl border border-yellow-400/30 rounded-xl p-4 text-center shadow-lg hover:shadow-yellow-400/20 transition-all duration-300">
                <div class="text-xs uppercase tracking-wider text-slate-400 font-medium mb-1">Score</div>
                <div class="text-2xl font-bold text-yellow-400" x-text="score">0</div>
            </div>

            <!-- Time Card -->
            <div class="bg-slate-800/80 backdrop-blur-xl border rounded-xl p-4 text-center shadow-lg transition-all duration-300"
                 :class="timeLeft <= 5 ? 'border-red-400/50 shadow-red-400/20' : 'border-slate-700/50'">
                <div class="text-xs uppercase tracking-wider text-slate-400 font-medium mb-1">Time</div>
                <div class="text-2xl font-bold" :class="timeLeft <= 5 ? 'text-red-400 animate-pulse' : 'text-white'" x-text="timeLeft.toFixed(1) + 's'"></div>
            </div>
        </div>
    </header>

    <!-- Game Area -->
    <main class="relative z-20 flex-1 flex items-center justify-center px-6">

        <!-- START SCREEN -->
        <div x-show="!isPlaying && !gameOver" class="text-center max-w-2xl mx-auto animate-fade-in">
            <!-- Premium Icon -->
            <div class="relative mb-8">
                <div class="w-32 h-32 bg-gradient-to-r from-blue-500 via-purple-500 to-pink-500 rounded-full flex items-center justify-center mx-auto shadow-2xl shadow-purple-500/30">
                    <i class="fas fa-calculator text-white text-5xl"></i>
                </div>
                <div class="absolute -top-2 -right-2 w-12 h-12 bg-yellow-400 rounded-full animate-ping"></div>
            </div>

            <h1 class="text-7xl md:text-8xl font-black text-transparent bg-clip-text bg-gradient-to-r from-blue-400 via-purple-400 to-pink-400 mb-6 tracking-tight">
                MATH MANIA
            </h1>
            <p class="text-xl md:text-2xl text-slate-300 font-light leading-relaxed mb-12 max-w-xl mx-auto">
                Challenge your mathematical prowess! Solve equations at lightning speed and climb the leaderboards.
            </p>

            <button @click="startGame()" class="inline-flex items-center px-12 py-6 bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 hover:from-blue-700 hover:via-purple-700 hover:to-pink-700 text-white font-bold text-xl rounded-2xl shadow-2xl hover:shadow-purple-500/40 transition-all duration-300 hover:transform hover:scale-110">
                <i class="fas fa-play mr-4 text-2xl"></i>
                START CHALLENGE
            </button>

            <!-- Decorative Elements -->
            <div class="mt-12 flex justify-center">
                <div class="flex gap-4">
                    <div class="w-3 h-3 bg-blue-400 rounded-full animate-bounce"></div>
                    <div class="w-3 h-3 bg-purple-400 rounded-full animate-bounce delay-100"></div>
                    <div class="w-3 h-3 bg-pink-400 rounded-full animate-bounce delay-200"></div>
                </div>
            </div>
        </div>

        <!-- GAMEPLAY -->
        <div x-show="isPlaying" class="w-full max-w-2xl mx-auto text-center animate-fade-in" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100">

            <!-- Question Display -->
            <div class="relative mb-12">
                <div class="absolute inset-0 bg-gradient-to-r from-blue-500/20 via-purple-500/20 to-pink-500/20 rounded-3xl blur-2xl animate-pulse"></div>
                <div class="relative bg-slate-800/90 backdrop-blur-xl border border-slate-700/50 rounded-3xl p-12 shadow-2xl">
                    <div class="text-6xl md:text-7xl font-black text-white mb-4 animate-float" x-text="currentQuestion.text"></div>
                    <div class="w-24 h-1 bg-gradient-to-r from-blue-400 to-purple-400 rounded-full mx-auto"></div>
                </div>
            </div>

            <!-- Answer Options -->
            <div class="grid grid-cols-2 gap-6 max-w-lg mx-auto">
                <template x-for="(option, index) in currentQuestion.options" :key="index">
                    <button @click="checkAnswer(option)"
                        class="group relative py-6 px-8 bg-slate-800/80 backdrop-blur-xl border border-slate-700/50 rounded-2xl text-3xl font-bold text-white shadow-xl hover:shadow-2xl transition-all duration-300 hover:transform hover:scale-105 overflow-hidden"
                        :class="answered && option === currentQuestion.answer ? 'bg-green-500/90 border-green-400 text-white shadow-green-400/30' : (answered && option !== currentQuestion.answer && selectedArg === option ? 'bg-red-500/90 border-red-400 text-white shadow-red-400/30' : 'hover:border-purple-400/50 hover:bg-slate-700/80')">

                        <!-- Hover Glow Effect -->
                        <div class="absolute inset-0 bg-gradient-to-r from-blue-500/10 via-purple-500/10 to-pink-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

                        <!-- Content -->
                        <span class="relative z-10" x-text="option"></span>

                        <!-- Correct/Incorrect Icons -->
                        <div class="absolute top-2 right-2 opacity-0 transition-opacity duration-300"
                             :class="answered && option === currentQuestion.answer ? 'opacity-100' : ''">
                            <i class="fas fa-check text-green-300 text-lg"></i>
                        </div>
                        <div class="absolute top-2 right-2 opacity-0 transition-opacity duration-300"
                             :class="answered && option !== currentQuestion.answer && selectedArg === option ? 'opacity-100' : ''">
                            <i class="fas fa-times text-red-300 text-lg"></i>
                        </div>
                    </button>
                </template>
            </div>
        </div>

        <!-- GAME OVER -->
        <div x-show="gameOver" class="text-center max-w-2xl mx-auto animate-zoom-in" style="display: none;">
            <!-- Trophy Icon -->
            <div class="relative mb-8">
                <div class="w-32 h-32 bg-gradient-to-r from-yellow-400 via-orange-500 to-red-500 rounded-full flex items-center justify-center mx-auto shadow-2xl shadow-yellow-400/30 animate-bounce">
                    <i class="fas fa-trophy text-white text-5xl"></i>
                </div>
                <div class="absolute -top-2 -right-2 w-8 h-8 bg-green-400 rounded-full animate-ping"></div>
            </div>

            <h2 class="text-5xl md:text-6xl font-black text-white mb-4">CHALLENGE COMPLETE!</h2>
            <p class="text-2xl text-slate-300 mb-8">
                Final Score: <span class="text-yellow-400 font-bold text-3xl" x-text="score"></span>
            </p>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
                <div class="bg-slate-800/80 backdrop-blur-xl border border-slate-700/50 rounded-xl p-6 text-center">
                    <div class="text-2xl text-blue-400 mb-2">
                        <i class="fas fa-brain"></i>
                    </div>
                    <div class="text-sm text-slate-400 uppercase tracking-wider">Questions Solved</div>
                    <div class="text-xl font-bold text-white" x-text="Math.floor(score / 10)">0</div>
                </div>
                <div class="bg-slate-800/80 backdrop-blur-xl border border-slate-700/50 rounded-xl p-6 text-center">
                    <div class="text-2xl text-purple-400 mb-2">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="text-sm text-slate-400 uppercase tracking-wider">Time Survived</div>
                    <div class="text-xl font-bold text-white">10.0s</div>
                </div>
                <div class="bg-slate-800/80 backdrop-blur-xl border border-slate-700/50 rounded-xl p-6 text-center">
                    <div class="text-2xl text-pink-400 mb-2">
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="text-sm text-slate-400 uppercase tracking-wider">Difficulty Reached</div>
                    <div class="text-xl font-bold text-white" x-text="Math.floor(difficulty)">1</div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <button @click="startGame()" class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-bold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover:transform hover:scale-105">
                    <i class="fas fa-redo mr-3"></i>
                    Play Again
                </button>
                <a href="<?= app_base_url('/quiz') ?>" class="inline-flex items-center px-8 py-4 bg-slate-800/80 backdrop-blur-xl border border-slate-700/50 hover:border-slate-600/50 text-white font-bold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover:transform hover:scale-105">
                    <i class="fas fa-home mr-3"></i>
                    Dashboard
                </a>
            </div>
        </div>

    </main>
</div>

<style>
/* Premium Animations */
@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-15px); }
}

.animate-float {
    animation: float 4s ease-in-out infinite;
}

.animate-fade-in {
    animation: fadeIn 0.8s ease-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.animate-zoom-in {
    animation: zoomIn 0.6s ease-out;
}

@keyframes zoomIn {
    from { opacity: 0; transform: scale(0.8); }
    to { opacity: 1; transform: scale(1); }
}

/* Custom scrollbar */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: rgba(51, 65, 85, 0.3);
    border-radius: 4px;
}

::-webkit-scrollbar-thumb {
    background: linear-gradient(to bottom, #3b82f6, #8b5cf6, #ec4899);
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(to bottom, #2563eb, #7c3aed, #db2777);
}

/* Responsive text scaling */
@media (max-width: 768px) {
    .text-6xl { font-size: 3rem; }
    .text-7xl { font-size: 3.5rem; }
    .text-8xl { font-size: 4rem; }
}

/* Enhanced glow effects */
.glow-blue {
    box-shadow: 0 0 30px rgba(59, 130, 246, 0.3);
}

.glow-purple {
    box-shadow: 0 0 30px rgba(139, 92, 246, 0.3);
}

.glow-pink {
    box-shadow: 0 0 30px rgba(236, 72, 153, 0.3);
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