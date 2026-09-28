<?php
/**
 * Modern Block Wrapper Template
 * 
 * Consistent UI shell for all sidebar/center blocks.
 */
?>
<div class="nuke-block mb-8 group">
    <div class="block-header flex items-center justify-between border-b border-gray-100 dark:border-gray-700 pb-3 mb-4">
        <h3 class="text-xs uppercase tracking-[0.2em] font-black text-gray-400 dark:text-gray-500 group-hover:text-blue-600 transition-colors">
            <?= htmlspecialchars($title) ?>
        </h3>
        <div class="h-1 w-8 bg-gray-100 dark:bg-gray-800 rounded-full overflow-hidden">
            <div class="h-full bg-blue-600 w-0 group-hover:w-full transition-all duration-500"></div>
        </div>
    </div>
    
    <div class="block-content text-sm text-gray-600 dark:text-gray-400 leading-relaxed prose dark:prose-invert prose-sm max-w-none">
        <?= $content ?>
    </div>
</div>
