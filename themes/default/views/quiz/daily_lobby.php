<?php

/**
 * Daily Quest Lobby
 * Premium SaaS Design
 * Stack: PHP + Tailwind CSS + Alpine.js
 */
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily Quest | Bishwo Calculator</title>
    <link rel="stylesheet" href="<?php echo app_base_url('themes/default/assets/css/quiz.min.css?v=' . time()); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-background text-white min-h-screen flex items-center justify-center p-4 overflow-hidden">

    <!-- Background Decoration -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-[-10%] left-[-10%] w-96 h-96 bg-primary/10 rounded-full blur-3xl animate-pulse"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-96 h-96 bg-red-500/10 rounded-full blur-3xl animate-pulse" style="animation-delay: 1s;"></div>
    </div>

    <div class="glass-card w-full max-w-2xl p-8 md:p-12 relative z-10 border-white/10 text-center animate-fade-in-up">

        <!-- Icon/Visual -->
        <div class="mb-8 relative inline-block">
            <div class="w-24 h-24 rounded-3xl bg-gradient-to-br from-red-500 to-orange-600 flex items-center justify-center text-4xl shadow-2xl shadow-red-500/20 animate-bounce">
                <i class="fas fa-fire-alt text-white"></i>
            </div>
            <!-- Streak Badge -->
            <div class="absolute -top-2 -right-2 bg-white text-gray-900 text-xs font-bold px-3 py-1 rounded-full shadow-lg">
                <?php echo $user['login_streak'] ?? 0; ?> DAY STREAK
            </div>
        </div>

        <h1 class="text-4xl md:text-5xl font-black mb-4 tracking-tight">DAILY <span class="text-primary">QUEST</span></h1>
        <p class="text-gray-400 text-lg mb-10 max-w-md mx-auto">
            Your daily engineering briefing is ready. Complete today's challenge to earn coins and maintain your streak.
        </p>

        <!-- Quest Intel -->
        <div class="grid grid-cols-2 gap-4 mb-10">
            <div class="bg-white/5 border border-white/10 rounded-2xl p-4 text-left">
                <span class="text-[10px] font-bold text-gray-500 uppercase tracking-widest block mb-1">Target Topic</span>
                <span class="text-white font-bold flex items-center gap-2">
                    <i class="fas fa-crosshairs text-primary"></i>
                    <?php echo $focus_area ?? 'General Engineering'; ?>
                </span>
            </div>
            <div class="bg-white/5 border border-white/10 rounded-2xl p-4 text-left">
                <span class="text-[10px] font-bold text-gray-500 uppercase tracking-widest block mb-1">Potential Loot</span>
                <span class="text-white font-bold flex items-center gap-2">
                    <i class="fas fa-coins text-yellow-500"></i>
                    <?php echo $daily['reward_coins'] ?? 50; ?> Coins
                </span>
            </div>
        </div>

        <!-- Rules/Info -->
        <div class="flex flex-col md:flex-row items-center justify-center gap-6 text-sm text-gray-500 mb-10">
            <div class="flex items-center gap-2">
                <i class="fas fa-clock text-primary"></i> 10 Questions
            </div>
            <div class="flex items-center gap-2">
                <i class="fas fa-shield-alt text-green-500"></i> Fixed Difficulty
            </div>
            <div class="flex items-center gap-2">
                <i class="fas fa-bolt text-yellow-500"></i> Instant Reward
            </div>
        </div>

        <!-- Call to Action -->
        <form action="<?php echo app_base_url('/quiz/daily'); ?>" method="POST">
            <input type="hidden" name="action" value="start">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

            <div class="flex flex-col gap-4">
                <button type="submit" class="w-full bg-primary hover:bg-primary/90 text-white py-5 rounded-2xl text-xl font-black transition-all shadow-xl shadow-primary/20 hover:scale-[1.02] active:scale-[0.98] group flex items-center justify-center gap-3">
                    <span>COMMENCE MISSION</span>
                    <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                </button>

                <a href="<?php echo app_base_url('/quiz'); ?>" class="text-gray-500 hover:text-white font-bold text-sm transition-colors uppercase tracking-widest">
                    Maybe Later
                </a>
            </div>
        </form>

    </div>

    <!-- Stats Mini-Panel -->
    <div class="fixed bottom-8 left-8 hidden md:block animate-fade-in-up" style="animation-delay: 0.5s;">
        <div class="bg-surface/50 backdrop-blur-md border border-white/10 rounded-2xl p-4 pr-8 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-primary/20 flex items-center justify-center text-primary">
                <i class="fas fa-user-shield text-xl"></i>
            </div>
            <div>
                <div class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-0.5">Your Authority</div>
                <div class="text-white font-bold">LVL <?php echo floor(($user['xp'] ?? 0) / 1000) + 1; ?> ENGINEER</div>
            </div>
        </div>
    </div>

</body>

</html>