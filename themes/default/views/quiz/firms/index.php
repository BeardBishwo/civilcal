

<div class="container py-5">
    <div class="row mb-5 text-center">
        <div class="col-12">
            <h1 class="display-4 font-weight-bold text-gradient">Engineering Firms</h1>
            <p class="lead text-muted">Join an elite firm or start your own to build mega projects together.</p>
        </div>
    </div>

    <?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger mb-4">
        <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
    </div>
    <?php endif; ?>

    <div class="row">
        <!-- Create Firm Section -->
        <div class="col-md-4 mb-4">
            <div class="card shadow-lg border-primary h-100">
                <div class="card-body p-4 text-center">
                    <div class="icon-circle bg-primary-light text-primary mb-3">
                        <i class="fas fa-plus fa-2x"></i>
                    </div>
                    <h4>Start a Firm</h4>
                    <p class="text-muted small">Cost: 5,000 Coins</p>
                    <form action="/api/firms/create" method="POST">
                        <input type="hidden" name="nonce" value="<?php echo htmlspecialchars($createNonce ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        <input type="text" name="trap_answer" id="firm_create_trap" style="display:none" autocomplete="off">
                        <div class="form-group mb-3">
                            <input type="text" name="name" class="form-control" placeholder="Firm Name" required>
                        </div>
                        <div class="form-group mb-4">
                            <textarea name="description" class="form-control" placeholder="Description..." rows="2"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block btn-lg">Founder's License</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Discovery List -->
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white font-weight-bold">
                    🛡️ Active Firms
                </div>
                <div class="list-group list-group-flush">
                    <?php if (empty($firms)): ?>
                    <div class="p-5 text-center text-muted">
                        No firms found. Be the first to start one!
                    </div>
                    <?php else: ?>
                    <?php foreach ($firms as $f): ?>
                    <div class="list-group-item d-flex justify-content-between align-items-center p-4">
                        <div class="d-flex align-items-center">
                            <div class="firm-logo mr-4">
                                <img src="<?php echo $f['logo_url'] ?: 'https://cdn-icons-png.flaticon.com/512/1063/1063376.png'; ?>" width="50">
                            </div>
                            <div>
                                <h5 class="mb-0 font-weight-bold"><?php echo htmlspecialchars($f['name']); ?></h5>
                                <div class="badge badge-secondary">Lvl <?php echo $f['level']; ?></div>
                                <span class="text-muted small ml-2"><?php echo $f['member_count']; ?> Members</span>
                            </div>
                        </div>
                        <button class="btn btn-outline-primary rounded-pill px-4" onclick="requestJoin(<?php echo $f['id']; ?>)">Request Join</button>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const joinNonce = '<?php echo htmlspecialchars($joinNonce ?? '', ENT_QUOTES, 'UTF-8'); ?>';
    function getTrap() {
        return document.getElementById('firm_create_trap') ? document.getElementById('firm_create_trap').value : '';
    }

    async function requestJoin(guildId) {
        const fd = new FormData();
        fd.append('guild_id', guildId);
        fd.append('nonce', joinNonce);
        fd.append('trap_answer', getTrap());

        try {
            const res = await fetch('/api/firms/join', { method: 'POST', body: fd });
            const data = await res.json();
            alert(data.message);
        } catch (e) {
            alert('Request failed.');
        }
    }
</script>

<style>
    .text-gradient { background: linear-gradient(135deg, #10b981, #3b82f6); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
    .icon-circle { width: 70px; height: 70px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; }
    .bg-primary-light { background: rgba(59, 130, 246, 0.1); }
</style>
