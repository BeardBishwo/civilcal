const ExamEngine = {
    data: null,
    currentIndex: 0,
    answers: {}, // QuestionID -> Value
    reviewFlags: {},
    timerInterval: null,
    submitLock: false,

    init: function (payload) {
        this.data = payload;
        this.answers = Array.isArray(payload.savedAnswers) ? {} : payload.savedAnswers; // Handle empty array vs object

        // Render Palette
        this.renderPalette();

        // Start Timer
        this.startTimer(payload.durationMinutes * 60); // Simple duration logic. 
        // Real implementation should calculate remaining time from timestamps.

        // Load First Question
        this.loadQuestion(0);

        // MathJax
        if (window.MathJax) MathJax.typesetPromise();

        // Anti-Cheat (Tab Visibility)
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                alert("Warning: Please focus on the exam!");
            }
        });
    },

    startTimer: function (seconds) {
        // Mock remaining calculation
        // Ideally: endTime = startTime + duration. Remaining = endTime - now.
        // We'll trust local for this demo, usually server sync needed.
        let remaining = seconds;

        // Get storage
        const storedRemaining = localStorage.getItem('exam_timer_' + this.data.attemptId);
        if (storedRemaining) remaining = parseInt(storedRemaining);

        const display = document.getElementById('examTimer');

        this.timerInterval = setInterval(() => {
            remaining--;
            localStorage.setItem('exam_timer_' + this.data.attemptId, remaining);

            const h = Math.floor(remaining / 3600);
            const m = Math.floor((remaining % 3600) / 60);
            const s = remaining % 60;

            display.innerText = `${h.toString().padStart(2, '0')}:${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`;

            if (remaining <= 0) {
                clearInterval(this.timerInterval);
                this.submit(true); // Auto submit
            }
        }, 1000);
    },

    loadQuestion: function (index) {
        if (index < 0 || index >= this.data.questions.length) return;
        this.currentIndex = index;

        const q = this.data.questions[index];
        const container = document.getElementById('questionContainer');

        // Type Logic
        let inputHTML = '';
        const savedVal = this.answers[q.id];

        if (q.type === 'mcq_single' || q.type === 'true_false') {
            const opts = q.options; // Assuming array of keys [option_1, option_2, etc] or custom structured options? 
            // In DB, options is JSON: {"option_1": "Val", "option_2": "Val"}
            // Let's iterate.

            // Reformat if object
            const optsArray = [];
            if (q.type === 'true_false') {
                // True/False usually specialized. Let's assume standard options structure or fixed logic.
                // Our Creator saves them as Option 1 (green) / Option 2 (red)
                optsArray.push({ key: 1, val: "True", color: "success" });
                optsArray.push({ key: 2, val: "False", color: "danger" });
            } else {
                // MCQ
                for (let k = 1; k <= 5; k++) {
                    if (opts['option_' + k]) {
                        optsArray.push({ key: k, val: opts['option_' + k] });
                    }
                }
            }

            inputHTML = `<div class="d-flex flex-column gap-3 mt-4">`;
            optsArray.forEach(opt => {
                const isChecked = savedVal == opt.key ? 'checked' : '';
                const activeClass = isChecked ? 'border-primary bg-light' : 'border-gray-200';

                inputHTML += `
                    <label class="option-label d-flex p-3 rounded-3 border ${activeClass}" onclick="ExamEngine.highlightOption(this)">
                        <input type="radio" name="q_opt" class="option-radio d-none" value="${opt.key}" ${isChecked} onchange="ExamEngine.save(${q.id}, this.value)">
                        <div class="fw-bold bg-light border rounded-circle d-flex align-items-center justify-content-center me-3" style="width:24px; height:24px; min-width:24px;">${String.fromCharCode(64 + parseInt(opt.key))}</div>
                        <div class="w-100 fs-5">${opt.val}</div>
                    </label>
                `;
            });
            inputHTML += `</div>`;
        }

        container.innerHTML = `
            <h5 class="text-muted fw-bold mb-3 small text-uppercase">Question ${index + 1} of ${this.data.questions.length}</h5>
            <div class="fs-4 fw-bold text-gray-800 mb-4" style="line-height:1.6;">
                ${q.content.text || q.content} <!-- Handle object or string -->
            </div>
            ${inputHTML}
        `;

        // Update Buttons
        document.getElementById('btnPrev').disabled = index === 0;
        document.getElementById('btnNext').innerHTML = index === this.data.questions.length - 1 ? 'Finish' : 'Next <i class="fas fa-arrow-right ms-2"></i>';
        document.getElementById('btnNext').onclick = index === this.data.questions.length - 1 ? () => ExamEngine.confirmSubmit() : () => ExamEngine.next();

        // Update Review Checkbox
        document.getElementById('markReview').checked = !!this.reviewFlags[q.id];

        // Highlight Palette
        document.querySelectorAll('.q-btn').forEach(b => b.classList.remove('active'));
        document.getElementById('p_btn_' + index).classList.add('active');

        // Render Math
        if (window.MathJax) MathJax.typesetPromise([container]);
    },

    highlightOption: function (el) {
        // UI Visuals
        el.parentElement.querySelectorAll('.option-label').forEach(l => {
            l.className = l.className.replace('border-primary bg-light', 'border-gray-200');
        });
        el.className = el.className.replace('border-gray-200', 'border-primary bg-light');
    },

    save: function (qId, val) {
        this.answers[qId] = val;
        this.updatePalette(this.currentIndex, true);

        // Background Sync
        const formData = new FormData();
        formData.append('attempt_id', this.data.attemptId);
        formData.append('question_id', qId);
        formData.append('selected_options', val);
        formData.append('csrf_token', this.data.csrf);

        fetch('/quiz/save-answer', { method: 'POST', body: formData });
    },

    renderPalette: function () {
        const grid = document.getElementById('paletteGrid');
        grid.innerHTML = this.data.questions.map((q, idx) => {
            const hasAns = this.answers[q.id] ? 'answered' : '';
            return `<div id="p_btn_${idx}" onclick="ExamEngine.loadQuestion(${idx})" class="q-btn ${hasAns}">${idx + 1}</div>`;
        }).join('');
    },

    updatePalette: function (idx, isAnswered) {
        const btn = document.getElementById('p_btn_' + idx);
        if (isAnswered) btn.classList.add('answered');

        // Review toggle
        if (this.reviewFlags[this.data.questions[idx].id]) btn.classList.add('review');
        else btn.classList.remove('review');
    },

    toggleReview: function () {
        const qId = this.data.questions[this.currentIndex].id;
        this.reviewFlags[qId] = !this.reviewFlags[qId];
        this.updatePalette(this.currentIndex, !!this.answers[qId]);
    },

    next: function () {
        this.loadQuestion(this.currentIndex + 1);
    },

    prev: function () {
        this.loadQuestion(this.currentIndex - 1);
    },

    confirmSubmit: function () {
        const answered = Object.keys(this.answers).length;
        const total = this.data.questions.length;

        document.getElementById('answeredCount').innerText = answered;
        document.getElementById('totalCount').innerText = total;

        if (answered < total) {
            document.getElementById('unansweredAlert').classList.remove('d-none');
        } else {
            document.getElementById('unansweredAlert').classList.add('d-none');
        }

        new bootstrap.Modal('#submitModal').show();
    },

    submit: function (force = false) {
        if (this.submitLock) return;
        this.submitLock = true;

        const form = new FormData();
        form.append('attempt_id', this.data.attemptId);
        form.append('nonce', this.data.nonce);
        form.append('csrf_token', this.data.csrf);

        fetch('/quiz/submit', { method: 'POST', body: form })
            .then(r => {
                if (r.ok) location.href = '/quiz/result/' + this.data.attemptId;
                else alert("Submission failed. Please check connection.");
            });
    }
};
