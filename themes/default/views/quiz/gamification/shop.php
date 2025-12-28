

<div class="container py-5">
    <!-- Header -->
    <div class="row mb-5">
        <div class="col-12 text-center">
            <h1 class="display-4 font-weight-bold text-dark mb-2">🛍️ General Store</h1>
            <p class="text-muted lead">Equip yourself with elite engineering tools to dominate the leaderboard.</p>
        </div>
    </div>

    <!-- Wallet & Stats -->
    <div class="row mb-4">
        <div class="col-md-6 mx-auto">
            <div class="card shadow-lg border-0 bg-primary text-white overflow-hidden">
                <div class="card-body p-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase mb-1 opacity-75">Your Balance</h6>
                        <h2 class="mb-0 font-weight-bold" id="current-coins">
                            <i class="fas fa-coins mr-2"></i><?php echo number_format($wallet['coins']); ?> Coins
                        </h2>
                    </div>
                    <div class="text-right">
                        <i class="fas fa-store fa-3x opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Shop Grid -->
    <div class="row">
        <!-- 50/50 -->
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm border-0 shop-card" data-tilt>
                <div class="card-body text-center p-4">
                    <div class="icon-circle bg-light-blue mb-4 mx-auto">
                        <i class="fas fa-divide fa-2x text-primary"></i>
                    </div>
                    <h4 class="font-weight-bold mb-2">50/50</h4>
                    <p class="text-muted small mb-4">instantly removes two incorrect options from a question during a battle.</p>
                    <div class="inventory-badge mb-4">
                        <span class="badge badge-pill badge-light p-2 px-3">
                            <i class="fas fa-box mr-2"></i>In Stock: <strong id="inv-50_50"><?php echo $inventory['50_50']; ?></strong>
                        </span>
                    </div>
                    <button class="btn btn-primary btn-block btn-lg btn-buy" data-type="50_50" data-cost="100">
                        Buy for 100 <i class="fas fa-coins ml-1"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- AI Hint -->
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm border-0 shop-card" data-tilt>
                <div class="card-body text-center p-4">
                    <div class="icon-circle bg-light-green mb-4 mx-auto">
                        <i class="fas fa-brain fa-2x text-success"></i>
                    </div>
                    <h4 class="font-weight-bold mb-2">AI Hint</h4>
                    <p class="text-muted small mb-4">Get a strategic clue from our engineering AI to point you to the right answer.</p>
                    <div class="inventory-badge mb-4">
                        <span class="badge badge-pill badge-light p-2 px-3">
                            <i class="fas fa-box mr-2"></i>In Stock: <strong id="inv-ai_hint"><?php echo $inventory['ai_hint']; ?></strong>
                        </span>
                    </div>
                    <button class="btn btn-success btn-block btn-lg btn-buy" data-type="ai_hint" data-cost="200">
                        Buy for 200 <i class="fas fa-coins ml-1"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Freeze Time -->
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm border-0 shop-card" data-tilt>
                <div class="card-body text-center p-4">
                    <div class="icon-circle bg-light-red mb-4 mx-auto">
                        <i class="fas fa-snowflake fa-2x text-danger"></i>
                    </div>
                    <h4 class="font-weight-bold mb-2">Freeze Time</h4>
                    <p class="text-muted small mb-4">Stops the countdown for 30 seconds, giving you ample time to solve complex calcs.</p>
                    <div class="inventory-badge mb-4">
                        <span class="badge badge-pill badge-light p-2 px-3">
                            <i class="fas fa-box mr-2"></i>In Stock: <strong id="inv-freeze_time"><?php echo $inventory['freeze_time']; ?></strong>
                        </span>
                    </div>
                    <button class="btn btn-danger btn-block btn-lg btn-buy" data-type="freeze_time" data-cost="300">
                        Buy for 300 <i class="fas fa-coins ml-1"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.shop-card { transition: all 0.3s cubic-bezier(.25,.8,.25,1); border: 2px solid transparent !important; }
.shop-card:hover { transform: translateY(-10px); box-shadow: 0 14px 28px rgba(0,0,0,0.25), 0 10px 10px rgba(0,0,0,0.22); border-color: #6a11cb !important; }
.icon-circle { width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; }
.bg-light-blue { background: rgba(52, 152, 219, 0.1); }
.bg-light-green { background: rgba(46, 204, 113, 0.1); }
.bg-light-red { background: rgba(231, 76, 60, 0.1); }
.inventory-badge strong { color: #6a11cb; font-size: 1.1rem; }
.btn-buy { position: relative; overflow: hidden; }
</style>

<script>
document.querySelectorAll('.btn-buy').forEach(btn => {
    btn.addEventListener('click', async function() {
        const type = this.dataset.type;
        const cost = this.dataset.cost;
        const typeLabel = type.replace('_', ' ');
        
        if (!confirm(`Do you want to purchase ${typeLabel} for ${cost} coins?`)) return;

        const originalHtml = this.innerHTML;
        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
        this.disabled = true;

        try {
            const formData = new FormData();
            formData.append('type', type);
            // Replace with actual CSRF token if method exists
            // formData.append('csrf_token', '<?php echo $this->csrfToken(); ?>'); 

            const response = await fetch('/api/shop/purchase', {
                method: 'POST',
                body: formData
            });
            const data = await response.json();

            if (data.success) {
                // Success feedback
                this.innerHTML = '<i class="fas fa-check"></i> Purchased!';
                this.classList.replace('btn-primary', 'btn-success');
                
                // Play coin sound
                if (window.playSfx) playSfx('coins');

                // Update UI counters
                const invEl = document.getElementById(`inv-${type}`);
                invEl.innerText = parseInt(invEl.innerText) + 1;
                
                const coinEl = document.getElementById('current-coins');
                const newBalance = parseInt(coinEl.innerText.replace(/,/g, '')) - parseInt(cost);
                coinEl.innerHTML = `<i class="fas fa-coins mr-2"></i>${newBalance.toLocaleString()} Coins`;

                setTimeout(() => {
                    this.innerHTML = originalHtml;
                    this.classList.add('btn-primary');
                    this.classList.remove('btn-success');
                    this.disabled = false;
                }, 2000);
            } else {
                alert(data.message);
                this.innerHTML = originalHtml;
                this.disabled = false;
            }
        } catch (e) {
            alert('Error connecting to the vault.');
            this.innerHTML = originalHtml;
            this.disabled = false;
        }
    });
});
</script>
