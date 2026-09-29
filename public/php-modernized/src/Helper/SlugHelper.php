<?php

declare(strict_types=1);

namespace FtoniNuke\Helper;

class SlugHelper
{
    /**
     * SEO dostu slug üret (Türkçe karakter desteği)
     */
    public static function slugify(string $text): string
    {
        // Türkçe karakterler
        $tr = ['ç','ğ','ı','i','ö','ş','ü','Ç','Ğ','İ','Ö','Ş','Ü','â','Â','î','Î','û','Û'];
        $en = ['c','g','i','i','o','s','u','c','g','i','o','s','u','a','a','i','i','u','u'];
        $text = str_replace($tr, $en, $text);

        // Diğer Unicode karakterleri ASCII'ye çevir
        $text = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text) ?: $text;

        // Küçük harf
        $text = mb_strtolower($text, 'UTF-8');

        // Alfa-numerik olmayanları tireye çevir
        $text = preg_replace('/[^a-z0-9]+/', '-', $text);

        // Baştaki/sondaki tireleri temizle
        $text = trim($text, '-');

        // Çoklu tireleri tek tireye indir
        $text = preg_replace('/-+/', '-', $text);

        // Çok uzun slug'ları kes (200 karakter)
        if (mb_strlen($text) > 200) {
            $text = mb_substr($text, 0, 200);
            $text = rtrim($text, '-');
        }

        return $text !== '' ? $text : 'n-a';
    }
}
