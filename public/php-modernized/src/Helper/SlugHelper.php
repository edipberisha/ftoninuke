<?php

declare(strict_types=1);

namespace FtoniNuke\Helper;

/**
 * SlugHelper - Generates SEO-friendly URLs for articles.
 */
class SlugHelper
{
    /**
     * Create a slug from a string
     */
    public static function slugify(string $text): string
    {
        // Replace non-letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);

        // Transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // Remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        // Trim
        $text = trim($text, '-');

        // Remove duplicate -
        $text = preg_replace('~-+~', '-', $text);

        // Lowercase
        $text = strtolower($text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;
    }
}
