<div class="min-h-screen bg-gradient-to-br from-slate-900 via-indigo-900 to-purple-900 relative overflow-hidden" x-data="blueprintRevealGame()">
    <!-- Premium Background Effects -->
    <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%239C92AC" fill-opacity="0.04"%3E%3Ccircle cx="30" cy="30" r="2"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-40"></div>

    <!-- Animated Background Elements -->
    <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-gradient-to-r from-indigo-500/15 to-purple-500/15 rounded-full blur-3xl animate-pulse"></div>
    <div class="absolute bottom-1/4 right-1/4 w-80 h-80 bg-gradient-to-r from-purple-500/15 to-pink-500/15 rounded-full blur-3xl animate-pulse delay-1000"></div>
    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-64 h-64 bg-gradient-to-r from-emerald-500/10 to-cyan-500/10 rounded-full blur-3xl animate-pulse delay-2000"></div>

    <!-- Premium HUD -->
    <header class="relative z-20 p-6">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <div class="flex items-center gap-6">
                <a href="<?= app_base_url('/blueprint') ?>" class="group inline-flex items-center justify-center w-14 h-14 bg-slate-800/80 backdrop-blur-xl border border-slate-700/50 rounded-2xl text-white hover:bg-slate-700/80 hover:border-slate-600/50 transition-all duration-300 hover:transform hover:scale-110 shadow-lg">
                    <i class="fas fa-arrow-left text-xl group-hover:-translate-x-1 transition-transform duration-300"></i>
                </a>
                <div>
                    <h1 class="text-3xl md:text-4xl font-black text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 via-purple-400 to-pink-400 mb-1 tracking-tight">
                        BLUEPRINT REVELATION
                    </h1>
                    <p class="text-slate-300 font-light text-sm">Section <span x-text="currentSection"></span> of <span x-text="totalSections"></span> • <span x-text="blueprint.title"></span></p>
                </div>
            </div>

            <!-- Progress & Stats -->
            <div class="flex items-center gap-6">
                <!-- Completion Progress -->
                <div class="bg-slate-800/80 backdrop-blur-xl border border-indigo-400/30 rounded-2xl px-6 py-4 shadow-lg">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-r from-indigo-500 to-purple-500 rounded-xl flex items-center justify-center shadow-lg">
                            <i class="fas fa-chart-line text-white"></i>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-indigo-400" x-text="completionPercentage + '%'"></div>
                            <div class="text-xs uppercase tracking-wider text-slate-400 font-medium">Complete</div>
                        </div>
                    </div>
                </div>

                <!-- Timer -->
                <div class="bg-slate-800/80 backdrop-blur-xl border rounded-2xl px-6 py-4 shadow-lg"
                     :class="timeLeft <= 10 ? 'border-red-400/50 shadow-red-400/20' : 'border-slate-700/50'">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center shadow-lg"
                             :class="timeLeft <= 10 ? 'bg-red-500' : 'bg-slate-700'">
                            <i class="fas fa-clock text-white"></i>
                        </div>
                        <div>
                            <div class="text-2xl font-bold" :class="timeLeft <= 10 ? 'text-red-400' : 'text-white'" x-text="timeLeft + 's'"></div>
                            <div class="text-xs uppercase tracking-wider text-slate-400 font-medium">Time Left</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Game Area -->
    <main class="relative z-20 max-w-7xl mx-auto px-8 pb-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

            <!-- Left: Blueprint Display -->
            <div class="space-y-6">
                <!-- Blueprint Container -->
                <div class="bg-slate-800/90 backdrop-blur-xl border border-slate-700/50 rounded-3xl p-8 shadow-2xl">
                    <div class="text-center mb-6">
                        <h2 class="text-2xl font-bold text-white mb-2">Engineering Blueprint</h2>
                        <p class="text-slate-400 text-sm">Master the terminology to reveal blueprint sections</p>
                    </div>

                    <!-- Blueprint SVG Display -->
                    <div class="relative bg-slate-900/50 rounded-2xl p-4 border border-slate-600/30">
                        <div class="blueprint-container max-w-full overflow-hidden rounded-xl">
                            <!-- This will be populated by JavaScript with the SVG -->
                            <div id="blueprint-svg-container" class="flex items-center justify-center min-h-96">
                                <div class="text-center text-slate-400">
                                    <i class="fas fa-drafting-compass text-6xl mb-4"></i>
                                    <p>Blueprint sections will be revealed as you progress</p>
                                </div>
                            </div>
                        </div>

                        <!-- Layer Progress Indicator -->
                        <div class="mt-4 grid grid-cols-5 gap-2">
                            <template x-for="i in 5" :key="i">
                                <div class="h-2 rounded-full transition-all duration-500"
                                     :class="i <= revealedLayers.length ? 'bg-gradient-to-r from-indigo-500 to-purple-500' : 'bg-slate-700'">
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Educational Content Panel -->
                <div class="bg-slate-800/90 backdrop-blur-xl border border-slate-700/50 rounded-3xl p-6 shadow-2xl" x-show="showEducationalContent" x-transition>
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-graduation-cap text-white"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-white mb-2">Learning Moment</h3>
                            <div class="text-slate-300 space-y-2" x-html="educationalContent"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Game Interface -->
            <div class="space-y-6">
                <!-- Question Card -->
                <div class="bg-slate-800/90 backdrop-blur-xl border border-slate-700/50 rounded-3xl p-8 shadow-2xl">
                    <div class="text-center mb-6">
                        <h3 class="text-xl font-bold text-white mb-2">Terminology Challenge</h3>
                        <p class="text-slate-400 text-sm">Match the engineering term with its correct definition</p>
                    </div>

                    <!-- Current Question -->
                    <div class="bg-indigo-50 border border-indigo-100 rounded-2xl p-6 mb-6">
                        <div class="text-sm text-indigo-600 uppercase tracking-wider font-medium mb-2">Engineering Term</div>
                        <div class="text-2xl font-bold text-indigo-800" x-text="currentQuestion.term || 'Loading...'"></div>
                    </div>

                    <!-- Answer Options -->
                    <div class="space-y-3">
                        <template x-for="(option, index) in currentQuestion.options" :key="index">
                            <button @click="selectAnswer(option)"
                                class="w-full p-4 text-left bg-slate-700/50 hover:bg-slate-600/50 border border-slate-600/30 hover:border-slate-500/50 rounded-xl transition-all duration-200 group"
                                :class="selectedAnswer === option ? 'bg-indigo-600/20 border-indigo-400/50' : ''">
                                <div class="flex items-center justify-between">
                                    <span class="text-white font-medium" x-text="option"></span>
                                    <div class="w-6 h-6 rounded-full border-2 border-slate-500 group-hover:border-slate-400 transition-colors"
                                         :class="selectedAnswer === option ? 'border-indigo-400 bg-indigo-400' : ''">
                                        <i class="fas fa-check text-white text-xs" x-show="selectedAnswer === option"></i>
                                    </div>
                                </div>
                            </button>
                        </template>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex gap-3 mt-6">
                        <button @click="useHint()"
                            class="flex-1 py-3 px-4 bg-gradient-to-r from-yellow-500 to-orange-600 hover:from-yellow-600 hover:to-orange-700 text-black font-bold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed"
                            :disabled="hintUsed || !currentQuestion.term">
                            <i class="fas fa-lightbulb mr-2"></i>
                            Hint (-5 coins)
                        </button>
                        <button @click="submitAnswer()"
                            class="flex-1 py-3 px-4 bg-gradient-to-r from-indigo-600 to-purple-700 hover:from-indigo-700 hover:to-purple-800 text-white font-bold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed"
                            :disabled="!selectedAnswer || !currentQuestion.term">
                            <i class="fas fa-check mr-2"></i>
                            Submit
                        </button>
                    </div>
                </div>

                <!-- Results Panel -->
                <div class="bg-slate-800/90 backdrop-blur-xl border border-slate-700/50 rounded-3xl p-6 shadow-2xl" x-show="showResult" x-transition>
                    <div class="text-center">
                        <div class="w-16 h-16 mx-auto mb-4 rounded-full flex items-center justify-center"
                             :class="result.correct ? 'bg-green-500' : 'bg-red-500'">
                            <i class="fas text-2xl text-white" :class="result.correct ? 'fa-check' : 'fa-times'"></i>
                        </div>

                        <h3 class="text-xl font-bold mb-2" :class="result.correct ? 'text-green-400' : 'text-red-400'"
                            x-text="result.correct ? 'Correct!' : 'Incorrect'"></h3>

                        <p class="text-slate-300 mb-4" x-text="result.message"></p>

                        <div class="flex gap-3">
                            <button @click="nextQuestion()"
                                class="flex-1 py-3 px-4 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-bold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300">
                                <i class="fas fa-arrow-right mr-2"></i>
                                Continue
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Progress Summary -->
                <div class="bg-slate-800/90 backdrop-blur-xl border border-slate-700/50 rounded-3xl p-6 shadow-2xl">
                    <h4 class="text-lg font-bold text-white mb-4">Section Progress</h4>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-slate-400">Questions Answered</span>
                            <span class="text-white font-bold" x-text="stats.answered"></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-400">Correct Answers</span>
                            <span class="text-green-400 font-bold" x-text="stats.correct"></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-400">Current Streak</span>
                            <span class="text-indigo-400 font-bold" x-text="stats.streak"></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-400">Coins Earned</span>
                            <span class="text-yellow-400 font-bold" x-text="stats.coinsEarned"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Hidden data for JavaScript -->
    <script type="application/json" id="game-data">
        <?= json_encode([
            'blueprint' => $blueprint,
            'terms' => $terms,
            'currentSection' => $currentSection,
            'totalSections' => $totalSections,
            'revealedLayers' => $revealedLayers,
            'completionPercentage' => $completionPercentage,
            'educationContent' => $educationContent
        ]) ?>
    </script>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        function blueprintRevealGame() {
            return {
                // Game state
                blueprint: <?= json_encode($blueprint) ?>,
                currentSection: <?= $currentSection ?>,
                totalSections: <?= $totalSections ?>,
                revealedLayers: <?= json_encode($revealedLayers) ?>,
                completionPercentage: <?= $completionPercentage ?>,

                // Question data
                questions: <?= json_encode($terms) ?>,
                currentQuestionIndex: 0,
                currentQuestion: {},
                selectedAnswer: null,
                showResult: false,
                result: {},

                // Game stats
                stats: {
                    answered: 0,
                    correct: 0,
                    streak: 0,
                    coinsEarned: 0
                },

                // UI state
                timeLeft: 60,
                timer: null,
                hintUsed: false,
                showEducationalContent: false,
                educationalContent: '',

                init() {
                    this.loadBlueprintSVG();
                    this.startTimer();
                    this.nextQuestion();
                },

                loadBlueprintSVG() {
                    // Load and render blueprint with revealed layers
                    fetch(`<?= app_base_url('/api/blueprint/render/') ?>${this.blueprint.id}`)
                        .then(response => response.text())
                        .then(svg => {
                            document.getElementById('blueprint-svg-container').innerHTML = svg;
                        })
                        .catch(error => {
                            console.error('Error loading blueprint:', error);
                        });
                },

                startTimer() {
                    this.timer = setInterval(() => {
                        this.timeLeft--;
                        if (this.timeLeft <= 0) {
                            this.endGame();
                        }
                    }, 1000);
                },

                nextQuestion() {
                    if (this.currentQuestionIndex >= this.questions.length) {
                        this.endSection();
                        return;
                    }

                    const question = this.questions[this.currentQuestionIndex];
                    this.currentQuestion = {
                        term: question.term,
                        options: this.shuffleArray([...question.wrong_definitions, question.correct_definition]),
                        correct: question.correct_definition
                    };

                    this.selectedAnswer = null;
                    this.showResult = false;
                    this.hintUsed = false;
                    this.showEducationalContent = false;
                },

                selectAnswer(answer) {
                    this.selectedAnswer = answer;
                },

                submitAnswer() {
                    if (!this.selectedAnswer) return;

                    const isCorrect = this.selectedAnswer === this.currentQuestion.correct;
                    this.stats.answered++;

                    if (isCorrect) {
                        this.stats.correct++;
                        this.stats.streak++;
                        this.stats.coinsEarned += 10;
                    } else {
                        this.stats.streak = 0;
                        this.showEducationalContent = true;
                        this.educationalContent = "Incorrect. The correct definition is: <strong>" + this.currentQuestion.correct + "</strong>";
                    }

                    this.result = {
                        correct: isCorrect,
                        message: isCorrect ? "Correct! Blueprint section revealed." : "Incorrect. Try again."
                    };

                    this.showResult = true;
                    this.currentQuestionIndex++;
                },

                useHint() {
                    if (this.hintUsed) return;

                    // Deduct coins and show hint
                    this.hintUsed = true;
                    this.stats.coinsEarned -= 5;

                    // Show educational hint
                    this.showEducationalContent = true;
                    this.educationalContent = "<strong>Hint:</strong> " + this.currentQuestion.correct.substring(0, 50) + "...";
                },

                endSection() {
                    // Submit section results
                    fetch('<?= app_base_url("/quiz/terminology/submit") ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            blueprint_id: this.blueprint.id,
                            section_id: this.currentSection,
                            correct_matches: this.stats.correct,
                            total_terms: this.stats.answered,
                            time_spent: 60 - this.timeLeft
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            if (data.sectionPassed) {
                                this.revealedLayers = data.revealedLayers;
                                this.completionPercentage = data.completionPercentage;

                                if (data.completionPercentage >= 100) {
                                    alert('Blueprint completed! +' + data.coinsAwarded + ' coins earned!');
                                    window.location.href = '<?= app_base_url("/blueprint") ?>';
                                } else {
                                    alert(data.message);
                                    window.location.reload();
                                }
                            } else {
                                alert(data.message);
                                // Allow retry
                                this.currentQuestionIndex = 0;
                                this.stats = { answered: 0, correct: 0, streak: 0, coinsEarned: 0 };
                                this.nextQuestion();
                            }
                        }
                    });
                },

                endGame() {
                    clearInterval(this.timer);
                    alert('Time\'s up! Section failed.');
                    window.location.href = '<?= app_base_url("/blueprint") ?>';
                },

                shuffleArray(array) {
                    for (let i = array.length - 1; i > 0; i--) {
                        const j = Math.floor(Math.random() * (i + 1));
                        [array[i], array[j]] = [array[j], array[i]];
                    }
                    return array;
                }
            }
        }
    </script>
</div>