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
}