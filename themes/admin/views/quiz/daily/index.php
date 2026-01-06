<?php
/**
 * PREMIUM DAILY QUEST SCHEDULER
 * Professional, high-density layout for auto-pilot quiz management.
 */
$calendar = $calendar ?? [];
$scheduledCount = count($calendar);
?>

<div class="admin-wrapper-container">
    <div class="admin-content-wrapper">

        <!-- Compact Page Header -->
        <div class="compact-header">
            <div class="header-left">
                <div class="header-title">
                    <i class="fas fa-calendar-check"></i>
                    <h1>Daily Quest Scheduler</h1>
                </div>
                <div class="header-subtitle">Monitor and manage the Auto-Pilot Quiz System.</div>
            </div>
            
            <div class="header-actions" style="display:flex; gap:10px;">
                <div class="stat-pill">
                    <span class="label">SCHEDULED</span>
                    <span class="value"><?php echo $scheduledCount; ?> Days</span>
                </div>
                <div class="stat-pill success">
                    <span class="label">STREAK</span>
                    <span class="value">Active</span>
                </div>
            </div>
        </div>

        <!-- Action Toolbar -->
        <div class="compact-toolbar">
            <div class="toolbar-left">
                <div class="search-compact">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search schedule..." id="schedule-search" onkeyup="filterSchedule()">
                </div>
            </div>
            <div class="toolbar-right">
                <button class="btn-create-premium" onclick="generateQuizzes()">
                    <i class="fas fa-bolt"></i> GENERATE NEXT 7 DAYS
                </button>
            </div>
        </div>

        <!-- Content Area -->
        <div class="table-container">
            <div class="table-wrapper">
                <table class="table-compact">
                    <thead>
                        <tr>
                            <th style="width: 250px;">Date & Status</th>
                            <th>Scheduled Quizzes</th>
                            <th class="text-center" style="width: 150px;">Total Questions</th>
                            <th class="text-center" style="width: 150px;">Total Rewards</th>
                        </tr>
                    </thead>
                    <tbody id="scheduleList">
                        <?php 
                        $daysToShow = 14;
                        for($i=0; $i<$daysToShow; $i++): 
                            $date = date('Y-m-d', strtotime("+$i days"));
                            $items = $calendar[$date] ?? [];
                            $isToday = ($i == 0);
                            
                            $totalQs = 0;
                            $totalCoins = 0;
                            foreach($items as $q) {
                                $totalQs += count(json_decode($q['questions'], true));
                                $totalCoins += $q['reward_coins'];
                            }
                        ?>
                        <tr class="schedule-item <?= $isToday ? 'today-highlight' : '' ?>">
                            <td class="align-middle">
                                <div class="item-info">
                                    <div class="date-badge <?= $isToday ? 'active' : '' ?>">
                                        <div class="day"><?= date('D', strtotime($date)) ?></div>
                                        <div class="num"><?= date('j', strtotime($date)) ?></div>
                                    </div>
                                    <div class="item-text">
                                        <div class="item-title"><?= date('F Y', strtotime($date)) ?></div>
                                        <?php if($isToday): ?>
                                            <div class="status-tag pulse-active">TODAY</div>
                                        <?php else: ?>
                                            <div class="status-tag">Upcoming</div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td class="align-middle">
                                <?php if(empty($items)): ?>
                                    <div class="empty-inline">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        <span>No quizzes scheduled. <a href="javascript:void(0)" onclick="generateQuizzes()">Auto-generate?</a></span>
                                    </div>
                                <?php else: ?>
                                    <div class="quiz-pill-row">
                                        <?php foreach($items as $quiz): ?>
                                            <div class="quiz-tag">
                                                <i class="fas fa-globe-asia"></i>
                                                <?= $quiz['stream_title'] ? htmlspecialchars($quiz['stream_title']) : 'General' ?>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td class="text-center align-middle">
                                <?php if(!empty($items)): ?>
                                    <span class="metric-badge"><?= $totalQs ?> Questions</span>
                                <?php else: ?>
                                    <span class="metric-dim">--</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center align-middle">
                                <?php if(!empty($items)): ?>
                                    <span class="coin-badge"><i class="fas fa-coins text-warning"></i> <?= $totalCoins ?></span>
                                <?php else: ?>
                                    <span class="metric-dim">0</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endfor; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
async function generateQuizzes() {
    const result = await Swal.fire({
        title: 'Generate Quizzes?',
        text: "This will auto-pilot quizzes for the next 7 days based on available questions.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#667eea',
        cancelButtonColor: '#cbd5e1',
        confirmButtonText: 'Yes, Generate'
    });

    if (result.isConfirmed) {
        Swal.fire({
            title: 'Generating Quests...',
            html: 'Please wait while we sync the question bank...',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        try {
            const response = await fetch('<?= app_base_url('admin/quiz/daily/generate') ?>', { method: 'POST' });
            const data = await response.json();
            
            if(data.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Sync Complete',
                    text: 'Weekly schedule has been refreshed.',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => location.reload());
            } else {
                Swal.fire('Error', data.message, 'error');
            }
        } catch (err) {
            Swal.fire('Error', 'Network or Server Failure', 'error');
        }
    }
}

function filterSchedule() {
    const query = document.getElementById('schedule-search').value.toLowerCase();
    document.querySelectorAll('.schedule-item').forEach(el => {
        const text = el.innerText.toLowerCase();
        el.style.display = text.indexOf(query) > -1 ? '' : 'none';
    });
}
</script>

<style>
/* ========================================
   PREMIUM CORE STYLES (Synchronized)
   ======================================== */
:root {
    --admin-primary: #667eea;
    --admin-secondary: #764ba2;
    --admin-gray-50: #f8f9fa;
    --admin-gray-200: #e5e7eb;
    --admin-gray-600: #4b5563;
    --admin-gray-800: #1f2937;
    --admin-success: #10b981;
}

.admin-wrapper-container { padding: 1rem; background: var(--admin-gray-50); min-height: calc(100vh - 70px); }
.admin-content-wrapper { background: white; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); overflow: hidden; /* padding-bottom: 2rem; REMOVED FOR CLEANER UI */ }

/* Header */
.compact-header {
    display: flex; justify-content: space-between; align-items: center; padding: 1.5rem 2rem;
    background: linear-gradient(135deg, var(--admin-primary) 0%, var(--admin-secondary) 100%);
    color: white;
}
.header-title { display: flex; align-items: center; gap: 0.75rem; }
.header-title h1 { margin: 0; font-size: 1.5rem; font-weight: 700; color: white; }
.header-subtitle { font-size: 0.85rem; opacity: 0.8; margin-top: 4px; font-weight: 500; }

.stat-pill {
    background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.2);
    border-radius: 8px; padding: 0.5rem 1rem; display: flex; flex-direction: column; align-items: center; min-width: 80px;
}
.stat-pill.success { background: rgba(16, 185, 129, 0.15); border-color: rgba(16, 185, 129, 0.3); }
.stat-pill .label { font-size: 0.65rem; font-weight: 700; letter-spacing: 0.5px; opacity: 0.9; }
.stat-pill .value { font-size: 1.1rem; font-weight: 800; line-height: 1.1; }

/* Toolbar */
.compact-toolbar {
    display: flex; justify-content: space-between; align-items: center;
    padding: 1rem 2rem; background: #f8fafc; border-bottom: 1px solid var(--admin-gray-200);
}
.search-compact { position: relative; width: 300px; }
.search-compact i { position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 0.85rem; }
.search-compact input {
    width: 100%; height: 38px; padding: 0 0.75rem 0 2.25rem; font-size: 0.875rem;
    border: 1px solid #cbd5e1; border-radius: 8px; outline: none; transition: all 0.2s;
}
.btn-create-premium {
    height: 40px; padding: 0 1.25rem; background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);
    color: white; font-weight: 600; font-size: 0.85rem; border: none; border-radius: 8px; cursor: pointer;
    display: flex; align-items: center; gap: 0.5rem; box-shadow: 0 2px 4px rgba(79, 70, 229, 0.2);
}

/* Table Design */
.table-compact { width: 100%; border-collapse: collapse; }
.table-compact th {
    background: white; padding: 0.75rem 1.5rem; text-align: left; font-weight: 600;
    color: #94a3b8; text-transform: uppercase; font-size: 0.7rem; letter-spacing: 0.5px; border-bottom: 1px solid #e2e8f0;
}
.table-compact td { padding: 0.75rem 1.5rem; border-bottom: 1px solid #f1f5f9; }
.schedule-item:hover { background: #f8fafc; }
.today-highlight { background: #f0f7ff !important; border-left: 4px solid var(--admin-primary); }

/* Date Badge */
.date-badge {
    width: 50px; height: 50px; border-radius: 10px; background: #f1f5f9; border: 1px solid #e2e8f0;
    display: flex; flex-direction: column; align-items: center; justify-content: center;
}
.date-badge.active { background: var(--admin-primary); color: white; border-color: var(--admin-primary); }
.date-badge .day { font-size: 0.65rem; font-weight: 700; text-transform: uppercase; line-height: 1; }
.date-badge .num { font-size: 1.1rem; font-weight: 800; line-height: 1; margin-top: 2px; }

.item-info { display: flex; align-items: center; gap: 1rem; }
.item-title { font-weight: 700; color: #334155; font-size: 0.9rem; }
.status-tag { font-size: 0.65rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; }
.pulse-active { color: var(--admin-primary); display: flex; align-items: center; gap: 4px; }
.pulse-active::before { content: ""; width: 6px; height: 6px; background: var(--admin-primary); border-radius: 50%; display: block; animation: pulse 1.5s infinite; }

/* Quiz Pills */
.quiz-pill-row { display: flex; flex-wrap: wrap; gap: 0.5rem; }
.quiz-tag {
    padding: 0.25rem 0.75rem; background: white; border: 1px solid #e2e8f0; border-radius: 20px;
    font-size: 0.75rem; font-weight: 600; color: #4b5563; display: flex; align-items: center; gap: 0.4rem;
}
.empty-inline { font-size: 0.8rem; color: #94a3b8; display: flex; align-items: center; gap: 0.5rem; }
.empty-inline a { color: var(--admin-primary); font-weight: 600; text-decoration: none; }

.metric-badge { font-size: 0.75rem; font-weight: 700; color: #334155; padding: 4px 10px; background: #f1f5f9; border-radius: 6px; }
.coin-badge { font-size: 0.85rem; font-weight: 800; color: #d97706; }
.metric-dim { color: #cbd5e1; font-weight: 700; }

@keyframes pulse {
    0% { transform: scale(1); opacity: 1; }
    50% { transform: scale(1.5); opacity: 0.5; }
    100% { transform: scale(1); opacity: 1; }
}
</style>
