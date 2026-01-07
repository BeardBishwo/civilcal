<?php include_once __DIR__ . '/../../layouts/header.php'; ?>

<div class="bg-gray-50 min-h-screen py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Welcome Header -->
        <div class="bg-white rounded-2xl shadow-sm p-8 mb-8 flex flex-col md:flex-row items-center justify-between">
            <div class="flex items-center gap-6">
                <!-- Avatar -->
                <div class="w-20 h-20 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 text-2xl font-bold shadow-inner">
                    <?php if(!empty($user['avatar'])): ?>
                        <img src="<?php echo app_base_url('avatar/' . $user['avatar']); ?>" class="w-full h-full rounded-full object-cover">
                    <?php else: ?>
                        <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
                    <?php endif; ?>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Welcome, <?php echo htmlspecialchars($user['first_name'] ?: $user['username']); ?>!</h1>
                    <p class="text-gray-500">Member since <?php echo date('M Y', strtotime($user['created_at'])); ?></p>
                </div>
            </div>
            <div class="mt-4 md:mt-0 flex gap-3">
                 <a href="<?php echo app_base_url('exams'); ?>" class="px-6 py-2 rounded-lg bg-indigo-600 text-white font-medium hover:bg-indigo-700 transition shadow-sm">
                    <i class="fas fa-play mr-2"></i> Start Exam
                </a>
                <a href="<?php echo app_base_url('logout'); ?>" class="px-6 py-2 rounded-lg border border-gray-300 text-gray-700 font-medium hover:bg-gray-100 transition">
                    Sign Out
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Left Column: Stats & Profile -->
            <div class="space-y-8">
                <!-- Stats Card -->
                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">Your Performance</h2>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-blue-50 p-4 rounded-xl">
                            <span class="block text-2xl font-bold text-blue-700"><?php echo $statistics['quizzes_completed_count'] ?? 0; ?></span>
                            <span class="text-xs text-blue-600 font-medium uppercase">Exams Taken</span>
                        </div>
                        <div class="bg-green-50 p-4 rounded-xl">
                            <span class="block text-2xl font-bold text-green-700"><?php echo $rank_data['xp'] ?? 0; ?></span>
                            <span class="text-xs text-green-600 font-medium uppercase">XP Earned</span>
                        </div>
                        <!-- Add Average Score if available in stats -->
                    </div>
                    <div class="mt-4 text-center border-t pt-4">
                        <a href="<?php echo app_base_url('/profile/analytics'); ?>" class="text-indigo-600 font-medium hover:underline text-sm flex items-center justify-center">
                            View Full Analytics <i class="fas fa-chevron-right ml-1 text-xs"></i>
                        </a>
                    </div>
                </div>

                <!-- Profile Completion -->
                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">Profile Completion</h2>
                    <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
                        <div class="bg-indigo-600 h-2 rounded-full" style="width: <?php echo $profile_completion; ?>%"></div>
                    </div>
                    <span class="text-sm text-gray-500"><?php echo $profile_completion; ?>% Completed</span>
                </div>
            </div>

            <!-- Right Column: Recent Activity -->
            <div class="lg:col-span-2 space-y-8">
                
                <!-- Recent Exams -->
                <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                        <h2 class="text-lg font-bold text-gray-900">Recent Exam Sessions</h2>
                        <a href="<?php echo app_base_url('/profile/exams'); ?>" class="text-sm text-indigo-600 font-bold hover:underline">View All History</a>
                    </div>
                    
                    <?php if (!empty($recent_exams)): ?>
                    <div class="divide-y divide-gray-100">
                        <?php foreach($recent_exams as $exam): ?>
                            <?php
                                $score = $exam['score'];
                                $total = $exam['total_questions'];
                                $pct = ($total > 0) ? round(($score/$total)*100) : 0;
                                $statusColor = ($pct >= 50) ? 'text-green-600' : 'text-orange-600';
                            ?>
                            <div class="p-6 hover:bg-gray-50 transition flex items-center justify-between group">
                                <div class="flex items-center gap-4">
                                     <div class="w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center text-gray-400 group-hover:bg-indigo-100 group-hover:text-indigo-600 transition">
                                        <i class="fas fa-file-alt text-xl"></i>
                                     </div>
                                     <div>
                                        <h3 class="font-bold text-gray-900 text-lg"><?php echo htmlspecialchars($exam['category_name'] ?: 'General Exam'); ?></h3>
                                        <div class="text-sm text-gray-500 mt-1 flex gap-2">
                                            <span><i class="far fa-calendar-alt mr-1"></i> <?php echo date('M d, Y', strtotime($exam['created_at'])); ?></span>
                                            <span>&bull;</span>
                                            <span class="capitalize"><i class="fas fa-layer-group mr-1"></i> <?php echo $exam['mode']; ?> Mode</span>
                                        </div>
                                     </div>
                                </div>
                                <div class="flex items-center gap-6">
                                    <div class="text-right">
                                        <div class="text-2xl font-bold <?php echo $statusColor; ?>"><?php echo $pct; ?>%</div>
                                        <div class="text-xs text-gray-400 font-medium">Score: <?php echo $score; ?>/<?php echo $total; ?></div>
                                    </div>
                                    <div>
                                        <a href="<?php echo app_base_url('exams/result/' . $exam['id']); ?>" class="px-4 py-2 rounded-lg border border-gray-200 text-gray-600 text-sm font-bold hover:bg-indigo-50 hover:text-indigo-600 hover:border-indigo-200 transition">
                                            Review
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <div class="p-12 text-center text-gray-500">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-400">
                             <i class="fas fa-inbox text-3xl"></i>
                        </div>
                        <p class="text-lg font-medium text-gray-600">No exams taken yet</p>
                        <p class="text-sm text-gray-400 mb-6">Your exam history and results will appear here.</p>
                        
                        <a href="<?php echo app_base_url('exams'); ?>" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-indigo-600 text-white font-bold hover:bg-indigo-700 shadow-md hover:shadow-lg transition transform hover:-translate-y-0.5">
                            <i class="fas fa-play"></i> Start Your First Exam
                        </a>
                    </div>
                    <?php endif; ?>
                </div>

            </div>
        </div>

    </div>
</div>

<?php include_once __DIR__ . '/../../layouts/footer.php'; ?>
