<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($meta_description); ?>">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    
    <!-- Header -->
    <header class="bg-white shadow-sm border-b">
        <div class="max-w-4xl mx-auto px-4 py-4">
            <a href="<?php echo app_base_url(); ?>" class="text-2xl font-bold text-indigo-600 hover:text-indigo-700">
                PSC Exam Prep
            </a>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-4xl mx-auto px-4 py-8">
        
        <!-- Post Header -->
        <header class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-2xl p-8 mb-8">
            <h1 class="text-4xl font-bold mb-4"><?php echo htmlspecialchars($post['title']); ?></h1>
            <div class="flex flex-wrap gap-4 text-sm">
                <span class="bg-white/20 px-3 py-1 rounded-full">
                    <i class="fas fa-list"></i> <?php echo count($questions); ?> Questions
                </span>
                <span class="bg-white/20 px-3 py-1 rounded-full">
                    <i class="fas fa-eye"></i> <?php echo number_format($post['view_count']); ?> Views
                </span>
                <span class="bg-white/20 px-3 py-1 rounded-full">
                    <i class="fas fa-tag"></i> <?php echo ucfirst($post['type']); ?>
                </span>
            </div>
        </header>
        
        <!-- Introduction -->
        <?php if (!empty($post['introduction'])): ?>
        <section class="bg-white rounded-xl shadow-sm p-6 mb-8">
            <div class="prose prose-lg max-w-none">
                <?php echo nl2br(htmlspecialchars($post['introduction'])); ?>
            </div>
        </section>
        <?php endif; ?>
        
        <!-- Questions List -->
        <section class="space-y-6 mb-8">
            <?php foreach ($questions as $index => $q): ?>
                <?php $content = json_decode($q['content'], true); ?>
                
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <!-- Question Header -->
                    <div class="bg-gradient-to-r from-indigo-50 to-purple-50 p-4 border-b flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-indigo-600 text-white rounded-full flex items-center justify-center font-bold">
                                <?php echo $index + 1; ?>
                            </div>
                            <div class="flex gap-2">
                                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs font-bold">
                                    <?php echo $q['default_marks']; ?> marks
                                </span>
                                <span class="bg-purple-100 text-purple-800 px-2 py-1 rounded text-xs font-bold">
                                    <?php echo ['Easy', 'Easy-Mid', 'Medium', 'Hard', 'Expert'][$q['difficulty_level'] - 1]; ?>
                                </span>
                                <?php if ($q['type'] === 'THEORY'): ?>
                                <span class="bg-amber-100 text-amber-800 px-2 py-1 rounded text-xs font-bold">
                                    <?php echo $q['theory_type'] == 'short' ? 'Short' : 'Long'; ?> Answer
                                </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Question Content -->
                    <div class="p-6">
                        <!-- Question -->
                        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-4 rounded-r-lg">
                            <h3 class="font-bold text-sm text-blue-900 mb-2 flex items-center gap-2">
                                <i class="fas fa-question-circle"></i> Question:
                            </h3>
                            <p class="text-gray-800 leading-relaxed">
                                <?php echo nl2br(htmlspecialchars($content['text'] ?? '')); ?>
                            </p>
                            <?php 
                                $options = $content['options'] ?? [];
                                $correct = $content['correct'] ?? null;
                                if (!empty($options)): 
                            ?>
                                <div class="mt-4 grid gap-2">
                                    <?php foreach ($options as $idx => $opt): ?>
                                        <div class="flex items-center p-3 rounded-lg border <?php echo (isset($correct) && (int)$correct === $idx) ? 'border-green-500 bg-green-50' : 'border-gray-200 bg-white'; ?>">
                                            <span class="w-6 h-6 flex items-center justify-center rounded-full <?php echo (isset($correct) && (int)$correct === $idx) ? 'bg-green-500 text-white' : 'bg-gray-100 text-gray-500'; ?> text-xs font-bold mr-3">
                                                <?php echo chr(65 + $idx); ?>
                                            </span>
                                            <span class="text-sm text-gray-800"><?php echo htmlspecialchars($opt); ?></span>
                                            <?php if (isset($correct) && (int)$correct === $idx): ?>
                                                <i class="fas fa-check-circle text-green-500 ml-auto"></i>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Answer (Collapsible) -->
                        <details class="bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg group">
                            <summary class="font-bold cursor-pointer text-green-900 flex items-center gap-2 hover:text-green-700">
                                <i class="fas fa-eye"></i> View Answer
                                <i class="fas fa-chevron-down group-open:rotate-180 transition-transform ml-auto"></i>
                            </summary>
                            <div class="mt-3 text-gray-800 leading-relaxed">
                                <?php echo nl2br(htmlspecialchars($q['answer_explanation'] ?? 'Answer not available')); ?>
                            </div>
                        </details>
                        
                        <!-- Individual Question Link -->
                        <?php if (!empty($q['slug'])): ?>
                        <div class="mt-3 pt-3 border-t">
                            <a href="<?php echo app_base_url($q['slug']); ?>" 
                               class="text-indigo-600 hover:text-indigo-800 text-sm font-medium inline-flex items-center gap-1">
                                View detailed answer <i class="fas fa-arrow-right text-xs"></i>
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </section>
        
        <!-- Conclusion -->
        <?php if (!empty($post['conclusion'])): ?>
        <section class="bg-white rounded-xl shadow-sm p-6 mb-8">
            <div class="prose prose-lg max-w-none">
                <?php echo nl2br(htmlspecialchars($post['conclusion'])); ?>
            </div>
        </section>
        <?php endif; ?>
        
        <!-- CTA Section -->
        <section class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl p-8 text-white text-center">
            <h2 class="text-3xl font-bold mb-4">Ready to Practice?</h2>
            <p class="mb-6 text-indigo-100">Master these questions and ace your PSC exam!</p>
            <div class="flex flex-wrap justify-center gap-4">
                <a href="<?php echo app_base_url('quiz/practice'); ?>" 
                   class="bg-white text-indigo-600 px-8 py-3 rounded-lg font-bold hover:bg-indigo-50 transition inline-flex items-center gap-2">
                    <i class="fas fa-play-circle"></i> Take Mock Test
                </a>
                <a href="<?php echo app_base_url('admin/quiz/questions'); ?>" 
                   class="bg-indigo-500 text-white px-8 py-3 rounded-lg font-bold hover:bg-indigo-400 transition inline-flex items-center gap-2">
                    <i class="fas fa-database"></i> View All Questions
                </a>
            </div>
        </section>

    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white mt-16 py-8">
        <div class="max-w-4xl mx-auto px-4 text-center">
            <p>&copy; <?php echo date('Y'); ?> PSC Exam Prep. All rights reserved.</p>
        </div>
    </footer>

</body>
</html>
