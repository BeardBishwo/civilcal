<?php
$page_title = 'Command Center - ' . \App\Services\SettingsService::get('site_name', 'Civil Cal');
?>

<style>
    .glass-panel {
        background: rgba(20, 20, 20, 0.6);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.08);
        box-shadow: 0 0 0 1px rgba(0, 0, 0, 0.2), 0 20px 40px -10px rgba(0, 0, 0, 0.5);
    }

    .neon-text {
        text-shadow: 0 0 20px rgba(99, 102, 241, 0.5);
    }

    .gold-text {
        text-shadow: 0 0 20px rgba(234, 179, 8, 0.3);
    }
</style>

<div class="min-h-screen pb-20 pt-24 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">

    <div class="relative mb-12 animate-fade-in-up">
        <div class="flex flex-col md:flex-row items-end justify-between gap-6">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-500/10 border border-blue-500/20 text-blue-400 text-xs font-bold uppercase tracking-wider mb-4">
                    <span class="w-2 h-2 rounded-full bg-blue-400 animate-pulse"></span>
                    Live Workspace
                </div>
                <h1 class="text-4xl md:text-5xl font-black text-white tracking-tight mb-2">
                    Welcome back, <br />
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 via-purple-400 to-indigo-400 animate-gradient-x">
                        <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Engineer'); ?>
                    </span>
                </h1>
                <p class="text-gray-400 text-lg max-w-xl">
                    Your daily briefing: <span class="text-white font-medium"><?php echo $daily_quest['streak'] ?? 0; ?> Day Streak</span> active.
                </p>
            </div>

            <!-- Payment Success Notification -->
            <?php if (isset($payment_success) && $payment_success): ?>
            <div class="fixed top-4 right-4 z-50 animate-fade-in-up">
                <div class="bg-green-500/10 border border-green-500/20 rounded-lg p-4 max-w-sm shadow-xl backdrop-blur-sm">
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle text-green-400 text-xl"></i>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-green-400 font-semibold text-sm">Payment Successful!</h3>
                            <p class="text-green-200 text-sm mt-1">Welcome to premium! Your subscription is now active.</p>
                            <button onclick="this.parentElement.parentElement.parentElement.remove()" 
                                    class="mt-2 text-green-300 hover:text-green-100 text-xs underline">
                                Dismiss
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <div class="glass-panel p-1.5 rounded-xl flex items-center gap-1" x-data="{ mode: '<?php echo $user['study_mode'] ?? 'psc'; ?>' }">
                <button @click="mode = 'psc'; toggleMode('psc')"
                    :class="mode === 'psc' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/25' : 'text-gray-400 hover:text-white'"
                    class="px-6 py-2.5 rounded-lg text-sm font-bold transition-all duration-300 flex items-center gap-2">
                    <i class="fas fa-shield-alt"></i> Loksewa
                </button>
                <button @click="mode = 'world'; toggleMode('world')"
                    :class="mode === 'world' ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/25' : 'text-gray-400 hover:text-white'"
                    class="px-6 py-2.5 rounded-lg text-sm font-bold transition-all duration-300 flex items-center gap-2">
                    <i class="fas fa-globe-asia"></i> World
                </button>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

        <div class="lg:col-span-8 space-y-8">

            <div class="glass-panel rounded-3xl p-8 relative overflow-hidden group animate-fade-in-up animation-delay-100">
                <div class="absolute top-0 right-0 w-64 h-64 bg-indigo-600/20 blur-3xl rounded-full -mr-32 -mt-32 transition-opacity group-hover:opacity-75"></div>

                <div class="flex items-start justify-between mb-8 relative z-10">
                    <div class="flex items-center gap-6">
                        <div class="relative">
                            <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 p-0.5 shadow-2xl shadow-indigo-500/20">
                                <div class="w-full h-full bg-black rounded-2xl flex items-center justify-center relative overflow-hidden">
                                    <?php
                                    $level = str_pad($rank['rank_level'] ?? 1, 2, '0', STR_PAD_LEFT);
                                    $slug = strtolower(explode(' ', $rank['rank'] ?? 'Intern')[0]); // Simple slug
                                    ?>
                                    <img src="/themes/default/assets/resources/ranks/rank_<?php echo $level; ?>_<?php echo $slug; ?>.webp"
                                        onerror="this.src='/themes/default/assets/resources/ranks/rank_01_intern.webp'"
                                        class="w-16 h-16 object-contain transform group-hover:scale-110 transition-transform duration-500">
                                </div>
                            </div>
                            <div class="absolute -bottom-3 -right-3 bg-gray-900 border border-white/10 text-white text-xs font-bold px-2 py-1 rounded-lg">
                                Lvl <?php echo $rank['rank_level'] ?? 1; ?>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-2xl font-black text-white mb-1"><?php echo $rank['rank'] ?? 'Intern'; ?></h3>
                            <div class="text-indigo-400 font-medium text-sm flex items-center gap-2">
                                <span>Next: <?php echo $rank['next_rank'] ?? 'Surveyor'; ?></span>
                                <i class="fas fa-chevron-right text-xs opacity-50"></i>
                            </div>
                        </div>
                    </div>

                    <div class="text-right hidden sm:block">
                        <div class="text-3xl font-black text-white tracking-tight"><?php echo number_format($rank['total_power'] ?? 0); ?></div>
                        <div class="text-xs text-gray-500 font-bold uppercase tracking-wider">Total Power</div>
                    </div>
                </div>

                <div class="relative h-4 bg-white/5 rounded-full overflow-hidden mb-2">
                    <div class="absolute top-0 left-0 h-full bg-gradient-to-r from-indigo-500 via-purple-500 to-indigo-500 w-[<?php echo $rank['rank_progress'] ?? 0; ?>%] shadow-[0_0_20px_rgba(99,102,241,0.5)] transition-all duration-1000 ease-out" style="width: <?php echo $rank['rank_progress'] ?? 0; ?>%"></div>
                </div>
                <div class="flex justify-between text-xs font-bold text-gray-500 uppercase tracking-wider">
                    <span>Current Tier</span>
                    <span><?php echo number_format(($rank['next_rank_power'] ?? 100) - ($rank['total_power'] ?? 0)); ?> XP to Promotion</span>
                </div>

                <?php if (($rank['rank_progress'] ?? 0) >= 100): ?>
                    <button onclick="triggerPromotion()" class="mt-6 w-full py-4 bg-gradient-to-r from-yellow-400 to-orange-500 rounded-xl text-black font-black text-lg uppercase tracking-widest shadow-lg shadow-orange-500/20 hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center justify-center gap-3">
                        <i class="fas fa-crown"></i> Claim Promotion
                    </button>
                <?php endif; ?>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 animate-fade-in-up animation-delay-200">
                <a href="<?php echo app_base_url('/calculators'); ?>" class="glass-panel p-6 rounded-2xl hover:bg-white/5 transition-colors group text-center">
                    <div class="w-12 h-12 mx-auto bg-blue-500/10 text-blue-400 rounded-xl flex items-center justify-center text-xl mb-3 group-hover:scale-110 transition-transform">
                        <i class="fas fa-calculator"></i>
                    </div>
                    <div class="font-bold text-white text-sm">Tools</div>
                </a>
                <a href="<?php echo app_base_url('/quiz'); ?>" class="glass-panel p-6 rounded-2xl hover:bg-white/5 transition-colors group text-center">
                    <div class="w-12 h-12 mx-auto bg-green-500/10 text-green-400 rounded-xl flex items-center justify-center text-xl mb-3 group-hover:scale-110 transition-transform">
                        <i class="fas fa-brain"></i>
                    </div>
                    <div class="font-bold text-white text-sm">Quiz</div>
                </a>
                <a href="<?php echo app_base_url('/blog'); ?>" class="glass-panel p-6 rounded-2xl hover:bg-white/5 transition-colors group text-center">
                    <div class="w-12 h-12 mx-auto bg-purple-500/10 text-purple-400 rounded-xl flex items-center justify-center text-xl mb-3 group-hover:scale-110 transition-transform">
                        <i class="fas fa-book-reader"></i>
                    </div>
                    <div class="font-bold text-white text-sm">Learn</div>
                </a>
                <a href="<?php echo app_base_url('/profile'); ?>" class="glass-panel p-6 rounded-2xl hover:bg-white/5 transition-colors group text-center">
                    <div class="w-12 h-12 mx-auto bg-pink-500/10 text-pink-400 rounded-xl flex items-center justify-center text-xl mb-3 group-hover:scale-110 transition-transform">
                        <i class="fas fa-user-astronaut"></i>
                    </div>
                    <div class="font-bold text-white text-sm">Profile</div>
                </a>
            </div>

        </div>

        <div class="lg:col-span-4 space-y-6">

            <div class="glass-panel p-6 rounded-3xl relative overflow-hidden animate-fade-in-up animation-delay-300">
                <div class="absolute inset-0 bg-gradient-to-br from-indigo-900/50 to-transparent opacity-50"></div>
                <div class="relative z-10">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-bold text-white flex items-center gap-2">
                            <i class="fas fa-fire text-orange-500"></i> Daily Quest
                        </h3>
                        <span class="bg-orange-500/10 text-orange-400 px-2 py-1 rounded text-xs font-bold">x<?php echo $daily_quest['multiplier'] ?? 1; ?> Multiplier</span>
                    </div>

                    <div class="bg-black/40 rounded-xl p-4 mb-4 border border-white/5">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm text-gray-300">Target: 3 Quizzes</span>
                            <span class="text-sm font-bold text-white"><?php echo $user['daily_progress'] ?? 0; ?>/3</span>
                        </div>
                        <div class="h-2 bg-white/10 rounded-full overflow-hidden">
                            <div class="h-full bg-orange-500 w-1/3"></div>
                        </div>
                    </div>

                    <a href="<?php echo app_base_url('/quiz/daily/start'); ?>" class="block w-full py-3 bg-white text-black font-bold text-center rounded-xl hover:bg-gray-100 transition-colors">
                        Start Now
                    </a>
                </div>
            </div>

            <div class="glass-panel p-6 rounded-3xl relative overflow-hidden border-yellow-500/30 animate-fade-in-up animation-delay-400">
                <div class="absolute -right-4 -top-4 text-9xl text-yellow-500/10 transform rotate-12">
                    <i class="fas fa-trophy"></i>
                </div>
                <div class="relative z-10">
                    <h3 class="font-bold text-yellow-400 text-lg mb-1">Battle Royale</h3>
                    <p class="text-sm text-gray-400 mb-4">Compete for the weekly prize pool of 5,000 coins.</p>
                    <a href="<?php echo app_base_url('/contests'); ?>" class="inline-flex items-center gap-2 text-yellow-400 font-bold hover:text-yellow-300 transition-colors">
                        Enter Arena <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    function toggleMode(mode) {
        // Optimistic UI handled by Alpine
        fetch('<?php echo app_base_url("/api/career/mode"); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                mode: mode
            })
        }).then(res => {
            if (res.ok) window.location.reload();
        });
    }
</script>