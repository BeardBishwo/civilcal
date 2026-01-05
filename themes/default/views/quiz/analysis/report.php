<?php
/**
 * Exam Result / Analysis Page
 * Premium SaaS Design
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam Analysis | Bishwo Calculator</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --bg-dark: #0f172a;
            --bg-card: #1e293b;
            --border: #334155;
            --primary: #6366f1;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
        }

        body {
            background-color: var(--bg-dark);
            color: var(--text-main);
            font-family: 'Inter', sans-serif;
            margin: 0;
            min-height: 100vh;
            padding-bottom: 50px;
        }

        /* Header */
        .result-header {
            background: linear-gradient(135deg, #1e1b4b 0%, #0f172a 100%);
            padding: 40px 0 60px;
            border-bottom: 1px solid var(--border);
            text-align: center;
        }

        .score-circle {
            width: 140px;
            height: 140px;
            border-radius: 50%;
            background: rgba(255,255,255,0.05);
            border: 4px solid var(--primary);
            margin: 0 auto 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            box-shadow: 0 0 30px rgba(99, 102, 241, 0.3);
            position: relative;
        }

        .score-val { font-size: 2.5rem; font-weight: 800; line-height: 1; }
        .score-total { font-size: 0.9rem; color: var(--text-muted); margin-top: 5px; }

        .exam-title { font-size: 1.5rem; font-weight: 700; margin-bottom: 10px; }
        .exam-meta { color: var(--text-muted); font-size: 0.9rem; }

        /* Content Container */
        .container {
            max-width: 1000px;
            margin: -40px auto 0;
            padding: 0 20px;
            display: grid;
            gap: 30px;
        }

        /* Card Styles */
        .card {
            background: var(--bg-card);
            border-radius: 16px;
            border: 1px solid var(--border);
            padding: 25px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }

        .card-header {
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--border);
            padding-bottom: 15px;
        }

        /* Metrics Grid */
        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
        }

        .metric-box {
            background: rgba(255,255,255,0.02);
            padding: 15px;
            border-radius: 10px;
            text-align: center;
            border: 1px solid var(--border);
        }
        
        .metric-val { font-size: 1.25rem; font-weight: 700; color: white; display: block; margin-bottom: 5px; }
        .metric-label { font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; }

        .text-success { color: var(--success); }
        .text-danger { color: var(--danger); }
        .text-warning { color: var(--warning); }

        /* Incorrect Questions Review */
        .review-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .review-item {
            background: rgba(239, 68, 68, 0.05); /* Slight redshift */
            border: 1px solid rgba(239, 68, 68, 0.2);
            border-radius: 12px;
            padding: 20px;
            transition: all 0.2s;
        }
        
        .review-item:hover {
            border-color: var(--danger);
            background: rgba(239, 68, 68, 0.1);
        }

        .ri-header {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin-bottom: 10px;
        }

        .ri-icon { color: var(--danger); font-size: 1.2rem; margin-top: 2px; }
        
        .ri-question {
            font-weight: 600;
            font-size: 1rem;
            line-height: 1.5;
            flex: 1;
        }

        .ri-explanation {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid rgba(255,255,255,0.1);
            font-size: 0.9rem;
            color: var(--text-muted);
        }
        .ri-explanation strong { color: var(--text-main); display: block; margin-bottom: 5px; font-size: 0.8rem; text-transform: uppercase; }

        /* Actions */
        .action-bar {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 20px;
        }

        .btn {
            padding: 12px 30px;
            border-radius: 10px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
            font-size: 1rem;
        }

        .btn-primary { background: var(--primary); color: white; }
        .btn-primary:hover { background: #4f46e5; transform: translateY(-2px); }

        .btn-outline { background: transparent; border: 1px solid var(--border); color: var(--text-muted); }
        .btn-outline:hover { border-color: white; color: white; background: rgba(255,255,255,0.05); }

        /* Mobile */
        @media (max-width: 768px) {
            .metrics-grid { grid-template-columns: 1fr 1fr; }
            .score-circle { width: 100px; height: 100px; }
            .score-val { font-size: 1.8rem; }
        }
    </style>
</head>
<body>

    <?php
    // $attempt, $incorrect_answers
    $percentage = ($attempt['score'] / $attempt['total_marks']) * 100;
    $statusColor = $percentage >= 40 ? 'var(--success)' : 'var(--danger)';
    ?>

    <header class="result-header">
        <div class="score-circle" style="border-color: <?php echo $statusColor; ?>; box-shadow: 0 0 30px <?php echo $percentage >= 40 ? 'rgba(16, 185, 129, 0.3)' : 'rgba(239, 68, 68, 0.3)'; ?>">
            <span class="score-val"><?php echo round($attempt['score'], 1); ?></span>
            <span class="score-total">/ <?php echo $attempt['total_marks']; ?></span>
        </div>
        <h1 class="exam-title"><?php echo htmlspecialchars($attempt['title']); ?></h1>
        <p class="exam-meta">
            Completed on <?php echo date('M d, Y h:i A', strtotime($attempt['completed_at'])); ?> 
            &bull; <?php echo ($percentage >= 40) ? '<span class="text-success">Passed</span>' : '<span class="text-danger">Failed</span>'; ?>
        </p>
    </header>

    <div class="container">
        <!-- Key Metrics -->
        <div class="metrics-grid">
            <div class="metric-box">
                <span class="metric-val"><?php echo round($percentage, 1); ?>%</span>
                <span class="metric-label">Percentage</span>
            </div>
            <div class="metric-box">
                <span class="metric-val"><?php echo gmdate("H:i:s", strtotime($attempt['completed_at']) - strtotime($attempt['started_at'])); ?></span>
                <span class="metric-label">Time Taken</span>
            </div>
            <div class="metric-box">
                <span class="metric-val text-danger"><?php echo count($incorrect_answers); ?></span>
                <span class="metric-label">Mistakes</span>
            </div>
            <div class="metric-box">
                <span class="metric-val text-warning">+<?php echo $_SESSION['latest_streak_info']['coins'] ?? 0; ?></span>
                <span class="metric-label">Coins Earned</span>
            </div>
        </div>

        <!-- Smart Analysis / Mistakes -->
        <?php if (!empty($incorrect_answers)): ?>
        <div class="card">
            <div class="card-header">
                <div>
                    <i class="fas fa-microscope text-warning mr-2"></i> Smart Analysis
                </div>
                <!-- Link to full solution if available -->
            </div>
            
            <div class="review-list">
                <?php foreach ($incorrect_answers as $inc): ?>
                    <div class="review-item">
                        <div class="ri-header">
                            <i class="fas fa-times-circle ri-icon"></i>
                            <div class="ri-question">
                                <?php 
                                    if (is_array($inc['content'])) {
                                        echo $inc['content']['text'] ?? '';
                                    } else {
                                        echo $inc['content'];
                                    }
                                ?>
                            </div>
                        </div>
                        <?php if (!empty($inc['explanation'])): ?>
                            <div class="ri-explanation">
                                <strong><i class="fas fa-lightbulb"></i> Explanation</strong>
                                <?php echo $inc['explanation']; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Actions -->
        <div class="action-bar">
            <a href="<?php echo app_base_url('/quiz'); ?>" class="btn btn-outline">
                <i class="fas fa-home"></i> Back to Portal
            </a>
            <a href="<?php echo app_base_url('/quiz/start/' . ($attempt['slug'] ?? 'daily-quest')); ?>" class="btn btn-primary">
                <i class="fas fa-redo"></i> Retake Exam
            </a>
        </div>
    </div>

</body>
</html>
