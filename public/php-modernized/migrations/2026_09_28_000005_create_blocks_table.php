<?php

return [
    'up' => function ($db) {
        $db->query("CREATE TABLE IF NOT EXISTS nuke_blocks (
            bid INT AUTO_INCREMENT PRIMARY KEY,
            bposition CHAR(1) DEFAULT 'r',
            title VARCHAR(100),
            content TEXT,
            blockfile VARCHAR(255),
            active TINYINT DEFAULT 1,
            weight INT DEFAULT 0
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    },
    'down' => function ($db) {
        $db->query("DROP TABLE IF EXISTS nuke_blocks");
    },
];