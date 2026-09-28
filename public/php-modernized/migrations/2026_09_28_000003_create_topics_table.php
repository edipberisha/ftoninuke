<?php

return [
    'up' => function ($db) {
        $db->query("CREATE TABLE IF NOT EXISTS nuke_topics (
            topic_id INT AUTO_INCREMENT PRIMARY KEY,
            topic_name VARCHAR(100) NOT NULL,
            topic_slug VARCHAR(100) NOT NULL UNIQUE,
            topic_image VARCHAR(255) DEFAULT '',
            topic_description TEXT,
            created_at INT NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        // Varsayılan kategoriler
        $defaults = ['Genel', 'Haberler', 'Duyurular', 'Teknoloji', 'Yazılım'];
        foreach ($defaults as $name) {
            $slug = strtolower(str_replace(
                ['ç','ğ','ı','i','ö','ş','ü'],
                ['c','g','i','i','o','s','u'],
                $name
            ));
            try {
                $db->query(
                    "INSERT INTO nuke_topics (topic_name, topic_slug, created_at) VALUES (?, ?, ?)",
                    [$name, $slug, time()]
                );
            } catch (\Throwable $e) {}
        }
    },
    'down' => function ($db) {
        $db->query("DROP TABLE IF EXISTS nuke_topics");
    },
];