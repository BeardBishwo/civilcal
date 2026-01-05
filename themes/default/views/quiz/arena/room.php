<?php
/**
 * Exam Room Interface
 * Premium SaaS Design (Distraction Free)
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title ?? 'Exam Room'); ?> | Bishwo Calculator</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --bg-dark: #0f172a;
            --bg-panel: #1e293b;
            --border-color: #334155;
            --primary: #6366f1;
            --primary-hover: #4f46e5;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
        }

        * { box-sizing: border-box; }
        
        body {
            background-color: var(--bg-dark);
            color: var(--text-main);
            font-family: 'Inter', sans-serif;
            margin: 0;
            height: 100vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        /* Top Bar */
        .exam-header {
            height: 60px;
            background: var(--bg-panel);
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            z-index: 50;
        }

        .exam-title {
            font-weight: 700;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .timer-display {
            font-family: 'JetBrains Mono', monospace;
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--warning);
            background: rgba(245, 158, 11, 0.1);
            padding: 6px 14px;
            border-radius: 8px;
            border: 1px solid rgba(245, 158, 11, 0.2);
            animation: pulse-timer 2s infinite;
        }

        @keyframes pulse-timer {
            0% { box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.2); }
            70% { box-shadow: 0 0 0 6px rgba(245, 158, 11, 0); }
            100% { box-shadow: 0 0 0 0 rgba(245, 158, 11, 0); }
        }

        .btn-submit {
            background: var(--success);
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-submit:hover { background: #059669; }

        /* Main Layout */
        .exam-layout {
            display: flex;
            flex: 1;
            overflow: hidden;
        }

        /* Sidebar - Question Palette */
        .question-palette {
            width: 280px;
            background: var(--bg-panel);
            border-right: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
        }

        .palette-header {
            padding: 15px;
            border-bottom: 1px solid var(--border-color);
            font-weight: 600;
            font-size: 0.9rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .palette-grid {
            padding: 15px;
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 8px;
            overflow-y: auto;
            flex: 1;
        }

        .q-node {
            aspect-ratio: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255,255,255,0.05);
            border: 1px solid var(--border-color);
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-muted);
            transition: all 0.2s;
        }

        .q-node:hover { border-color: var(--primary); color: white; }
        .q-node.active { background: var(--primary); color: white; border-color: var(--primary); }
        .q-node.answered { background: var(--success); color: white; border-color: var(--success); }
        .q-node.marked { border-color: var(--warning); color: var(--warning); background: rgba(245, 158, 11, 0.1); }

        .palette-legend {
            padding: 15px;
            border-top: 1px solid var(--border-color);
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            font-size: 0.75rem;
            color: var(--text-muted);
        }

        .legend-item { display: flex; align-items: center; gap: 6px; }
        .legend-dot { width: 8px; height: 8px; border-radius: 50%; }

        /* Question Area */
        .question-area {
            flex: 1;
            padding: 40px;
            overflow-y: auto;
            position: relative;
        }

        .q-card {
            max-width: 900px;
            margin: 0 auto;
        }

        .q-meta {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
        }

        .q-badge {
            background: rgba(99, 102, 241, 0.1);
            color: var(--primary);
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 600;
            border: 1px solid rgba(99, 102, 241, 0.2);
        }

        .q-content {
            font-size: 1.2rem;
            line-height: 1.6;
            margin-bottom: 30px;
        }
        
        .q-content table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            border: 1px solid var(--border-color);
        }
        
        .q-content th, .q-content td {
            border: 1px solid var(--border-color);
            padding: 8px;
            text-align: left;
        }
        
        .q-content img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            margin-top: 10px;
        }

        .options-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 15px;
        }

        .option-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--border-color);
            padding: 20px;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: flex-start;
            gap: 15px;
        }

        .option-card:hover {
            border-color: var(--primary);
            background: rgba(99, 102, 241, 0.05);
        }

        .option-card.selected {
            border-color: var(--primary);
            background: rgba(99, 102, 241, 0.1);
            box-shadow: 0 0 0 1px var(--primary);
        }

        .opt-radio {
            width: 20px;
            height: 20px;
            border: 2px solid var(--text-muted);
            border-radius: 50%;
            margin-top: 2px;
            flex-shrink: 0;
            position: relative;
        }

        .option-card.selected .opt-radio {
            border-color: var(--primary);
        }

        .option-card.selected .opt-radio::after {
            content: '';
            position: absolute;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            width: 10px; height: 10px;
            background: var(--primary);
            border-radius: 50%;
        }

        .q-nav {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
            padding-top: 20px;
            border-top: 1px solid var(--border-color);
        }

        .btn-nav {
            background: var(--bg-panel);
            color: var(--text-main);
            border: 1px solid var(--border-color);
            padding: 10px 24px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }

        .btn-nav:hover { background: rgba(255,255,255,0.1); }
        .btn-nav:disabled { opacity: 0.5; cursor: not-allowed; }
        
        .btn-primary { background: var(--primary); border-color: var(--primary); }
        .btn-primary:hover { background: var(--primary-hover); }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .exam-layout { flex-direction: column; }
            .question-palette { width: 100%; height: 60px; border-right: none; border-bottom: 1px solid var(--border-color); flex-direction: row; align-items: center; }
            .palette-grid { display: flex; padding: 10px; overflow-x: auto; grid-template-columns: none; }
            .palette-header, .palette-legend { display: none; }
            .q-node { width: 40px; height: 40px; flex-shrink: 0; }
            .question-area { padding: 20px; }
            .timer-display { font-size: 1rem; padding: 4px 10px; }
        }
    </style>
</head>
<body>

<?php
// PHP Variables from Controller
// $attempt, $questions, $savedAnswers, $quizNonce, $csrfToken
$durationSecs = ($attempt['duration_minutes'] ?? 60) * 60;
$startTime = $attempt['started_at_ts'] ?? time(); // Ideally passed from controller
$elapsed = time() - $startTime;
$remaining = max(0, $durationSecs - $elapsed);
?>

<div class="exam-header">
    <div class="exam-title">
        <i class="fas fa-layer-group text-primary"></i> 
        <?php echo htmlspecialchars($title); ?>
    </div>
    <div class="d-flex align-items-center gap-3">
        <div class="timer-display" id="exam-timer">
            00:00:00
        </div>
        <button class="btn-submit" onclick="submitExam()">
            Submit <span class="hidden-mobile">Exam</span>
        </button>
    </div>
</div>

<div class="exam-layout">
    <!-- Sidebar -->
    <div class="question-palette">
        <div class="palette-header">
            Question Palette
        </div>
        <div class="palette-grid">
            <?php foreach ($questions as $index => $q): 
                $qid = $q['id'];
                $isAnswered = isset($savedAnswers[$qid]);
                $statusClass = $isAnswered ? 'answered' : '';
                $activeClass = $index === 0 ? 'active' : '';
            ?>
                <div class="q-node <?php echo $statusClass . ' ' . $activeClass; ?>" 
                     id="node-<?php echo $index; ?>" 
                     onclick="loadQuestion(<?php echo $index; ?>)">
                    <?php echo $index + 1; ?>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="palette-legend">
            <div class="legend-item"><span class="legend-dot" style="background:var(--success)"></span> Answered</div>
            <div class="legend-item"><span class="legend-dot" style="border:1px solid var(--border-color)"></span> Unanswered</div>
            <div class="legend-item"><span class="legend-dot" style="background:var(--primary)"></span> Current</div>
            <div class="legend-item"><span class="legend-dot" style="background:var(--warning)"></span> Marked</div>
        </div>
    </div>

    <!-- Question Area -->
    <div class="question-area">
        <?php foreach ($questions as $index => $q): 
            $qid = $q['id'];
            $displayStyle = $index === 0 ? 'block' : 'none';
            // Parse Options if string
            $options = is_string($q['options']) ? json_decode($q['options'], true) : $q['options'];
            $selected = $savedAnswers[$qid] ?? null;
        ?>
            <div class="q-card question-item" id="q-<?php echo $index; ?>" style="display: <?php echo $displayStyle; ?>;">
                <div class="q-meta">
                    <span class="q-badge">Question <?php echo $index + 1; ?> of <?php echo count($questions); ?></span>
                    <span class="q-badge" style="color:var(--text-muted); background:rgba(255,255,255,0.05); border-color:var(--border-color)">
                        +<?php echo $q['default_marks']; ?> / -<?php echo $q['default_negative_marks']; ?>
                    </span>
                </div>

                <div class="q-content">
                    <?php 
                        // Handle content array structure (text/image)
                        if (is_array($q['content'])) {
                            echo $q['content']['text'] ?? '';
                            if (!empty($q['content']['image'])) {
                                echo '<br><img src="' . htmlspecialchars($q['content']['image']) . '">';
                            }
                        } else {
                            echo $q['content']; 
                        }
                    ?>
                </div>

                <div class="options-grid">
                    <?php if ($options && is_array($options)): ?>
                        <?php foreach ($options as $key => $opt): 
                            $optText = is_array($opt) ? ($opt['text'] ?? '') : $opt;
                            $isSelected = $selected == $key;
                            $cardClass = $isSelected ? 'selected' : '';
                        ?>
                            <div class="option-card <?php echo $cardClass; ?>" 
                                 onclick="selectOption(<?php echo $index; ?>, '<?php echo $qid; ?>', '<?php echo $key; ?>', this)">
                                <div class="opt-radio"></div>
                                <div><?php echo htmlspecialchars($optText); ?></div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <div class="q-nav">
                    <button class="btn-nav" <?php echo $index === 0 ? 'disabled' : ''; ?> 
                            onclick="loadQuestion(<?php echo $index - 1; ?>)">
                        <i class="fas fa-arrow-left"></i> Prev
                    </button>
                    
                    <button class="btn-nav" onclick="toggleMark(<?php echo $index; ?>)">
                        <i class="fas fa-flag"></i> Mark
                    </button>

                    <button class="btn-nav btn-primary" 
                            onclick="loadQuestion(<?php echo $index + 1; ?>)">
                        Next <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
    // State
    let currentQ = 0;
    const totalQ = <?php echo count($questions); ?>;
    let remainingSecs = <?php echo $durationSecs; ?>; // Simplified, ideally sync with server
    const attemptId = '<?php echo $attempt['id']; ?>';
    const csrfToken = '<?php echo $csrfToken; ?>';

    // Timer
    const timerEl = document.getElementById('exam-timer');
    const timerInterval = setInterval(() => {
        remainingSecs--;
        if (remainingSecs <= 0) {
            clearInterval(timerInterval);
            submitExam(true);
        }
        updateTimerDisplay();
    }, 1000);

    function updateTimerDisplay() {
        const h = Math.floor(remainingSecs / 3600);
        const m = Math.floor((remainingSecs % 3600) / 60);
        const s = remainingSecs % 60;
        timerEl.textContent = 
            (h < 10 ? '0'+h : h) + ':' + 
            (m < 10 ? '0'+m : m) + ':' + 
            (s < 10 ? '0'+s : s);
            
        if (remainingSecs < 300) { // 5 mins
            timerEl.style.color = '#ef4444';
            timerEl.style.borderColor = '#ef4444';
        }
    }

    // Navigation
    function loadQuestion(index) {
        if (index < 0 || index >= totalQ) return;
        
        // Hide Current
        document.getElementById('q-' + currentQ).style.display = 'none';
        document.getElementById('node-' + currentQ).classList.remove('active');
        
        // Show New
        currentQ = index;
        document.getElementById('q-' + currentQ).style.display = 'block';
        document.getElementById('node-' + currentQ).classList.add('active');
    }

    // Selection
    function selectOption(qIndex, qId, optKey, cardEl) {
        // UI Update
        const parent = cardEl.parentElement;
        const cards = parent.querySelectorAll('.option-card');
        cards.forEach(c => c.classList.remove('selected'));
        cardEl.classList.add('selected');
        
        // Palette Update
        document.getElementById('node-' + qIndex).classList.add('answered');

        // Server Save (Debounced ideally, but simple here)
        saveAnswer(qId, optKey);
    }

    function saveAnswer(qId, optKey) {
        const formData = new FormData();
        formData.append('attempt_id', attemptId);
        formData.append('question_id', qId);
        formData.append('selected_options', optKey); // Assuming single choice for now
        formData.append('csrf_token', csrfToken); // Add CSRF if needed by framework

        fetch('<?php echo app_base_url("quiz/save-answer"); ?>', {
            method: 'POST',
            body: formData
        }).catch(err => console.error('Save failed', err));
    }

    function toggleMark(index) {
        document.getElementById('node-' + index).classList.toggle('marked');
        // Visual only for now
    }

    function submitExam(auto = false) {
        if (!auto && !confirm('Are you sure you want to submit? This action cannot be undone.')) return;
        
        const formData = new FormData();
        formData.append('attempt_id', attemptId);
        formData.append('nonce', '<?php echo $quizNonce; ?>');
        
        fetch('<?php echo app_base_url("quiz/submit"); ?>', {
            method: 'POST',
            body: formData
        }).then(res => {
            window.location.href = '<?php echo app_base_url("quiz/result/" . $attemptId); ?>';
        });
    }

    updateTimerDisplay();
</script>

</body>
</html>
