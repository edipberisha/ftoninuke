<?php

declare(strict_types=1);

namespace FtoniNuke\Controller;

use FtoniNuke\Core\Database;
use FtoniNuke\Repository\NewsRepository;
use FtoniNuke\Repository\TopicRepository;
use FtoniNuke\Repository\MediaRepository;
use FtoniNuke\Service\MediaService;

class AdminController
{
    private Database $db;

    public function __construct()
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: /public/login');
            exit;
        }
        if (($_SESSION['user_level'] ?? 0) < 2) {
            http_response_code(403);
            die('Bu sayfaya erişim yetkiniz yok.');
        }
        $this->db = Database::getInstance();
    }

    // ─── Dashboard ────────────────────────────────────
    public function dashboard(): void
    {
        $totalUsers   = 0;
        $totalStories = 0;

        try {
            $row = $this->db->fetch("SELECT COUNT(*) AS c FROM nuke_users");
            $totalUsers = (int)($row['c'] ?? 0);
        } catch (\Throwable $e) {}

        try {
            $row = $this->db->fetch("SELECT COUNT(*) AS c FROM nuke_stories");
            $totalStories = (int)($row['c'] ?? 0);
        } catch (\Throwable $e) {}

        $stats = [
            'php_version'   => PHP_VERSION,
            'db_status'     => 'Connected',
            'active_users'  => !empty($_SESSION['user_id']) ? 1 : 0,
            'total_stories' => $totalStories,
            'total_users'   => $totalUsers,
        ];

        $recentLogs = [];
        $pageTitle  = 'Dashboard';
        $active     = 'dashboard';

        ob_start();
        require __DIR__ . '/../Views/admin/dashboard.html.php';
        $content = ob_get_clean();
        require __DIR__ . '/../Views/admin/layout.html.php';
    }

    // ─── Media ────────────────────────────────────────
    public function media(): void
    {
        $mediaList = [];
        try {
            $mediaRepo = new MediaRepository();
            $mediaList = $mediaRepo->getAll(100, 0);
        } catch (\Throwable $e) {}

        $pageTitle = 'Medya';
        $active    = 'media';

        ob_start();
        require __DIR__ . '/../Views/admin/media/index.html.php';
        $content = ob_get_clean();
        require __DIR__ . '/../Views/admin/layout.html.php';
    }

    // ─── Themes ───────────────────────────────────────
    public function themes(): void
    {
        $themes = [];
        $themesDir = __DIR__ . '/../../public/themes';
        if (is_dir($themesDir)) {
            foreach (scandir($themesDir) as $item) {
                if ($item === '.' || $item === '..') continue;
                if (!is_dir($themesDir . '/' . $item)) continue;
                $themes[] = [
                    'name'    => $item,
                    'version' => '1.0',
                    'active'  => ($item === 'default'),
                ];
            }
        }
        if (empty($themes)) {
            $themes[] = ['name' => 'default', 'version' => '1.0', 'active' => true];
        }

        $pageTitle = 'Temalar';
        $active    = 'themes';

        ob_start();
        require __DIR__ . '/../Views/admin/themes/index.html.php';
        $content = ob_get_clean();
        require __DIR__ . '/../Views/admin/layout.html.php';
    }

    // ─── Widgets ──────────────────────────────────────
    public function widgets(): void
    {
        $availableWidgets = [];
        $widgetsDir = __DIR__ . '/../Widgets';
        if (is_dir($widgetsDir)) {
            foreach (scandir($widgetsDir) as $item) {
                if ($item === '.' || $item === '..') continue;
                if (!is_dir($widgetsDir . '/' . $item)) continue;
                $availableWidgets[] = ['name' => $item, 'slug' => $item];
            }
        }

        $pageTitle = 'Widget\'lar';
        $active    = 'widgets';

        ob_start();
        require __DIR__ . '/../Views/admin/widgets/index.html.php';
        $content = ob_get_clean();
        require __DIR__ . '/../Views/admin/layout.html.php';
    }

    // ─── Users ────────────────────────────────────────
        public function users(): void
    {
        $repo   = new \FtoniNuke\Repository\UserRepository();
        $users  = $repo->all();
        $flash  = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        $pageTitle = 'Kullanıcılar';
        $active    = 'users';

        ob_start();
        require __DIR__ . '/../Views/admin/users.html.php';
        $content = ob_get_clean();
        require __DIR__ . '/../Views/admin/layout.html.php';
    }

    public function userCreate(): void
    {
        $errors = [];
        $data   = ['username' => '', 'user_email' => '', 'user_level' => 1];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data['username']   = trim($_POST['username'] ?? '');
            $data['user_email'] = trim($_POST['user_email'] ?? '');
            $data['user_level'] = (int)($_POST['user_level'] ?? 1);

            $service = new \FtoniNuke\Service\UserService();
            $result  = $service->create([
                'username'   => $data['username'],
                'user_email' => $data['user_email'],
                'password'   => $_POST['password'] ?? '',
                'user_level' => $data['user_level'],
            ]);

            if ($result['ok']) {
                $_SESSION['flash'] = '✅ Kullanıcı oluşturuldu: ' . $data['username'];
                header('Location: /public/admin/users');
                exit;
            }

            $errors[] = $result['error'];
        }

        $pageTitle = 'Yeni Kullanıcı';
        $active    = 'users';
        $formMode  = 'create';

        ob_start();
        require __DIR__ . '/../Views/admin/user-form.html.php';
        $content = ob_get_clean();
        require __DIR__ . '/../Views/admin/layout.html.php';
    }

    public function userEdit(): void
    {
        $id     = (int)($_GET['id'] ?? 0);
        $repo   = new \FtoniNuke\Repository\UserRepository();
        $user   = $repo->findById($id);

        if (!$user) {
            $_SESSION['flash'] = '❌ Kullanıcı bulunamadı.';
            header('Location: /public/admin/users');
            exit;
        }

        $errors = [];
        $data   = $user;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data['username']   = trim($_POST['username'] ?? '');
            $data['user_email'] = trim($_POST['user_email'] ?? '');
            $data['user_level'] = (int)($_POST['user_level'] ?? 1);

            $service = new \FtoniNuke\Service\UserService();
            $result  = $service->update($id, [
                'username'   => $data['username'],
                'user_email' => $data['user_email'],
                'password'   => $_POST['password'] ?? '',
                'user_level' => $data['user_level'],
            ]);

            if ($result['ok']) {
                $_SESSION['flash'] = '✅ Kullanıcı güncellendi.';
                header('Location: /public/admin/users');
                exit;
            }

            $errors[] = $result['error'];
        }

        $pageTitle = 'Kullanıcı Düzenle';
        $active    = 'users';
        $formMode  = 'edit';

        ob_start();
        require __DIR__ . '/../Views/admin/user-form.html.php';
        $content = ob_get_clean();
        require __DIR__ . '/../Views/admin/layout.html.php';
    }

    public function userDelete(): void
    {
        $id = (int)($_GET['id'] ?? 0);

        $service = new \FtoniNuke\Service\UserService();
        $result  = $service->delete($id);

        $_SESSION['flash'] = $result['ok']
            ? '🗑️ Kullanıcı silindi.'
            : '❌ ' . $result['error'];

        header('Location: /public/admin/users');
        exit;
    }

    // ─── Security ─────────────────────────────────────
    public function security(): void
    {
        $pageTitle = 'Güvenlik';
        $active    = 'security';

        ob_start();
        require __DIR__ . '/../Views/admin/security.html.php';
        $content = ob_get_clean();
        require __DIR__ . '/../Views/admin/layout.html.php';
    }

    // ─── Topics (kategoriler) ─────────────────────────
    public function topics(): void
    {
        $topicRepo = new TopicRepository();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['topic_name'] ?? '');
            $desc = trim($_POST['topic_description'] ?? '');

            if ($name !== '') {
                $topicRepo->create($name, $desc);
                $_SESSION['flash'] = '✅ Kategori eklendi.';
            } else {
                $_SESSION['flash'] = '❌ Kategori adı gerekli.';
            }
            header('Location: /public/admin/topics');
            exit;
        }

        if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
            $topicRepo->delete((int)$_GET['delete']);
            $_SESSION['flash'] = '🗑️ Kategori silindi.';
            header('Location: /public/admin/topics');
            exit;
        }

        $topics = $topicRepo->all();
        $flash  = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        $pageTitle = 'Kategoriler';
        $active    = 'topics';

        ob_start();
        require __DIR__ . '/../Views/admin/topics.html.php';
        $content = ob_get_clean();
        require __DIR__ . '/../Views/admin/layout.html.php';
    }

    // ─── Menu (menü yönetimi) ─────────────────────────
    public function menu(): void
    {
        $repo = new \FtoniNuke\Repository\MenuRepository();

        // POST: yeni ekle veya güncelle
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'title'      => trim($_POST['title'] ?? ''),
                'url'        => trim($_POST['url'] ?? ''),
                'target'     => $_POST['target'] ?? '_self',
                'parent_id'  => (int)($_POST['parent_id'] ?? 0),
                'sort_order' => (int)($_POST['sort_order'] ?? 0),
                'active'     => isset($_POST['active']) ? 1 : 0,
            ];

            if ($data['title'] === '' || $data['url'] === '') {
                $_SESSION['flash'] = '❌ Başlık ve URL gerekli.';
            } else {
                $editId = (int)($_POST['edit_id'] ?? 0);
                if ($editId > 0) {
                    $repo->update($editId, $data);
                    $_SESSION['flash'] = '✅ Menü öğesi güncellendi.';
                } else {
                    $repo->create($data);
                    $_SESSION['flash'] = '✅ Menü öğesi eklendi.';
                }
            }
            header('Location: /public/admin/menu');
            exit;
        }

        // Toggle aktif/pasif
        if (isset($_GET['toggle']) && is_numeric($_GET['toggle'])) {
            $repo->toggleActive((int)$_GET['toggle']);
            header('Location: /public/admin/menu');
            exit;
        }

        // Sil
        if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
            $repo->delete((int)$_GET['delete']);
            $_SESSION['flash'] = '🗑️ Menü öğesi silindi.';
            header('Location: /public/admin/menu');
            exit;
        }

        $items = $repo->all();
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        $pageTitle = 'Menü Yönetimi';
        $active    = 'menu';

        ob_start();
        require __DIR__ . '/../Views/admin/menu.html.php';
        $content = ob_get_clean();
        require __DIR__ . '/../Views/admin/layout.html.php';
    }
	
	    // ══════════════════════════════════════════════════
    // BLOCKS (sol/sağ sütun kutucukları)
    // ══════════════════════════════════════════════════
    public function blocks(): void
    {
        $repo   = new \FtoniNuke\Repository\BlockRepository();
        $blocks = $repo->all();
        $flash  = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        $pageTitle = 'Bloklar';
        $active    = 'blocks';

        ob_start();
        require __DIR__ . '/../Views/admin/blocks.html.php';
        $content = ob_get_clean();
        require __DIR__ . '/../Views/admin/layout.html.php';
    }

    public function blockCreate(): void
    {
        $repo = new \FtoniNuke\Repository\BlockRepository();
        $availableFiles = $repo->availableBlockFiles();

        $errors = [];
        $data = [
            'bposition' => 'r',
            'title' => '',
            'block_type' => 'html',
            'content' => '',
            'image' => '',
            'link_url' => '',
            'config' => '',
            'blockfile' => '',
            'weight' => 0,
            'active' => 1,
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $this->collectBlockData($_POST, $_FILES);

            if ($data['title'] === '') $errors[] = 'Blok başlığı gerekli.';

            if (empty($errors)) {
                $repo->create($data);
                $_SESSION['flash'] = '✅ Blok oluşturuldu.';
                header('Location: /public/admin/blocks');
                exit;
            }
        }

        $block = $data; // view uyumluluğu
        $pageTitle = 'Yeni Blok';
        $active    = 'blocks';

        ob_start();
        require __DIR__ . '/../Views/admin/block-form.html.php';
        $content = ob_get_clean();
        require __DIR__ . '/../Views/admin/layout.html.php';
    }

    public function blockEdit(): void
    {
        $bid  = (int)($_GET['bid'] ?? 0);
        $repo = new \FtoniNuke\Repository\BlockRepository();
        $block = $repo->findById($bid);

        if (!$block) {
            $_SESSION['flash'] = '❌ Blok bulunamadı.';
            header('Location: /public/admin/blocks');
            exit;
        }

        $availableFiles = $repo->availableBlockFiles();
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $this->collectBlockData($_POST, $_FILES, $block);

            if ($data['title'] === '') $errors[] = 'Blok başlığı gerekli.';

            if (empty($errors)) {
                $repo->update($bid, $data);
                $_SESSION['flash'] = '✅ Blok güncellendi.';
                header('Location: /public/admin/blocks');
                exit;
            }
            $block = array_merge($block, $data);
        }

        $pageTitle = 'Blok Düzenle';
        $active    = 'blocks';

        ob_start();
        require __DIR__ . '/../Views/admin/block-form.html.php';
        $content = ob_get_clean();
        require __DIR__ . '/../Views/admin/layout.html.php';
    }

    public function blockDelete(): void
    {
        $bid  = (int)($_GET['bid'] ?? 0);
        $repo = new \FtoniNuke\Repository\BlockRepository();
        $repo->delete($bid);
        $_SESSION['flash'] = '🗑️ Blok silindi.';
        header('Location: /public/admin/blocks');
        exit;
    }

    public function blockToggle(): void
    {
        $bid  = (int)($_GET['bid'] ?? 0);
        $repo = new \FtoniNuke\Repository\BlockRepository();
        $repo->toggleActive($bid);
        header('Location: /public/admin/blocks');
        exit;
    }

    /**
     * POST verisini topla (blok tipine göre)
     */
    private function collectBlockData(array $post, array $files, array $existing = []): array
    {
        $data = [
            'bposition'  => in_array($post['bposition'] ?? 'r', ['l','r','c'], true) ? $post['bposition'] : 'r',
            'title'      => trim($post['title'] ?? ''),
            'block_type' => in_array($post['block_type'] ?? 'html', ['html','social','ad','php'], true) ? $post['block_type'] : 'html',
            'content'    => $post['content'] ?? '',
            'blockfile'  => trim($post['blockfile'] ?? ''),
            'weight'     => (int)($post['weight'] ?? 0),
            'active'     => isset($post['active']) ? 1 : 0,
            'link_url'   => trim($post['link_url'] ?? ''),
            'image'      => $existing['image'] ?? '',
            'config'     => $existing['config'] ?? '',
        ];

        // Sosyal medya config (JSON)
        if ($data['block_type'] === 'social') {
            $socials = [
                'facebook', 'twitter', 'instagram', 'youtube',
                'linkedin', 'github', 'tiktok', 'whatsapp', 'telegram',
            ];
            $config = [];
            foreach ($socials as $s) {
                $config[$s] = trim($post['social_' . $s] ?? '');
            }
            $data['config'] = json_encode($config);
        }

        // Reklam bloğu görseli
        if ($data['block_type'] === 'ad' && !empty($files['ad_image']['name'])) {
            try {
                $mediaService = new MediaService(new MediaRepository());
                $result = $mediaService->upload($files['ad_image'], (int)($_SESSION['user_id'] ?? 0));
                $data['image'] = $result['url'];
            } catch (\Throwable $e) {
                // sessizce geç
            }
        }

        // Görsel kaldırma
        if (!empty($post['remove_image'])) {
            $data['image'] = '';
        }

        return $data;
    }
	
	    // ══════════════════════════════════════════════════
    // COMMENTS (yorum moderasyonu)
    // ══════════════════════════════════════════════════
    public function comments(): void
    {
        $repo   = new \FtoniNuke\Repository\CommentRepository();
        $filter = $_GET['filter'] ?? 'pending';

        // Aksiyonlar
        if (isset($_GET['approve']) && is_numeric($_GET['approve'])) {
            $repo->approve((int)$_GET['approve']);
            $_SESSION['flash'] = '✅ Yorum onaylandı.';
            header('Location: /public/admin/comments?filter=' . $filter);
            exit;
        }

        if (isset($_GET['unapprove']) && is_numeric($_GET['unapprove'])) {
            $repo->unapprove((int)$_GET['unapprove']);
            $_SESSION['flash'] = '⏳ Yorum onaydan kaldırıldı.';
            header('Location: /public/admin/comments?filter=' . $filter);
            exit;
        }

        if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
            $repo->delete((int)$_GET['delete']);
            $_SESSION['flash'] = '🗑️ Yorum silindi.';
            header('Location: /public/admin/comments?filter=' . $filter);
            exit;
        }

        if (isset($_GET['ban']) && is_numeric($_GET['ban'])) {
            $comment = $repo->findById((int)$_GET['ban']);
            if ($comment && !empty($comment['author_ip'])) {
                $repo->banIp($comment['author_ip'], 'Admin tarafından yasaklandı');
                $_SESSION['flash'] = '🚫 IP yasaklandı: ' . $comment['author_ip'];
            }
            header('Location: /public/admin/comments?filter=' . $filter);
            exit;
        }

        $comments = $repo->all($filter, 200, 0);
        $pending  = $repo->countPending();
        $bannedIps = $repo->bannedIps();
        $flash    = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        $pageTitle = 'Yorumlar';
        $active    = 'comments';

        ob_start();
        require __DIR__ . '/../Views/admin/comments.html.php';
        $content = ob_get_clean();
        require __DIR__ . '/../Views/admin/layout.html.php';
    }
	
	    // ══════════════════════════════════════════════════
    // PAGES (statik sayfalar)
    // ══════════════════════════════════════════════════
    public function pages(): void
    {
        $repo   = new \FtoniNuke\Repository\PageRepository();
        $pages  = $repo->all();
        $flash  = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        $pageTitle = 'Sayfalar';
        $active    = 'pages';

        ob_start();
        require __DIR__ . '/../Views/admin/pages.html.php';
        $content = ob_get_clean();
        require __DIR__ . '/../Views/admin/layout.html.php';
    }

    public function pageCreate(): void
    {
        $errors = [];
        $data = [
            'title' => '', 'slug' => '', 'content' => '',
            'template' => 'default', 'meta_description' => '',
            'active' => 1, 'show_in_menu' => 0, 'sort_order' => 0,
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data['title']            = trim($_POST['title'] ?? '');
            $data['content']          = $_POST['content'] ?? '';
            $data['template']         = $_POST['template'] ?? 'default';
            $data['meta_description'] = trim($_POST['meta_description'] ?? '');
            $data['active']           = isset($_POST['active']) ? 1 : 0;
            $data['show_in_menu']     = isset($_POST['show_in_menu']) ? 1 : 0;
            $data['sort_order']       = (int)($_POST['sort_order'] ?? 0);

            $repo = new \FtoniNuke\Repository\PageRepository();
            $data['slug'] = !empty($_POST['slug'])
                ? \FtoniNuke\Helper\SlugHelper::slugify($_POST['slug'])
                : $repo->makeUniqueSlug($data['title']);

            if ($data['title'] === '') $errors[] = 'Başlık gerekli.';
            if ($repo->slugExists($data['slug'])) $errors[] = 'Bu slug zaten kullanılıyor.';

            if (empty($errors)) {
                $repo->create($data);
                $_SESSION['flash'] = '✅ Sayfa oluşturuldu.';
                header('Location: /public/admin/pages');
                exit;
            }
        }

        $pageTitle = 'Yeni Sayfa';
        $active    = 'pages';
        $formMode  = 'create';
        $page      = $data;

        ob_start();
        require __DIR__ . '/../Views/admin/page-form.html.php';
        $content = ob_get_clean();
        require __DIR__ . '/../Views/admin/layout.html.php';
    }

    public function pageEdit(): void
    {
        $id   = (int)($_GET['id'] ?? 0);
        $repo = new \FtoniNuke\Repository\PageRepository();
        $page = $repo->findById($id);

        if (!$page) {
            $_SESSION['flash'] = '❌ Sayfa bulunamadı.';
            header('Location: /public/admin/pages');
            exit;
        }

        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $page['title']            = trim($_POST['title'] ?? '');
            $page['content']          = $_POST['content'] ?? '';
            $page['template']         = $_POST['template'] ?? 'default';
            $page['meta_description'] = trim($_POST['meta_description'] ?? '');
            $page['active']           = isset($_POST['active']) ? 1 : 0;
            $page['show_in_menu']     = isset($_POST['show_in_menu']) ? 1 : 0;
            $page['sort_order']       = (int)($_POST['sort_order'] ?? 0);

            $newSlug = !empty($_POST['slug'])
                ? \FtoniNuke\Helper\SlugHelper::slugify($_POST['slug'])
                : $repo->makeUniqueSlug($page['title'], $id);

            if ($page['title'] === '') $errors[] = 'Başlık gerekli.';
            if ($repo->slugExists($newSlug, $id)) $errors[] = 'Bu slug zaten kullanılıyor.';

            $page['slug'] = $newSlug;

            if (empty($errors)) {
                $repo->update($id, $page);
                $_SESSION['flash'] = '✅ Sayfa güncellendi.';
                header('Location: /public/admin/pages');
                exit;
            }
        }

        $pageTitle = 'Sayfa Düzenle';
        $active    = 'pages';
        $formMode  = 'edit';

        ob_start();
        require __DIR__ . '/../Views/admin/page-form.html.php';
        $content = ob_get_clean();
        require __DIR__ . '/../Views/admin/layout.html.php';
    }

    public function pageDelete(): void
    {
        $id   = (int)($_GET['id'] ?? 0);
        $repo = new \FtoniNuke\Repository\PageRepository();
        $repo->delete($id);

        $_SESSION['flash'] = '🗑️ Sayfa silindi.';
        header('Location: /public/admin/pages');
        exit;
    }

    public function pageToggle(): void
    {
        $id   = (int)($_GET['id'] ?? 0);
        $repo = new \FtoniNuke\Repository\PageRepository();
        $repo->toggleActive($id);
        header('Location: /public/admin/pages');
        exit;
    }

    // ══════════════════════════════════════════════════
    // CONTACT MESSAGES (iletişim formu mesajları)
    // ══════════════════════════════════════════════════
    public function messages(): void
    {
        $repo  = new \FtoniNuke\Repository\PageRepository();

        if (isset($_GET['read']) && is_numeric($_GET['read'])) {
            $repo->markMessageRead((int)$_GET['read']);
            header('Location: /public/admin/messages');
            exit;
        }

        if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
            $repo->deleteContactMessage((int)$_GET['delete']);
            $_SESSION['flash'] = '🗑️ Mesaj silindi.';
            header('Location: /public/admin/messages');
            exit;
        }

        $messages = $repo->allContactMessages(100);
        $flash    = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        $pageTitle = 'İletişim Mesajları';
        $active    = 'messages';

        ob_start();
        require __DIR__ . '/../Views/admin/messages.html.php';
        $content = ob_get_clean();
        require __DIR__ . '/../Views/admin/layout.html.php';
    }
	
    // ─── Settings ─────────────────────────────────────
	
        public function settings(): void
    {
        $service = new \FtoniNuke\Service\SettingsService();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'sitename'                 => trim($_POST['sitename'] ?? ''),
                'theme'                    => trim($_POST['theme'] ?? 'default'),
                'registration_enabled'     => isset($_POST['registration_enabled']) ? 1 : 0,
                'email_verification'       => isset($_POST['email_verification']) ? 1 : 0,
                'default_user_level'       => (int)($_POST['default_user_level'] ?? 1),
                'comments_require_approval'=> isset($_POST['comments_require_approval']) ? 1 : 0,
                'captcha_enabled'          => isset($_POST['captcha_enabled']) ? 1 : 0,
                'comment_rate_limit'       => (int)($_POST['comment_rate_limit'] ?? 5),
                'smtp_host'                => trim($_POST['smtp_host'] ?? ''),
                'smtp_port'                => (int)($_POST['smtp_port'] ?? 587),
                'smtp_user'                => trim($_POST['smtp_user'] ?? ''),
                'smtp_pass'                => $_POST['smtp_pass'] ?? '',
                'smtp_secure'              => $_POST['smtp_secure'] ?? 'tls',
                'smtp_from_email'          => trim($_POST['smtp_from_email'] ?? ''),
                'smtp_from_name'           => trim($_POST['smtp_from_name'] ?? 'FtoniNuke'),
                'contact_email'            => trim($_POST['contact_email'] ?? ''),
				'smtp_host'        => trim($_POST['smtp_host'] ?? ''),
                'smtp_port'        => (int)($_POST['smtp_port'] ?? 587),
                'smtp_user'        => trim($_POST['smtp_user'] ?? ''),
                'smtp_pass'        => $_POST['smtp_pass'] ?? '',
                'smtp_secure'      => $_POST['smtp_secure'] ?? 'tls',
                'smtp_from_email'  => trim($_POST['smtp_from_email'] ?? ''),
                'smtp_from_name'   => trim($_POST['smtp_from_name'] ?? 'FtoniNuke'),
                'contact_email'    => trim($_POST['contact_email'] ?? ''),
            ];
            $service->update($data);
            $_SESSION['flash'] = '✅ Ayarlar güncellendi.';
            header('Location: /public/admin/settings');
            exit;
        }

        $config = $service->all();
        $flash  = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        $pageTitle = 'Sistem Ayarları';
        $active    = 'settings';

        ob_start();
        require __DIR__ . '/../Views/admin/settings.html.php';
        $content = ob_get_clean();
        require __DIR__ . '/../Views/admin/layout.html.php';
    }

    public function settingsTestMail(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            // Önce formu kaydet
            if (!empty($_POST['smtp_host'])) {
                $service = new \FtoniNuke\Service\SettingsService();
                $service->update([
                    'smtp_host'       => trim($_POST['smtp_host'] ?? ''),
                    'smtp_port'       => (int)($_POST['smtp_port'] ?? 587),
                    'smtp_user'       => trim($_POST['smtp_user'] ?? ''),
                    'smtp_pass'       => $_POST['smtp_pass'] ?? '',
                    'smtp_secure'     => $_POST['smtp_secure'] ?? 'tls',
                    'smtp_from_email' => trim($_POST['smtp_from_email'] ?? ''),
                    'smtp_from_name'  => trim($_POST['smtp_from_name'] ?? 'FtoniNuke'),
                    'contact_email'   => trim($_POST['contact_email'] ?? ''),
                ]);
            }

            $to = trim($_POST['test_mail_to'] ?? '');
            if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
                echo json_encode(['ok' => false, 'error' => 'Geçersiz e-posta adresi.']);
                return;
            }

            $mailer = new \FtoniNuke\Service\MailService();

            if (!$mailer->isConfigured()) {
                echo json_encode(['ok' => false, 'error' => 'SMTP ayarları eksik. Sunucu, kullanıcı ve şifre gerekli.']);
                return;
            }

            $html = '<h2>FtoniNuke Test Maili</h2><p>Bu bir test mesajıdır. Mail sistemi doğru çalışıyor! 🎉</p><p>Tarih: ' . date('d.m.Y H:i:s') . '</p>';

            $mailer->send($to, 'FtoniNuke SMTP Test', $html);

            echo json_encode(['ok' => true, 'message' => 'Test maili gönderildi: ' . $to]);
        } catch (\Throwable $e) {
            echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
        }
    }
	
    // ══════════════════════════════════════════════════
    // NEWS CRUD
    // ══════════════════════════════════════════════════
    public function news(): void
    {
        $repo     = new NewsRepository();
        $newsList = $repo->all(100, 0);

        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        $pageTitle = 'Haberler';
        $active    = 'news';

        ob_start();
        require __DIR__ . '/../Views/admin/news.html.php';
        $content = ob_get_clean();
        require __DIR__ . '/../Views/admin/layout.html.php';
    }

    public function newsCreate(): void
    {
        $topicRepo = new TopicRepository();
        $topics    = $topicRepo->all();

        $errors = [];
        $data   = ['title' => '', 'hometext' => '', 'bodytext' => '', 'topic' => 0, 'informant' => '', 'image' => ''];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data['title']     = trim($_POST['title'] ?? '');
            $data['hometext']  = trim($_POST['hometext'] ?? '');
            $data['bodytext']  = trim($_POST['bodytext'] ?? '');
            $data['topic']     = (int)($_POST['topic'] ?? 0);
            $data['informant'] = trim($_POST['informant'] ?? '');

            if ($data['title'] === '') $errors[] = 'Başlık gerekli.';
            if ($data['topic'] === 0)  $errors[] = 'Kategori seçilmeli.';

            // 1. Hidden input'tan gelen öne çıkan görsel (picker veya zaten yüklü)
            if (!empty($_POST['image'])) {
                $data['image'] = trim($_POST['image']);
            }

            // 2. Yeni yükleme varsa onu kullan (client-side resize yapılmış)
            if (!empty($_FILES['image_file']['name'])) {
                try {
                    $mediaService  = new MediaService(new MediaRepository());
                    $result        = $mediaService->upload($_FILES['image_file'], (int)($_SESSION['user_id'] ?? 0));
                    $data['image'] = $result['url'];
                } catch (\Throwable $e) {
                    $errors[] = 'Görsel: ' . $e->getMessage();
                }
            }

            if (empty($errors)) {
                $data['aid'] = $_SESSION['username'] ?? 'admin';
                $repo = new NewsRepository();
                $sid  = $repo->create($data);

                $_SESSION['flash'] = '✅ Haber oluşturuldu: #' . $sid;
                header('Location: /public/admin/news');
                exit;
            }
        }

        $pageTitle = 'Yeni Haber';
        $active    = 'news';

        ob_start();
        require __DIR__ . '/../Views/admin/news-form.html.php';
        $content = ob_get_clean();
        require __DIR__ . '/../Views/admin/layout.html.php';
    }

    public function newsEdit(): void
    {
        $topicRepo = new TopicRepository();
        $topics    = $topicRepo->all();

        $sid   = (int)($_GET['sid'] ?? 0);
        $repo  = new NewsRepository();
        $story = $repo->findById($sid);

        if (!$story) {
            $_SESSION['flash'] = '❌ Haber bulunamadı.';
            header('Location: /public/admin/news');
            exit;
        }

        $errors = [];
        $data   = $story;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data['title']     = trim($_POST['title'] ?? '');
            $data['hometext']  = trim($_POST['hometext'] ?? '');
            $data['bodytext']  = trim($_POST['bodytext'] ?? '');
            $data['topic']     = (int)($_POST['topic'] ?? 0);
            $data['informant'] = trim($_POST['informant'] ?? '');

            if ($data['title'] === '') $errors[] = 'Başlık gerekli.';
            if ($data['topic'] === 0)  $errors[] = 'Kategori seçilmeli.';

            // 1. Görsel kaldırma
            if (!empty($_POST['remove_image'])) {
                $data['image'] = '';
            }

            // 2. Hidden input'tan gelen öne çıkan görsel (picker ile seçildi)
            if (!empty($_POST['image'])) {
                $data['image'] = trim($_POST['image']);
            }

            // 3. Yeni yükleme varsa onu kullan
            if (!empty($_FILES['image_file']['name'])) {
                try {
                    $mediaService  = new MediaService(new MediaRepository());
                    $result        = $mediaService->upload($_FILES['image_file'], (int)($_SESSION['user_id'] ?? 0));
                    $data['image'] = $result['url'];
                } catch (\Throwable $e) {
                    $errors[] = 'Görsel: ' . $e->getMessage();
                }
            }

            if (empty($errors)) {
                $repo->update($sid, $data);
                $_SESSION['flash'] = '✅ Haber güncellendi.';
                header('Location: /public/admin/news');
                exit;
            }
        }

        $pageTitle = 'Haber Düzenle';
        $active    = 'news';

        ob_start();
        require __DIR__ . '/../Views/admin/news-form.html.php';
        $content = ob_get_clean();
        require __DIR__ . '/../Views/admin/layout.html.php';
    }

    public function newsDelete(): void
    {
        $sid  = (int)($_GET['sid'] ?? 0);
        $repo = new NewsRepository();
        $repo->delete($sid);

        $_SESSION['flash'] = '🗑️ Haber silindi.';
        header('Location: /public/admin/news');
        exit;
    }
}