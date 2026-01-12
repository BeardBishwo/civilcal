<?php

/**
 * Exam Room Interface
 * Premium SaaS Design (Refactored)
 * Stack: PHP + Tailwind CSS + Alpine.js
 */
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title ?? 'Exam Room'); ?> | Bishwo Calculator</title>

    <!-- Load Tailwind CSS -->
    <link rel="stylesheet" href="<?php echo app_base_url('themes/default/assets/css/quiz.min.css?v=' . time()); ?>">
    <!-- Load Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Code Highlight (Optional) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/styles/atom-one-dark.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/highlight.min.js"></script>
</head>

<body class="bg-background text-white h-screen flex flex-col overflow-hidden"
    x-data="examRoom()"
    :class="{'fullscreen-mode': focusMode}"
    x-init="$el.classList.add('animate-fade-in-up')">

    <?php
    // PHP Variables for Alpine
    $durationSecs = ($attempt['duration_minutes'] ?? 60) * 60;
    $startTime = $attempt['started_at_ts'] ?? time();
    $elapsed = time() - $startTime;
    $remaining = max(0, $durationSecs - $elapsed);
    $questionsJson = json_encode($questions);
    // Prepare Saved Answers JSON
    $savedAnswersJson = json_encode($savedAnswers ?? new stdClass());
    ?>

    <!-- Header -->
    <header class="h-16 bg-surface border-b border-white/10 flex items-center justify-between px-6 z-50 shrink-0">
        <div class="flex items-center gap-3">
            <a href="<?php echo app_base_url('quiz'); ?>" class="text-gray-400 hover:text-white transition-colors">
                <i class="fas fa-chevron-left"></i>
            </a>
            <div class="h-6 w-px bg-white/10"></div>
            <h1 class="font-bold text-lg flex items-center gap-2">
                <i class="fas fa-layer-group text-primary"></i>
                <span class="hidden md:inline"><?php echo htmlspecialchars($title); ?></span>
                <span class="md:hidden">Exam Room</span>
            </h1>
        </div>

        <div class="flex items-center gap-4">
            <!-- Timer -->
            <div class="font-mono text-lg font-bold bg-yellow-500/10 text-yellow-500 px-4 py-1.5 rounded-lg border border-yellow-500/20 flex items-center gap-2"
                :class="{'animate-pulse text-red-500 border-red-500/30 bg-red-500/10': remainingSecs < 300}">
                <i class="fas fa-clock text-xs"></i>
                <span x-text="formattedTime"></span>
            </div>

            <button @click="toggleFocus()"
                class="w-10 h-10 rounded-lg bg-white/5 hover:bg-white/10 flex items-center justify-center transition-colors text-gray-400 hover:text-white"
                title="Toggle Zen Mode">
                <i class="fas" :class="focusMode ? 'fa-compress' : 'fa-expand'"></i>
            </button>

            <button @click="submitExam()" class="bg-green-600 hover:bg-green-500 text-white px-5 py-2 rounded-lg font-semibold transition-all shadow-lg hover:shadow-green-500/20 flex items-center gap-2">
                <span>Submit</span>
                <i class="fas fa-paper-plane text-xs"></i>
            </button>
        </div>
    </header>

    <!-- Main Layout -->
    <main class="flex-1 flex overflow-hidden">

        <!-- Sidebar - Question Palette -->
        <aside class="w-72 bg-surface/50 border-r border-white/10 flex flex-col hidden md:flex shrink-0">
            <div class="p-4 border-b border-white/10 font-medium text-gray-400 text-xs uppercase tracking-wider flex justify-between items-center">
                <span>Question Palette</span>
                <span class="bg-white/5 px-2 py-1 rounded text-white" x-text="questions.length"></span>
            </div>

            <div class="flex-1 overflow-y-auto p-4 custom-scrollbar">
                <div class="grid grid-cols-5 gap-2">
                    <template x-for="(q, index) in questions" :key="q.id">
                        <button @click="loadQuestion(index)"
                            class="aspect-square flex items-center justify-center rounded-lg text-sm font-semibold transition-all border"
                            :class="{
                                'bg-primary border-primary text-white shadow-lg shadow-primary/20': currentQ === index,
                                'bg-green-500 border-green-500 text-white': currentQ !== index && isAnswered(q.id),
                                'bg-yellow-500/20 border-yellow-500 text-yellow-500': currentQ !== index && isMarked(index) && !isAnswered(q.id),
                                'bg-white/5 border-white/10 text-gray-400 hover:border-white/30 hover:text-white': currentQ !== index && !isAnswered(q.id) && !isMarked(index)
                            }">
                            <span x-text="index + 1"></span>
                            <!-- Mark Indicator -->
                            <div x-show="isMarked(index)" class="absolute top-0.5 right-0.5 w-1.5 h-1.5 rounded-full bg-yellow-400"></div>
                        </button>
                    </template>
                </div>
            </div>

            <div class="p-4 border-t border-white/10 text-xs text-gray-400 space-y-2">
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full bg-green-500"></div> Answered
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full bg-primary"></div> Current
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full bg-yellow-500"></div> Marked
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full bg-white/20 border border-white/10"></div> Not Answered
                </div>
            </div>
        </aside>

        <!-- Question Area -->
        <div class="flex-1 overflow-y-auto bg-background p-6 md:p-10 custom-scrollbar relative">
            <div class="max-w-4xl mx-auto pb-20">

                <!-- Question Card -->
                <div class="glass-card mb-8">
                    <!-- Meta -->
                    <div class="flex items-start justify-between mb-6">
                        <div class="flex items-center gap-3">
                            <span class="bg-primary/10 text-primary px-3 py-1 rounded-full text-sm font-bold border border-primary/20">
                                Question <span x-text="currentQ + 1"></span>
                            </span>
                            <span class="text-gray-500 text-sm">/ <span x-text="questions.length"></span></span>
                        </div>
                        <div class="flex items-center gap-2 text-sm text-gray-400 bg-white/5 px-3 py-1 rounded-lg border border-white/5">
                            <span class="text-green-400">+<span x-text="questions[currentQ].default_marks"></span></span>
                            <span class="text-white/20">|</span>
                            <span class="text-red-400">-<span x-text="questions[currentQ].default_negative_marks"></span></span>
                        </div>
                    </div>

                    <!-- Question Text -->
                    <div class="prose prose-invert max-w-none mb-8 text-lg md:text-xl font-medium leading-relaxed">
                        <div x-html="parseContent(questions[currentQ].content)"></div>
                    </div>

                    <!-- Options -->
                    <div class="grid gap-3">
                        <template x-for="(opt, key) in parseOptions(questions[currentQ].options)" :key="key">
                            <div @click="selectOption(questions[currentQ].id, key)"
                                class="group relative flex items-start gap-4 p-4 rounded-xl border border-white/10 bg-white/[0.02] cursor-pointer transition-all hover:bg-white/[0.05] hover:border-primary/50"
                                :class="{'border-primary bg-primary/10 shadow-lg shadow-primary/5 ring-1 ring-primary/50': isSelected(questions[currentQ].id, key)}">

                                <!-- Radio Indicator -->
                                <div class="mt-1 w-5 h-5 rounded-full border-2 border-gray-500 group-hover:border-primary flex items-center justify-center shrink-0 transition-colors"
                                    :class="{'border-primary bg-primary': isSelected(questions[currentQ].id, key)}">
                                    <div class="w-2 h-2 rounded-full bg-white transform scale-0 transition-transform"
                                        :class="{'scale-100': isSelected(questions[currentQ].id, key)}"></div>
                                </div>

                                <!-- Option Text -->
                                <div class="text-gray-300 group-hover:text-white text-base md:text-lg select-none" x-text="opt.text || opt"></div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Mobile Palette Toggle (Sticky Bottom) -->
                <div class="md:hidden fixed bottom-0 left-0 right-0 bg-surface border-t border-white/10 p-4 z-40 flex overflow-x-auto gap-2">
                    <template x-for="(q, index) in questions" :key="q.id">
                        <button @click="loadQuestion(index)"
                            class="w-10 h-10 shrink-0 flex items-center justify-center rounded-lg text-sm font-bold border"
                            :class="{
                                'bg-primary border-primary text-white': currentQ === index,
                                'bg-green-500 border-green-500 text-white': currentQ !== index && isAnswered(q.id),
                                'bg-white/5 border-white/10 text-gray-400': currentQ !== index && !isAnswered(q.id)
                            }">
                            <span x-text="index + 1"></span>
                        </button>
                    </template>
                </div>

            </div>
        </div>

        <!-- Navigation Footer (Desktop) -->
        <div class="w-full md:w-auto fixed bottom-0 md:bottom-auto md:relative right-0 bg-background/90 md:bg-transparent backdrop-blur md:backdrop-filter-none border-t md:border-t-0 p-4 z-50 flex items-center justify-between md:justify-end gap-4 max-w-4xl mx-auto md:absolute md:bottom-8 md:right-10 md:left-10 pointer-events-none">
            <div class="pointer-events-auto flex items-center gap-4 w-full justify-between">
                <button @click="prevQ()" :disabled="currentQ === 0"
                    class="bg-surface hover:bg-surfaceHover text-white px-6 py-3 rounded-xl font-semibold border border-white/10 disabled:opacity-50 disabled:cursor-not-allowed transition-all flex items-center gap-2">
                    <i class="fas fa-arrow-left"></i> Previous
                </button>

                <button @click="toggleMark(currentQ)"
                    class="bg-yellow-500/10 hover:bg-yellow-500/20 text-yellow-500 px-6 py-3 rounded-xl font-semibold border border-yellow-500/20 transition-all flex items-center gap-2">
                    <i class="fas fa-flag" :class="{'text-yellow-500': isMarked(currentQ), 'text-yellow-500/50': !isMarked(currentQ)}"></i>
                    <span x-text="isMarked(currentQ) ? 'Unmark' : 'Mark'"></span>
                </button>

                <button @click="nextQ()"
                    class="bg-primary hover:bg-primary/90 text-white px-8 py-3 rounded-xl font-semibold shadow-lg shadow-primary/20 transition-all flex items-center gap-2 group">
                    <span x-text="currentQ === questions.length - 1 ? 'Review' : 'Next'"></span>
                    <i class="fas fa-arrow-right transform group-hover:translate-x-1 transition-transform"></i>
                </button>
            </div>
        </div>

    </main>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('examRoom', () => ({
                remainingSecs: <?php echo $remaining; ?>,
                currentQ: 0,
                questions: <?php echo $questionsJson; ?>,
                storedAnswers: <?php echo $savedAnswersJson; ?>, // Format: {qid: optionKey}
                markedQuestions: new Set(),

                csrfToken: '<?php echo $csrfToken; ?>',
                attemptId: '<?php echo $attempt['id']; ?>',
                nonce: '<?php echo $quizNonce; ?>',

                focusMode: false,
                timerInterval: null,

                init() {
                    this.startTimer();
                    // Load marked questions from local storage if needed, or session

                    // Highlight code blocks
                    this.$watch('currentQ', () => {
                        this.$nextTick(() => {
                            hljs.highlightAll();
                        });
                    });
                },

                get formattedTime() {
                    const h = Math.floor(this.remainingSecs / 3600);
                    const m = Math.floor((this.remainingSecs % 3600) / 60);
                    const s = this.remainingSecs % 60;
                    return `${h.toString().padStart(2, '0')}:${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`;
                },

                startTimer() {
                    this.timerInterval = setInterval(() => {
                        if (this.remainingSecs > 0) {
                            this.remainingSecs--;
                        } else {
                            this.timeUp();
                        }
                    }, 1000);
                },

                timeUp() {
                    clearInterval(this.timerInterval);
                    alert('Time is up! Submitting your exam automatically.');
                    this.submitExam(true);
                },

                loadQuestion(index) {
                    this.currentQ = index;
                },

                nextQ() {
                    if (this.currentQ < this.questions.length - 1) {
                        this.currentQ++;
                    }
                },

                prevQ() {
                    if (this.currentQ > 0) {
                        this.currentQ--;
                    }
                },

                parseContent(content) {
                    // Handle array content (text + image) from legacy system
                    if (typeof content === 'object' && content !== null) {
                        let html = content.text || '';
                        if (content.image) {
                            html += `<br><img src="${content.image}" class="mt-4 rounded-lg border border-white/10 max-h-96 mx-auto">`;
                        }
                        return html;
                    }
                    return content;
                },

                parseOptions(options) {
                    if (typeof options === 'string') {
                        try {
                            return JSON.parse(options);
                        } catch (e) {
                            return [];
                        }
                    }
                    return options;
                },

                isSelected(qid, key) {
                    // Handle looser type comparison just in case
                    return this.storedAnswers[qid] == key;
                },

                isAnswered(qid) {
                    return this.storedAnswers.hasOwnProperty(qid);
                },

                isMarked(index) {
                    return this.markedQuestions.has(index);
                },

                toggleMark(index) {
                    if (this.markedQuestions.has(index)) {
                        this.markedQuestions.delete(index);
                    } else {
                        this.markedQuestions.add(index);
                    }
                    // Force reactivity by re-assigning (Set doesn't trigger reactivity by default deep)
                    this.markedQuestions = new Set(this.markedQuestions);
                },

                toggleFocus() {
                    this.focusMode = !this.focusMode;
                    if (this.focusMode) {
                        document.documentElement.requestFullscreen().catch((e) => {
                            console.log(e);
                            this.focusMode = false; // Revert if denied
                        });
                    } else {
                        if (document.fullscreenElement) {
                            document.exitFullscreen();
                        }
                    }
                },

                selectOption(qid, key) {
                    // Optimistic UI Update
                    this.storedAnswers[qid] = key;

                    // Server Save
                    const formData = new FormData();
                    formData.append('attempt_id', this.attemptId);
                    formData.append('question_id', qid);
                    formData.append('selected_options', key);
                    formData.append('csrf_token', this.csrfToken);

                    fetch('<?php echo app_base_url("quiz/save-answer"); ?>', {
                        method: 'POST',
                        body: formData
                    }).catch(console.error);
                },

                submitExam(auto = false) {
                    if (!auto && !confirm('Are you sure you want to finish the exam?')) return;

                    const formData = new FormData();
                    formData.append('attempt_id', this.attemptId);
                    formData.append('nonce', this.nonce);

                    fetch('<?php echo app_base_url("quiz/submit"); ?>', {
                        method: 'POST',
                        body: formData
                    }).then(() => {
                        window.location.href = '<?php echo app_base_url("quiz/result/" . $attempt['id']); ?>';
                    });
                }
            }));
        });
    </script>
</body>

</html>