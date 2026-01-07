<?php include_once __DIR__ . '/../partials/header.php'; ?>

<!-- Load Tailwind CSS -->
<link rel="stylesheet" href="<?php echo app_base_url('themes/default/assets/css/quiz.min.css?v=' . time()); ?>">

<div class="bg-background min-h-screen text-white font-sans pb-20">
    
    <!-- Hero Section -->
    <div class="relative pt-24 pb-16 lg:pt-32 lg:pb-24 overflow-hidden">
        <!-- Background Effects -->
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none">
            <div class="absolute top-[-10%] right-[-5%] w-[500px] h-[500px] bg-primary/20 rounded-full blur-[100px] animate-blob"></div>
            <div class="absolute bottom-[-10%] left-[-5%] w-[500px] h-[500px] bg-secondary/20 rounded-full blur-[100px] animate-blob animation-delay-2000"></div>
        </div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center z-10">
            <h1 class="text-5xl md:text-7xl font-black mb-6 tracking-tight animate-fade-in-up">
                <span class="block text-white mb-2">Practice Exams &</span>
                <span class="bg-gradient-to-r from-primary via-accent to-secondary bg-clip-text text-transparent">Mock Tests</span>
            </h1>
            <p class="mt-4 max-w-2xl mx-auto text-xl text-gray-400 animate-fade-in-up animation-delay-100">
                Choose your engineering discipline and start practicing with our comprehensive question bank.
                Track your progress and get exam-ready.
            </p>
        </div>
    </div>

    <!-- Categories Grid -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($categories as $index => $category): ?>
                <a href="<?php echo app_base_url('exams/category/' . $category['slug']); ?>" 
                   class="group glass-card p-1 rounded-3xl hover:-translate-y-2 transition-all duration-300 block animate-fade-in-up"
                   style="animation-delay: <?php echo ($index * 100) + 200; ?>ms">
                    
                    <div class="bg-surface/50 rounded-[20px] p-8 h-full border border-white/5 group-hover:bg-surface/80 transition-colors relative overflow-hidden">
                        
                        <!-- Hover Glow -->
                        <div class="absolute inset-0 bg-gradient-to-br from-primary/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>

                        <div class="relative z-10">
                            <div class="flex items-center justify-between mb-8">
                                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-primary to-accent flex items-center justify-center text-white shadow-lg shadow-primary/25 group-hover:scale-110 transition-transform duration-300">
                                    <i class="fas fa-graduation-cap text-2xl"></i>
                                </div>
                                <span class="bg-white/10 border border-white/10 text-white text-xs font-bold px-3 py-1.5 rounded-full flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-400"></span>
                                    <?php echo number_format($category['question_count']); ?> Questions
                                </span>
                            </div>
                            
                            <h3 class="text-2xl font-bold text-white mb-3 group-hover:text-primary transition-colors">
                                <?php echo htmlspecialchars($category['title']); ?>
                            </h3>
                            
                            <p class="text-gray-400 text-sm mb-8 line-clamp-2 min-h-[40px]">
                                <?php echo htmlspecialchars($category['description'] ?? 'Comprehensive study materials and practice sets.'); ?>
                            </p>
                            
                            <div class="flex items-center text-white font-bold text-sm group-hover:text-accent transition-colors">
                                Start Practice <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                            </div>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../partials/footer.php'; ?>
