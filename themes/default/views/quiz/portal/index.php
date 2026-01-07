<?php
/**
 * PREMIUM QUIZ PORTAL - SAAS EDITION (Refactored)
 * Stack: PHP + Tailwind CSS + Alpine.js
 */
?>

<!-- Load Tailwind CSS for Quiz Section -->
<link rel="stylesheet" href="<?php echo app_base_url('themes/default/assets/css/quiz.min.css?v=' . time()); ?>">

<!-- Load Alpine.js -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>

<div class="min-h-screen bg-background text-white pb-20" x-data="quizPortal()">
    
    <!-- Hero Section -->
    <div class="relative overflow-hidden bg-gradient-to-br from-background via-background to-primary/20 pt-20 pb-32">
        <!-- Animated Background Blobs -->
        <div class="absolute top-0 right-0 -mr-20 -mt-20 w-96 h-96 rounded-full bg-primary/20 blur-3xl opacity-50 animate-pulse"></div>
        <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-80 h-80 rounded-full bg-secondary/20 blur-3xl opacity-50 animate-pulse animation-delay-2000"></div>

        <div class="container mx-auto px-6 relative z-10 text-center">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/5 border border-white/10 backdrop-blur-sm mb-8 animate-fade-in-up">
                <span class="relative flex h-3 w-3">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                  <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                </span>
                <span class="text-sm font-medium text-gray-300">50,000+ Active Learners</span>
            </div>

            <h1 class="text-5xl md:text-7xl font-black mb-6 leading-tight animate-fade-in-up animation-delay-100">
                Master Your <br/>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary via-accent to-secondary">Engineering Dreams</span>
            </h1>

            <p class="text-xl text-gray-400 max-w-2xl mx-auto mb-10 animate-fade-in-up animation-delay-200">
                AI-powered mock tests, real-time analytics, and gamified learning for Loksewa, License & Entrance exams.
            </p>

            <!-- Search Bar -->
            <div class="max-w-xl mx-auto relative group animate-fade-in-up animation-delay-300">
                <div class="absolute -inset-1 bg-gradient-to-r from-primary to-secondary rounded-xl blur opacity-25 group-hover:opacity-50 transition duration-200"></div>
                <div class="relative flex items-center bg-black/80 backdrop-blur-xl border border-white/10 rounded-xl p-2">
                    <i class="fas fa-search text-gray-400 ml-4 text-lg"></i>
                    <input 
                        type="text" 
                        x-model="searchQuery"
                        placeholder="Search for exams, topics, or categories..." 
                        class="w-full bg-transparent border-none text-white px-4 py-3 focus:ring-0 placeholder-gray-500 text-lg"
                    >
                    <button class="bg-primary hover:bg-primary/90 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                        Search
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="container mx-auto px-6 -mt-20 relative z-20">
        
        <!-- Filters (Optional - can be expanded) -->
        <div class="flex flex-wrap justify-center gap-4 mb-12">
            <button @click="filter = 'all'" :class="{'bg-primary text-white': filter === 'all', 'bg-white/5 text-gray-400 hover:bg-white/10': filter !== 'all'}" class="px-6 py-2 rounded-full backdrop-blur-md border border-white/5 transition-all">All</button>
            <button @click="filter = 'mock_test'" :class="{'bg-red-500/20 text-red-400 border-red-500/30': filter === 'mock_test', 'bg-white/5 text-gray-400 hover:bg-white/10': filter !== 'mock_test'}" class="px-6 py-2 rounded-full backdrop-blur-md border border-white/5 transition-all">Mock Tests</button>
            <button @click="filter = 'practice_set'" :class="{'bg-green-500/20 text-green-400 border-green-500/30': filter === 'practice_set', 'bg-white/5 text-gray-400 hover:bg-white/10': filter !== 'practice_set'}" class="px-6 py-2 rounded-full backdrop-blur-md border border-white/5 transition-all">Practice Sets</button>
        </div>

        <!-- Categories / Exams Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Dynamic PHP Loop interacting with Alpine for filtering -->
            <?php if (!empty($exams)): ?>
                <?php foreach ($exams as $exam): ?>
                    <?php 
                        // Prepare variables for the component
                        $title = $exam['title'];
                        $description = $exam['description'];
                        $link = app_base_url('quiz/overview/' . $exam['slug']);
                        $questions_count = $exam['question_count'];
                        $category = ucfirst(str_replace('_', ' ', $exam['type']));
                        $icon = $exam['icon'] ?? 'fas fa-graduation-cap';
                    ?>
                    <div x-show="shouldShow('<?php echo $exam['type']; ?>', '<?php echo addslashes($exam['title']); ?>')" 
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 transform scale-95"
                         x-transition:enter-end="opacity-100 transform scale-100"
                         class="h-full">
                        <?php include dirname(__DIR__) . '/../components/quiz/card.php'; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-span-full text-center py-20">
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-white/5 mb-6">
                        <i class="fas fa-inbox text-4xl text-gray-600"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-300 mb-2">No Exams Found</h3>
                    <p class="text-gray-500">Check back later for new content.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('quizPortal', () => ({
        searchQuery: '',
        filter: 'all',

        shouldShow(type, title) {
            // Filter by Type
            if (this.filter !== 'all' && type !== this.filter) return false;
            
            // Filter by Search
            if (this.searchQuery === '') return true;
            return title.toLowerCase().includes(this.searchQuery.toLowerCase());
        }
    }));
});
</script>
