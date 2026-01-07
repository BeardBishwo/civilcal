<?php
/**
 * Exam Overview / Instructions Page
 * Premium SaaS Design (Refactored)
 * Stack: PHP + Tailwind CSS + Alpine.js
 */
?>

<!-- Load Tailwind CSS -->
<link rel="stylesheet" href="<?php echo app_base_url('themes/default/assets/css/quiz.min.css?v=' . time()); ?>">

<div class="min-h-screen bg-background text-white font-sans flex items-center justify-center p-6 relative overflow-hidden">

    <!-- Background Effects -->
    <div class="absolute top-0 right-0 -mr-20 -mt-20 w-96 h-96 rounded-full bg-primary/20 blur-3xl opacity-50 animate-pulse"></div>
    <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-80 h-80 rounded-full bg-secondary/20 blur-3xl opacity-50 animate-pulse animation-delay-2000"></div>

    <!-- Back Link -->
    <a href="<?php echo app_base_url('quiz'); ?>" class="absolute top-8 left-8 text-gray-400 hover:text-white flex items-center gap-2 transition-colors z-20">
        <i class="fas fa-arrow-left"></i> Back to Portal
    </a>

    <!-- Main Card -->
    <div class="glass-card w-full max-w-4xl p-10 md:p-16 relative z-10 text-center animate-fade-in-up">
        
        <!-- Icon -->
        <div class="w-24 h-24 mx-auto mb-8 bg-white/5 rounded-2xl flex items-center justify-center text-4xl text-primary border border-white/10 shadow-lg shadow-primary/10">
            <i class="fas fa-file-signature"></i>
        </div>

        <!-- Title & Desc -->
        <h1 class="text-4xl md:text-5xl font-black mb-4 bg-gradient-to-r from-primary via-accent to-secondary bg-clip-text text-transparent">
            <?php echo htmlspecialchars($exam['title']); ?>
        </h1>
        <p class="text-lg text-gray-400 max-w-2xl mx-auto mb-10 leading-relaxed">
            <?php echo !empty($exam['description']) ? htmlspecialchars($exam['description']) : 'Prepare yourself for a challenge. This exam covers comprehensive topics to test your mastery.'; ?>
        </p>

        <!-- Stats Grid -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-10 border-y border-white/10 py-8">
            <div class="text-center">
                <h4 class="text-xs uppercase tracking-wider text-gray-500 font-bold mb-1">Duration</h4>
                <div class="text-2xl font-bold text-white"><?php echo $exam['duration_minutes']; ?>m</div>
            </div>
            <div class="text-center">
                <h4 class="text-xs uppercase tracking-wider text-gray-500 font-bold mb-1">Questions</h4>
                <div class="text-2xl font-bold text-white"><?php echo $question_count; ?></div>
            </div>
            <div class="text-center">
                <h4 class="text-xs uppercase tracking-wider text-gray-500 font-bold mb-1">Total Marks</h4>
                <div class="text-2xl font-bold text-white"><?php echo $exam['total_marks']; ?></div>
            </div>
            <div class="text-center">
                <h4 class="text-xs uppercase tracking-wider text-gray-500 font-bold mb-1">Mode</h4>
                <div class="text-2xl font-bold text-white capitalize"><?php echo $exam['mode']; ?></div>
            </div>
        </div>

        <!-- Instructions -->
        <div class="bg-black/20 rounded-xl p-8 mb-10 text-left border border-white/5">
            <h3 class="text-lg font-bold text-white mb-6 flex items-center gap-2">
                <i class="fas fa-clipboard-list text-primary"></i> Exam Instructions
            </h3>
            <ul class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <li class="flex items-start gap-3 text-gray-400 text-sm">
                    <i class="fas fa-check-circle text-primary mt-1"></i> Complete all questions within the time limit.
                </li>
                <li class="flex items-start gap-3 text-gray-400 text-sm">
                    <i class="fas fa-check-circle text-primary mt-1"></i> Review your answers before submitting.
                </li>
                <li class="flex items-start gap-3 text-gray-400 text-sm">
                    <i class="fas fa-check-circle text-primary mt-1"></i> Don't switch tabs (Proctored Mode enabled).
                </li>
                <li class="flex items-start gap-3 text-gray-400 text-sm">
                    <i class="fas fa-check-circle text-primary mt-1"></i> Incorrect answers may have negative marking.
                </li>
                <li class="flex items-start gap-3 text-gray-400 text-sm">
                    <i class="fas fa-check-circle text-primary mt-1"></i> Ensure stable internet connection.
                </li>
                <li class="flex items-start gap-3 text-gray-400 text-sm">
                    <i class="fas fa-check-circle text-primary mt-1"></i> Best of luck, Engineer!
                </li>
            </ul>
        </div>

        <!-- Start Button -->
        <form action="<?php echo app_base_url('quiz/start/' . $exam['slug']); ?>" method="GET">
            <button type="submit" class="group btn-primary text-lg px-12 py-4 rounded-xl shadow-lg shadow-primary/25 hover:shadow-primary/40 transition-all flex items-center gap-3 mx-auto">
                <span>Start Exam Now</span>
                <i class="fas fa-arrow-right transform group-hover:translate-x-1 transition-transform"></i>
            </button>
        </form>

    </div>
</div>
