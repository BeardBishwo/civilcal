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

            <button @click="openReportModal()" class="w-10 h-10 rounded-lg bg-red-500/10 hover:bg-red-500/20 flex items-center justify-center transition-colors text-red-500 border border-red-500/20" title="Report Issue">
                <i class="fas fa-bug"></i>
            </button>

            <button @click="submitExam()" class="bg-green-600 hover:bg-green-500 text-white px-5 py-2 rounded-lg font-semibold transition-all shadow-lg hover:shadow-green-500/20 flex items-center gap-2">
                <span>Submit</span>
                <i class="fas fa-paper-plane text-xs"></i>
            </button>
        </div>
    </header>

    <!-- Report Modal -->
    <div x-show="reporting"
        class="fixed inset-0 z-[999] flex items-center justify-center bg-black/90 backdrop-blur-md p-4"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0">
        <div class="glass-card w-full max-w-md p-6 border-white/10" @click.away="reporting = false">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold flex items-center gap-2">
                    <i class="fas fa-flag text-red-500"></i> Report Question
                </h3>
                <button @click="reporting = false" class="text-gray-400 hover:text-white">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <form @submit.prevent="submitReport()">
                <div class="space-y-5">
                    <div>
                        <label class="block text-xs uppercase tracking-wider text-gray-500 font-bold mb-3">What is wrong?</label>
                        <div class="grid grid-cols-2 gap-3">
                            <template x-for="type in [
                                {id: 'wrong_answer', label: 'Wrong Answer', icon: 'fa-check-circle'},
                                {id: 'typo', label: 'Typo/Error', icon: 'fa-font'},
                                {id: 'missing_content', label: 'Missing Info', icon: 'fa-image'},
                                {id: 'other', label: 'Other Issue', icon: 'fa-ellipsis-h'}
                            ]">
                                <button type="button" @click="reportForm.issue_type = type.id"
                                    class="flex flex-col items-center justify-center p-4 rounded-2xl border-2 transition-all gap-2 group"
                                    :class="reportForm.issue_type === type.id ? 'bg-primary/20 border-primary text-primary shadow-lg shadow-primary/10' : 'bg-white/5 border-white/5 hover:border-white/10 text-gray-400 hover:text-white'">
                                    <i class="fas text-xl" :class="type.icon"></i>
                                    <span class="text-xs font-bold" x-text="type.label"></span>
                                </button>
                            </template>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs uppercase tracking-wider text-gray-500 font-bold mb-2">Optional Details</label>
                        <textarea x-model="reportForm.description"
                            class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-primary outline-none transition-all h-24 resize-none"
                            placeholder="Help us fix it faster..."></textarea>
                    </div>
                    <!-- Photo Evidence (Phase 8) -->
                    <div>
                        <label class="block text-xs uppercase tracking-wider text-gray-500 font-bold mb-2">Visual Evidence (Optional)</label>
                        <div class="relative group">
                            <input type="file" @change="reportForm.screenshot = $event.target.files[0]"
                                class="w-full bg-white/5 border border-dashed border-white/20 rounded-xl px-4 py-4 text-xs text-gray-400 cursor-pointer hover:border-primary/50 transition-all file:hidden">
                            <div class="absolute inset-0 flex items-center justify-center pointer-events-none text-[10px] font-bold uppercase tracking-widest text-gray-500 group-hover:text-primary transition-colors">
                                <i class="fas fa-camera mr-2"></i>
                                <span x-text="reportForm.screenshot ? reportForm.screenshot.name : 'Tap to upload screenshot (Max 2MB)'"></span>
                            </div>
                        </div>
                    </div>
                    <button type="submit"
                        class="w-full bg-primary hover:bg-primary/90 text-white py-4 rounded-xl font-bold transition-all shadow-lg shadow-primary/20 flex items-center justify-center gap-2"
                        :disabled="isSubmittingReport">
                        <template x-if="!isSubmittingReport">
                            <span>Submit Report</span>
                        </template>
                        <template x-if="isSubmittingReport">
                            <i class="fas fa-spinner fa-spin"></i>
                        </template>
                    </button>
                </div>
            </form>
        </div>
    </div>

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

                                <!-- Radio/Checkbox Indicator -->
                                <div class="mt-1 w-5 h-5 flex items-center justify-center shrink-0 transition-all"
                                    :class="{
                                        'rounded-full border-2 border-gray-500 group-hover:border-primary': questions[currentQ].type !== 'MULTI',
                                        'rounded border-2 border-gray-500 group-hover:border-primary': questions[currentQ].type === 'MULTI',
                                        'border-primary bg-primary': isSelected(questions[currentQ].id, key)
                                    }">
                                    <!-- Radio Dot -->
                                    <div x-show="questions[currentQ].type !== 'MULTI'"
                                        class="w-2 h-2 rounded-full bg-white transform scale-0 transition-transform"
                                        :class="{'scale-100': isSelected(questions[currentQ].id, key)}"></div>
                                    <!-- Checkbox Tick -->
                                    <i x-show="questions[currentQ].type === 'MULTI'"
                                        class="fas fa-check text-[10px] text-white transform scale-0 transition-transform"
                                        :class="{'scale-100': isSelected(questions[currentQ].id, key)}"></i>
                                </div>

                                <!-- Option Text -->
                                <div class="text-gray-300 group-hover:text-white text-base md:text-lg select-none" x-text="opt.text || opt"></div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Navigation Actions -->
                <div class="flex flex-col md:flex-row items-center justify-between gap-4 mt-8">
                    <button @click="prevQ()" :disabled="currentQ === 0"
                        class="w-full md:flex-1 bg-surface hover:bg-surfaceHover text-white px-10 py-4 rounded-xl font-semibold border border-white/10 disabled:opacity-50 disabled:cursor-not-allowed transition-all flex items-center justify-center gap-2 whitespace-nowrap">
                        <i class="fas fa-arrow-left text-sm"></i> Previous
                    </button>

                    <button @click="toggleMark(currentQ)"
                        class="w-full md:flex-1 bg-yellow-500/10 hover:bg-yellow-500/20 text-yellow-500 px-10 py-4 rounded-xl font-semibold border border-yellow-500/20 transition-all flex items-center justify-center gap-2 text-center whitespace-nowrap">
                        <i class="fas fa-flag" :class="{'text-yellow-500': isMarked(currentQ), 'text-yellow-500/50': !isMarked(currentQ)}"></i>
                        <span x-text="isMarked(currentQ) ? 'Unmark' : 'Mark Question'"></span>
                    </button>

                    <button @click="nextQ()"
                        class="w-full md:flex-1 bg-primary hover:bg-primary/90 text-white px-10 py-4 rounded-xl font-semibold shadow-lg shadow-primary/20 transition-all flex items-center justify-center gap-3 group whitespace-nowrap">
                        <span x-text="currentQ === questions.length - 1 ? 'Go to Review' : 'Save & Next'"></span>
                        <i class="fas fa-arrow-right transform group-hover:translate-x-1 transition-transform"></i>
                    </button>
                </div>

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

                reporting: false,
                isSubmittingReport: false,
                reportForm: {
                    issue_type: 'wrong_answer',
                    description: '',
                    screenshot: null
                },

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
                    if (!content) return '';
                    // Handle JSON string if passed as string
                    let data = content;
                    if (typeof content === 'string' && content.startsWith('{')) {
                        try {
                            data = JSON.parse(content);
                        } catch (e) {}
                    }

                    if (typeof data === 'object' && data !== null) {
                        let html = data.text || '';
                        if (data.image) {
                            // Ensure full URL if it's local path
                            let imgSrc = data.image.startsWith('http') ? data.image : '<?php echo app_base_url(""); ?>' + data.image;
                            html += `<div class="mt-6 flex justify-center"><img src="${imgSrc}" class="rounded-2xl border border-white/10 shadow-2xl max-h-96 w-auto object-contain bg-white/5 p-2"></div>`;
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
                    const ans = this.storedAnswers[qid];
                    if (Array.isArray(ans)) {
                        return ans.includes(key);
                    }
                    return ans == key;
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
                    const qType = this.questions[this.currentQ].type;

                    if (qType === 'MULTI') {
                        if (!Array.isArray(this.storedAnswers[qid])) {
                            this.storedAnswers[qid] = [];
                        }
                        const index = this.storedAnswers[qid].indexOf(key);
                        if (index > -1) {
                            this.storedAnswers[qid].splice(index, 1);
                        } else {
                            this.storedAnswers[qid].push(key);
                        }
                        // Re-assign for reactivity
                        this.storedAnswers[qid] = [...this.storedAnswers[qid]];
                    } else {
                        this.storedAnswers[qid] = key;
                    }

                    // Server Save
                    const formData = new FormData();
                    formData.append('attempt_id', this.attemptId);
                    formData.append('question_id', qid);
                    formData.append('selected_options', JSON.stringify(this.storedAnswers[qid]));
                    formData.append('csrf_token', this.csrfToken);

                    fetch('<?php echo app_base_url("quiz/save-answer"); ?>', {
                        method: 'POST',
                        body: formData
                    }).catch(console.error);
                },

                openReportModal() {
                    this.reportForm.description = '';
                    this.reportForm.screenshot = null;
                    this.reporting = true;
                },

                submitReport() {
                    this.isSubmittingReport = true;

                    const formData = new FormData();
                    formData.append('question_id', this.questions[this.currentQ].id);
                    formData.append('issue_type', this.reportForm.issue_type);
                    formData.append('description', this.reportForm.description);
                    if (this.reportForm.screenshot) {
                        formData.append('screenshot', this.reportForm.screenshot);
                    }
                    formData.append('csrf_token', this.csrfToken);

                    fetch('<?php echo app_base_url("quiz/report-question"); ?>', {
                            method: 'POST',
                            body: formData
                        })
                        .then(res => res.json())
                        .then(res => {
                            if (res.success) {
                                alert(res.message);
                                this.reporting = false;
                            } else {
                                alert('Error submitting report. Please try again.');
                            }
                        })
                        .finally(() => {
                            this.isSubmittingReport = false;
                        });
                },

                submitExam(auto = false) {
                    if (!auto && !confirm('Are you sure you want to finish the exam?')) return;

                    const formData = new FormData();
                    formData.append('attempt_id', this.attemptId);
                    formData.append('csrf_token', this.csrfToken);

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