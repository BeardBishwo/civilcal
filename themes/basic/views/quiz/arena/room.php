<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title); ?></title>
    <!-- Core CSS -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="<?php echo app_base_url('themes/admin/assets/css/sb-admin-2.min.css'); ?>" rel="stylesheet">
    <link href="<?php echo app_base_url('themes/admin/assets/vendor/fontawesome-free/css/all.min.css'); ?>" rel="stylesheet" type="text/css">
    
    <style>
        body, html { height: 100%; overflow: hidden; background-color: #f8f9fc; }
        .exam-container { height: 100vh; display: flex; flex-direction: column; }
        .top-bar { height: 60px; background: #fff; box-shadow: 0 .15rem 1.75rem 0 rgba(58,59,69,.15); z-index: 10; display: flex; align-items: center; justify-content: space-between; padding: 0 20px; }
        .main-content { flex: 1; display: flex; overflow: hidden; }
        .sidebar { width: 300px; background: #fff; border-right: 1px solid #e3e6f0; display: flex; flex-direction: column; overflow-y: auto; }
        .question-area { flex: 1; padding: 30px; overflow-y: auto; }
        .q-pallete-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 10px; padding: 15px; }
        .q-btn { width: 100%; aspect-ratio: 1; display: flex; align-items: center; justify-content: center; border: 1px solid #d1d3e2; border-radius: 5px; font-weight: bold; cursor: pointer; transition: all 0.2s; }
        .q-btn:hover { background-color: #eaecf4; }
        .q-btn.active { border-color: #4e73df; background-color: #e8eeff; color: #4e73df; }
        .q-btn.answered { background-color: #1cc88a; color: white; border-color: #1cc88a; }
        .q-btn.reviewed { background-color: #f6c23e; color: white; border-color: #f6c23e; }
        .q-btn.current { border: 2px solid #4e73df; }
        
        .question-text { font-size: 1.2rem; font-weight: 600; color: #2e2e2e; margin-bottom: 20px; }
        .option-card { border: 1px solid #e3e6f0; border-radius: 8px; padding: 15px; margin-bottom: 12px; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; }
        .option-card:hover { background-color: #f8f9fc; border-color: #b7b9cc; }
        .option-card.selected { border-color: #4e73df; background-color: #f0f4ff; }
        .option-icon { width: 30px; height: 30px; border-radius: 50%; background: #eaecf4; display: flex; align-items: center; justify-content: center; font-weight: bold; margin-right: 15px; }
        .option-card.selected .option-icon { background: #4e73df; color: white; }
        
        /* MathJax sizing */
        .mjx-chtml { font-size: 110% !important; }
    </style>
    <!-- MathJax -->
    <script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
    <script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
</head>
<body>

<div class="exam-container">
    <!-- Top Bar -->
    <div class="top-bar">
        <div class="d-flex align-items-center">
            <h5 class="m-0 font-weight-bold text-primary mr-3"><?php echo htmlspecialchars($attempt['title']); ?></h5>
            <span class="badge badge-secondary"><?php echo $attempt['mode'] == 'exam' ? 'Exam Mode' : 'Practice Mode'; ?></span>
        </div>
        <div class="d-flex align-items-center">
            <div class="mr-4 text-center">
                <div class="small text-gray-500 text-uppercase font-weight-bold">Time Left</div>
                <div class="h4 mb-0 font-weight-bold text-danger" id="timer">--:--:--</div>
            </div>
            <button class="btn btn-primary" onclick="submitExam()">Finish Exam</button>
        </div>
    </div>

    <div class="main-content">
        <!-- Sidebar: Palette -->
        <div class="sidebar">
            <div class="p-3 border-bottom bg-gray-100">
                <h6 class="m-0 font-weight-bold text-gray-800">Question Palette</h6>
                <div class="d-flex justify-content-between small text-muted mt-2">
                    <span><span class="badge badge-success badge-dot mr-1"></span>Answered</span>
                    <span><span class="badge badge-warning badge-dot mr-1"></span>Review</span>
                    <span><span class="badge badge-light border badge-dot mr-1"></span>Unseen</span>
                </div>
            </div>
            <div class="q-pallete-grid">
                <?php foreach($questions as $index => $q): ?>
                    <div class="q-btn" id="btn-q-<?php echo $index; ?>" onclick="loadQuestion(<?php echo $index; ?>)">
                        <?php echo $index + 1; ?>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="mt-auto p-3 border-top bg-light">
                <h6 class="font-weight-bold text-gray-800 mb-3 small text-uppercase">Lifelines</h6>
                <div class="row no-gutters mb-3">
                    <div class="col-6 p-1">
                        <button class="btn btn-outline-warning btn-sm btn-block" onclick="activateLifeline('50_50')" title="Remove 2 wrong answers (50 Coins)">
                             50/50 
                        </button>
                    </div>
                    <div class="col-6 p-1">
                        <button class="btn btn-outline-info btn-sm btn-block" onclick="activateLifeline('skip')" title="Skip to next question (20 Coins)">
                             Skip 
                        </button>
                    </div>
                    <div class="col-6 p-1">
                        <button class="btn btn-outline-success btn-sm btn-block" onclick="activateLifeline('poll')" title="Show most picked answer (100 Coins)">
                             Poll 
                        </button>
                    </div>
                    <div class="col-6 p-1">
                        <button class="btn btn-outline-primary btn-sm btn-block" onclick="activateLifeline('freeze')" title="Pause timer for 60s (30 Coins)">
                             Freeze 
                        </button>
                    </div>
                </div>
                <button class="btn btn-block btn-info btn-sm" onclick="toggleReview()">Mark for Review</button>
            </div>
        </div>

        <!-- Question Area -->
        <div class="question-area" id="questionConf">
            <!-- Dynamic Content -->
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="<?php echo app_base_url('themes/admin/assets/vendor/jquery/jquery.min.js'); ?>"></script>
<script src="<?php echo app_base_url('themes/admin/assets/vendor/bootstrap/js/bootstrap.bundle.min.js'); ?>"></script>

<script>
    // State
    const questions = <?php echo json_encode($questions); ?>;
    const savedAnswers = <?php echo json_encode($savedAnswers); ?>; // {q_id: val}
    const attemptId = <?php echo $attempt['id']; ?>;
    const saveUrl = '<?php echo app_base_url('quiz/save-answer'); ?>';
    const submitUrl = '<?php echo app_base_url('quiz/submit'); ?>';
    let quizNonce = '<?php echo htmlspecialchars($quizNonce ?? '', ENT_QUOTES, 'UTF-8'); ?>';
    
    let currentIndex = 0;
    let reviewStatus = {}; // {index: bool}
    const honeypotInput = document.createElement('input');
    honeypotInput.type = 'text';
    honeypotInput.name = 'trap_answer';
    honeypotInput.id = 'trap_answer';
    honeypotInput.autocomplete = 'off';
    honeypotInput.style.display = 'none';
    document.body.appendChild(honeypotInput);
    
    // Timer
    const durationMins = <?php echo $attempt['duration_minutes']; ?>;
    const startTime = new Date('<?php echo $attempt['started_at']; ?>').getTime();
    const serverNow = new Date().getTime(); // Roughly sync
    const offset = new Date().getTime() - serverNow; // simplified
    
    let timeLimit = durationMins > 0 ? (startTime + durationMins * 60000) : 0;
    
    function updateTimer() {
        if (!timeLimit) return;
        
        let now = new Date().getTime();
        let dist = timeLimit - now;
        
        if (dist < 0) {
            clearInterval(timerInterval);
            document.getElementById('timer').innerHTML = "EXPIRED";
            alert("Time is up! Submitting exam automatically.");
            submitExam(true);
            return;
        }
        
        let h = Math.floor((dist % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        let m = Math.floor((dist % (1000 * 60 * 60)) / (1000 * 60));
        let s = Math.floor((dist % (1000 * 60)) / 1000);
        
        document.getElementById('timer').innerHTML = 
            (h < 10 ? "0"+h : h) + ":" + (m < 10 ? "0"+m : m) + ":" + (s < 10 ? "0"+s : s);
    }
    
    let timerInterval = setInterval(updateTimer, 1000);
    updateTimer();

    // -- Logic --
    
    function loadQuestion(index) {
        currentIndex = index;
        const q = questions[index];
        const saved = savedAnswers[q.id];
        
        // Update Palette UI
        $('.q-btn').removeClass('active current');
        $('#btn-q-' + index).addClass('active current');
        
        // Render
        let html = `
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="badge badge-light border">Question ${index + 1} of ${questions.length}</span>
                <span class="badge badge-info">${q.default_marks} Marks</span>
            </div>
            
            <div class="question-text">
                ${q.content.text}
            </div>
            ${ q.content.image ? `<img src="${q.content.image}" class="img-fluid mb-4 rounded border" style="max-height: 300px;">` : '' }
            
            <div class="options-container">
        `;
        
        // Options
        if (q.type == 'mcq_single' || q.type == 'true_false') {
            q.options.forEach((opt, idx) => {
                let isSelected = (saved == idx); // Using index as value
                html += `
                    <div class="option-card ${isSelected ? 'selected' : ''}" onclick="selectOption(${idx}, 'mcq_single')">
                        <div class="option-icon">${String.fromCharCode(65 + idx)}</div>
                        <div class="option-text w-100">${opt.text}</div>
                        ${opt.image ? `<img src="${opt.image}" height="40" class="ml-2">` : ''}
                    </div>
                `;
            });
        }
        
        html += `
            </div>
            
            <div class="mt-5 d-flex justify-content-between">
                <button class="btn btn-secondary px-4" onclick="nav(-1)" ${index === 0 ? 'disabled' : ''}>Previous</button>
                <button class="btn btn-primary px-4" onclick="nav(1)">${index === questions.length - 1 ? 'Finish' : 'Next'}</button>
            </div>
        `;
        
        $('#questionConf').html(html);
        
        // Reprocess MathJax
        if(window.MathJax) {
            MathJax.typesetPromise();
        }
    }
    
    window.selectOption = function(val, type) {
        const q = questions[currentIndex];
        
        // UI
        $('.option-card').removeClass('selected');
        // Index based selection for now
        $('.option-card').eq(val).addClass('selected');
        
        // Update Saved State
        savedAnswers[q.id] = val;
        $('#btn-q-' + currentIndex).addClass('answered');
        
        // AJAX Save
        $.post(saveUrl, {
            attempt_id: attemptId,
            question_id: q.id,
            selected_options: val,
            trap_answer: document.getElementById('trap_answer').value || ''
        });
    };
    
    window.nav = function(dir) {
        let newIndex = currentIndex + dir;
        if(newIndex >= 0 && newIndex < questions.length) {
            loadQuestion(newIndex);
        } else if (newIndex >= questions.length) {
            // Confirm submit?
        }
    };
    
    window.toggleReview = function() {
        let b = $('#btn-q-' + currentIndex);
        if(b.hasClass('reviewed')) {
            b.removeClass('reviewed');
        } else {
            b.addClass('reviewed');
        }
    };

    window.activateLifeline = function(type) {
        if (!confirm(`Using ${type.replace('_', ' ')} will cost coins. Proceed?`)) return;

        const q = questions[currentIndex];
        $.post('<?php echo app_base_url("api/quiz/lifeline/use"); ?>', { 
            type: type,
            question_id: q.id 
        }, function(res) {
            if (res.success) {
                // alert(res.message);
                applyLifelineEffect(type, res);
                // Refresh Resource HUD (if present)
                if (typeof refreshResourceHUD === 'function') refreshResourceHUD();
            } else {
                alert(res.message);
            }
        });
    };

    function applyLifelineEffect(type, data) {
        if (type === '50_50' && data.hide_indices) {
            data.hide_indices.forEach(idx => {
                $('.option-card').eq(idx).css({
                    'opacity': '0',
                    'pointer-events': 'none',
                    'transition': 'opacity 0.5s ease'
                });
            });
        } else if (type === 'poll' && data.poll_results) {
            $('.option-card').each(function(i) {
                let percent = data.poll_results[i] || 0;
                $(this).css('position', 'relative').append(`
                    <div class="poll-bar-container" style="position: absolute; bottom: 0; left: 0; width: 100%; height: 4px; background: #e3e6f0; border-radius: 0 0 8px 8px; overflow: hidden;">
                        <div class="poll-bar-fill" style="width: 0%; height: 100%; background: #1cc88a; transition: width 1s ease;"></div>
                    </div>
                    <div class="poll-percent" style="margin-left: auto; font-weight: bold; color: #1cc88a; font-size: 0.8rem; transition: opacity 0.5s;">${percent}%</div>
                `);
                setTimeout(() => {
                    $(this).find('.poll-bar-fill').css('width', percent + '%');
                }, 100);
            });
        } else if (type === 'skip') {
            nav(1);
        } else if (type === 'freeze') {
            clearInterval(timerInterval);
            // Change timer color to blue to show it's frozen
            $('#timer').removeClass('text-danger').addClass('text-info');
            setTimeout(() => {
                timerInterval = setInterval(updateTimer, 1000);
                $('#timer').removeClass('text-info').addClass('text-danger');
            }, 60000);
            alert("Timer frozen for 60 seconds!");
        }
    }
    
    window.submitExam = function(force = false) {
        if(!force && !confirm("Are you sure you want to submit the exam? You cannot change answers after submission.")) return;
        
        $.post(submitUrl, { 
            attempt_id: attemptId,
            nonce: quizNonce,
            trap_answer: document.getElementById('trap_answer').value || ''
        }, function() {
            window.location.href = '<?php echo app_base_url('quiz/result/' . $attempt['id']); ?>';
        });
    };

    // Init Logic
    // Mark saved
    Object.keys(savedAnswers).forEach(qid => {
        // Find index
        let idx = questions.findIndex(q => q.id == qid);
        if(idx >= 0) $('#btn-q-' + idx).addClass('answered');
    });
    
    loadQuestion(0);
    
</script>

</body>
</html>
