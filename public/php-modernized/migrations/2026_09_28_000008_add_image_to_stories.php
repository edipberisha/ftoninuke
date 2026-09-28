<?php

return [
    'up' => function ($db) {
        // Kolon zaten varsa hata verir, try/catch ile yakala
        try {
            $db->query("ALTER TABLE nuke_stories ADD COLUMN image VARCHAR(255) DEFAULT '' AFTER title");
        } catch (\Throwable $e) {
            // Kolon zaten varsa sorun değil
        }
    },
    'down' => function ($db) {
        try {
            $db->query("ALTER TABLE nuke_stories DROP COLUMN image");
        } catch (\Throwable $e) {}
    },
];