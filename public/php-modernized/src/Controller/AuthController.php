<?php

declare(strict_types=1);

namespace FtoniNuke\Controller;

use FtoniNuke\Core\Database;

class AuthController
{
    public function showLogin(): void
    {
        if (!empty($_SESSION['user_id'])) {
            header('Location: /public/admin');
            exit;
        }
        $error = $_SESSION['login_error'] ?? null;
        unset($_SESSION['login_error']);
        require __DIR__ . '/../Views/login.html.php';
    }

    public function login(): void
    {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($username === '' || $password === '') {
            $_SESSION['login_error'] = 'Kullanıcı adı ve şifre gerekli.';
            header('Location: /public/login');
            exit;
        }

        try {
            $db = Database::getInstance();
            $user = $db->fetch(
                "SELECT user_id, username, user_password, user_email, user_level
                 FROM nuke_users WHERE username = ? LIMIT 1",
                [$username]
            );

            if (!$user || !password_verify($password, $user['user_password'])) {
                $_SESSION['login_error'] = 'Kullanıcı adı veya şifre hatalı.';
                header('Location: /public/login');
                exit;
            }

            // Başarılı giriş
            $_SESSION['user_id']    = (int)$user['user_id'];   // ← DÜZELTİLDİ
            $_SESSION['username']   = $user['username'];
            $_SESSION['user_level'] = (int)$user['user_level'];

            header('Location: /public/admin');
            exit;
        } catch (\Throwable $e) {
            $_SESSION['login_error'] = 'Sistem hatası: ' . $e->getMessage();
            header('Location: /public/login');
            exit;
        }
    }

    public function logout(): void
    {
        $_SESSION = [];
        session_destroy();
        header('Location: /public/login');
        exit;
    }

    public function showRegister(): void
    {
        $settings = new \FtoniNuke\Service\SettingsService();

        if (!$settings->isRegistrationEnabled()) {
            http_response_code(404);
            echo '<h1>404 — Kayıt kapalı</h1>';
            echo '<p><a href="/public/">Ana sayfaya dön</a></p>';
            return;
        }

        if (!empty($_SESSION['user_id'])) {
            header('Location: /public/');
            exit;
        }

        $error = $_SESSION['register_error'] ?? null;
        $old   = $_SESSION['register_old'] ?? [];
        unset($_SESSION['register_error'], $_SESSION['register_old']);

        $siteName = 'FtoniNuke';

        require __DIR__ . '/../Views/register.html.php';
    }

    public function register(): void
    {
        $settings = new \FtoniNuke\Service\SettingsService();
        if (!$settings->isRegistrationEnabled()) {
            header('Location: /public/');
            exit;
        }

        if (!empty($_POST['website_url'])) {
            header('Location: /public/register');
            exit;
        }

        $username  = trim($_POST['username'] ?? '');
        $email     = trim($_POST['user_email'] ?? '');
        $password  = $_POST['password'] ?? '';
        $password2 = $_POST['password_confirm'] ?? '';

        if ($password !== $password2) {
            $_SESSION['register_error'] = 'Şifreler eşleşmiyor.';
            $_SESSION['register_old'] = ['username' => $username, 'user_email' => $email];
            header('Location: /public/register');
            exit;
        }

        $service = new \FtoniNuke\Service\UserService();
        $result = $service->create([
            'username'   => $username,
            'user_email' => $email,
            'password'   => $password,
            'user_level' => $settings->int('default_user_level', 0),
        ]);

        if ($result['ok']) {
            $_SESSION['login_error'] = '✅ Kayıt başarılı! Şimdi giriş yapabilirsiniz.';
            header('Location: /public/login');
            exit;
        }

        $_SESSION['register_error'] = $result['error'];
        $_SESSION['register_old'] = ['username' => $username, 'user_email' => $email];
        header('Location: /public/register');
        exit;
    }

}
