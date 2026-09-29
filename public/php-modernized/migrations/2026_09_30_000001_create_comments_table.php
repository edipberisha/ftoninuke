<?php

return [
    'up' => function ($db) {
        // ─── Yorumlar ─────────────────────────────────
        $db->query("CREATE TABLE IF NOT EXISTS nuke_comments (
            cid INT AUTO_INCREMENT PRIMARY KEY,
            sid INT NOT NULL,
            pid INT DEFAULT 0,
            author_name VARCHAR(100) NOT NULL,
            author_email VARCHAR(150) DEFAULT '',
            author_id INT DEFAULT 0,
            author_ip VARCHAR(45) DEFAULT '',
            content TEXT NOT NULL,
            active TINYINT DEFAULT 0,
            likes INT DEFAULT 0,
            created_at INT NOT NULL,
            INDEX idx_sid (sid),
            INDEX idx_active (active),
            INDEX idx_pid (pid)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        // ─── Ban listesi ──────────────────────────────
        $db->query("CREATE TABLE IF NOT EXISTS nuke_comment_bans (
            id INT AUTO_INCREMENT PRIMARY KEY,
            ip_address VARCHAR(45) NOT NULL,
            reason VARCHAR(255) DEFAULT '',
            created_at INT NOT NULL,
            INDEX idx_ip (ip_address)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    },
    'down' => function ($db) {
        $db->query("DROP TABLE IF EXISTS nuke_comments");
        $db->query("DROP TABLE IF EXISTS nuke_comment_bans");
    },
];
