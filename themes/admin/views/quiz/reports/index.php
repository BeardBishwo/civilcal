<?php

/**
 * PREMIUM REPORT MANAGER
 * Matches Question Bank Design
 */
$reports = $reports ?? [];
$stats = $stats ?? ['total' => 0];
?>

<div class="admin-wrapper-container">
    <div class="admin-content-wrapper">

        <!-- Compact Page Header -->
        <div class="compact-header" style="background: linear-gradient(135deg, #e74a3b 0%, #be2617 100%);">
            <div class="header-left">
                <div class="header-title">
                    <i class="fas fa-bug"></i>
                    <h1>Issue Reports</h1>
                </div>
                <div class="header-subtitle">
                    User reported issues requiring attention
                </div>
            </div>
            <div class="header-actions" style="display:flex; gap:10px;">
                <div class="stat-pill warning">
                    <span class="label">PENDING</span>
                    <span class="value"><?php echo number_format($stats['total']); ?></span>
                </div>
            </div>
        </div>

        <!-- Toolbar -->
        <div class="compact-toolbar">
            <div class="toolbar-left">
                <!-- Filters could go here -->
                <span class="filter-label" style="color:#b91c1c;">FILTERS:</span>
                <span class="text-xs text-slate-500 font-medium ml-2">Displaying pending reports</span>
            </div>
            <div class="toolbar-right" style="display: flex; align-items: center; gap: 0.75rem;">
                <div class="search-compact">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search reports..." id="report-search" onkeyup="filterReports()">
                </div>
                <div class="action-buttons-compact">
                    <button class="action-btn-premium" onclick="location.reload()" title="Refresh">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Content Area -->
        <div class="table-container">
            <div class="table-wrapper">
                <table class="table-compact">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 60px;">ID</th>
                            <th style="width: 150px;">Category</th>
                            <th style="width: 120px;">Reporter</th>
                            <th style="width: 120px;">Issue Type</th>
                            <th style="min-width: 250px;">Question / Description</th>
                            <th class="text-center" style="width: 120px;">Q. Type</th>
                            <th class="text-center" style="width: 150px;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="reportList">
                        <?php if (empty($reports)): ?>
                            <tr>
                                <td colspan="7" class="empty-cell">
                                    <div class="empty-state-compact">
                                        <i class="fas fa-check-circle" style="color: #10b981;"></i>
                                        <h3 style="color: #059669;">All Clear!</h3>
                                        <p>No pending issue reports. Your content is clean.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($reports as $r):
                                $qContent = $r['content'] ?? [];
                                $qText = strip_tags($qContent['text'] ?? ($qContent['question'] ?? ''));
                                if (strlen($qText) > 80) $qText = substr($qText, 0, 80) . '...';
                            ?>
                                <tr class="report-item" data-id="<?php echo $r['question_id']; ?>">
                                    <!-- ID (Grouped by QID) -->
                                    <td class="text-center align-middle">
                                        <span class="text-xs font-bold text-slate-400">#<?php echo $r['question_id']; ?></span>
                                        <?php if ($r['report_count'] > 1): ?>
                                            <div class="mt-1 badge-pill" style="background:#fee2e2; color:#b91c1c; font-size:0.65rem;">
                                                +<?php echo $r['report_count'] - 1; ?> MORE
                                            </div>
                                        <?php endif; ?>
                                    </td>

                                    <!-- Category -->
                                    <td class="align-middle">
                                        <div class="text-sm font-medium text-slate-700"><?php echo htmlspecialchars($r['course_title']); ?></div>
                                        <div class="text-xs text-slate-500"><?php echo htmlspecialchars($r['category_title']); ?></div>
                                    </td>

                                    <!-- Reporters (Grouped) -->
                                    <td class="align-middle">
                                        <?php
                                        $reporters = explode(',', $r['reporters']);
                                        $firstReporter = trim($reporters[0] ?? 'Unknown');
                                        $count = count($reporters);
                                        ?>
                                        <div class="flex items-center gap-2">
                                            <div class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center text-xs font-bold text-slate-600 border-2 border-white shadow-sm" title="First Reporter: <?php echo $firstReporter; ?>">
                                                <?php echo strtoupper(substr($firstReporter, 0, 1)); ?>
                                            </div>
                                            <div>
                                                <div class="flex items-center gap-2">
                                                    <span class="text-sm font-medium leading-tight">
                                                        <?php echo htmlspecialchars($r['first_reporter']); ?>
                                                    </span>
                                                    <?php if ($t = $r['first_reporter_trust']): ?>
                                                        <span class="px-1.5 py-0.5 rounded <?php echo $t['class']; ?> font-bold text-[9px] uppercase">
                                                            <?php echo $t['status']; ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                                <?php if ($count > 1): ?>
                                                    <a href="javascript:void(0)" onclick="viewReporters(<?php echo $r['question_id']; ?>)" class="text-xs text-indigo-600 font-bold hover:underline">
                                                        +<?php echo $count - 1; ?> others reported
                                                    </a>
                                                <?php else: ?>
                                                    <div class="text-xs text-slate-400">
                                                        <?php echo date('M d, H:i', strtotime($r['created_at'])); ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Issue Type -->
                                    <td class="align-middle">
                                        <span class="badge-pill" style="background:#fee2e2; color:#b91c1c; border-color:#fca5a5;">
                                            <?php echo strtoupper($r['issue_type'] ?? 'General'); ?>
                                        </span>
                                    </td>

                                    <!-- Descriptions / Question -->
                                    <td>
                                        <div class="descriptions-stack mb-2">
                                            <?php foreach ($r['descriptions'] as $desc): ?>
                                                <div class="p-2 bg-red-50 rounded border border-red-100 mb-1 text-xs text-red-800 italic">
                                                    "<?php echo htmlspecialchars($desc); ?>"
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        <div class="text-sm text-slate-600 flex items-center gap-2">
                                            <span class="text-xs font-bold text-slate-400">Q#<?php echo $r['question_id']; ?>:</span>
                                            <?php echo htmlspecialchars($qText); ?>
                                        </div>
                                    </td>

                                    <!-- Q. Type -->
                                    <td class="text-center align-middle">
                                        <span class="text-xs font-bold text-slate-500 bg-slate-100 px-2 py-1 rounded">
                                            <?php echo $r['q_type']; ?>
                                        </span>
                                    </td>

                                    <!-- Actions -->
                                    <td class="text-center align-middle">
                                        <div class="actions-compact justify-center">
                                            <a href="<?php echo app_base_url('admin/quiz/questions/edit/' . $r['question_id']); ?>" target="_blank" class="action-btn-icon edit-btn" title="Edit Question">
                                                <i class="fas fa-external-link-alt"></i>
                                            </a>
                                            <button onclick="resolveReport(<?php echo $r['question_id']; ?>, <?php echo $r['report_count']; ?>)" class="action-btn-icon text-emerald-600 bg-emerald-50 hover:bg-emerald-100" title="Resolve All">
                                                <i class="fas fa-check-double"></i>
                                            </button>
                                            <button onclick="ignoreReport(<?php echo $r['question_id']; ?>)" class="action-btn-icon text-slate-500 bg-slate-50 hover:bg-slate-100" title="Dismiss All">
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

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Pass PHP variables to JS
    const defaultMsgFirst = <?php echo json_encode($defaults['msg_first'] ?? ''); ?>;

    // Help: View Screenshot (Phase 8)
    function viewScreenshot(url) {
        Swal.fire({
            title: 'Visual Evidence',
            imageUrl: url,
            imageAlt: 'Evidence Photo',
            width: 'auto',
            confirmButtonText: 'Great, I saw it',
            confirmButtonColor: '#6366f1',
            customClass: {
                image: 'rounded-2xl shadow-2xl border border-slate-200 p-2 bg-white max-h-[80vh] w-auto'
            }
        });
    }

    function resolveReport(id, count) {
        let title = 'Resolve Issue?';
        let btnText = 'Yes, Resolved';
        let htmlText = "This will mark reports as resolved and reward users.";

        if (count > 1) {
            title = `Resolve All ${count} Reports?`;
            btnText = `Yes, Resolve All (${count})`;
            htmlText = `<b>${count} users</b> will be notified and rewarded.`;
        }

        Swal.fire({
            title: title,
            html: htmlText,
            icon: 'question',
            input: 'textarea',
            inputLabel: 'Message to First Reporter',
            inputValue: defaultMsgFirst, // Pre-fill with default
            inputPlaceholder: 'Type your custom thank you message...',
            inputAttributes: {
                'aria-label': 'Type your message here'
            },
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            confirmButtonText: btnText,
            footer: '<span class="text-xs text-slate-400">Others will receive the standard "Consolation" message.</span>'
        }).then(async (result) => {
            if (result.isConfirmed) {
                postAction('<?php echo app_base_url('admin/quiz/report/resolve'); ?>', id, result.value);
            }
        });
    }

    function ignoreReport(id) {
        Swal.fire({
            title: 'Dismiss Report?',
            text: "Ignore this report without changes.",
            icon: 'warning',
            input: 'textarea',
            inputLabel: 'Reason for Rejection (Optional)',
            inputPlaceholder: 'e.g., "The calculation assumes standard pressure..."',
            showCancelButton: true,
            confirmButtonColor: '#64748b',
            confirmButtonText: 'Dismiss',
            footer: '<span class="text-xs text-slate-400">If you provide a reason, the user will be notified.</span>'
        }).then(async (result) => {
            if (result.isConfirmed) {
                postAction('<?php echo app_base_url('admin/quiz/report/ignore'); ?>', id, result.value);
            }
        });
    }

    async function postAction(url, id, message = '') {
        const formData = new FormData();
        formData.append('id', id);
        if (message) {
            formData.append('reply_message', message);
        }

        try {
            const res = await fetch(url, {
                method: 'POST',
                body: formData
            });
            const d = await res.json();

            // Assuming simplified response or standard json
            Swal.fire({
                icon: 'success',
                title: 'Success',
                timer: 1000,
                showConfirmButton: false
            }).then(() => {
                // Animate removal
                const row = document.querySelector(`tr[data-id="${id}"]`);
                if (row) row.remove();

                // If empty, reload to show empty state
                if (document.querySelectorAll('.report-item').length === 0) location.reload();
            });

        } catch (e) {
            Swal.fire('Error', 'Action failed', 'error');
        }
    }

    async function viewReporters(questionId) {
        Swal.fire({
            title: 'Reporter Details',
            html: '<div id="reporters-modal-loading" class="p-4 text-center"><i class="fas fa-circle-notch fa-spin text-2xl text-indigo-600"></i><p class="text-sm text-slate-500 mt-2">Loading details...</p></div>',
            width: '700px',
            showConfirmButton: true,
            confirmButtonText: 'Close',
            customClass: {
                htmlContainer: 'p-0 text-left'
            },
            didOpen: async () => {
                try {
                    const res = await fetch(`<?php echo app_base_url('admin/quiz/report/getReporters'); ?>?id=${questionId}`);
                    const data = await res.json();

                    let html = '<div class="p-4"><table class="w-full text-xs text-left">';
                    html += '<thead class="bg-slate-50 text-slate-500 uppercase font-bold border-b"><tr><th class="p-2">User / Trust</th><th class="p-2">Time</th><th class="p-2">Description</th><th class="p-2 text-center">Action</th></tr></thead>';
                    html += '<tbody>';

                    data.forEach((r, index) => {
                        const date = new Date(r.created_at).toLocaleString();
                        const isFirst = index === 0;
                        const rowClass = isFirst ? 'bg-indigo-50/50' : '';
                        const badgeFirst = isFirst ? '<span class="ml-1 px-1 bg-indigo-600 text-white rounded text-[8px]">FIRST</span>' : '';

                        // Trust Badge
                        const t = r.trust || {
                            status: 'N/A',
                            class: 'bg-slate-100 text-slate-400',
                            score: ''
                        };
                        const trustLabel = `<span class="px-1.5 py-0.5 rounded ${t.class} font-bold text-[9px] uppercase">${t.status} ${t.score}</span>`;

                        html += `<tr class="border-b ${rowClass}" id="report-row-${r.id}">
                            <td class="p-2 font-bold">
                                ${r.username}${badgeFirst}<br>
                                <span class="text-[10px] text-slate-400 font-normal">${r.email || ''}</span><br>
                                ${trustLabel}
                            </td>
                            <td class="p-2 text-slate-500">${date}</td>
                            <td class="p-2 italic text-slate-600">"${r.description}"</td>
                            <td class="p-2 text-center">
                                <div class="flex gap-1 justify-center">
                                    ${r.screenshot ? `
                                        <button onclick="viewScreenshot('${window.appConfig.baseUrl}/${r.screenshot}')" 
                                            class="w-7 h-7 flex items-center justify-center rounded bg-indigo-50 text-indigo-600 hover:bg-indigo-100" 
                                            title="View Evidence">
                                            <i class="fas fa-camera"></i>
                                        </button>
                                    ` : ''}
                                    <button onclick="resolveSingleReport(${r.id}, '${r.username}')" class="w-7 h-7 flex items-center justify-center rounded bg-emerald-50 text-emerald-600 hover:bg-emerald-100" title="Resolve This Only">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button onclick="ignoreSingleReport(${r.id}, '${r.username}')" class="w-7 h-7 flex items-center justify-center rounded bg-red-50 text-red-600 hover:bg-red-100" title="Dismiss This Only">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>`;
                    });

                    html += '</tbody></table></div>';
                    Swal.getHtmlContainer().innerHTML = html;
                } catch (e) {
                    Swal.getHtmlContainer().innerHTML = '<div class="p-4 text-red-500 text-center">Failed to load reporter details.</div>';
                }
            }
        });
    }

    function resolveSingleReport(reportId, username) {
        Swal.fire({
            title: `Resolve for ${username}?`,
            text: "This will reward only this specific user.",
            icon: 'question',
            input: 'textarea',
            inputLabel: 'Personal Message',
            inputValue: defaultMsgFirst,
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            confirmButtonText: 'Resolve Single'
        }).then(async (result) => {
            if (result.isConfirmed) {
                postSingleAction('<?php echo app_base_url('admin/quiz/report/resolve'); ?>', reportId, result.value);
            }
        });
    }

    function ignoreSingleReport(reportId, username) {
        Swal.fire({
            title: `Dismiss ${username}'s Report?`,
            icon: 'warning',
            input: 'textarea',
            inputLabel: 'Reason (Optional)',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'Dismiss'
        }).then(async (result) => {
            if (result.isConfirmed) {
                postSingleAction('<?php echo app_base_url('admin/quiz/report/ignore'); ?>', reportId, result.value);
            }
        });
    }

    async function postSingleAction(url, reportId, message = '') {
        const formData = new FormData();
        formData.append('report_id', reportId); // NOTE: report_id instead of id/question_id
        if (message) formData.append('reply_message', message);

        try {
            const res = await fetch(url, {
                method: 'POST',
                body: formData
            });
            const d = await res.json();

            if (d.status === 'success') {
                const row = document.getElementById(`report-row-${reportId}`);
                if (row) {
                    row.style.opacity = '0.5';
                    row.style.pointerEvents = 'none';
                    row.classList.add('bg-slate-50');
                    row.querySelector('.p-2.text-center').innerHTML = '<span class="text-xs font-bold text-slate-400">DONE</span>';
                }

                // If it was the last one in the main table group, we might want to refresh, 
                // but for now, individual rows inside modal is fine.
            } else {
                Swal.fire('Error', d.message || 'Action failed', 'error');
            }
        } catch (e) {
            Swal.fire('Error', 'Network error', 'error');
        }
    }

    function filterReports() {
        const query = document.getElementById('report-search').value.toLowerCase();
        document.querySelectorAll('.report-item').forEach(el => {
            const text = el.innerText.toLowerCase();
            el.style.display = text.indexOf(query) > -1 ? '' : 'none';
        });
    }
</script>

<style>
    /* Reuse Question Bank Styles */
    :root {
        --admin-primary: #667eea;
        --admin-secondary: #764ba2;
        --admin-gray-50: #f8f9fa;
    }

    .admin-wrapper-container {
        padding: 1rem;
        background: var(--admin-gray-50);
        min-height: calc(100vh - 70px);
    }

    .admin-content-wrapper {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    /* Headers */
    .compact-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.5rem 2rem;
        color: white;
    }

    .header-title {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .header-title h1 {
        margin: 0;
        font-size: 1.5rem;
        font-weight: 700;
        color: white;
    }

    .header-title i {
        font-size: 1.25rem;
        opacity: 0.9;
    }

    .header-subtitle {
        font-size: 0.85rem;
        opacity: 0.8;
        margin-top: 4px;
        font-weight: 500;
    }

    .stat-pill {
        background: rgba(255, 255, 255, 0.15);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 8px;
        padding: 0.5rem 1rem;
        min-width: 80px;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .stat-pill .label {
        font-size: 0.65rem;
        font-weight: 700;
    }

    .stat-pill .value {
        font-size: 1.1rem;
        font-weight: 800;
    }

    /* Filter Bar */
    .compact-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem 2rem;
        background: #fef2f2;
        /* Light Red tint for Reports */
        border-bottom: 1px solid #fecaca;
    }

    .filter-label {
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: 0.5px;
    }

    .search-compact {
        position: relative;
    }

    .search-compact input {
        padding-left: 2rem;
        padding-right: 1rem;
        height: 36px;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        font-size: 0.85rem;
        width: 250px;
    }

    .search-compact i {
        position: absolute;
        left: 0.75rem;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: 0.8rem;
    }

    /* Table */
    .table-container {
        padding: 0;
    }

    .table-compact {
        width: 100%;
        border-collapse: collapse;
    }

    .table-compact th {
        background: #f8fafc;
        padding: 0.75rem 1rem;
        text-align: left;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #64748b;
        border-bottom: 1px solid #e2e8f0;
    }

    .table-compact td {
        padding: 0.75rem 1rem;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.9rem;
        color: #334155;
    }

    .table-compact tr:hover td {
        background: #f8fafc;
    }

    .badge-pill {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.7rem;
        font-weight: 700;
        border: 1px solid transparent;
    }

    .action-btn-premium {
        width: 32px;
        height: 32px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: white;
        border: 1px solid #e2e8f0;
        color: #64748b;
        cursor: pointer;
        transition: 0.2s;
    }

    .action-btn-premium:hover {
        border-color: #cbd5e1;
        background: #f8fafc;
    }

    .actions-compact {
        display: flex;
        gap: 6px;
    }

    .action-btn-icon {
        width: 28px;
        height: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 4px;
        border: 1px solid transparent;
        text-decoration: none;
        font-size: 0.8rem;
    }

    .edit-btn {
        color: #3b82f6;
        background: #eff6ff;
    }

    .empty-state-compact {
        padding: 4rem 2rem;
        text-align: center;
    }

    .empty-state-compact i {
        font-size: 3rem;
        margin-bottom: 1rem;
        opacity: 0.8;
    }

    .empty-state-compact h3 {
        margin: 0 0 0.5rem 0;
        font-size: 1.25rem;
        font-weight: 700;
    }

    .empty-state-compact p {
        color: #64748b;
        margin: 0;
    }
</style>