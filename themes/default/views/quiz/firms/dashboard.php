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
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="//unpkg.com/alpinejs" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-background text-white font-sans min-h-screen pb-20" x-data="firmDashboard()">

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

                    <!-- XP Progress -->
                    <div class="w-full">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold uppercase tracking-widest text-gray-500">Firm Reputation</span>
                            <span class="text-xs font-bold text-white"><?php echo $guild['xp'] % 1000; ?> / 1000 XP</span>
                        </div>
                        <div class="h-3 bg-black/30 rounded-full overflow-hidden border border-white/5">
                            <div class="h-full bg-gradient-to-r from-cyan-400 to-blue-500 rounded-full" style="width: <?php echo ($guild['xp'] % 1000) / 10; ?>%"></div>
                        </div>
                        <div class="text-[10px] text-gray-500 mt-2 text-right">Next Level: <?php echo 1000 - ($guild['xp'] % 1000); ?> XP required</div>
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
                                    <button onclick="donate()" class="w-full h-full bg-amber-500 hover:bg-amber-400 text-black font-black uppercase tracking-wider rounded-xl text-xs shadow-lg shadow-amber-500/20 transition-all hover:-translate-y-1">
                                        Donate
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Members List -->
                <div class="glass-card p-1 rounded-3xl">
                    <div class="bg-surface/50 backdrop-blur-xl rounded-[20px] p-8 border border-white/5">
                        <div class="flex items-center justify-between mb-8">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-blue-500/10 flex items-center justify-center text-blue-500">
                                    <i class="fas fa-users"></i>
                                </div>
                                <h3 class="text-xl font-bold text-white">Team Roster</h3>
                            </div>
                            <span class="text-xs font-bold text-gray-500 bg-white/5 px-2 py-1 rounded"><?php echo count($members); ?> Members</span>
                        </div>

                        <div class="space-y-3">
                            <?php foreach ($members as $m): ?>
                            <div class="flex items-center justify-between p-4 rounded-xl bg-white/5 border border-white/5 hover:bg-white/10 transition-colors">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-gray-700 to-gray-900 flex items-center justify-center text-xs font-bold text-white border border-white/10">
                                        <?php echo substr($m['username'], 0, 2); ?>
                                    </div>
                                    <div>
                                        <div class="font-bold text-white text-sm"><?php echo htmlspecialchars($m['full_name'] ?: $m['username']); ?></div>
                                        <div class="text-xs text-cyan-400 font-medium"><?php echo $m['role']; ?></div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-[10px] text-gray-500 uppercase tracking-wider">Joined</div>
                                    <div class="text-xs font-mono text-gray-400"><?php echo date('M d', strtotime($m['joined_at'])); ?></div>
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
                // Alpine logic here if needed, currently using global funcs for compatibility with inline PHP vars
            }
        }

        async function donate() {
            const type = document.getElementById('res-type').value;
            const amount = document.getElementById('res-amt').value;
            
            if (!amount || amount <= 0) {
                 Swal.fire({ icon: 'warning', title: 'Invalid Amount', text: 'Please enter a positive number.', background: '#1e293b', color: '#fff' });
                 return;
            }

            const fd = new FormData();
            fd.append('type', type);
            fd.append('amount', amount);
            fd.append('csrf_token', '<?php echo csrf_token(); ?>');

            try {
                Swal.showLoading();
                const res = await fetch('/api/firms/donate', { method: 'POST', body: fd });
                const data = await res.json();
                
                if (data.success) {
                    await Swal.fire({ icon: 'success', title: 'Generous Contribution!', text: 'Resources added to vault.', timer: 1500, showConfirmButton: false, background: '#1e293b', color: '#fff' });
                    location.reload();
                } else {
                    Swal.fire({ icon: 'error', title: 'Donation Failed', text: data.message, background: '#1e293b', color: '#fff' });
                }
            } catch (e) {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Connection failed.', background: '#1e293b', color: '#fff' });
            }
        }

        async function handleRequest(requestId, action) {
             try {
                const fd = new FormData();
                fd.append('request_id', requestId);
                fd.append('action', action);
                fd.append('csrf_token', '<?php echo csrf_token(); ?>');

                const res = await fetch('/api/firms/handle-request', { method: 'POST', body: fd });
                const data = await res.json();
                if (data.success) location.reload();
                else Swal.fire({ icon: 'error', title: 'Action Failed', text: data.message, background: '#1e293b', color: '#fff' });
            } catch (e) {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Connection failed.', background: '#1e293b', color: '#fff' });
            }
        }
    </script>
</body>
</html>
