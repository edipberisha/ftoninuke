#!/usr/bin/env php
<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use FtoniNuke\Core\Migrator;

$cmd = $argv[1] ?? 'status';
$migrator = new Migrator();

echo "🚀 FtoniNuke Migrator" . PHP_EOL;
echo str_repeat('─', 50) . PHP_EOL;

switch ($cmd) {
    case 'migrate':
        $result = $migrator->migrate();
        if ($result['status'] === 'empty') {
            echo 'ℹ️  ' . $result['message'] . PHP_EOL;
            break;
        }
        echo '📦 Batch: ' . $result['batch'] . PHP_EOL;
        foreach ($result['results'] as $r) {
            $icon = $r['status'] === 'ok' ? '✅' : '❌';
            echo "  {$icon} {$r['name']}";
            if (isset($r['message'])) echo " — {$r['message']}";
            echo PHP_EOL;
        }
        break;

    case 'rollback':
        $result = $migrator->rollback();
        if ($result['status'] === 'empty') {
            echo 'ℹ️  ' . $result['message'] . PHP_EOL;
            break;
        }
        echo '↩️  Batch geri alınıyor: ' . $result['batch'] . PHP_EOL;
        foreach ($result['results'] as $r) {
            $icon = $r['status'] === 'ok' ? '✅' : '❌';
            echo "  {$icon} {$r['name']}";
            if (isset($r['message'])) echo " — {$r['message']}";
            echo PHP_EOL;
        }
        break;

    case 'status':
    default:
        $rows = $migrator->status();
        if (empty($rows)) {
            echo 'ℹ️  Migration dosyası yok.' . PHP_EOL;
            break;
        }
        foreach ($rows as $r) {
            $icon = $r['executed'] ? '✅' : '⏳';
            echo "  {$icon} {$r['name']}" . PHP_EOL;
        }
        echo PHP_EOL . 'Legend: ✅ uygulandı, ⏳ bekliyor' . PHP_EOL;
        break;
}
