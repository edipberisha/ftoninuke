<?php

declare(strict_types=1);

namespace FtoniNuke\Service;

use FtoniNuke\Repository\MediaRepository;
use FtoniNuke\Helper\SlugHelper;
use RuntimeException;

/**
 * MediaService - Handles file uploads, directory structuring and image processing logic.
 */
class MediaService
{
    private MediaRepository $mediaRepo;
    private string $uploadBaseDir;

    public function __construct(MediaRepository $mediaRepo)
    {
        $this->mediaRepo = $mediaRepo;
        $this->uploadBaseDir = __DIR__ . '/../../public/uploads';
    }

    /**
     * Upload a file and record metadata
     */
    public function upload(array $file, int $uploaderId): array
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new RuntimeException("File upload failed with error code: " . $file['error']);
        }

        // 1. Prepare Directory Structure (uploads/year/month/)
        $year = date('Y');
        $month = date('m');
        $relativeDir = "{$year}/{$month}";
        $absoluteDir = "{$this->uploadBaseDir}/{$relativeDir}";

        if (!is_dir($absoluteDir)) {
            mkdir($absoluteDir, 0755, true);
        }

        // 2. Clean Filename
        $info = pathinfo($file['name']);
        $cleanName = SlugHelper::slugify($info['filename']) . '.' . strtolower($info['extension']);
        
        // Prevent collisions
        $targetPath = "{$absoluteDir}/{$cleanName}";
        if (file_exists($targetPath)) {
            $cleanName = time() . '-' . $cleanName;
            $targetPath = "{$absoluteDir}/{$cleanName}";
        }

        // 3. Move File
        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            throw new RuntimeException("Failed to move uploaded file.");
        }

        // 4. Save to DB
        $relativeFilePath = "/public/uploads/{$relativeDir}/{$cleanName}";
        $mediaId = $this->mediaRepo->save([
            'filename'    => $cleanName,
            'path'        => $relativeFilePath,
            'mime_type'   => $file['type'],
            'size'        => $file['size'],
            'uploader_id' => $uploaderId
        ]);

        return [
            'id'   => $mediaId,
            'url'  => $relativeFilePath,
            'name' => $cleanName
        ];
    }

    /**
     * Delete both file and database record
     */
    public function delete(int $id): bool
    {
        $media = $this->mediaRepo->findById($id);
        if (!$media) return false;

        $absolutePath = __DIR__ . '/../../public' . $media['path'];
        if (file_exists($absolutePath)) {
            unlink($absolutePath);
        }

        return $this->mediaRepo->delete($id);
    }
}
