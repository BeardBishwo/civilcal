<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($meta_description); ?>">
    
    <!-- Open Graph for Social Sharing -->
    <meta property="og:title" content="<?php echo htmlspecialchars($page_title); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($meta_description); ?>">
    <meta property="og:type" content="article">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Schema.org for SEO -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "Article",
      "headline": "<?php echo htmlspecialchars($question['question_text']); ?>",
      "author": {
        "@type": "Organization",
        "name": "PSC Exam Prep"
      },
      "datePublished": "<?php echo $question['blog_published_at'] ?? date('Y-m-d'); ?>",
      "educationalLevel": "<?php echo $question['theory_type'] == 'short' ? 'Intermediate' : 'Advanced'; ?>"
    }
    </script>
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
        
        <!-- Breadcrumb -->
        <nav class="text-sm text-gray-600 mb-6">
            <a href="<?php echo app_base_url(); ?>" class="hover:text-indigo-600">Home</a>
            <span class="mx-2">›</span>
            <span><?php echo htmlspecialchars($question['category_name'] ?? 'PSC Questions'); ?></span>
        </nav>

        <!-- Article -->
        <article class="bg-white rounded-2xl shadow-lg overflow-hidden">
            
            <!-- Header -->
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-8 text-white">
                <h1 class="text-3xl md:text-4xl font-bold leading-tight mb-4">
                    <?php echo nl2br(htmlspecialchars($question['question_text'])); ?>
                </h1>
                
                <div class="flex flex-wrap gap-3 text-sm">
                    <span class="bg-white/20 px-3 py-1 rounded-full">
                        <i class="fas fa-folder"></i> <?php echo htmlspecialchars($question['category_name'] ?? 'General'); ?>
                    </span>
                    <span class="bg-white/20 px-3 py-1 rounded-full">
                        <i class="fas fa-star"></i> <?php echo ['Easy', 'Easy-Mid', 'Medium', 'Hard', 'Expert'][$question['difficulty_level'] - 1]; ?>
                    </span>
                    <span class="bg-white/20 px-3 py-1 rounded-full">
                        <i class="fas fa-award"></i> <?php echo $question['default_marks']; ?> Marks
                    </span>
                    <span class="bg-white/20 px-3 py-1 rounded-full">
                        <i class="fas fa-file-alt"></i> <?php echo $question['theory_type'] == 'short' ? 'Short Answer' : 'Long Answer'; ?>
                    </span>
                    <span class="bg-white/20 px-3 py-1 rounded-full">
                        <i class="fas fa-clock"></i> <?php echo $readingTime; ?> min read
                    </span>
                </div>
            </div>

            <!-- Content -->
            <div class="p-8">
                
                <!-- Question Section -->
                <section class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-question-circle text-indigo-600"></i>
                        Question
                    </h2>
                    <div class="bg-blue-50 border-l-4 border-blue-500 p-6 rounded-r-lg">
                        <p class="text-lg text-gray-700 leading-relaxed">
                            <?php echo nl2br(htmlspecialchars($question['question_text'])); ?>
                        </p>

                        <?php if (!empty($question['options'])): ?>
                            <div class="mt-6 grid gap-3">
                                <?php foreach ($question['options'] as $index => $option): ?>
                                    <div class="flex items-center p-4 rounded-lg border-2 <?php echo (isset($question['correct']) && (int)$question['correct'] === $index) ? 'border-green-500 bg-green-50' : 'border-gray-200 bg-white'; ?>">
                                        <span class="w-8 h-8 flex items-center justify-center rounded-full <?php echo (isset($question['correct']) && (int)$question['correct'] === $index) ? 'bg-green-500 text-white' : 'bg-gray-100 text-gray-500'; ?> font-bold mr-3">
                                            <?php echo chr(65 + $index); ?>
                                        </span>
                                        <span class="text-gray-800 font-medium"><?php echo htmlspecialchars($option); ?></span>
                                        <?php if (isset($question['correct']) && (int)$question['correct'] === $index): ?>
                                            <i class="fas fa-check-circle text-green-500 ml-auto text-xl"></i>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </section>

                <!-- Answer Section -->
                <section class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-book-open text-green-600"></i>
                        Model Answer & Marking Scheme
                    </h2>
                    <div class="bg-green-50 border-l-4 border-green-500 p-6 rounded-r-lg prose prose-lg max-w-none">
                        <?php echo nl2br(htmlspecialchars($question['answer_explanation'] ?? 'Answer not available')); ?>
                    </div>
                </section>

                <!-- Share Section -->
                <section class="mb-8 p-6 bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Share this question</h3>
                    <div class="flex flex-wrap gap-3">
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(app_base_url($question['slug'])); ?>" 
                           target="_blank"
                           class="flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                            <i class="fab fa-facebook"></i> Facebook
                        </a>
                        <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode(app_base_url($question['slug'])); ?>&text=<?php echo urlencode($question['question_text']); ?>" 
                           target="_blank"
                           class="flex items-center gap-2 bg-sky-500 text-white px-4 py-2 rounded-lg hover:bg-sky-600 transition">
                            <i class="fab fa-twitter"></i> Twitter
                        </a>
                        <a href="https://wa.me/?text=<?php echo urlencode($question['question_text'] . ' ' . app_base_url($question['slug'])); ?>" 
                           target="_blank"
                           class="flex items-center gap-2 bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
                            <i class="fab fa-whatsapp"></i> WhatsApp
                        </a>
                    </div>
                </section>

                <!-- Related Questions -->
                <?php if (!empty($related)): ?>
                <section class="mb-8">
                    <h3 class="text-2xl font-bold text-gray-800 mb-4">Related Questions</h3>
                    <div class="grid md:grid-cols-2 gap-4">
                        <?php foreach (array_slice($related, 0, 6) as $r): ?>
                            <?php $rContent = json_decode($r['content'], true); ?>
                            <a href="<?php echo app_base_url($r['slug']); ?>" 
                               class="block p-4 bg-gray-50 hover:bg-indigo-50 border border-gray-200 hover:border-indigo-300 rounded-lg transition group">
                                <div class="text-gray-700 group-hover:text-indigo-700 font-medium line-clamp-2 mb-2">
                                    <?php echo htmlspecialchars(substr($rContent['text'], 0, 100)); ?>...
                                </div>
                                <div class="flex gap-2 text-xs text-gray-500">
                                    <span class="bg-gray-200 px-2 py-1 rounded"><?php echo $r['default_marks']; ?> marks</span>
                                    <span class="bg-gray-200 px-2 py-1 rounded"><?php echo $r['theory_type'] == 'short' ? 'Short' : 'Long'; ?></span>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </section>
                <?php endif; ?>

                <!-- CTA Section -->
                <section class="p-8 bg-gradient-to-r from-indigo-600 to-purple-600 rounded-xl text-white text-center">
                    <h3 class="text-2xl font-bold mb-4">Practice More Questions</h3>
                    <p class="mb-6 text-indigo-100">Master your PSC exam preparation with thousands of questions</p>
                    <div class="flex flex-wrap justify-center gap-4">
                        <a href="<?php echo app_base_url('quiz/practice'); ?>" 
                           class="bg-white text-indigo-600 px-6 py-3 rounded-lg font-bold hover:bg-indigo-50 transition">
                            <i class="fas fa-play-circle"></i> Take Mock Test
                        </a>
                        <a href="<?php echo app_base_url('admin/quiz/questions'); ?>" 
                           class="bg-indigo-500 text-white px-6 py-3 rounded-lg font-bold hover:bg-indigo-400 transition">
                            <i class="fas fa-database"></i> View All Questions
                        </a>
                    </div>
                </section>

            </div>
        </article>

        <!-- Views Counter -->
        <div class="mt-4 text-center text-sm text-gray-500">
            <i class="fas fa-eye"></i> <?php echo number_format($question['view_count']); ?> views
        </div>

    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white mt-16 py-8">
        <div class="max-w-4xl mx-auto px-4 text-center">
            <p>&copy; <?php echo date('Y'); ?> PSC Exam Prep. All rights reserved.</p>
        </div>
    </footer>

</body>
</html>
