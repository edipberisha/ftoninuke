<?php

declare(strict_types=1);

namespace FtoniNuke\Service;

use FtoniNuke\Repository\BlockRepository;

class BlockRenderer
{
    private BlockRepository $repo;

    public function __construct()
    {
        $this->repo = new BlockRepository();
    }

    /**
     * Belirli bir pozisyondaki (l/r/c) blokları render eder
     */
    public function render(string $position): string
    {
        $blocks = $this->repo->byPosition($position);

        if (empty($blocks)) return '';

        $html = '';
        foreach ($blocks as $block) {
            $content = $this->renderBlockContent($block);

            // Sosyal medya bloğu boşsa hiç gösterme
            if ($block['block_type'] === 'social' && trim($content) === '') {
                continue;
            }

            $html .= '<aside class="block block-' . htmlspecialchars($block['block_type']) . '">';
            if (!empty($block['title'])) {
                $html .= '<h3 class="block-title">' . htmlspecialchars($block['title']) . '</h3>';
            }
            $html .= '<div class="block-content">' . $content . '</div>';
            $html .= '</aside>';
        }

        return $html;
    }

    private function renderBlockContent(array $block): string
    {
        switch ($block['block_type']) {
            case 'html':
                return $block['content'] ?? '';

            case 'ad':
                return $this->renderAd($block);

            case 'social':
                return $this->renderSocial($block);

            case 'php':
                return $this->renderPhp($block);

            default:
                return '';
        }
    }

    private function renderAd(array $block): string
    {
        if (empty($block['image'])) return '';

        $img = '<img src="' . htmlspecialchars($block['image']) . '" alt="' . htmlspecialchars($block['title'] ?? 'Reklam') . '" style="width:100%;height:auto;border-radius:8px;display:block">';

        if (!empty($block['link_url'])) {
            return '<a href="' . htmlspecialchars($block['link_url']) . '" target="_blank" rel="noopener nofollow">' . $img . '</a>';
        }

        return $img;
    }

    private function renderSocial(array $block): string
    {
        $config = json_decode($block['config'] ?? '{}', true);
        if (!is_array($config)) return '';

        $networks = [
            'facebook'  => ['📘', 'Facebook',  '#1877f2'],
            'twitter'   => ['𝕏',  'Twitter/X', '#000000'],
            'instagram' => ['📷', 'Instagram', '#e4405f'],
            'youtube'   => ['▶️', 'YouTube',   '#ff0000'],
            'linkedin'  => ['💼', 'LinkedIn',  '#0a66c2'],
            'github'    => ['🐙', 'GitHub',    '#333333'],
            'tiktok'    => ['🎵', 'TikTok',    '#000000'],
            'whatsapp'  => ['💬', 'WhatsApp',  '#25d366'],
            'telegram'  => ['✈️', 'Telegram',  '#0088cc'],
        ];

        $html = '<div class="social-grid">';
        $hasAny = false;

        foreach ($networks as $key => $meta) {
            $url = trim($config[$key] ?? '');
            if ($url === '') continue;
            $hasAny = true;

            if (!preg_match('#^https?://#i', $url)) {
                $url = 'https://' . $url;
            }

            $html .= '<a href="' . htmlspecialchars($url) . '" target="_blank" rel="noopener nofollow" '
                   . 'class="social-link social-' . $key . '" '
                   . 'style="--brand-color:' . $meta[2] . '" '
                   . 'title="' . htmlspecialchars($meta[1]) . '">'
                   . '<span class="social-icon">' . $meta[0] . '</span>'
                   . '<span class="social-label">' . htmlspecialchars($meta[1]) . '</span>'
                   . '</a>';
        }

        $html .= '</div>';

        return $hasAny ? $html : '';
    }

    private function renderPhp(array $block): string
    {
        $file = $block['blockfile'] ?? '';
        if ($file === '') return '';

        if (!preg_match('#^[a-zA-Z0-9_\-]+\.php$#', $file)) return '';

        $path = __DIR__ . '/../Blocks/' . $file;
        if (!file_exists($path)) return '';

        ob_start();
        try {
            extract(['block' => $block]);
            require $path;
        } catch (\Throwable $e) {
            return '<em style="color:#c0392b">Blok hatası: ' . htmlspecialchars($e->getMessage()) . '</em>';
        }
        return ob_get_clean();
    }
}
