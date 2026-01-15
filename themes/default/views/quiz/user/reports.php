<?php

/**
 * User Report History View
 */
?>
<div class="max-w-6xl mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-slate-800 flex items-center gap-3">
            <span class="p-3 bg-indigo-100 text-indigo-600 rounded-2xl shadow-sm">
                <i class="fas fa-flag"></i>
            </span>
            My Reported Issues
        </h1>
        <p class="text-slate-500 mt-2 ml-16">Track the status of your reports and view feedback from our administrators.</p>
    </div>

    <!-- Hall of Heroes (Phase 10) -->
    <div class="mb-8 overflow-hidden bg-gradient-to-r from-indigo-600 to-violet-700 rounded-3xl p-6 shadow-xl shadow-indigo-200 text-white relative">
        <div class="absolute top-0 right-0 p-8 opacity-10">
            <i class="fas fa-trophy text-8xl transform rotate-12"></i>
        </div>
        <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="text-center md:text-left">
                <h3 class="text-xl font-bold flex items-center justify-center md:justify-start gap-2">
                    <i class="fas fa-award text-amber-400"></i> Hall of Heroes
                </h3>
                <p class="text-indigo-100 text-sm mt-1">Our top contributors making the platform better Every Day.</p>
            </div>

            <div class="flex flex-wrap justify-center gap-4">
                <?php if (!empty($leaderboard)): ?>
                    <?php foreach ($leaderboard as $index => $hero):
                        $rankColors = ['bg-amber-400 text-amber-900', 'bg-slate-300 text-slate-800', 'bg-orange-400 text-orange-900'];
                        $rankColor = $rankColors[$index] ?? 'bg-white/10 text-white';
                    ?>
                        <div class="flex items-center gap-3 bg-white/10 backdrop-blur-md rounded-2xl px-4 py-3 border border-white/10">
                            <div class="w-8 h-8 rounded-full <?php echo $rankColor; ?> flex items-center justify-center font-black text-xs">
                                <?php echo $index + 1; ?>
                            </div>
                            <div>
                                <div class="text-sm font-bold"><?php echo htmlspecialchars($hero['username']); ?></div>
                                <div class="text-[10px] text-indigo-100 font-bold uppercase tracking-tighter"><?php echo $hero['resolved_count']; ?> Fixes</div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-white/50 text-xs italic">The hall is currently empty. Be the first hero!</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <?php
        $stats = [
            'pending' => 0,
            'resolved' => 0,
            'ignored' => 0
        ];
        foreach ($reports as $r) $stats[$r['status']]++;
        ?>
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl">
                <i class="fas fa-clock"></i>
            </div>
            <div>
                <div class="text-2xl font-black text-slate-800"><?php echo $stats['pending']; ?></div>
                <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Pending Investigations</div>
            </div>
        </div>
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl">
                <i class="fas fa-check-circle"></i>
            </div>
            <div>
                <div class="text-2xl font-black text-slate-800"><?php echo $stats['resolved']; ?></div>
                <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Resolved & Rewarded</div>
            </div>
        </div>
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-slate-50 text-slate-600 flex items-center justify-center text-xl">
                <i class="fas fa-archive"></i>
            </div>
            <div>
                <div class="text-2xl font-black text-slate-800"><?php echo $stats['ignored']; ?></div>
                <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Archived / Dismissed</div>
            </div>
        </div>
    </div>

    <!-- Reports Table -->
    <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
        <div class="p-6 border-b border-slate-50 flex justify-between items-center">
            <h3 class="font-bold text-slate-700">Recent Reports</h3>
            <div class="text-xs text-slate-400 italic">Showing your last <?php echo count($reports); ?> reports</div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Question / Issue</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Status</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <?php if (empty($reports)): ?>
                        <tr>
                            <td colspan="3" class="px-6 py-20 text-center">
                                <div class="max-w-xs mx-auto">
                                    <div class="w-16 h-16 bg-slate-50 text-slate-200 rounded-full flex items-center justify-center text-3xl mx-auto mb-4">
                                        <i class="fas fa-search"></i>
                                    </div>
                                    <h4 class="font-bold text-slate-800">No reports found</h4>
                                    <p class="text-xs text-slate-400 mt-1">Help us improve the question bank by reporting errors you find during practice!</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($reports as $r):
                            $statusClass = [
                                'pending' => 'bg-amber-100 text-amber-700',
                                'resolved' => 'bg-emerald-100 text-emerald-700',
                                'ignored' => 'bg-slate-100 text-slate-600'
                            ][$r['status']];
                        ?>
                            <tr class="hover:bg-slate-50/50 transition-colors group">
                                <td class="px-6 py-6 max-w-md">
                                    <div class="text-sm font-bold text-slate-800 line-clamp-1 group-hover:text-indigo-600 transition">
                                        <?php echo htmlspecialchars($r['question_text']); ?>
                                    </div>
                                    <div class="flex items-center gap-2 mt-2">
                                        <span class="px-2 py-0.5 rounded-md bg-indigo-50 text-indigo-600 text-[10px] font-bold uppercase tracking-wider">
                                            <?php echo str_replace('_', ' ', $r['issue_type'] ?: 'other'); ?>
                                        </span>
                                        <span class="text-xs italic text-slate-400 border-l pl-2 border-slate-200">
                                            "<?php echo htmlspecialchars($r['description']); ?>"
                                        </span>
                                    </div>

                                    <?php if (!empty($r['screenshot'])): ?>
                                        <div class="mt-3">
                                            <button onclick="viewEvidence('<?php echo app_base_url($r['screenshot']); ?>')"
                                                class="flex items-center gap-2 px-3 py-1.5 rounded-lg bg-indigo-50 text-indigo-600 border border-indigo-100 text-xs font-bold hover:bg-indigo-100 transition shadow-sm">
                                                <i class="fas fa-camera"></i> View Evidence
                                            </button>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($r['status'] !== 'pending'): ?>
                                        <div class="mt-4 p-3 bg-slate-50 rounded-xl relative border border-slate-100">
                                            <div class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1 flex items-center gap-1">
                                                <i class="fas fa-comment-dots"></i> Admin Response
                                            </div>
                                            <div class="text-xs text-slate-600 leading-relaxed font-medium">
                                                <?php echo !empty($r['reply_message']) ? htmlspecialchars($r['reply_message']) : ($r['status'] === 'resolved' ? 'The issue has been verified and fixed. Thank you!' : 'This report was archived as it does not meet our quality guidelines.'); ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-6 align-top">
                                    <span class="px-3 py-1 rounded-full <?php echo $statusClass; ?> text-[10px] font-black uppercase tracking-widest shadow-sm">
                                        <?php echo $r['status']; ?>
                                    </span>
                                    <?php if ($r['status'] === 'resolved'): ?>
                                        <div class="mt-2 text-[10px] font-bold text-emerald-600 flex items-center gap-1 animate__animated animate__heartBeat">
                                            <i class="fas fa-coins"></i> Reward Sent
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-6 text-right align-top">
                                    <div class="text-xs font-bold text-slate-700"><?php echo date('M d, Y', strtotime($r['created_at'])); ?></div>
                                    <div class="text-[10px] text-slate-400 mt-1"><?php echo date('h:i A', strtotime($r['created_at'])); ?></div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function viewEvidence(url) {
        Swal.fire({
            title: 'Visual Evidence',
            imageUrl: url,
            imageAlt: 'Evidence Screenshot',
            width: 'auto',
            confirmButtonText: 'Got it',
            confirmButtonColor: '#4f46e5',
            customClass: {
                image: 'rounded-2xl shadow-2xl border border-slate-200 border-8 p-1 bg-white'
            }
        });
    }
</script>