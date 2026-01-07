<?php include_once __DIR__ . '/../../layouts/header.php'; ?>

<div class="bg-gray-50 min-h-screen py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-bold text-gray-900">My Exam History</h1>
            <a href="<?php echo app_base_url('/profile'); ?>" class="text-indigo-600 font-medium hover:underline flex items-center">
                <i class="fas fa-arrow-left mr-2"></i> Back to Dashboard
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
            
            <?php if (!empty($exams)): ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 text-gray-600 text-sm font-bold uppercase border-b border-gray-200">
                            <th class="p-6">Date</th>
                            <th class="p-6">Category</th>
                            <th class="p-6">Mode</th>
                            <th class="p-6 text-center">Score</th>
                            <th class="p-6 text-center">Status</th>
                            <th class="p-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        <?php foreach($exams as $exam): ?>
                        <?php 
                            $pct = ($exam['total_questions'] > 0) ? round(($exam['score'] / $exam['total_questions']) * 100) : 0;
                            $pass = $pct >= 50;
                        ?>
                        <tr class="hover:bg-gray-50 transition group">
                            <td class="p-6">
                                <div class="font-bold text-gray-900"><?php echo date('M d, Y', strtotime($exam['created_at'])); ?></div>
                                <div class="text-xs text-gray-500"><?php echo date('h:i A', strtotime($exam['created_at'])); ?></div>
                            </td>
                            <td class="p-6">
                                <span class="font-medium"><?php echo htmlspecialchars($exam['category_name'] ?: 'General'); ?></span>
                            </td>
                            <td class="p-6 capitalize">
                                <span class="px-2 py-1 rounded text-xs font-bold <?php echo ($exam['mode'] == 'mock') ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700'; ?>">
                                    <?php echo $exam['mode']; ?>
                                </span>
                            </td>
                            <td class="p-6 text-center">
                                <div class="font-bold text-lg <?php echo $pass ? 'text-green-600' : 'text-orange-600'; ?>">
                                    <?php echo $pct; ?>%
                                </div>
                                <div class="text-xs text-gray-400"><?php echo $exam['score']; ?>/<?php echo $exam['total_questions']; ?></div>
                            </td>
                            <td class="p-6 text-center">
                                <?php if($exam['status'] == 'completed'): ?>
                                    <span class="inline-flex items-center gap-1 text-green-600 font-bold text-sm">
                                        <i class="fas fa-check-circle"></i> Completed
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center gap-1 text-gray-500 font-medium text-sm">
                                        <i class="fas fa-clock"></i> In Progress
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="p-6 text-right">
                                <?php if($exam['status'] == 'completed'): ?>
                                <a href="<?php echo app_base_url('exams/result/' . $exam['id']); ?>" class="inline-flex items-center px-4 py-2 rounded-lg bg-white border border-gray-200 hover:border-indigo-300 hover:text-indigo-600 transition font-bold text-sm shadow-sm group-hover:shadow-md">
                                    Result <i class="fas fa-chevron-right ml-2 opacity-50 group-hover:opacity-100"></i>
                                </a>
                                <?php else: ?>
                                    <a href="<?php echo app_base_url('exams/take/' . $exam['id']); ?>" class="inline-flex items-center px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 transition font-bold text-sm shadow-sm">
                                        Resume <i class="fas fa-play ml-2"></i>
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Simple Pagination UI (Can be improved) -->
            <div class="p-6 border-t border-gray-200 bg-gray-50 flex justify-between items-center">
                <button disabled class="px-4 py-2 rounded bg-white border text-gray-400 cursor-not-allowed">Previous</button>
                <div class="text-sm text-gray-500">Page 1</div>
                <button class="px-4 py-2 rounded bg-white border text-gray-700 hover:bg-gray-100">Next</button>
            </div>

            <?php else: ?>
            <div class="p-20 text-center">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-400">
                        <i class="fas fa-history text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900">No History Found</h3>
                <p class="text-gray-500 mt-2 mb-6">You haven't taken any exams yet.</p>
                <a href="<?php echo app_base_url('exams'); ?>" class="px-6 py-2 rounded-xl bg-indigo-600 text-white font-bold hover:bg-indigo-700">
                    Browse Exams
                </a>
            </div>
            <?php endif; ?>
        </div>

    </div>
</div>

<?php include_once __DIR__ . '/../../layouts/footer.php'; ?>
