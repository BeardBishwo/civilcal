<?php
/**
 * Exam Result / Analysis Page
 * Premium SaaS Design (Refactored)
 * Stack: PHP + Tailwind CSS
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam Analysis | Bishwo Calculator</title>
    <!-- Load Tailwind CSS -->
    <link rel="stylesheet" href="<?php echo app_base_url('themes/default/assets/css/quiz.min.css?v=' . time()); ?>">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-background text-white min-h-screen pb-20 font-sans">

    <?php
    // $attempt, $incorrect_answers
    $percentage = ($attempt['score'] / $attempt['total_marks']) * 100;
    $passed = $percentage >= 40;
    $statusColorClass = $passed ? 'text-green-500 border-green-500 shadow-green-500/30' : 'text-red-500 border-red-500 shadow-red-500/30';
    $statusBgClass = $passed ? 'bg-green-500/10' : 'bg-red-500/10';
    ?>

    <!-- Header -->
    <header class="bg-gradient-to-br from-surface to-background pt-12 pb-20 border-b border-white/10 text-center relative overflow-hidden">
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none">
            <div class="absolute top-[-10%] left-[-10%] w-96 h-96 bg-primary/10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-[-10%] right-[-10%] w-96 h-96 bg-secondary/10 rounded-full blur-3xl"></div>
        </div>

        <div class="relative z-10 container mx-auto px-4">
            <div class="w-40 h-40 mx-auto mb-6 rounded-full border-4 <?php echo $statusColorClass; ?> flex flex-col items-center justify-center bg-surface shadow-2xl relative animate-fade-in-up">
                <span class="text-4xl font-extrabold leading-none <?php echo $passed ? 'text-green-400' : 'text-red-400'; ?>">
                    <?php echo round($attempt['score'], 1); ?>
                </span>
                <span class="text-sm text-gray-400 mt-1 font-medium">out of <?php echo $attempt['total_marks']; ?></span>
            </div>

            <h1 class="text-3xl font-bold mb-2"><?php echo htmlspecialchars($attempt['title']); ?></h1>
            <p class="text-gray-400 text-sm flex items-center justify-center gap-2">
                <span>Completed on <?php echo date('M d, Y h:i A', strtotime($attempt['completed_at'])); ?></span>
                <span class="w-1 h-1 rounded-full bg-gray-500"></span>
                <span class="<?php echo $passed ? 'text-green-400 bg-green-400/10 px-2 py-0.5 rounded' : 'text-red-400 bg-red-400/10 px-2 py-0.5 rounded'; ?> font-semibold">
                    <?php echo $passed ? 'Passed' : 'Failed'; ?>
                </span>
            </p>
        </div>
    </header>

    <div class="container mx-auto px-4 -mt-10 relative z-20 max-w-5xl">
        <!-- Key Metrics -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <div class="glass-card p-6 text-center">
                <span class="text-2xl font-bold block mb-1"><?php echo round($percentage, 1); ?>%</span>
                <span class="text-xs text-gray-400 uppercase tracking-wider font-semibold">Percentage</span>
            </div>
            <div class="glass-card p-6 text-center">
                <span class="text-2xl font-bold block mb-1"><?php echo gmdate("H:i:s", strtotime($attempt['completed_at']) - strtotime($attempt['started_at'])); ?></span>
                <span class="text-xs text-gray-400 uppercase tracking-wider font-semibold">Time Taken</span>
            </div>
            <div class="glass-card p-6 text-center">
                <span class="text-2xl font-bold text-red-400 block mb-1"><?php echo count($incorrect_answers); ?></span>
                <span class="text-xs text-gray-400 uppercase tracking-wider font-semibold">Mistakes</span>
            </div>
            <div class="glass-card p-6 text-center">
                <span class="text-2xl font-bold text-yellow-400 block mb-1">+<?php echo $_SESSION['latest_streak_info']['coins'] ?? 0; ?></span>
                <span class="text-xs text-gray-400 uppercase tracking-wider font-semibold">Coins Earned</span>
            </div>
        </div>

        <!-- Smart Analysis / Mistakes -->
        <?php if (!empty($incorrect_answers)): ?>
        <div class="glass-card p-8 mb-8">
            <div class="flex items-center justify-between mb-6 pb-4 border-b border-white/10">
                <div class="text-xl font-bold flex items-center gap-2">
                    <i class="fas fa-microscope text-primary"></i> Smart Analysis
                </div>
            </div>
            
            <div class="space-y-4">
                <?php foreach ($incorrect_answers as $inc): ?>
                    <div class="bg-red-500/5 border border-red-500/20 rounded-xl p-6 hover:bg-red-500/10 hover:border-red-500/30 transition-all">
                        <div class="flex items-start gap-3 mb-4">
                            <i class="fas fa-times-circle text-red-500 mt-1 text-lg shrink-0"></i>
                            <div class="font-medium text-lg leading-snug">
                                <?php 
                                    if (is_array($inc['content'])) {
                                        echo $inc['content']['text'] ?? '';
                                    } else {
                                        echo $inc['content'];
                                    }
                                ?>
                            </div>
                        </div>
                        <?php if (!empty($inc['explanation'])): ?>
                            <div class="mt-4 pt-4 border-t border-red-500/10 text-gray-300 text-sm leading-relaxed">
                                <strong class="text-white block mb-1 text-xs uppercase tracking-wider opacity-70"><i class="fas fa-lightbulb text-yellow-500 mr-1"></i> Explanation</strong>
                                <?php echo $inc['explanation']; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Actions -->
        <div class="flex flex-col md:flex-row justify-center gap-4 mt-8">
            <a href="<?php echo app_base_url('quiz'); ?>" class="btn-secondary px-8 py-3 rounded-xl border border-white/10 hover:bg-white/5 flex items-center justify-center gap-2 font-semibold transition-all">
                <i class="fas fa-home"></i> Back to Portal
            </a>
            <a href="<?php echo app_base_url('/quiz/start/' . ($attempt['slug'] ?? 'daily-quest')); ?>" class="btn-primary px-8 py-3 rounded-xl shadow-lg shadow-primary/20 flex items-center justify-center gap-2 font-semibold transition-all">
                <i class="fas fa-redo"></i> Retake Exam
            </a>
        </div>
    </div>

</body>
</html>
