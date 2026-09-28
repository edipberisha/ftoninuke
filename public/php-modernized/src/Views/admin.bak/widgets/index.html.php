<?php
/**
 * Admin Layout & Widget Manager UI
 */
?>
<div class="widget-manager-admin p-8 bg-gray-950 min-h-screen text-gray-300 font-mono">
    <header class="flex justify-between items-center mb-10 pb-6 border-b border-gray-800">
        <div>
            <h1 class="text-2xl font-black text-white italic uppercase tracking-tight">Layout <span class="text-blue-600">Architect</span></h1>
            <p class="text-xs text-gray-500 mt-1">Drag, drop and configure your theme's active widget areas.</p>
        </div>
        <div class="flex gap-4">
             <button class="bg-gray-800 hover:bg-gray-700 text-[10px] px-4 py-2 rounded uppercase tracking-widest font-bold">
                 Global Settings
             </button>
        </div>
    </header>

    <div class="grid grid-cols-1 xl:grid-cols-4 gap-10">
        
        <!-- Available Widgets Sidebar -->
        <aside class="xl:col-span-1 space-y-6">
            <h2 class="text-xs font-black uppercase tracking-[0.2em] text-gray-500 mb-4">Available Widgets</h2>
            <div class="space-y-3">
                <?php foreach ($availableWidgets as $widget): ?>
                    <div class="p-4 bg-gray-900 border border-gray-800 rounded-xl cursor-grab hover:border-blue-500/50 group transition-all">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold text-gray-400 group-hover:text-white"><?= htmlspecialchars($widget['name']) ?></span>
                            <i data-lucide="grip-vertical" class="w-4 h-4 text-gray-700"></i>
                        </div>
                        <p class="text-[10px] text-gray-600">Source: /src/Widgets/<?= $widget['slug'] ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="mt-10 p-6 bg-blue-500/5 border border-blue-500/20 rounded-2xl">
                 <h4 class="text-[11px] font-bold text-blue-400 mb-2 uppercase">Pro Tip</h4>
                 <p class="text-[10px] text-gray-500 leading-relaxed">
                    Create a folder in <code>src/Widgets</code> with a <code>Widget.php</code> file to register a new plugin instantly.
                 </p>
            </div>
        </aside>

        <!-- Dynamic Layout Grid -->
        <main class="xl:col-span-3">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                
                <!-- Left Sidebar Area -->
                <div class="space-y-6">
                   <div class="text-[10px] uppercase tracking-widest text-gray-600 font-bold border-b border-gray-800 pb-2">Left Sidebar</div>
                   <div class="min-h-[400px] border-2 border-dashed border-gray-800/50 rounded-2xl p-4 bg-gray-900/20 space-y-4">
                        <?php foreach ($layout['l'] as $block): ?>
                            <div class="p-4 bg-gray-900 border border-gray-800 rounded-xl shadow-lg relative group">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-xs font-bold text-blue-500"><?= htmlspecialchars($block['title']) ?></span>
                                    <div class="flex gap-2">
                                        <button class="text-gray-600 hover:text-white"><i data-lucide="settings" class="w-3 h-3"></i></button>
                                        <button class="text-gray-600 hover:text-red-500"><i data-lucide="x" class="w-3 h-3"></i></button>
                                    </div>
                                </div>
                                <div class="text-[9px] text-gray-600"><?= $block['blockfile'] ?: 'Static Content' ?></div>
                            </div>
                        <?php endforeach; ?>
                   </div>
                </div>

                <!-- Center Area -->
                <div class="space-y-6">
                   <div class="text-[10px] uppercase tracking-widest text-gray-600 font-bold border-b border-gray-800 pb-2">Main Content Area</div>
                   <div class="min-h-[400px] border-2 border-dashed border-gray-800/50 rounded-2xl p-4 bg-gray-900/20 space-y-4">
                        <?php foreach ($layout['c'] as $block): ?>
                            <div class="p-4 bg-gray-900 border border-gray-800 rounded-xl">
                                <span class="text-xs font-bold text-white"><?= htmlspecialchars($block['title']) ?></span>
                            </div>
                        <?php endforeach; ?>
                   </div>
                </div>

                <!-- Right Sidebar Area -->
                <div class="space-y-6">
                   <div class="text-[10px] uppercase tracking-widest text-gray-600 font-bold border-b border-gray-800 pb-2">Right Sidebar</div>
                   <div class="min-h-[400px] border-2 border-dashed border-gray-800/50 rounded-2xl p-4 bg-gray-900/20 space-y-4 text-center flex flex-col items-center justify-center">
                        <?php if (empty($layout['r'])): ?>
                            <i data-lucide="plus-circle" class="w-8 h-8 text-gray-800 mb-2"></i>
                            <span class="text-[10px] text-gray-700">Drop widgets here</span>
                        <?php endif; ?>
                        <?php foreach ($layout['r'] as $block): ?>
                            <div class="p-4 bg-gray-900 border border-gray-800 rounded-xl w-full text-left">
                                <span class="text-xs font-bold text-white"><?= htmlspecialchars($block['title']) ?></span>
                            </div>
                        <?php endforeach; ?>
                   </div>
                </div>

            </div>
        </main>
    </div>
</div>

<script>
    lucide.createIcons();
</script>
