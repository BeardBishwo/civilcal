<?php
/**
 * Exam Overview / Instructions Page
 * Premium SaaS Design
 */
?>
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

:root {
    --ov-bg: #0f172a;
    --ov-card: rgba(255, 255, 255, 0.03);
    --ov-border: rgba(255, 255, 255, 0.08);
    --ov-primary: #667eea;
    --ov-primary-grad: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --ov-text: #ffffff;
    --ov-muted: #94a3b8;
    --ov-glow: 0 8px 32px rgba(102, 126, 234, 0.15);
}

.overview-wrapper {
    background: var(--ov-bg);
    min-height: 100vh;
    color: var(--ov-text);
    font-family: 'Inter', system-ui, sans-serif;
    position: relative;
    padding: 60px 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}

/* Background Effects */
.ov-bg-orb {
    position: absolute;
    width: 600px;
    height: 600px;
    border-radius: 50%;
    filter: blur(100px);
    opacity: 0.15;
    z-index: 0;
    animation: float 20s infinite ease-in-out;
}
.ov-orb-1 { top: -20%; left: -10%; background: #667eea; }
.ov-orb-2 { bottom: -20%; right: -10%; background: #764ba2; animation-delay: -10s; }

@keyframes float {
    0%, 100% { transform: translate(0, 0); }
    50% { transform: translate(30px, -30px); }
}

.overview-card {
    position: relative;
    z-index: 10;
    background: var(--ov-card);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid var(--ov-border);
    border-radius: 24px;
    padding: 60px;
    max-width: 900px;
    width: 100%;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
    text-align: center;
}

.exam-icon-wrapper {
    width: 100px;
    height: 100px;
    margin: 0 auto 30px;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 40px;
    color: var(--ov-primary);
    border: 1px solid var(--ov-border);
    box-shadow: var(--ov-glow);
}

.exam-title {
    font-size: 2.5rem;
    font-weight: 800;
    margin-bottom: 15px;
    background: var(--ov-primary-grad);
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
    line-height: 1.2;
}

.exam-desc {
    color: var(--ov-muted);
    font-size: 1.1rem;
    max-width: 600px;
    margin: 0 auto 40px;
    line-height: 1.6;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
    margin-bottom: 40px;
    border-top: 1px solid var(--ov-border);
    border-bottom: 1px solid var(--ov-border);
    padding: 30px 0;
}

.stat-item h4 {
    color: var(--ov-muted);
    font-size: 0.85rem;
    text-transform: uppercase;
    font-weight: 600;
    letter-spacing: 1px;
    margin: 0 0 5px;
}

.stat-item .value {
    font-size: 1.5rem;
    font-weight: 700;
    color: white;
}

.instructions-list {
    text-align: left;
    background: rgba(0, 0, 0, 0.2);
    border-radius: 16px;
    padding: 30px;
    margin-bottom: 40px;
}

.instructions-list h3 {
    font-size: 1.1rem;
    font-weight: 700;
    margin-bottom: 20px;
    color: white;
    display: flex;
    align-items: center;
    gap: 10px;
}

.instructions-list ul {
    list-style: none;
    padding: 0;
    margin: 0;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
}

.instructions-list li {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    color: var(--ov-muted);
    font-size: 0.95rem;
}

.instructions-list li i {
    color: var(--ov-primary);
    margin-top: 4px;
}

.btn-start {
    background: var(--ov-primary-grad);
    color: white;
    border: none;
    padding: 18px 60px;
    font-size: 1.2rem;
    font-weight: 700;
    border-radius: 16px;
    cursor: pointer;
    transition: all 0.3s;
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
    display: inline-flex;
    align-items: center;
    gap: 12px;
    text-decoration: none;
}

.btn-start:hover {
    transform: translateY(-3px) scale(1.02);
    box-shadow: 0 20px 40px rgba(102, 126, 234, 0.5);
}

.back-link {
    position: absolute;
    top: 40px;
    left: 40px;
    color: var(--ov-muted);
    text-decoration: none;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: color 0.2s;
    z-index: 20;
}
.back-link:hover { color: white; }

@media (max-width: 768px) {
    .overview-card { padding: 30px; }
    .exam-title { font-size: 2rem; }
    .stats-grid { grid-template-columns: 1fr 1fr; }
    .instructions-list ul { grid-template-columns: 1fr; }
}
</style>

<div class="overview-wrapper">
    <div class="ov-bg-orb ov-orb-1"></div>
    <div class="ov-bg-orb ov-orb-2"></div>

    <a href="<?php echo app_base_url('quiz'); ?>" class="back-link">
        <i class="fas fa-arrow-left"></i> Back to Portal
    </a>

    <div class="overview-card">
        <div class="exam-icon-wrapper">
            <i class="fas fa-file-signature"></i>
        </div>

        <h1 class="exam-title"><?php echo htmlspecialchars($exam['title']); ?></h1>
        <p class="exam-desc">
            <?php echo !empty($exam['description']) ? htmlspecialchars($exam['description']) : 'Prepare yourself for a challenge. This exam covers comprehensive topics to test your mastery.'; ?>
        </p>

        <div class="stats-grid">
            <div class="stat-item">
                <h4>Duration</h4>
                <div class="value"><?php echo $exam['duration_minutes']; ?>m</div>
            </div>
            <div class="stat-item">
                <h4>Questions</h4>
                <div class="value"><?php echo $question_count; ?></div>
            </div>
            <div class="stat-item">
                <h4>Total Marks</h4>
                <div class="value"><?php echo $exam['total_marks']; ?></div>
            </div>
            <div class="stat-item">
                <h4>Mode</h4>
                <div class="value" style="text-transform: capitalize;"><?php echo $exam['mode']; ?></div>
            </div>
        </div>

        <div class="instructions-list">
            <h3><i class="fas fa-clipboard-list"></i> Exam Instructions</h3>
            <ul>
                <li><i class="fas fa-check-circle"></i> Complete all questions within the time limit.</li>
                <li><i class="fas fa-check-circle"></i> Review your answers before submitting.</li>
                <li><i class="fas fa-check-circle"></i> Don't switch tabs (Proctored Mode enabled).</li>
                <li><i class="fas fa-check-circle"></i> Incorrect answers may have negative marking.</li>
                <li><i class="fas fa-check-circle"></i> Ensure stable internet connection.</li>
                <li><i class="fas fa-check-circle"></i> Best of luck, Engineer!</li>
            </ul>
        </div>

        <form action="<?php echo app_base_url('quiz/start/' . $exam['slug']); ?>" method="GET">
            <button type="submit" class="btn-start">
                Start Exam Now <i class="fas fa-arrow-right"></i>
            </button>
        </form>
    </div>
</div>
