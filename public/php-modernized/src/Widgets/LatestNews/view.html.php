<?php
/**
 * View for Latest News Widget
 */
?>
<ul class="space-y-3">
    <?php foreach ($stories as $story): ?>
        <li class="group flex items-center gap-3">
            <div class="w-1.5 h-1.5 rounded-full bg-blue-600 opacity-40 group-hover:opacity-100 transition-opacity"></div>
            <a href="/news/<?= $story['sid'] ?>" class="text-sm hover:text-blue-600 transition-colors truncate">
                <?= htmlspecialchars($story['title']) ?>
            </a>
        </li>
    <?php endforeach; ?>
</ul>
