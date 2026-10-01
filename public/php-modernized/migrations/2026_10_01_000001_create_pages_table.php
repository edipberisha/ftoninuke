<?php

return [
    'up' => function ($db) {
        // ─── Statik Sayfalar ────────────────────────────
        $db->query("CREATE TABLE IF NOT EXISTS nuke_pages (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            slug VARCHAR(255) NOT NULL UNIQUE,
            content LONGTEXT,
            template VARCHAR(50) DEFAULT 'default',
            meta_description VARCHAR(255) DEFAULT '',
            active TINYINT DEFAULT 1,
            show_in_menu TINYINT DEFAULT 0,
            sort_order INT DEFAULT 0,
            created_at INT NOT NULL,
            updated_at INT DEFAULT 0,
            INDEX idx_slug (slug),
            INDEX idx_active (active)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        // ─── İletişim Mesajları ─────────────────────────
        $db->query("CREATE TABLE IF NOT EXISTS nuke_contact_messages (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(150) NOT NULL,
            subject VARCHAR(255) DEFAULT '',
            message TEXT NOT NULL,
            ip_address VARCHAR(45) DEFAULT '',
            is_read TINYINT DEFAULT 0,
            created_at INT NOT NULL,
            INDEX idx_read (is_read)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        // ─── Varsayılan Sayfalar ────────────────────────
        $defaults = [
            [
                'Hakkımızda', 'hakkimizda',
                '<h2>Hakkımızda</h2><p>FtoniNuke modern bir PHP-Nuke devamıdır. 20 yıllık PHP-Nuke mirasını modern standartlarda yeniden yorumluyoruz.</p><p>Bu sayfayı admin panelinden düzenleyebilirsiniz.</p>',
                'default', 1, 1, 1,
            ],
            [
                'Gizlilik Politikası', 'gizlilik-politikasi',
                '<h2>Gizlilik Politikası</h2><p>Bu sayfa KVKK/GDPR uyumlu gizlilik politikanız için ayrılmıştır.</p><h3>Toplanan Veriler</h3><p>...</p><h3>Çerezler</h3><p>...</p>',
                'default', 1, 0, 2,
            ],
            [
                'İletişim', 'iletisim',
                '',
                'contact', 1, 1, 3,
            ],
        ];

        foreach ($defaults as $d) {
            try {
                $db->query(
                    "INSERT INTO nuke_pages (title, slug, content, template, active, show_in_menu, sort_order, created_at)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
                    [$d[0], $d[1], $d[2], $d[3], $d[4], $d[5], $d[6], time()]
                );
            } catch (\Throwable $e) {}
        }
    },
    'down' => function ($db) {
        $db->query("DROP TABLE IF EXISTS nuke_contact_messages");
        $db->query("DROP TABLE IF EXISTS nuke_pages");
    },
];
