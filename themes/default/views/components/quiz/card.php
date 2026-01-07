<?php
// Props: $title, $description, $icon, $link, $questions_count, $category
?>
<div class="glass-card group relative overflow-hidden">
    <div class="absolute top-0 right-0 p-4 opacity-50 group-hover:opacity-100 transition-opacity">
        <i class="<?= $icon ?? 'fas fa-question-circle' ?> text-4xl text-white/10 group-hover:text-white/20 transform group-hover:rotate-12 transition-all"></i>
    </div>
    
    <div class="relative z-10">
        <span class="inline-block px-3 py-1 rounded-full text-xs font-medium bg-primary/20 text-primary mb-3 border border-primary/20">
            <?= $category ?? 'General' ?>
        </span>
        
        <h3 class="text-xl font-bold text-white mb-2 group-hover:text-primary transition-colors">
            <?= $title ?>
        </h3>
        
        <p class="text-gray-400 text-sm mb-4 line-clamp-2">
            <?= $description ?>
        </p>
        
        <div class="flex items-center justify-between mt-4">
            <span class="text-xs text-gray-500 flex items-center">
                <i class="fas fa-list-ul mr-2"></i> <?= $questions_count ?? 0 ?> Questions
            </span>
            
            <a href="<?= $link ?>" class="btn-primary text-sm transform translate-y-2 opacity-0 group-hover:translate-y-0 group-hover:opacity-100 transition-all duration-300">
                Start Quiz <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
    </div>
    
    <!-- Hover Glow Effect -->
    <div class="absolute -bottom-10 -right-10 w-32 h-32 bg-primary/20 rounded-full blur-3xl group-hover:bg-primary/30 transition-all duration-500"></div>
</div>
