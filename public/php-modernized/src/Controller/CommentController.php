<?php

declare(strict_types=1);

namespace FtoniNuke\Controller;

use FtoniNuke\Service\CommentService;

class CommentController
{
    /**
     * POST /public/comment/submit — Yeni yorum
     */
    public function submit(): void
    {
        // Sadece POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /public/');
            exit;
        }

        $service = new CommentService();
        $ip      = $this->getClientIp();

        $result = $service->submit(
            $_POST,
            $ip,
            (int)($_SESSION['user_id'] ?? 0),
            (string)($_SESSION['username'] ?? '')
        );

        $sid = (int)($_POST['sid'] ?? 0);

        if ($result['ok']) {
            $_SESSION['comment_flash'] = ['type' => 'success', 'msg' => '✅ Yorumunuz onay için gönderildi.'];
        } else {
            $_SESSION['comment_flash'] = ['type' => 'error', 'msg' => '❌ ' . $result['error']];
            // Form verisini geri koy (kullanıcı yeniden yazmasın)
            $_SESSION['comment_old'] = [
                'author_name'  => $_POST['author_name'] ?? '',
                'author_email' => $_POST['author_email'] ?? '',
                'content'      => $_POST['content'] ?? '',
            ];
        }

        header('Location: /public/?sid=' . $sid . '#comments');
        exit;
    }

    /**
     * AJAX: CAPTCHA yenile
     */
    public function refreshCaptcha(): void
    {
        header('Content-Type: application/json');
        $service = new CommentService();
        echo json_encode($service->generateCaptcha());
        exit;
    }

    private function getClientIp(): string
    {
        // Proxy arkasındaysa
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            return trim($ips[0]);
        }
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
}
