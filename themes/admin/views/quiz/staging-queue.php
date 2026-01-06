<?php
/**
 * STAGING QUEUE MANAGER
 * View and manage all import batches
 */
?>

<div class="admin-wrapper-container">
    <div class="admin-content-wrapper">

        <!-- Premium Header -->
        <div class="compact-header">
            <div class="header-left">
                <div class="header-title">
                    <i class="fas fa-layer-group"></i>
                    <h1>Staging Queue</h1>
                </div>
                <div class="header-subtitle">Manage import batches • Review staged questions • Clean old data</div>
            </div>
            
            <div class="header-actions" style="display:flex; gap:10px;">
                <div class="stat-pill">
                    <span class="label">BATCHES</span>
                    <span class="value"><?= count($batches) ?></span>
                </div>
                <div class="stat-pill success">
                    <span class="label">TOTAL STAGED</span>
                    <span class="value"><?= array_sum(array_column($batches, 'total_questions')) ?></span>
                </div>
                <a href="<?= app_base_url('admin/quiz/import') ?>" class="btn btn-primary btn-compact" style="background:white; color:var(--admin-primary); text-decoration:none;">
                    <i class="fas fa-upload"></i>
                    <span>NEW IMPORT</span>
                </a>
            </div>
        </div>

        <!-- Batches List -->
        <div class="pages-content" style="padding: 2rem;">
            
            <?php if (empty($batches)): ?>
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <h3>No Staging Batches</h3>
                    <p>Upload a file to create your first import batch</p>
                    <a href="<?= app_base_url('admin/quiz/import') ?>" class="btn btn-primary gradient mt-3">
                        <i class="fas fa-upload"></i> Start Import
                    </a>
                </div>
            <?php else: ?>
                
                <div class="batch-grid">
                    <?php foreach ($batches as $batch): ?>
                        <div class="batch-card">
                            <div class="batch-header">
                                <div class="batch-info">
                                    <h3><?= htmlspecialchars($batch['batch_id']) ?></h3>
                                    <p class="batch-meta">
                                        <i class="fas fa-user"></i> <?= htmlspecialchars($batch['uploader_name']) ?>
                                        <span class="separator">•</span>
                                        <i class="fas fa-clock"></i> <?= date('M j, Y g:i A', strtotime($batch['uploaded_at'])) ?>
                                    </p>
                                </div>
                            </div>
                            
                            <div class="batch-stats">
                                <div class="stat-item">
                                    <div class="stat-value"><?= $batch['total_questions'] ?></div>
                                    <div class="stat-label">Total Questions</div>
                                </div>
                                <div class="stat-item success">
                                    <div class="stat-value"><?= $batch['clean_count'] ?></div>
                                    <div class="stat-label">Clean</div>
                                </div>
                                <div class="stat-item danger">
                                    <div class="stat-value"><?= $batch['duplicate_count'] ?></div>
                                    <div class="stat-label">Conflicts</div>
                                </div>
                            </div>
                            
                            <div class="batch-actions">
                                <a href="<?= app_base_url('admin/quiz/staging/batch/' . urlencode($batch['batch_id'])) ?>" class="btn btn-primary btn-compact">
                                    <i class="fas fa-eye"></i> View Details
                                </a>
                                <button class="btn btn-danger btn-compact" onclick="deleteBatch('<?= htmlspecialchars($batch['batch_id']) ?>')">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="bulk-actions-bar">
                    <button class="btn btn-secondary" onclick="cleanOldBatches()">
                        <i class="fas fa-broom"></i> Clean Old Batches (30+ days)
                    </button>
                </div>

            <?php endif; ?>

        </div>
    </div>
</div>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
const baseUrl = '<?= app_base_url() ?>';

function deleteBatch(batchId) {
    Swal.fire({
        title: 'Delete Batch?',
        text: `This will permanently delete batch "${batchId}" and all its staged questions.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        confirmButtonText: 'Delete Batch'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`${baseUrl}/admin/quiz/staging/delete`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ batch_id: batchId })
            })
            .then(r => r.json())
            .then(d => {
                if (d.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => location.reload());
                } else {
                    Swal.fire('Error', d.message, 'error');
                }
            });
        }
    });
}

function cleanOldBatches() {
    Swal.fire({
        title: 'Clean Old Batches?',
        text: 'This will delete all batches older than 30 days.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#667eea',
        confirmButtonText: 'Clean Now'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`${baseUrl}/admin/quiz/staging/clean-old`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(r => r.json())
            .then(d => {
                if (d.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Cleaned!',
                        text: d.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => location.reload());
                } else {
                    Swal.fire('Error', d.message, 'error');
                }
            });
        }
    });
}
</script>

<style>
:root {
    --admin-primary: #667eea;
    --admin-secondary: #764ba2;
    --admin-success: #10b981;
    --admin-danger: #ef4444;
}

.admin-wrapper-container { 
    padding: 1rem; 
    background: #f8f9fa; 
    min-height: calc(100vh - 70px); 
}

.admin-content-wrapper { 
    background: white; 
    border-radius: 12px; 
    box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); 
    overflow: hidden; 
}

/* Premium Header */
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

.header-subtitle { 
    font-size: 0.85rem; 
    opacity: 0.8; 
    margin-top: 4px; 
    font-weight: 500; 
}

.stat-pill { 
    background: rgba(255,255,255,0.15); 
    border: 1px solid rgba(255,255,255,0.2); 
    border-radius: 8px; 
    padding: 0.5rem 1rem; 
    display: flex; 
    flex-direction: column; 
    align-items: center; 
    min-width: 80px; 
}

.stat-pill.success { 
    background: rgba(16, 185, 129, 0.15); 
    border-color: rgba(16, 185, 129, 0.3); 
}

.stat-pill .label { 
    font-size: 0.65rem; 
    font-weight: 700; 
    letter-spacing: 0.5px; 
}

.stat-pill .value { 
    font-size: 1.1rem; 
    font-weight: 800; 
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    color: #94a3b8;
}

.empty-state i {
    font-size: 4rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}

.empty-state h3 {
    font-size: 1.5rem;
    font-weight: 700;
    color: #64748b;
    margin-bottom: 0.5rem;
}

/* Batch Grid */
.batch-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.batch-card {
    background: white;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    overflow: hidden;
    transition: all 0.2s ease;
}

.batch-card:hover {
    border-color: var(--admin-primary);
    box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
    transform: translateY(-2px);
}

.batch-header {
    background: linear-gradient(135deg, #f8fafc, #f1f5f9);
    padding: 1.25rem;
    border-bottom: 2px solid #e2e8f0;
}

.batch-info h3 {
    font-size: 1rem;
    font-weight: 700;
    color: #1e293b;
    margin: 0 0 0.5rem 0;
}

.batch-meta {
    font-size: 0.75rem;
    color: #64748b;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.separator {
    color: #cbd5e1;
}

.batch-stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
    padding: 1.25rem;
    background: #fafbfc;
}

.stat-item {
    text-align: center;
}

.stat-value {
    font-size: 1.75rem;
    font-weight: 800;
    color: #1e293b;
}

.stat-item.success .stat-value {
    color: var(--admin-success);
}

.stat-item.danger .stat-value {
    color: var(--admin-danger);
}

.stat-label {
    font-size: 0.7rem;
    font-weight: 600;
    color: #94a3b8;
    text-transform: uppercase;
    margin-top: 0.25rem;
}

.batch-actions {
    display: flex;
    gap: 0.75rem;
    padding: 1.25rem;
    border-top: 1px solid #f1f5f9;
}

/* Bulk Actions */
.bulk-actions-bar {
    padding: 1.5rem;
    background: #f8fafc;
    border-radius: 8px;
    text-align: center;
}

/* Buttons */
.btn { 
    height: 40px; 
    padding: 0 1.25rem; 
    border-radius: 8px; 
    font-weight: 700; 
    font-size: 0.8rem; 
    cursor: pointer; 
    display: inline-flex; 
    align-items: center; 
    justify-content: center;
    gap: 0.5rem; 
    border: none; 
    transition: all 0.2s ease; 
    text-decoration: none;
}

.btn-primary { 
    background: var(--admin-primary); 
    color: white; 
}

.btn-primary.gradient { 
    background: linear-gradient(135deg, var(--admin-primary), var(--admin-secondary)); 
}

.btn-secondary { 
    background: #f1f5f9; 
    color: #475569; 
    border: 1px solid #e2e8f0;
}

.btn-danger { 
    background: white; 
    color: var(--admin-danger); 
    border: 2px solid var(--admin-danger); 
}

.btn-compact { 
    height: 36px; 
    padding: 0 0.75rem; 
    font-size: 0.75rem; 
    flex: 1;
}

.btn:hover { 
    transform: translateY(-1px); 
    box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
}

.mt-3 { margin-top: 1rem; }

@media (max-width: 768px) {
    .batch-grid {
        grid-template-columns: 1fr;
    }
}
</style>
