<?php

declare(strict_types=1);

namespace FtoniNuke\Controller\Api;

use FtoniNuke\Repository\MediaRepository;

class MediaController
{
    public function list(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        if (empty($_SESSION['user_id']) || ($_SESSION['user_level'] ?? 0) < 2) {
            http_response_code(403);
            echo json_encode(['error' => 'Yetkisiz erişim']);
            return;
        }

        try {
            $repo = new MediaRepository();
            $rows = $repo->getAll(200, 0);
            echo json_encode(['files' => $rows]);
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}
