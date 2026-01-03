<div class="container-fluid py-4">
    <!-- Header with AI Toggle -->
    <div class="d-flex justify-content-between align-items-center mb-4 bg-white p-4 rounded-4 shadow-sm border-0 glass-morphism">
        <div>
            <h2 class="fw-bold text-dark mb-1"><i class="fas fa-trophy text-warning me-2"></i>Contest Engine</h2>
            <p class="text-muted mb-0">Manage Battle Royale events and anti-cheat distributions.</p>
        </div>
        <div class="d-flex align-items-center bg-light p-3 rounded-4 border">
            <div class="me-3 text-end">
                <span class="d-block fw-bold text-dark">AI Auto-Pilot</span>
                <small class="text-success"><?= $autoManager ? 'Active: Daily Contests Generating' : 'Inactive' ?></small>
            </div>
            <div class="form-check form-switch mt-1">
                <input class="form-check-input" type="checkbox" id="aiToggle" <?= $autoManager ? 'checked' : '' ?> style="width: 3.5em; height: 1.75em;">
            </div>
        </div>
    </div>

    <div class="row">
        <!-- New Contest Card -->
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden">
                <div class="card-header bg-primary text-white py-3 border-0">
                    <h5 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Create New Contest</h5>
                </div>
                <div class="card-body p-4">
                    <form id="contestForm">
                        <div class="mb-3">
                            <label class="form-label">Contest Title</label>
                            <input type="text" name="title" class="form-control rounded-3" placeholder="e.g. Weekend Mega Battle" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Start Time</label>
                            <input type="datetime-local" name="start_time" class="form-control rounded-3" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">End Time</label>
                            <input type="datetime-local" name="end_time" class="form-control rounded-3">
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label">Entry Fee (Coins)</label>
                                <input type="number" name="entry_fee" class="form-control rounded-3" value="10">
                            </div>
                            <div class="col-6">
                                <label class="form-label">Prize Pool</label>
                                <input type="number" name="prize_pool" class="form-control rounded-3" value="500">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Winner Logic</label>
                            <div class="p-3 bg-light rounded-3 border">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="winner_type" id="typeLucky" checked>
                                    <label class="form-check-label text-dark fw-bold" for="typeLucky">
                                        Lucky Draw (Anti-Cheat)
                                    </label>
                                    <small class="d-block text-muted">A random winner is picked from the top scorers. Safe from financial exploit.</small>
                                </div>
                                <div class="mt-2">
                                    <label class="small text-muted mb-1">Max Winners to Draw</label>
                                    <input type="number" name="winner_count" class="form-control form-control-sm" value="1" style="max-width: 80px;">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Question IDs (Comma Separated)</label>
                            <textarea name="questions" class="form-control rounded-3" placeholder="12,45,78,90..." rows="2"></textarea>
                            <small class="text-muted italic">Used to manual override. AI Manager picks these automatically.</small>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 rounded-3 py-2 fw-bold shadow-sm">
                            <i class="fas fa-rocket me-2"></i>Launch Contest
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Contest List -->
        <div class="col-md-8 mb-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="mb-0 text-dark">Active & Scheduled Events</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-muted uppercase small fw-bold">
                                <tr>
                                    <th class="ps-4">Contest & Time</th>
                                    <th>Status</th>
                                    <th>Participation</th>
                                    <th>Prize</th>
                                    <th class="text-end pe-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($contests)): ?>
                                    <tr>
                                        <td colspan="5" class="py-5 text-center text-muted">
                                            <i class="fas fa-calendar-times display-4 mb-3 d-block"></i>
                                            No contests scheduled yet.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach($contests as $contest): ?>
                                        <tr>
                                            <td class="ps-4">
                                                <div class="fw-bold text-dark"><?= htmlspecialchars($contest['title']) ?></div>
                                                <small class="text-muted"><i class="far fa-clock me-1"></i><?= date('M d, H:i', strtotime($contest['start_time'])) ?></small>
                                            </td>
                                            <td>
                                                <?php if($contest['status'] == 'live'): ?>
                                                    <span class="badge bg-success-soft text-success border border-success px-3 rounded-pill bg-opacity-10">
                                                        <i class="fas fa-circle-notch fa-spin me-1"></i>LIVE
                                                    </span>
                                                <?php elseif($contest['status'] == 'upcoming'): ?>
                                                    <span class="badge bg-blue-soft text-primary border border-primary px-3 rounded-pill bg-opacity-10">UPCOMING</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary-soft text-secondary border px-3 rounded-pill bg-opacity-10">ENDED</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="small fw-bold">45 Participants</div>
                                                <small class="text-muted">Fee: <?= $contest['entry_fee'] ?> Coins</small>
                                            </td>
                                            <td>
                                                <div class="text-warning fw-bold"><?= $contest['prize_pool'] ?> Coins</div>
                                                <small class="text-muted"><?= $contest['winner_count'] ?> Winners</small>
                                            </td>
                                            <td class="text-end pe-4">
                                                <?php if($contest['status'] !== 'ended'): ?>
                                                    <button onclick="processContest(<?= $contest['id'] ?>)" class="btn btn-sm btn-outline-warning rounded-pill px-3">
                                                        <i class="fas fa-check-double me-1"></i>Judge
                                                    </button>
                                                <?php endif; ?>
                                                <button class="btn btn-link text-muted"><i class="fas fa-ellipsis-v"></i></button>
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
    </div>
</div>

<script>
document.getElementById('aiToggle').addEventListener('change', function() {
    const status = this.checked ? '1' : '0';
    
    fetch('<?= app_base_url('admin/contest/toggle-auto') ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'status=' + status
    })
    .then(r => r.json())
    .then(data => {
        if(data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Updated',
                text: data.message,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });
            setTimeout(() => location.reload(), 1000);
        }
    });
});

document.getElementById('contestForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    fetch('<?= app_base_url('admin/contest/store') ?>', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: new URLSearchParams(formData)
    })
    .then(r => r.json())
    .then(data => {
        if(data.success) {
            Swal.fire('Success', data.message, 'success').then(() => location.reload());
        } else {
            Swal.fire('Error', data.error, 'error');
        }
    });
});

function processContest(id) {
    Swal.fire({
        title: 'Run Lucky Draw?',
        text: 'This will pick winners from top scorers and distribute prizes instantly!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, Judge now!'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('<?= app_base_url('admin/contest/process/') ?>' + id, { method: 'POST' })
            .then(r => r.json())
            .then(data => {
                if(data.success) {
                    Swal.fire('Awarded!', data.message, 'success').then(() => location.reload());
                } else {
                    Swal.fire('Error', data.error, 'error');
                }
            });
        }
    });
}
</script>

<style>
.glass-morphism {
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(10px);
}
.bg-success-soft { background-color: rgba(40, 167, 69, 0.1); }
.bg-blue-soft { background-color: rgba(0, 123, 255, 0.1); }
.bg-secondary-soft { background-color: rgba(108, 117, 125, 0.1); }
</style>
