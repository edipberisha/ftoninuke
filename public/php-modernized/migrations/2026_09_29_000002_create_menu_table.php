<?php

return [
    'up' => function ($db) {
        $db->query("CREATE TABLE IF NOT EXISTS nuke_menu (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(100) NOT NULL,
            url VARCHAR(255) NOT NULL,
            target VARCHAR(10) DEFAULT '_self',
            parent_id INT DEFAULT 0,
            sort_order INT DEFAULT 0,
            active TINYINT DEFAULT 1,
            created_at INT NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        // Varsayılan menü öğeleri
        $defaults = [
            ['Ana Sayfa', '/public/',            0, 1],
            ['Admin',     '/public/admin',       0, 2],
            ['Profil',    '/public/profile',     0, 3],
        ];
        foreach ($defaults as $d) {
            try {
                $db->query(
                    "INSERT INTO nuke_menu (title, url, parent_id, sort_order, created_at) VALUES (?, ?, ?, ?, ?)",
                    [$d[0], $d[1], $d[2], $d[3], time()]
                );
            } catch (\Throwable $e) {}
        }
    },
    'down' => function ($db) {
        $db->query("DROP TABLE IF EXISTS nuke_menu");
    },
];
