<?php
// Props: $title, $description, $icon, $link, $questions_count, $category, $badges
?>
<div x-data="{ loaded: false }" x-init="setTimeout(() => loaded = true, 300)" class="h-full">
    <div x-show="!loaded" class="glass-panel p-6 rounded-2xl h-full animate-pulse border border-white/5">
        <div class="w-16 h-6 bg-white/10 rounded-full mb-4"></div>
        <div class="w-3/4 h-6 bg-white/10 rounded-lg mb-3"></div>
        <div class="w-full h-10 bg-white/5 rounded-xl mt-auto"></div>
    </div>

    <div x-show="loaded"
        x-transition:enter="transition ease-out duration-500"
        x-transition:enter-start="opacity-0 translate-y-4"
        x-transition:enter-end="opacity-100 translate-y-0"
        class="glass-panel p-6 rounded-2xl h-full group hover:bg-white/[0.04] transition-all border border-white/5 hover:border-indigo-500/30">

        <div class="flex justify-between items-start mb-4">
            <span class="px-2.5 py-0.5 rounded-md text-xs font-medium bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">
                <?php echo $category ?? 'General'; ?>
            </span>
            <?php if (!empty($badges)): ?>
                <div class="flex gap-1">
                    <?php foreach (array_slice($badges, 0, 2) as $badge): ?>
                        <span class="w-2 h-2 rounded-full bg-<?php echo $badge['color']; ?>-500" title="<?php echo $badge['label']; ?>"></span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <h3 class="text-xl font-bold text-white mb-2 group-hover:text-indigo-400 transition-colors"><?php echo $title; ?></h3>
        <p class="text-gray-400 text-sm mb-6 line-clamp-2"><?php echo $description; ?></p>

        <div class="flex items-center justify-between mt-auto pt-4 border-t border-white/5">
            <div class="text-xs text-gray-500"><?php echo $questions_count; ?> Qs</div>
            <a href="<?php echo $link; ?>" class="w-8 h-8 rounded-full bg-white/5 flex items-center justify-center text-white group-hover:bg-indigo-600 transition-all">
                <i class="fas fa-arrow-right text-xs"></i>
            </a>
        </div>
    </div>
</div>