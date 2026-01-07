<?php
/**
 * Gamification: Sawmill (Resource Processing)
 * Premium Dark Mode UI - Refactored
 */
?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sawmill | Civil City</title>
    <!-- Load Tailwind & General Quiz CSS -->
    <link rel="stylesheet" href="<?php echo app_base_url('themes/default/assets/css/quiz.min.css?v=' . time()); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="//unpkg.com/alpinejs" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-background text-white font-sans min-h-screen pb-20" x-data="sawmillOps()">

    <!-- Header -->
    <header class="h-16 bg-surface/80 backdrop-blur-md border-b border-white/5 flex items-center justify-between px-6 sticky top-0 z-50">
        <a href="<?php echo app_base_url('quiz'); ?>" class="flex items-center gap-2 text-gray-400 hover:text-white transition-colors text-sm font-medium">
            <i class="fas fa-arrow-left"></i> <span>Portal</span>
        </a>
        <div class="text-right">
            <h1 class="text-lg font-black bg-gradient-to-r from-amber-400 to-orange-400 bg-clip-text text-transparent leading-none">Industrial Estate</h1>
            <p class="text-[10px] uppercase tracking-widest text-gray-500 font-bold">BB Sawmill</p>
        </div>
    </header>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Dashboard Header -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12 items-center">
            <div class="flex items-center gap-6">
                <div class="w-20 h-20 rounded-2xl bg-amber-500/10 flex items-center justify-center border border-amber-500/20 shadow-lg shadow-amber-500/10 shrink-0">
                    <img src="<?php echo app_base_url('themes/default/assets/resources/buildings/saw_farm.webp'); ?>" class="w-14 h-14 object-contain">
                </div>
                <div>
                    <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-amber-500 mb-1">
                        <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                        Production Unit
                    </div>
                    <h1 class="text-4xl font-black text-white">Sawmill Operations</h1>
                    <p class="text-gray-400 max-w-md mt-2">Refine raw timber logs into construction-grade planks for architectural projects.</p>
                </div>
            </div>

            <div class="flex justify-end">
                <div class="glass-card p-1 rounded-2xl">
                    <div class="bg-surface/80 backdrop-blur-xl rounded-[14px] px-8 py-4 border border-white/10 flex items-center gap-8">
                        <div>
                            <span class="text-xs font-bold uppercase tracking-wider text-gray-500 block mb-1">Treasury</span>
                            <div class="flex items-center gap-2">
                                <img src="<?php echo app_base_url('themes/default/assets/resources/currency/coin.webp'); ?>" class="w-6 h-6 object-contain">
                                <span class="text-2xl font-black text-white"><?php echo number_format($wallet['coins']); ?></span>
                            </div>
                        </div>
                        <div class="w-px h-10 bg-white/10"></div>
                        <div>
                            <span class="text-xs font-bold uppercase tracking-wider text-gray-500 block mb-1">Fee per Yield</span>
                            <div class="flex items-center gap-1 text-amber-500 font-bold">
                                <i class="fas fa-coins text-xs"></i> <span>10</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <!-- Left: Resource Stockpile -->
            <div class="lg:col-span-4">
                <div class="glass-card p-1 rounded-3xl h-full">
                    <div class="bg-surface/50 backdrop-blur-xl rounded-[20px] p-8 h-full border border-white/5 relative overflow-hidden group">
                        <!-- Decor -->
                        <div class="absolute -top-20 -left-20 w-64 h-64 bg-amber-500/10 rounded-full blur-3xl opacity-50 group-hover:opacity-70 transition-opacity"></div>
                        
                        <h3 class="flex items-center gap-2 text-xl font-black text-white mb-8 relative z-10">
                            <i class="fas fa-warehouse text-amber-500"></i> Stockpile
                        </h3>

                        <div class="space-y-6 relative z-10">
                            <!-- Logs -->
                            <div class="p-4 rounded-xl bg-black/20 border border-white/5 flex items-center justify-between">
                                <div class="flex items-center gap-4">
                                    <img src="<?php echo app_base_url('themes/default/assets/resources/materials/log.webp'); ?>" class="w-12 h-12 object-contain">
                                    <div>
                                        <div class="text-xs font-bold text-gray-500 uppercase tracking-wider">Raw Timber</div>
                                        <div class="text-2xl font-bold text-white"><?php echo number_format($wallet['wood_logs']); ?></div>
                                    </div>
                                </div>
                                <div class="text-xs font-bold text-amber-500 bg-amber-500/10 px-3 py-1 rounded-full">Input</div>
                            </div>

                            <!-- Planks -->
                            <div class="p-4 rounded-xl bg-black/20 border border-white/5 flex items-center justify-between">
                                <div class="flex items-center gap-4">
                                    <img src="<?php echo app_base_url('themes/default/assets/resources/materials/plank.webp'); ?>" class="w-12 h-12 object-contain">
                                    <div>
                                        <div class="text-xs font-bold text-gray-500 uppercase tracking-wider">Polished Planks</div>
                                        <div class="text-2xl font-bold text-green-400"><?php echo number_format($wallet['wood_planks']); ?></div>
                                    </div>
                                </div>
                                <div class="text-xs font-bold text-green-500 bg-green-500/10 px-3 py-1 rounded-full">Output</div>
                            </div>
                        </div>

                        <div class="mt-8 pt-6 border-t border-white/5 text-sm text-gray-400 leading-relaxed relative z-10">
                            <i class="fas fa-info-circle text-amber-500 mr-2"></i> Convert logs to planks to unlock advanced architectural blueprints in the City Builder.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Processing Unit -->
            <div class="lg:col-span-8">
                <div class="glass-card p-1 rounded-3xl h-full">
                    <div class="bg-surface/80 backdrop-blur-xl rounded-[20px] h-full border border-white/5 flex flex-col">
                        
                        <div class="p-6 border-b border-white/5 flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-bold text-white">Wood Processing Terminal</h3>
                                <span class="text-xs font-mono text-gray-500">v4.2 • INDUSTRIAL MODE</span>
                            </div>
                            <div class="flex items-center gap-2 text-green-400 text-xs font-bold bg-green-500/10 px-3 py-1 rounded-full border border-green-500/20">
                                <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span> SYSTEM OPTIMAL
                            </div>
                        </div>

                        <div class="p-8 flex-grow flex flex-col items-center justify-center">
                            
                            <!-- Visualization -->
                            <div class="flex flex-col md:flex-row items-center justify-center gap-8 md:gap-16 mb-12 w-full">
                                <!-- Input -->
                                <div class="text-center group">
                                    <div class="w-32 h-32 rounded-full border-2 border-dashed border-white/10 flex items-center justify-center mb-4 relative group-hover:border-amber-500/50 transition-colors">
                                        <div class="absolute inset-0 bg-amber-500/5 rounded-full blur-xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                        <img src="<?php echo app_base_url('themes/default/assets/resources/materials/log.webp'); ?>" class="w-20 h-20 object-contain relative z-10">
                                    </div>
                                    <div class="font-bold text-white text-sm">INPUT</div>
                                    <div class="text-gray-500 text-xs">Premium Log</div>
                                </div>

                                <!-- Process Arrow -->
                                <div class="hidden md:flex flex-col items-center gap-2 text-gray-600">
                                    <i class="fas fa-cog fa-spin text-2xl text-amber-500"></i>
                                    <div class="w-24 h-0.5 bg-gradient-to-r from-transparent via-amber-500/50 to-transparent"></div>
                                </div>
                                <div class="md:hidden text-amber-500 text-xl"><i class="fas fa-arrow-down"></i></div>

                                <!-- Output -->
                                <div class="text-center group">
                                    <div class="w-32 h-32 rounded-full border-2 border-dashed border-green-500/30 flex items-center justify-center mb-4 relative bg-green-500/5">
                                        <div class="absolute inset-0 bg-green-500/10 rounded-full blur-xl opacity-50 animate-pulse"></div>
                                        <img src="<?php echo app_base_url('themes/default/assets/resources/materials/plank.webp'); ?>" class="w-20 h-20 object-contain relative z-10">
                                    </div>
                                    <div class="font-bold text-white text-sm">OUTPUT</div>
                                    <div class="text-green-500 text-xs font-bold">x4 Planks</div>
                                </div>
                            </div>

                            <!-- Controls -->
                            <div class="w-full max-w-md bg-white/5 rounded-2xl p-6 border border-white/5">
                                <div class="flex items-center justify-between mb-6">
                                    <button @click="qty = Math.max(1, qty - 1)" class="w-12 h-12 rounded-xl bg-white/5 hover:bg-white/10 flex items-center justify-center text-white transition-colors border border-white/5 hover:border-white/20">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    
                                    <div class="text-center">
                                        <div class="text-4xl font-black text-white font-mono" x-text="qty">1</div>
                                        <div class="text-xs uppercase tracking-widest text-gray-500 mt-1">Batch Size</div>
                                    </div>

                                    <button @click="qty++" class="w-12 h-12 rounded-xl bg-white/5 hover:bg-white/10 flex items-center justify-center text-white transition-colors border border-white/5 hover:border-white/20">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>

                                <button @click="process()" :disabled="loading" class="w-full py-4 bg-gradient-to-r from-amber-500 to-orange-600 rounded-xl font-black text-black uppercase tracking-widest hover:shadow-lg hover:shadow-amber-500/20 transition-all transform hover:-translate-y-1 disabled:opacity-50 disabled:cursor-not-allowed">
                                    <span x-show="!loading"><i class="fas fa-cogs mr-2"></i> Initiate Operations</span>
                                    <span x-show="loading"><i class="fas fa-spinner fa-spin mr-2"></i> Processing...</span>
                                </button>
                                
                                <div class="flex justify-between mt-4 text-xs font-mono text-gray-500">
                                    <span>Cost: <span class="text-white" x-text="qty * 10">10</span> Coins</span>
                                    <span>Uses: <span class="text-white" x-text="qty">1</span> Log</span>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        function sawmillOps() {
            return {
                qty: 1,
                loading: false,

                async process() {
                    const cost = this.qty * 10;
                    const logs = this.qty;

                    // Client-side pre-validation
                    const currentCoins = <?php echo $wallet['coins'] ?? 0; ?>;
                    const currentLogs = <?php echo $wallet['wood_logs'] ?? 0; ?>;

                    if (currentCoins < cost) {
                        Swal.fire({ icon: 'error', title: 'Insufficient Funds', text: 'You need more coins.', background: '#1e293b', color: '#fff' });
                        return;
                    }
                    if (currentLogs < logs) {
                        Swal.fire({ icon: 'error', title: 'Insufficient Materials', text: 'You need more logs.', background: '#1e293b', color: '#fff' });
                        return;
                    }

                    this.loading = true;
                    try {
                        const fd = new FormData();
                        fd.append('quantity', this.qty);
                        fd.append('csrf_token', '<?php echo csrf_token(); ?>');

                        const res = await fetch('/api/city/craft', { method: 'POST', body: fd });
                        const data = await res.json();

                        if (res.ok) {
                            await Swal.fire({
                                icon: 'success',
                                title: 'Refinement Complete',
                                text: `Successfully produced ${this.qty * 4} Planks!`,
                                timer: 1500,
                                showConfirmButton: false,
                                background: '#1e293b',
                                color: '#fff'
                            });
                            location.reload();
                        } else {
                            throw new Error(data.message);
                        }
                    } catch (e) {
                         Swal.fire({ icon: 'error', title: 'Operation Failed', text: e.message, background: '#1e293b', color: '#fff' });
                    } finally {
                        this.loading = false;
                    }
                }
            }
        }
    </script>
</body>
</html>
