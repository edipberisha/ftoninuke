<?php

return [
    'up' => function ($db) {
        $db->query("CREATE TABLE IF NOT EXISTS nuke_users (
            user_id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            user_email VARCHAR(100) NOT NULL UNIQUE,
            user_password VARCHAR(255) NOT NULL,
            user_level TINYINT DEFAULT 1,
            created_at INT NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    },
    'down' => function ($db) {
        $db->query("DROP TABLE IF EXISTS nuke_users");
    },
];