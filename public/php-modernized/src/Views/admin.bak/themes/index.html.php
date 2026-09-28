<?php
/**
 * Admin Theme Store UI
 */
?>
<div class="admin-themes bg-gray-950 min-h-screen text-gray-300 p-8 font-mono">
    <header class="flex justify-between items-center mb-12 pb-6 border-b border-gray-800">
        <div>
            <h1 class="text-2xl font-black text-white italic uppercase tracking-tight">Theme <span class="text-blue-600">Forge</span></h1>
            <p class="text-xs text-gray-500 mt-1">Select and hot-swap the visual identity of FtoniNuke.</p>
        </div>
        <button class="bg-gray-800 hover:bg-gray-700 text-[10px] px-4 py-2 rounded uppercase tracking-widest font-bold">
            Scan Directory
        </button>
    </header>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
        <?php foreach ($themes as $theme): ?>
            <div class="theme-card group relative bg-gray-900 border <?= $theme['active'] ? 'border-blue-600 shadow-[0_0_20px_rgba(37,99,235,0.1)]' : 'border-gray-800' ?> rounded-2xl overflow-hidden transition-all duration-300">
                
                <!-- Active Badge -->
                <?php if ($theme['active']): ?>
                    <div class="absolute top-4 right-4 z-10">
                        <span class="px-3 py-1 bg-blue-600 text-white text-[9px] font-bold uppercase rounded-full shadow-lg">Active</span>
                    </div>
                <?php endif; ?>

                <div class="aspect-[16/10] bg-black/40 overflow-hidden relative">
                    <div class="absolute inset-0 flex items-center justify-center opacity-20 group-hover:opacity-40 transition-opacity">
                         <i data-lucide="image" class="w-12 h-12"></i>
                    </div>
                </div>

                <div class="p-6">
                    <div class="flex justify-between items-start mb-4">
                        <h3 class="text-lg font-bold text-white group-hover:text-blue-400 transition-colors">
                            <?= htmlspecialchars($theme['name']) ?>
                        </h3>
                        <span class="text-[10px] text-gray-600">v<?= $theme['version'] ?></span>
                    </div>

                    <p class="text-xs text-gray-500 line-clamp-2 mb-6 leading-relaxed">
                        <?= htmlspecialchars($theme['description']) ?>
                    </p>

                    <div class="flex items-center justify-between mt-auto">
                        <span class="text-[10px] text-gray-600">By <span class="text-gray-400"><?= htmlspecialchars($theme['author']) ?></span></span>
                        
                        <?php if (!$theme['active']): ?>
                            <button class="text-[10px] font-bold text-blue-500 hover:text-blue-400 uppercase tracking-widest flex items-center gap-1 group/btn">
                                Activate 
                                <i data-lucide="arrow-right" class="w-3 h-3 transform group-hover/btn:translate-x-1 transition-transform"></i>
                            </button>
                        <?php else: ?>
                            <button class="text-[10px] font-bold text-gray-700 uppercase tracking-widest cursor-default">
                                Current
                            </button>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Hover Overlay for Customize -->
                <?php if ($theme['active']): ?>
                    <div class="absolute bottom-6 right-6 opacity-0 group-hover:opacity-100 transition-opacity">
                         <button class="p-2 bg-blue-600 rounded-lg text-white shadow-xl">
                            <i data-lucide="pen-tool" class="w-4 h-4"></i>
                         </button>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>

        <!-- Theme Builder Card -->
        <div class="border-2 border-dashed border-gray-800 rounded-2xl p-8 flex flex-col items-center justify-center text-center opacity-40 hover:opacity-100 transition-opacity cursor-pointer">
            <div class="w-12 h-12 bg-gray-800 rounded-full flex items-center justify-center mb-4">
                <i data-lucide="plus" class="w-6 h-6"></i>
            </div>
            <h4 class="text-xs font-bold uppercase tracking-widest">Create New Theme</h4>
            <p class="text-[10px] text-gray-600 mt-1">Start from v1.1 Boilerplate</p>
        </div>
    </div>
</div>

<script>
    lucide.createIcons();
</script>
