<?php

declare(strict_types=1);

namespace FtoniNuke\Controller;

use FtoniNuke\Service\MigrationService;
use FtoniNuke\Service\LanguageManager;
use FtoniNuke\Core\Database;
use RuntimeException;

/**
 * InstallController - Manages the multi-step WordPress-style setup wizard.
 */
class InstallController
{
    private string $lockFile;
    private string $configPath;
    private string $appConfigPath;

    public function __construct()
    {
        $this->lockFile = __DIR__ . '/../../install/installed.lock';
        $this->configPath = __DIR__ . '/../../config/database.php';
        $this->appConfigPath = __DIR__ . '/../../config/app.php';

        if (file_exists($this->lockFile)) {
            die("FtoniNuke is already installed. Remove 'install/installed.lock' to reinstall.");
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Initialize Languages
        require_once __DIR__ . '/../Helper/lang_helpers.php';
        LanguageManager::init(__DIR__ . '/../../lang');
    }

    /**
     * Step 0: Language Selection
     */
    public function step0(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $locale = $_POST['locale'] ?? 'en';
            $_SESSION['setup_locale'] = $locale;
            header('Location: ?step=1');
            exit;
        }

        $languages = [
            'tr' => 'Türkçe',
            'en' => 'English',
            'sq' => 'Shqip',
            'de' => 'Deutsch',
            'fr' => 'Français',
            'it' => 'Italiano',
            'es' => 'Español'
        ];

        require_once __DIR__ . '/../Views/install/step0.html.php';
    }

    /**
     * Step 1: Pre-Flight Requirements Check
     */
    public function step1(): void
    {
        $checks = [
            'php_version' => version_compare(PHP_VERSION, '8.1.0', '>='),
            'pdo_mysql'   => extension_loaded('pdo_mysql'),
            'json'        => extension_loaded('json'),
            'config_writable'  => is_writable(dirname($this->configPath)),
            'uploads_writable' => is_writable(__DIR__ . '/../../public/uploads'),
            'cache_writable'   => is_writable(__DIR__ . '/../../cache'),
        ];

        $allGood = !in_array(false, $checks, true);
        
        require_once __DIR__ . '/../Views/install/step1.html.php';
    }

    /**
     * Step 2: Database Setup
     */
    public function step2(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $host = $_POST['db_host'] ?? 'localhost';
            $name = $_POST['db_name'] ?? '';
            $user = $_POST['db_user'] ?? '';
            $pass = $_POST['db_pass'] ?? '';

            try {
                // Test Connection
                $dsn = "mysql:host=$host;dbname=$name;charset=utf8mb4";
                new \PDO($dsn, $user, $pass, [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]);

                // Write Config
                $configContent = "<?php\n\nreturn [\n    'host' => '$host',\n    'dbname' => '$name',\n    'user' => '$user',\n    'pass' => '$pass',\n    'prefix' => 'nuke_',\n];\n";
                file_put_contents($this->configPath, $configContent);

                header('Location: ?step=3');
                exit;
            } catch (\PDOException $e) {
                $error = "Connection Failed: " . $e->getMessage();
            }
        }

        require_once __DIR__ . '/../Views/install/step2.html.php';
    }

    /**
     * Step 3: Database Migrations
     */
    public function step3(): void
    {
        try {
            $migrator = new MigrationService();
            $migrator->setDatabase(Database::getInstance());
            $migrator->run();
            
            header('Location: ?step=4');
            exit;
        } catch (\Exception $e) {
            $error = $e->getMessage();
            require_once __DIR__ . '/../Views/install/error.html.php';
        }
    }

    /**
     * Step 4: Admin Setup
     */
    public function step4(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $adminUser = $_POST['username'] ?? '';
            $adminPass = password_hash($_POST['password'] ?? '', PASSWORD_BCRYPT);
            $adminEmail = $_POST['email'] ?? '';
            $siteName = $_POST['sitename'] ?? 'FtoniNuke Portal';

            $db = Database::getInstance();
            $prefix = 'nuke_';

            // Insert Admin
            $db->query("INSERT INTO {$prefix}users (username, user_password, user_email, user_level, created_at) VALUES (?, ?, ?, ?, ?)", [
                $adminUser, $adminPass, $adminEmail, 2, time()
            ]);

            // Update Config
            $db->query("INSERT INTO {$prefix}config (sitename, theme, api_key) VALUES (?, ?, ?)", [
                $siteName, 'ftoni_modern', bin2hex(random_bytes(16))
            ]);

            header('Location: ?step=5');
            exit;
        }

        require_once __DIR__ . '/../Views/install/step4.html.php';
    }

    /**
     * Step 5: Finalize
     */
    public function step5(): void
    {
        // Persist Locale
        $locale = $_SESSION['setup_locale'] ?? 'en';
        $appConfigContent = "<?php\n\nreturn [\n    'locale' => '$locale',\n    'version' => '1.4',\n];\n";
        file_put_contents($this->appConfigPath, $appConfigContent);

        file_put_contents($this->lockFile, date('Y-m-d H:i:s'));
        require_once __DIR__ . '/../Views/install/step5.html.php';
    }
}
