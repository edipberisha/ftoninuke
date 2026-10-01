<?php

declare(strict_types=1);

namespace FtoniNuke\Service;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class MailService
{
    private SettingsService $settings;

    public function __construct()
    {
        $this->settings = new SettingsService();
    }

    /**
     * SMTP ayarları yapılmış mı?
     */
    public function isConfigured(): bool
    {
        return !empty($this->settings->get('smtp_host'))
            && !empty($this->settings->get('smtp_user'));
    }

    /**
     * Mail gönder
     */
    public function send(string $to, string $subject, string $htmlBody): bool
    {
        if (!class_exists(PHPMailer::class)) {
            // PHPMailer yoksa native mail() dene
            $headers  = "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
            $headers .= "From: " . $this->settings->get('smtp_from_name', 'FtoniNuke')
                     . " <" . $this->settings->get('smtp_from_email', 'noreply@localhost') . ">\r\n";
            return @mail($to, $subject, $htmlBody, $headers);
        }

        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host       = $this->settings->get('smtp_host');
            $mail->SMTPAuth   = true;
            $mail->Username   = $this->settings->get('smtp_user');
            $mail->Password   = $this->settings->get('smtp_pass');
            $mail->SMTPSecure = $this->settings->get('smtp_secure', 'tls');
            $mail->Port       = (int)$this->settings->get('smtp_port', 587);
            $mail->CharSet    = 'UTF-8';

            $mail->setFrom(
                $this->settings->get('smtp_from_email', 'noreply@localhost'),
                $this->settings->get('smtp_from_name', 'FtoniNuke')
            );
            $mail->addAddress($to);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $htmlBody;

            return $mail->send();
        } catch (\Throwable $e) {
            throw new \RuntimeException('Mail gönderilemedi: ' . $e->getMessage());
        }
    }

    /**
     * İletişim formu bildirimi (admin'e)
     */
    public function sendContactNotification(string $name, string $email, string $subject, string $message): bool
    {
        $adminEmail = $this->settings->get('contact_email')
                   ?: $this->settings->get('smtp_from_email', '');

        if (empty($adminEmail)) return false;

        $siteName = $this->settings->get('sitename', 'FtoniNuke');
        $subjectLine = '[' . $siteName . '] İletişim Formu: ' . ($subject ?: 'Yeni Mesaj');

        $html = '
            <h2>İletişim Formu Mesajı</h2>
            <p><strong>Ad:</strong> ' . htmlspecialchars($name) . '</p>
            <p><strong>E-posta:</strong> ' . htmlspecialchars($email) . '</p>
            <p><strong>Konu:</strong> ' . htmlspecialchars($subject) . '</p>
            <hr>
            <p><strong>Mesaj:</strong></p>
            <p>' . nl2br(htmlspecialchars($message)) . '</p>
            <hr>
            <p><small>Gönderim: ' . date('d.m.Y H:i') . '</small></p>
        ';

        return $this->send($adminEmail, $subjectLine, $html);
    }
}
