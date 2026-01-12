<?php

/**
 * PREMIUM QUESTION BANK
 * Professional, high-density layout matching Categories design.
 */
$questions = $questions ?? [];
$stats = $stats ?? ['total' => 0, 'mcq' => 0, 'multi' => 0];
$courses = $courses ?? [];
$educationLevels = $educationLevels ?? [];
$mainCategories = $mainCategories ?? [];
$positionLevels = $positionLevels ?? [];
?>

<div class="admin-wrapper-container">
    <div class="admin-content-wrapper">

        <!-- Compact Page Header -->
        <div class="compact-header">
            <div class="header-left">
                <div class="header-title">
                    <i class="fas fa-database"></i>
                    <h1>Question Bank</h1>
                </div>
                <div class="header-subtitle">
                    <?php echo number_format($stats['mcq']); ?> MCQ •
                    <?php echo number_format($stats['multi']); ?> Multi •
                    <?php echo number_format($stats['order'] ?? 0); ?> Sequence •
                    <?php echo number_format($stats['tf'] ?? 0); ?> True/False •
                    <?php echo number_format($stats['theory_short'] ?? 0); ?> Short •
                    <?php echo number_format($stats['theory_long'] ?? 0); ?> Long
                </div>
            </div>
            <div class="header-actions" style="display:flex; gap:10px;">
                <div class="stat-pill">
                    <span class="label">TOTAL</span>
                    <span class="value"><?php echo number_format($stats['total']); ?></span>
                </div>
                <div style="width:1px; height:40px; background:rgba(255,255,255,0.2);"></div>
                <a href="<?php echo app_base_url('admin/quiz/questions/create'); ?>" class="btn-create-premium" style="text-decoration:none;">
                    <i class="fas fa-plus"></i> NEW QUESTION
                </a>
            </div>
        </div>

        <!-- Filter Toolbar -->
        <div class="compact-toolbar">
            <div class="toolbar-left">
                <form method="GET" style="display:flex; align-items:center; gap:0.75rem; margin:0;">
                    <span class="filter-label">FILTER:</span>

                    <select name="stream" class="filter-select" style="width:150px;">
                        <option value="">All Courses</option>
                        <?php foreach ($courses as $c): ?>
                            <option value="<?php echo $c['id']; ?>" <?php echo ($_GET['stream'] ?? '') == $c['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($c['title']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <select name="education_level" class="filter-select" style="width:150px;">
                        <option value="">All Levels</option>
                        <?php foreach ($educationLevels as $l): ?>
                            <option value="<?php echo $l['id']; ?>" <?php echo ($_GET['education_level'] ?? '') == $l['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($l['title']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <select name="topic_id" class="filter-select" style="width:180px;">
                        <option value="">All Categories</option>
                        <?php if (!empty($mainCategories)): ?>
                            <?php foreach ($mainCategories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>" <?php echo ($_GET['topic_id'] ?? '') == $cat['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['title']); ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>

                    <select name="type" class="filter-select" style="width:180px;">
                        <option value="">All Types</option>
                        <option value="MCQ" <?php echo ($_GET['type'] ?? '') == 'MCQ' ? 'selected' : ''; ?>>MCQ</option>
                        <option value="TF" <?php echo ($_GET['type'] ?? '') == 'TF' ? 'selected' : ''; ?>>True/False</option>
                        <option value="MULTI" <?php echo ($_GET['type'] ?? '') == 'MULTI' ? 'selected' : ''; ?>>Multi-Select</option>
                        <option value="SEQUENCE" <?php echo ($_GET['type'] ?? '') == 'SEQUENCE' ? 'selected' : ''; ?>>Sequence</option>
                        <option value="NUMERICAL" <?php echo ($_GET['type'] ?? '') == 'NUMERICAL' ? 'selected' : ''; ?>>Numerical</option>
                        <option value="TEXT" <?php echo ($_GET['type'] ?? '') == 'TEXT' ? 'selected' : ''; ?>>Text</option>
                        <option value="THEORY" <?php echo ($_GET['type'] ?? '') == 'THEORY' ? 'selected' : ''; ?>>Theory (All)</option>
                        <option value="THEORY_SHORT" <?php echo ($_GET['type'] ?? '') == 'THEORY_SHORT' ? 'selected' : ''; ?>>└─ Short Answer (4 marks)</option>
                        <option value="THEORY_LONG" <?php echo ($_GET['type'] ?? '') == 'THEORY_LONG' ? 'selected' : ''; ?>>└─ Long Answer (8 marks)</option>
                    </select>

                    <button type="submit" class="btn-filter-apply">
                        <i class="fas fa-filter"></i>
                    </button>

                    <?php if (!empty($_GET['stream']) || !empty($_GET['type']) || !empty($_GET['topic_id'])): ?>
                        <a href="<?php echo app_base_url('admin/quiz/questions'); ?>" class="btn-filter-clear">
                            <i class="fas fa-times"></i>
                        </a>
                    <?php endif; ?>
                </form>
            </div>
            <div class="toolbar-right">
                <div class="search-compact">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search questions..." id="question-search" onkeyup="filterQuestions()">
                </div>
            </div>
        </div>

        <!-- Content Area -->
        <div class="table-container">
            <div class="table-wrapper">
                <table class="table-compact">
                    <thead>
                        <tr>
                            <th style="width: 40px;" class="text-center">
                                <input type="checkbox" id="selectAll" onclick="toggleSelectAll()">
                            </th>
                            <th style="width: 60px;" class="text-center">ID</th>
                            <th style="width: 45%;">Question Content</th>
                            <th class="text-center" style="width: 15%;">Type</th>
                            <th class="text-center" style="width: 12%;">Difficulty</th>
                            <th class="text-center" style="width: 10%;">Status</th>
                            <th class="text-center" style="width: 100px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="questionList">
                        <?php if (empty($questions)): ?>
                            <tr>
                                <td colspan="7" class="empty-cell">
                                    <div class="empty-state-compact">
                                        <i class="fas fa-search"></i>
                                        <h3>No questions found</h3>
                                        <p>Try adjusting your filters or add a new question.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($questions as $q):
                                $content = json_decode($q['content'], true);
                                $text = strip_tags($content['text'] ?? '');
                                if (strlen($text) > 100) $text = substr($text, 0, 100) . '...';
                            ?>
                                <tr class="question-item group" data-id="<?php echo $q['id']; ?>">
                                    <td class="text-center align-middle">
                                        <input type="checkbox" class="row-checkbox" value="<?php echo $q['id']; ?>" onchange="updateBulkToolbar()">
                                    </td>
                                    <td class="text-center align-middle">
                                        <span class="text-xs font-bold text-slate-400"><?php echo $q['id']; ?></span>
                                    </td>
                                    <td>
                                        <div class="item-info">
                                            <div class="item-icon" style="background: <?php echo $q['type'] == 'MCQ' ? '#dbeafe' : '#d1fae5'; ?>; color: <?php echo $q['type'] == 'MCQ' ? '#1e40af' : '#065f46'; ?>;">
                                                <i class="fas fa-<?php echo $q['type'] == 'MCQ' ? 'check-circle' : 'list-check'; ?>"></i>
                                            </div>
                                            <div class="item-text">
                                                <div class="item-title"><?php echo htmlspecialchars($text); ?></div>
                                                <?php if (!empty($q['unique_code'])): ?>
                                                    <div class="item-slug"><?php echo htmlspecialchars($q['unique_code']); ?></div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <?php
                                        $badgeColor = '#dbeafe';
                                        $textColor = '#1e40af';
                                        $borderColor = '#93c5fd';
                                        $displayType = $q['type'];

                                        if ($q['type'] == 'THEORY') {
                                            $badgeColor = '#fef3c7';
                                            $textColor = '#92400e';
                                            $borderColor = '#fcd34d';

                                            // Determine short or long
                                            if (!empty($q['theory_type'])) {
                                                $displayType = $q['theory_type'] == 'short' ? 'THEORY (S)' : 'THEORY (L)';
                                            } else {
                                                $displayType = $q['default_marks'] <= 4 ? 'THEORY (S)' : 'THEORY (L)';
                                            }
                                        } elseif ($q['type'] == 'MCQ') {
                                            $badgeColor = '#dbeafe';
                                            $textColor = '#1e40af';
                                            $borderColor = '#93c5fd';
                                        } elseif ($q['type'] == 'MULTI') {
                                            $badgeColor = '#d1fae5';
                                            $textColor = '#065f46';
                                            $borderColor = '#6ee7b7';
                                        } elseif ($q['type'] == 'TF') {
                                            $badgeColor = '#e0e7ff';
                                            $textColor = '#3730a3';
                                            $borderColor = '#a5b4fc';
                                        } elseif ($q['type'] == 'ORDER') {
                                            $badgeColor = '#f3e8ff';
                                            $textColor = '#6b21a8';
                                            $borderColor = '#d8b4fe';
                                            $displayType = 'SEQUENCE';
                                        }
                                        ?>
                                        <span class="badge-pill" style="background: <?php echo $badgeColor; ?>; color: <?php echo $textColor; ?>; border-color: <?php echo $borderColor; ?>;">
                                            <?php echo $displayType; ?>
                                        </span>
                                    </td>
                                    <td class="text-center align-middle">
                                        <div class="difficulty-stars">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <i class="<?php echo $i <= $q['difficulty_level'] ? 'fas' : 'far'; ?> fa-star"></i>
                                            <?php endfor; ?>
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">
                                        <span class="status-badge <?php echo $q['is_active'] ? 'status-active' : 'status-inactive'; ?>">
                                            <?php echo $q['is_active'] ? 'Active' : 'Inactive'; ?>
                                        </span>
                                    </td>
                                    <td class="text-center align-middle">
                                        <div class="actions-compact justify-center">
                                            <a href="<?php echo app_base_url('admin/quiz/questions/edit/' . $q['id']); ?>" class="action-btn-icon edit-btn" title="Edit">
                                                <i class="fas fa-pencil-alt"></i>
                                            </a>
                                            <button onclick="deleteQuestion(<?php echo $q['id']; ?>)" class="action-btn-icon delete-btn" title="Delete">
                                                <i class="fas fa-trash-alt"></i>
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

        <!-- Pagination -->
        <?php if ($total > 0): ?>
            <div class="pagination-bar">
                <div class="pagination-info">
                    Showing <?php echo $offset + 1; ?> to <?php echo min($offset + $limit, $total); ?> of <?php echo number_format($total); ?> rows

                    <!-- Per Page Selector -->
                    <select onchange="changePerPage(this.value)" class="per-page-select" style="margin-left: 1rem; padding: 0.25rem 0.5rem; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 0.85rem; cursor: pointer;">
                        <option value="5" <?php echo ($limit == 5) ? 'selected' : ''; ?>>5</option>
                        <option value="10" <?php echo ($limit == 10) ? 'selected' : ''; ?>>10</option>
                        <option value="20" <?php echo ($limit == 20) ? 'selected' : ''; ?>>20</option>
                        <option value="50" <?php echo ($limit == 50) ? 'selected' : ''; ?>>50</option>
                        <option value="100" <?php echo ($limit == 100) ? 'selected' : ''; ?>>100</option>
                        <option value="200" <?php echo ($limit == 200) ? 'selected' : ''; ?>>200</option>
                    </select>
                    <span style="margin-left: 0.5rem; font-size: 0.85rem; color: #64748b;">rows per page</span>
                </div>
                <div class="pagination-controls">
                    <a href="?page=<?php echo max(1, $page - 1); ?>&per_page=<?php echo $limit; ?>" class="pagination-btn" <?php echo $page == 1 ? 'style="pointer-events:none;opacity:0.5;"' : ''; ?>>
                        <i class="fas fa-chevron-left"></i>
                    </a>
                    <span class="pagination-current">Page <?php echo $page; ?> of <?php echo $totalPages; ?></span>
                    <a href="?page=<?php echo min($totalPages, $page + 1); ?>&per_page=<?php echo $limit; ?>" class="pagination-btn" <?php echo $page >= $totalPages ? 'style="pointer-events:none;opacity:0.5;"' : ''; ?>>
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </div>
            </div>
        <?php endif; ?>

        <!-- Float Bulk Toolbar -->
        <div id="bulkToolbar" class="bulk-toolbar">
            <div class="bulk-info">
                <span class="bulk-count">0</span> Selected
            </div>
            <div class="bulk-actions">
                <button onclick="bulkDelete()" class="btn-bulk-delete">
                    <i class="fas fa-trash-alt"></i> Delete
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function deleteQuestion(id) {
        Swal.fire({
            title: 'Delete Question?',
            text: "This action cannot be undone.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#cbd5e1',
            confirmButtonText: 'Delete'
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    const res = await fetch('<?php echo app_base_url('admin/quiz/questions/delete/'); ?>' + id, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    const d = await res.json();

                    if (d.success || d.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: d.message || 'Question has been deleted.',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => location.reload());
                    } else {
                        Swal.fire('Error!', d.message || 'Failed to delete question.', 'error');
                    }
                } catch (err) {
                    console.error('Delete Error:', err);
                    Swal.fire('System Error', 'Could not connect to the server or invalid response received.', 'error');
                }
            }
        });
    }

    function toggleSelectAll() {
        const checked = document.getElementById('selectAll').checked;
        document.querySelectorAll('.row-checkbox').forEach(el => el.checked = checked);
        updateBulkToolbar();
    }

    function updateBulkToolbar() {
        const selected = document.querySelectorAll('.row-checkbox:checked').length;
        const toolbar = document.getElementById('bulkToolbar');
        document.querySelector('.bulk-count').innerText = selected;

        if (selected > 0) {
            toolbar.classList.add('active');
        } else {
            toolbar.classList.remove('active');
        }
    }

    async function bulkDelete() {
        const ids = Array.from(document.querySelectorAll('.row-checkbox:checked')).map(el => parseInt(el.value));
        if (ids.length === 0) return;

        Swal.fire({
            title: `Delete ${ids.length} Questions?`,
            text: "This cannot be undone.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'Delete All'
        }).then(async (result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Processing...',
                    text: `Deleting ${ids.length} questions...`,
                    didOpen: () => {
                        Swal.showLoading();
                    },
                    allowOutsideClick: false
                });

                try {
                    const res = await fetch('<?php echo app_base_url('admin/quiz/questions/bulk-delete'); ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            ids: ids
                        })
                    });

                    const d = await res.json();

                    if (d.success || d.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: d.message || `Deleted ${d.deleted || ids.length} questions successfully`,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => location.reload());
                    } else {
                        Swal.fire('Error!', d.message || 'Failed to delete questions.', 'error');
                    }
                } catch (err) {
                    console.error('Bulk Delete Error:', err);
                    Swal.fire('System Error', 'Could not connect to the server. Please try again.', 'error');
                }
            }
        });
    }

    function changePerPage(value) {
        const url = new URL(window.location.href);
        url.searchParams.set('per_page', value);
        url.searchParams.set('page', '1'); // Reset to page 1 when changing per page
        window.location.href = url.toString();
    }

    function filterQuestions() {
        const query = document.getElementById('question-search').value.toLowerCase();
        document.querySelectorAll('.question-item').forEach(el => {
            const text = el.innerText.toLowerCase();
            el.style.display = text.indexOf(query) > -1 ? '' : 'none';
        });
    }
</script>

<style>
    /* ========================================
   PREMIUM CORE STYLES
   ======================================== */
    :root {
        --admin-primary: #667eea;
        --admin-secondary: #764ba2;
        --admin-gray-50: #f8f9fa;
        --admin-gray-200: #e5e7eb;
        --admin-gray-300: #d1d5db;
        --admin-gray-400: #9ca3af;
        --admin-gray-600: #4b5563;
        --admin-gray-800: #1f2937;
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

    /* Header */
    .compact-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.5rem 2rem;
        background: linear-gradient(135deg, var(--admin-primary) 0%, var(--admin-secondary) 100%);
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
        display: flex;
        flex-direction: column;
        align-items: center;
        min-width: 80px;
    }

    .stat-pill.warning {
        background: rgba(252, 211, 77, 0.15);
        border-color: rgba(252, 211, 77, 0.3);
    }

    .stat-pill.success {
        background: rgba(16, 185, 129, 0.15);
        border-color: rgba(16, 185, 129, 0.3);
    }

    .stat-pill .label {
        font-size: 0.65rem;
        font-weight: 700;
        letter-spacing: 0.5px;
        opacity: 0.9;
    }

    .stat-pill .value {
        font-size: 1.1rem;
        font-weight: 800;
        line-height: 1.1;
    }

    .btn-create-premium {
        height: 40px;
        padding: 0 1.5rem;
        background: white;
        color: var(--admin-primary);
        font-weight: 600;
        font-size: 0.875rem;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: 0.2s;
        box-shadow: 0 2px 4px rgba(255, 255, 255, 0.2);
        white-space: nowrap;
    }

    .btn-create-premium:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 6px rgba(255, 255, 255, 0.3);
    }

    /* Filter Bar */
    .compact-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem 2rem;
        background: #eff6ff;
        border-bottom: 1px solid #bfdbfe;
    }

    .filter-label {
        font-size: 0.7rem;
        font-weight: 700;
        color: #1e40af;
        letter-spacing: 0.5px;
    }

    .filter-select {
        font-size: 0.85rem;
        font-weight: 600;
        color: #1e40af;
        border: 1px solid #93c5fd;
        border-radius: 6px;
        padding: 0.25rem 0.5rem;
        background: white;
        outline: none;
        height: 32px;
    }

    .btn-filter-apply,
    .btn-filter-clear {
        width: 32px;
        height: 32px;
        border: 1px solid #93c5fd;
        border-radius: 6px;
        background: white;
        color: #1e40af;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: 0.2s;
        text-decoration: none;
    }

    .btn-filter-apply:hover {
        background: #1e40af;
        color: white;
    }

    .btn-filter-clear:hover {
        background: #fef2f2;
        color: #ef4444;
        border-color: #fecaca;
    }

    .search-compact {
        position: relative;
        width: 100%;
        max-width: 300px;
    }

    .search-compact i {
        position: absolute;
        left: 0.75rem;
        top: 50%;
        transform: translateY(-50%);
        color: #64748b;
        font-size: 0.85rem;
    }

    .search-compact input {
        width: 100%;
        height: 36px;
        padding: 0 0.75rem 0 2.25rem;
        font-size: 0.85rem;
        border: 1px solid #bfdbfe;
        border-radius: 6px;
        outline: none;
        background: white;
        color: #1e40af;
    }

    /* Table */
    .table-compact {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.875rem;
    }

    .table-compact th {
        background: white;
        padding: 0.75rem 1.5rem;
        text-align: left;
        font-weight: 600;
        color: #94a3b8;
        text-transform: uppercase;
        font-size: 0.7rem;
        letter-spacing: 0.5px;
        border-bottom: 1px solid #e2e8f0;
    }

    .table-compact td {
        padding: 0.6rem 1.5rem;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }

    .question-item:hover {
        background: #f8fafc;
    }

    .item-info {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .item-icon {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        color: #94a3b8;
    }

    .item-title {
        font-weight: 600;
        color: #334155;
        line-height: 1.3;
    }

    .item-slug {
        font-size: 0.75rem;
        color: #94a3b8;
        font-family: monospace;
    }

    .badge-pill {
        background: #e0e7ff;
        color: #4338ca;
        padding: 6px 12px;
        border-radius: 12px;
        font-size: 0.7rem;
        font-weight: 700;
        border: 1px solid #c7d2fe;
        display: inline-block;
        text-align: center;
    }

    .difficulty-stars {
        color: #fbbf24;
        font-size: 0.75rem;
    }

    .status-badge {
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 0.65rem;
        font-weight: 700;
        text-transform: uppercase;
    }

    .status-active {
        background: #ecfdf5;
        color: #065f46;
        border: 1px solid #a7f3d0;
    }

    .status-inactive {
        background: #fef2f2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }

    .action-btn-icon {
        width: 32px;
        height: 32px;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        background: white;
        color: #94a3b8;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: 0.2s;
    }

    .action-btn-icon:hover {
        transform: translateY(-1px);
    }

    .edit-btn:hover {
        color: #3b82f6;
        background: #eff6ff;
    }

    .delete-btn:hover {
        color: #ef4444;
        background: #fef2f2;
    }

    .actions-compact {
        display: flex;
        gap: 0.5rem;
    }

    .actions-compact.justify-center {
        justify-content: center;
    }

    .empty-state-compact {
        text-align: center;
        padding: 3rem 1rem;
        color: #94a3b8;
    }

    .empty-state-compact i {
        font-size: 2.5rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }

    .empty-state-compact h3 {
        margin: 0 0 0.5rem 0;
        color: #64748b;
        font-size: 1.1rem;
    }

    .empty-state-compact p {
        font-size: 0.9rem;
        margin: 0;
    }

    /* Pagination */
    .pagination-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 2rem;
        background: #f8fafc;
        border-top: 1px solid #e2e8f0;
    }

    .pagination-info {
        font-size: 0.85rem;
        color: #64748b;
    }

    .pagination-controls {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .pagination-btn {
        width: 32px;
        height: 32px;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        background: white;
        color: #64748b;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        transition: 0.2s;
    }

    .pagination-btn:hover {
        background: #667eea;
        color: white;
        border-color: #667eea;
    }

    .pagination-current {
        font-size: 0.85rem;
        font-weight: 600;
        color: #667eea;
        padding: 0 0.5rem;
    }

    /* Bulk Toolbar */
    .bulk-toolbar {
        position: fixed;
        bottom: 2rem;
        left: 50%;
        transform: translateX(-50%) translateY(100px);
        background: #1e293b;
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: 50px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        display: flex;
        align-items: center;
        gap: 2rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        opacity: 0;
        z-index: 100;
    }

    .bulk-toolbar.active {
        transform: translateX(-50%) translateY(0);
        opacity: 1;
    }

    .bulk-info {
        font-weight: 600;
        font-size: 0.9rem;
    }

    .bulk-count {
        color: #818cf8;
        font-weight: 800;
    }

    .bulk-actions {
        display: flex;
        gap: 0.5rem;
    }

    .btn-bulk-delete {
        background: #ef4444;
        color: white;
        border: none;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: 0.2s;
    }

    .btn-bulk-delete:hover {
        background: #dc2626;
    }
</style>