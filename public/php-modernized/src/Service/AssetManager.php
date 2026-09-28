<?php

declare(strict_types=1);

namespace FtoniNuke\Service;

/**
 * AssetManager - Manages CSS and JS files for the theme.
 */
class AssetManager
{
    private array $styles = [];
    private array $scripts = [];

    /**
     * Add a CSS file
     */
    public function addStyle(string $handle, string $src, array $deps = [], string $ver = '1.0'): void
    {
        $this->styles[$handle] = ['src' => $src, 'ver' => $ver];
    }

    /**
     * Add a JS file
     */
    public function addScript(string $handle, string $src, array $deps = [], string $ver = '1.0', bool $inFooter = true): void
    {
        $this->scripts[$handle] = ['src' => $src, 'ver' => $ver, 'footer' => $inFooter];
    }

    /**
     * Render style tags
     */
    public function renderStyles(): string
    {
        $output = "";
        foreach ($this->styles as $style) {
            $output .= "<link rel=\"stylesheet\" href=\"{$style['src']}?v={$style['ver']}\">\n";
        }
        return $output;
    }

    /**
     * Render script tags
     */
    public function renderScripts(bool $footer = true): string
    {
        $output = "";
        foreach ($this->scripts as $script) {
            if ($script['footer'] === $footer) {
                $output .= "<script src=\"{$script['src']}?v={$script['ver']}\"></script>\n";
            }
        }
        return $output;
    }
}
