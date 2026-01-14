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
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>
</head>

<body class="bg-background text-white min-h-screen pb-20 font-sans" x-data="analysisView()">

    <?php
    // $attempt, $incorrect_answers
    $percentage = ($attempt['total_marks'] > 0) ? ($attempt['score'] / $attempt['total_marks']) * 100 : 0;
    $passed = $percentage >= 40;
    $statusColorClass = $passed ? 'text-green-500 border-green-500 shadow-green-500/30' : 'text-red-500 border-red-500 shadow-red-500/30';
    ?>

    <!-- Report Modal -->
    <div x-show="reporting"
        class="fixed inset-0 z-[100] flex items-center justify-center bg-black/80 backdrop-blur-sm p-4"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        style="display: none;">
        <div class="glass-card w-full max-w-md p-6 border-white/10" @click.away="reporting = false">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold flex items-center gap-2">
                    <i class="fas fa-flag text-red-500"></i> Report Issue
                </h3>
                <button @click="reporting = false" class="text-gray-400 hover:text-white">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <form @submit.prevent="submitReport()">
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs uppercase tracking-wider text-gray-500 font-bold mb-2">Issue Type</label>
                        <select x-model="reportForm.issue_type" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-primary outline-none transition-all">
                            <option value="wrong_answer">Wrong Answer</option>
                            <option value="typo">Typo / Spelling Error</option>
                            <option value="missing_content">Missing Image/Formula</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs uppercase tracking-wider text-gray-500 font-bold mb-2">Details</label>
                        <textarea x-model="reportForm.description"
                            class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-primary outline-none transition-all h-32 resize-none"
                            placeholder="What's wrong?"></textarea>
                    </div>
                    <button type="submit"
                        class="w-full bg-primary hover:bg-primary/90 text-white py-4 rounded-xl font-bold transition-all flex items-center justify-center gap-2"
                        :disabled="isSubmitting">
                        <template x-if="!isSubmitting"><span>Submit Report</span></template>
                        <template x-if="isSubmitting"><i class="fas fa-spinner fa-spin"></i></template>
                    </button>
                </div>
            </form>
        </div>
    </div>

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
                <span class="text-2xl font-bold block mb-1"><?php echo gmdate("H:i:s", max(0, strtotime($attempt['completed_at']) - strtotime($attempt['started_at']))); ?></span>
                <span class="text-xs text-gray-400 uppercase tracking-wider font-semibold">Time Taken</span>
            </div>
            <div class="glass-card p-6 text-center">
                <span class="text-2xl font-bold text-red-400 block mb-1"><?php echo count($incorrect_answers); ?></span>
                <span class="text-xs text-gray-400 uppercase tracking-wider font-semibold">Mistakes</span>
            </div>
            <div class="glass-card p-6 text-center">
                <span class="text-2xl font-bold text-yellow-400 block mb-1">+<?php echo $_SESSION['latest_streak_info']['coins'] ?? 0; ?></span>
                <span class="text-xs text-gray-400 uppercase tracking-wider font-semibold">Coins</span>
            </div>
        </div>

        <!-- Mistake Analysis -->
        <?php if (!empty($incorrect_answers)): ?>
            <div class="glass-card p-8 mb-8">
                <div class="flex items-center justify-between mb-6 pb-4 border-b border-white/10">
                    <div class="text-xl font-bold flex items-center gap-2">
                        <i class="fas fa-microscope text-primary"></i> Review Mistakes
                    </div>
                </div>

                <div class="space-y-8">
                    <?php foreach ($incorrect_answers as $inc): ?>
                        <div class="group relative bg-white/[0.02] border border-white/10 rounded-2xl p-6 hover:bg-white/[0.04] hover:border-red-500/30 transition-all">
                            <!-- Flag Button -->
                            <button @click="openReport(<?php echo $inc['question_id']; ?>)"
                                class="absolute top-4 right-4 text-gray-500 hover:text-red-500 transition-colors p-2"
                                title="Report Question">
                                <i class="fas fa-flag"></i>
                            </button>

                            <div class="flex items-start gap-4 mb-6">
                                <div class="w-8 h-8 rounded-lg bg-red-500/20 text-red-500 flex items-center justify-center shrink-0 font-bold">
                                    !
                                </div>
                                <div class="flex-1">
                                    <div class="font-medium text-lg leading-snug mb-4">
                                        <?php
                                        $content = $inc['content'];
                                        echo is_array($content) ? ($content['text'] ?? '') : $content;
                                        if (is_array($content) && !empty($content['image'])) {
                                            $imgSrc = (strpos($content['image'], 'http') === 0) ? $content['image'] : app_base_url('') . $content['image'];
                                            echo "<div class='mt-4'><img src='{$imgSrc}' class='rounded-xl border border-white/10 max-h-64 object-contain bg-white/5'></div>";
                                        }
                                        ?>
                                    </div>

                                    <!-- Answers Grid -->
                                    <div class="grid md:grid-cols-2 gap-4">
                                        <!-- Your Answer -->
                                        <div class="bg-red-500/10 border border-red-500/20 rounded-xl p-4">
                                            <span class="text-xs font-bold uppercase tracking-wider text-red-400 block mb-2">Your Answer</span>
                                            <div class="text-white">
                                                <?php
                                                $selected = $inc['selected_options'];
                                                if (is_array($selected)) {
                                                    foreach ($selected as $key) {
                                                        $optText = $inc['options'][$key]['text'] ?? $inc['options'][$key] ?? $key;
                                                        echo "<div class='flex items-center gap-2'><i class='fas fa-times text-red-500 text-xs'></i> " . htmlspecialchars($optText) . "</div>";
                                                    }
                                                } else {
                                                    $optText = $inc['options'][$selected]['text'] ?? $inc['options'][$selected] ?? 'No Answer';
                                                    echo "<i class='fas fa-times text-red-500 mr-2'></i> " . htmlspecialchars($optText);
                                                }
                                                ?>
                                            </div>
                                        </div>
                                        <!-- Correct Answer -->
                                        <div class="bg-green-500/10 border border-green-500/20 rounded-xl p-4">
                                            <span class="text-xs font-bold uppercase tracking-wider text-green-400 block mb-2">Correct Answer</span>
                                            <div class="text-white">
                                                <?php
                                                if ($inc['type'] === 'MULTI') {
                                                    if (is_array($inc['correct_answer_json'])) {
                                                        foreach ($inc['correct_answer_json'] as $key) {
                                                            $optText = $inc['options'][$key]['text'] ?? $inc['options'][$key] ?? $key;
                                                            echo "<div class='flex items-center gap-2'><i class='fas fa-check text-green-500 text-xs'></i> " . htmlspecialchars($optText) . "</div>";
                                                        }
                                                    }
                                                } else {
                                                    $correctKey = $inc['correct_answer'] ?? ($inc['correct_answer_json'][0] ?? null);
                                                    $optText = $inc['options'][$correctKey]['text'] ?? $inc['options'][$correctKey] ?? 'Unknown';
                                                    echo "<i class='fas fa-check text-green-500 mr-2'></i> " . htmlspecialchars($optText);
                                                }
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <?php if (!empty($inc['explanation'])): ?>
                                <div class="mt-4 p-4 bg-primary/5 rounded-xl border border-primary/10 text-gray-300 text-sm leading-relaxed">
                                    <strong class="text-white block mb-1 text-xs uppercase tracking-wider opacity-70 flex items-center gap-2">
                                        <i class="fas fa-lightbulb text-yellow-500"></i> Explanation
                                    </strong>
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

    <script>
        function analysisView() {
            return {
                reporting: false,
                isSubmitting: false,
                reportForm: {
                    qid: null,
                    issue_type: 'wrong_answer',
                    description: ''
                },
                openReport(qid) {
                    this.reportForm.qid = qid;
                    this.reportForm.description = '';
                    this.reporting = true;
                },
                submitReport() {
                    this.isSubmitting = true;
                    const formData = new FormData();
                    formData.append('question_id', this.reportForm.qid);
                    formData.append('issue_type', this.reportForm.issue_type);
                    formData.append('csrf_token', '<?php echo $_SESSION["csrf_token"] ?? ""; ?>');

                    fetch('<?php echo app_base_url("quiz/report-question"); ?>', {
                            method: 'POST',
                            body: formData
                        })
                        .then(res => res.json())
                        .then(res => {
                            if (res.success) {
                                alert(res.message);
                                this.reporting = false;
                            } else {
                                alert('Error submitting report.');
                            }
                        })
                        .finally(() => {
                            this.isSubmitting = false;
                        });
                }
            }
        }
    </script>
</body>

</html>