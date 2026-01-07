
<?php include_once __DIR__ . '/../../partials/header.php'; ?>

<!-- Load Tailwind CSS -->
<link rel="stylesheet" href="<?php echo app_base_url('themes/default/assets/css/quiz.min.css?v=' . time()); ?>">

<div class="bg-background min-h-screen font-sans text-white pb-20 overflow-x-hidden relative">
    
    <!-- Background Gradient Orbs -->
    <!-- Background Gradient Orbs Removed per user request -->
    <div class="fixed top-0 left-0 w-full h-full overflow-hidden pointer-events-none z-0">
        <!-- Orbs removed -->
    </div>

    <!-- Main Content -->
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-24 lg:pt-32">
        
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            
            <!-- Hero Text -->
            <div class="lg:col-span-7">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/5 border border-white/10 text-xs font-bold uppercase tracking-wider mb-6 text-accent animate-fade-in-up">
                    <span class="w-2 h-2 rounded-full bg-accent animate-pulse"></span>
                    Realtime Arena
                </div>
                
                <h1 class="text-5xl md:text-7xl font-black tracking-tight mb-6 leading-tight animate-fade-in-up animation-delay-100">
                    Engineering <br>
                    <span class="bg-gradient-to-r from-primary via-accent to-secondary bg-clip-text text-transparent">Battle Royale</span>
                </h1>
                
                <p class="text-xl text-gray-400 mb-10 max-w-2xl leading-relaxed animate-fade-in-up animation-delay-200">
                    Join friends or create your own lobby. Server-locked scoring, anti-replay wagers, and premium-grade distinction.
                </p>
                
                <div class="flex flex-wrap gap-8 animate-fade-in-up animation-delay-300">
                    <div class="flex flex-col gap-1">
                        <span class="text-xs uppercase tracking-wider text-gray-500 font-bold">Latency Guard</span>
                        <span class="text-white font-bold flex items-center gap-2">
                            <i class="fas fa-shield-alt text-green-400"></i> Active
                        </span>
                    </div>
                    <div class="flex flex-col gap-1">
                        <span class="text-xs uppercase tracking-wider text-gray-500 font-bold">Security</span>
                        <span class="text-white font-bold flex items-center gap-2">
                            <i class="fas fa-lock text-primary"></i> Nonce + Honeypot
                        </span>
                    </div>
                    <div class="flex flex-col gap-1">
                        <span class="text-xs uppercase tracking-wider text-gray-500 font-bold">Players</span>
                        <span class="text-white font-bold flex items-center gap-2">
                            <i class="fas fa-users text-accent"></i> Live
                        </span>
                    </div>
                </div>
            </div>

            <!-- Join Card -->
            <div class="lg:col-span-5 relative">
                <!-- Decorative Blur Removed -->
                
                <div class="glass-card p-1 rounded-3xl animate-fade-in-up animation-delay-400">
                    <!-- Darker, higher contrast background -->
                    <div class="bg-gray-900/60 backdrop-blur-2xl rounded-[20px] p-8 border border-white/10 shadow-[0_8px_32px_rgba(0,0,0,0.5)]">
                        <div class="flex items-center gap-4 mb-8">
                            <div class="w-12 h-12 rounded-xl bg-white/5 flex items-center justify-center text-white text-xl border border-white/10">
                                <i class="fas fa-search"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-white leading-tight">Join a Battle</h3>
                                <p class="text-sm text-gray-400">Enter a lobby code to enter</p>
                            </div>
                        </div>

                        <form action="/quiz/lobby/join" method="POST" class="space-y-4">
                            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                            
                            <div class="relative group">
                                <input type="text" name="code" 
                                       class="glass-input w-full px-5 py-4 pl-12 rounded-xl bg-black/50 border border-white/20 text-white placeholder-gray-500 focus:outline-none focus:border-primary focus:bg-black/70 transition-all font-mono text-lg uppercase tracking-widest shadow-inner"
                                       placeholder="A7X92" required>
                                <i class="fas fa-hashtag absolute left-5 top-1/2 -translate-y-1/2 text-gray-500 group-focus-within:text-primary transition-colors"></i>
                            </div>

                            <button type="submit" class="btn-primary w-full py-4 rounded-xl font-bold flex items-center justify-center gap-2 text-lg shadow-lg shadow-primary/25 hover:shadow-primary/40 transition-all">
                                Join Room <i class="fas fa-arrow-right"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Options Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-20">
            
            <!-- Host Card -->
            <div class="glass-card p-1 rounded-3xl group hover:-translate-y-1 transition-transform duration-300">
                <!-- Darker background for better text contrast -->
                <div class="bg-gray-900/40 hover:bg-gray-900/60 rounded-[20px] p-8 h-full border border-white/10 hover:border-accent/30 transition-all shadow-lg">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-12 h-12 rounded-xl bg-accent/20 flex items-center justify-center text-accent text-xl border border-accent/20">
                            <i class="fas fa-crown"></i>
                        </div>
                        <div>
                            <span class="text-xs font-bold text-accent uppercase tracking-wider block mb-1">Host Mode</span>
                            <h3 class="text-xl font-bold text-white">Create a Lobby</h3>
                        </div>
                    </div>
                    
                    <p class="text-gray-400 mb-8 leading-relaxed">
                        Spin up a secure lobby, invite friends via code, and let the server handle fairness and scoring.
                    </p>

                    <form action="/quiz/lobby/create" method="POST">
                        <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                        <input type="hidden" name="exam_id" value="1"> <!-- Default exam for now -->
                        <button type="submit" class="w-full py-3 bg-white/5 hover:bg-white/10 text-white border border-white/10 rounded-xl font-bold transition-all flex items-center justify-center gap-2 group-hover:border-accent/50 group-hover:text-accent">
                            Create New Room <i class="fas fa-plus"></i>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Features Card -->
            <div class="glass-card p-1 rounded-3xl group hover:-translate-y-1 transition-transform duration-300">
                <div class="bg-gray-900/40 hover:bg-gray-900/60 rounded-[20px] p-8 h-full border border-white/10 hover:border-yellow-500/30 transition-all shadow-lg">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-12 h-12 rounded-xl bg-yellow-500/20 flex items-center justify-center text-yellow-400 text-xl border border-yellow-500/20">
                            <i class="fas fa-bolt"></i>
                        </div>
                        <div>
                            <span class="text-xs font-bold text-yellow-400 uppercase tracking-wider block mb-1">Stack Specs</span>
                            <h3 class="text-xl font-bold text-white">Competitive Engine</h3>
                        </div>
                    </div>

                    <ul class="space-y-3">
                        <li class="flex items-start gap-3">
                            <i class="fas fa-check-circle text-green-400 mt-1"></i>
                            <span class="text-gray-400 text-sm">Server-authoritative scoring & wager validation</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <i class="fas fa-check-circle text-green-400 mt-1"></i>
                            <span class="text-gray-400 text-sm">Real-time WebSocket-style pulse updates</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <i class="fas fa-check-circle text-green-400 mt-1"></i>
                            <span class="text-gray-400 text-sm">Premium glass UI aligned with core design</span>
                        </li>
                    </ul>
                </div>
            </div>
            
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../../partials/footer.php'; ?>
