<div class="min-h-screen bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-900 relative overflow-hidden">

    <!-- Premium Background Effects -->
    <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%239C92AC" fill-opacity="0.04"%3E%3Ccircle cx="30" cy="30" r="2"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-40"></div>

    <!-- Animated Background Elements -->
    <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-gradient-to-r from-cyan-500/15 to-blue-500/15 rounded-full blur-3xl animate-pulse"></div>
    <div class="absolute bottom-1/4 right-1/4 w-80 h-80 bg-gradient-to-r from-indigo-500/15 to-purple-500/15 rounded-full blur-3xl animate-pulse delay-1000"></div>
    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-64 h-64 bg-gradient-to-r from-emerald-500/10 to-teal-500/10 rounded-full blur-3xl animate-pulse delay-2000"></div>

    <!-- Premium Header -->
    <header class="relative z-20 p-8">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <div class="flex items-center gap-6">
                <a href="<?= app_base_url('/quiz') ?>" class="group inline-flex items-center justify-center w-14 h-14 bg-slate-800/80 backdrop-blur-xl border border-slate-700/50 rounded-2xl text-white hover:bg-slate-700/80 hover:border-slate-600/50 transition-all duration-300 hover:transform hover:scale-110 shadow-lg">
                    <i class="fas fa-arrow-left text-xl group-hover:-translate-x-1 transition-transform duration-300"></i>
                </a>
                <div>
                    <h1 class="text-4xl md:text-5xl font-black text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 via-blue-400 to-indigo-400 mb-2 tracking-tight">
                        EXAM HALL
                    </h1>
                    <p class="text-slate-300 font-light text-lg">Choose your challenge and prove your mastery</p>
                </div>
            </div>

            <!-- Stats Badge -->
            <div class="bg-slate-800/80 backdrop-blur-xl border border-cyan-400/30 rounded-2xl px-6 py-4 shadow-lg">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-r from-cyan-500 to-blue-500 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-graduation-cap text-white"></i>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-cyan-400"><?= count($exams) ?></div>
                        <div class="text-xs uppercase tracking-wider text-slate-400 font-medium">Available Exams</div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="relative z-20 max-w-7xl mx-auto px-8 pb-16">

        <?php if (empty($exams)): ?>
            <!-- Empty State -->
            <div class="text-center py-20">
                <div class="relative mb-12">
                    <div class="w-32 h-32 bg-gradient-to-r from-slate-700 to-slate-600 rounded-full flex items-center justify-center mx-auto shadow-2xl">
                        <i class="fas fa-clipboard-list text-slate-400 text-6xl"></i>
                    </div>
                    <div class="absolute -top-2 -right-2 w-8 h-8 bg-cyan-400 rounded-full animate-ping"></div>
                </div>
                <h3 class="text-3xl font-bold text-white mb-4">The Exam Hall is Quiet</h3>
                <p class="text-xl text-slate-400 mb-8 max-w-2xl mx-auto leading-relaxed">
                    No examinations are currently scheduled. New challenges will be available soon. Stay prepared and check back later.
                </p>
                <a href="<?= app_base_url('/quiz') ?>" class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-slate-700 to-slate-800 hover:from-slate-800 hover:to-slate-900 text-white font-bold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover:transform hover:scale-105 border border-slate-600/50">
                    <i class="fas fa-home mr-3"></i>
                    Return to Dashboard
                </a>
            </div>
        <?php else: ?>

            <?php foreach ($grouped_exams as $type => $group): ?>
                <!-- Exam Category Section -->
                <div class="mb-16">
                    <!-- Category Header -->
                    <div class="flex items-center gap-4 mb-8">
                        <div class="w-12 h-12 bg-gradient-to-r from-cyan-500 to-blue-500 rounded-xl flex items-center justify-center shadow-lg">
                            <i class="fas fa-layer-group text-white"></i>
                        </div>
                        <div>
                            <h2 class="text-3xl font-bold text-white mb-1"><?= $type ?> Exams</h2>
                            <div class="h-1 w-24 bg-gradient-to-r from-cyan-400 to-blue-400 rounded-full"></div>
                        </div>
                    </div>

                    <!-- Exam Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        <?php foreach ($group as $exam): ?>
                            <!-- Exam Card -->
                            <div class="group relative">
                                <!-- Glow Effect -->
                                <div class="absolute inset-0 bg-gradient-to-r from-cyan-500/10 via-blue-500/10 to-indigo-500/10 rounded-3xl blur-2xl group-hover:blur-xl transition-all duration-500 opacity-0 group-hover:opacity-100"></div>

                                <!-- Card Content -->
                                <div class="relative bg-slate-800/90 backdrop-blur-xl border border-slate-700/50 rounded-3xl p-8 hover:border-cyan-400/30 transition-all duration-300 hover:transform hover:scale-105 shadow-2xl hover:shadow-cyan-400/10 overflow-hidden">

                                    <!-- Top Border Gradient -->
                                    <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-cyan-400 via-blue-500 to-indigo-400"></div>

                                    <!-- Status Badges -->
                                    <div class="flex justify-between items-start mb-6">
                                        <div class="px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-500 text-white font-bold text-sm rounded-xl shadow-lg">
                                            <?= ucfirst($exam['mode']) ?>
                                        </div>
                                        <?php if ($exam['price'] > 0): ?>
                                            <div class="px-4 py-2 bg-gradient-to-r from-yellow-500 to-orange-500 text-black font-bold text-sm rounded-xl shadow-lg flex items-center gap-2">
                                                <i class="fas fa-gem"></i>
                                                Premium
                                            </div>
                                        <?php else: ?>
                                            <div class="px-4 py-2 bg-gradient-to-r from-green-500 to-emerald-500 text-white font-bold text-sm rounded-xl shadow-lg flex items-center gap-2">
                                                <i class="fas fa-check"></i>
                                                Free
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Exam Title -->
                                    <h3 class="text-2xl font-bold text-white mb-4 leading-tight">
                                        <?= htmlspecialchars($exam['title']) ?>
                                    </h3>

                                    <!-- Description -->
                                    <p class="text-slate-400 mb-8 leading-relaxed line-clamp-3">
                                        <?= htmlspecialchars($exam['description'] ?? 'Challenge yourself with this comprehensive examination.') ?>
                                    </p>

                                    <!-- Exam Stats -->
                                    <div class="grid grid-cols-2 gap-4 mb-8">
                                        <div class="bg-slate-700/50 rounded-xl p-4 text-center border border-slate-600/30">
                                            <div class="text-cyan-400 font-bold text-xl mb-1">
                                                <i class="fas fa-clock mr-1"></i>
                                                <?= $exam['duration_minutes'] ?>
                                            </div>
                                            <p class="text-slate-400 text-xs uppercase tracking-wider font-medium">Minutes</p>
                                        </div>
                                        <div class="bg-slate-700/50 rounded-xl p-4 text-center border border-slate-600/30">
                                            <div class="text-blue-400 font-bold text-xl mb-1">
                                                <i class="fas fa-question-circle mr-1"></i>
                                                <!-- Assuming questions count, adjust if you have actual count -->
                                                <?= isset($exam['question_count']) ? $exam['question_count'] : 'Multi' ?>
                                            </div>
                                            <p class="text-slate-400 text-xs uppercase tracking-wider font-medium">Questions</p>
                                        </div>
                                    </div>

                                    <!-- Start Button -->
                                    <a href="<?= app_base_url('/quiz/exam/start/' . $exam['slug']) ?>"
                                        class="group/btn w-full py-4 px-6 bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-600 hover:to-blue-700 text-white font-bold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover:transform hover:scale-105 flex items-center justify-center">
                                        <span class="mr-3">Start Examination</span>
                                        <i class="fas fa-arrow-right group-hover/btn:translate-x-1 transition-transform duration-300"></i>
                                    </a>

                                    <!-- Footer Info -->
                                    <div class="mt-6 pt-6 border-t border-slate-700/50">
                                        <div class="flex items-center justify-between text-sm">
                                            <span class="text-slate-400">
                                                <i class="fas fa-calendar-alt mr-2"></i>
                                                Available until
                                            </span>
                                            <span class="text-cyan-400 font-medium">
                                                <?= $exam['end_datetime'] ? date('M d, Y', strtotime($exam['end_datetime'])) : 'No Limit' ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>

        <?php endif; ?>

    </main>
</div>

<style>
.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

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
    background: linear-gradient(to bottom, #06b6d4, #3b82f6, #6366f1);
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(to bottom, #0891b2, #2563eb, #4f46e5);
}

/* Responsive text scaling */
@media (max-width: 768px) {
    .text-4xl { font-size: 2.5rem; }
    .text-5xl { font-size: 3rem; }
}

/* Enhanced glow effects */
.glow-cyan {
    box-shadow: 0 0 30px rgba(6, 182, 212, 0.3);
}

.glow-blue {
    box-shadow: 0 0 30px rgba(59, 130, 246, 0.3);
}

.glow-indigo {
    box-shadow: 0 0 30px rgba(99, 102, 241, 0.3);
}
</style>