

<div class="container py-5">
    <div class="row align-items-end mb-5">
        <div class="col-md-8">
            <h1 class="display-4 font-weight-bold mb-0"><?php echo htmlspecialchars($guild['name']); ?></h1>
            <p class="lead text-muted"><?php echo htmlspecialchars($guild['description']); ?></p>
            <div class="d-flex align-items-center mt-3">
                <div class="badge badge-primary px-3 py-2 mr-3">LEVEL <?php echo $guild['level']; ?></div>
                <div class="flex-grow-1" style="max-width: 300px;">
                    <div class="progress" style="height: 10px;">
                        <div class="progress-bar bg-success" style="width: <?php echo ($guild['xp'] % 1000) / 10; ?>%"></div>
                    </div>
                    <small class="text-muted"><?php echo $guild['xp'] % 1000; ?> / 1000 XP to next level</small>
                </div>
            </div>
        </div>
        <div class="col-md-4 text-right">
            <div class="badge badge-warning p-2 px-3 mb-2"><?php echo $my_role; ?></div>
            <div class="text-muted small">Founded <?php echo date('M Y', strtotime($guild['created_at'])); ?></div>
        </div>
    </div>

    <div class="row">
        <!-- Resource Vault -->
        <div class="col-md-8">
            <div class="card shadow-lg border-0 mb-4 bg-dark text-white">
                <div class="card-header bg-transparent border-secondary font-weight-bold">
                    🏛️ Firm Resource Vault
                </div>
                <div class="card-body p-4">
                    <div class="row text-center mb-4">
                        <?php foreach ($vault as $res): ?>
                        <div class="col-3">
                            <div class="vault-item">
                                <div class="h3 font-weight-bold"><?php echo number_format($res['amount']); ?></div>
                                <div class="text-uppercase small text-muted"><?php echo $res['resource_type']; ?></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <hr class="border-secondary">
                    <h6 class="mb-3">Donate to the Vault</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <select id="res-type" class="form-control bg-secondary border-0 text-white">
                                <option value="bricks">Bricks (You: <?php echo $wallet['bricks']; ?>)</option>
                                <option value="cement">Cement (You: <?php echo $wallet['cement']; ?>)</option>
                                <option value="steel">Steel (You: <?php echo $wallet['steel']; ?>)</option>
                                <option value="coins">Coins (You: <?php echo $wallet['coins']; ?>)</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="number" id="res-amt" class="form-control bg-secondary border-0 text-white" placeholder="Amount">
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-success btn-block" onclick="donate()">Donate</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Members List -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white font-weight-bold">
                    👥 Team Members
                </div>
                <div class="list-group list-group-flush">
                    <?php foreach ($members as $m): ?>
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong><?php echo htmlspecialchars($m['full_name'] ?: $m['username']); ?></strong>
                            <span class="text-muted small ml-2"><?php echo $m['role']; ?></span>
                        </div>
                        <div class="text-right small text-muted">
                            Joined <?php echo date('M d', strtotime($m['joined_at'])); ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Activity Feed -->
        <div class="col-md-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white font-weight-bold">
                    🔔 Activity Stream
                </div>
                <div class="card-body">
                    <div class="alert alert-info py-2 small">
                        <i class="fas fa-info-circle"></i> Donate resources to level up your Firm and unlock Mega Projects!
                    </div>
                    <div class="activity-timeline">
                        <!-- Simplified Feed -->
                        <div class="timeline-item mb-3">
                            <small class="text-muted d-block">System</small>
                            Firm established!
                        </div>
                    </div>

                    <?php if ($my_role === 'Leader' && !empty($requests)): ?>
                    <hr>
                    <h6 class="mb-3">Pending Requests</h6>
                    <?php foreach ($requests as $r): ?>
                    <div class="alert alert-secondary d-flex justify-content-between align-items-center py-2 mb-2">
                        <span class="small"><?php echo htmlspecialchars($r['username']); ?></span>
                        <div>
                            <button class="btn btn-sm btn-success p-0 px-2" onclick="handleRequest(<?php echo $r['id']; ?>, 'approve')">✓</button>
                            <button class="btn btn-sm btn-danger p-0 px-2" onclick="handleRequest(<?php echo $r['id']; ?>, 'decline')">×</button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>

                    <div class="mt-4">
                        <a href="/quiz/firms/leave" class="text-danger small" onclick="return confirm('Are you sure you want to leave?')">Leave Firm</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    async function donate() {
        const type = document.getElementById('res-type').value;
        const amount = document.getElementById('res-amt').value;
        
        if (!amount || amount <= 0) return alert('Enter a valid amount');

        const fd = new FormData();
        fd.append('type', type);
        fd.append('amount', amount);

        try {
            const res = await fetch('/api/firms/donate', { method: 'POST', body: fd });
            const data = await res.json();
            if (data.success) {
                location.reload();
            } else {
                alert(data.message);
            }
        } catch (e) {
            alert('Donation failed.');
        }
    }

    async function handleRequest(requestId, action) {
        const fd = new FormData();
        fd.append('request_id', requestId);
        fd.append('action', action);

        try {
            const res = await fetch('/api/firms/handle-request', { method: 'POST', body: fd });
            const data = await res.json();
            if (data.success) location.reload();
            else alert(data.message);
        } catch (e) {
            alert('Action failed.');
        }
    }
</script>

<style>
    .vault-item { padding: 1.5rem; background: rgba(255,255,255,0.05); border-radius: 12px; }
    .bg-dark { background-color: #0f172a !important; }
</style>
