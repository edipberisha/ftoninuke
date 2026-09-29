<?php

declare(strict_types=1);

namespace FtoniNuke\Service;

use FtoniNuke\Repository\CommentRepository;

class CommentService
{
    private CommentRepository $repo;

    // Rate limit: IP başına saatte max yorum
    private const RATE_LIMIT_PER_HOUR = 5;

    // Honeypot alan adı
    private const HONEYPOT_FIELD = 'website_url';

    // Form dolum süresi (saniye)
    private const MIN_FILL_TIME = 3;

    // Captcha session anahtarı
    private const CAPTCHA_KEY = 'comment_captcha';

    public function __construct()
    {
        $this->repo = new CommentRepository();
    }

    /**
     * Yeni yorum kaydet (tüm güvenlik kontrolleri ile)
     * @return array{ok: bool, id?: int, error?: string}
     */
    public function submit(array $post, string $ip, int $userId = 0, string $userName = ''): array
    {
        // ─── Katman 1: Honeypot ───────────────────────
        if (!empty($post[self::HONEYPOT_FIELD])) {
            return ['ok' => false, 'error' => 'Spam tespit edildi.'];
        }

        // ─── Katman 2: Zaman kontrolü ─────────────────
        $startedAt = (int)($post['form_started_at'] ?? 0);
        if ($startedAt === 0 || (time() - $startedAt) < self::MIN_FILL_TIME) {
            return ['ok' => false, 'error' => 'Form çok hızlı gönderildi. Lütfen tekrar deneyin.'];
        }

        // ─── Katman 3: Matematik CAPTCHA ──────────────
        $captchaAnswer = (int)($post['captcha_answer'] ?? -1);
        $captchaExpected = (int)($_SESSION[self::CAPTCHA_KEY] ?? -999);
        if ($captchaAnswer !== $captchaExpected) {
            unset($_SESSION[self::CAPTCHA_KEY]);
            return ['ok' => false, 'error' => 'Güvenlik kodu hatalı.'];
        }
        unset($_SESSION[self::CAPTCHA_KEY]); // Tek kullanımlık

        // ─── Katman 4: Rate limit ─────────────────────
        if ($this->repo->countByIp($ip, 60) >= self::RATE_LIMIT_PER_HOUR) {
            return ['ok' => false, 'error' => 'Çok fazla yorum gönderdiniz. Lütfen 1 saat sonra tekrar deneyin.'];
        }

        // ─── Ban kontrolü ─────────────────────────────
        if ($this->repo->isBanned($ip)) {
            return ['ok' => false, 'error' => 'Bu IP adresi yasaklı.'];
        }

        // ─── Veri validasyonu ─────────────────────────
        $sid         = (int)($post['sid'] ?? 0);
        $pid         = (int)($post['pid'] ?? 0);
        $content     = trim($post['content'] ?? '');
        $guestName   = trim($post['author_name'] ?? '');
        $guestEmail  = trim($post['author_email'] ?? '');

        if ($sid <= 0) {
            return ['ok' => false, 'error' => 'Geçersiz haber.'];
        }
        if (mb_strlen($content) < 5) {
            return ['ok' => false, 'error' => 'Yorum en az 5 karakter olmalı.'];
        }
        if (mb_strlen($content) > 3000) {
            return ['ok' => false, 'error' => 'Yorum 3000 karakterden uzun olamaz.'];
        }

        // Kullanıcı yoksa misafir kontrolü
        if ($userId === 0) {
            if (mb_strlen($guestName) < 2) {
                return ['ok' => false, 'error' => 'Adınızı girin (en az 2 karakter).'];
            }
            if ($guestEmail !== '' && !filter_var($guestEmail, FILTER_VALIDATE_EMAIL)) {
                return ['ok' => false, 'error' => 'Geçersiz e-posta adresi.'];
            }
        }

        // Aynı içerik tekrarı
        if ($this->repo->isDuplicate($content, $ip, 10)) {
            return ['ok' => false, 'error' => 'Aynı yorumu kısa süre önce gönderdiniz.'];
        }

        // ─── İçeriği temizle (XSS) ────────────────────
        $cleanContent = $this->sanitize($content);

        // ─── Kaydet ───────────────────────────────────
        $authorName = $userId > 0 ? $userName : $guestName;

        $cid = $this->repo->create([
            'sid'          => $sid,
            'pid'          => $pid,
            'author_name'  => $authorName,
            'author_email' => $guestEmail,
            'author_id'    => $userId,
            'author_ip'    => $ip,
            'content'      => $cleanContent,
            'active'       => 0, // Onay bekliyor
        ]);

        return ['ok' => true, 'id' => $cid];
    }

    /**
     * Yeni CAPTCHA üret (session'a kaydet)
     * @return array{question: string, hash: string}
     */
    public function generateCaptcha(): array
    {
        $a = random_int(2, 9);
        $b = random_int(2, 9);
        $_SESSION[self::CAPTCHA_KEY] = $a + $b;
        return [
            'question' => "$a + $b = ?",
            'hash'     => bin2hex(random_bytes(8)),
        ];
    }

    /**
     * İçeriği güvenli hale getir (XSS önleme)
     */
    private function sanitize(string $content): string
    {
        // HTML etiketlerini kaçır
        $content = htmlspecialchars($content, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        // Çok fazla boş satırı temizle
        $content = preg_replace("/\n{3,}/", "\n\n", $content);
        return trim($content);
    }

    // ─── Getter'lar ───────────────────────────────────
    public function getRepo(): CommentRepository
    {
        return $this->repo;
    }

    public function getHoneypotField(): string
    {
        return self::HONEYPOT_FIELD;
    }
}
