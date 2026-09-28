<?php

/**
 * Global Template Helpers - FtoniNuke v1.1
 * Inspired by WordPress, providing a friendly API for theme developers.
 */

use FtoniNuke\Service\ThemeManager;

/**
 * Singleton-like access to ThemeManager (would be managed by Service Container in production)
 */
function get_theme_manager(): ThemeManager {
    static $manager = null;
    if ($manager === null) {
        // This is a dirty shortcut for demonstration. 
        // In reality, this would be injected via DI.
        global $blockManager, $assetManager; 
        $manager = new ThemeManager($blockManager, $assetManager);
    }
    return $manager;
}

function get_header() {
    get_theme_manager()->get_header();
}

function get_footer() {
    get_theme_manager()->get_footer();
}

function the_content($content) {
    get_theme_manager()->the_content($content);
}

function get_sidebar($position = 'l') {
    // Access global or injected BlockManager
    global $blockManager;
    echo $blockManager->renderPosition($position);
}

function wp_head() {
    echo get_theme_manager()->getAssetManager()->renderStyles();
}

function wp_footer() {
    echo get_theme_manager()->getAssetManager()->renderScripts();
}
