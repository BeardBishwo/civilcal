<?php
/**
 * Engineering Firms: Index (Alliance Discovery)
 * Premium Dark Mode UI - Refactored
 */
?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Engineering Firms | Civil City</title>
    <!-- Load Tailwind & General Quiz CSS -->
    <link rel="stylesheet" href="<?php echo app_base_url('themes/default/assets/css/quiz.min.css?v=' . time()); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="//unpkg.com/alpinejs" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-background text-white font-sans min-h-screen pb-20" x-data="firmsIndex()">

    <!-- Header -->
    <header class="h-16 bg-surface/80 backdrop-blur-md border-b border-white/5 flex items-center justify-between px-6 sticky top-0 z-50">
        <a href="<?php echo app_base_url('quiz'); ?>" class="flex items-center gap-2 text-gray-400 hover:text-white transition-colors text-sm font-medium">
            <i class="fas fa-arrow-left"></i> <span>Portal</span>
        </a>
        <div class="text-right">
            <h1 class="text-lg font-black bg-gradient-to-r from-cyan-400 to-blue-500 bg-clip-text text-transparent leading-none">Engineering Firms</h1>
            <p class="text-[10px] uppercase tracking-widest text-gray-500 font-bold">Alliance Network</p>
        </div>
    </header>

    <!-- Hero Section -->
    <div class="relative overflow-hidden py-16 border-b border-white/5">
        <!-- Background Effects -->
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-cyan-500/10 rounded-full blur-[100px] animate-blob"></div>
            <div class="absolute bottom-0 left-0 w-[600px] h-[600px] bg-blue-600/10 rounded-full blur-[100px] animate-blob animation-delay-2000"></div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-cyan-500/10 text-cyan-400 text-xs font-bold uppercase tracking-widest border border-cyan-500/20 mb-6">
                        <span class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse"></span> Collaborative Economy
                    </div>
                    <h1 class="text-5xl md:text-6xl font-black text-white mb-6 tracking-tight leading-tight">
                        Build together.<br>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 to-blue-500">Conquer together.</span>
                    </h1>
                    <p class="text-xl text-gray-400 mb-8 max-w-lg leading-relaxed">Form elite crews, pool resources, and unlock mega projects with premium-grade collaboration tools.</p>
                    
                    <div class="flex flex-wrap gap-8">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-surface/50 border border-white/10 flex items-center justify-center text-green-400">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <div>
                                <div class="text-xs font-bold text-gray-500 uppercase tracking-wider">Security</div>
                                <div class="font-bold text-white">Nonce + Honeypot</div>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-surface/50 border border-white/10 flex items-center justify-center text-amber-500">
                                <i class="fas fa-coins"></i>
                            </div>
                            <div>
                                <div class="text-xs font-bold text-gray-500 uppercase tracking-wider">Economy</div>
                                <div class="font-bold text-white">Server-Validated</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Create Firm Card -->
                <div class="bg-surface/60 backdrop-blur-xl rounded-3xl p-1 border border-white/10 shadow-2xl">
                    <div class="bg-black/20 rounded-[20px] p-8">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-cyan-400 to-blue-600 flex items-center justify-center text-white text-xl shadow-lg">
                                <i class="fas fa-building"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-white">Founder's License</h3>
                                <div class="text-cyan-400 font-bold text-sm">Create New Firm</div>
                            </div>
                        </div>

                        <form action="/api/firms/create" method="POST" @submit.prevent="createFirm">
                            <input type="hidden" name="nonce" value="<?php echo htmlspecialchars($createNonce ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <input type="text" name="trap_answer" id="firm_create_trap" style="display:none" autocomplete="off">
                            
                            <div class="space-y-4 mb-6">
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Firm Name</label>
                                    <input type="text" name="name" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-cyan-500 focus:bg-white/10 transition-all font-bold placeholder-gray-600" placeholder="e.g. Omega Construct" required>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Description</label>
                                    <textarea name="description" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-cyan-500 focus:bg-white/10 transition-all font-medium placeholder-gray-600 h-24 resize-none" placeholder="What is your firm's mission?"></textarea>
                                </div>
                            </div>

                            <button type="submit" class="w-full py-4 bg-gradient-to-r from-cyan-500 to-blue-600 rounded-xl font-bold text-white uppercase tracking-widest shadow-lg shadow-cyan-500/20 hover:shadow-cyan-500/40 hover:-translate-y-1 transition-all">
                                Establish Firm <span class="opacity-70 text-xs ml-1">(5,000 Coins)</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Firms List -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="flex items-center justify-between mb-8">
            <h3 class="text-2xl font-black text-white flex items-center gap-3">
                <i class="fas fa-globe text-gray-500"></i> Global Directory
            </h3>
            <div class="text-sm font-bold text-gray-500 bg-white/5 px-4 py-2 rounded-lg">
                <?php echo count($firms); ?> Active Alliances
            </div>
        </div>

        <?php if (empty($firms)): ?>
            <div class="text-center py-20 bg-surface/30 rounded-3xl border border-white/5">
                <div class="w-24 h-24 bg-white/5 rounded-full flex items-center justify-center mx-auto mb-6 text-gray-600 text-4xl">
                    <i class="fas fa-city"></i>
                </div>
                <h3 class="text-xl font-bold text-white mb-2">No Firms Established</h3>
                <p class="text-gray-400">Be the pioneer—create the first firm and start recruiting engineers.</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($firms as $f): ?>
                <div class="glass-card p-1 rounded-2xl hover:-translate-y-2 transition-transform duration-300">
                    <div class="bg-surface/80 backdrop-blur-md rounded-[14px] p-6 h-full flex flex-col border border-white/5">
                        <div class="flex items-center gap-4 mb-4">
                            <img src="<?php echo $f['logo_url'] ?: app_base_url('themes/default/assets/images/default-firm.png'); ?>" 
                                 class="w-16 h-16 rounded-xl object-cover border border-white/10 bg-black/20"
                                 onerror="this.src='https://ui-avatars.com/api/?name=<?php echo urlencode($f['name']); ?>&background=0D8ABC&color=fff'">
                            <div>
                                <h4 class="font-bold text-white text-lg leading-tight mb-1"><?php echo htmlspecialchars($f['name']); ?></h4>
                                <div class="flex items-center gap-2">
                                    <span class="bg-white/10 text-white text-[10px] font-bold px-2 py-0.5 rounded uppercase tracking-wider">Lvl <?php echo $f['level']; ?></span>
                                    <span class="text-gray-500 text-xs font-medium"><i class="fas fa-users mr-1"></i><?php echo $f['member_count']; ?></span>
                                </div>
                            </div>
                        </div>

                        <p class="text-gray-400 text-sm mb-6 line-clamp-2 flex-grow h-10">
                            <?php echo htmlspecialchars($f['description'] ?: 'No description provided.'); ?>
                        </p>

                        <div class="flex items-center justify-between mt-auto">
                            <div class="flex -space-x-2">
                                <!-- Placeholders for member avatars if available, otherwise generic circles -->
                                <div class="w-8 h-8 rounded-full bg-white/10 border-2 border-[#0f172a] flex items-center justify-center text-[10px] text-gray-500">
                                    <i class="fas fa-user"></i>
                                </div>
                            </div>
                            <button @click="requestJoin(<?php echo $f['id']; ?>)" class="px-4 py-2 bg-white/5 hover:bg-white/10 border border-white/10 rounded-lg text-sm font-bold text-white transition-colors">
                                Request Join
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script>
        const joinNonce = '<?php echo htmlspecialchars($joinNonce ?? '', ENT_QUOTES, 'UTF-8'); ?>';

        function firmsIndex() {
            return {
                getTrap() {
                    return document.getElementById('firm_create_trap') ? document.getElementById('firm_create_trap').value : '';
                },

                async createFirm(e) {
                    const form = e.target;
                    const fd = new FormData(form);

                    try {
                        Swal.showLoading();
                        const res = await fetch(form.action, { method: 'POST', body: fd });
                        const data = await res.json();

                        if (data.success) {
                            await Swal.fire({ icon: 'success', title: 'Established!', text: 'Firm created successfully.', background: '#1e293b', color: '#fff' });
                            location.href = data.redirect || '/quiz/firms/dashboard';
                        } else {
                            Swal.fire({ icon: 'error', title: 'Start-up Failed', text: data.message, background: '#1e293b', color: '#fff' });
                        }
                    } catch (err) {
                        Swal.fire({ icon: 'error', title: 'Error', text: 'Connection failed.', background: '#1e293b', color: '#fff' });
                    }
                },

                async requestJoin(guildId) {
                    const fd = new FormData();
                    fd.append('guild_id', guildId);
                    fd.append('nonce', joinNonce);
                    fd.append('trap_answer', this.getTrap());

                    const result = await Swal.fire({
                        title: 'Join Firm?',
                        text: "Send a membership request to this firm.",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Send Request',
                        background: '#1e293b',
                        color: '#fff'
                    });

                    if (result.isConfirmed) {
                        try {
                            const res = await fetch('/api/firms/join', { method: 'POST', body: fd });
                            const data = await res.json();
                            
                            if (data.success) {
                                Swal.fire({ icon: 'success', title: 'Sent', text: data.message, background: '#1e293b', color: '#fff' });
                            } else {
                                Swal.fire({ icon: 'error', title: 'Failed', text: data.message, background: '#1e293b', color: '#fff' });
                            }
                        } catch (e) {
                             Swal.fire({ icon: 'error', title: 'Error', text: 'Connection failed.', background: '#1e293b', color: '#fff' });
                        }
                    }
                }
            }
        }
    </script>
</body>
</html>
