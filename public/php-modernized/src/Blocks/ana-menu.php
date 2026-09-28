<?php
$menuRepo = new \FtoniNuke\Repository\MenuRepository();
$menu = $menuRepo->allHierarchical();
?>
<ul class="block-menu">
    <?php foreach ($menu as $item): ?>
        <li>
            <a href="<?= htmlspecialchars($item['url']) ?>"
               target="<?= htmlspecialchars($item['target'] ?? '_self') ?>">
                <?= htmlspecialchars($item['title']) ?>
            </a>
            <?php if (!empty($item['children'])): ?>
                <ul>
                    <?php foreach ($item['children'] as $child): ?>
                        <li>
                            <a href="<?= htmlspecialchars($child['url']) ?>">
                                └ <?= htmlspecialchars($child['title']) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </li>
    <?php endforeach; ?>
</ul>
