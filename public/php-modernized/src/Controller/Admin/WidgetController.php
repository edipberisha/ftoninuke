<?php

declare(strict_types=1);

namespace FtoniNuke\Controller\Admin;

use FtoniNuke\Security\Auth;
use FtoniNuke\Service\BlockManager;
use FtoniNuke\Repository\BlockRepository;

/**
 * WidgetController - Admin interface for managing widgets and layouts.
 */
class WidgetController
{
    private Auth $auth;
    private BlockManager $blockManager;
    private BlockRepository $blockRepo;

    public function __construct(Auth $auth, BlockManager $blockManager, BlockRepository $blockRepo)
    {
        $this->auth = $auth;
        $this->blockManager = $blockManager;
        $this->blockRepo = $blockRepo;

        if (!$this->auth->isAuthenticated() || (int)$this->auth->getCurrentUser()['user_level'] < 2) {
            header('Location: /login.php');
            exit;
        }
    }

    /**
     * Layout & Widget Manager UI
     */
    public function index(): void
    {
        $availableWidgets = $this->blockManager->getAvailableWidgets();
        $activeBlocks = $this->blockRepo->getActive();
        
        // Group by position
        $layout = [
            'l' => [], // Left
            'c' => [], // Center
            'r' => [], // Right
            'd' => []  // Center Down
        ];

        foreach ($activeBlocks as $block) {
            $layout[$block['bposition']][] = $block;
        }

        require_once __DIR__ . '/../../Views/admin/widgets/index.html.php';
    }
}
