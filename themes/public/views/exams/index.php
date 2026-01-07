<?php include_once __DIR__ . '/../../layouts/header.php'; ?>

<!-- Exam Hub Hero -->
<div class="relative bg-white pt-24 pb-16 lg:pt-32 lg:pb-24 overflow-hidden">
    <div class="absolute inset-0">
        <div class="absolute inset-y-0 left-0 w-1/2 bg-gray-50"></div>
    </div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h1 class="text-4xl tracking-tight font-extrabold text-gray-900 sm:text-5xl md:text-6xl">
                <span class="block xl:inline">Practice Exams &</span>
                <span class="block text-indigo-600 xl:inline">Mock Tests</span>
            </h1>
            <p class="mt-3 max-w-md mx-auto text-base text-gray-500 sm:text-lg md:mt-5 md:text-xl md:max-w-3xl">
                Choose your engineering discipline and start practicing with our comprehensive question bank.
                Track your progress and get exam-ready.
            </p>
        </div>
    </div>
</div>

<!-- Categories Grid -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-20">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <?php foreach ($categories as $category): ?>
            <a href="<?php echo app_base_url('exams/category/' . $category['slug']); ?>" 
               class="group relative bg-white border border-gray-200 rounded-2xl p-8 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                
                <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-indigo-50 rounded-full opacity-50 blur-xl group-hover:bg-indigo-100 transition"></div>
                
                <div class="flex items-center justify-between mb-6">
                    <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center text-indigo-600">
                        <i class="fas fa-graduation-cap text-xl"></i>
                    </div>
                    <span class="bg-green-100 text-green-800 text-xs font-bold px-3 py-1 rounded-full">
                        <?php echo number_format($category['question_count']); ?> Questions
                    </span>
                </div>
                
                <h3 class="text-xl font-bold text-gray-900 group-hover:text-indigo-600 transition mb-2">
                    <?php echo htmlspecialchars($category['name']); ?>
                </h3>
                
                <p class="text-gray-500 text-sm mb-6">
                    <?php echo htmlspecialchars($category['description'] ?? 'Comprehensive study materials and practice sets.'); ?>
                </p>
                
                <div class="flex items-center text-indigo-600 font-semibold group-hover:text-indigo-700">
                    Start Practice <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</div>

<?php include_once __DIR__ . '/../../layouts/footer.php'; ?>
