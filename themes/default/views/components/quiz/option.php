<?php
// Props: $id, $text, $label (A, B, C, D)
?>
<div x-data="{ selected: false }" 
     @click="selected = !selected"
     :class="{ 'border-primary bg-primary/10': selected, 'border-white/10 hover:border-white/30 hover:bg-white/5': !selected }"
     class="glass cursor-pointer rounded-lg p-4 border transition-all duration-200 flex items-center group mb-3">
    
    <div :class="{ 'bg-primary text-white border-primary': selected, 'text-gray-400 border-gray-600 group-hover:border-gray-400': !selected }"
         class="w-8 h-8 rounded-full border-2 flex items-center justify-center text-sm font-bold mr-4 transition-all">
        <?= $label ?>
    </div>
    
    <div class="text-gray-200 font-medium group-hover:text-white transition-colors">
        <?= $text ?>
    </div>
    
    <div x-show="selected" x-transition.scale class="ml-auto text-primary">
        <i class="fas fa-check-circle text-xl"></i>
    </div>
</div>
