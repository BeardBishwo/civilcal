<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title); ?></title>
    <!-- Core CSS -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?php echo app_base_url('themes/admin/assets/vendor/fontawesome-free/css/all.min.css'); ?>" rel="stylesheet" type="text/css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        :root {
            --arena-bg: #f5f7fb;
            --arena-card-bg: #ffffff;
            --arena-sidebar-bg: #ffffff;
            --arena-primary: #4361ee;
            --arena-secondary: #3f37c9;
            --arena-success: #4cc9f0;
            --arena-accent: #f72585;
            --arena-text: #2b2d42;
            --arena-text-muted: #8d99ae;
            --glass-bg: rgba(255, 255, 255, 0.8);
            --glass-border: rgba(255, 255, 255, 0.3);
        }

        body.dark-mode {
            --arena-bg: #0b0e14;
            --arena-card-bg: #161b22;
            --arena-sidebar-bg: #161b22;
            --arena-text: #e6edf3;
            --arena-text-muted: #8b949e;
            --glass-bg: rgba(22, 27, 34, 0.8);
            --glass-border: rgba(255, 255, 255, 0.1);
        }

        body, html { height: 100%; overflow: hidden; background-color: var(--arena-bg); font-family: 'Inter', sans-serif; transition: background-color 0.3s; }
        .exam-container { height: 100vh; display: flex; flex-direction: column; }
        
        .top-bar { 
            height: 70px; 
            background: var(--glass-bg); 
            backdrop-filter: blur(12px); 
            border-bottom: 1px solid var(--glass-border); 
            z-index: 100; 
            display: flex; 
            align-items: center; 
            justify-content: space-between; 
            padding: 0 30px; 
        }

        .main-content { flex: 1; display: flex; overflow: hidden; }
        
        .sidebar { 
            width: 340px; 
            background: var(--arena-sidebar-bg); 
            border-right: 1px solid var(--glass-border); 
            display: flex; 
            flex-direction: column; 
            transition: transform 0.3s ease;
        }

        .focus-mode .sidebar { transform: translateX(-100%); width: 0; }
        .focus-mode .top-bar { display: none; }

        .question-area { flex: 1; padding: 40px; overflow-y: auto; background-color: var(--arena-bg); }
        .question-card-wrapper { max-width: 900px; margin: 0 auto; }

        .palette-container { padding: 20px; overflow-y: auto; flex: 1; }
        .q-pallete-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 12px; }
        
        .q-btn { 
            width: 100%; 
            aspect-ratio: 1; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            border: 1px solid var(--glass-border); 
            border-radius: 12px; 
            font-weight: 600; 
            font-size: 0.9rem;
            cursor: pointer; 
            background: var(--arena-card-bg);
            color: var(--arena-text);
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); 
        }

        .q-btn:hover { transform: scale(1.05); box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .q-btn.active { background: var(--arena-primary); color: white; border-color: var(--arena-primary); box-shadow: 0 4px 15px rgba(67, 97, 238, 0.3); }
        .q-btn.answered { background: #1cc88a; color: white; border-color: #1cc88a; }
        .q-btn.reviewed { background: #f6c23e; color: white; border-color: #f6c23e; }

        .question-header { font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px; color: var(--arena-text-muted); font-weight: 700; margin-bottom: 10px; }
        .question-text { font-size: 1.4rem; font-weight: 600; color: var(--arena-text); line-height: 1.5; margin-bottom: 30px; }

        .option-card { 
            background: var(--arena-card-bg);
            border: 2px solid var(--glass-border); 
            border-radius: 16px; 
            padding: 20px; 
            margin-bottom: 15px; 
            cursor: pointer; 
            transition: all 0.2s; 
            display: flex; 
            align-items: center;
            color: var(--arena-text);
        }

        .option-card:hover { transform: translateX(8px); border-color: var(--arena-primary); background: rgba(67, 97, 238, 0.05); }
        .option-card.selected { border-color: var(--arena-primary); background: rgba(67, 97, 238, 0.1); box-shadow: 0 4px 20px rgba(67, 97, 238, 0.15); }

        .option-icon { 
            width: 40px; 
            height: 40px; 
            border-radius: 12px; 
            background: var(--arena-bg); 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            font-weight: 700; 
            margin-right: 20px; 
            transition: all 0.2s;
        }

        .option-card.selected .option-icon { background: var(--arena-primary); color: white; }

        .status-dot { width: 8px; height: 8px; border-radius: 50%; display: inline-block; margin-right: 5px; }
        
        /* Focus Toggle */
        .floating-controls { position: fixed; bottom: 30px; right: 30px; display: flex; gap: 10px; z-index: 101; }
        .btn-round { width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; background: var(--arena-primary); color: white; border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.2); }

        /* MathJax sizing */
        .mjx-chtml { font-size: 120% !important; }
    </style>
    <!-- MathJax -->
    <script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
    <script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
</head>
<body class="<?php echo isset($_COOKIE['dark_mode']) && $_COOKIE['dark_mode'] == '1' ? 'dark-mode' : ''; ?>">

<div class="exam-container" id="examEngine">
    <!-- Top Bar -->
    <div class="top-bar">
        <div class="d-flex align-items-center">
            <div class="me-4 d-none d-md-block">
                <div class="small text-uppercase fw-bold text-muted mb-1" style="font-size: 0.65rem;">Active Exam</div>
                <h5 class="fw-bold m-0 text-dark-emphasis"><?php echo htmlspecialchars($attempt['title']); ?></h5>
            </div>
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill fw-bold">
                <i class="fas fa-shield-alt me-1"></i> FOCUS MODE
            </span>
        </div>
        <div class="d-flex align-items-center">
            <div class="text-center me-5">
                <div class="small fw-bold text-muted text-uppercase mb-1" style="font-size: 0.65rem;">Remaining</div>
                <div class="h4 mb-0 fw-bold font-monospace" id="timer" style="letter-spacing: 1px;">--:--:--</div>
            </div>
            <button class="btn btn-primary px-4 fw-bold rounded-pill shadow-sm" onclick="confirmSubmit()">
                FINISH NOW <i class="fas fa-check-double ms-2"></i>
            </button>
        </div>
    </div>

    <div class="main-content">
        <!-- Sidebar -->
        <div class="sidebar shadow-sm">
            <div class="p-4 border-bottom glass-bg">
                <h6 class="fw-bold mb-3">Navigation Hub</h6>
                <div class="d-flex flex-wrap gap-3 small text-muted">
                    <span><span class="status-dot bg-success"></span>Solved</span>
                    <span><span class="status-dot" style="background: #f6c23e;"></span>Review</span>
                    <span><span class="status-dot bg-light border"></span>Empty</span>
                </div>
            </div>
            <div class="palette-container">
                <div class="q-pallete-grid">
                    <?php foreach($questions as $index => $q): ?>
                        <div class="q-btn" id="btn-q-<?php echo $index; ?>" onclick="loadQuestion(<?php echo $index; ?>)">
                            <?php echo $index + 1; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="p-4 border-top bg-light-subtle">
                <h6 class="fw-bold mb-3 small opacity-75">STRATEGY TOOLS</h6>
                <div class="row g-2 mb-4">
                    <div class="col-6"><button class="btn btn-outline-info btn-sm w-100 rounded-3 py-2" onclick="activateLifeline('50_50')">50:50</button></div>
                    <div class="col-6"><button class="btn btn-outline-info btn-sm w-100 rounded-3 py-2" onclick="activateLifeline('poll')">POLL</button></div>
                </div>
                <button class="btn btn-warning w-100 fw-bold rounded-3 py-2" onclick="toggleReview()">
                    <i class="far fa-bookmark me-2"></i> MARK FOR REVIEW
                </button>
            </div>
        </div>

        <!-- Question Area -->
        <div class="question-area" id="arena-scroller">
            <div class="question-card-wrapper animate__animated animate__fadeIn" id="questionConf">
                <!-- Data injection point -->
            </div>
        </div>
    </div>
</div>

<div class="floating-controls">
    <button class="btn-round" onclick="toggleDarkMode()" title="Toggle Appearance">
        <i class="fas fa-moon"></i>
    </button>
    <button class="btn-round" onclick="toggleFocusMode()" title="Refined Focus">
        <i class="fas fa-expand-arrows-alt"></i>
    </button>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    const questions = <?php echo json_encode($questions); ?>;
    const savedAnswers = <?php echo json_encode($savedAnswers); ?>;
    const attemptId = <?php echo $attempt['id']; ?>;
    const saveUrl = '<?php echo app_base_url('quiz/save-answer'); ?>';
    const submitUrl = '<?php echo app_base_url('quiz/submit'); ?>';
    let csrfToken = '<?php echo htmlspecialchars($csrfToken ?? '', ENT_QUOTES, 'UTF-8'); ?>';
    
    let currentIndex = 0;

    // Timer Logic Corrected
    const durationMins = <?php echo $attempt['duration_minutes']; ?>;
    const startTimeStamp = new Date('<?php echo $attempt['started_at']; ?>').getTime();
    const serverTimestamp = <?php echo time(); ?> * 1000;
    const clientTimestamp = new Date().getTime();
    const timeDelta = serverTimestamp - clientTimestamp;

    function getAdjustedNow() { return new Date().getTime() + timeDelta; }
    
    let timeLimit = durationMins > 0 ? (startTimeStamp + durationMins * 60000) : 0;
    
    function updateTimer() {
        if (!timeLimit) return;
        let dist = timeLimit - getAdjustedNow();
        
        if (dist < 0) {
            clearInterval(timerInterval);
            document.getElementById('timer').innerHTML = "00:00:00";
            alert("Time Limit Reached! Results are being compiled.");
            confirmSubmit(true);
            return;
        }
        
        let h = Math.floor(dist / (1000 * 60 * 60));
        let m = Math.floor((dist % (1000 * 60 * 60)) / (1000 * 60));
        let s = Math.floor((dist % (1000 * 60)) / 1000);
        
        document.getElementById('timer').innerHTML = 
            (h < 10 ? "0"+h : h) + ":" + (m < 10 ? "0"+m : m) + ":" + (s < 10 ? "0"+s : s);
            
        if (dist < 300000) document.getElementById('timer').classList.add('text-danger');
    }
    
    let timerInterval = setInterval(updateTimer, 1000);
    updateTimer();

    function loadQuestion(index) {
        currentIndex = index;
        const q = questions[index];
        const saved = savedAnswers[q.id];
        
        $('.q-btn').removeClass('active');
        $('#btn-q-' + index).addClass('active');
        
        let html = `
            <div class="question-header">QUESTION ${index + 1} OF ${questions.length}</div>
            <div class="question-text">${q.content.text}</div>
            ${ q.content.image ? `<img src="${q.content.image}" class="img-fluid mb-4 rounded-4 shadow-sm border" style="max-height: 350px;">` : '' }
            
            <div class="options-container">
        `;
        
        // Options
        // This implementation assumes single choice MCQ.
        // If other types (MULTI, ORDER) are needed, their rendering logic must be re-added.
        q.options.forEach((opt, idx) => {
            let val = idx + 1;
            let isSelected = (saved == val);
            html += `
                <div class="option-card q-opt-${val} ${isSelected ? 'selected' : ''}" onclick="selectOption(${val})">
                    <div class="option-icon">${String.fromCharCode(65 + idx)}</div>
                    <div class="flex-grow-1">${opt.text}</div>
                    ${opt.image ? `<img src="${opt.image}" class="ms-3 rounded" height="50">` : ''}
                </div>
            `;
        });
        
        html += `
            </div>
            <div class="mt-5 d-flex justify-content-between align-items-center border-top pt-4">
                <button class="btn btn-outline-secondary px-5 py-2 fw-bold" onclick="nav(-1)" ${index === 0 ? 'disabled' : ''}>PREVIOUS</button>
                <div class="text-muted small fw-bold">ID: #Q-${q.id}</div>
                <button class="btn btn-primary px-5 py-2 fw-bold" onclick="nav(1)">${index === questions.length - 1 ? 'FINISH EXAM' : 'NEXT STEP'}</button>
            </div>
        `;
        
        $('#questionConf').html(html);
        $('#arena-scroller').scrollTop(0);
        
        if(window.MathJax) MathJax.typesetPromise();
    }

    window.selectOption = function(val) {
        const q = questions[currentIndex];
        $('.option-card').removeClass('selected');
        $('.q-opt-' + val).addClass('selected');
        
        savedAnswers[q.id] = val;
        $('#btn-q-' + currentIndex).addClass('answered');
        
        $.post(saveUrl, { attempt_id: attemptId, question_id: q.id, selected_options: val, csrf_token: csrfToken });
    };

    window.nav = function(dir) {
        let n = currentIndex + dir;
        if(n >= 0 && n < questions.length) loadQuestion(n);
        else if (n >= questions.length) confirmSubmit();
    };

    window.toggleDarkMode = function() {
        $('body').toggleClass('dark-mode');
        const isDark = $('body').hasClass('dark-mode');
        document.cookie = `dark_mode=${isDark ? '1' : '0'}; path=/; max-age=31536000`;
        $('.btn-round i').toggleClass('fa-moon fa-sun');
    };

    window.toggleFocusMode = function() {
        $('body').toggleClass('focus-mode');
        setTimeout(() => $(window).trigger('resize'), 300);
    };

    window.confirmSubmit = function(force = false) {
        if(!force && !confirm("Ready to wrap up? You have answered " + Object.keys(savedAnswers).length + "/" + questions.length + " questions.")) return;
        
        $.post(submitUrl, { attempt_id: attemptId, csrf_token: csrfToken }, function() {
            window.location.href = '<?php echo app_base_url('quiz/result/' . $attempt['id']); ?>';
        });
    };

    window.toggleReview = function() {
        $('#btn-q-' + currentIndex).toggleClass('reviewed');
    };

    window.activateLifeline = function(type) {
        if (!confirm(`Using ${type.replace('_', ' ')} will cost coins. Proceed?`)) return;

        const q = questions[currentIndex];
        $.post('<?php echo app_base_url("api/quiz/lifeline/use"); ?>', { 
            type: type,
            question_id: q.id,
            csrf_token: csrfToken
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
                // Assuming idx is 0-indexed for options
                $('.option-card').eq(idx).css({
                    'opacity': '0.3', // Changed to opacity for visibility but disabled
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
        }
    }

    // Initialize
    Object.keys(savedAnswers).forEach(qid => {
        let idx = questions.findIndex(q => q.id == qid);
        if(idx >= 0) $('#btn-q-' + idx).addClass('answered');
    });
    loadQuestion(0);
</script>

</body>
</html>
