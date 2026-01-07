<?php
/**
 * Gamification: Quiz Shop (Pashupati Nath Market)
 * Premium Dark Mode UI - Refactored
 */
$economyResources = \App\Services\SettingsService::get('economy_resources', []);
$coinConfig = $economyResources['coins'] ?? ['name' => 'BB Coins', 'icon' => 'themes/default/assets/resources/currency/coin.webp'];
?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Market | Pashupati Nath</title>
    <!-- Load Tailwind & General Quiz CSS -->
    <link rel="stylesheet" href="<?php echo app_base_url('themes/default/assets/css/quiz.min.css?v=' . time()); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-background text-white font-sans min-h-screen pb-20" x-data="shopSystem()">

    <!-- Header -->
    <header class="h-16 bg-surface/80 backdrop-blur-md border-b border-white/5 flex items-center justify-between px-6 sticky top-0 z-50">
        <a href="<?php echo app_base_url('quiz'); ?>" class="flex items-center gap-2 text-gray-400 hover:text-white transition-colors text-sm font-medium">
            <i class="fas fa-arrow-left"></i> <span>Portal</span>
        </a>
        <div class="text-right">
            <h1 class="text-lg font-black bg-gradient-to-r from-yellow-400 to-amber-600 bg-clip-text text-transparent leading-none">Pashupati Nath Market</h1>
            <p class="text-[10px] uppercase tracking-widest text-gray-500 font-bold">Sacred Trading</p>
        </div>
    </header>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Hero -->
        <div class="text-center mb-10 relative">
            <div class="w-20 h-20 mb-4 flex items-center justify-center">
                <img src="<?php echo app_base_url('themes/default/assets/resources/buildings/shop.webp'); ?>" class="w-full h-full object-contain drop-shadow-lg">
            </div>
            <h1 class="text-4xl md:text-5xl font-black text-white mb-2 tracking-tight">Temple Market</h1>
            <p class="text-gray-400 text-lg">Trade artifacts, engineering materials, and precious bundles.</p>
        </div>

        <!-- Wallet Card -->
        <div class="max-w-2xl mx-auto mb-12">
            <div class="glass-card p-1 rounded-3xl bg-gradient-to-br from-amber-500/20 via-transparent to-transparent">
                <div class="bg-surface rounded-[20px] p-8 border border-white/10 shadow-2xl relative overflow-hidden">
                    <div class="absolute -right-10 -bottom-10 opacity-5 text-9xl rotate-12 pointer-events-none">
                        <i class="fas fa-coins"></i>
                    </div>
                    
                    <div class="flex items-center justify-between relative z-10">
                        <div>
                            <span class="text-xs font-bold uppercase tracking-widest text-amber-500 mb-1 block">Treasury Balance</span>
                            <div class="flex items-end gap-3">
                                <span class="text-5xl font-black text-white leading-none tracking-tight"><?php echo number_format($wallet['coins']); ?></span>
                                <span class="text-lg font-bold text-gray-400 mb-1"><?php echo htmlspecialchars($coinConfig['name']); ?></span>
                            </div>
                        </div>
                        <div class="w-20 h-20 rounded-2xl bg-amber-500/10 flex items-center justify-center border border-amber-500/20 shadow-lg shadow-amber-500/10">
                            <img src="<?php echo app_base_url($coinConfig['icon']); ?>" class="w-12 h-12 object-contain drop-shadow-md">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <div class="flex flex-wrap justify-center gap-4 mb-10">
            <button @click="tab = 'lifelines'" 
                    class="px-6 py-3 rounded-full font-bold text-sm transition-all border border-transparent"
                    :class="tab === 'lifelines' ? 'bg-primary text-white shadow-lg shadow-primary/25' : 'bg-surface hover:bg-white/5 text-gray-400 hover:text-white border-white/5'">
                <i class="fas fa-magic mr-2"></i> Artifacts
            </button>
            <button @click="tab = 'materials'" 
                    class="px-6 py-3 rounded-full font-bold text-sm transition-all border border-transparent"
                    :class="tab === 'materials' ? 'bg-primary text-white shadow-lg shadow-primary/25' : 'bg-surface hover:bg-white/5 text-gray-400 hover:text-white border-white/5'">
                <i class="fas fa-gem mr-2"></i> Materials
            </button>
            <button @click="tab = 'bundles'" 
                    class="px-6 py-3 rounded-full font-bold text-sm transition-all border border-transparent"
                    :class="tab === 'bundles' ? 'bg-primary text-white shadow-lg shadow-primary/25' : 'bg-surface hover:bg-white/5 text-gray-400 hover:text-white border-white/5'">
                <i class="fas fa-box-open mr-2"></i> Bundles
            </button>
            <?php if (!empty($cashPacks)): ?>
            <button @click="tab = 'cash'" 
                    class="px-6 py-3 rounded-full font-bold text-sm transition-all border border-transparent"
                    :class="tab === 'cash' ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-500/25' : 'bg-surface hover:bg-white/5 text-gray-400 hover:text-white border-white/5'">
                <i class="fas fa-dollar-sign mr-2"></i> Premium
            </button>
            <?php endif; ?>
        </div>

        <!-- Content Area -->
        <div class="min-h-[400px]">
            
            <!-- Lifelines Tab -->
            <div x-show="tab === 'lifelines'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php 
                $lifelineData = [
                    ['id' => '50_50', 'name' => '50/50 Artifact', 'desc' => 'Sacrifice coins to remove two incorrect paths.', 'cost' => 100, 'icon' => 'fas fa-divide', 'color' => 'from-blue-500 to-indigo-600'],
                    ['id' => 'ai_hint', 'name' => 'AI Oracle Hint', 'desc' => 'Consult the digital deity for a strategic clue.', 'cost' => 200, 'icon' => 'fas fa-brain', 'color' => 'from-amber-400 to-orange-500'],
                    ['id' => 'freeze_time', 'name' => 'Temporal Stasis', 'desc' => 'Freeze the sands of time for 30 seconds.', 'cost' => 300, 'icon' => 'fas fa-snowflake', 'color' => 'from-cyan-400 to-blue-500']
                ];
                foreach ($lifelineData as $item): 
                    $owned = $inventory[$item['id']] ?? 0;
                ?>
                <div class="glass-card p-1 rounded-3xl hover:-translate-y-2 transition-transform duration-300 group">
                    <div class="bg-surface rounded-[20px] p-6 h-full flex flex-col border border-white/10">
                        <div class="w-16 h-16 rounded-2xl bg-gradient-to-br <?php echo $item['color']; ?> flex items-center justify-center text-white text-2xl shadow-lg mb-6">
                            <i class="<?php echo $item['icon']; ?>"></i>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-2"><?php echo $item['name']; ?></h3>
                        <p class="text-gray-400 text-sm mb-6 flex-grow"><?php echo $item['desc']; ?></p>
                        
                        <div class="flex items-center justify-between mb-4 bg-white/5 px-4 py-2 rounded-lg">
                            <span class="text-xs uppercase font-bold text-gray-500">Owned</span>
                            <span class="font-mono font-bold text-white"><?php echo $owned; ?></span>
                        </div>

                        <button @click="buyLifeline('<?php echo $item['id']; ?>', <?php echo $item['cost']; ?>, '<?php echo $item['name']; ?>')"
                                class="w-full py-3 bg-white/5 hover:bg-white/10 border border-white/10 rounded-xl font-bold text-white transition-all flex items-center justify-center gap-2 group-hover:border-primary/50 group-hover:text-primary">
                            <span><?php echo $item['cost']; ?></span>
                            <img src="<?php echo app_base_url($coinConfig['icon']); ?>" class="w-5 h-5">
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Materials Tab -->
            <div x-show="tab === 'materials'" style="display: none;" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php 
                foreach ($economyResources as $id => $res): 
                    if ($id === 'coins' || ($res['buy'] <= 0 && $res['sell'] <= 0)) continue;
                    $owned = $wallet[$id] ?? 0;
                ?>
                <div class="glass-card p-4 rounded-3xl" x-data="{ qty: 1 }">
                    <div class="flex flex-col h-full bg-surface rounded-2xl p-4 border border-white/10">
                        <div class="h-40 flex items-center justify-center bg-white/5 rounded-xl mb-4 relative overflow-hidden">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent"></div>
                            <img src="<?php echo app_base_url($res['icon']); ?>" class="h-24 object-contain transition-transform hover:scale-110 duration-300">
                        </div>
                        
                        <h3 class="font-bold text-white text-lg mb-1 truncate"><?php echo htmlspecialchars($res['name']); ?></h3>
                        <div class="text-xs font-mono text-gray-400 mb-4 bg-white/5 inline-block px-2 py-1 rounded">Stock: <?php echo number_format($owned); ?></div>

                        <div class="flex items-center gap-2 mb-4 bg-black/20 p-1 rounded-lg">
                            <button @click="qty = Math.max(1, qty - 1)" class="w-8 h-8 flex items-center justify-center bg-white/10 hover:bg-white/20 rounded text-white font-bold transition-colors">-</button>
                            <input type="number" x-model.number="qty" class="flex-1 bg-transparent text-center font-bold text-white text-sm focus:outline-none" min="1">
                            <button @click="qty++" class="w-8 h-8 flex items-center justify-center bg-white/10 hover:bg-white/20 rounded text-white font-bold transition-colors">+</button>
                        </div>

                        <div class="grid grid-cols-2 gap-2 mt-auto">
                            <?php if ($res['buy'] > 0): ?>
                            <button @click="trade('buy', '<?php echo $id; ?>', <?php echo $res['buy']; ?>, qty, '<?php echo $res['name']; ?>')" 
                                    class="py-2 rounded-lg bg-amber-500 hover:bg-amber-400 text-black font-bold text-xs transition-colors">
                                BUY <?php echo $res['buy']; ?>
                            </button>
                            <?php endif; ?>
                            <?php if ($res['sell'] > 0): ?>
                            <button @click="trade('sell', '<?php echo $id; ?>', <?php echo $res['sell']; ?>, qty, '<?php echo $res['name']; ?>')" 
                                    class="py-2 rounded-lg bg-white/5 hover:bg-white/10 border border-white/10 text-white font-bold text-xs transition-colors"
                                    :disabled="<?php echo $owned; ?> <= 0">
                                SELL <?php echo $res['sell']; ?>
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Bundles Tab -->
            <div x-show="tab === 'bundles'" style="display: none;" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($bundles as $key => $bundle): ?>
                <div class="glass-card p-1 rounded-3xl relative">
                    <div class="absolute -top-3 -right-3 bg-green-500 text-black text-xs font-black uppercase py-1 px-3 rounded-full shadow-lg z-10">
                        Save <?php echo $bundle['savings']; ?>!
                    </div>
                    <div class="bg-surface rounded-[20px] p-6 h-full flex flex-col border border-white/10">
                        <div class="h-32 flex items-center justify-center mb-6">
                             <img src="<?php echo app_base_url($bundle['icon']); ?>" class="h-24 object-contain drop-shadow-[0_0_15px_rgba(255,255,255,0.2)]">
                        </div>
                        <h3 class="text-xl font-bold text-white mb-2"><?php echo htmlspecialchars($bundle['name']); ?></h3>
                        <p class="text-sm text-gray-400 mb-6 flex-grow"><?php echo htmlspecialchars($bundle['description']); ?></p>
                        
                        <div class="bg-blue-500/10 text-blue-400 text-xs font-bold px-3 py-2 rounded-lg mb-6 inline-block w-max">
                            <?php echo $bundle['qty']; ?>x <?php echo ucfirst($bundle['resource']); ?>
                        </div>

                        <button @click="buyBundle('<?php echo $key; ?>', <?php echo $bundle['buy']; ?>, '<?php echo htmlspecialchars($bundle['name']); ?>')"
                                class="w-full py-3 bg-gradient-to-r from-primary to-accent hover:opacity-90 rounded-xl font-bold text-white transition-opacity flex items-center justify-center gap-2 shadow-lg shadow-primary/20">
                            <span><?php echo $bundle['buy']; ?></span>
                             <img src="<?php echo app_base_url($coinConfig['icon']); ?>" class="w-5 h-5">
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Cash Packs -->
             <?php if (!empty($cashPacks)): ?>
             <div x-show="tab === 'cash'" style="display: none;" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($cashPacks as $key => $pack): ?>
                <div class="glass-card p-1 rounded-3xl relative border border-emerald-500/20">
                    <?php if ($pack['popular']): ?>
                    <div class="absolute -top-3 left-1/2 -translate-x-1/2 bg-amber-500 text-black text-xs font-black uppercase py-1 px-4 rounded-full shadow-lg z-10">
                        Best Value
                    </div>
                    <?php endif; ?>
                    <div class="bg-surface rounded-[20px] p-6 h-full flex flex-col items-center text-center">
                        <img src="<?php echo app_base_url($pack['icon']); ?>" class="h-28 object-contain mb-6 drop-shadow-xl">
                        <h3 class="text-2xl font-black text-white mb-1"><?php echo number_format($pack['coins']); ?> Coins</h3>
                        <p class="text-gray-400 text-sm mb-6"><?php echo htmlspecialchars($pack['description']); ?></p>
                        
                        <button class="mt-auto w-full py-3 bg-emerald-600 hover:bg-emerald-500 rounded-xl font-bold text-white transition-colors shadow-lg shadow-emerald-600/20">
                            $<?php echo number_format($pack['price_usd'], 2); ?> USD
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
             </div>
             <?php endif; ?>

        </div>
    </div>

    <!-- Honeypot -->
    <input type="text" id="shop_trap" name="trap_answer" style="display:none;" autocomplete="off">

    <script>
        function shopSystem() {
            return {
                tab: 'lifelines',
                
                async buyLifeline(type, cost, name) {
                    const result = await Swal.fire({
                        title: 'Confirm Purchase',
                        html: `Purchase <b>${name}</b> for <b class="text-amber-500">${cost} Coins</b>?`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, Manifest it!',
                        background: '#1e293b',
                        color: '#fff',
                        customClass: { popup: 'border border-white/10 rounded-3xl' }
                    });

                    if (result.isConfirmed) {
                        this.performTransaction('/api/shop/purchase', { type });
                    }
                },

                async trade(action, resource, price, qty, name) {
                    const total = price * qty;
                    const actionText = action === 'buy' ? 'Procure' : 'Liquidate';
                    
                    const result = await Swal.fire({
                        title: 'Confirm Trade',
                        html: `${actionText} <b>${qty}x ${name}</b> for <b class="text-amber-500">${total} Coins</b>?`,
                        icon: 'info',
                        showCancelButton: true,
                        confirmButtonText: `Yes, ${actionText}`,
                        background: '#1e293b',
                        color: '#fff',
                        customClass: { popup: 'border border-white/10 rounded-3xl' }
                    });

                    if (result.isConfirmed) {
                        const url = action === 'buy' ? '/api/shop/purchase-resource' : '/api/shop/sell-resource';
                        this.performTransaction(url, { resource, amount: qty });
                    }
                },

                async buyBundle(bundleKey, cost, name) {
                    const result = await Swal.fire({
                        title: 'Confirm Bundle',
                        html: `Purchase <b>${name}</b> for <b class="text-amber-500">${cost} Coins</b>?`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Unlock Bundle',
                        background: '#1e293b',
                        color: '#fff',
                        customClass: { popup: 'border border-white/10 rounded-3xl' }
                    });

                    if (result.isConfirmed) {
                        this.performTransaction('/api/shop/purchase-bundle', { bundle: bundleKey });
                    }
                },

                async performTransaction(url, data) {
                    try {
                        Swal.showLoading();
                        
                        const fd = new FormData();
                        for (const [key, val] of Object.entries(data)) fd.append(key, val);
                        fd.append('csrf_token', '<?php echo csrf_token(); ?>');
                        fd.append('trap_answer', document.getElementById('shop_trap').value || '');

                        const res = await fetch(app_base_url(url.substring(1)), { method: 'POST', body: fd });
                        const result = await res.json();
                        
                        if (result.success) {
                            await Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: 'Transaction completed successfully.',
                                timer: 1500,
                                showConfirmButton: false,
                                background: '#1e293b',
                                color: '#fff'
                            });
                            location.reload();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Transaction Failed',
                                text: result.message,
                                background: '#1e293b',
                                color: '#fff'
                            });
                        }
                    } catch (e) {
                         Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Connection lost.',
                                background: '#1e293b',
                                color: '#fff'
                            });
                    }
                }
            }
        }
    </script>
</body>
</html>
