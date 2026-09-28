<?php

return [
    'up' => function ($db) {
        $db->query("CREATE TABLE IF NOT EXISTS nuke_config (
            id INT AUTO_INCREMENT PRIMARY KEY,
            sitename VARCHAR(255),
            theme VARCHAR(50) DEFAULT 'default',
            api_key VARCHAR(100)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    },
    'down' => function ($db) {
        $db->query("DROP TABLE IF EXISTS nuke_config");
    },
];