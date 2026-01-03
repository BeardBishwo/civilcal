<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 bg-white p-4 rounded-4 shadow-sm border">
        <div>
            <h2 class="fw-bold mb-0 text-dark">Technical Word Bank</h2>
            <p class="text-muted mb-0 small"><i class="fas fa-brain me-1"></i> Manage terminology for the Blueprint Builder game.</p>
        </div>
        <button class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#addWordModal">
            <i class="fas fa-plus me-2"></i> Add New Term
        </button>
    </div>

    <!-- Main Table Card -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted uppercase small fw-bold">
                    <tr>
                        <th class="ps-4">Term</th>
                        <th>Definition</th>
                        <th>Level</th>
                        <th>Lang</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($words)): ?>
                        <tr>
                            <td colspan="5" class="py-5 text-center text-muted">
                                <i class="fas fa-book-open display-4 mb-3 d-block opacity-25"></i>
                                No technical terms in the bank yet.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($words as $word): ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold text-dark"><?= htmlspecialchars($word['term']) ?></div>
                                    <small class="text-muted"><?= htmlspecialchars($word['synonyms'] ?: 'No synonyms') ?></small>
                                </td>
                                <td>
                                    <div class="text-truncate" style="max-width: 300px;" title="<?= htmlspecialchars($word['definition']) ?>">
                                        <?= htmlspecialchars($word['definition']) ?>
                                    </div>
                                </td>
                                <td>
                                    <?php if($word['difficulty_level'] == 1): ?>
                                        <span class="badge bg-success-soft text-success rounded-pill px-3">EASY</span>
                                    <?php elseif($word['difficulty_level'] == 2): ?>
                                        <span class="badge bg-warning-soft text-warning rounded-pill px-3">MEDIUM</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger-soft text-danger rounded-pill px-3">HARD</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border"><?= strtoupper($word['language']) ?></span>
                                </td>
                                <td class="text-end pe-4">
                                    <button class="btn btn-sm btn-link text-danger" onclick="deleteWord(<?= $word['id'] ?>)">
                                        <i class="fas fa-trash"></i>
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

<!-- Add Word Modal -->
<div class="modal fade" id="addWordModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <form id="wordForm">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Add Engineering Term</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Technical Term</label>
                        <input type="text" name="term" class="form-control rounded-3" placeholder="e.g. Tensile Strength" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Definition</label>
                        <textarea name="definition" class="form-control rounded-3" rows="3" placeholder="Explain the term clearly..." required></textarea>
                    </div>
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold">Difficulty</label>
                            <select name="difficulty_level" class="form-select rounded-3">
                                <option value="1">Easy</option>
                                <option value="2">Medium</option>
                                <option value="3">Hard</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold">Language</label>
                            <select name="language" class="form-select rounded-3">
                                <option value="en">English</option>
                                <option value="np">Nepali</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="submit" class="btn btn-primary w-100 rounded-pill py-2 fw-bold">Save to Word Bank</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('wordForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const fd = new FormData(e.target);
    const resp = await fetch('<?= app_base_url("/admin/quiz/word-bank/store") ?>', {
        method: 'POST',
        body: fd
    });
    const data = await resp.json();
    if(data.success) {
        location.reload();
    } else {
        alert(data.error);
    }
});

async function deleteWord(id) {
    if(!confirm('Are you sure?')) return;
    const resp = await fetch('<?= app_base_url("/admin/quiz/word-bank/delete/") ?>' + id, {
        method: 'POST'
    });
    const data = await resp.json();
    if(data.success) {
        location.reload();
    }
}
</script>

<style>
.bg-success-soft { background: rgba(16, 185, 129, 0.1); }
.bg-warning-soft { background: rgba(245, 158, 11, 0.1); }
.bg-danger-soft { background: rgba(239, 68, 68, 0.1); }
.table-hover tbody tr:hover { background-color: rgba(99, 102, 241, 0.02); }
</style>
