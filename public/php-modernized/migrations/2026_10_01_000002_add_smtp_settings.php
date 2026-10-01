<?php

return [
    'up' => function ($db) {
        $columns = [
            "ADD COLUMN smtp_host VARCHAR(255) DEFAULT ''",
            "ADD COLUMN smtp_port INT DEFAULT 587",
            "ADD COLUMN smtp_user VARCHAR(255) DEFAULT ''",
            "ADD COLUMN smtp_pass VARCHAR(255) DEFAULT ''",
            "ADD COLUMN smtp_secure VARCHAR(10) DEFAULT 'tls'",
            "ADD COLUMN smtp_from_email VARCHAR(255) DEFAULT ''",
            "ADD COLUMN smtp_from_name VARCHAR(100) DEFAULT 'FtoniNuke'",
            "ADD COLUMN contact_email VARCHAR(255) DEFAULT ''",
        ];
        foreach ($columns as $c) {
            try { $db->query("ALTER TABLE nuke_config $c"); } catch (\Throwable $e) {}
        }
    },
    'down' => function ($db) {
        foreach (['smtp_host','smtp_port','smtp_user','smtp_pass','smtp_secure','smtp_from_email','smtp_from_name','contact_email'] as $c) {
            try { $db->query("ALTER TABLE nuke_config DROP COLUMN $c"); } catch (\Throwable $e) {}
        }
    },
];
