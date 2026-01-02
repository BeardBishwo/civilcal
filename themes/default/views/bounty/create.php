<?php
// themes/default/views/bounty/create.php
require_once __DIR__ . '/../partials/header.php';
?>
<style>
    .b-shell { max-width: 960px; margin: 0 auto; padding: 32px 16px; color: #e8edf5; }
    .b-card { background: rgba(21,26,38,0.78); border: 1px solid rgba(255,255,255,0.08); border-radius: 18px; box-shadow: 0 10px 30px rgba(0,0,0,0.35); padding: 32px; }
    .b-label { display: block; margin-bottom: 8px; font-size: 13px; color: #cbd5e1; font-weight: 600; letter-spacing: 0.5px; }
    .b-input, .b-textarea { width: 100%; background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.12); color: #e8edf5; border-radius: 12px; padding: 14px 16px; font-size: 15px; transition: all 0.2s; }
    .b-input:focus, .b-textarea:focus { background: rgba(255,255,255,0.08); border-color: #fbbf24; outline: none; box-shadow: 0 0 0 4px rgba(251, 191, 36, 0.1); }
    .b-textarea { min-height: 140px; resize: vertical; line-height: 1.6; }
    .b-helper { font-size: 12px; color: #94a3b8; margin-top: 6px; }
    
    .b-header { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 24px; padding-bottom: 24px; border-bottom: 1px solid rgba(255,255,255,0.1); }
    .b-title { font-size: 28px; font-weight: 800; color: #fff; background: linear-gradient(135deg, #fff 0%, #cbd5e1 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
    .b-balance { text-align: right; }
    .b-coin-pill { display: inline-flex; align-items: center; gap: 8px; background: rgba(234, 179, 8, 0.15); border: 1px solid rgba(234, 179, 8, 0.3); padding: 8px 16px; border-radius: 100px; color: #fbbf24; font-weight: 700; }
    
    .b-notice { background: rgba(234, 179, 8, 0.1); border-left: 4px solid #f59e0b; padding: 16px; border-radius: 8px; margin-bottom: 32px; display: flex; gap: 12px; }
    .b-notice i { color: #f59e0b; font-size: 20px; margin-top: 2px; }
    .b-notice p { color: #e2e8f0; font-size: 14px; line-height: 1.5; margin: 0; }
    
    .b-btn-primary { width: 100%; background: linear-gradient(135deg, #d97706 0%, #b45309 100%); color: white; border: none; padding: 16px; border-radius: 14px; font-weight: 700; font-size: 16px; cursor: pointer; transition: transform 0.2s, box-shadow 0.2s; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.2); }
    .b-btn-primary:hover { transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(180, 83, 9, 0.4); }
    .b-btn-primary:active { transform: translateY(0); }
    .b-btn-primary:disabled { opacity: 0.7; cursor: not-allowed; transform: none; }
</style>

<div class="b-shell">
    <div class="b-card">
        <div class="b-header">
            <div>
                <h1 class="b-title">Post a Bounty</h1>
                <div style="color: #94a3b8; font-size: 14px; margin-top: 4px;">Request custom blueprints from our engineering community</div>
            </div>
            <div class="b-balance">
                <div style="font-size: 12px; color: #94a3b8; margin-bottom: 6px;">Your Balance</div>
                <div class="b-coin-pill">
                    <img src="<?= app_base_url('/themes/default/assets/resources/currency/coin.webp') ?>" alt="Coins" style="width: 20px; height: 20px;">
                    <span><?= number_format($data['coins']) ?> Coins</span>
                </div>
            </div>
        </div>

        <div class="b-notice">
            <i class="fas fa-sack-dollar"></i>
            <div>
                <strong>Escrow Protection:</strong>
                <p>The bounty amount will be securely locked when you post. Funds are only released to the engineer once you review and accept their submission. You can cancel unanswered bounties at any time for a full refund.</p>
            </div>
        </div>

        <form id="create-bounty-form" class="space-y-6">
            <div style="margin-bottom: 24px;">
                <label class="b-label">Project Title <span style="color:#ef4444">*</span></label>
                <input type="text" name="title" required class="b-input" placeholder="e.g., Structural Analysis for 2-Story Residential Building">
            </div>

            <div style="margin-bottom: 24px;">
                <label class="b-label">Detailed Requirements <span style="color:#ef4444">*</span></label>
                <textarea name="description" required class="b-textarea" placeholder="Describe your request in detail. Include specific requirements like:
- Preferred software (AutoCAD, ETABS, SketchUp)
- File formats needed (DWG, PDF, XLS of calculations)
- Building codes to follow (e.g., NBC 105:2020)
- Site location or specific constraints"></textarea>
            </div>

            <div style="margin-bottom: 32px;">
                <label class="b-label">Bounty Amount (Coins) <span style="color:#ef4444">*</span></label>
                <div style="position: relative;">
                    <img src="<?= app_base_url('/themes/default/assets/resources/currency/coin.webp') ?>" alt="Coins" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); width: 20px; height: 20px;">
                    <input type="number" name="amount" min="10" required class="b-input" style="padding-left: 48px; font-weight: 700; color: #fbbf24; font-size: 18px;" placeholder="500">
                </div>
                <div class="b-helper">Minimum 10 coins. Higher bounties attract better talent faster.</div>
            </div>

            <button type="submit" id="submit-btn" class="b-btn-primary">
                <i class="fas fa-lock" style="margin-right: 8px; opacity: 0.8;"></i> 
                Lock Coins & Post Bounty
            </button>
        </form>
    </div>
</div>

<script>
document.getElementById('create-bounty-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = document.getElementById('submit-btn');
    const originalText = btn.innerHTML;
    
    // Basic validation
    const amount = this.querySelector('[name=amount]').value;
    const currentCoins = <?= $data['coins'] ?>;
    
    if(parseInt(amount) > currentCoins) {
        alert('Insufficient balance! You need more coins to post this bounty.');
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';

    const jsonData = {
        title: this.querySelector('[name=title]').value,
        description: this.querySelector('[name=description]').value,
        amount: amount
    };

    fetch('/api/bounty/create', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(jsonData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Success animation or redirect
            const btn = document.getElementById('submit-btn');
            btn.style.background = '#22c55e';
            btn.innerHTML = '<i class="fas fa-check"></i> Success!';
            setTimeout(() => {
                window.location.href = '/bounty';
            }, 1000);
        } else {
            alert('Error: ' + data.message);
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    })
    .catch(err => {
        console.error(err);
        alert('Failed. Please try again.');
        btn.disabled = false;
        btn.innerHTML = originalText;
    });
});
</script>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
