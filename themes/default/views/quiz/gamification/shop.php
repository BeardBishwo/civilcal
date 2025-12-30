<?php
/**
 * Pashupati Nath Temple Market - Overhauled for Premium UX
 */
$economyResources = \App\Services\SettingsService::get('economy_resources', []);
$coinConfig = $economyResources['coins'] ?? ['name' => 'BB Coins', 'icon' => 'themes/default/assets/resources/currency/coin.webp'];
?>

<div class="market-container">
    <!-- Premium Header -->
    <div class="market-header text-center">
        <div class="header-icon-float">🛕</div>
        <h1 class="market-title">Pashupati Nath Temple Market</h1>
        <p class="market-subtitle">Sacred trading grounds for engineering artifacts and construction materials.</p>
    </div>

    <!-- Dynamic Wallet Display -->
    <div class="wallet-section">
        <div class="wallet-card">
            <div class="wallet-info">
                <span class="wallet-label text-uppercase">Treasury Capital</span>
                <div class="wallet-balance">
                    <img src="<?php echo app_base_url($coinConfig['icon']); ?>" class="coin-glow">
                    <span id="current-coins" class="counter-value"><?php echo number_format($wallet['coins']); ?></span>
                    <span class="coin-name"><?php echo htmlspecialchars($coinConfig['name']); ?></span>
                </div>
            </div>
            <div class="wallet-visual">
                <i class="fas fa-gopuram"></i>
            </div>
        </div>
    </div>

    <!-- Market Navigation -->
    <div class="market-nav">
        <button class="nav-item active" onclick="switchMarketTab(event, 'lifelines')">
            <i class="fas fa-magic"></i> Artifacts
        </button>
        <button class="nav-item" onclick="switchMarketTab(event, 'materials')">
            <i class="fas fa-gem"></i> Materials
        </button>
        <button class="nav-item" onclick="switchMarketTab(event, 'bundles')">
            <i class="fas fa-box-open"></i> Bundles
        </button>
        <?php if (!empty($cashPacks)): ?>
        <button class="nav-item" onclick="switchMarketTab(event, 'cash')">
            <i class="fas fa-dollar-sign"></i> Premium
        </button>
        <?php endif; ?>
    </div>

    <div class="market-content">
        <!-- Lifelines Tab -->
        <div id="lifelines" class="market-tab active">
            <div class="item-grid">
                <?php 
                $lifelineData = [
                    ['id' => '50_50', 'name' => '50/50 Artifact', 'desc' => 'Sacrifice coins to remove two incorrect paths.', 'cost' => 100, 'icon' => 'fas fa-divide', 'color' => 'blue'],
                    ['id' => 'ai_hint', 'name' => 'AI Oracle Hint', 'desc' => 'Consult the digital deity for a strategic clue.', 'cost' => 200, 'icon' => 'fas fa-brain', 'color' => 'gold'],
                    ['id' => 'freeze_time', 'name' => 'Temporal Stasis', 'desc' => 'Freeze the sands of time for 30 seconds.', 'cost' => 300, 'icon' => 'fas fa-snowflake', 'color' => 'cyan']
                ];
                foreach ($lifelineData as $item): 
                    $owned = $inventory[$item['id']] ?? 0;
                ?>
                <div class="premium-card lifeline-card" data-color="<?= $item['color'] ?>">
                    <div class="card-inner">
                        <div class="item-icon">
                            <i class="<?= $item['icon'] ?>"></i>
                        </div>
                        <h3 class="item-name"><?= $item['name'] ?></h3>
                        <p class="item-desc"><?= $item['desc'] ?></p>
                        <div class="item-stock">Owned: <span><?= $owned ?></span></div>
                        <button class="buy-button" onclick="buyLifeline('<?= $item['id'] ?>', <?= $item['cost'] ?>)">
                            <span><?= $item['cost'] ?></span>
                            <img src="<?= app_base_url($coinConfig['icon']) ?>" width="18">
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Materials Tab -->
        <div id="materials" class="market-tab">
            <div class="item-grid">
                <?php 
                foreach ($economyResources as $id => $res): 
                    if ($id === 'coins' || ($res['buy'] <= 0 && $res['sell'] <= 0)) continue;
                    $owned = $wallet[$id] ?? 0;
                ?>
                <div class="premium-card material-card">
                    <div class="card-inner">
                        <div class="material-preview">
                            <img src="<?= app_base_url($res['icon']) ?>" class="<?= $res['buy'] > 50 ? 'rare-glow' : '' ?>">
                        </div>
                        <h3 class="item-name"><?= htmlspecialchars($res['name']) ?></h3>
                        <div class="item-stock">Inventory: <span><?= number_format($owned) ?></span></div>
                        
                        <div class="qty-control">
                            <button onclick="changeQty('<?= $id ?>', -1)">-</button>
                            <input type="number" id="qty-<?= $id ?>" value="1" min="1">
                            <button onclick="changeQty('<?= $id ?>', 1)">+</button>
                        </div>

                        <div class="trade-actions">
                            <?php if ($res['buy'] > 0): ?>
                            <button class="trade-btn buy" onclick="tradeMaterial('buy', '<?= $id ?>', <?= $res['buy'] ?>)">
                                BUY (<?= $res['buy'] ?>)
                            </button>
                            <?php endif; ?>
                            <?php if ($res['sell'] > 0): ?>
                            <button class="trade-btn sell" onclick="tradeMaterial('sell', '<?= $id ?>', <?= $res['sell'] ?>)" <?= $owned <= 0 ? 'disabled' : '' ?>>
                                SELL (<?= $res['sell'] ?>)
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Bundles Tab -->
        <div id="bundles" class="market-tab">
            <div class="bundles-header">
                <h2><i class="fas fa-box-open"></i> Special Bundle Offers</h2>
                <p>Save coins with bulk purchases!</p>
            </div>
            <div class="item-grid">
                <?php foreach ($bundles as $key => $bundle): ?>
                <div class="premium-card bundle-card">
                    <div class="savings-badge">Save <?= $bundle['savings'] ?> Coins!</div>
                    <div class="card-inner">
                        <div class="material-preview">
                            <img src="<?= app_base_url($bundle['icon']) ?>" class="rare-glow">
                        </div>
                        <h3 class="item-name"><?= htmlspecialchars($bundle['name']) ?></h3>
                        <p class="bundle-desc"><?= htmlspecialchars($bundle['description']) ?></p>
                        <div class="bundle-details">
                            <span class="qty-badge"><?= $bundle['qty'] ?>x <?= ucfirst($bundle['resource']) ?></span>
                        </div>
                        <button class="buy-button bundle-buy" onclick="buyBundle('<?= $key ?>', <?= $bundle['buy'] ?>)">
                            <span><?= $bundle['buy'] ?></span>
                            <img src="<?= app_base_url($coinConfig['icon']) ?>" width="18">
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Cash Packs Tab -->
        <?php if (!empty($cashPacks)): ?>
        <div id="cash" class="market-tab">
            <div class="bundles-header">
                <h2><i class="fas fa-gem"></i> Premium Coin Packs</h2>
                <p>Support the game and boost your treasury!</p>
            </div>
            <div class="item-grid">
                <?php foreach ($cashPacks as $key => $pack): ?>
                <div class="premium-card cash-card <?= $pack['popular'] ? 'popular' : '' ?>">
                    <?php if ($pack['popular']): ?>
                    <div class="popular-badge">BEST VALUE</div>
                    <?php endif; ?>
                    <div class="card-inner">
                        <div class="material-preview">
                            <img src="<?= app_base_url($pack['icon']) ?>">
                        </div>
                        <h3 class="item-name"><?= htmlspecialchars($pack['name']) ?></h3>
                        <p class="bundle-desc"><?= htmlspecialchars($pack['description']) ?></p>
                        <div class="cash-amount"><?= number_format($pack['coins']) ?> Coins</div>
                        <button class="buy-button cash-buy" onclick="alert('Payment integration coming soon!')">
                            $<?= number_format($pack['price_usd'], 2) ?> USD
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<style>
@import url('https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&family=Outfit:wght@300;400;600;700&display=swap');

.market-container {
    max-width: 1200px;
    margin: 40px auto;
    padding: 0 20px;
    font-family: 'Outfit', sans-serif;
    color: #1a1a1a;
}

/* Header Styling */
.market-header { margin-bottom: 50px; }
.header-icon-float { font-size: 4rem; animation: float 3s ease-in-out infinite; }
.market-title { font-family: 'Cinzel', serif; font-size: 2.8rem; font-weight: 700; background: linear-gradient(135deg, #b8860b 0%, #daa520 50%, #b8860b 100%); -webkit-background-clip: text; background-clip: text; -webkit-text-fill-color: transparent; margin-bottom: 10px; }
.market-subtitle { font-size: 1.1rem; color: #666; max-width: 600px; margin: 0 auto; }

/* Wallet Section */
.wallet-section { margin-bottom: 40px; display: flex; justify-content: center; }
.wallet-card { background: #0f172a; color: white; border-radius: 20px; padding: 25px 40px; display: flex; align-items: center; gap: 40px; box-shadow: 0 20px 40px rgba(0,0,0,0.2), inset 0 1px 1px rgba(255,255,255,0.1); position: relative; overflow: hidden; }
.wallet-label { font-size: 0.8rem; letter-spacing: 2px; color: #94a3b8; display: block; margin-bottom: 5px; }
.wallet-balance { display: flex; align-items: center; gap: 15px; }
.wallet-balance img { width: 45px; height: 45px; filter: drop-shadow(0 0 10px rgba(218, 165, 32, 0.5)); }
.counter-value { font-size: 2.5rem; font-weight: 700; }
.coin-name { font-size: 1rem; color: #facc15; font-weight: 600; align-self: flex-end; margin-bottom: 10px; }
.wallet-visual { font-size: 4rem; opacity: 0.1; position: absolute; right: -10px; bottom: -10px; transform: rotate(-15deg); }

/* Navigation */
.market-nav { border-bottom: 2px solid #e2e8f0; display: flex; justify-content: center; gap: 20px; margin-bottom: 40px; }
.nav-item { background: none; border: none; padding: 15px 30px; font-size: 1.1rem; font-weight: 600; color: #64748b; cursor: pointer; border-bottom: 3px solid transparent; transition: 0.3s; display: flex; align-items: center; gap: 10px; }
.nav-item:hover { color: #1e293b; }
.nav-item.active { color: #b8860b; border-bottom-color: #b8860b; }

/* Cards Grid */
.item-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 30px; }
.premium-card { background: white; border-radius: 24px; padding: 3px; position: relative; transition: 0.4s cubic-bezier(0.4, 0, 0.2, 1); box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
.premium-card:hover { transform: translateY(-10px); box-shadow: 0 20px 40px rgba(0,0,0,0.12); }
.card-inner { background: white; border-radius: 21px; padding: 30px; height: 100%; display: flex; flex-direction: column; align-items: center; text-align: center; }

/* Lifeline Specifics */
.lifeline-card[data-color="blue"] { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
.lifeline-card[data-color="gold"] { background: linear-gradient(135deg, #f59e0b, #b45309); }
.lifeline-card[data-color="cyan"] { background: linear-gradient(135deg, #06b6d4, #0e7490); }

.item-icon { width: 70px; height: 70px; background: #f8fafc; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; margin-bottom: 20px; color: #1e293b; }
.item-name { font-size: 1.4rem; font-weight: 700; margin-bottom: 12px; }
.item-desc { font-size: 0.95rem; color: #64748b; margin-bottom: 20px; line-height: 1.5; }
.item-stock { font-size: 0.85rem; padding: 6px 16px; background: #f1f5f9; border-radius: 50px; font-weight: 600; margin-bottom: 25px; }
.buy-button { width: 100%; background: #1e293b; color: white; border: none; padding: 14px; border-radius: 14px; font-weight: 700; display: flex; align-items: center; justify-content: center; gap: 10px; cursor: pointer; transition: 0.3s; margin-top: auto; }
.buy-button:hover { background: #0f172a; transform: scale(1.02); }

/* Material Specifics */
.material-preview img { width: 120px; height: 120px; object-fit: contain; margin-bottom: 20px; transition: 0.3s; }
.rare-glow { filter: drop-shadow(0 0 15px rgba(59, 130, 246, 0.4)); }
.qty-control { display: flex; align-items: center; gap: 5px; margin-bottom: 25px; }
.qty-control button { width: 36px; height: 36px; border-radius: 12px; border: 1px solid #e2e8f0; background: white; cursor: pointer; font-weight: 700; }
.qty-control input { width: 60px; text-align: center; border: 1px solid #e2e8f0; border-radius: 12px; padding: 7px; font-weight: 700; -moz-appearance: textfield; -webkit-appearance: none; appearance: none; }
.qty-control input::-webkit-outer-spin-button, .qty-control input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }

.trade-actions { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; width: 100%; }
.trade-btn { padding: 12px; border-radius: 12px; font-weight: 700; cursor: pointer; transition: 0.3s; }
.trade-btn.buy { background: #daa520; border: none; color: #1e293b; }
.trade-btn.sell { background: white; border: 2px solid #1e293b; color: #1e293b; }
.trade-btn:hover:not(:disabled) { transform: translateY(-2px); filter: brightness(1.1); }
.trade-btn:disabled { opacity: 0.4; cursor: not-allowed; }

/* Bundle & Cash Pack Specifics */
.bundles-header { text-align: center; margin-bottom: 40px; }
.bundles-header h2 { font-size: 2rem; font-weight: 700; color: #1e293b; margin-bottom: 10px; }
.bundles-header p { color: #64748b; font-size: 1.1rem; }

.bundle-card, .cash-card { position: relative; }
.savings-badge { position: absolute; top: -10px; right: -10px; background: linear-gradient(135deg, #10b981, #059669); color: white; padding: 8px 16px; border-radius: 20px; font-weight: 700; font-size: 0.85rem; z-index: 10; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4); }
.popular-badge { position: absolute; top: -10px; left: 50%; transform: translateX(-50%); background: linear-gradient(135deg, #f59e0b, #d97706); color: white; padding: 8px 20px; border-radius: 20px; font-weight: 700; font-size: 0.85rem; z-index: 10; box-shadow: 0 4px 12px rgba(245, 158, 11, 0.4); }

.bundle-desc { font-size: 0.9rem; color: #64748b; margin-bottom: 15px; line-height: 1.4; }
.bundle-details { margin-bottom: 20px; }
.qty-badge { background: #eff6ff; color: #2563eb; padding: 8px 16px; border-radius: 12px; font-weight: 700; font-size: 0.9rem; }

.cash-amount { font-size: 2rem; font-weight: 800; color: #daa520; margin-bottom: 20px; }
.cash-buy { background: linear-gradient(135deg, #10b981, #059669); }
.cash-buy:hover { background: linear-gradient(135deg, #059669, #047857); }
.cash-card.popular { border: 3px solid #f59e0b; }

/* Animations */
@keyframes float { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-15px); } }

@media (max-width: 768px) {
    .market-title { font-size: 2rem; }
    .wallet-card { flex-direction: column; text-align: center; gap: 20px; padding: 25px; }
    .wallet-visual { display: none; }
    .counter-value { font-size: 2rem; }
}
</style>

<script>
// Nonce + honeypot
let shopNonce = '<?php echo isset($shopNonce) ? htmlspecialchars($shopNonce, ENT_QUOTES, 'UTF-8') : ''; ?>';
const shopTrap = document.createElement('input');
shopTrap.type = 'text';
shopTrap.name = 'trap_answer';
shopTrap.id = 'shop_trap';
shopTrap.autocomplete = 'off';
shopTrap.style.display = 'none';
document.body.appendChild(shopTrap);

function switchMarketTab(evt, tabName) {
    document.querySelectorAll('.market-tab').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.nav-item').forEach(t => t.classList.remove('active'));
    document.getElementById(tabName).classList.add('active');
    evt.currentTarget.classList.add('active');
}

function changeQty(id, delta) {
    const input = document.getElementById(`qty-${id}`);
    const newVal = parseInt(input.value) + delta;
    if (newVal >= 1) input.value = newVal;
}

async function buyLifeline(type, cost) {
    if (!confirm(`Manifest ${type.replace('_', ' ')} for ${cost} Coins?`)) return;
    performTransaction('/api/shop/purchase', { type, csrf_token: '<?= csrf_token() ?>' });
}

async function tradeMaterial(action, resource, price) {
    const qty = parseInt(document.getElementById(`qty-${resource}`).value);
    const total = qty * price;
    const url = action === 'buy' ? '/api/shop/purchase-resource' : '/api/shop/sell-resource';
    
    if (!confirm(`${action === 'buy' ? 'Procure' : 'Relinquish'} ${qty} units for ${total} Coins?`)) return;

    performTransaction(url, { resource, amount: qty, csrf_token: '<?= csrf_token() ?>' });
}

async function buyBundle(bundleKey, cost) {
    if (!confirm(`Purchase this bundle for ${cost} Coins?`)) return;
    performTransaction('/api/shop/purchase-bundle', { bundle: bundleKey, csrf_token: '<?= csrf_token() ?>' });
}

async function performTransaction(url, data) {
    try {
        const fd = new FormData();
        for (const [key, val] of Object.entries(data)) fd.append(key, val);
        fd.append('nonce', shopNonce);
        fd.append('trap_answer', document.getElementById('shop_trap').value || '');

        const res = await fetch(app_base_url(url.substring(1)), { method: 'POST', body: fd });
        const result = await res.json();
        
        if (result.success) {
            if (result.nonce) shopNonce = result.nonce;
            location.reload();
        } else {
            alert("Oracle says: " + result.message);
        }
    } catch (e) {
        alert("A mystical error occurred: " + e.message);
    }
}
</script>
