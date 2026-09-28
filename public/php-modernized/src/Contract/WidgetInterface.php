<?php

declare(strict_types=1);

namespace FtoniNuke\Contract;

/**
 * WidgetInterface - Defines the structure for modern blocks (widgets).
 */
interface WidgetInterface
{
    /**
     * Set the block data from database
     */
    public function setData(array $data): void;

    /**
     * Render the block content HTML
     * @param array $settings Dynamic configuration for this widget instance
     */
    public function render(array $settings = []): string;

    /**
     * Get the block title
     */
    public function getTitle(): string;

    /**
     * Should this block be cached?
     */
    public function isCacheable(): bool;

    /**
     * Cache TTL in seconds
     */
    public function getCacheTtl(): int;
}
