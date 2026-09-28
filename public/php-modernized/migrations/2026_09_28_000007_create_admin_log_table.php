<?php

return [
    'up' => function ($db) {
        $db->query("CREATE TABLE IF NOT EXISTS nuke_admin_log (
            id INT AUTO_INCREMENT PRIMARY KEY,
            admin_id INT,
            action VARCHAR(50),
            module VARCHAR(50),
            details TEXT,
            ip_address VARCHAR(45),
            created_at INT NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    },
    'down' => function ($db) {
        $db->query("DROP TABLE IF EXISTS nuke_admin_log");
    },
];