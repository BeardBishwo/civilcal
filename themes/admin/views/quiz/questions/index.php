<?php
/**
 * PREMIUM QUESTION BANK DASHBOARD
 */
?>

<!-- Load Tailwind CSS -->
<script src="https://cdn.tailwindcss.com"></script>
<!-- FontAwesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    body { background-color: #f3f4f6; font-family: 'Inter', sans-serif; }
    .table-row-hover:hover { background-color: #f8fafc; }
    .status-badge { padding: 4px 10px; border-radius: 20px; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; }
    .status-active { background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; }
    .status-inactive { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }
    .btn-create { background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%); transition: all 0.2s; }
    .btn-create:hover { transform: translateY(-1px); box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.3); }
</style>

<div class="p-6 max-w-[1400px] mx-auto min-h-screen">

    <!-- HEADER & STATS -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 mb-6 overflow-hidden">
        <div class="px-6 py-5 bg-slate-900 flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-white/10 flex items-center justify-center text-white shadow-inner border border-white/10">
                    <i class="fas fa-database text-xl"></i>
                </div>
                <div>
                    <h1 class="text-white font-bold text-xl tracking-tight">Question Bank</h1>
                    <p class="text-slate-400 text-sm">Manage your entire exam repository</p>
                </div>
            </div>
            
            <!-- Quick Stats -->
            <div class="flex bg-white/5 rounded-xl p-1.5 border border-white/10 backdrop-blur-sm">
                <div class="px-6 py-1 border-r border-white/10 text-center">
                    <span class="block text-[10px] text-slate-400 font-bold uppercase tracking-wider">Total</span>
                    <span class="block text-white font-bold text-xl"><?php echo number_format($stats['total'] ?? 0); ?></span>
                </div>
                <div class="px-6 py-1 border-r border-white/10 text-center">
                    <span class="block text-[10px] text-amber-400 font-bold uppercase tracking-wider">MCQ</span>
                    <span class="block text-white font-bold text-xl"><?php echo number_format($stats['mcq'] ?? 0); ?></span>
                </div>
                <div class="px-6 py-1 text-center">
                    <span class="block text-[10px] text-emerald-400 font-bold uppercase tracking-wider">Multi</span>
                    <span class="block text-white font-bold text-xl"><?php echo number_format($stats['multi'] ?? 0); ?></span>
                </div>
            </div>
        </div>

        <!-- Toolbar -->
        <div class="px-6 py-4 bg-white border-t border-slate-100 flex flex-col md:flex-row justify-between items-center gap-4">
            <form action="" method="GET" class="flex items-center gap-3 w-full md:w-auto flex-1">
                <div class="relative w-full md:w-72">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" name="search" value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>" placeholder="Search questions..." class="w-full pl-9 pr-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/10 outline-none transition">
                </div>
                
                <select name="type" class="bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-sm text-slate-700 outline-none cursor-pointer hover:border-slate-300 transition" onchange="this.form.submit()">
                    <option value="">All Types</option>
                    <option value="MCQ" <?php echo ($_GET['type'] ?? '') == 'MCQ' ? 'selected' : ''; ?>>MCQ</option>
                    <option value="TF" <?php echo ($_GET['type'] ?? '') == 'TF' ? 'selected' : ''; ?>>True/False</option>
                </select>

                <select name="topic_id" class="bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-sm text-slate-700 outline-none cursor-pointer hover:border-slate-300 transition max-w-[200px]" onchange="this.form.submit()">
                    <option value="">All Topics</option>
                    <?php if(!empty($mainCategories)): ?>
                        <?php foreach($mainCategories as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>" <?php echo ($_GET['topic_id'] ?? '') == $cat['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['title']); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </form>

            <div class="flex gap-3">
                <a href="<?php echo app_base_url('admin/quiz/questions/create'); ?>" class="btn-create text-white px-5 py-2.5 rounded-xl text-sm font-bold shadow-md flex items-center">
                    <i class="fas fa-plus mr-2"></i> New Question
                </a>
            </div>
        </div>
    </div>

    <!-- TABLE VIEW -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <?php if (empty($questions)): ?>
            <div class="text-center py-16">
                <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-300 text-3xl border border-slate-100">
                    <i class="fas fa-search"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-700">No questions found</h3>
                <p class="text-slate-500 text-sm mt-1">Try adjusting your filters or add a new question.</p>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-slate-50 text-xs uppercase text-slate-500 font-bold border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-4 w-1/2">Question Content</th>
                            <th class="px-6 py-4">Type / Topic</th>
                            <th class="px-6 py-4">Difficulty</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-slate-100">
                        <?php foreach ($questions as $q): ?>
                            <?php 
                                $content = json_decode($q['content'], true); 
                                $text = strip_tags($content['text'] ?? '');
                                if (strlen($text) > 80) $text = substr($text, 0, 80) . '...';
                                $badgeColor = $q['type'] == 'MCQ' ? 'text-blue-700 bg-blue-50 border-blue-100' : 'text-emerald-700 bg-emerald-50 border-emerald-100';
                            ?>
                            <tr class="table-row-hover transition group">
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="font-semibold text-slate-700 mb-1 line-clamp-2"><?php echo $text; ?></span>
                                        <span class="text-xs text-slate-400 font-mono flex items-center">
                                            <i class="fas fa-hashtag text-[10px] mr-1 opacity-50"></i> <?php echo htmlspecialchars((string)$q['unique_code']); ?>
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col items-start gap-1">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase border <?php echo $badgeColor; ?>">
                                            <?php echo $q['type']; ?>
                                        </span>
                                        <span class="text-xs text-slate-500 font-medium truncate max-w-[120px]" title="<?php echo htmlspecialchars($q['topic_name'] ?? 'General'); ?>">
                                            <?php echo htmlspecialchars($q['topic_name'] ?? 'General'); ?>
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex text-amber-400 text-xs">
                                        <?php for($i=1; $i<=5; $i++): ?>
                                            <i class="<?php echo $i <= $q['difficulty_level'] ? 'fas' : 'far'; ?> fa-star"></i>
                                        <?php endfor; ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="status-badge <?php echo $q['is_active'] ? 'status-active' : 'status-inactive'; ?>">
                                        <i class="fas fa-<?php echo $q['is_active'] ? 'check-circle' : 'ban'; ?> mr-1"></i>
                                        <?php echo $q['is_active'] ? 'Active' : 'Inactive'; ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2 opacity-60 group-hover:opacity-100 transition">
                                        <a href="<?php echo app_base_url('admin/quiz/questions/edit/' . $q['id']); ?>" class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 text-slate-500 hover:text-indigo-600 hover:border-indigo-200 hover:bg-indigo-50 transition" title="Edit">
                                            <i class="fas fa-pen text-xs"></i>
                                        </a>
                                        <form action="<?php echo app_base_url('admin/quiz/questions/delete/' . $q['id']); ?>" method="POST" class="d-inline" onsubmit="return confirm('Delete this question?');">
                                            <button type="submit" class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 text-slate-500 hover:text-red-600 hover:border-red-200 hover:bg-red-50 transition" title="Delete">
                                                <i class="fas fa-trash text-xs"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if ($total > $limit): ?>
                <div class="px-6 py-4 border-t border-slate-100 flex justify-between items-center bg-slate-50/50">
                    <span class="text-xs font-medium text-slate-500">
                        Showing <span class="font-bold text-slate-700"><?php echo count($questions); ?></span> of <span class="font-bold text-slate-700"><?php echo $total; ?></span> results
                    </span>
                    <div class="flex gap-2">
                        <!-- Simple Pagination logic -->
                        <a href="?page=<?php echo max(1, $page - 1); ?>" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-500 hover:border-indigo-300 hover:text-indigo-600 text-xs transition">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                        <a href="?page=<?php echo $page + 1; ?>" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-500 hover:border-indigo-300 hover:text-indigo-600 text-xs transition">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

</div>