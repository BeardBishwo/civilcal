<?php
// themes/default/views/quiz/home.php
?>
<div class="min-h-screen bg-gray-900 text-white font-sans">
    <!-- Hero Section -->
    <div class="relative overflow-hidden bg-gradient-to-br from-gray-900 via-gray-900 to-indigo-900/40 pt-20 pb-20">
        <div class="container mx-auto px-4 relative z-10">
            <div class="text-center max-w-3xl mx-auto">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 text-sm font-medium mb-6">
                    <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                    50,000+ Active Learners
                </div>

                <h1 class="text-5xl md:text-6xl font-extrabold mb-6 tracking-tight">
                    Master Your <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-indigo-500">Engineering Dreams</span>
                </h1>

                <p class="text-xl text-gray-400 mb-10 leading-relaxed">
                    AI-powered mock tests, real-time analytics, and gamified learning for Loksewa, License & Entrance exams.
                </p>

                <div class="relative max-w-2xl mx-auto">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="h-6 w-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text"
                        class="block w-full pl-12 pr-32 py-4 bg-gray-800/50 border border-gray-700 text-white rounded-2xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent placeholder-gray-500 backdrop-blur-sm transition-all shadow-xl"
                        placeholder="Search for exams, topics, or categories...">
                    <button class="absolute right-2 top-2 bottom-2 px-6 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl transition-colors shadow-lg shadow-indigo-500/20">
                        Search
                    </button>
                </div>
            </div>
        </div>

        <!-- Decoration Blurs -->
        <div class="absolute top-0 left-1/4 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>
    </div>

    <!-- Quiz Modes Grid -->
    <div class="container mx-auto px-4 py-16 -mt-10 relative z-20">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

            <?php if (!empty($modes['quiz_mode_zone'])): ?>
                <!-- Quiz Zone -->
                <a href="<?= app_base_url('/quiz/zone') ?>" class="group relative bg-gray-800/50 hover:bg-gray-800 border border-gray-700 hover:border-indigo-500/30 rounded-2xl p-6 transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl hover:shadow-indigo-500/10 backdrop-blur-md">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-green-400 to-emerald-600 flex items-center justify-center mb-6 text-white shadow-lg shadow-green-500/20 group-hover:scale-110 transition-transform">
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2 group-hover:text-green-400 transition-colors">Quiz Zone</h3>
                    <p class="text-gray-400 text-sm">Select your favorite Zone to play chapter-wise quizzes.</p>

                    <div class="absolute top-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity">
                        <div class="w-2 h-2 rounded-full bg-green-400 shadow-[0_0_10px_rgba(74,222,128,0.5)]"></div>
                    </div>
                </a>
            <?php endif; ?>

            <?php if (!empty($modes['quiz_mode_daily'])): ?>
                <!-- Daily Quiz -->
                <a href="<?= app_base_url('/quiz/daily') ?>" class="group relative bg-gray-800/50 hover:bg-gray-800 border border-gray-700 hover:border-indigo-500/30 rounded-2xl p-6 transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl hover:shadow-indigo-500/10 backdrop-blur-md">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-pink-500 to-rose-600 flex items-center justify-center mb-6 text-white shadow-lg shadow-rose-500/20 group-hover:scale-110 transition-transform">
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2 group-hover:text-rose-400 transition-colors">Daily Quiz</h3>
                    <p class="text-gray-400 text-sm">Daily basic new quiz game to build your streak.</p>
                    <span class="absolute top-4 right-4 px-2 py-1 bg-red-500/20 text-red-400 text-xs font-bold rounded uppercase tracking-wider">New</span>
                </a>
            <?php endif; ?>

            <?php if (!empty($modes['quiz_mode_contest'])): ?>
                <!-- Contest -->
                <a href="<?= app_base_url('/quiz/contests') ?>" class="group relative bg-gray-800/50 hover:bg-gray-800 border border-gray-700 hover:border-indigo-500/30 rounded-2xl p-6 transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl hover:shadow-indigo-500/10 backdrop-blur-md">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-yellow-400 to-amber-600 flex items-center justify-center mb-6 text-white shadow-lg shadow-amber-500/20 group-hover:scale-110 transition-transform">
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2 group-hover:text-amber-400 transition-colors">Contest Play</h3>
                    <p class="text-gray-400 text-sm">Compete in scheduled exams for prizes and glory.</p>
                </a>
            <?php endif; ?>

            <?php if (!empty($modes['quiz_mode_battle_1v1'])): ?>
                <!-- 1 vs 1 Battle -->
                <a href="<?= app_base_url('/quiz/battle') ?>" class="group relative bg-gray-800/50 hover:bg-gray-800 border border-gray-700 hover:border-indigo-500/30 rounded-2xl p-6 transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl hover:shadow-indigo-500/10 backdrop-blur-md">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center mb-6 text-white shadow-lg shadow-indigo-500/20 group-hover:scale-110 transition-transform">
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2 group-hover:text-indigo-400 transition-colors">1 V/S 1 Battle</h3>
                    <p class="text-gray-400 text-sm">Challenge random players to a duel.</p>
                </a>
            <?php endif; ?>

            <!-- More placeholders for Math Mania, Guess Word etc (controlled by toggles) -->
            <?php if (!empty($modes['quiz_mode_math_mania'])): ?>
                <!-- Math Mania -->
                <a href="<?= app_base_url('/quiz/math-mania') ?>" class="group relative bg-gray-800/50 hover:bg-gray-800 border border-gray-700 hover:border-indigo-500/30 rounded-2xl p-6 transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl hover:shadow-indigo-500/10 backdrop-blur-md">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-red-400 to-red-600 flex items-center justify-center mb-6 text-white shadow-lg shadow-red-500/20 group-hover:scale-110 transition-transform">
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2 group-hover:text-red-400 transition-colors">Math Mania</h3>
                    <p class="text-gray-400 text-sm">Challenge your mind with complex equations.</p>
                </a>
            <?php endif; ?>

            <?php if (!empty($modes['quiz_mode_exam'])): ?>
                <!-- Exam Mode -->
                <a href="<?= app_base_url('/quiz/exam') ?>" class="group relative bg-gray-800/50 hover:bg-gray-800 border border-gray-700 hover:border-indigo-500/30 rounded-2xl p-6 transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl hover:shadow-indigo-500/10 backdrop-blur-md">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-purple-500 to-violet-600 flex items-center justify-center mb-6 text-white shadow-lg shadow-purple-500/20 group-hover:scale-110 transition-transform">
                        <div class="text-2xl"><i class="fas fa-file-alt"></i></div>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2 group-hover:text-purple-400 transition-colors">Exam Mode</h3>
                    <p class="text-gray-400 text-sm">Structured exams with time limits and grading.</p>
                </a>
            <?php endif; ?>

            <?php if (!empty($modes['quiz_mode_guess_word'])): ?>
                <!-- Guess Word -->
                <a href="<?= app_base_url('/quiz/guess-word') ?>" class="group relative bg-gray-800/50 hover:bg-gray-800 border border-gray-700 hover:border-indigo-500/30 rounded-2xl p-6 transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl hover:shadow-indigo-500/10 backdrop-blur-md">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-pink-500 to-rose-500 flex items-center justify-center mb-6 text-white shadow-lg shadow-pink-500/20 group-hover:scale-110 transition-transform">
                        <div class="text-2xl"><i class="fas fa-spell-check"></i></div>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2 group-hover:text-pink-400 transition-colors">Guess Word</h3>
                    <p class="text-gray-400 text-sm">Word guessing games and vocabulary challenges.</p>
                </a>
            <?php endif; ?>

            <?php if (!empty($modes['quiz_mode_multi_match'])): ?>
                <!-- Multi Match -->
                <a href="<?= app_base_url('/quiz/multi-match') ?>" class="group relative bg-gray-800/50 hover:bg-gray-800 border border-gray-700 hover:border-indigo-500/30 rounded-2xl p-6 transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl hover:shadow-indigo-500/10 backdrop-blur-md">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-teal-400 to-teal-600 flex items-center justify-center mb-6 text-white shadow-lg shadow-teal-500/20 group-hover:scale-110 transition-transform">
                        <div class="text-2xl"><i class="fas fa-puzzle-piece"></i></div>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2 group-hover:text-teal-400 transition-colors">Multi Match</h3>
                    <p class="text-gray-400 text-sm">Match multiple items and pairs correctly.</p>
                </a>
            <?php endif; ?>

            <?php if (!empty($modes['quiz_mode_true_false'])): ?>
                <!-- True Formse -->
                <a href="<?= app_base_url('/quiz/true-false') ?>" class="group relative bg-gray-800/50 hover:bg-gray-800 border border-gray-700 hover:border-indigo-500/30 rounded-2xl p-6 transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl hover:shadow-indigo-500/10 backdrop-blur-md">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-cyan-400 to-cyan-600 flex items-center justify-center mb-6 text-white shadow-lg shadow-cyan-500/20 group-hover:scale-110 transition-transform">
                        <div class="text-2xl"><i class="fas fa-check-double"></i></div>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2 group-hover:text-cyan-400 transition-colors">True/False</h3>
                    <p class="text-gray-400 text-sm">Quick true or false statement quizzes.</p>
                </a>
            <?php endif; ?>

            <?php if (!empty($modes['quiz_mode_battle_group'])): ?>
                <!-- Group Battle -->
                <a href="<?= app_base_url('/quiz/battle-group') ?>" class="group relative bg-gray-800/50 hover:bg-gray-800 border border-gray-700 hover:border-indigo-500/30 rounded-2xl p-6 transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl hover:shadow-indigo-500/10 backdrop-blur-md">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-orange-400 to-orange-600 flex items-center justify-center mb-6 text-white shadow-lg shadow-orange-500/20 group-hover:scale-110 transition-transform">
                        <div class="text-2xl"><i class="fas fa-users"></i></div>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2 group-hover:text-orange-400 transition-colors">Group Battle</h3>
                    <p class="text-gray-400 text-sm">Team-based quiz competitions and battles.</p>
                </a>
            <?php endif; ?>

        </div>
    </div>
</div>