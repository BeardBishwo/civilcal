
<?php
/**
 * Quiz Leaderboard
 * Premium SaaS Design (Refactored)
 * Stack: PHP + Tailwind CSS + Alpine.js
 */
?>

<!-- Load Tailwind CSS -->
<link rel="stylesheet" href="<?php echo app_base_url('themes/default/assets/css/quiz.min.css?v=' . time()); ?>">

<div class="min-h-screen bg-background text-white font-sans relative overflow-hidden pb-20">

    <!-- Background Decoration -->
    <div class="absolute top-0 left-0 w-full h-[500px] bg-gradient-radial from-primary/10 to-transparent opacity-50 pointer-events-none"></div>

    <!-- Header Section -->
    <section class="relative pt-16 pb-12 text-center z-10 px-4">
        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-yellow-500/10 border border-yellow-500/30 text-yellow-500 text-sm font-bold mb-6 animate-fade-in-up">
            <i class="fas fa-trophy"></i>
            <span>Hall of Fame</span>
        </div>
        
        <h1 class="text-4xl md:text-6xl font-black mb-4 bg-gradient-to-r from-primary via-accent to-secondary bg-clip-text text-transparent animate-fade-in-up animation-delay-100">
            Top Performers
        </h1>
        <p class="text-lg text-gray-400 max-w-xl mx-auto mb-10 animate-fade-in-up animation-delay-200">
            Compete with the best and climb the ranks to prove your mastery.
        </p>
        
        <!-- Period Filters -->
        <div class="flex flex-wrap justify-center gap-4 animate-fade-in-up animation-delay-300">
            <?php 
            $periods = [
                'weekly' => ['icon' => 'fa-calendar-week', 'label' => 'Weekly'],
                'monthly' => ['icon' => 'fa-calendar-alt', 'label' => 'Monthly'],
                'yearly' => ['icon' => 'fa-calendar', 'label' => 'Yearly']
            ];
            foreach ($periods as $key => $p): 
                $isActive = $current_period == $key;
            ?>
            <a href="?period=<?php echo $key; ?>" 
               class="flex items-center gap-2 px-6 py-3 rounded-xl font-semibold transition-all <?php echo $isActive ? 'bg-primary text-white shadow-lg shadow-primary/25' : 'bg-surface border border-white/10 text-gray-400 hover:text-white hover:bg-white/5'; ?>">
                <i class="fas <?php echo $p['icon']; ?>"></i>
                <span><?php echo $p['label']; ?></span>
            </a>
            <?php endforeach; ?>
        </div>
    </section>

    <div class="container mx-auto px-4 max-w-6xl relative z-10">
        
        <?php if (empty($rankings)): ?>
        <!-- Empty State -->
        <div class="glass-card text-center py-20 animate-fade-in-up">
            <div class="text-6xl text-yellow-500/50 mb-6"><i class="fas fa-trophy"></i></div>
            <h3 class="text-2xl font-bold mb-2">No Rankings Yet</h3>
            <p class="text-gray-400 mb-8">Be the first to take a test and claim the top spot!</p>
            <a href="<?php echo app_base_url('quiz'); ?>" class="btn-primary px-8 py-3 rounded-xl inline-flex items-center gap-2">
                <i class="fas fa-play-circle"></i> Start Learning
            </a>
        </div>
        <?php else: ?>

        <?php 
        $topThree = array_slice($rankings, 0, 3);
        $restRankings = array_slice($rankings, 3);
        ?>

        <!-- Top 3 Podium -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12 items-end">
            <?php foreach ($topThree as $index => $rank): 
                $pos = $rank['calculated_rank'];
                $isFirst = $pos == 1;
                $isSecond = $pos == 2;
                $isThird = $pos == 3;
                
                $orderClass = $isFirst ? 'md:order-2 md:-mt-12' : ($isSecond ? 'md:order-1' : 'md:order-3');
                $borderClass = $isFirst ? 'border-yellow-500/50' : ($isSecond ? 'border-gray-400/50' : 'border-orange-500/50');
                $bgClass = $isFirst ? 'bg-yellow-500/10' : ($isSecond ? 'bg-gray-400/10' : 'bg-orange-500/10');
                $medal = $isFirst ? '🥇' : ($isSecond ? '🥈' : '🥉');
                $scaleClass = $isFirst ? 'transform md:scale-110 z-20' : 'z-10';
            ?>
            <div class="glass-card p-8 text-center relative <?php echo "$orderClass $scaleClass"; ?> animate-fade-in-up">
                <div class="text-4xl mb-4"><?php echo $medal; ?></div>
                
                <div class="relative inline-block mb-4">
                    <div class="w-24 h-24 rounded-full flex items-center justify-center text-3xl font-black text-white bg-gradient-to-br from-primary to-accent shadow-lg ring-4 ring-offset-4 ring-offset-background <?php echo $borderClass; ?>">
                        <?php echo strtoupper(substr($rank['full_name'], 0, 1)); ?>
                    </div>
                    <div class="absolute -bottom-2 -right-2 w-8 h-8 rounded-full bg-background border border-white/10 flex items-center justify-center text-xs font-bold">
                        #<?php echo $pos; ?>
                    </div>
                </div>

                <h3 class="text-xl font-bold mb-1 truncate"><?php echo htmlspecialchars($rank['full_name']); ?></h3>
                <p class="text-sm text-gray-400 mb-6">@<?php echo htmlspecialchars($rank['username']); ?></p>

                <div class="grid grid-cols-3 gap-2 border-t border-white/10 pt-4">
                    <div>
                        <div class="text-lg font-black text-transparent bg-clip-text bg-gradient-to-r from-primary to-accent"><?php echo number_format($rank['total_score']); ?></div>
                        <div class="text-[10px] uppercase tracking-wider text-gray-500">Score</div>
                    </div>
                    <div class="border-l border-white/10">
                        <div class="text-lg font-bold text-white"><?php echo number_format($rank['accuracy_avg'], 1); ?>%</div>
                        <div class="text-[10px] uppercase tracking-wider text-gray-500">Acc</div>
                    </div>
                    <div class="border-l border-white/10">
                        <div class="text-lg font-bold text-white"><?php echo $rank['tests_taken']; ?></div>
                        <div class="text-[10px] uppercase tracking-wider text-gray-500">Tests</div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Rest of List -->
        <?php if (!empty($restRankings)): ?>
        <div class="glass-card p-0 overflow-hidden animate-fade-in-up animation-delay-500">
            <div class="px-6 py-4 border-b border-white/10 bg-white/5">
                <h2 class="font-bold text-lg">Runner Ups</h2>
            </div>
            <div class="divide-y divide-white/5">
                <?php foreach ($restRankings as $rank): 
                    $isUser = ($rank['user_id'] == ($_SESSION['user_id'] ?? 0));
                ?>
                <div class="p-4 flex items-center gap-4 hover:bg-white/5 transition-colors <?php echo $isUser ? 'bg-primary/10' : ''; ?>">
                    <div class="w-10 text-center font-black text-gray-500 text-lg">#<?php echo $rank['calculated_rank']; ?></div>
                    
                    <div class="w-10 h-10 rounded-full bg-surface border border-white/10 flex items-center justify-center font-bold text-sm shrink-0">
                        <?php echo strtoupper(substr($rank['full_name'], 0, 1)); ?>
                    </div>
                    
                    <div class="flex-1 min-w-0">
                        <h4 class="font-bold text-white truncate"><?php echo htmlspecialchars($rank['full_name']); ?></h4>
                        <p class="text-xs text-gray-500">@<?php echo htmlspecialchars($rank['username']); ?></p>
                    </div>

                    <div class="hidden md:flex items-center gap-6 text-sm">
                        <div class="w-24 text-right">
                            <i class="fas fa-star text-yellow-500 mr-1"></i> <span class="font-bold"><?php echo number_format($rank['total_score']); ?></span>
                        </div>
                        <div class="w-20 text-right">
                            <i class="fas fa-bullseye text-blue-400 mr-1"></i> <?php echo number_format($rank['accuracy_avg'], 1); ?>%
                        </div>
                        <div class="w-20 text-right text-gray-400">
                            <?php echo $rank['tests_taken']; ?> tests
                        </div>
                    </div>
                    
                    <!-- Mobile Stats Compact -->
                    <div class="md:hidden text-right">
                         <div class="font-bold text-yellow-500"><?php echo number_format($rank['total_score']); ?></div>
                         <div class="text-xs text-gray-500"><?php echo number_format($rank['accuracy_avg'], 1); ?>%</div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php endif; ?>
    </div>
</div>
