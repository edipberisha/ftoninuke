<?php

declare(strict_types=1);

namespace FtoniNuke\Controller;

use FtoniNuke\Repository\PageRepository;
use FtoniNuke\Core\Database;

class PageController
{
    public function show(string $slug): void
    {
        $repo = new PageRepository();
        $page = $repo->findBySlug($slug);

        if (!$page) {
            http_response_code(404);
            echo '<h1>404 — Sayfa bulunamadı</h1>';
            echo '<p><a href="/public/">Ana sayfaya dön</a></p>';
            return;
        }

        // Site adı
        try {
            $db = Database::getInstance();
            $config = $db->fetch("SELECT sitename FROM nuke_config LIMIT 1");
            $siteName = $config['sitename'] ?? 'FtoniNuke';
        } catch (\Throwable $e) {
            $siteName = 'FtoniNuke';
        }

        // Template seç
        $template = $page['template'] ?? 'default';
        $templateFile = match ($template) {
            'contact' => 'page-contact.html.php',
            'full'    => 'page-full.html.php',
            default   => 'page.html.php',
        };

        $path = __DIR__ . '/../Views/' . $templateFile;
        if (!file_exists($path)) {
            $path = __DIR__ . '/../Views/page.html.php';
        }

        require $path;
    }

    /**
     * İletişim formu POST
     */
    public function contactSubmit(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /public/iletisim');
            exit;
        }

        // Honeypot
        if (!empty($_POST['website_url'])) {
            header('Location: /public/iletisim');
            exit;
        }

        $name    = trim($_POST['name'] ?? '');
        $email   = trim($_POST['email'] ?? '');
        $subject = trim($_POST['subject'] ?? '');
        $message = trim($_POST['message'] ?? '');

        $errors = [];
        if (mb_strlen($name) < 2) $errors[] = 'Adınızı girin.';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Geçerli e-posta girin.';
        if (mb_strlen($message) < 10) $errors[] = 'Mesaj en az 10 karakter olmalı.';

        if (!empty($errors)) {
            $_SESSION['contact_flash'] = ['type' => 'error', 'msg' => implode(' ', $errors)];
            $_SESSION['contact_old'] = ['name' => $name, 'email' => $email, 'subject' => $subject, 'message' => $message];
            header('Location: /public/iletisim');
            exit;
        }

        // IP
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        if (strpos($ip, ',') !== false) {
            $ip = trim(explode(',', $ip)[0]);
        }

        try {
            $repo = new PageRepository();
            $msgId = $repo->saveContactMessage([
                'name' => $name,
                'email' => $email,
                'subject' => $subject,
                'message' => $message,
                'ip_address' => $ip,
            ]);

            // Mail gönder (ayarlar varsa)
            try {
                $mailer = new \FtoniNuke\Service\MailService();
                $mailer->sendContactNotification($name, $email, $subject, $message);
            } catch (\Throwable $e) {
                // Mail hatası sessizce geç — mesaj DB'de kayıtlı
            }

            $_SESSION['contact_flash'] = ['type' => 'success', 'msg' => '✅ Mesajınız gönderildi. En kısa sürede dönüş yapacağız.'];
        } catch (\Throwable $e) {
            $_SESSION['contact_flash'] = ['type' => 'error', 'msg' => 'Bir hata oluştu: ' . $e->getMessage()];
        }

        header('Location: /public/iletisim');
        exit;
    }
}
