<?php include_once __DIR__ . '/../../layouts/header.php'; ?>

<div class="bg-gray-50 min-h-screen py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center mb-12">
            <h1 class="text-4xl font-extrabold text-gray-900 mb-2">Global Leaderboard</h1>
            <p class="text-gray-500 text-lg">Celebrate the top performers in our community.</p>
        </div>

        <?php if (empty($topUsers)): ?>
            <div class="text-center py-20 bg-white rounded-3xl shadow-sm">
                <div class="text-gray-400 mb-4 text-5xl"><i class="fas fa-trophy opacity-50"></i></div>
                <h2 class="text-xl font-bold text-gray-700">No rankings yet</h2>
                <p class="text-gray-500">Be the first to take an exam and earn XP!</p>
                <a href="<?php echo app_base_url('/exams'); ?>" class="mt-6 inline-block px-8 py-3 bg-indigo-600 text-white font-bold rounded-lg hover:bg-indigo-700 transition">Start Competing</a>
            </div>
        <?php else: ?>

            <!-- Podium Section (Top 3) -->
            <?php if (count($topUsers) >= 3): ?>
            <div class="flex flex-col md:flex-row justify-center items-end gap-6 mb-16 relative">
                
                <!-- 2nd Place -->
                <div class="order-2 md:order-1 flex flex-col items-center">
                    <div class="w-24 h-24 rounded-full border-4 border-gray-300 overflow-hidden shadow-lg mb-4 relative">
                        <?php $user2 = $topUsers[1]; ?>
                         <?php if(!empty($user2['avatar'])): ?>
                            <img src="<?php echo app_base_url('avatar/' . $user2['avatar']); ?>" class="w-full h-full object-cover">
                        <?php else: ?>
                            <div class="w-full h-full bg-gray-200 flex items-center justify-center text-gray-500 font-bold text-2xl">
                                <?php echo strtoupper(substr($user2['username'], 0, 1)); ?>
                            </div>
                        <?php endif; ?>
                        <div class="absolute bottom-0 inset-x-0 bg-gray-400 text-white text-xs font-bold text-center py-1">Silver</div>
                    </div>
                    <div class="bg-white p-4 rounded-xl shadow-sm text-center w-40 relative z-10 border-t-4 border-gray-300">
                        <div class="text-2xl font-bold text-gray-800">#2</div>
                        <div class="font-bold text-gray-900 truncate"><?php echo htmlspecialchars($user2['first_name'] ?: $user2['username']); ?></div>
                        <div class="text-indigo-600 font-bold"><?php echo number_format($user2['xp']); ?> XP</div>
                    </div>
                </div>

                <!-- 1st Place -->
                <div class="order-1 md:order-2 flex flex-col items-center z-20 -mt-10">
                    <div class="text-yellow-400 text-4xl mb-2"><i class="fas fa-crown"></i></div>
                    <div class="w-32 h-32 rounded-full border-4 border-yellow-400 overflow-hidden shadow-xl mb-4 relative ring-4 ring-yellow-100">
                         <?php $user1 = $topUsers[0]; ?>
                         <?php if(!empty($user1['avatar'])): ?>
                            <img src="<?php echo app_base_url('avatar/' . $user1['avatar']); ?>" class="w-full h-full object-cover">
                        <?php else: ?>
                            <div class="w-full h-full bg-yellow-100 flex items-center justify-center text-yellow-600 font-bold text-4xl">
                                <?php echo strtoupper(substr($user1['username'], 0, 1)); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="bg-white p-6 rounded-2xl shadow-lg text-center w-56 relative border-t-4 border-yellow-400 transform scale-105">
                        <div class="text-3xl font-extrabold text-gray-800">#1</div>
                        <div class="font-bold text-gray-900 text-lg truncate"><?php echo htmlspecialchars($user1['first_name'] ?: $user1['username']); ?></div>
                        <div class="text-indigo-600 font-extrabold text-lg"><?php echo number_format($user1['xp']); ?> XP</div>
                        <div class="text-xs text-gray-400 mt-1"><?php echo $user1['rank_title'] ?? 'Champion'; ?></div>
                    </div>
                </div>

                <!-- 3rd Place -->
                <div class="order-3 md:order-3 flex flex-col items-center">
                    <div class="w-24 h-24 rounded-full border-4 border-yellow-700 overflow-hidden shadow-lg mb-4 relative">
                        <?php $user3 = $topUsers[2]; ?>
                         <?php if(!empty($user3['avatar'])): ?>
                            <img src="<?php echo app_base_url('avatar/' . $user3['avatar']); ?>" class="w-full h-full object-cover">
                        <?php else: ?>
                            <div class="w-full h-full bg-orange-100 flex items-center justify-center text-yellow-800 font-bold text-2xl">
                                <?php echo strtoupper(substr($user3['username'], 0, 1)); ?>
                            </div>
                        <?php endif; ?>
                        <div class="absolute bottom-0 inset-x-0 bg-yellow-700 text-white text-xs font-bold text-center py-1">Bronze</div>
                    </div>
                    <div class="bg-white p-4 rounded-xl shadow-sm text-center w-40 relative z-10 border-t-4 border-yellow-700">
                        <div class="text-2xl font-bold text-gray-800">#3</div>
                        <div class="font-bold text-gray-900 truncate"><?php echo htmlspecialchars($user3['first_name'] ?: $user3['username']); ?></div>
                        <div class="text-indigo-600 font-bold"><?php echo number_format($user3['xp']); ?> XP</div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- The Rest of the List -->
            <div class="bg-white rounded-3xl shadow-sm overflow-hidden max-w-4xl mx-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 border-b border-gray-100 text-gray-500 uppercase text-xs font-bold">
                        <tr>
                            <th class="px-6 py-4 w-16 text-center">Rank</th>
                            <th class="px-6 py-4">User</th>
                            <th class="px-6 py-4 text-center">Tests Taken</th>
                            <th class="px-6 py-4 text-right">Total XP</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php foreach($topUsers as $index => $u): ?>
                            <?php 
                                // Skip top 3 if we showed podium? Or show all?
                                // Usually standard to show all or skip top 3.
                                // Let's show all for clarity, but highlight podium ones slightly or just normally.
                                // Actually, list usually starts from 4 if podium exists.
                                // But simpler to list all for now, or just start loop from 3.
                                if (count($topUsers) >= 3 && $index < 3) continue; // Skip podium users in table? Or keep them?
                                // Let's show list starting from 4 if we have >= 3 users.
                                $rank = $index + 1;
                                $isMe = ($currentUserData && $currentUserData['id'] == $u['id']);
                            ?>
                            <tr class="<?php echo $isMe ? 'bg-indigo-50 border-l-4 border-indigo-500' : 'hover:bg-gray-50'; ?> transition">
                                <td class="px-6 py-4 text-center font-bold text-gray-700 text-lg">
                                    #<?php echo $rank; ?>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 font-bold overflow-hidden">
                                            <?php if(!empty($u['avatar'])): ?>
                                                <img src="<?php echo app_base_url('avatar/' . $u['avatar']); ?>" class="w-full h-full object-cover">
                                            <?php else: ?>
                                                <?php echo strtoupper(substr($u['username'], 0, 1)); ?>
                                            <?php endif; ?>
                                        </div>
                                        <div>
                                            <div class="font-bold text-gray-900">
                                                <?php echo htmlspecialchars($u['first_name'] ?: $u['username']); ?>
                                                <?php if($isMe): ?><span class="bg-indigo-100 text-indigo-700 text-xs px-2 py-0.5 rounded ml-2">YOU</span><?php endif; ?>
                                            </div>
                                            <div class="text-xs text-gray-400"><?php echo $u['rank_title'] ?? 'Member'; ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center text-gray-600 font-medium">
                                    <?php echo $u['quiz_solved_count']; ?>
                                </td>
                                <td class="px-6 py-4 text-right font-bold text-indigo-600">
                                    <?php echo number_format($u['xp']); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($currentUserRank && $currentUserRank > 50): ?>
            <!-- My Sticky Rank (if not in top 50) -->
            <div class="fixed bottom-0 inset-x-0 bg-white border-t border-gray-200 p-4 shadow-lg transform translate-y-0 transition">
                <div class="max-w-4xl mx-auto flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="text-2xl font-bold text-gray-400">#<?php echo $currentUserRank; ?></div>
                        <div class="flex items-center gap-3">
                             <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold">
                                <?php echo strtoupper(substr($currentUserData['username'], 0, 1)); ?>
                            </div>
                            <div class="font-bold text-gray-900">You</div>
                        </div>
                    </div>
                     <div class="text-indigo-600 font-bold text-lg"><?php echo number_format($currentUserData['xp']); ?> XP</div>
                </div>
            </div>
            <?php endif; ?>

        <?php endif; ?>

    </div>
</div>

<?php include_once __DIR__ . '/../../layouts/footer.php'; ?>
