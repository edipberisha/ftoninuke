<?php

declare(strict_types=1);

namespace FtoniNuke\Service;

use FtoniNuke\Core\Database;
use FtoniNuke\Service\BlockManager;
use FtoniNuke\Service\AssetManager;

/**
 * ThemeManager v2 - Modern Dynamic Theme Engine.
 * Scans directories and handles template hierarchy.
 */
class ThemeManager
{
    private BlockManager $blockManager;
    private AssetManager $assetManager;
    private string $activeTheme;
    private string $themesDir;
    private array $viewData = [];
    private bool $showLeftSidebar = true;
    private bool $showRightSidebar = true;

    public function __construct(BlockManager $blockManager, AssetManager $assetManager)
    {
        $this->blockManager = $blockManager;
        $this->assetManager = $assetManager;
        $this->themesDir = __DIR__ . '/../../public/themes';
        
        // Get active theme from DB (Fall back to ftoni_modern)
        $this->activeTheme = $this->getActiveThemeName();
        
        // Default Assets
        $this->assetManager->addStyle('google-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap');
    }

    /**
     * Get active theme name from database
     */
    private function getActiveThemeName(): string
    {
        try {
            $db = Database::getInstance();
            // Assuming table nuke_config exists with theme name
            $config = $db->fetch("SELECT theme FROM " . ($_ENV['DB_PREFIX'] ?? 'nuke_') . "config LIMIT 1");
            return $config['theme'] ?? 'ftoni_modern';
        } catch (\Exception $e) {
            return 'ftoni_modern';
        }
    }

    /**
     * List all available themes by scanning the directory
     */
    public function getAvailableThemes(): array
    {
        $themes = [];
        $dirs = array_filter(glob($this->themesDir . '/*'), 'is_dir');

        foreach ($dirs as $dir) {
            $slug = basename($dir);
            $jsonFile = $dir . '/theme.json';
            if (file_exists($jsonFile)) {
                $meta = json_decode(file_get_contents($jsonFile), true);
                if ($meta) {
                    $meta['slug'] = $slug;
                    $meta['active'] = ($slug === $this->activeTheme);
                    $themes[] = $meta;
                }
            }
        }

        return $themes;
    }

    /**
     * Main rendering method - PSR-style layout injection
     */
    public function render(string $viewFile, array $data = []): void
    {
        // 1. Merge data
        $data = array_merge($this->viewData, $data);
        extract($data);

        // 2. Local template scope
        $themePath = "{$this->themesDir}/{$this->activeTheme}";
        
        // 3. Render content
        ob_start();
        $viewPath = __DIR__ . "/../Views/{$viewFile}.php";
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            echo "View [{$viewFile}] not found.";
        }
        $content = ob_get_clean();

        // 4. Render Layout from Theme folder
        $layoutFile = "{$themePath}/layout.php";
        
        // Fallback to core layout if theme doesn't provide one
        if (!file_exists($layoutFile)) {
            $layoutFile = __DIR__ . '/../Views/layouts/main.layout.php';
        }

        // Prepare sidebars for layout
        $leftSidebar = $this->showLeftSidebar ? $this->blockManager->renderPosition('l') : "";
        $rightSidebar = $this->showRightSidebar ? $this->blockManager->renderPosition('r') : "";

        include $layoutFile;
    }

    // --- Template Tags (The Loop Helpers) ---
    
    public function get_header(): void
    {
        $headerFile = "{$this->themesDir}/{$this->activeTheme}/header.php";
        if (file_exists($headerFile)) {
            include $headerFile;
        } else {
            // Core fallback
            include __DIR__ . '/../Views/layouts/header.php';
        }
    }

    public function get_footer(): void
    {
        $footerFile = "{$this->themesDir}/{$this->activeTheme}/footer.php";
        if (file_exists($footerFile)) {
            include $footerFile;
        } else {
            // Core fallback
            include __DIR__ . '/../Views/layouts/footer.php';
        }
    }

    public function the_content(string $content): void
    {
        echo $content;
    }

    public function getAssetManager(): AssetManager
    {
        return $this->assetManager;
    }
}
