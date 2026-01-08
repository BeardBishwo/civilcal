<?php
/**
 * Gamification: Battle Pass (Season 1)
 * Premium Dark Mode UI - Refactored
 */
?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Battle Pass | Civil City</title>
    <!-- Load Tailwind & General Quiz CSS -->
    <link rel="stylesheet" href="<?php echo app_base_url('themes/default/assets/css/quiz.min.css?v=' . time()); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-background text-white font-sans min-h-screen pb-20" x-data="battlePass()">

    <!-- Header -->
    <header class="h-16 bg-surface/80 backdrop-blur-md border-b border-white/5 flex items-center justify-between px-6 sticky top-0 z-50">
        <a href="<?php echo app_base_url('quiz'); ?>" class="flex items-center gap-2 text-gray-400 hover:text-white transition-colors text-sm font-medium">
            <i class="fas fa-arrow-left"></i> <span>Portal</span>
        </a>
        <div class="text-right">
            <h1 class="text-lg font-black bg-gradient-to-r from-purple-400 to-indigo-400 bg-clip-text text-transparent leading-none">Season 1</h1>
            <p class="text-[10px] uppercase tracking-widest text-gray-500 font-bold">Civil Uprising</p>
        </div>
    </header>

    <!-- Hero Section -->
    <div class="relative overflow-hidden py-12 md:py-20 border-b border-white/5">
        <!-- Animated Background -->
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-purple-500/10 rounded-full blur-[100px] animate-blob"></div>
            <div class="absolute bottom-0 left-0 w-[500px] h-[500px] bg-indigo-500/10 rounded-full blur-[100px] animate-blob animation-delay-2000"></div>
        </div>

        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                
                <!-- Main Info -->
                <div>
                    <div class="flex items-center gap-3 mb-4">
                        <span class="bg-white/5 border border-white/10 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-widest text-gray-400 flex items-center gap-2">
                            <i class="fas fa-calendar-alt"></i> Season 1
                        </span>
                        <?php if (!$progress['is_premium_unlocked']): ?>
                            <span class="bg-amber-500/10 border border-amber-500/20 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-widest text-amber-500 flex items-center gap-2">
                                <i class="fas fa-lock"></i> Free Tier
                            </span>
                        <?php else: ?>
                            <span class="bg-amber-500/10 border border-amber-500/20 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-widest text-amber-500 flex items-center gap-2">
                                <i class="fas fa-crown"></i> Premium Active
                            </span>
                        <?php endif; ?>
                    </div>

                    <h1 class="text-5xl md:text-6xl font-black text-white mb-4 tracking-tight leading-tight">
                        Civil <span class="text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-indigo-400">Uprising</span>
                    </h1>
                    <p class="text-xl text-gray-400 mb-8 max-w-lg">Complete missions, earn XP, and unlock exclusive engineering blueprints and rewards.</p>

                    <!-- Level Progress Card -->
                    <div class="glass-card p-1 rounded-2xl max-w-md">
                        <div class="bg-surface rounded-[14px] p-6 border border-white/10">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center gap-4">
                                    <div class="text-center">
                                        <div class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Level</div>
                                        <div class="text-3xl font-black text-white"><?php echo $progress['current_level']; ?></div>
                                    </div>
                                    <div class="w-px h-8 bg-white/10"></div>
                                    <div>
                                        <div class="text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">XP Progress</div>
                                        <div class="text-sm font-bold text-white"><?php echo ($progress['current_xp'] % 1000); ?> <span class="text-gray-500">/ 1000</span></div>
                                    </div>
                                </div>
                                <?php if (!$progress['is_premium_unlocked']): ?>
                                <button class="bg-gradient-to-r from-amber-500 to-orange-500 text-white text-xs font-bold px-4 py-2 rounded-lg shadow-lg hover:shadow-amber-500/25 transition-all">
                                    <i class="fas fa-crown mr-1"></i> UNLOCK
                                </button>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Progress Bar -->
                            <div class="h-2 bg-black/20 rounded-full overflow-hidden relative">
                                <div class="h-full bg-gradient-to-r from-purple-500 to-indigo-500 rounded-full relative" style="width: <?php echo ($progress['current_xp'] % 1000) / 10; ?>%">
                                    <div class="absolute inset-0 bg-white/20 animate-[shimmer_2s_infinite]"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stats Grid -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="glass-card p-6 rounded-2xl flex flex-col items-center justify-center text-center">
                        <i class="fas fa-fire text-orange-500 text-2xl mb-2"></i>
                        <div class="text-2xl font-black text-white"><?php echo min(7, $progress['current_level']); ?></div>
                        <div class="text-xs font-bold text-gray-500 uppercase tracking-wider">Day Streak</div>
                    </div>
                    <div class="glass-card p-6 rounded-2xl flex flex-col items-center justify-center text-center">
                        <i class="fas fa-tasks text-blue-500 text-2xl mb-2"></i>
                        <div class="text-2xl font-black text-white">3/5</div>
                        <div class="text-xs font-bold text-gray-500 uppercase tracking-wider">Missions</div>
                    </div>
                    <div class="glass-card p-6 rounded-2xl flex flex-col items-center justify-center text-center col-span-2">
                        <div class="flex items-center gap-2 mb-2">
                            <img src="<?php echo app_base_url('themes/default/assets/resources/currency/coin.webp'); ?>" class="w-6 h-6">
                            <span class="text-2xl font-black text-white">+250</span>
                        </div>
                        <div class="text-xs font-bold text-gray-500 uppercase tracking-wider">Earnings Today</div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
            
            <!-- Rewards Track -->
            <div class="lg:col-span-8">
                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-xl font-bold text-white flex items-center gap-2">
                        <i class="fas fa-trophy text-purple-500"></i> Reward Track
                    </h3>
                    <div class="flex items-center gap-4 text-xs font-bold uppercase tracking-wider">
                        <div class="flex items-center gap-2 text-indigo-400">
                            <span class="w-2 h-2 rounded-full bg-indigo-400"></span> Free
                        </div>
                        <div class="flex items-center gap-2 text-amber-500">
                            <span class="w-2 h-2 rounded-full bg-amber-500"></span> Premium
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <?php foreach ($rewards as $index => $reward): 
                        $isClaimed = in_array($reward['id'], $progress['claimed_rewards']);
                        $isUnlocked = $progress['current_level'] >= $reward['level'];
                        $canClaim = $isUnlocked && !$isClaimed && (!$reward['is_premium'] || $progress['is_premium_unlocked']);
                    ?>
                    <div class="group relative overflow-hidden rounded-xl border border-white/10 bg-surface hover:bg-surface/80 transition-all 
                                <?php echo $reward['is_premium'] ? 'border-l-2 border-l-amber-500' : 'border-l-2 border-l-indigo-500'; ?> 
                                <?php echo $isClaimed ? 'opacity-50' : ''; ?>
                                <?php echo $canClaim ? 'ring-1 ring-white/20' : ''; ?>">
                        
                        <div class="p-4 flex items-center gap-6">
                            <!-- Level Badge -->
                            <div class="flex flex-col items-center justify-center w-12 h-12 rounded-lg bg-black/20 border border-white/5 shrink-0">
                                <span class="text-[10px] text-gray-500 font-bold uppercase">Lvl</span>
                                <span class="text-lg font-bold text-white leading-none"><?php echo $reward['level']; ?></span>
                            </div>

                            <!-- Icon -->
                            <div class="w-12 h-12 rounded-lg bg-white/5 flex items-center justify-center shrink-0">
                                <?php if ($reward['reward_type'] == 'bricks'): ?>
                                    <img src="<?php echo app_base_url('themes/default/assets/resources/materials/brick_single.webp'); ?>" class="w-8 h-8 object-contain">
                                <?php elseif ($reward['reward_type'] == 'coins'): ?>
                                    <img src="<?php echo app_base_url('themes/default/assets/resources/currency/coin.webp'); ?>" class="w-8 h-8 object-contain">
                                <?php elseif ($reward['reward_type'] == 'lifeline'): ?>
                                    <i class="fas fa-bolt text-purple-400 text-xl"></i>
                                <?php else: ?>
                                    <img src="<?php echo app_base_url('themes/default/assets/resources/materials/plank.webp'); ?>" class="w-8 h-8 object-contain">
                                <?php endif; ?>
                            </div>

                            <!-- Info -->
                            <div class="flex-grow">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="font-bold text-white"><?php echo strtoupper($reward['reward_type']); ?></span>
                                    <?php if ($reward['is_premium']): ?>
                                        <span class="bg-amber-500/10 text-amber-500 text-[10px] font-bold px-2 py-0.5 rounded uppercase tracking-wider"><i class="fas fa-crown"></i> Premium</span>
                                    <?php endif; ?>
                                </div>
                                <div class="text-xs font-bold text-gray-500 uppercase tracking-wider">
                                    <?php if ($isClaimed): ?>CLAIMED<?php elseif ($isUnlocked): ?>READY TO CLAIM<?php else: ?>LOCKED<?php endif; ?>
                                </div>
                            </div>

                            <!-- Action -->
                            <div>
                                <?php if ($canClaim): ?>
                                    <button class="bg-white text-black font-bold uppercase text-xs px-4 py-2 rounded-lg hover:bg-gray-200 transition-colors shadow-lg shadow-white/10"
                                            @click="claimReward(<?php echo $reward['id']; ?>)">
                                        Claim
                                    </button>
                                <?php elseif ($isClaimed): ?>
                                    <div class="w-8 h-8 rounded-full bg-green-500/20 flex items-center justify-center text-green-500">
                                        <i class="fas fa-check"></i>
                                    </div>
                                <?php else: ?>
                                    <div class="w-8 h-8 rounded-full bg-white/5 flex items-center justify-center text-gray-600">
                                        <i class="fas fa-lock"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-4 space-y-8">
                
                <!-- Missions -->
                <div class="glass-card p-6 rounded-3xl">
                    <h3 class="text-lg font-bold text-white mb-6 flex items-center gap-2">
                        <i class="fas fa-crosshairs text-blue-500"></i> Active Missions
                    </h3>
                    <div class="space-y-4">
                        <div class="p-4 rounded-xl bg-white/5 border border-white/5">
                            <div class="flex justify-between items-start mb-2">
                                <div class="text-sm font-bold text-white">Solve 5 Civil Questions</div>
                                <span class="text-[10px] font-bold text-purple-400 bg-purple-500/10 px-2 py-0.5 rounded">+100 XP</span>
                            </div>
                            <div class="w-full bg-black/20 h-1.5 rounded-full overflow-hidden">
                                <div class="bg-blue-500 h-full w-[60%]"></div>
                            </div>
                        </div>
                        <div class="p-4 rounded-xl bg-white/5 border border-white/5 opacity-50">
                            <div class="flex justify-between items-start mb-2">
                                <div class="text-sm font-bold text-white decoration-line-through">Win a Battle Royale</div>
                                <span class="text-green-500"><i class="fas fa-check-circle"></i></span>
                            </div>
                            <div class="w-full bg-black/20 h-1.5 rounded-full overflow-hidden">
                                <div class="bg-green-500 h-full w-full"></div>
                            </div>
                        </div>
                        <div class="p-4 rounded-xl bg-white/5 border border-white/5">
                            <div class="flex justify-between items-start mb-2">
                                <div class="text-sm font-bold text-white">Login 3 Days in Row</div>
                                <span class="text-[10px] font-bold text-purple-400 bg-purple-500/10 px-2 py-0.5 rounded">+50 XP</span>
                            </div>
                            <div class="w-full bg-black/20 h-1.5 rounded-full overflow-hidden">
                                <div class="bg-blue-500 h-full w-[100%]"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Premium Promo -->
                <?php if (!$progress['is_premium_unlocked']): ?>
                <div class="bg-gradient-to-br from-amber-500 to-orange-600 rounded-3xl p-8 text-center relative overflow-hidden group">
                    <div class="absolute inset-0 bg-black/10 group-hover:bg-transparent transition-colors"></div>
                    <div class="relative z-10">
                        <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center mx-auto mb-4 text-3xl text-white shadow-lg">
                            <i class="fas fa-crown"></i>
                        </div>
                        <h3 class="text-2xl font-black text-white mb-2">Go Premium</h3>
                        <p class="text-white/80 text-sm mb-6">Unlock exclusive architect skins, double XP weekends, and premium resource packs.</p>
                        <button class="w-full py-3 bg-white text-amber-600 font-black uppercase tracking-widest rounded-xl hover:bg-gray-100 transition-colors shadow-xl">
                            Upgrade Now
                        </button>
                    </div>
                </div>
                <?php endif; ?>

            </div>

        </div>
    </div>

    <script>
        function battlePass() {
            return {
                async claimReward(id) {
                    // Placeholder for claim logic
                    // In real app, call API
                    alert('Claim logic to be implemented for reward ID: ' + id);
                }
            }
        }
    </script>
</body>
</html>
