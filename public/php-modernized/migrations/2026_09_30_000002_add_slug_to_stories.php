<?php

use FtoniNuke\Helper\SlugHelper;

return [
    'up' => function ($db) {
        try {
            $db->query("ALTER TABLE nuke_stories ADD COLUMN slug VARCHAR(255) DEFAULT '' AFTER title");
        } catch (\Throwable $e) {}

        try {
            $db->query("ALTER TABLE nuke_stories ADD UNIQUE INDEX idx_slug (slug)");
        } catch (\Throwable $e) {}

        $stories = $db->fetchAll("SELECT sid, title FROM nuke_stories WHERE slug = '' OR slug IS NULL");
        foreach ($stories as $s) {
            $slug = SlugHelper::slugify((string)$s['title']);
            if ($slug === '' || $slug === 'n-a') $slug = 'haber';
            $slug .= '-' . $s['sid'];
            try {
                $db->query("UPDATE nuke_stories SET slug = ? WHERE sid = ?", [$slug, $s['sid']]);
            } catch (\Throwable $e) {}
        }
    },
    'down' => function ($db) {
        try { $db->query("ALTER TABLE nuke_stories DROP INDEX idx_slug"); } catch (\Throwable $e) {}
        try { $db->query("ALTER TABLE nuke_stories DROP COLUMN slug"); } catch (\Throwable $e) {}
    },
];
