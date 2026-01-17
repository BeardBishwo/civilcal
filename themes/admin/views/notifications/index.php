<?php
$page_title = 'Notification Manager';
?>

<div class="max-w-7xl mx-auto px-6 py-8">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-indigo-600 to-purple-600">
                Notification Manager
            </h1>
            <p class="text-gray-400 text-sm mt-1">Broadcast alerts or message specific users.</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="/user/notifications" target="_blank" class="px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-sm hover:bg-white/10 text-gray-300">
                <i class="fas fa-external-link-alt me-2"></i> View My History
            </a>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <!-- Compose Form -->
        <div class="lg:col-span-2">
            <div class="backdrop-blur-xl bg-surface/50 border border-white/10 rounded-2xl shadow-2xl p-6 relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/5 to-purple-500/5 pointer-events-none"></div>

                <h2 class="text-xl font-semibold text-white mb-6 flex items-center gap-2">
                    <i class="fas fa-paper-plane text-indigo-400"></i> Compose Notification
                </h2>

                <form id="notificationForm" class="space-y-6 relative z-10">
                    <!-- Target Selection -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-gray-300">Target Audience</label>
                            <div class="flex gap-4 p-1 bg-white/5 rounded-xl border border-white/10">
                                <label class="flex-1 cursor-pointer">
                                    <input type="radio" name="target" value="user" class="peer sr-only" checked onchange="toggleUserSelect(true)">
                                    <div class="text-center py-2 rounded-lg text-gray-400 peer-checked:bg-indigo-600 peer-checked:text-white transition-all hover:bg-white/5">
                                        Single User
                                    </div>
                                </label>
                                <label class="flex-1 cursor-pointer">
                                    <input type="radio" name="target" value="all" class="peer sr-only" onchange="toggleUserSelect(false)">
                                    <div class="text-center py-2 rounded-lg text-gray-400 peer-checked:bg-purple-600 peer-checked:text-white transition-all hover:bg-white/5">
                                        Broadcast All
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="space-y-2 transition-all duration-300" id="userSelectContainer">
                            <label class="text-sm font-medium text-gray-300">Select User</label>
                            <select name="user_id" class="w-full bg-black/50 border border-white/10 rounded-xl px-4 py-2.5 text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none">
                                <option value="">Select a user...</option>
                                <?php foreach ($users as $u): ?>
                                    <option value="<?php echo $u['id']; ?>">
                                        <?php echo htmlspecialchars($u['username'] . ' (' . $u['email'] . ')'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Type Selection -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-gray-300">Notification Type</label>
                        <div class="flex gap-3">
                            <?php
                            $types = [
                                'info' => ['icon' => 'fa-info-circle', 'color' => 'bg-blue-500'],
                                'success' => ['icon' => 'fa-check-circle', 'color' => 'bg-emerald-500'],
                                'warning' => ['icon' => 'fa-exclamation-triangle', 'color' => 'bg-amber-500'],
                                'error' => ['icon' => 'fa-times-circle', 'color' => 'bg-rose-500']
                            ];
                            foreach ($types as $key => $style): ?>
                                <label class="cursor-pointer">
                                    <input type="radio" name="type" value="<?php echo $key; ?>" class="peer sr-only" <?php echo $key === 'info' ? 'checked' : ''; ?>>
                                    <div class="px-4 py-2 rounded-lg border border-white/10 text-gray-400 hover:bg-white/5 peer-checked:border-<?php echo explode('-', $style['color'])[1]; ?>-500 peer-checked:text-white transition-all peer-checked:bg-white/10 flex items-center gap-2">
                                        <i class="fas <?php echo $style['icon']; ?> <?php echo str_replace('bg-', 'text-', $style['color']); ?>"></i>
                                        <span class="capitalize"><?php echo $key; ?></span>
                                    </div>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="space-y-4">
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-gray-300">Title</label>
                            <input type="text" name="title" required placeholder="e.g., System Maintenance Alert"
                                class="w-full bg-black/50 border border-white/10 rounded-xl px-4 py-2.5 text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none placeholder-gray-600">
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium text-gray-300">Message</label>
                            <textarea name="message" required rows="3" placeholder="Enter notification content..."
                                class="w-full bg-black/50 border border-white/10 rounded-xl px-4 py-2.5 text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none placeholder-gray-600"></textarea>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium text-gray-300">Action URL <span class="text-gray-500 text-xs">(Optional)</span></label>
                            <input type="text" name="url" placeholder="https://..." value="#"
                                class="w-full bg-black/50 border border-white/10 rounded-xl px-4 py-2.5 text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none placeholder-gray-600">
                        </div>
                    </div>

                    <div class="pt-4 border-t border-white/10 flex justify-end">
                        <button type="submit" id="sendBtn" class="px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white rounded-xl font-medium shadow-lg shadow-indigo-500/25 active:scale-95 transition-all flex items-center gap-2">
                            <i class="fas fa-paper-plane text-sm"></i>
                            Send Notification
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Preview / Tips -->
        <div class="space-y-8">
            <div class="backdrop-blur-xl bg-white/5 border border-white/10 rounded-2xl p-6">
                <h3 class="text-lg font-semibold text-white mb-4">Preview</h3>
                <div class="bg-black/40 rounded-xl p-4 border-l-4 border-indigo-500 relative overflow-hidden" id="previewCard">
                    <div class="flex gap-3">
                        <div class="w-10 h-10 rounded-lg bg-indigo-500/10 flex items-center justify-center text-indigo-400 flex-shrink-0">
                            <i class="fas fa-info-circle text-lg" id="previewIcon"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-white font-medium truncate" id="previewTitle">Notification Title</h4>
                            <p class="text-gray-400 text-sm mt-1 line-clamp-2" id="previewMessage">Preview message content will appear here...</p>
                        </div>
                    </div>
                    <div class="mt-3 flex gap-2">
                        <button class="text-xs text-indigo-400 hover:text-indigo-300 font-medium">View Details</button>
                        <button class="text-xs text-gray-500 hover:text-gray-300">Mark as read</button>
                    </div>
                </div>
            </div>

            <div class="backdrop-blur-xl bg-gradient-to-br from-amber-500/10 to-orange-500/5 border border-amber-500/20 rounded-2xl p-6">
                <h3 class="text-lg font-semibold text-amber-500 mb-2">
                    <i class="fas fa-lightbulb me-2"></i> Pro Tips
                </h3>
                <ul class="text-sm text-gray-400 space-y-2 list-disc list-inside">
                    <li>Use <strong>Broadcast</strong> sparingly to avoid spamming all users.</li>
                    <li><strong>Action URLs</strong> can link to internal pages (e.g., `/pricing`) or external sites.</li>
                    <li><strong>Success</strong> type is great for payment confirmations.</li>
                    <li><strong>Warning</strong> type is ideal for expiration alerts.</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleUserSelect(show) {
        const container = document.getElementById('userSelectContainer');
        if (show) {
            container.classList.remove('opacity-50', 'pointer-events-none');
        } else {
            container.classList.add('opacity-50', 'pointer-events-none');
        }
    }

    // Live Preview
    const form = document.getElementById('notificationForm');
    const titleInput = form.querySelector('[name="title"]');
    const messageInput = form.querySelector('[name="message"]');
    const typeInputs = form.querySelectorAll('[name="type"]');

    function updatePreview() {
        document.getElementById('previewTitle').textContent = titleInput.value || 'Notification Title';
        document.getElementById('previewMessage').textContent = messageInput.value || 'Preview message content will appear here...';

        const type = document.querySelector('[name="type"]:checked').value;
        const iconMap = {
            'info': ['fa-info-circle', 'text-indigo-400', 'bg-indigo-500/10', 'border-indigo-500'],
            'success': ['fa-check-circle', 'text-emerald-400', 'bg-emerald-500/10', 'border-emerald-500'],
            'warning': ['fa-exclamation-triangle', 'text-amber-400', 'bg-amber-500/10', 'border-amber-500'],
            'error': ['fa-times-circle', 'text-rose-400', 'bg-rose-500/10', 'border-rose-500']
        };

        const styles = iconMap[type];
        const card = document.getElementById('previewCard');
        const icon = document.getElementById('previewIcon');
        const iconBox = icon.parentElement;

        // Reset classes
        card.className = `bg-black/40 rounded-xl p-4 border-l-4 relative overflow-hidden transition-colors duration-300 ${styles[3]}`;
        icon.className = `fas ${styles[0]} text-lg`;
        iconBox.className = `w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0 transition-colors duration-300 ${styles[1]} ${styles[2]}`;
    }

    [titleInput, messageInput, ...typeInputs].forEach(el => el.addEventListener('input', updatePreview));

    // Form Submit
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        if (!confirm('Send this notification?')) return;

        const btn = document.getElementById('sendBtn');
        const originalContent = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Sending...';
        btn.disabled = true;

        try {
            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());

            const res = await fetch('<?php echo app_base_url("/admin/notifications/broadcast"); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });

            const result = await res.json();

            if (result.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Sent!',
                    text: result.message,
                    background: '#18181b',
                    color: '#fff'
                });
                form.reset();
                updatePreview();
            } else {
                throw new Error(result.error || 'Failed to send');
            }
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: error.message,
                background: '#18181b',
                color: '#fff'
            });
        } finally {
            btn.innerHTML = originalContent;
            btn.disabled = false;
        }
    });
</script>