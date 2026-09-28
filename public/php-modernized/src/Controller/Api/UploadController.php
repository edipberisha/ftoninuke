<?php

declare(strict_types=1);

namespace FtoniNuke\Controller\Api;

use FtoniNuke\Repository\MediaRepository;
use FtoniNuke\Service\MediaService;

class UploadController
{
    public function image(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        if (empty($_SESSION['user_id']) || ($_SESSION['user_level'] ?? 0) < 2) {
            http_response_code(403);
            echo json_encode(['error' => 'Yetkisiz erişim']);
            return;
        }

        if (empty($_FILES['file'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Dosya gönderilmedi']);
            return;
        }

        try {
            $service = new MediaService(new MediaRepository());
            $result  = $service->upload($_FILES['file'], (int)$_SESSION['user_id']);
            echo json_encode([
                'location' => $result['url'],
                'id'       => $result['id'],
            ]);
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}
