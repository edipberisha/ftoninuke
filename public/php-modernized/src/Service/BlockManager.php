<?php

declare(strict_types=1);

namespace FtoniNuke\Service;

use FtoniNuke\Repository\BlockRepository;
use FtoniNuke\Contract\WidgetInterface;
use RuntimeException;

/**
 * BlockManager - Orchestrates widget rendering and caching.
 */
class BlockManager
{
    private BlockRepository $blockRepo;
    private string $cacheDir;
    private string $widgetsDir;

    public function __construct(BlockRepository $blockRepo)
    {
        $this->blockRepo = $blockRepo;
        $this->cacheDir = __DIR__ . '/../../cache/blocks';
        $this->widgetsDir = __DIR__ . '/../Widgets';

        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0755, true);
        }
    }

    /**
     * Discovery: Scan widgets folder for available plugins
     */
    public function getAvailableWidgets(): array
    {
        $widgets = [];
        $dirs = array_filter(glob($this->widgetsDir . '/*'), 'is_dir');

        foreach ($dirs as $dir) {
            $slug = basename($dir);
            if (file_exists($dir . '/Widget.php')) {
                $widgets[] = [
                    'slug' => $slug,
                    'name' => str_replace('_', ' ', $slug),
                    'path' => $dir
                ];
            }
        }
        return $widgets;
    }

    /**
     * Render blocks for a specific position
     */
    public function renderPosition(string $position): string
    {
        $blocks = $this->blockRepo->getActiveByPosition($position);
        $output = "";

        foreach ($blocks as $blockData) {
            $output .= $this->renderBlock($blockData);
        }

        return $output;
    }

    /**
     * Render a single block with caching logic
     */
    public function renderBlock(array $blockData): string
    {
        $cacheKey = "block_" . $blockData['bid'] . ".html";
        
        // 1. Try to load from cache
        if ($content = $this->getCache($cacheKey)) {
            return $this->wrap($blockData['title'], $content);
        }

        // 2. Instantiate and render Widget
        $content = $this->executeBlockLogic($blockData);

        // 3. Save to cache
        $this->setCache($cacheKey, $content, 3600); 

        return $this->wrap($blockData['title'], $content);
    }

    /**
     * Logic Execution Engine
     */
    private function executeBlockLogic(array $data): string
    {
        // Settings are stored as JSON in legacy 'content' or a new 'settings' field
        $settings = json_decode($data['content'] ?? '{}', true) ?: [];

        // Modern Widget Check (Convention: FtoniNuke\Widgets\{Slug}\Widget)
        if (!empty($data['blockfile']) && strpos($data['blockfile'], 'Widget.php') !== false) {
            $slug = dirname($data['blockfile']); // e.g. "LatestNews"
            $className = "\\FtoniNuke\\Widgets\\{$slug}\\Widget";
            
            if (class_exists($className)) {
                // Dependency Injection would handle this in a real app
                // Here we manually instantiate for simplicity
                $widget = new $className(new \FtoniNuke\Repository\NewsRepository());
                if ($widget instanceof WidgetInterface) {
                    $widget->setData($data);
                    return $widget->render($settings);
                }
            }
        }

        // Fallback for Legacy file-blocks
        if (!empty($data['blockfile']) && file_exists(__DIR__ . "/../../blocks/" . basename($data['blockfile']))) {
            ob_start();
            include __DIR__ . "/../../blocks/" . basename($data['blockfile']);
            return ob_get_clean();
        }

        return (string)($data['content'] ?? '');
    }

    private function getCache(string $key): ?string
    {
        $file = $this->cacheDir . '/' . $key;
        if (file_exists($file) && (time() - filemtime($file) < 3600)) {
            return file_get_contents($file);
        }
        return null;
    }

    private function setCache(string $key, string $content, int $ttl): void
    {
        file_put_contents($this->cacheDir . '/' . $key, $content);
    }
}
