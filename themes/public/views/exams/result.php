<?php include_once __DIR__ . '/../../layouts/header.php'; ?>

<?php
$score = $session['score'];
$total = $session['total_questions'];
$percentage = ($total > 0) ? round(($score / $total) * 100) : 0;
$gradeColor = ($percentage >= 50) ? 'green' : 'red';
$gradeText = ($percentage >= 50) ? 'Passed' : 'Needs Improvement';
?>

<div class="bg-gray-50 min-h-screen py-10">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Result Card -->
        <div class="bg-white rounded-3xl shadow-xl overflow-hidden mb-12">
            <div class="bg-gradient-to-r from-gray-900 to-gray-800 p-8 text-center text-white">
                <h1 class="text-3xl font-bold mb-2">Exam Results</h1>
                <p class="text-gray-400">Section: Exam Session #<?php echo $session['id']; ?></p>
            </div>
            
            <div class="p-8 grid grid-cols-1 md:grid-cols-3 gap-8 text-center divide-y md:divide-y-0 md:divide-x divide-gray-100">
                <div class="py-4">
                    <div class="text-gray-500 text-sm font-bold uppercase tracking-wide mb-1">Score</div>
                    <div class="text-5xl font-extrabold text-<?php echo $gradeColor; ?>-600">
                        <?php echo $score; ?><span class="text-2xl text-gray-400">/<?php echo $total; ?></span>
                    </div>
                </div>
                <div class="py-4">
                    <div class="text-gray-500 text-sm font-bold uppercase tracking-wide mb-1">Accuracy</div>
                    <div class="text-5xl font-extrabold text-gray-800">
                        <?php echo $percentage; ?><span class="text-2xl">%</span>
                    </div>
                </div>
                <div class="py-4">
                    <div class="text-gray-500 text-sm font-bold uppercase tracking-wide mb-1">Verdict</div>
                    <div class="text-3xl font-bold text-<?php echo $gradeColor; ?>-600 mt-2">
                        <?php echo $gradeText; ?>
                    </div>
                </div>
            </div>
            
            <div class="bg-gray-50 p-6 flex justify-center gap-4 border-t border-gray-100">
                <a href="<?php echo app_base_url('exams'); ?>" class="px-6 py-2 rounded-xl bg-white border border-gray-300 text-gray-700 font-bold hover:bg-gray-100 transition">
                    Back to Hub
                </a>
            </div>
        </div>
        
        <!-- Detailed Breakdown -->
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Detailed Explanations</h2>
        
        <div class="space-y-6">
            <?php foreach($answers as $index => $ans): ?>
                <?php 
                    $content = json_decode($ans['content'], true);
                    $options = $content['options'] ?? [];
                    $correctIdx = $content['correct'] ?? null;
                    $userIdx = $ans['user_answer'];
                    
                    $isCorrect = $ans['is_correct'];
                    $statusColor = $isCorrect ? 'green' : 'red';
                    $borderColor = $isCorrect ? 'border-green-200' : 'border-red-200';
                    $bgColor = $isCorrect ? 'bg-green-50' : 'bg-red-50';
                ?>
                
                <div class="bg-white rounded-2xl shadow-sm border <?php echo $borderColor; ?> overflow-hidden">
                    <div class="p-6">
                        <div class="flex items-start gap-4">
                            <div class="mt-1 w-8 h-8 rounded-full <?php echo $bgColor; ?> flex items-center justify-center text-<?php echo $statusColor; ?>-600 font-bold shrink-0">
                                <?php echo $index + 1; ?>
                            </div>
                            <div class="flex-grow">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">
                                    <?php echo nl2br(htmlspecialchars($content['text'])); ?>
                                </h3>
                                
                                <div class="grid gap-2 mb-4">
                                    <?php foreach($options as $optIdx => $opt): ?>
                                        <?php 
                                            // Determine styling for this option
                                            $optClass = "border-gray-200 bg-white";
                                            $icon = "";
                                            
                                            if ((string)$correctIdx === (string)$optIdx) {
                                                $optClass = "border-green-500 bg-green-50 ring-1 ring-green-500";
                                                $icon = '<i class="fas fa-check text-green-600 ml-auto"></i>';
                                            } elseif ((string)$userIdx === (string)$optIdx && !$isCorrect) {
                                                $optClass = "border-red-500 bg-red-50 ring-1 ring-red-500";
                                                $icon = '<i class="fas fa-times text-red-600 ml-auto"></i>';
                                            }
                                        ?>
                                        <div class="p-3 rounded-lg border <?php echo $optClass; ?> flex items-center">
                                            <span class="w-6 h-6 rounded-full border flex items-center justify-center text-xs font-bold mr-3 bg-white text-gray-500">
                                                <?php echo chr(65 + $optIdx); ?>
                                            </span>
                                            <span class="text-gray-800"><?php echo htmlspecialchars($opt); ?></span>
                                            <?php echo $icon; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                
                                <div class="bg-gray-50 rounded-xl p-4 text-gray-700 text-sm leading-relaxed">
                                    <span class="font-bold block text-gray-900 mb-1">Explanation:</span>
                                    <?php echo nl2br(htmlspecialchars($ans['answer_explanation'] ?? 'No explanation provided.')); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
    </div>
</div>

<?php include_once __DIR__ . '/../../layouts/footer.php'; ?>
