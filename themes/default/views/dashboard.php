<?php
$page_title = 'User Dashboard - ' . \App\Services\SettingsService::get('site_name', 'Bishwo Calculator');
?>

<style>
    .dashboard-hero {
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
        padding: 60px 20px;
        border-radius: 30px;
        color: white;
        text-align: center;
        margin-bottom: 40px;
        box-shadow: 0 20px 40px rgba(99, 102, 241, 0.2);
    }
    .dashboard-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 30px;
    }
    .quest-card {
        background: #1e293b;
        color: white;
        border-radius: 24px;
        padding: 30px;
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(255,255,255,0.1);
    }
    .quest-card::after {
        content: '';
        position: absolute;
        top: -50px;
        right: -50px;
        width: 150px;
        height: 150px;
        background: rgba(255,255,255,0.05);
        border-radius: 50%;
    }
    .rank-card {
        background: white;
        border-radius: 24px;
        padding: 30px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 10px 25px rgba(0,0,0,0.05);
    }
    .status-badge {
        padding: 6px 16px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 700;
        text-transform: uppercase;
    }
    .status-badge.pending { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
    .status-badge.completed { background: rgba(16, 185, 129, 0.1); color: #10b981; }
    
    .progress-track {
        height: 12px;
        background: #f1f5f9;
        border-radius: 6px;
        margin: 20px 0 10px;
        overflow: hidden;
    }
    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #6366f1, #8b5cf6);
        border-radius: 6px;
        transition: width 1s ease;
    }

    @media (max-width: 900px) {
        .dashboard-grid { grid-template-columns: 1fr; }
    }
</style>

<div class="container" style="padding: 40px 20px;">
    <div class="dashboard-hero">
        <h1 style="font-size: 2.5rem; font-weight: 800; margin-bottom: 10px;">नमस्ते, <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Engineer'); ?>!</h1>
        <p style="font-size: 1.1rem; opacity: 0.9;">Welcome to your professional engineering workspace.</p>
    </div>

    <div class="dashboard-grid">
        <!-- Rank Progress Card -->
        <div class="rank-card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
                <h3 style="font-weight: 800; color: #1e293b; margin: 0;">Professional Rank</h3>
                <span style="font-size: 1.2rem; font-weight: 800; color: #6366f1;">Level <?php echo $rank['rank_level']; ?></span>
            </div>
            
            <div style="display: flex; align-items: center; gap: 20px; margin-bottom: 20px;">
                <div style="width: 60px; height: 60px; background: #f1f5f9; border-radius: 15px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; color: #6366f1;">
                    <i class="fas fa-medal"></i>
                </div>
                <div>
                    <h4 style="margin: 0; color: #1e293b; font-size: 1.1rem;"><?php echo $rank['rank']; ?></h4>
                    <p style="margin: 0; color: #64748b; font-size: 0.85rem;">Next: <?php echo $rank['next_rank']; ?></p>
                </div>
            </div>

            <div class="progress-track">
                <div class="progress-fill" style="width: <?php echo $rank['rank_progress']; ?>%;"></div>
            </div>
            <div style="display: flex; justify-content: space-between; font-size: 0.75rem; color: #64748b; font-weight: 600;">
                <span><?php echo number_format($rank['total_power']); ?> pts</span>
                <span><?php echo number_format($rank['next_rank_power']); ?> pts</span>
            </div>

            <a href="<?php echo app_base_url('/profile'); ?>" style="display: block; text-align: center; margin-top: 25px; color: #6366f1; text-decoration: none; font-weight: 700; font-size: 0.9rem;">
                View Detailed Metrics <i class="fas fa-arrow-right" style="margin-left: 5px;"></i>
            </a>
        </div>

        <!-- Daily Mission Card -->
        <div class="quest-card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
                <h3 style="font-weight: 800; color: white; margin: 0;">Current Mission</h3>
                <span class="status-badge <?php echo $quest['completed'] ? 'completed' : 'pending'; ?>">
                    <?php echo $quest['completed'] ? '<i class="fas fa-check-circle"></i> Completed' : 'In Progress'; ?>
                </span>
            </div>

            <p style="color: #94a3b8; font-size: 0.9rem; margin-bottom: 20px;">Complete a calculation with the designated tool of the day to earn bonus coins!</p>
            
            <div style="background: rgba(255,255,255,0.05); border-radius: 20px; padding: 20px; border: 1px solid rgba(255,255,255,0.1); margin-bottom: 25px;">
                <div style="display: flex; align-items: center; gap: 15px;">
                    <div style="font-size: 1.5rem; color: #fbbf24;">
                        <i class="fas fa-rocket"></i>
                    </div>
                    <div>
                        <h4 style="margin: 0; font-size: 1rem; color: white;">Tool of the Day</h4>
                        <p style="margin: 4px 0 0; color: #fbbf24; font-weight: 700;"><?php echo $quest['tool']['name']; ?></p>
                    </div>
                </div>
            </div>

            <?php if (!$quest['completed']): ?>
        <a href="<?php echo get_tool_url($quest['tool']); ?>" class="btn" style="background: white; color: #1e293b; width: 100%; border-radius: 12px; font-weight: 700; padding: 12px; text-decoration: none; display: block; text-align: center; transition: all 0.2s;">
                    Launch Tool Now
        </a>
    <?php else: ?>
        <div style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2); color: #10b981; padding: 12px; border-radius: 12px; text-align: center; font-weight: 700;">
            Reward Collected <i class="fas fa-coins" style="margin-left: 5px;"></i>
        </div>
    <?php endif; ?>
</div>

<?php 
function get_tool_url($tool) {
    if (!$tool) return '#';
    // CMS Calculators use category and calculator_id
    if (isset($tool['category']) && isset($tool['calculator_id'])) {
        return app_base_url('/calculators/' . $tool['category'] . '/' . $tool['calculator_id'] . '/protected');
    }
    // Unit converters use slug
    if (isset($tool['slug'])) {
        return app_base_url('/' . $tool['slug']);
    }
    // Fallback to path if exists
    return app_base_url($tool['path'] ?? '#');
}
?>
        <!-- App Shortcuts Card -->
        <div class="rank-card" style="grid-column: 1 / -1;">
            <h3 style="font-weight: 800; color: #1e293b; margin-bottom: 25px;">Quick Actions</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
                <a href="<?php echo app_base_url('/calculators'); ?>" style="text-decoration: none; background: #f8fafc; padding: 20px; border-radius: 16px; border: 1px solid #e2e8f0; display: flex; align-items: center; gap: 15px; transition: all 0.2s;">
                    <i class="fas fa-th-large" style="color: #6366f1; font-size: 1.2rem;"></i>
                    <span style="color: #1e293b; font-weight: 700;">All Tools</span>
                </a>
                <a href="<?php echo app_base_url('/blog'); ?>" style="text-decoration: none; background: #f8fafc; padding: 20px; border-radius: 16px; border: 1px solid #e2e8f0; display: flex; align-items: center; gap: 15px; transition: all 0.2s;">
                    <i class="fas fa-newspaper" style="color: #10b981; font-size: 1.2rem;"></i>
                    <span style="color: #1e293b; font-weight: 700;">Read & Earn</span>
                </a>
                <a href="<?php echo app_base_url('/quiz'); ?>" style="text-decoration: none; background: #f8fafc; padding: 20px; border-radius: 16px; border: 1px solid #e2e8f0; display: flex; align-items: center; gap: 15px; transition: all 0.2s;">
                    <i class="fas fa-brain" style="color: #f59e0b; font-size: 1.2rem;"></i>
                    <span style="color: #1e293b; font-weight: 700;">Take a Quiz</span>
                </a>
                <?php if (isset($_SESSION['user_role']) && in_array($_SESSION['user_role'], ['admin', 'superadmin'])): ?>
                <a href="<?php echo app_base_url('/admin/dashboard'); ?>" style="text-decoration: none; background: #fef2f2; padding: 20px; border-radius: 16px; border: 1px solid #fee2e2; display: flex; align-items: center; gap: 15px; transition: all 0.2s;">
                    <i class="fas fa-user-shield" style="color: #ef4444; font-size: 1.2rem;"></i>
                    <span style="color: #1e293b; font-weight: 700;">Admin Panel</span>
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
