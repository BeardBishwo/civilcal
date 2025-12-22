<style>
:root {
    --m-primary: #0ea5e9;
    --m-secondary: #10b981;
    --m-accent: #8b5cf6;
    --m-bg: #0f172a;
    --m-card-bg: rgba(30, 41, 59, 0.7);
    --m-border: rgba(255, 255, 255, 0.1);
    --m-glass: blur(12px) saturate(180%);
}

.marketplace-wrapper {
    background: var(--m-bg);
    padding: 2rem;
    border-radius: 20px;
    color: #f8fafc;
    min-height: calc(100vh - 120px);
    font-family: 'Inter', system-ui, -apple-system, sans-serif;
}

.marketplace-header {
    position: relative;
    background: radial-gradient(circle at top right, rgba(14, 165, 233, 0.15), transparent),
                radial-gradient(circle at bottom left, rgba(139, 92, 246, 0.15), transparent);
    padding: 3rem 2rem;
    border-radius: 24px;
    margin-bottom: 3rem;
    border: 1px solid var(--m-border);
    overflow: hidden;
    text-align: center;
}

.marketplace-header::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle at center, rgba(16, 185, 129, 0.05), transparent 70%);
    animation: pulse 10s infinite alternate;
    z-index: 0;
}

.marketplace-header h1 {
    font-size: 3rem;
    font-weight: 800;
    margin: 0;
    background: linear-gradient(to right, #fff, #94a3b8);
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
    position: relative;
    z-index: 1;
}

.marketplace-header p {
    font-size: 1.25rem;
    color: #94a3b8;
    margin: 1rem 0 0 0;
    position: relative;
    z-index: 1;
}

.marketplace-nav {
    display: inline-flex;
    background: var(--m-card-bg);
    backdrop-filter: var(--m-glass);
    padding: 0.5rem;
    border-radius: 9999px;
    border: 1px solid var(--m-border);
    margin-bottom: 3rem;
    position: sticky;
    top: 20px;
    z-index: 100;
    left: 50%;
    transform: translateX(-50%);
}

.m-nav-item {
    padding: 0.75rem 2rem;
    border-radius: 9999px;
    cursor: pointer;
    font-weight: 600;
    font-size: 0.95rem;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    color: #94a3b8;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.m-nav-item:hover {
    color: #fff;
}

.m-nav-item.active {
    background: var(--m-primary);
    color: white;
    box-shadow: 0 0 20px rgba(14, 165, 233, 0.4);
}

.items-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 2rem;
}

.m-card {
    background: var(--m-card-bg);
    backdrop-filter: var(--m-glass);
    border: 1px solid var(--m-border);
    border-radius: 24px;
    overflow: hidden;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    display: flex;
    flex-direction: column;
}

.m-card:hover {
    transform: translateY(-8px);
    border-color: rgba(14, 165, 233, 0.4);
    box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.5);
}

.m-card-preview {
    height: 180px;
    position: relative;
    background: #1e293b;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}

.m-card-preview::after {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(to bottom, transparent, rgba(15, 23, 42, 0.8));
}

.m-card-preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.m-card:hover .m-card-preview img {
    transform: scale(1.1);
}

.m-card-icon {
    font-size: 3rem;
    color: rgba(148, 163, 184, 0.3);
    position: relative;
    z-index: 1;
}

.m-card-body {
    padding: 1.5rem;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.m-type-badge {
    display: inline-flex;
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.7rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 1rem;
}

.badge-script { background: rgba(139, 92, 246, 0.1); color: #a78bfa; border: 1px solid rgba(139, 92, 246, 0.2); }
.badge-plugin { background: rgba(16, 185, 129, 0.1); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.2); }
.badge-theme { background: rgba(12, 165, 233, 0.1); color: #38bdf8; border: 1px solid rgba(12, 165, 233, 0.2); }

.m-item-title {
    font-size: 1.25rem;
    font-weight: 700;
    margin: 0 0 0.5rem 0;
    color: #fff;
}

.m-item-desc {
    font-size: 0.9rem;
    color: #94a3b8;
    line-height: 1.5;
    margin-bottom: 2rem;
    flex: 1;
}

.m-card-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: auto;
}

.m-price {
    font-size: 1.5rem;
    font-weight: 800;
    color: #fff;
}

.m-btn-purchase {
    background: #fff;
    color: #0f172a;
    padding: 0.75rem 1.5rem;
    border-radius: 12px;
    font-weight: 700;
    font-size: 0.9rem;
    text-decoration: none !important;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.m-btn-purchase:hover {
    background: var(--m-primary);
    color: #fff;
    transform: scale(1.05);
    box-shadow: 0 0 20px rgba(14, 165, 233, 0.3);
}

.empty-state {
    text-align: center;
    padding: 4rem;
    background: var(--m-card-bg);
    border-radius: 24px;
    border: 1px dashed var(--m-border);
    margin-top: 2rem;
}
</style>

<div class="marketplace-wrapper">
    <div class="marketplace-header">
        <h1>🛍️ Marketplace</h1>
        <p>Premium solutions to supercharge your platform</p>
    </div>

    <div style="text-align: center;">
        <div class="marketplace-nav">
            <div class="m-nav-item active" data-type="all">
                <i class="fas fa-layer-group"></i> All Items
            </div>
            <?php foreach ($categories as $cat): ?>
                <div class="m-nav-item" data-type="<?php echo $cat['id']; ?>">
                    <i class="<?php echo $cat['icon']; ?>"></i> <?php echo $cat['name']; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="items-grid" id="marketplace-grid">
        <?php if (!empty($items)): ?>
            <?php foreach ($items as $item): ?>
                <div class="m-card" data-type="<?php echo $item['type']; ?>">
                    <div class="m-card-preview">
                        <?php if (isset($item['preview_image'])): ?>
                            <img src="<?php echo $item['preview_image']; ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" loading="lazy">
                        <?php else: ?>
                            <i class="fas fa-rocket m-card-icon"></i>
                        <?php endif; ?>
                    </div>
                    <div class="m-card-body">
                        <div>
                            <span class="m-type-badge badge-<?php echo $item['type']; ?>"><?php echo $item['type']; ?></span>
                        </div>
                        <h3 class="m-item-title"><?php echo htmlspecialchars($item['name']); ?></h3>
                        <p class="m-item-desc"><?php echo htmlspecialchars($item['description']); ?></p>
                        
                        <div class="m-card-footer">
                            <span class="m-price">$<?php echo number_format($item['price'], 2); ?></span>
                            <a href="javascript:void(0)" class="m-btn-purchase" onclick="purchaseItem(<?php echo $item['id']; ?>, '<?php echo $item['type']; ?>')">
                                Purchase
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-store-slash" style="font-size: 4rem; color: #475569; margin-bottom: 1.5rem;"></i>
                <h3 style="color: #64748b; font-size: 1.5rem;">The store is currently empty</h3>
                <p style="color: #475569;">Check back later for new premium items!</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function purchaseItem(id, type) {
    if (window.AdminApp && typeof window.AdminApp.showNotification === 'function') {
        window.AdminApp.showNotification('Redirecting to checkout for ' + type + '...', 'info');
    } else {
        alert('Redirecting to checkout...');
    }
}

document.querySelectorAll('.m-nav-item').forEach(item => {
    item.addEventListener('click', function() {
        const type = this.dataset.type;
        
        // Update UI
        document.querySelectorAll('.m-nav-item').forEach(nav => nav.classList.remove('active'));
        this.classList.add('active');
        
        // Filter items with smooth transition
        const cards = document.querySelectorAll('.m-card');
        cards.forEach(card => {
            const cardType = card.dataset.type;
            const matches = type === 'all' || cardType === type || (cardType + 's') === type;
            
            if (matches) {
                card.style.display = 'flex';
                setTimeout(() => {
                    card.style.opacity = '1';
                    card.style.transform = 'scale(1)';
                }, 10);
            } else {
                card.style.opacity = '0';
                card.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    card.style.display = 'none';
                }, 300);
            }
        });
    });
});
</script>
