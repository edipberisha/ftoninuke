<?php

return [
    'up' => function ($db) {
        $db->query("CREATE TABLE IF NOT EXISTS nuke_import_log (
            id INT AUTO_INCREMENT PRIMARY KEY,
            source VARCHAR(50) NOT NULL,
            step VARCHAR(50) NOT NULL,
            total INT DEFAULT 0,
            imported INT DEFAULT 0,
            skipped INT DEFAULT 0,
            failed INT DEFAULT 0,
            dry_run TINYINT DEFAULT 0,
            details TEXT,
            started_at INT NOT NULL,
            finished_at INT DEFAULT 0,
            status VARCHAR(20) DEFAULT 'running',
            INDEX idx_source (source),
            INDEX idx_status (status)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    },
    'down' => function ($db) {
        $db->query("DROP TABLE IF EXISTS nuke_import_log");
    },
];
