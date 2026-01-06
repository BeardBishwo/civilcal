<?php
/**
 * PREMIUM LEADERBOARD MANAGEMENT
 * Professional, high-density layout with performance trends and metallic rankings.
 */
$rankings = $rankings ?? [];
$current_period = $current_period ?? 'weekly';
$current_value = $current_value ?? '';
?>

<div class="admin-wrapper-container">
    <div class="admin-content-wrapper">

        <!-- Compact Page Header -->
        <div class="compact-header">
            <div class="header-left">
                <div class="header-title">
                    <i class="fas fa-trophy"></i>
                    <h1>Leaderboard Vault</h1>
                </div>
                <div class="header-subtitle">Top 100 Performers • <?php echo ucfirst($current_period); ?> Cycle</div>
            </div>
            
            <div class="header-actions" style="display:flex; gap:10px;">
                <div class="stat-pill">
                    <span class="label">PERIOD</span>
                    <span class="value"><?php echo strtoupper($current_period); ?></span>
                </div>
                <div class="stat-pill warning">
                    <span class="label">ACTIVE USERS</span>
                    <span class="value"><?php echo count($rankings); ?></span>
                </div>
            </div>
        </div>

        <!-- Period Selection Toolbar -->
        <div class="compact-toolbar">
            <div class="toolbar-left">
                <div class="period-pills">
                    <a href="?period=weekly" class="pill-btn <?php echo $current_period == 'weekly' ? 'active' : ''; ?>">Weekly</a>
                    <a href="?period=monthly" class="pill-btn <?php echo $current_period == 'monthly' ? 'active' : ''; ?>">Monthly</a>
                    <a href="?period=yearly" class="pill-btn <?php echo $current_period == 'yearly' ? 'active' : ''; ?>">Yearly</a>
                </div>
            </div>
            <div class="toolbar-right">
                <div class="drag-hint">
                    <i class="fas fa-history"></i> Last Updated: <?php echo date('H:i'); ?>
                </div>
            </div>
        </div>

        <!-- Leaderboard Table -->
        <div class="table-container">
            <div class="table-wrapper">
                <table class="table-compact">
                    <thead>
                        <tr>
                            <th style="width: 80px;" class="text-center">Standing</th>
                            <th>Engineer Identifier</th>
                            <th class="text-center">Total Points</th>
                            <th class="text-center">Precision</th>
                            <th class="text-center">Missions</th>
                            <th class="text-center">Trend</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($rankings)): ?>
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state-compact">
                                        <i class="fas fa-award"></i>
                                        <h3>The vault is empty</h3>
                                        <p>No activity recorded for this period yet.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($rankings as $rank): ?>
                                <tr class="<?php echo $rank['calculated_rank'] <= 3 ? 'rank-highlight' : ''; ?>">
                                    <td class="text-center align-middle">
                                        <div class="rank-orb <?php echo 'orb-' . $rank['calculated_rank']; ?>">
                                            <?php if ($rank['calculated_rank'] == 1): ?>
                                                <i class="fas fa-crown"></i>
                                            <?php elseif ($rank['calculated_rank'] == 2): ?>
                                                <i class="fas fa-medal silver"></i>
                                            <?php elseif ($rank['calculated_rank'] == 3): ?>
                                                <i class="fas fa-medal bronze"></i>
                                            <?php else: ?>
                                                #<?php echo $rank['calculated_rank']; ?>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="item-info">
                                            <div class="user-avatar-main <?php echo $rank['calculated_rank'] == 1 ? 'rank-1-prime' : ''; ?>">
                                                <?php echo strtoupper(substr($rank['username'] ?? 'U', 0, 1)); ?>
                                            </div>
                                            <div class="item-text">
                                                <div class="item-title"><?php echo htmlspecialchars($rank['full_name']); ?></div>
                                                <div class="item-slug">@<?php echo htmlspecialchars($rank['username']); ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">
                                        <div class="score-orb points">
                                            <?php echo number_format($rank['total_score'], 0); ?>
                                        </div>
                                    </td>
                                    <td class="text-center align-middle" style="width: 180px;">
                                        <div class="precision-bundle">
                                            <div class="bundle-top">
                                                <span class="bundle-val"><?php echo number_format($rank['accuracy_avg'], 1); ?>%</span>
                                                <span class="bundle-label">ACCURACY</span>
                                            </div>
                                            <div class="bundle-progress">
                                                <div class="progress-bar" style="width: <?php echo $rank['accuracy_avg']; ?>%;"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">
                                        <span class="metric-text"><?php echo $rank['tests_taken']; ?></span>
                                    </td>
                                    <td class="text-center align-middle">
                                        <?php if($rank['trend'] > 0): ?>
                                            <div class="trend-indicator up">
                                                <i class="fas fa-caret-up"></i>
                                                <span><?php echo $rank['trend']; ?></span>
                                            </div>
                                        <?php elseif($rank['trend'] < 0): ?>
                                            <div class="trend-indicator down">
                                                <i class="fas fa-caret-down"></i>
                                                <span><?php echo abs($rank['trend']); ?></span>
                                            </div>
                                        <?php else: ?>
                                            <span class="trend-neutral">STABLE</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center align-middle">
                                        <button class="action-btn-icon ban-btn" title="Restrict Profile">
                                            <i class="fas fa-user-slash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
/* ========================================
   PREMIUM LEADERBOARD DASHBOARD STYLES
   ======================================== */
:root {
    --premium-primary: #6366f1;
    --premium-secondary: #7c3aed;
    --premium-gold: #fbbf24;
    --premium-silver: #94a3b8;
    --premium-bronze: #d97706;
}

.admin-wrapper-container { padding: 1rem; background: #f8fafc; min-height: calc(100vh - 70px); }
.admin-content-wrapper { background: white; border-radius: 16px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05); overflow: hidden; /* padding-bottom: 2rem; REMOVED FOR CLEANER UI */ }

/* Font Branding */
@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap');
.admin-wrapper-container *:not(i):not([class*="fa-"]) { font-family: 'Outfit', sans-serif; }

/* FontAwesome Safeguard */
.admin-wrapper-container i.fas, 
.admin-wrapper-container i.fa-solid,
.admin-wrapper-container i.fa-regular,
.admin-wrapper-container i.fa-brands { 
    font-family: "Font Awesome 6 Free", "Font Awesome 5 Free", "FontAwesome" !important; 
    font-weight: 900;
}

/* Header Protocol */
.compact-header { 
    display: flex; justify-content: space-between; align-items: center; padding: 1.5rem 2rem; 
    background: linear-gradient(135deg, #1e1b4b 0%, #4338ca 100%); color: white; 
}
.header-title { display: flex; align-items: center; gap: 0.75rem; }
.header-title h1 { margin: 0; font-size: 1.5rem; font-weight: 800; color: white; letter-spacing: -0.5px; }
.header-subtitle { font-size: 0.85rem; opacity: 0.8; margin-top: 4px; font-weight: 500; }

.stat-pill { 
    background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.15); border-radius: 10px; 
    padding: 0.5rem 1.25rem; display: flex; flex-direction: column; align-items: center; min-width: 100px;
}
.stat-pill.warning { background: rgba(251, 191, 36, 0.1); border-color: rgba(251, 191, 36, 0.2); color: #fbbf24; }
.stat-pill .label { font-size: 0.6rem; font-weight: 800; opacity: 0.8; text-transform: uppercase; letter-spacing: 1px; color: inherit; }
.stat-pill .value { font-size: 1.15rem; font-weight: 800; line-height: 1.1; color: inherit; }

/* Toolbar Protocol */
.compact-toolbar { display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 2rem; background: #f1f5f9; border-bottom: 1px solid #e2e8f0; }
.period-pills { display: flex; gap: 4px; background: #e2e8f0; padding: 4px; border-radius: 10px; }
.pill-btn { 
    padding: 6px 16px; font-size: 0.85rem; font-weight: 700; border-radius: 8px; color: #64748b; 
    text-decoration: none; transition: 0.2s;
}
.pill-btn:hover { background: #cbd5e1; color: #1e293b; }
.pill-btn.active { background: white; color: #4338ca; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }

.drag-hint { font-size: 0.75rem; font-weight: 700; color: #94a3b8; display: flex; align-items: center; gap: 0.5rem; }

/* Ranking Table Protocol */
.table-container { padding: 1.5rem 2rem; }
.table-wrapper { border-radius: 12px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02); }
.table-compact { width: 100%; border-collapse: collapse; }
.table-compact th { 
    background: #f8fafc; padding: 1rem 1.5rem; text-align: left; font-size: 0.7rem; 
    font-weight: 800; color: #94a3b8; text-transform: uppercase; border-bottom: 2px solid #f1f5f9;
}
.table-compact td { padding: 0.75rem 1.5rem; border-bottom: 1px solid #f8fafc; vertical-align: middle; }
.table-compact tr:last-child td { border-bottom: none; }
.table-compact tr:hover { background: #fdfdfd; }
.rank-highlight { background: #f5f3ff44; }

/* Metallic Orbs */
.rank-orb { 
    width: 36px; height: 36px; border-radius: 10px; display: inline-flex; align-items: center; 
    justify-content: center; font-weight: 800; font-size: 1rem; color: #64748b;
}
.orb-1 { background: linear-gradient(135deg, #fbbf24 0%, #d97706 100%); color: white; font-size: 1.25rem; box-shadow: 0 4px 10px rgba(217, 119, 6, 0.3); }
.orb-2 { background: linear-gradient(135deg, #cbd5e1 0%, #64748b 100%); color: white; font-size: 1.15rem; }
.orb-3 { background: linear-gradient(135deg, #fed7aa 0%, #d97706 100%); color: white; font-size: 1.15rem; }
.silver { color: #f1f5f9; }
.bronze { color: #fff7ed; }

/* User Badges */
.user-avatar-main { 
    width: 40px; height: 40px; border-radius: 12px; background: #e2e8f0; color: #64748b; 
    display: flex; align-items: center; justify-content: center; font-size: 1rem; font-weight: 800;
}
.rank-1-prime { background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); color: white; }

.item-info { display: flex; align-items: center; gap: 1rem; }
.item-title { font-weight: 700; color: #1e293b; font-size: 0.95rem; line-height: 1.2; }
.item-slug { font-size: 0.75rem; color: #94a3b8; font-weight: 600; font-family: monospace; }

/* Results Metrics */
.score-orb.points { background: #eff6ff; color: #1d4ed8; padding: 6px 14px; border-radius: 10px; font-weight: 800; display: inline-block; }

.precision-bundle { display: flex; flex-direction: column; gap: 4px; }
.bundle-top { display: flex; justify-content: space-between; align-items: flex-end; }
.bundle-val { font-weight: 800; color: #1e293b; font-size: 0.9rem; }
.bundle-label { font-size: 0.6rem; font-weight: 800; color: #94a3b8; letter-spacing: 0.5px; }
.bundle-progress { height: 6px; background: #f1f5f9; border-radius: 20px; overflow: hidden; }
.bundle-progress .progress-bar { height: 100%; background: linear-gradient(90deg, #10b981 0%, #34d399 100%); border-radius: 20px; }

.metric-text { font-weight: 700; color: #475569; font-size: 0.9rem; }

/* Trends */
.trend-indicator { display: inline-flex; align-items: center; gap: 4px; font-weight: 800; font-size: 0.8rem; padding: 4px 10px; border-radius: 20px; }
.trend-indicator.up { background: #ecfdf5; color: #10b981; }
.trend-indicator.down { background: #fff1f2; color: #e11d48; }
.trend-neutral { font-size: 0.7rem; font-weight: 800; color: #94a3b8; letter-spacing: 1px; }

/* Actions */
.action-btn-icon { 
    width: 36px; height: 36px; border-radius: 10px; border: 1px solid #e2e8f0; background: white; 
    color: #94a3b8; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: 0.2s;
}
.ban-btn:hover { background: #fff1f2; color: #e11d48; border-color: #fecaca; transform: translateY(-1px); }

/* Empty States */
.empty-state-compact { padding: 4rem; text-align: center; color: #94a3b8; }
.empty-state-compact i { font-size: 3rem; opacity: 0.3; margin-bottom: 1.5rem; }
.empty-state-compact h3 { font-size: 1.25rem; font-weight: 800; color: #64748b; margin-bottom: 0.5rem; }
.empty-state-compact p { font-size: 0.9rem; }
</style>
