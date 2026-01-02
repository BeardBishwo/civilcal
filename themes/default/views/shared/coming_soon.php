<?php
// themes/default/views/shared/coming_soon.php
?>
<div class="cs-page">
    <div class="cs-orbit cs-orbit-1"></div>
    <div class="cs-orbit cs-orbit-2"></div>
    
    <div class="cs-container">
        <div class="cs-glass-card">
            <div class="cs-icon-shell">
                <i class="fas fa-tools cs-pulse-icon"></i>
            </div>
            <h1 class="cs-title"><?php echo $module_name ?? 'Under Construction'; ?></h1>
            <p class="cs-text">We are currently building something amazing for the Civil City community. This module is under active development and will be available soon.</p>
            
            <div class="cs-progress-shell">
                <div class="cs-progress-labels">
                    <span>Progress</span>
                    <span>75%</span>
                </div>
                <div class="cs-progress-bar">
                    <div class="cs-progress-fill" style="width: 75%;"></div>
                </div>
            </div>

            <div class="cs-meta">
                <div class="cs-meta-item">
                    <span class="cs-meta-label">ETA</span>
                    <span class="cs-meta-value"><?php echo $expected_date ?? 'Coming Soon'; ?></span>
                </div>
                <div class="cs-meta-divider"></div>
                <div class="cs-meta-item">
                    <span class="cs-meta-label">Status</span>
                    <span class="cs-meta-value cs-status-active">Architecting</span>
                </div>
            </div>

            <div class="cs-actions">
                <a href="<?php echo app_base_url('dashboard'); ?>" class="cs-btn-primary">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.cs-page {
    background: radial-gradient(circle at top right, #1a1f3c, #0a0e1a 60%);
    min-height: calc(100vh - 200px);
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    overflow: hidden;
    color: #e8ecf2;
    font-family: 'Inter', sans-serif;
    padding: 60px 20px;
}

.cs-orbit {
    position: absolute;
    border-radius: 50%;
    filter: blur(80px);
    z-index: 0;
    opacity: 0.15;
}

.cs-orbit-1 {
    width: 400px;
    height: 400px;
    background: #7c5dff;
    top: -100px;
    right: -100px;
}

.cs-orbit-2 {
    width: 300px;
    height: 300px;
    background: #00d1ff;
    bottom: -50px;
    left: -50px;
}

.cs-container {
    position: relative;
    z-index: 1;
    max-width: 600px;
    width: 100%;
    text-align: center;
}

.cs-glass-card {
    background: rgba(255, 255, 255, 0.03);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 24px;
    padding: 48px 32px;
    box-shadow: 0 24px 48px rgba(0, 0, 0, 0.4);
}

.cs-icon-shell {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, rgba(124, 93, 255, 0.2), rgba(0, 209, 255, 0.1));
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 24px;
    border: 1px solid rgba(124, 93, 255, 0.3);
}

.cs-pulse-icon {
    font-size: 32px;
    color: #7c5dff;
    animation: cs-pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes cs-pulse {
    0%, 100% { transform: scale(1); opacity: 1; }
    50% { transform: scale(1.1); opacity: 0.7; }
}

.cs-title {
    font-size: 36px;
    font-weight: 800;
    margin-bottom: 16px;
    background: linear-gradient(135deg, #fff, #9aa4b5);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    letter-spacing: -0.02em;
}

.cs-text {
    font-size: 16px;
    color: #9aa4b5;
    line-height: 1.6;
    margin-bottom: 32px;
}

.cs-progress-shell {
    margin-bottom: 32px;
}

.cs-progress-labels {
    display: flex;
    justify-content: space-between;
    font-size: 12px;
    font-weight: 600;
    color: #7c5dff;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 8px;
}

.cs-progress-bar {
    height: 8px;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 999px;
    overflow: hidden;
}

.cs-progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #7c5dff, #00d1ff);
    border-radius: 999px;
    box-shadow: 0 0 15px rgba(124, 93, 255, 0.5);
}

.cs-meta {
    display: flex;
    background: rgba(0, 0, 0, 0.2);
    border-radius: 16px;
    padding: 16px;
    margin-bottom: 32px;
}

.cs-meta-item {
    flex: 1;
}

.cs-meta-divider {
    width: 1px;
    background: rgba(255, 255, 255, 0.1);
    margin: 0 16px;
}

.cs-meta-label {
    display: block;
    font-size: 10px;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: #5a6b8a;
    margin-bottom: 4px;
}

.cs-meta-value {
    font-weight: 700;
    font-size: 14px;
    color: #e8ecf2;
}

.cs-status-active {
    color: #00d1ff;
    position: relative;
    padding-left: 12px;
}

.cs-status-active::before {
    content: '';
    position: absolute;
    left: 0;
    top: 50%;
    transform: translateY(-50%);
    width: 6px;
    height: 6px;
    background: #00d1ff;
    border-radius: 50%;
    box-shadow: 0 0 8px #00d1ff;
}

.cs-btn-primary {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    background: linear-gradient(135deg, #7c5dff, #6366f1);
    color: white;
    padding: 14px 28px;
    border-radius: 14px;
    font-weight: 600;
    font-size: 15px;
    text-decoration: none !important;
    transition: all 0.3s ease;
    box-shadow: 0 8px 20px rgba(124, 93, 255, 0.3);
}

.cs-btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 28px rgba(124, 93, 255, 0.4);
    filter: brightness(1.1);
    color: white;
}

@media (max-width: 480px) {
    .cs-title { font-size: 28px; }
    .cs-glass-card { padding: 32px 24px; }
}
</style>
