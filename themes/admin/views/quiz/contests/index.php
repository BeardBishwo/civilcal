<?php
/**
 * PREMIUM CONTEST ENGINE DASHBOARD
 * Professional, high-density layout for Battle Royale management.
 */
$contests = $contests ?? [];
$autoManager = $autoManager ?? false;
?>

<div class="admin-wrapper-container">
    <div class="admin-content-wrapper">

        <!-- Compact Page Header -->
        <div class="compact-header">
            <div class="header-left">
                <div class="header-title">
                    <i class="fas fa-trophy"></i>
                    <h1>Contest Engine</h1>
                </div>
                <div class="header-subtitle">Battle Royale Events & Anti-Cheat Distribution Hub</div>
            </div>
            
            <div class="header-actions" style="display:flex; gap:10px;">
                <div class="ai-status-pill <?php echo $autoManager ? 'active' : ''; ?>">
                    <div class="pill-info">
                        <span class="label">AI AUTO-PILOT</span>
                        <span class="value"><?php echo $autoManager ? 'OPERATIONAL' : 'INACTIVE'; ?></span>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="aiToggle" <?php echo $autoManager ? 'checked' : ''; ?>>
                    </div>
                </div>
                <div class="stat-pill warning">
                    <span class="label">LIVE EVENTS</span>
                    <span class="value"><?php 
                        $liveCount = 0;
                        foreach($contests as $c) if($c['status'] == 'live') $liveCount++;
                        echo $liveCount;
                    ?></span>
                </div>
            </div>
        </div>

        <!-- Integration Toolbar (Creation Row) -->
        <div class="creation-toolbar">
            <h5 class="toolbar-title">Commission New Event</h5>
            <form id="contestForm" class="creation-form">
                
                <div class="input-group-premium" style="flex: 2; min-width: 200px;">
                    <i class="fas fa-bullseye icon"></i>
                    <input type="text" name="title" class="form-input-premium" placeholder="Contest Designation (e.g. Mega Weekend)" required>
                </div>
                
                <div class="input-group-premium" style="flex: 1.5; min-width: 180px;">
                    <i class="fas fa-calendar-alt icon"></i>
                    <input type="datetime-local" name="start_time" class="form-input-premium" required>
                </div>

                <div class="input-group-premium" style="flex: 1; min-width: 100px;">
                    <i class="fas fa-coins icon"></i>
                    <input type="number" name="entry_fee" class="form-input-premium" placeholder="Fee" value="10">
                </div>

                <div class="input-group-premium" style="flex: 1; min-width: 100px;">
                    <i class="fas fa-crown icon"></i>
                    <input type="number" name="prize_pool" class="form-input-premium" placeholder="Prize" value="500">
                </div>

                <button type="submit" class="btn-create-premium">
                    <i class="fas fa-rocket"></i> LAUNCH
                </button>
            </form>
        </div>

        <!-- Secondary Filter Toolbar -->
        <div class="compact-toolbar">
            <div class="toolbar-left">
                <div class="search-compact">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search events..." id="contest-search">
                </div>
            </div>
            <div class="toolbar-right">
                <div class="drag-hint">
                    <i class="fas fa-shield-alt"></i> Anti-Cheat Algorithm: <span class="text-success">ACTIVE</span>
                </div>
            </div>
        </div>

        <!-- Contest Feed Area -->
        <div class="table-container">
            <div class="table-wrapper">
                <table class="table-compact">
                    <thead>
                        <tr>
                            <th>Mission Designation</th>
                            <th class="text-center">Protocol Status</th>
                            <th class="text-center">Tactical Info</th>
                            <th class="text-center">Prize Payload</th>
                            <th class="text-center">Standing</th>
                            <th class="text-center">Engagement</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($contests)): ?>
                            <tr>
                                <td colspan="6">
                                    <div class="empty-state-compact">
                                        <i class="fas fa-calendar-times"></i>
                                        <h3>No events scheduled</h3>
                                        <p>Commission your first contest using the toolbar above.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($contests as $contest): ?>
                                <tr class="event-item">
                                    <td>
                                        <div class="item-info">
                                            <div class="item-icon-orb <?php echo $contest['status']; ?>">
                                                <i class="fas fa-microchip"></i>
                                            </div>
                                            <div class="item-text">
                                                <div class="item-title"><?php echo htmlspecialchars($contest['title']); ?></div>
                                                <div class="item-subtitle">
                                                    <i class="far fa-clock"></i> Deployment: <?php echo date('M d, H:i', strtotime($contest['start_time'])); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">
                                        <?php if($contest['status'] == 'live'): ?>
                                            <span class="status-pill-premium active">
                                                <span class="pulse"></span> LIVE
                                            </span>
                                        <?php elseif($contest['status'] == 'upcoming'): ?>
                                            <span class="status-pill-premium scheduled">SCHEDULED</span>
                                        <?php else: ?>
                                            <span class="status-pill-premium archival">FINISHED</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center align-middle">
                                        <div class="tactical-bundle">
                                            <div class="t-label">ENROLLMENT</div>
                                            <div class="t-value"><?php echo $contest['entry_fee']; ?> <span class="small">COINS</span></div>
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">
                                        <div class="prize-bundle">
                                            <div class="p-value"><?php echo number_format($contest['prize_pool']); ?></div>
                                            <div class="p-label"><i class="fas fa-coins text-warning"></i> TOTAL POOL</div>
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">
                                        <span class="standing-tag"><?php echo $contest['winner_count']; ?> WINNERS</span>
                                    </td>
                                    <td class="text-center align-middle">
                                        <div class="actions-compact justify-center">
                                            <?php if($contest['status'] !== 'ended'): ?>
                                                <button onclick="processContest(<?php echo $contest['id']; ?>)" class="action-btn-icon judge-btn" title="Judge Event">
                                                    <i class="fas fa-gavel"></i>
                                                </button>
                                            <?php endif; ?>
                                            <button class="action-btn-icon config-btn" title="Config">
                                                <i class="fas fa-cog"></i>
                                            </button>
                                            <button class="action-btn-icon delete-btn" title="Abort">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
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

<script>
document.getElementById('aiToggle').addEventListener('change', async function() {
    const status = this.checked ? '1' : '0';
    try {
        const response = await fetch('<?php echo app_base_url('admin/contest/toggle-auto'); ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'status=' + status
        });
        const d = await response.json();
        if(d.success) {
            Swal.fire({
                icon: 'success', title: 'AI Protocol Update', text: d.message,
                toast: true, position: 'top-end', showConfirmButton: false, timer: 2000
            }).then(() => location.reload());
        }
    } catch(e) { Swal.fire('Error', 'Communication Failure', 'error'); }
});

document.getElementById('contestForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    try {
        const response = await fetch('<?php echo app_base_url('admin/contest/store'); ?>', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: new URLSearchParams(formData)
        });
        const d = await response.json();
        if(d.success) {
            Swal.fire({ icon: 'success', title: 'Mission Launched', text: d.message }).then(() => location.reload());
        } else {
            Swal.fire('Launch Aborted', d.error, 'error');
        }
    } catch(e) { Swal.fire('Error', 'Launch Sequence Failure', 'error'); }
});

function processContest(id) {
    Swal.fire({
        title: 'Initiate Lucky Draw?',
        text: 'Anti-cheat algorithm will analyze top scorers and distribute prize payloads.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#4f46e5',
        confirmButtonText: 'ENGAGE JUDGEMENT'
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                const response = await fetch('<?php echo app_base_url('admin/contest/process/'); ?>' + id, { method: 'POST' });
                const d = await response.json();
                if(d.success) {
                    Swal.fire('Mission Complete', d.message, 'success').then(() => location.reload());
                } else {
                    Swal.fire('Operation Failure', d.error, 'error');
                }
            } catch(e) { Swal.fire('Error', 'Network Interference Deteced', 'error'); }
        }
    });
}
</script>

<style>
/* ========================================
   PREMIUM CONTEST ENGINE STYLES
   ======================================== */
:root {
    --arch-primary: #4f46e5;
    --arch-secondary: #0ea5e9;
    --arch-dark: #1e1b4b;
    --arch-success: #10b981;
}

.admin-wrapper-container { padding: 1rem; background: #f1f5f9; min-height: calc(100vh - 70px); }
.admin-content-wrapper { background: white; border-radius: 16px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); overflow: hidden; padding-bottom: 2rem; }

/* Font Branding */
@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap');
.admin-wrapper-container *:not(i):not([class*="fa-"]) { font-family: 'Outfit', sans-serif; }

/* Header Architect */
.compact-header { 
    display: flex; justify-content: space-between; align-items: center; padding: 1.5rem 2rem; 
    background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%); color: white; border-bottom: 4px solid #4f46e5;
}
.header-title { display: flex; align-items: center; gap: 0.75rem; }
.header-title h1 { margin: 0; font-size: 1.5rem; font-weight: 800; color: white; letter-spacing: -0.5px; }
.header-subtitle { font-size: 0.8rem; opacity: 0.7; margin-top: 4px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; }

.ai-status-pill { 
    background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; 
    padding: 0.5rem 1rem; display: flex; align-items: center; gap: 1rem; transition: 0.3s;
}
.ai-status-pill.active { background: rgba(16, 185, 129, 0.1); border-color: rgba(16, 185, 129, 0.2); }
.pill-info { display: flex; flex-direction: column; align-items: flex-end; }
.pill-info .label { font-size: 0.6rem; font-weight: 800; opacity: 0.7; color: white; }
.pill-info .value { font-size: 0.9rem; font-weight: 800; color: #10b981; }

.stat-pill { 
    background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.15); border-radius: 12px; 
    padding: 0.5rem 1.25rem; display: flex; flex-direction: column; align-items: center; min-width: 90px;
}
.stat-pill.warning { background: rgba(251, 191, 36, 0.1); border-color: rgba(251, 191, 36, 0.2); color: #fbbf24; }
.stat-pill .label { font-size: 0.6rem; font-weight: 800; opacity: 0.8; text-transform: uppercase; }
.stat-pill .value { font-size: 1.2rem; font-weight: 800; line-height: 1; }

/* Creation Row */
.creation-toolbar { padding: 1.25rem 2rem; background: #f8fafc; border-bottom: 1px solid #e2e8f0; }
.toolbar-title { font-size: 0.7rem; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 0.75rem; }
.creation-form { display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap; }

.input-group-premium { position: relative; }
.input-group-premium .icon { position: absolute; left: 0.85rem; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 0.9rem; pointer-events: none; }
.form-input-premium {
    width: 100%; height: 42px; padding: 0 0.75rem 0 2.5rem; font-size: 0.9rem; border: 1px solid #cbd5e1;
    border-radius: 10px; outline: none; transition: 0.2s; background: white; font-weight: 500;
}
.form-input-premium:focus { border-color: #4f46e5; box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1); }

.btn-create-premium {
    height: 42px; padding: 0 1.5rem; background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);
    color: white; font-weight: 700; font-size: 0.9rem; border: none; border-radius: 10px; cursor: pointer;
    display: flex; align-items: center; gap: 0.6rem; transition: 0.3s; box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.3);
}
.btn-create-premium:hover { transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.4); }

/* Compact Toolbar */
.compact-toolbar { display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 2rem; background: #f1f5f9; border-bottom: 1px solid #e2e8f0; }
.search-compact { position: relative; width: 100%; max-width: 250px; }
.search-compact i { position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 0.8rem; }
.search-compact input {
    width: 100%; height: 34px; padding: 0 0.75rem 0 2.25rem; font-size: 0.8rem;
    border: 1px solid #cbd5e1; border-radius: 8px; outline: none; background: white; font-weight: 600;
}
.drag-hint { font-size: 0.75rem; font-weight: 700; color: #64748b; display: flex; align-items: center; gap: 0.4rem; }

/* Table Protocol */
.table-container { padding: 1.5rem 2rem; }
.table-wrapper { border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02); }
.table-compact { width: 100%; border-collapse: collapse; }
.table-compact th { 
    background: #f8fafc; padding: 0.75rem 1.5rem; text-align: left; font-size: 0.7rem; 
    font-weight: 800; color: #94a3b8; text-transform: uppercase; border-bottom: 2px solid #f1f5f9;
}
.table-compact td { padding: 0.75rem 1.5rem; border-bottom: 1px solid #f8fafc; vertical-align: middle; }
.event-item:hover { background: #f8fafc; }

/* Item Design */
.item-info { display: flex; align-items: center; gap: 1rem; }
.item-icon-orb { 
    width: 42px; height: 42px; border-radius: 12px; display: flex; align-items: center; 
    justify-content: center; font-size: 1.1rem; border: 1px solid #e2e8f0;
}
.item-icon-orb.live { background: #ecfdf5; color: #10b981; border-color: #d1fae5; }
.item-icon-orb.upcoming { background: #eff6ff; color: #3b82f6; border-color: #dbeafe; }
.item-icon-orb.ended { background: #f8fafc; color: #94a3b8; border-color: #e2e8f0; }

.item-title { font-weight: 700; color: #1e293b; font-size: 0.95rem; line-height: 1.2; }
.item-subtitle { font-size: 0.75rem; color: #94a3b8; margin-top: 2px; font-weight: 600; }

/* Status Pills */
.status-pill-premium { 
    padding: 6px 14px; border-radius: 20px; font-size: 0.65rem; font-weight: 800; 
    display: inline-flex; align-items: center; gap: 6px; letter-spacing: 0.5px;
}
.status-pill-premium.active { background: #ecfdf5; color: #10b981; border: 1px solid #d1fae5; }
.status-pill-premium.scheduled { background: #eff6ff; color: #3b82f6; border: 1px solid #dbeafe; }
.status-pill-premium.archival { background: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0; }

.pulse { width: 6px; height: 6px; background: #10b981; border-radius: 50%; display: inline-block; animation: pulse-ring 1.5s infinite; }
@keyframes pulse-ring { 0% { transform: scale(0.8); opacity: 1; } 100% { transform: scale(2.5); opacity: 0; } }

/* Bundles */
.tactical-bundle { text-align: center; }
.tactical-bundle .t-label { font-size: 0.6rem; font-weight: 800; color: #94a3b8; }
.tactical-bundle .t-value { font-size: 0.9rem; font-weight: 800; color: #475569; }
.tactical-bundle .small { font-size: 0.6rem; opacity: 0.7; }

.prize-bundle { text-align: center; background: #fffbeb; padding: 6px 14px; border-radius: 10px; border: 1px solid #fef3c7; }
.prize-bundle .p-value { font-size: 1rem; font-weight: 800; color: #d97706; }
.prize-bundle .p-label { font-size: 0.6rem; font-weight: 800; color: #b45309; }

.standing-tag { font-size: 0.75rem; font-weight: 800; color: #6366f1; background: #eef2ff; padding: 4px 12px; border-radius: 20px; }

/* Actions */
.actions-compact { display: flex; gap: 6px; justify-content: center; }
.action-btn-icon { 
    width: 34px; height: 34px; border-radius: 8px; border: 1px solid #e2e8f0; background: white; 
    color: #94a3b8; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: 0.2s;
}
.action-btn-icon:hover { transform: translateY(-1px); background: #f8fafc; color: #4f46e5; border-color: #4f46e5; }
.judge-btn:hover { background: #fffbeb; color: #d97706; border-color: #fbbf24; }
.delete-btn:hover { background: #fff1f2; color: #e11d48; border-color: #fecaca; }

/* Empty State */
.empty-state-compact { padding: 4rem; text-align: center; color: #94a3b8; }
.empty-state-compact i { font-size: 3rem; opacity: 0.2; margin-bottom: 1rem; }
.empty-state-compact h3 { font-size: 1.1rem; font-weight: 800; color: #64748b; margin-bottom: 0.5rem; }
</style>
