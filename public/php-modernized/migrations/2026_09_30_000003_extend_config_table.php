<?php

return [
    'up' => function ($db) {
        $columns = [
            "ADD COLUMN registration_enabled TINYINT DEFAULT 0",
            "ADD COLUMN email_verification TINYINT DEFAULT 0",
            "ADD COLUMN default_user_level TINYINT DEFAULT 1",
            "ADD COLUMN comments_require_approval TINYINT DEFAULT 1",
            "ADD COLUMN captcha_enabled TINYINT DEFAULT 1",
            "ADD COLUMN comment_rate_limit INT DEFAULT 5",
        ];
        foreach ($columns as $c) {
            try { $db->query("ALTER TABLE nuke_config $c"); } catch (\Throwable $e) {}
        }
    },
    'down' => function ($db) {
        foreach (['registration_enabled','email_verification','default_user_level','comments_require_approval','captcha_enabled','comment_rate_limit'] as $c) {
            try { $db->query("ALTER TABLE nuke_config DROP COLUMN $c"); } catch (\Throwable $e) {}
        }
    },
];
