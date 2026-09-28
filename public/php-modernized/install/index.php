<?php

declare(strict_types=1);

/**
 * Installer Entry Point
 */

require_once __DIR__ . '/../vendor/autoload.php';

use FtoniNuke\Controller\InstallController;

$controller = new InstallController();
$step = (int)($_GET['step'] ?? 0);

switch ($step) {
    case 0:
        $controller->step0();
        break;
    case 1:
        $controller->step1();
        break;
    case 2:
        $controller->step2();
        break;
    case 3:
        $controller->step3();
        break;
    case 4:
        $controller->step4();
        break;
    case 5:
        $controller->step5();
        break;
    default:
        $controller->step1();
        break;
}
