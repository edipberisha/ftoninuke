<?php

return [
    'up' => function ($db) {
        // Kolonları ekle (varsa hata yok)
        $cols = [
            "ADD COLUMN block_type VARCHAR(20) DEFAULT 'html' AFTER bposition",
            "ADD COLUMN image VARCHAR(255) DEFAULT '' AFTER content",
            "ADD COLUMN link_url VARCHAR(255) DEFAULT '' AFTER image",
            "ADD COLUMN config TEXT AFTER link_url",
            "ADD COLUMN created_at INT DEFAULT 0 AFTER weight",
        ];
        foreach ($cols as $c) {
            try { $db->query("ALTER TABLE nuke_blocks $c"); } catch (\Throwable $e) {}
        }

        // Varsayılan bloklar
        $defaults = [
            // bposition, title, block_type, content, image, link_url, config, weight
            ['r', 'Sosyal Medya',  'social', '', '', '', json_encode([
                'facebook'  => '',
                'twitter'   => '',
                'instagram' => '',
                'youtube'   => '',
                'linkedin'  => '',
                'github'    => '',
            ]), 10],
            ['r', 'İstatistikler', 'php', '', '', '', '', 20],
            ['l', 'Ana Menü',      'php', '', '', '', '', 5],
        ];

        foreach ($defaults as $d) {
            try {
                $db->query(
                    "INSERT INTO nuke_blocks (bposition, title, block_type, content, image, link_url, config, weight, active, created_at)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1, ?)",
                    [$d[0], $d[1], $d[2], $d[3], $d[4], $d[5], $d[6], $d[7], time()]
                );
            } catch (\Throwable $e) {}
        }
    },
    'down' => function ($db) {
        // Sadece eklenen kolonları kaldır, tabloyu silme
        foreach (['block_type','image','link_url','config','created_at'] as $c) {
            try { $db->query("ALTER TABLE nuke_blocks DROP COLUMN $c"); } catch (\Throwable $e) {}
        }
    },
];
