<?php

declare(strict_types=1);

namespace FtoniNuke\Widgets\LatestNews;

use FtoniNuke\Contract\WidgetInterface;
use FtoniNuke\Repository\NewsRepository;

/**
 * LatestNewsWidget - Displays recent news units with dynamic count.
 */
class Widget implements WidgetInterface
{
    private NewsRepository $newsRepo;
    private array $data = [];

    public function __construct(NewsRepository $newsRepo)
    {
        $this->newsRepo = $newsRepo;
    }

    public function setData(array $data): void
    {
        $this->data = $data;
    }

    public function render(array $settings = []): string
    {
        $count = (int)($settings['count'] ?? 5);
        $stories = $this->newsRepo->getLatest($count);
        
        ob_start();
        include __DIR__ . '/view.html.php';
        return ob_get_clean();
    }

    public function getTitle(): string
    {
        return $this->data['title'] ?? 'Latest News';
    }

    public function isCacheable(): bool
    {
        return true;
    }

    public function getCacheTtl(): int
    {
        return 300; // 5 minutes
    }
}
