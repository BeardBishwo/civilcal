<?php
/**
 * Public Exam Interface
 * Premium SaaS Design (Refactored)
 * Stack: PHP + Tailwind CSS + Alpine.js
 */
$isMock = ($session['mode'] === 'mock');
?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($category['name']); ?> | Exam</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="<?php echo app_base_url('themes/default/assets/css/quiz.min.css?v=' . time()); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="//unpkg.com/alpinejs" defer></script>
</head>
<body class="bg-background text-white font-sans h-screen overflow-hidden select-none"
      x-data="examEngine()">

    <!-- Header -->
    <header class="h-16 bg-surface border-b border-white/10 flex items-center justify-between px-6 fixed top-0 left-0 w-full z-50">
        <div class="flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-primary to-accent flex items-center justify-center text-white font-bold shadow-lg shadow-primary/25">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <div>
                <h1 class="font-bold text-lg leading-tight"><?php echo htmlspecialchars($category['name']); ?></h1>
                <div class="text-xs text-gray-400 font-medium">
                    <?php echo $isMock ? 'Mock Exam Mode' : 'Practice Mode'; ?>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-6">
             <!-- Timer -->
             <?php if($isMock): ?>
             <div class="flex items-center gap-3 px-4 py-2 bg-background rounded-full border border-white/10" 
                  :class="{'animate-pulse border-red-500/50': timeLeft < 300}">
                 <div class="w-2 h-2 rounded-full" :class="timeLeft < 300 ? 'bg-red-500' : 'bg-green-500'"></div>
                 <span class="font-mono text-xl font-bold tracking-widest" x-text="formatTime(timeLeft)"></span>
             </div>
             <?php endif; ?>

             <button @click="submitExam()" class="btn-primary px-6 py-2 rounded-lg text-sm font-bold shadow-lg shadow-primary/20 hover:shadow-primary/40 transition-all">
                Submit <span class="hidden md:inline">Exam</span>
             </button>
        </div>
    </header>

    <!-- Main Layout -->
    <div class="flex h-full pt-16">
        
        <!-- Sidebar (Question Palette) -->
        <aside class="w-80 bg-surface border-r border-white/5 flex flex-col hidden lg:flex">
            <div class="p-6 border-b border-white/5">
                <h3 class="font-bold text-gray-400 text-sm uppercase tracking-wider mb-4">Question Palette</h3>
                <div class="grid grid-cols-5 gap-2">
                    <template x-for="(q, index) in questions" :key="q.id">
                        <button @click="jumpTo(index)" 
                                class="w-10 h-10 rounded-lg text-sm font-bold transition-all relative group"
                                :class="getPaletteClass(index)">
                            <span x-text="index + 1"></span>
                            <!-- Hover tooltip -->
                            <span class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-2 py-1 bg-black text-xs rounded opacity-0 group-hover:opacity-100 pointer-events-none whitespace-nowrap z-50 transition-opacity">
                                Q<span x-text="index + 1"></span>
                            </span>
                        </button>
                    </template>
                </div>
            </div>
            
            <div class="p-6 mt-auto border-t border-white/5">
                <div class="space-y-3 text-xs font-medium text-gray-400">
                    <div class="flex items-center gap-3">
                        <div class="w-3 h-3 rounded-full bg-primary shadow shadow-primary/50"></div> Current
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-3 h-3 rounded-full bg-green-500/20 border border-green-500"></div> Answered
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-3 h-3 rounded-full bg-white/5 border border-white/10"></div> Not Visited
                    </div>
                </div>
            </div>
        </aside>

        <!-- Content Area -->
        <main class="flex-1 overflow-y-auto relative bg-background">
            <div class="max-w-4xl mx-auto px-6 py-10 pb-32">
                
                <!-- Question Card -->
                <div class="glass-card p-0 overflow-hidden min-h-[500px] flex flex-col" x-show="!loading" x-transition.opacity.duration.300ms>
                    
                    <!-- Question Header & Progress -->
                    <div class="px-8 py-6 border-b border-white/5 bg-white/5 flex justify-between items-center relative">
                        <span class="font-bold text-gray-400">Question <span x-text="currentIndex + 1"></span> of <span x-text="questions.length"></span></span>
                        
                        <!-- Progress Bar -->
                        <div class="absolute bottom-0 left-0 w-full h-1 bg-white/5">
                            <div class="h-full bg-primary transition-all duration-300" :style="`width: ${(currentIndex + 1) / questions.length * 100}%`"></div>
                        </div>
                    </div>

                    <!-- Question Text -->
                    <div class="p-8 md:p-10 flex-1">
                        <div class="text-xl md:text-2xl font-medium leading-relaxed text-white mb-8">
                             <span class="text-primary font-bold mr-2" x-text="(currentIndex + 1) + '.'"></span>
                             <span x-html="questions[currentIndex].text"></span>
                        </div>

                        <!-- Options -->
                        <div class="grid gap-4">
                            <template x-for="(opt, idx) in questions[currentIndex].options" :key="idx">
                                <div @click="selectAnswer(idx)" 
                                     class="group relative p-5 rounded-xl border-2 cursor-pointer transition-all duration-200 flex items-center gap-4 hover:bg-white/5"
                                     :class="answers[questions[currentIndex].id] === idx ? 'border-primary bg-primary/10 hover:bg-primary/10' : 'border-white/10 hover:border-white/30 bg-surface'">
                                    
                                    <!-- Radio Circle -->
                                    <div class="w-8 h-8 rounded-full border-2 flex items-center justify-center font-bold text-sm shrink-0 transition-colors"
                                         :class="answers[questions[currentIndex].id] === idx ? 'border-primary bg-primary text-white' : 'border-gray-500 text-gray-500 group-hover:border-gray-400'">
                                        <span x-text="String.fromCharCode(65 + idx)"></span>
                                    </div>
                                    
                                    <div class="text-lg text-gray-200 group-hover:text-white" x-text="opt"></div>
                                    
                                    <!-- Checkmark for selected -->
                                    <div x-show="answers[questions[currentIndex].id] === idx" class="absolute right-5 text-primary text-xl animate-scale-in">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                </div>
                            </template>
                        </div>
                        
                        <!-- Practice Mode Feedback -->
                        <?php if(!$isMock): ?>
                        <div x-show="showFeedback && answers[questions[currentIndex].id] !== undefined" 
                             x-transition.opacity
                             class="mt-8 p-6 rounded-xl border"
                             :class="isCorrect ? 'bg-green-500/10 border-green-500/30' : 'bg-red-500/10 border-red-500/30'">
                            
                            <div class="flex items-center gap-3 mb-3 font-bold text-lg" :class="isCorrect ? 'text-green-400' : 'text-red-400'">
                                <i class="fas" :class="isCorrect ? 'fa-check-circle' : 'fa-times-circle'"></i>
                                <span x-text="isCorrect ? 'Correct!' : 'Incorrect'"></span>
                            </div>
                            
                            <div class="text-gray-300 leading-relaxed">
                                <strong class="text-xs uppercase tracking-wider text-gray-500 block mb-1">Explanation</strong>
                                <span x-html="explanation"></span>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                    </div>

                    <!-- Footer Nav -->
                    <div class="px-8 py-6 bg-surface border-t border-white/5 flex justify-between items-center">
                        <button @click="prev()" :disabled="currentIndex === 0" 
                                class="px-6 py-3 rounded-xl font-semibold flex items-center gap-2 transition-all"
                                :class="currentIndex === 0 ? 'opacity-50 cursor-not-allowed text-gray-600' : 'text-gray-400 hover:text-white hover:bg-white/5'">
                            <i class="fas fa-arrow-left"></i> Previous
                        </button>
                        
                        <button @click="next()" x-show="currentIndex < questions.length - 1"
                                class="px-8 py-3 rounded-xl bg-primary text-white font-bold shadow-lg shadow-primary/25 hover:shadow-primary/40 hover:-translate-y-1 transition-all flex items-center gap-2">
                            Next <i class="fas fa-arrow-right"></i>
                        </button>
                        
                        <button @click="submitExam()" x-show="currentIndex === questions.length - 1"
                                class="px-8 py-3 rounded-xl bg-green-500 text-white font-bold shadow-lg shadow-green-500/25 hover:shadow-green-500/40 hover:-translate-y-1 transition-all flex items-center gap-2">
                            Finish Exam <i class="fas fa-check"></i>
                        </button>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <!-- Alpine Data -->
    <script>
        const QUESTIONS = <?php echo json_encode($questions); ?>;
        const SESSION_ID = <?php echo $session['id']; ?>;
        const MODE = '<?php echo $session['mode']; ?>';
        // Pre-fill answers if any (not implemented in backend yet but good structure)
        const PREV_ANSWERS = {}; 

        function examEngine() {
            return {
                questions: QUESTIONS,
                currentIndex: 0,
                answers: PREV_ANSWERS, // {qId: answerIndex}
                timeLeft: 45 * 60,
                loading: false,
                
                // Feedback State
                showFeedback: false,
                isCorrect: false,
                explanation: '',

                init() {
                    if (MODE === 'mock') {
                        setInterval(() => {
                            if (this.timeLeft > 0) this.timeLeft--;
                            else this.submitExam(true);
                        }, 1000);
                    }
                },

                formatTime(seconds) {
                    const m = Math.floor(seconds / 60).toString().padStart(2, '0');
                    const s = (seconds % 60).toString().padStart(2, '0');
                    return `${m}:${s}`;
                },

                selectAnswer(idx) {
                     // Check if already answered in mock mode (allow change) or practice mode (prevent change if logic dictates)
                     // For now allow always
                     const qId = this.questions[this.currentIndex].id;
                     this.answers[qId] = idx;
                     
                     if (MODE === 'practice') {
                         this.checkAnswer(qId, idx);
                     }
                },

                getPaletteClass(index) {
                    const qId = this.questions[index].id;
                    if (index === this.currentIndex) return 'bg-primary text-white ring-2 ring-primary ring-offset-2 ring-offset-surface';
                    if (this.answers[qId] !== undefined) return 'bg-green-500/20 text-green-400 border border-green-500/50';
                    return 'bg-white/5 text-gray-500 hover:bg-white/10 hover:text-gray-300';
                },

                async checkAnswer(qId, ans) {
                    this.showFeedback = true; // Show loading or existing
                    
                    const fd = new URLSearchParams();
                    fd.append('question_id', qId);
                    fd.append('answer', ans);

                    const res = await fetch('<?php echo app_base_url("exams/check-answer"); ?>', {
                        method: 'POST',
                        body: fd
                    });
                    const data = await res.json();
                    
                    this.isCorrect = data.correct;
                    this.explanation = data.explanation || 'No explanation provided.';
                },

                next() {
                    if (this.currentIndex < this.questions.length - 1) {
                        this.currentIndex++;
                        this.showFeedback = false; // Reset for next q
                        // If practice mode and already answered, fetch feedback silently? 
                        // Simplified: Only fetch on click.
                    }
                },

                prev() {
                    if (this.currentIndex > 0) {
                        this.currentIndex--;
                        this.showFeedback = (MODE === 'practice' && this.answers[this.questions[this.currentIndex].id] !== undefined);
                    }
                },

                jumpTo(index) {
                    this.currentIndex = index;
                    this.showFeedback = (MODE === 'practice' && this.answers[this.questions[this.currentIndex].id] !== undefined);
                },

                async submitExam(force = false) {
                    if (!force && !confirm('Are you sure you want to finish the exam?')) return;
                    
                    const payload = [];
                    for (const [qId, ans] of Object.entries(this.answers)) {
                        payload.push({question_id: qId, answer: ans});
                    }

                    const fd = new FormData();
                    fd.append('session_id', SESSION_ID);
                    fd.append('answers', JSON.stringify(payload));

                    const res = await fetch('<?php echo app_base_url("exams/submit"); ?>', {
                        method: 'POST',
                        body: fd
                    });
                    const data = await res.json();
                    
                    if (data.success) {
                        window.location.href = data.redirect;
                    } else {
                        alert('Submission failed. Please try again.');
                    }
                }
            }
        }
    </script>
</body>
</html>
