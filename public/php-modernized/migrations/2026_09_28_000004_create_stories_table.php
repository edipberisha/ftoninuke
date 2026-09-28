<?php

return [
    'up' => function ($db) {
        $db->query("CREATE TABLE IF NOT EXISTS nuke_stories (
            sid INT AUTO_INCREMENT PRIMARY KEY,
            aid VARCHAR(50),
            title VARCHAR(255) NOT NULL,
            image VARCHAR(255) DEFAULT '',
            `time` INT NOT NULL,
            hometext TEXT,
            bodytext LONGTEXT,
            topic INT DEFAULT 1,
            informant VARCHAR(50)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    },
    'down' => function ($db) {
        $db->query("DROP TABLE IF EXISTS nuke_stories");
    },
];