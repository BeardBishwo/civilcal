<?php
/**
 * Gamification: City Builder (Architect's Studio)
 * Premium Dark Mode UI - Refactored
 */
?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Architect's Studio | Civil City</title>
    <!-- Load Tailwind & General Quiz CSS -->
    <link rel="stylesheet" href="<?php echo app_base_url('themes/default/assets/css/quiz.min.css?v=' . time()); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="//unpkg.com/alpinejs" defer></script>
</head>
<body class="bg-background text-white font-sans min-h-screen pb-20" x-data="cityBuilder()">

    <!-- Header -->
    <header class="h-16 bg-surface/80 backdrop-blur-md border-b border-white/5 flex items-center justify-between px-6 sticky top-0 z-50">
        <a href="<?php echo app_base_url('quiz'); ?>" class="flex items-center gap-2 text-gray-400 hover:text-white transition-colors text-sm font-medium">
            <i class="fas fa-arrow-left"></i> <span>Portal</span>
        </a>
        <div class="text-right">
            <h1 class="text-lg font-black bg-gradient-to-r from-primary to-accent bg-clip-text text-transparent leading-none">Architect's Studio</h1>
            <p class="text-[10px] uppercase tracking-widest text-gray-500 font-bold">Civil City</p>
        </div>
    </header>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Resources Wallet -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <!-- Coins -->
            <div class="glass-card p-4 rounded-2xl flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-white/5 flex items-center justify-center p-2 border border-white/10 shrink-0">
                    <img src="<?php echo app_base_url('themes/default/assets/resources/currency/coin.webp'); ?>" class="w-full h-full object-contain">
                </div>
                <div>
                    <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Gold Coins</h4>
                    <div class="text-2xl font-black text-white leading-none mt-1"><?php echo number_format($wallet['coins'] ?? 0); ?></div>
                </div>
            </div>
            <!-- Bricks -->
            <div class="glass-card p-4 rounded-2xl flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-white/5 flex items-center justify-center p-2 border border-white/10 shrink-0">
                    <img src="<?php echo app_base_url('themes/default/assets/resources/materials/brick_single.webp'); ?>" class="w-full h-full object-contain">
                </div>
                <div>
                    <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Bricks</h4>
                    <div class="text-2xl font-black text-white leading-none mt-1"><?php echo number_format($wallet['bricks'] ?? 0); ?></div>
                </div>
            </div>
            <!-- Cement -->
            <div class="glass-card p-4 rounded-2xl flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-white/5 flex items-center justify-center p-2 border border-white/10 shrink-0">
                    <img src="<?php echo app_base_url('themes/default/assets/resources/materials/bbcement.webp'); ?>" class="w-full h-full object-contain">
                </div>
                <div>
                    <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Cement</h4>
                    <div class="text-2xl font-black text-white leading-none mt-1"><?php echo number_format($wallet['cement'] ?? 0); ?></div>
                </div>
            </div>
            <!-- Steel -->
            <div class="glass-card p-4 rounded-2xl flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-white/5 flex items-center justify-center p-2 border border-white/10 shrink-0">
                    <img src="<?php echo app_base_url('themes/default/assets/resources/materials/steel.webp'); ?>" class="w-full h-full object-contain">
                </div>
                <div>
                    <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Steel</h4>
                    <div class="text-2xl font-black text-white leading-none mt-1"><?php echo number_format($wallet['steel'] ?? 0); ?></div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            
            <!-- Sidebar: Blueprint Catalog -->
            <div class="lg:col-span-1 space-y-6">
                <div class="glass-card p-6 rounded-3xl sticky top-24">
                    <h3 class="flex items-center gap-2 text-xl font-black text-white mb-6">
                        <i class="fas fa-hammer text-primary"></i> Catalog
                    </h3>

                    <div class="space-y-3">
                        <!-- House -->
                        <div class="p-4 rounded-xl bg-surface border border-white/10 hover:border-primary/50 hover:bg-surface/80 transition-all group">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="w-10 h-10 rounded-lg bg-green-500/20 flex items-center justify-center text-green-400 text-lg">
                                    <i class="fas fa-home"></i>
                                </div>
                                <div class="flex-1">
                                    <div class="font-bold text-white text-sm">Residential Unit</div>
                                    <div class="text-[10px] font-bold text-green-400 bg-green-500/10 px-2 py-0.5 rounded-full inline-block mt-1 border border-green-500/20">100 Bricks</div>
                                </div>
                            </div>
                            <button @click="build('house', 100, 'bricks')" :disabled="loading" 
                                    class="w-full py-2 bg-green-500/10 hover:bg-green-500 text-xs font-bold uppercase tracking-wider rounded-lg transition-colors border border-green-500/30 hover:border-green-500 text-green-400 hover:text-white">
                                Construct
                            </button>
                        </div>

                        <!-- Road -->
                        <div class="p-4 rounded-xl bg-surface border border-white/10 hover:border-primary/50 hover:bg-surface/80 transition-all group">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="w-10 h-10 rounded-lg bg-indigo-500/20 flex items-center justify-center text-indigo-400 text-lg">
                                    <i class="fas fa-road"></i>
                                </div>
                                <div class="flex-1">
                                    <div class="font-bold text-white text-sm">Asphalt Road</div>
                                    <div class="text-[10px] font-bold text-indigo-400 bg-indigo-500/10 px-2 py-0.5 rounded-full inline-block mt-1 border border-indigo-500/20">50 Cement</div>
                                </div>
                            </div>
                            <button @click="build('road', 50, 'cement')" :disabled="loading" 
                                    class="w-full py-2 bg-indigo-500/10 hover:bg-indigo-500 text-xs font-bold uppercase tracking-wider rounded-lg transition-colors border border-indigo-500/30 hover:border-indigo-500 text-indigo-400 hover:text-white">
                                Construct
                            </button>
                        </div>

                        <!-- Bridge -->
                        <div class="p-4 rounded-xl bg-surface border border-white/10 hover:border-primary/50 hover:bg-surface/80 transition-all group">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="w-10 h-10 rounded-lg bg-red-500/20 flex items-center justify-center text-red-400 text-lg">
                                    <i class="fas fa-archway"></i>
                                </div>
                                <div class="flex-1">
                                    <div class="font-bold text-white text-sm">Arch Bridge</div>
                                    <div class="text-[10px] font-bold text-red-400 bg-red-500/10 px-2 py-0.5 rounded-full inline-block mt-1 border border-red-500/20">500 Br + 200 St</div>
                                </div>
                            </div>
                            <button @click="build('bridge', 500, 'bricks')" :disabled="loading" 
                                    class="w-full py-2 bg-red-500/10 hover:bg-red-500 text-xs font-bold uppercase tracking-wider rounded-lg transition-colors border border-red-500/30 hover:border-red-500 text-red-400 hover:text-white">
                                Construct
                            </button>
                        </div>

                        <!-- Tower -->
                        <div class="p-4 rounded-xl bg-surface border border-white/10 hover:border-primary/50 hover:bg-surface/80 transition-all group">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="w-10 h-10 rounded-lg bg-blue-500/20 flex items-center justify-center text-blue-400 text-lg">
                                    <i class="fas fa-building"></i>
                                </div>
                                <div class="flex-1">
                                    <div class="font-bold text-white text-sm">Corporate Tower</div>
                                    <div class="text-[10px] font-bold text-blue-400 bg-blue-500/10 px-2 py-0.5 rounded-full inline-block mt-1 border border-blue-500/20">1k Br + 500 St</div>
                                </div>
                            </div>
                            <button @click="build('tower', 1000, 'bricks')" :disabled="loading" 
                                    class="w-full py-2 bg-blue-500/10 hover:bg-blue-500 text-xs font-bold uppercase tracking-wider rounded-lg transition-colors border border-blue-500/30 hover:border-blue-500 text-blue-400 hover:text-white">
                                Construct
                            </button>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Main: City Map -->
            <div class="lg:col-span-3">
                <div class="bg-surface border border-white/10 rounded-3xl p-8 min-h-[600px] relative overflow-hidden">
                    <!-- Grid Background -->
                    <div class="absolute inset-0 z-0 opacity-20" 
                         style="background-image: linear-gradient(#fff 1px, transparent 1px), linear-gradient(90deg, #fff 1px, transparent 1px); background-size: 40px 40px;">
                    </div>
                
                    <?php if (empty($buildings)): ?>
                        <div class="absolute inset-0 flex flex-col items-center justify-center text-center z-10 p-8">
                            <div class="w-24 h-24 rounded-full bg-white/5 flex items-center justify-center mb-6">
                                <i class="fas fa-hard-hat text-4xl text-gray-600"></i>
                            </div>
                            <h3 class="text-2xl font-black text-white mb-2">Virgin Terraforming Site</h3>
                            <p class="text-gray-400 max-w-md">The site is cleared and ready for development. Solve quiz challenges to earn materials and begin colonization.</p>
                        </div>
                    <?php else: ?>
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 relative z-10">
                            <?php foreach($buildings as $idx => $b): ?>
                                <?php 
                                    $icon = 'home'; $color = 'text-green-500'; $bg = 'bg-green-500/10';
                                    if($b['building_type'] == 'road') { $icon = 'road'; $color = 'text-indigo-400'; $bg = 'bg-indigo-500/10'; }
                                    if($b['building_type'] == 'bridge') { $icon = 'archway'; $color = 'text-red-500'; $bg = 'bg-red-500/10'; }
                                    if($b['building_type'] == 'tower') { $icon = 'building'; $color = 'text-blue-500'; $bg = 'bg-blue-500/10'; }
                                ?>
                                <div class="bg-surface border border-white/10 p-4 rounded-xl flex flex-col items-center text-center hover:-translate-y-1 transition-transform shadow-lg shadow-black/20 animate-fade-in-up" 
                                     style="animation-delay: <?php echo $idx * 50; ?>ms">
                                    <div class="w-14 h-14 rounded-xl <?php echo $bg; ?> flex items-center justify-center <?php echo $color; ?> text-2xl mb-3 shadow-inner">
                                        <i class="fas fa-<?php echo $icon; ?>"></i>
                                    </div>
                                    <div class="font-bold text-white text-sm capitalize"><?php echo $b['building_type']; ?></div>
                                    <div class="text-[10px] text-gray-500 font-mono mt-1">Lvl <?php echo $b['level']; ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>

    <script>
        function cityBuilder() {
            return {
                loading: false,
                async build(type, cost, resource) {
                    if(!confirm('Construct ' + type + '?')) return;
                    
                    this.loading = true;
                    const fd = new FormData();
                    fd.append('type', type);
                    fd.append('csrf_token', '<?php echo csrf_token(); ?>');
                    
                    try {
                        const res = await fetch('<?php echo app_base_url("api/city/build"); ?>', {
                            method: 'POST',
                            body: fd
                        });
                        const data = await res.json();
                        
                        if (res.ok) {
                            window.location.reload(); 
                        } else {
                            alert(data.message || 'Construction Failed');
                        }
                    } catch(e) {
                        alert('Connection Error');
                    } finally {
                        this.loading = false;
                    }
                }
            }
        }
    </script>
</body>
</html>
