<?php
// Header included by layout
?>

<div class="max-w-4xl mx-auto px-6 py-8">
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-indigo-600 to-purple-600">
                New Notification
            </h1>
            <p class="text-gray-500 text-sm mt-1">Compose and send broadcast messages</p>
        </div>
        <a href="/admin/notifications" class="px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm hover:bg-gray-50 text-gray-600 shadow-sm flex items-center gap-2 transition-all">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>

    <!-- Main Card -->
    <div class="bg-white border border-gray-100 rounded-2xl shadow-xl p-8 relative overflow-hidden">
        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-indigo-500 to-purple-500"></div>

        <form id="notificationForm" class="space-y-8 relative z-10" onsubmit="sendNotification(event)">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">

            <!-- Target Selection -->
            <div class="space-y-3">
                <label class="text-sm font-bold text-gray-700 uppercase tracking-wider">Target Audience</label>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <label class="cursor-pointer relative group">
                        <input type="radio" name="target_type" value="global" class="peer sr-only" checked onchange="toggleTarget('global')">
                        <div class="p-5 rounded-xl bg-gray-50 border border-gray-200 group-hover:border-indigo-300 peer-checked:border-indigo-500 peer-checked:bg-indigo-50 peer-checked:text-indigo-700 transition-all text-center h-full flex flex-col items-center justify-center">
                            <div class="w-12 h-12 rounded-full bg-indigo-100 flex items-center justify-center mb-3 peer-checked:bg-indigo-200">
                                <i class="fas fa-globe text-xl text-indigo-600"></i>
                            </div>
                            <div class="font-bold text-gray-800 peer-checked:text-indigo-800">All Users</div>
                        </div>
                    </label>

                    <label class="cursor-pointer relative group">
                        <input type="radio" name="target_type" value="role" class="peer sr-only" onchange="toggleTarget('role')">
                        <div class="p-4 rounded-xl bg-black/40 border border-white/10 hover:border-purple-500/50 peer-checked:border-purple-500 peer-checked:bg-purple-500/10 transition-all text-center">
                            <i class="fas fa-user-tag text-2xl mb-2 text-purple-400"></i>
                            <div class="font-medium text-white">By Role</div>
                        </div>
                    </label>

                    <label class="cursor-pointer relative">
                        <input type="radio" name="target_type" value="specific" class="peer sr-only" onchange="toggleTarget('specific')">
                        <div class="p-4 rounded-xl bg-black/40 border border-white/10 hover:border-pink-500/50 peer-checked:border-pink-500 peer-checked:bg-pink-500/10 transition-all text-center">
                            <i class="fas fa-user text-2xl mb-2 text-pink-400"></i>
                            <div class="font-medium text-white">Specific User</div>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Dynamic Selectors -->
            <div id="roleSelector" class="hidden animate-fade-in-down">
                <label class="text-sm font-medium text-gray-300 block mb-2">Select Role</label>
                <select name="role_value" class="w-full bg-black/50 border border-white/10 rounded-xl px-4 py-2.5 text-white focus:ring-2 focus:ring-purple-500 outline-none">
                    <option value="admin">Admin</option>
                    <option value="editor">Editor</option>
                    <option value="user">User</option>
                </select>
            </div>

            <div id="userSelector" class="hidden animate-fade-in-down">
                <label class="text-sm font-medium text-gray-300 block mb-2">Search User (ID or Email)</label>
                <input type="text" name="user_id" placeholder="Enter User ID" class="w-full bg-black/50 border border-white/10 rounded-xl px-4 py-2.5 text-white focus:ring-2 focus:ring-pink-500 outline-none">
                <p class="text-xs text-gray-500 mt-1">Enter the exact User ID for now.</p>
            </div>

            <!-- Notification Type -->
            <div class="space-y-3">
                <label class="text-sm font-medium text-gray-300">Notification Type</label>
                <div class="flex gap-4">
                    <?php
                    $types = [
                        'info' => ['icon' => 'fa-info-circle', 'color' => 'indigo'],
                        'success' => ['icon' => 'fa-check-circle', 'color' => 'emerald'],
                        'warning' => ['icon' => 'fa-exclamation-triangle', 'color' => 'amber'],
                        'danger' => ['icon' => 'fa-times-circle', 'color' => 'rose']
                    ];
                    foreach ($types as $key => $style):
                    ?>
                        <label class="cursor-pointer">
                            <input type="radio" name="type" value="<?php echo $key; ?>" class="peer sr-only" <?php echo $key === 'info' ? 'checked' : ''; ?>>
                            <div class="px-4 py-2 rounded-lg border border-white/10 bg-black/20 text-gray-400 peer-checked:text-white peer-checked:border-<?php echo $style['color']; ?>-500 peer-checked:bg-<?php echo $style['color']; ?>-500/10 transition-all flex items-center gap-2">
                                <i class="fas <?php echo $style['icon']; ?>"></i>
                                <span class="capitalize"><?php echo $key; ?></span>
                            </div>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Content -->
            <div class="space-y-4">
                <div class="group">
                    <label class="text-sm font-medium text-gray-300 block mb-2">Title</label>
                    <input type="text" name="title" required placeholder="e.g., System Maintenance"
                        class="w-full bg-black/50 border border-white/10 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-indigo-500 outline-none transition-all placeholder-gray-600">
                </div>

                <div class="group">
                    <label class="text-sm font-medium text-gray-300 block mb-2">Message</label>
                    <textarea name="message" rows="4" required placeholder="Enter your message here..."
                        class="w-full bg-black/50 border border-white/10 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-indigo-500 outline-none transition-all placeholder-gray-600 font-sans"></textarea>
                    <div class="flex justify-between mt-1">
                        <span class="text-xs text-gray-500">Supports basic HTML</span>
                    </div>
                </div>

                <div class="group">
                    <label class="text-sm font-medium text-gray-300 block mb-2">Action URL <span class="text-gray-500 text-xs">(Optional)</span></label>
                    <input type="text" name="url" placeholder="https://..."
                        class="w-full bg-black/50 border border-white/10 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-indigo-500 outline-none transition-all placeholder-gray-600">
                </div>
            </div>

            <!-- Options -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-white/10">
                <div>
                    <label class="text-sm font-medium text-gray-300 block mb-2">Icon Class</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-icons text-gray-500"></i>
                        </div>
                        <input type="text" name="icon" value="fas fa-bell"
                            class="w-full bg-black/50 border border-white/10 rounded-xl pl-10 pr-4 py-2.5 text-white focus:ring-2 focus:ring-indigo-500 outline-none placeholder-gray-600">
                    </div>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-300 block mb-2">Expires At</label>
                    <input type="date" name="expires_at"
                        class="w-full bg-black/50 border border-white/10 rounded-xl px-4 py-2.5 text-white focus:ring-2 focus:ring-indigo-500 outline-none [color-scheme:dark]">
                </div>
            </div>

            <div class="flex items-center gap-3 p-4 bg-amber-500/10 border border-amber-500/20 rounded-xl">
                <div class="flex items-center h-5">
                    <input id="send_email" name="send_email" type="checkbox" value="1" class="w-4 h-4 rounded border-gray-600 text-amber-600 focus:ring-amber-600 bg-gray-700">
                </div>
                <div class="ml-2 text-sm">
                    <label for="send_email" class="font-medium text-amber-400">Send Email Blast</label>
                    <p class="text-gray-400 text-xs">This will queue emails to all targeted users. Use with caution.</p>
                </div>
            </div>

            <!-- Action -->
            <div class="flex justify-end pt-6 border-t border-white/10">
                <button type="submit" id="submitBtn" class="px-8 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white rounded-xl font-bold shadow-lg shadow-indigo-500/25 active:scale-95 transition-all flex items-center gap-2">
                    <i class="fas fa-paper-plane"></i>
                    Send Notification
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .hidden {
        display: none;
    }

    .animate-fade-in-down {
        animation: fadeInDown 0.3s ease-out;
    }

    @keyframes fadeInDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>

<script>
    function toggleTarget(type) {
        document.getElementById('roleSelector').style.display = type === 'role' ? 'block' : 'none';
        document.getElementById('userSelector').style.display = type === 'specific' ? 'block' : 'none';
    }

    async function sendNotification(e) {
        e.preventDefault();

        if (!confirm('Are you sure you want to send this broadcast?')) return;

        const form = e.target;
        const btn = document.getElementById('submitBtn');
        const originalText = btn.innerHTML;

        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
        btn.disabled = true;

        const fd = new FormData(form);
        const data = Object.fromEntries(fd.entries());

        // Handle Target Logic
        let endpoint = '/admin/api/notifications/broadcast';

        if (data.target_type === 'global') {
            data.target_group = 'all';
        } else if (data.target_type === 'role') {
            data.target_group = 'role';
            data.target_value = data.role_value;
        } else if (data.target_type === 'specific') {
            endpoint = '/admin/api/notifications/send';
            data.user_ids = [data.user_id];
        }

        try {
            const res = await fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });
            const result = await res.json();

            if (result.success) {
                // Use SweetAlert if available, otherwise native
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Sent',
                        text: result.message,
                        background: '#18181b',
                        color: '#fff'
                    }).then(() => window.location.href = '/admin/notifications');
                } else {
                    alert('Sent Successfully!');
                    window.location.href = '/admin/notifications';
                }
            } else {
                throw new Error(result.message || 'Failed to send');
            }
        } catch (err) {
            console.error(err);
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: err.message,
                    background: '#18181b',
                    color: '#fff'
                });
            } else {
                alert('Error: ' + err.message);
            }
        } finally {
            btn.innerHTML = originalText;
            btn.disabled = false;
        }
    }
</script>