<?php include_once __DIR__ . '/../../layouts/header.php'; ?>

<!-- Full screen mode logic will be handled by removing standard header/footer if requested, but for now we keep them or simplify -->
<?php
// Simple logic to minimize distraction
$isMock = ($session['mode'] === 'mock');
?>

<div class="bg-gray-50 min-h-screen py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            
            <!-- Main Question Area -->
            <div class="lg:col-span-3">
                <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
                    
                    <!-- Header -->
                    <div class="bg-gray-900 text-white p-4 flex justify-between items-center">
                        <div>
                            <span class="text-gray-400 text-sm block">Subject</span>
                            <span class="font-bold text-lg"><?php echo htmlspecialchars($category['name']); ?></span>
                        </div>
                        
                        <?php if($isMock): ?>
                        <div class="bg-gray-800 rounded-lg px-4 py-2 flex items-center gap-2">
                            <i class="fas fa-clock text-red-400"></i>
                            <span id="timer" class="font-mono text-xl font-bold">45:00</span>
                        </div>
                        <?php else: ?>
                        <div class="bg-gray-800 rounded-lg px-4 py-2">
                            <span class="text-green-400 font-bold">Practice Mode</span>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Progress Bar -->
                    <div class="w-full bg-gray-200 h-1">
                        <div id="progress-bar" class="bg-indigo-600 h-1 transition-all duration-300" style="width: 0%"></div>
                    </div>

                    <!-- Question Container -->
                    <div id="question-container" class="p-8 min-h-[400px]">
                        <!-- JS Injected Content -->
                        <div class="flex justify-center items-center h-64">
                            <i class="fas fa-spinner fa-spin text-4xl text-indigo-600"></i>
                        </div>
                    </div>

                    <!-- Footer Controls -->
                    <div class="bg-gray-50 px-8 py-4 border-t border-gray-200 flex justify-between items-center">
                        <button onclick="prevQuestion()" id="btn-prev" class="px-6 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 disabled:opacity-50 font-medium">
                            <i class="fas fa-chevron-left mr-2"></i> Previous
                        </button>
                        
                        <div class="hidden sm:block text-gray-500 font-medium">
                            Question <span id="current-q-num">1</span> of <?php echo count($questions); ?>
                        </div>

                        <button onclick="nextQuestion()" id="btn-next" class="px-6 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 font-medium">
                            Next <i class="fas fa-chevron-right ml-2"></i>
                        </button>
                        
                        <button onclick="submitExam()" id="btn-submit" class="hidden px-6 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700 font-bold">
                            Submit Exam <i class="fas fa-check ml-2"></i>
                        </button>
                    </div>
                    
                </div>
            </div>
            
            <!-- Sidebar / Navigator -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 sticky top-24">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Question Navigator</h3>
                    
                    <div class="grid grid-cols-5 gap-2 mb-6">
                        <?php foreach($questions as $idx => $q): ?>
                            <button onclick="jumpTo(<?php echo $idx; ?>)" 
                                    id="nav-btn-<?php echo $idx; ?>"
                                    class="w-full aspect-square rounded-lg border border-gray-200 text-sm font-medium hover:border-indigo-500 hover:text-indigo-600 transition flex items-center justify-center">
                                <?php echo $idx + 1; ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="space-y-3 text-sm text-gray-500 border-t pt-4">
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 rounded border border-gray-200"></div> Not Visited
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 rounded bg-indigo-100 border border-indigo-500 text-indigo-700"></div> Current
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 rounded bg-blue-600 border border-blue-600"></div> Answered
                        </div>
                    </div>

                    <button onclick="submitExam()" class="mt-6 w-full py-3 bg-gray-900 text-white rounded-lg font-bold hover:bg-gray-800 transition">
                        Finish Exam
                    </button>
                </div>
            </div>
            
        </div>
    </div>
</div>

<!-- DATA STORE -->
<script>
    const SESSION_ID = <?php echo $session['id']; ?>;
    const QUESTIONS = <?php echo json_encode($questions); ?>;
    const MODE = '<?php echo $session['mode']; ?>';
    let currentIndex = 0;
    let answers = {}; // {question_id: answer_val}
    let timerInterval;
    let timeLeft = 45 * 60; // 45 mins in seconds
</script>

<script>
    // Initialize
    document.addEventListener('DOMContentLoaded', () => {
        renderQuestion(0);
        updateNavigator();
        if (MODE === 'mock') startTimer();
    });

    function startTimer() {
        const display = document.getElementById('timer');
        timerInterval = setInterval(() => {
            timeLeft--;
            if (timeLeft <= 0) {
                clearInterval(timerInterval);
                alert("Time's up! Submitting exam automatically.");
                submitExam(true);
            }
            const m = Math.floor(timeLeft / 60).toString().padStart(2, '0');
            const s = (timeLeft % 60).toString().padStart(2, '0');
            display.innerText = `${m}:${s}`;
        }, 1000);
    }

    function renderQuestion(index) {
        currentIndex = index;
        const q = QUESTIONS[index];
        const container = document.getElementById('question-container');
        
        let optionsHtml = '';
        if (q.options && q.options.length) {
            optionsHtml = `<div class="grid gap-3 mt-6">`;
            q.options.forEach((opt, idx) => {
                const isSelected = answers[q.id] == idx;
                const activeClass = isSelected ? 'border-indigo-500 bg-indigo-50 ring-2 ring-indigo-200' : 'border-gray-200 hover:border-indigo-300 bg-white';
                
                optionsHtml += `
                    <div onclick="selectAnswer(${q.id}, ${idx})" 
                         class="cursor-pointer p-4 rounded-xl border-2 transition-all ${activeClass} flex items-center group">
                        <div class="w-8 h-8 rounded-full border-2 flex items-center justify-center mr-4 font-bold ${isSelected ? 'border-indigo-600 bg-indigo-600 text-white' : 'border-gray-300 text-gray-500 group-hover:border-indigo-400'}">
                            ${String.fromCharCode(65 + idx)}
                        </div>
                        <div class="text-gray-800 font-medium">${opt}</div>
                    </div>
                `;
            });
            optionsHtml += `</div>`;
        }

        // Explanation container (for Practice Mode)
        const feedbackHtml = `
            <div id="feedback-${q.id}" class="mt-6 hidden">
                <div id="feedback-alert-${q.id}" class="p-4 rounded-lg mb-4"></div>
                <div class="bg-gray-100 p-4 rounded-lg text-gray-700">
                    <span class="font-bold block mb-2">Explanation:</span>
                    <span id="explanation-${q.id}"></span>
                </div>
            </div>
        `;

        container.innerHTML = `
            <div class="text-xl text-gray-900 font-medium leading-relaxed">
                <span class="font-bold mr-2">${index + 1}.</span> 
                ${q.text.replace(/\n/g, '<br>')}
            </div>
            ${optionsHtml}
            ${feedbackHtml}
        `;

        // Update Nav
        document.getElementById('current-q-num').innerText = index + 1;
        document.getElementById('progress-bar').style.width = `${((index + 1) / QUESTIONS.length) * 100}%`;
        
        document.getElementById('btn-prev').disabled = (index === 0);
        
        if (index === QUESTIONS.length - 1) {
            document.getElementById('btn-next').classList.add('hidden');
            document.getElementById('btn-submit').classList.remove('hidden');
        } else {
            document.getElementById('btn-next').classList.remove('hidden');
            document.getElementById('btn-submit').classList.add('hidden');
        }

        updateNavigator();

        // If Practice Mode and already answered, show feedback
        if (MODE === 'practice' && answers[q.id] !== undefined) {
             checkAnswerAPI(q.id, answers[q.id], false); // passive check
        }
    }

    function selectAnswer(qId, ansIdx) {
        if (MODE === 'practice' && answers[qId] !== undefined) return; // Prevent re-answering in practice mode for now

        answers[qId] = ansIdx;
        renderQuestion(currentIndex); // Re-render to update UI
        
        if (MODE === 'practice') {
            checkAnswerAPI(qId, ansIdx, true);
        }
    }

    function checkAnswerAPI(qId, ans, animate) {
        // Only fetch if not already fetched? Or just fetch.
        const feedbackAlert = document.getElementById(`feedback-alert-${qId}`);
        const feedbackDiv = document.getElementById(`feedback-${qId}`);
        const explanationSpan = document.getElementById(`explanation-${qId}`);
        
        fetch('<?php echo app_base_url("exams/check-answer"); ?>', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: `question_id=${qId}&answer=${ans}`
        })
        .then(r => r.json())
        .then(data => {
            feedbackDiv.classList.remove('hidden');
            explanationSpan.innerHTML = data.explanation || 'No explanation available.';
            
            if (data.correct) {
                feedbackAlert.className = 'p-4 rounded-lg mb-4 bg-green-100 text-green-800 border border-green-200';
                feedbackAlert.innerHTML = '<i class="fas fa-check-circle mr-2"></i> <strong>Correct!</strong> Great job.';
            } else {
                feedbackAlert.className = 'p-4 rounded-lg mb-4 bg-red-100 text-red-800 border border-red-200';
                feedbackAlert.innerHTML = `<i class="fas fa-times-circle mr-2"></i> <strong>Incorrect.</strong> The correct answer is ${String.fromCharCode(65 + parseInt(data.correct_answer))}.`;
            }
        });
    }

    function prevQuestion() {
        if (currentIndex > 0) renderQuestion(currentIndex - 1);
    }

    function nextQuestion() {
        if (currentIndex < QUESTIONS.length - 1) renderQuestion(currentIndex + 1);
    }
    
    function jumpTo(idx) {
        renderQuestion(idx);
    }

    function updateNavigator() {
        QUESTIONS.forEach((q, idx) => {
            const btn = document.getElementById(`nav-btn-${idx}`);
            btn.className = `w-full aspect-square rounded-lg border text-sm font-medium flex items-center justify-center transition `;
            
            if (idx === currentIndex) {
                btn.className += 'bg-indigo-100 border-indigo-500 text-indigo-700 ring-2 ring-indigo-200';
            } else if (answers[q.id] !== undefined) {
                btn.className += 'bg-blue-600 border-blue-600 text-white';
            } else {
                btn.className += 'bg-white border-gray-200 text-gray-600 hover:bg-gray-50';
            }
        });
    }

    function submitExam(force = false) {
        if (!force && !confirm('Are you sure you want to submit your exam?')) return;
        
        // Prepare payload
        const payload = [];
        for (const [qId, ans] of Object.entries(answers)) {
            payload.push({question_id: qId, answer: ans});
        }
        
        const fd = new FormData();
        fd.append('session_id', SESSION_ID);
        fd.append('answers', JSON.stringify(payload));

        fetch('<?php echo app_base_url("exams/submit"); ?>', {
            method: 'POST',
            body: fd
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                window.location.href = data.redirect;
            } else {
                alert("Error submitting exam");
            }
        });
    }
</script>

<?php include_once __DIR__ . '/../../layouts/footer.php'; ?>
