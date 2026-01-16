<div class="min-h-screen bg-gradient-to-br from-slate-900 via-indigo-900 to-purple-900 relative overflow-hidden">

    <!-- Premium Background Effects -->
    <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%239C92AC" fill-opacity="0.04"%3E%3Ccircle cx="30" cy="30" r="2"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-40"></div>

    <!-- Animated Background Elements -->
    <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-gradient-to-r from-indigo-500/15 to-purple-500/15 rounded-full blur-3xl animate-pulse"></div>
    <div class="absolute bottom-1/4 right-1/4 w-80 h-80 bg-gradient-to-r from-purple-500/15 to-pink-500/15 rounded-full blur-3xl animate-pulse delay-1000"></div>
    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-64 h-64 bg-gradient-to-r from-emerald-500/10 to-cyan-500/10 rounded-full blur-3xl animate-pulse delay-2000"></div>

    <!-- Premium Header -->
    <header class="relative z-20 p-8">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <div class="flex items-center gap-6">
                <a href="<?= app_base_url('/quiz') ?>" class="group inline-flex items-center justify-center w-14 h-14 bg-slate-800/80 backdrop-blur-xl border border-slate-700/50 rounded-2xl text-white hover:bg-slate-700/80 hover:border-slate-600/50 transition-all duration-300 hover:transform hover:scale-110 shadow-lg">
                    <i class="fas fa-arrow-left text-xl group-hover:-translate-x-1 transition-transform duration-300"></i>
                </a>
                <div>
                    <h1 class="text-5xl md:text-6xl font-black text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 via-purple-400 to-pink-400 mb-2 tracking-tight">
                        ARCHITECT'S STUDIO
                    </h1>
                    <p class="text-slate-300 font-light text-lg">Master technical terminology and unlock engineering blueprints</p>
                </div>
            </div>

            <!-- Stats Badge -->
            <div class="bg-slate-800/80 backdrop-blur-xl border border-indigo-400/30 rounded-2xl px-6 py-4 shadow-lg">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-r from-indigo-500 to-purple-500 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-drafting-compass text-white"></i>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-indigo-400"><?= count($blueprints) ?></div>
                        <div class="text-xs uppercase tracking-wider text-slate-400 font-medium">Blueprints</div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="relative z-20 max-w-7xl mx-auto px-8 pb-16">

        <!-- Blueprint Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach($blueprints as $bp): ?>
                <?php $pct = $progress[$bp['id']] ?? 0; ?>
                <!-- Blueprint Card -->
                <div class="group relative">
                    <!-- Glow Effect -->
                    <div class="absolute inset-0 bg-gradient-to-r from-indigo-500/10 via-purple-500/10 to-pink-500/10 rounded-3xl blur-2xl group-hover:blur-xl transition-all duration-500 opacity-0 group-hover:opacity-100"></div>

                    <!-- Card Content -->
                    <div class="relative bg-slate-800/90 backdrop-blur-xl border border-slate-700/50 rounded-3xl overflow-hidden hover:border-indigo-400/30 transition-all duration-300 hover:transform hover:scale-105 shadow-2xl hover:shadow-indigo-400/10">

                        <!-- Blueprint Preview Area -->
                        <div class="relative h-64 bg-gradient-to-br from-slate-700/50 to-slate-800/50 flex items-center justify-center overflow-hidden">
                            <!-- Animated Background Pattern -->
                            <div class="absolute inset-0 opacity-10">
                                <div class="absolute top-4 left-4 w-16 h-16 border-2 border-indigo-400/30 rounded-lg transform rotate-12"></div>
                                <div class="absolute top-8 right-8 w-12 h-12 border-2 border-purple-400/30 rounded-lg transform -rotate-12"></div>
                                <div class="absolute bottom-6 left-1/2 transform -translate-x-1/2 w-20 h-8 border-2 border-pink-400/30 rounded-lg"></div>
                            </div>

                            <!-- Blueprint Icon -->
                            <div class="relative z-10">
                                <div class="w-20 h-20 bg-gradient-to-r from-indigo-500 to-purple-500 rounded-2xl flex items-center justify-center shadow-2xl shadow-indigo-500/30 mb-4">
                                    <i class="fas fa-pencil-ruler text-white text-3xl"></i>
                                </div>
                                <div class="text-center">
                                    <div class="text-white font-bold text-lg mb-1">Blueprint</div>
                                    <div class="text-slate-400 text-sm">Engineering Design</div>
                                </div>
                            </div>

                            <!-- Completion Badge -->
                            <?php if($pct >= 100): ?>
                                <div class="absolute top-4 right-4 bg-gradient-to-r from-green-500 to-emerald-500 text-white px-4 py-2 rounded-xl font-bold text-sm shadow-lg flex items-center gap-2">
                                    <i class="fas fa-check-circle"></i>
                                    COMPLETED
                                </div>
                            <?php endif; ?>

                            <!-- Progress Indicator -->
                            <div class="absolute bottom-4 left-4 right-4">
                                <div class="bg-slate-900/80 backdrop-blur-sm rounded-xl p-3">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="text-slate-300 text-sm font-medium">Reveal Progress</span>
                                        <span class="text-indigo-400 font-bold text-sm"><?= $pct ?>%</span>
                                    </div>
                                    <div class="w-full bg-slate-700/50 rounded-full h-2">
                                        <div class="bg-gradient-to-r from-indigo-500 to-purple-500 h-2 rounded-full transition-all duration-1000 ease-out" style="width: <?= $pct ?>%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card Body -->
                        <div class="p-8">
                            <!-- Title and Difficulty -->
                            <div class="mb-6">
                                <h3 class="text-2xl font-bold text-white mb-3 leading-tight">
                                    <?= htmlspecialchars($bp['title']) ?>
                                </h3>
                                <div class="flex items-center justify-between">
                                    <div class="px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-500 text-white font-bold text-sm rounded-xl shadow-lg">
                                        <?= $bp['difficulty'] == 1 ? 'Foundational' : 'Advanced' ?>
                                    </div>
                                    <div class="flex items-center gap-2 text-green-400 font-bold">
                                        <i class="fas fa-coins"></i>
                                        +<?= $bp['reward'] ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Features Grid -->
                            <div class="grid grid-cols-2 gap-4 mb-8">
                                <div class="bg-slate-700/50 rounded-xl p-4 text-center border border-slate-600/30">
                                    <div class="text-indigo-400 font-bold text-xl mb-1">
                                        <i class="fas fa-brain"></i>
                                    </div>
                                    <p class="text-slate-400 text-xs uppercase tracking-wider font-medium">Learning</p>
                                </div>
                                <div class="bg-slate-700/50 rounded-xl p-4 text-center border border-slate-600/30">
                                    <div class="text-purple-400 font-bold text-xl mb-1">
                                        <i class="fas fa-trophy"></i>
                                    </div>
                                    <p class="text-slate-400 text-xs uppercase tracking-wider font-medium">Achievement</p>
                                </div>
                            </div>

                            <!-- Action Button -->
                            <a href="<?= app_base_url('/blueprint/arena/'.$bp['id']) ?>"
                                class="group/btn w-full py-4 px-6 bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white font-bold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover:transform hover:scale-105 flex items-center justify-center">
                                <span class="mr-3">
                                    <?= $pct > 0 ? 'Continue Drafting' : 'Start Drafting' ?>
                                </span>
                                <i class="fas fa-arrow-right group-hover/btn:translate-x-1 transition-transform duration-300"></i>
                            </a>

                            <!-- Progress Hint -->
                            <?php if($pct > 0 && $pct < 100): ?>
                                <div class="mt-4 text-center">
                                    <p class="text-slate-400 text-sm">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        Keep matching terms to reveal more of the blueprint
                                    </p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Educational Info Section -->
        <div class="mt-16 bg-slate-800/80 backdrop-blur-xl border border-slate-700/50 rounded-3xl p-8 shadow-2xl">
            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold text-white mb-4">How Blueprint Revelation Works</h2>
                <p class="text-slate-400 max-w-2xl mx-auto">
                    Master technical terminology through interactive matching games. Each correct match reveals more of the engineering blueprint, combining education with achievement.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                        <i class="fas fa-graduation-cap text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2">Learn</h3>
                    <p class="text-slate-400">Master architectural and engineering terminology</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-gradient-to-r from-purple-500 to-pink-500 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                        <i class="fas fa-puzzle-piece text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2">Match</h3>
                    <p class="text-slate-400">Connect terms with their correct definitions</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-gradient-to-r from-green-500 to-emerald-500 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                        <i class="fas fa-unlock text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2">Unlock</h3>
                    <p class="text-slate-400">Reveal complete engineering blueprints</p>
                </div>
            </div>
        </div>
    </main>
</div>

<style>
/* Premium Animations */
@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
}

.animate-float {
    animation: float 6s ease-in-out infinite;
}

.animate-fade-in {
    animation: fadeIn 0.8s ease-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Custom scrollbar */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: rgba(51, 65, 85, 0.3);
    border-radius: 4px;
}

::-webkit-scrollbar-thumb {
    background: linear-gradient(to bottom, #6366f1, #8b5cf6, #ec4899);
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(to bottom, #4f46e5, #7c3aed, #db2777);
}

/* Responsive text scaling */
@media (max-width: 768px) {
    .text-5xl { font-size: 3rem; }
    .text-6xl { font-size: 3.5rem; }
}

/* Enhanced glow effects */
.glow-indigo {
    box-shadow: 0 0 30px rgba(99, 102, 241, 0.3);
}

.glow-purple {
    box-shadow: 0 0 30px rgba(139, 92, 246, 0.3);
}

.glow-pink {
    box-shadow: 0 0 30px rgba(236, 72, 153, 0.3);
}
</style>
