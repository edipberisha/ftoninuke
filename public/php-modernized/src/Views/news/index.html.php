<?php
/**
 * Modern News Index View
 * 
 * Replaces modules.php?name=News with a clean, card-based Grid layout.
 */
?>
<section class="news-container max-w-7xl mx-auto p-6 md:p-12">
    <header class="mb-12">
        <h2 class="text-sm uppercase tracking-widest text-blue-600 font-bold mb-2">Latest Insights</h2>
        <h1 class="text-4xl md:text-5xl font-black text-gray-900 dark:text-white tracking-tight">
            Nuke News <span class="text-blue-600">Reimagined</span>
        </h1>
    </header>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <?php foreach ($stories as $story): ?>
            <article class="news-card group flex flex-col bg-white dark:bg-gray-800 rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 dark:border-gray-700">
                <div class="card-image-wrap relative aspect-video overflow-hidden">
                    <img src="<?= htmlspecialchars($story['image']) ?>" 
                         alt="<?= htmlspecialchars($story['title']) ?>"
                         class="object-cover w-full h-full transform group-hover:scale-105 transition-transform duration-500"
                    >
                    <div class="absolute top-4 left-4">
                        <span class="px-3 py-1 bg-black/50 backdrop-blur-md text-white text-[10px] font-bold uppercase tracking-wider rounded-full">
                            <?= htmlspecialchars($story['category']) ?>
                        </span>
                    </div>
                </div>

                <div class="p-6 flex flex-col flex-grow">
                    <div class="flex items-center gap-3 mb-3 text-[10px] text-gray-400 font-mono uppercase">
                        <span><?= $story['date'] ?></span>
                        <span class="w-1 h-1 bg-gray-300 rounded-full"></span>
                        <span><?= $story['hits'] ?> Views</span>
                    </div>

                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3 line-clamp-2 leading-tight group-hover:text-blue-600 transition-colors">
                        <a href="/news/<?= $story['slug'] ?>-<?= $story['id'] ?>">
                            <?= htmlspecialchars($story['title']) ?>
                        </a>
                    </h3>

                    <div class="text-gray-500 dark:text-gray-400 text-sm line-clamp-3 mb-6 leading-relaxed">
                        <?= strip_tags($story['summary']) ?>
                    </div>

                    <div class="mt-auto pt-6 border-t border-gray-50 dark:border-gray-700 flex items-center justify-between">
                        <span class="text-xs font-medium text-gray-600 dark:text-gray-300">By <?= htmlspecialchars($story['author']) ?></span>
                        <a href="/news/<?= $story['slug'] ?>-<?= $story['id'] ?>" class="text-blue-600 hover:text-blue-700 text-xs font-bold flex items-center gap-1 group">
                            Read More
                            <svg class="w-3 h-3 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>
                        </a>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    </div>

    <!-- Pagination Placeholder -->
    <div class="mt-16 flex justify-center border-t border-gray-100 dark:border-gray-700 pt-8">
        <nav class="flex gap-2">
            <button class="p-2 border rounded hover:bg-gray-50 dark:hover:bg-gray-700">Prev</button>
            <button class="px-4 py-2 bg-blue-600 text-white rounded font-bold">1</button>
            <button class="px-4 py-2 border rounded hover:bg-gray-50 dark:hover:bg-gray-700">2</button>
            <button class="p-2 border rounded hover:bg-gray-50 dark:hover:bg-gray-700">Next</button>
        </nav>
    </div>
</section>
