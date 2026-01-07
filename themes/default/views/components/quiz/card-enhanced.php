<?php
// Props: $title, $description, $icon, $link, $questions_count, $category, $badges
?>
<div class="glass-card group relative overflow-hidden">
    <!-- Badges Container (Top Right) -->
    <?php if (!empty($badges)): ?>
    <div class="absolute top-4 right-4 z-20 flex flex-wrap gap-2 justify-end">
        <?php foreach ($badges as $badge): ?>
            <?php
            $colorClasses = [
                'blue' => 'bg-blue-500/20 text-blue-400 border-blue-500/30',
                'red' => 'bg-red-500/20 text-red-400 border-red-500/30',
                'yellow' => 'bg-yellow-500/20 text-yellow-400 border-yellow-500/30',
                'green' => 'bg-green-500/20 text-green-400 border-green-500/30',
                'purple' => 'bg-purple-500/20 text-purple-400 border-purple-500/30',
                'pink' => 'bg-pink-500/20 text-pink-400 border-pink-500/30',
            ];
            $badgeColor = $colorClasses[$badge['color']] ?? 'bg-gray-500/20 text-gray-400 border-gray-500/30';
            ?>
            <span class="px-2 py-1 rounded-full text-xs font-bold border backdrop-blur-sm <?= $badgeColor ?> uppercase tracking-wide">
                <?= $badge['label'] ?>
            </span>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <div class="absolute top-0 right-0 p-4 opacity-50 group-hover:opacity-100 transition-opacity">
        <i class="<?= $icon ?? 'fas fa-question-circle' ?> text-4xl text-white/10 group-hover:text-white/20 transform group-hover:rotate-12 transition-all"></i>
    </div>
    
    <div class="relative z-10">
        <span class="inline-block px-3 py-1 rounded-full text-xs font-medium bg-primary/20 text-primary mb-3 border border-primary/20">
            <?= $category ?? 'General' ?>
        </span>
        
        <h3 class="text-xl font-bold text-white mb-2 group-hover:text-primary transition-colors line-clamp-2">
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
