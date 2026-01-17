<?php
$page_title = 'Notifications';
?>

<div class="max-w-4xl mx-auto px-4 py-8">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-white to-gray-400">
                Notification Center
            </h1>
            <p class="text-gray-400 text-sm mt-1">Manage and view your system updates</p>
        </div>

        <?php if ($unreadCount > 0): ?>
            <button id="markAllPageBtn" class="group flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-500 hover:to-indigo-500 text-white rounded-xl transition-all duration-300 shadow-lg shadow-violet-500/20 active:scale-95">
                <i class="fas fa-check-double text-xs group-hover:scale-110 transition-transform"></i>
                <span class="font-medium">Mark all as read</span>
            </button>
        <?php endif; ?>
    </div>

    <!-- Notifications List -->
    <div class="backdrop-blur-xl bg-white/5 border border-white/10 rounded-2xl shadow-2xl overflow-hidden relative">
        <!-- Shine effect -->
        <div class="absolute inset-0 bg-gradient-to-br from-white/5 to-transparent pointer-events-none"></div>

        <?php if (empty($notifications)): ?>
            <div class="flex flex-col items-center justify-center py-20 text-center px-4">
                <div class="w-20 h-20 bg-white/5 rounded-full flex items-center justify-center mb-6 animate-pulse">
                    <i class="far fa-bell text-3xl text-gray-500"></i>
                </div>
                <h3 class="text-xl font-semibold text-white mb-2">All caught up!</h3>
                <p class="text-gray-400 max-w-sm">You have no new notifications at the moment. We'll verify you when something important happens.</p>
            </div>
        <?php else: ?>
            <div class="divide-y divide-white/5">
                <?php foreach ($notifications as $notification): ?>
                    <?php
                    $isUnread = !$notification['is_read'];
                    $icon = 'fa-info';
                    $iconColor = 'text-blue-400';
                    $iconBg = 'bg-blue-500/10';
                    $borderClass = 'border-l-4 border-blue-500';

                    switch ($notification['type']) {
                        case 'success':
                            $icon = 'fa-check';
                            $iconColor = 'text-emerald-400';
                            $iconBg = 'bg-emerald-500/10';
                            $borderClass = 'border-l-4 border-emerald-500';
                            break;
                        case 'warning':
                            $icon = 'fa-exclamation';
                            $iconColor = 'text-amber-400';
                            $iconBg = 'bg-amber-500/10';
                            $borderClass = 'border-l-4 border-amber-500';
                            break;
                        case 'error':
                            $icon = 'fa-times';
                            $iconColor = 'text-rose-400';
                            $iconBg = 'bg-rose-500/10';
                            $borderClass = 'border-l-4 border-rose-500';
                            break;
                        default: // info
                            $icon = 'fa-info';
                            $iconColor = 'text-blue-400';
                            $iconBg = 'bg-blue-500/10';
                            $borderClass = 'border-l-4 border-blue-500';
                            break;
                    }

                    // Override border if read
                    if (!$isUnread) {
                        $borderClass = 'border-l-4 border-transparent';
                    }

                    $actionUrl = $notification['action_url'] ?? '#';
                    ?>

                    <div class="notification-row group relative transition-all duration-300 hover:bg-white/5 <?php echo $isUnread ? 'bg-white/[0.02]' : ''; ?> rounded-2xl overflow-hidden"
                        data-id="<?php echo $notification['id']; ?>">

                        <!-- Billboard Mode (If Image Exists) -->
                        <?php if (!empty($notification['image_url'])): ?>
                            <div class="relative w-full h-48 md:h-64 overflow-hidden group">
                                <img src="<?php echo htmlspecialchars($notification['image_url']); ?>" class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700">
                                <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-gray-900/40 to-transparent"></div>
                                <div class="absolute bottom-0 left-0 p-6 z-10 w-full">
                                    <div class="flex justify-between items-end">
                                        <div>
                                            <span class="inline-block px-3 py-1 mb-2 text-xs font-bold tracking-wider text-white uppercase bg-purple-600 rounded-lg">
                                                Sponsored
                                            </span>
                                            <h4 class="text-2xl font-bold text-white mb-2 shadow-sm"><?php echo htmlspecialchars($notification['title']); ?></h4>
                                            <p class="text-gray-200 text-sm line-clamp-2 max-w-2xl"><?php echo htmlspecialchars($notification['message']); ?></p>
                                        </div>
                                        <?php if ($actionUrl && $actionUrl !== '#'): ?>
                                            <a href="<?php echo $actionUrl; ?>" class="hidden md:inline-flex items-center gap-2 px-6 py-2 bg-white text-gray-900 font-bold rounded-xl hover:bg-gray-100 transition-colors">
                                                Visit Link <i class="fas fa-external-link-alt"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <!-- Mark Read Button for Billboard -->
                            <?php if ($isUnread): ?>
                                <button onclick="markAsRead(<?php echo $notification['id']; ?>, this)" class="absolute top-4 right-4 bg-black/50 hover:bg-black/70 text-white p-2 rounded-full backdrop-blur-md transition-all">
                                    <i class="fas fa-check"></i>
                                </button>
                            <?php endif; ?>

                            <!-- Standard Mode -->
                        <?php else: ?>
                            <div class="p-6">
                                <!-- Unread Indicator (Left Border) -->
                                <div class="absolute left-0 top-0 bottom-0 w-1 transition-colors duration-300 <?php echo $isUnread ? $borderClass : 'border-transparent'; ?>"></div>

                                <div class="flex gap-5 items-start">
                                    <!-- Icon -->
                                    <div class="flex-shrink-0">
                                        <div class="w-12 h-12 rounded-xl flex items-center justify-center <?php echo $iconBg; ?> <?php echo $iconColor; ?> shadow-lg ring-1 ring-white/10 group-hover:scale-110 transition-transform duration-300">
                                            <i class="fas <?php echo $icon; ?>"></i>
                                        </div>
                                    </div>

                                    <!-- Content -->
                                    <div class="flex-grow min-w-0 pt-1">
                                        <div class="flex justify-between items-start gap-4 mb-2">
                                            <h4 class="text-lg font-semibold text-white truncate pr-4 <?php echo $isUnread ? '' : 'font-normal text-gray-200'; ?>">
                                                <?php echo htmlspecialchars($notification['title']); ?>
                                            </h4>
                                            <span class="text-xs font-mono text-gray-500 whitespace-nowrap bg-white/5 px-2 py-1 rounded">
                                                <?php echo date('M j, H:i', strtotime($notification['created_at'])); ?>
                                            </span>
                                        </div>

                                        <p class="text-gray-400 text-sm leading-relaxed mb-4 line-clamp-2">
                                            <?php echo htmlspecialchars($notification['message']); ?>
                                        </p>

                                        <!-- Actions -->
                                        <div class="flex items-center gap-3">
                                            <?php if ($actionUrl && $actionUrl !== '#'): ?>
                                                <a href="<?php echo $actionUrl; ?>" class="inline-flex items-center gap-2 px-4 py-1.5 text-xs font-medium text-white bg-white/10 hover:bg-white/20 rounded-lg transition-colors ring-1 ring-white/10">
                                                    <span>View Details</span>
                                                    <i class="fas fa-arrow-right text-[10px] opacity-70"></i>
                                                </a>
                                            <?php endif; ?>

                                            <?php if ($isUnread): ?>
                                                <button onclick="markAsRead(<?php echo $notification['id']; ?>, this)"
                                                    class="mark-read-btn inline-flex items-center gap-2 px-4 py-1.5 text-xs font-medium text-violet-300 hover:text-violet-200 hover:bg-violet-500/10 rounded-lg transition-all ml-auto">
                                                    <i class="fas fa-check"></i>
                                                    Mark as read
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
        <div class="mt-8 flex justify-center">
            <nav class="flex items-center gap-2 bg-white/5 px-2 py-2 rounded-2xl border border-white/10 shadow-lg backdrop-blur-md">
                <!-- Prev -->
                <?php if ($page > 1): ?>
                    <a href="?page=<?php echo $page - 1; ?>" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white/5 text-gray-400 hover:bg-white/10 hover:text-white transition-all">
                        <i class="fas fa-chevron-left text-sm"></i>
                    </a>
                <?php else: ?>
                    <span class="w-10 h-10 flex items-center justify-center rounded-xl text-gray-700 cursor-not-allowed">
                        <i class="fas fa-chevron-left text-sm"></i>
                    </span>
                <?php endif; ?>

                <!-- Numbers -->
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?page=<?php echo $i; ?>"
                        class="w-10 h-10 flex items-center justify-center rounded-xl text-sm font-medium transition-all
                       <?php echo $i == $page ? 'bg-gradient-to-br from-violet-600 to-indigo-600 text-white shadow-lg' : 'text-gray-400 hover:bg-white/10 hover:text-white'; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>

                <!-- Next -->
                <?php if ($page < $totalPages): ?>
                    <a href="?page=<?php echo $page + 1; ?>" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white/5 text-gray-400 hover:bg-white/10 hover:text-white transition-all">
                        <i class="fas fa-chevron-right text-sm"></i>
                    </a>
                <?php else: ?>
                    <span class="w-10 h-10 flex items-center justify-center rounded-xl text-gray-700 cursor-not-allowed">
                        <i class="fas fa-chevron-right text-sm"></i>
                    </span>
                <?php endif; ?>
            </nav>
        </div>
    <?php endif; ?>
</div>

<script>
    async function markAsRead(id, btn) {
        const row = btn.closest('.notification-row');

        // Optimistic UI Update
        if (row) {
            // 1. Remove background highlight
            row.classList.remove('bg-white/[0.02]');

            // 2. Hide border indicator
            const border = row.querySelector('.absolute.left-0');
            if (border) {
                border.className = 'absolute left-0 top-0 bottom-0 w-1 transition-colors duration-300 border-transparent';
            }

            // 3. Make title normal weight
            const title = row.querySelector('h4');
            if (title) {
                title.classList.remove('font-semibold', 'text-white');
                title.classList.add('font-normal', 'text-gray-200');
            }

            // 4. Fade out the button
            btn.style.opacity = '0';
            setTimeout(() => btn.remove(), 300);
        }

        try {
            const res = await fetch('<?php echo app_base_url("api/notifications/"); ?>' + id + '/read', {
                method: 'POST'
            });
            const data = await res.json();

            if (data.success) {
                // Update global badge
                const badge = document.getElementById('notificationBadge');
                if (badge) {
                    let count = parseInt(badge.textContent) || 0;
                    if (count > 0) {
                        count--;
                        badge.textContent = count > 99 ? '99+' : count;
                        if (count === 0) badge.style.display = 'none';
                    }
                }
            }
        } catch (e) {
            // Revert changes if needed (optional)
            console.error('Failed to mark read', e);
        }
    }

    document.getElementById('markAllPageBtn')?.addEventListener('click', async () => {
        if (!confirm('Mark all notifications as read?')) return;
        try {
            const btn = document.getElementById('markAllPageBtn');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Processing...';
            btn.disabled = true;

            const res = await fetch('<?php echo app_base_url("api/notifications/mark-all-read"); ?>', {
                method: 'POST'
            });
            const data = await res.json();
            if (data.success) {
                window.location.reload();
            } else {
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        } catch (e) {
            console.error(e);
        }
    });
</script>