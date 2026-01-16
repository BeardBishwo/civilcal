<div class="contest-portal min-h-screen bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900 relative overflow-hidden">
    <!-- Background Effects -->
    <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%239C92AC" fill-opacity="0.05"%3E%3Ccircle cx="30" cy="30" r="2"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-40"></div>
    <div class="absolute top-0 left-1/4 w-96 h-96 bg-gradient-to-r from-yellow-400/20 to-orange-500/20 rounded-full blur-3xl animate-pulse"></div>
    <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-gradient-to-r from-purple-500/20 to-pink-500/20 rounded-full blur-3xl animate-pulse delay-1000"></div>

    <div class="relative z-10 container mx-auto px-4 py-12">
        <!-- Premium Header -->
        <div class="text-center mb-16">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-r from-yellow-400 to-orange-500 rounded-full mb-6 shadow-2xl shadow-yellow-400/30">
                <i class="fas fa-crown text-white text-3xl"></i>
            </div>
            <h1 class="text-6xl md:text-7xl font-black text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 via-orange-400 to-yellow-300 mb-4 tracking-tight">
                BATTLE ROYALE
            </h1>
            <p class="text-xl md:text-2xl text-slate-300 font-light max-w-2xl mx-auto leading-relaxed">
                Enter the ultimate arena where intelligence meets fortune. Only the sharpest minds claim victory.
            </p>
            <div class="mt-8 flex justify-center">
                <div class="h-1 w-32 bg-gradient-to-r from-transparent via-yellow-400 to-transparent rounded-full"></div>
            </div>
        </div>

        <!-- Premium Stats Dashboard -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-16">
            <!-- Balance Card -->
            <div class="group relative">
                <div class="absolute inset-0 bg-gradient-to-r from-yellow-400/20 to-orange-500/20 rounded-2xl blur-xl group-hover:blur-2xl transition-all duration-500"></div>
                <div class="relative bg-slate-800/80 backdrop-blur-xl border border-yellow-400/30 rounded-2xl p-8 text-center hover:border-yellow-400/50 transition-all duration-300 hover:transform hover:scale-105 hover:shadow-2xl hover:shadow-yellow-400/20">
                    <div class="w-16 h-16 bg-gradient-to-r from-yellow-400 to-orange-500 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                        <i class="fas fa-coins text-white text-2xl"></i>
                    </div>
                    <h3 class="text-3xl font-bold text-yellow-400 mb-2"><?= number_format($user['coins']) ?></h3>
                    <p class="text-slate-400 font-medium uppercase tracking-wider text-sm">Your Balance</p>
                </div>
            </div>

            <!-- Active Warriors Card -->
            <div class="group relative">
                <div class="absolute inset-0 bg-gradient-to-r from-purple-500/20 to-pink-500/20 rounded-2xl blur-xl group-hover:blur-2xl transition-all duration-500"></div>
                <div class="relative bg-slate-800/80 backdrop-blur-xl border border-purple-400/30 rounded-2xl p-8 text-center hover:border-purple-400/50 transition-all duration-300 hover:transform hover:scale-105 hover:shadow-2xl hover:shadow-purple-400/20">
                    <div class="w-16 h-16 bg-gradient-to-r from-purple-500 to-pink-500 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                        <i class="fas fa-users text-white text-2xl"></i>
                    </div>
                    <h3 class="text-3xl font-bold text-purple-400 mb-2">1,245</h3>
                    <p class="text-slate-400 font-medium uppercase tracking-wider text-sm">Active Warriors</p>
                </div>
            </div>

            <!-- Prize Pool Card -->
            <div class="group relative">
                <div class="absolute inset-0 bg-gradient-to-r from-green-500/20 to-emerald-500/20 rounded-2xl blur-xl group-hover:blur-2xl transition-all duration-500"></div>
                <div class="relative bg-slate-800/80 backdrop-blur-xl border border-green-400/30 rounded-2xl p-8 text-center hover:border-green-400/50 transition-all duration-300 hover:transform hover:scale-105 hover:shadow-2xl hover:shadow-green-400/20">
                    <div class="w-16 h-16 bg-gradient-to-r from-green-500 to-emerald-500 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                        <i class="fas fa-trophy text-white text-2xl"></i>
                    </div>
                    <h3 class="text-3xl font-bold text-green-400 mb-2">50k+</h3>
                    <p class="text-slate-400 font-medium uppercase tracking-wider text-sm">Total Prize Pool</p>
                </div>
            </div>
        </div>

        <!-- Contests Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php if(empty($contests)): ?>
                <!-- Empty State -->
                <div class="col-span-full">
                    <div class="max-w-2xl mx-auto text-center py-20">
                        <div class="relative mb-8">
                            <div class="w-32 h-32 bg-gradient-to-r from-slate-700 to-slate-600 rounded-full flex items-center justify-center mx-auto shadow-2xl">
                                <i class="fas fa-ghost text-slate-400 text-5xl"></i>
                            </div>
                            <div class="absolute -top-2 -right-2 w-8 h-8 bg-yellow-400 rounded-full animate-ping"></div>
                        </div>
                        <h3 class="text-3xl font-bold text-white mb-4">The Arena Awaits</h3>
                        <p class="text-xl text-slate-400 mb-8 leading-relaxed">
                            No battles are currently active. Hone your skills in the training grounds and prepare for glory.
                        </p>
                        <a href="<?= app_base_url('/blueprint') ?>" class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 text-white font-bold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover:transform hover:scale-105">
                            <i class="fas fa-drafting-compass mr-3"></i>
                            Visit Blueprint Studio
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach($contests as $contest): ?>
                    <!-- Contest Card -->
                    <div class="group relative">
                        <!-- Glow Effect -->
                        <div class="absolute inset-0 bg-gradient-to-r from-yellow-400/10 via-orange-500/10 to-yellow-400/10 rounded-3xl blur-2xl group-hover:blur-xl transition-all duration-500 opacity-0 group-hover:opacity-100"></div>

                        <!-- Card Content -->
                        <div class="relative bg-slate-800/90 backdrop-blur-xl border border-slate-700/50 rounded-3xl p-8 hover:border-yellow-400/30 transition-all duration-300 hover:transform hover:scale-105 hover:shadow-2xl hover:shadow-yellow-400/10 overflow-hidden">

                            <!-- Status Badge -->
                            <div class="absolute top-6 right-6">
                                <div class="px-4 py-2 bg-gradient-to-r from-yellow-400 to-orange-500 text-black font-bold text-sm rounded-full shadow-lg animate-pulse">
                                    LIVE NOW
                                </div>
                            </div>

                            <!-- Top Border Gradient -->
                            <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-yellow-400 via-orange-500 to-yellow-400"></div>

                            <!-- Entry Fee -->
                            <div class="mb-6 text-right">
                                <p class="text-slate-400 text-sm uppercase tracking-wider font-medium">Entry Fee</p>
                                <p class="text-yellow-400 font-bold text-xl flex items-center justify-end">
                                    <i class="fas fa-coins mr-2"></i>
                                    <?= $contest['entry_fee'] ?>
                                </p>
                            </div>

                            <!-- Contest Title -->
                            <h3 class="text-2xl font-bold text-white mb-4 leading-tight">
                                <?= htmlspecialchars($contest['title']) ?>
                            </h3>

                            <!-- Description -->
                            <p class="text-slate-400 mb-8 leading-relaxed line-clamp-3">
                                <?= htmlspecialchars($contest['description']) ?>
                            </p>

                            <!-- Stats Grid -->
                            <div class="grid grid-cols-2 gap-4 mb-8">
                                <div class="bg-slate-700/50 rounded-xl p-4 text-center border border-slate-600/30">
                                    <div class="text-green-400 font-bold text-xl mb-1">
                                        <i class="fas fa-trophy mr-1"></i>
                                        <?= $contest['prize_pool'] ?>
                                    </div>
                                    <p class="text-slate-400 text-xs uppercase tracking-wider font-medium">Prize Pool</p>
                                </div>
                                <div class="bg-slate-700/50 rounded-xl p-4 text-center border border-slate-600/30">
                                    <div class="text-white font-bold text-xl mb-1">
                                        <?= $contest['winner_count'] ?>
                                    </div>
                                    <p class="text-slate-400 text-xs uppercase tracking-wider font-medium">Winners</p>
                                </div>
                            </div>

                            <!-- Join Button -->
                            <form action="<?= app_base_url('contest/join/'.$contest['id']) ?>" method="POST">
                                <button type="submit" class="w-full py-4 px-6 bg-gradient-to-r from-yellow-500 to-orange-600 hover:from-yellow-600 hover:to-orange-700 text-black font-bold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover:transform hover:scale-105 flex items-center justify-center group/btn">
                                    <span class="mr-3">Enter Battle</span>
                                    <i class="fas fa-rocket group-hover/btn:translate-x-1 transition-transform duration-300"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Custom scrollbar for webkit browsers */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: rgba(51, 65, 85, 0.3);
    border-radius: 4px;
}

::-webkit-scrollbar-thumb {
    background: linear-gradient(to bottom, #f59e0b, #f97316);
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(to bottom, #f97316, #ea580c);
}

/* Enhanced animations */
@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
}

.animate-float {
    animation: float 6s ease-in-out infinite;
}

/* Premium glow effects */
.glow-yellow {
    box-shadow: 0 0 30px rgba(245, 158, 11, 0.3);
}

.glow-purple {
    box-shadow: 0 0 30px rgba(147, 51, 234, 0.3);
}

.glow-green {
    box-shadow: 0 0 30px rgba(34, 197, 94, 0.3);
}

/* Responsive text scaling */
@media (max-width: 768px) {
    .text-6xl {
        font-size: 3rem;
    }
    .text-7xl {
        font-size: 3.5rem;
    }
}
</style>
