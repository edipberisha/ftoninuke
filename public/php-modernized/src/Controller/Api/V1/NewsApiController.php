<?php

declare(strict_types=1);

namespace FtoniNuke\Controller\Api\V1;

use FtoniNuke\Service\ContentManager;
use FtoniNuke\Repository\NewsRepository;
use FtoniNuke\Middleware\ApiMiddleware;

/**
 * NewsApiController - Exposes news data via JSON for mobile and IoT devices.
 */
class NewsApiController
{
    private ContentManager $contentManager;
    private ApiMiddleware $middleware;

    public function __construct(ContentManager $contentManager)
    {
        $this->contentManager = $contentManager;
        $this->middleware = new ApiMiddleware();
        
        // Execute security checks
        $this->middleware->handle();
    }

    /**
     * GET /api/v1/news
     */
    public function list(): void
    {
        header('Content-Type: application/json');
        
        try {
            $news = $this->contentManager->getHomepageNews();
            echo json_encode([
                'status' => 'success',
                'data'   => $news,
                'meta'   => [
                    'count' => count($news),
                    'api_version' => '1.0'
                ]
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Internal Server Error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * GET /api/v1/news/{id}
     */
    public function get(int $id): void
    {
        header('Content-Type: application/json');
        
        $story = $this->contentManager->viewStory($id);
        
        if ($story) {
            echo json_encode(['status' => 'success', 'data' => $story]);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Not Found', 'message' => 'Article not found']);
        }
    }
}
