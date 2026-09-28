<?php
/**
 * Blok render partial
 *
 * Kullanım:
 *   $position = 'l'; require __DIR__ . '/partials/blocks.html.php';
 *   $position = 'r'; require __DIR__ . '/partials/blocks.html.php';
 */

if (!isset($position)) $position = 'r';

$renderer = new \FtoniNuke\Service\BlockRenderer();
echo $renderer->render($position);
?>
