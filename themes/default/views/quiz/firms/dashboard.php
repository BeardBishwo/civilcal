<?php

/**
 * Engineering Firms: Dashboard
 * Premium Dark Mode UI - Refactored
 */
?>
<!DOCTYPE html>
<html lang="en" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($guild['name']); ?> | Firm Dashboard</title>
    <!-- Load Tailwind & General Quiz CSS -->
    <link rel="stylesheet" href="<?php echo app_base_url('themes/default/assets/css/quiz.min.css?v=' . time()); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="bg-background text-white font-sans min-h-screen pb-20" x-data="firmDashboard()" @donate.window="donateTrigger"></body>


<!-- Header -->
<header class="h-16 bg-surface/80 backdrop-blur-md border-b border-white/5 flex items-center justify-between px-6 sticky top-0 z-50">
    <a href="<?php echo app_base_url('quiz/firms'); ?>" class="flex items-center gap-2 text-gray-400 hover:text-white transition-colors text-sm font-medium">
        <i class="fas fa-arrow-left"></i> <span>All Firms</span>
    </a>
    <div class="text-right">
        <h1 class="text-lg font-black text-white leading-none"><?php echo htmlspecialchars($guild['name']); ?></h1>
        <p class="text-[10px] uppercase tracking-widest text-cyan-400 font-bold">Internal Network</p>
    </div>
</header>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <!-- Firm Overview -->
    <div class="glass-card p-1 rounded-3xl mb-8">
        <div class="bg-surface/60 backdrop-blur-xl rounded-[20px] p-8 border border-white/5 relative overflow-hidden">
            <!-- Decor -->
            <div class="absolute -right-20 -top-20 w-96 h-96 bg-cyan-500/10 rounded-full blur-3xl opacity-50"></div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 items-center relative z-10">
                <!-- Identity -->
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <h1 class="text-4xl font-black text-white"><?php echo htmlspecialchars($guild['name']); ?></h1>
                        <span class="px-3 py-1 rounded-full bg-cyan-500/20 text-cyan-400 text-xs font-bold uppercase tracking-wider border border-cyan-500/30">
                            Lvl <?php echo $guild['level']; ?>
                        </span>
                    </div>
                    <p class="text-gray-400 text-sm leading-relaxed"><?php echo htmlspecialchars($guild['description']); ?></p>
                    <div class="mt-4 flex items-center gap-4 text-xs font-mono text-gray-500">
                        <span><i class="fas fa-calendar-alt mr-1"></i> Est. <?php echo date('M Y', strtotime($guild['created_at'])); ?></span>
                        <span><i class="fas fa-shield-alt mr-1"></i> ID: <?php echo $guild['id']; ?></span>
                    </div>
                </div>

                <!-- XP & Efficiency -->
                <div class="w-full">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-bold uppercase tracking-widest text-gray-500">Firm Reputation</span>
                        <div class="flex items-center gap-3">
                            <span class="text-xs font-bold text-white"><?php echo $guild['xp'] % 1000; ?> / 1000 XP</span>
                            <span class="px-2 py-0.5 rounded bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-[10px] font-bold uppercase">
                                <i class="fas fa-chart-line mr-1"></i> Eff: <?php echo number_format($guild['xp'] / max(count($members), 1), 2); ?>
                            </span>
                        </div>
                    </div>
                    <div class="h-3 bg-black/30 rounded-full overflow-hidden border border-white/5 relative">
                        <div class="h-full bg-gradient-to-r from-cyan-400 to-blue-500 rounded-full" style="width: <?php echo ($guild['xp'] % 1000) / 10; ?>%"></div>
                    </div>
                    <div class="flex justify-between items-center mt-2">
                        <div class="text-[10px] text-gray-500">Next Level: <?php echo 1000 - ($guild['xp'] % 1000); ?> XP required</div>

                        <!-- Active Perks Status -->
                        <div class="flex gap-1" x-data="{ showPerks: false }">
                            <?php if (empty($activePerks)): ?>
                                <span class="text-[10px] text-gray-600">No active ops</span>
                            <?php else: ?>
                                <?php foreach ($activePerks as $perk): ?>
                                    <div class="w-6 h-6 rounded bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-500 text-xs" title="<?php echo $perk['name']; ?>">
                                        <i class="fas fa-bolt"></i>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Role Badge -->
                <div class="text-right">
                    <div class="inline-block px-6 py-3 rounded-2xl bg-white/5 border border-white/10">
                        <span class="block text-[10px] text-gray-500 uppercase tracking-widest mb-1">Your Rank</span>
                        <span class="text-xl font-black text-white block"><?php echo $my_role; ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

        <!-- Left: Vault & Operations -->
        <div class="lg:col-span-8 space-y-8">

            <!-- Resource Vault -->
            <div class="glass-card p-1 rounded-3xl">
                <div class="bg-surface/50 backdrop-blur-xl rounded-[20px] p-8 border border-white/5">
                    <div class="flex items-center gap-3 mb-8">
                        <div class="w-10 h-10 rounded-lg bg-amber-500/10 flex items-center justify-center text-amber-500">
                            <i class="fas fa-university"></i>
                        </div>
                        <h3 class="text-xl font-bold text-white">Resource Vault</h3>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                        <?php foreach ($vault as $res): ?>
                            <div class="bg-black/20 rounded-xl p-4 border border-white/5 text-center group hover:border-amber-500/30 transition-colors">
                                <div class="text-2xl font-black text-white group-hover:text-amber-500 transition-colors"><?php echo number_format($res['amount']); ?></div>
                                <div class="text-[10px] uppercase tracking-wider text-gray-500 font-bold mt-1"><?php echo $res['resource_type']; ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Dividends (Leader Only) -->
                    <?php if ($my_role === 'Leader' && $vault[0]['amount'] > 100): ?>
                        <div class="mb-6 p-4 rounded-xl bg-amber-500/5 border border-amber-500/10 flex items-center justify-between">
                            <div>
                                <h4 class="text-sm font-bold text-amber-500">Distribute Dividends</h4>
                                <p class="text-[10px] text-gray-500">Pay all members. 15% Tax applies.</p>
                            </div>
                            <button @click="distributeDividends()" class="px-3 py-1.5 bg-amber-500 hover:bg-amber-400 text-black text-xs font-bold rounded-lg shadow-lg shadow-amber-500/20">
                                Pay Details
                            </button>
                        </div>
                    <?php endif; ?>

                    <!-- Donation Panel -->
                    <div class="bg-white/5 rounded-xl p-6 border border-white/5">
                        <h4 class="text-sm font-bold text-white mb-4 uppercase tracking-wider">Contribute Resources</h4>
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                            <div class="md:col-span-5">
                                <select id="res-type" class="w-full bg-black/30 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-amber-500 font-bold text-sm h-full">
                                    <option value="coins">Coins (You: <?php echo $wallet['coins']; ?>)</option>
                                    <option value="bricks">Bricks (You: <?php echo $wallet['bricks']; ?>)</option>
                                    <option value="cement">Cement (You: <?php echo $wallet['cement']; ?>)</option>
                                    <option value="steel">Steel (You: <?php echo $wallet['steel']; ?>)</option>
                                </select>
                            </div>
                            <div class="md:col-span-4">
                                <input type="number" id="res-amt" class="w-full bg-black/30 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-amber-500 font-bold text-sm h-full" placeholder="Amount">
                            </div>
                            <div class="md:col-span-3">
                                <button @click="donate()" class="w-full h-full bg-amber-500 hover:bg-amber-400 text-black font-black uppercase tracking-wider rounded-xl text-xs shadow-lg shadow-amber-500/20 transition-all hover:-translate-y-1 disabled:opacity-50" :disabled="donating">
                                    <span x-show="!donating">Donate</span>
                                    <span x-show="donating"><i class="fas fa-spinner fa-spin"></i> Donating...</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tactical Operations (Perk Shop) -->
            <div class="glass-card p-1 rounded-3xl" x-data="{ tab: 'shop' }">
                <div class="bg-surface/50 backdrop-blur-xl rounded-[20px] p-8 border border-white/5">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-emerald-500/10 flex items-center justify-center text-emerald-500">
                                <i class="fas fa-crosshairs"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-white">Tactical Operations</h3>
                                <p class="text-xs text-gray-500">Purchase buffs and upgrades</p>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <?php foreach ($availablePerks as $perk): ?>
                            <div class="p-4 rounded-xl bg-black/20 border border-white/5 hover:border-emerald-500/30 transition-all group relative overflow-hidden">
                                <div class="relative z-10">
                                    <div class="flex justify-between items-start mb-2">
                                        <h4 class="font-bold text-gray-200"><?php echo htmlspecialchars($perk['name']); ?></h4>
                                        <span class="text-[10px] bg-emerald-500/10 text-emerald-400 px-2 py-0.5 rounded uppercase tracking-wider font-bold">
                                            <?php echo $perk['duration_hours']; ?>H
                                        </span>
                                    </div>
                                    <p class="text-xs text-gray-500 mb-4 h-8 overflow-hidden"><?php echo htmlspecialchars($perk['description']); ?></p>

                                    <div class="flex items-center justify-between mt-auto">
                                        <div class="text-xs font-mono text-gray-400">
                                            <?php if ($perk['cost_coins'] > 0): ?>
                                                <i class="fas fa-coins text-amber-400 mr-1"></i> <?php echo number_format($perk['cost_coins']); ?>
                                            <?php endif; ?>
                                        </div>
                                        <?php if ($my_role === 'Leader' || $my_role === 'Co-Leader'): ?>
                                            <button @click="purchasePerk(<?php echo $perk['id']; ?>)" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold rounded-lg transition-colors shadow-lg shadow-emerald-500/20">
                                                Deploy
                                            </button>
                                        <?php else: ?>
                                            <span class="text-[10px] text-gray-600 uppercase font-bold">Locked</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Members List & Roles (Updated) -->
            <div class="glass-card p-1 rounded-3xl">

                <!-- Members List & Roles -->
                <div class="glass-card p-1 rounded-3xl">
                    <div class="bg-surface/50 backdrop-blur-xl rounded-[20px] p-8 border border-white/5">
                        <div class="flex items-center justify-between mb-8">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-blue-500/10 flex items-center justify-center text-blue-500">
                                    <i class="fas fa-users"></i>
                                </div>
                                <h3 class="text-xl font-bold text-white">Unit Roster</h3>
                            </div>
                            <span class="text-xs font-bold text-gray-500 bg-white/5 px-2 py-1 rounded"><?php echo count($members); ?> / <?php echo $guild['max_members']; ?> Agents</span>
                        </div>

                        <div class="space-y-2 max-h-96 overflow-y-auto pr-2 custom-scrollbar">
                            <?php foreach ($members as $m): ?>
                                <div class="flex items-center justify-between p-3 rounded-xl bg-white/5 border border-white/5 hover:bg-white/10 transition-colors group">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-gray-700 to-gray-900 flex items-center justify-center text-xs font-bold text-white border border-white/10 ring-2 ring-transparent group-hover:ring-cyan-500/20 transition-all">
                                            <?php echo substr($m['username'], 0, 2); ?>
                                        </div>
                                        <div>
                                            <div class="font-bold text-white text-sm flex items-center gap-2">
                                                <?php echo htmlspecialchars($m['username']); ?>
                                                <?php if ($m['role'] === 'Leader'): ?>
                                                    <i class="fas fa-crown text-amber-500 text-[10px]"></i>
                                                <?php elseif ($m['role'] === 'Co-Leader'): ?>
                                                    <i class="fas fa-star text-cyan-400 text-[10px]"></i>
                                                <?php endif; ?>
                                            </div>
                                            <div class="text-[10px] text-gray-500 font-mono">
                                                Joined <?php echo date('M d', strtotime($m['joined_at'])); ?> • Score: <?php echo rand(100, 5000); // Placeholder for individual score 
                                                                                                                        ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <?php if ($my_role === 'Leader' && $m['role'] === 'Member'): ?>
                                            <button @click="promoteMember(<?php echo $m['user_id']; ?>)" class="w-7 h-7 rounded bg-cyan-500/10 text-cyan-400 hover:bg-cyan-500 hover:text-white flex items-center justify-center transition-all" title="Promote to Co-Leader">
                                                <i class="fas fa-chevron-up text-xs"></i>
                                            </button>
                                            <button class="w-7 h-7 rounded bg-red-500/10 text-red-400 hover:bg-red-500 hover:text-white flex items-center justify-center transition-all" title="Kick">
                                                <i class="fas fa-times text-xs"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Activity & Requests -->
            <div class="lg:col-span-4 space-y-8">

                <!-- Activity Feed -->
                <div class="glass-card p-6 rounded-3xl">
                    <h3 class="text-lg font-bold text-white mb-6 flex items-center gap-2">
                        <i class="fas fa-bell text-red-500"></i> Firm Activity
                    </h3>

                    <div class="space-y-4">
                        <div class="relative pl-6 border-l-2 border-white/10">
                            <div class="absolute -left-[5px] top-0 w-2.5 h-2.5 rounded-full bg-green-500 ring-4 ring-background"></div>
                            <div class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">System</div>
                            <div class="text-sm text-gray-300">Firm established successfully.</div>
                        </div>
                        <div class="p-3 rounded-lg bg-blue-500/10 border border-blue-500/20 text-xs text-blue-200">
                            <i class="fas fa-info-circle mr-1"></i> Tip: Donate resources to level up your firm and unlock Mega Projects.
                        </div>
                    </div>
                </div>

                <!-- Admin Actions -->
                <?php if ($my_role === 'Leader' && !empty($requests)): ?>
                    <div class="glass-card p-6 rounded-3xl border border-amber-500/30">
                        <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                            <i class="fas fa-user-clock text-amber-500"></i> Pending Requests
                        </h3>
                        <div class="space-y-3">
                            <?php foreach ($requests as $r): ?>
                                <div class="p-3 rounded-xl bg-white/5 border border-white/5 flex items-center justify-between">
                                    <span class="text-sm font-bold text-gray-300"><?php echo htmlspecialchars($r['username']); ?></span>
                                    <div class="flex gap-2">
                                        <button onclick="handleRequest(<?php echo $r['id']; ?>, 'approve')" class="w-8 h-8 rounded-lg bg-green-500 hover:bg-green-400 text-black flex items-center justify-center transition-colors">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button onclick="handleRequest(<?php echo $r['id']; ?>, 'decline')" class="w-8 h-8 rounded-lg bg-red-500 hover:bg-red-400 text-white flex items-center justify-center transition-colors">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Danger Zone -->
                <div class="glass-card p-6 rounded-3xl opacity-60 hover:opacity-100 transition-opacity">
                    <h3 class="text-sm font-bold text-gray-400 mb-4 uppercase tracking-widest">Zone of Danger</h3>
                    <a href="/quiz/firms/leave" onclick="return confirm('Are you sure? This cannot be undone.')" class="block w-full py-3 bg-red-500/10 hover:bg-red-500/20 border border-red-500/20 rounded-xl text-center text-red-500 font-bold text-sm transition-colors">
                        Leave Firm
                    </a>
                </div>

            </div>
        </div>
    </div>

    <script>
        function firmDashboard() {
            return {
                donating: false,
                donate() {
                    const typeSelect = document.getElementById('res-type');
                    const amtInput = document.getElementById('res-amt');
                    const resType = typeSelect.value;
                    const amount = parseInt(amtInput.value);

                    if (!amount || amount <= 0) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Invalid Amount',
                            text: 'Please enter a valid amount.',
                            background: '#1e293b',
                            color: '#fff'
                        });
                        return;
                    }

                    const fd = new FormData();
                    fd.append('resource_type', resType);
                    fd.append('amount', amount);
                    fd.append('csrf_token', '<?php echo csrf_token(); ?>');

                    this.donating = true;

                    fetch('/api/firms/donate-resources', {
                            method: 'POST',
                            body: fd
                        })
                        .then(res => res.json())
                        .then(data => {
                            this.donating = false;
                            if (data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Donated!',
                                    text: `${amount} ${resType} contributed to vault.`,
                                    background: '#1e293b',
                                    color: '#fff'
                                });
                                setTimeout(() => location.reload(), 1500);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Donation Failed',
                                    text: data.message,
                                    background: '#1e293b',
                                    color: '#fff'
                                });
                            }
                        })
                        .catch(e => {
                            this.donating = false;
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Connection failed.',
                                background: '#1e293b',
                                color: '#fff'
                            });
                        });
                }
            }
        }

        async function donate() {
            const type = document.getElementById('res-type').value;
            const amount = document.getElementById('res-amt').value;

            if (!amount || amount <= 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Invalid Amount',
                    text: 'Please enter a positive number.',
                    background: '#1e293b',
                    color: '#fff'
                });
                return;
            }

            const fd = new FormData();
            fd.append('type', type);
            fd.append('amount', amount);
            fd.append('csrf_token', '<?php echo csrf_token(); ?>');

            try {
                Swal.showLoading();
                const res = await fetch('/api/firms/donate', {
                    method: 'POST',
                    body: fd
                });
                const data = await res.json();

                if (data.success) {
                    await Swal.fire({
                        icon: 'success',
                        title: 'Generous Contribution!',
                        text: 'Resources added to vault.',
                        timer: 1500,
                        showConfirmButton: false,
                        background: '#1e293b',
                        color: '#fff'
                    });
                    location.reload();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Donation Failed',
                        text: data.message,
                        background: '#1e293b',
                        color: '#fff'
                    });
                }
            } catch (e) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Connection failed.',
                    background: '#1e293b',
                    color: '#fff'
                });
            }
        }

        async function handleRequest(requestId, action) {
            try {
                const fd = new FormData();
                fd.append('request_id', requestId);
                fd.append('action', action);
                fd.append('csrf_token', '<?php echo csrf_token(); ?>');

                const res = await fetch('/api/firms/handle-request', {
                    method: 'POST',
                    body: fd
                });
                const data = await res.json();
                if (data.success) location.reload();
                else Swal.fire({
                    icon: 'error',
                    title: 'Action Failed',
                    text: data.message,
                    background: '#1e293b',
                    color: '#fff'
                });
            } catch (e) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Connection failed.',
                    background: '#1e293b',
                    color: '#fff'
                });
            }
        }


        // Gameplay Functions
        async function purchasePerk(perkId) {
            const confirmed = await Swal.fire({
                title: 'Deploy Perk?',
                text: "This will deduct resources from the Firm Vault.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#374151',
                confirmButtonText: 'Yes, Deploy!',
                background: '#1e293b',
                color: '#fff'
            });

            if (!confirmed.isConfirmed) return;

            const fd = new FormData();
            fd.append('perk_id', perkId);
            fd.append('csrf_token', '<?php echo csrf_token(); ?>');

            try {
                const res = await fetch('/api/firms/perk/purchase', {
                    method: 'POST',
                    body: fd
                });
                const data = await res.json();

                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Perk Activated!',
                        text: data.message,
                        background: '#1e293b',
                        color: '#fff'
                    });
                    setTimeout(() => location.reload(), 1500);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Purchase Failed',
                        text: data.message,
                        background: '#1e293b',
                        color: '#fff'
                    });
                }
            } catch (e) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Network connection failed.',
                    background: '#1e293b',
                    color: '#fff'
                });
            }
        }

        async function promoteMember(userId) {
            const confirmed = await Swal.fire({
                title: 'Promote Member?',
                text: "Promote this user to Co-Leader? They will have vault access.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#06b6d4',
                cancelButtonColor: '#374151',
                confirmButtonText: 'Yes, Promote',
                background: '#1e293b',
                color: '#fff'
            });

            if (!confirmed.isConfirmed) return;

            const fd = new FormData();
            fd.append('user_id', userId);
            fd.append('csrf_token', '<?php echo csrf_token(); ?>');

            try {
                const res = await fetch('/api/firms/promote', {
                    method: 'POST',
                    body: fd
                });
                const data = await res.json();

                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Promoted!',
                        text: data.message,
                        background: '#1e293b',
                        color: '#fff'
                    });
                    location.reload();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Promotion Failed',
                        text: data.message,
                        background: '#1e293b',
                        color: '#fff'
                    });
                }
            } catch (e) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Network connection failed.',
                    background: '#1e293b',
                    color: '#fff'
                });
            }
        }

        async function distributeDividends() {
            const {
                value: amount
            } = await Swal.fire({
                title: 'Distribute Dividends',
                input: 'number',
                inputLabel: 'Amount per member (Coins)',
                inputPlaceholder: 'e.g. 100',
                text: '15% Tax will be burned from the vault.',
                showCancelButton: true,
                background: '#1e293b',
                color: '#fff',
                confirmButtonColor: '#f59e0b',
            });

            if (!amount) return;

            const fd = new FormData();
            fd.append('amount_per_member', amount);
            fd.append('csrf_token', '<?php echo csrf_token(); ?>');

            try {
                const res = await fetch('/api/firms/dividends', {
                    method: 'POST',
                    body: fd
                });
                const data = await res.json();

                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Payout Complete!',
                        text: data.message,
                        background: '#1e293b',
                        color: '#fff'
                    });
                    setTimeout(() => location.reload(), 1500);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Payout Failed',
                        text: data.message,
                        background: '#1e293b',
                        color: '#fff'
                    });
                }
            } catch (e) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Network connection failed.',
                    background: '#1e293b',
                    color: '#fff'
                });
            }
        }

        // Alpine Donate Function
        function donateTrigger() {
            const typeSelect = document.getElementById('res-type');
            const amtInput = document.getElementById('res-amt');
            const resType = typeSelect.value;
            const amount = parseInt(amtInput.value);

            if (!amount || amount <= 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Invalid Amount',
                    text: 'Please enter a valid amount.',
                    background: '#1e293b',
                    color: '#fff'
                });
                return;
            }

            const fd = new FormData();
            fd.append('type', resType); // Corrected parameter name
            fd.append('amount', amount);
            fd.append('csrf_token', '<?php echo csrf_token(); ?>');

            this.donating = true;

            fetch('/api/firms/donate', { // Corrected URL
                    method: 'POST',
                    body: fd
                })
                .then(res => res.json())
                .then(data => {
                    this.donating = false;
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Donated!',
                            text: `${amount} ${resType} contributed to vault.`,
                            background: '#1e293b',
                            color: '#fff'
                        });
                        location.reload();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Donation Failed',
                            text: data.message,
                            background: '#1e293b',
                            color: '#fff'
                        });
                    }
                })
                .catch(e => {
                    this.donating = false;
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Connection failed.',
                        background: '#1e293b',
                        color: '#fff'
                    });
                });
        }
    </script>
    </body>

</html>