<?php

declare(strict_types=1);

namespace FtoniNuke\Service\Import;

use FtoniNuke\Core\Database;
use PDO;
use PDOException;

/**
 * Import Service — Abstract Base
 *
 * Tüm importer'lar için ortak altyapı.
 * - Kaynak DB bağlantısı
 * - Dry-run modu
 * - İstatistik toplama
 * - Import log
 */
abstract class ImportService
{
    protected ?PDO $sourceDb = null;
    protected Database $targetDb;
    protected bool $dryRun = false;
    protected string $sourceName = 'unknown';
    protected array $stats = [];
    protected array $errors = [];
    protected array $warnings = [];
    protected int $importLogId = 0;

    public function __construct()
    {
        $this->targetDb = Database::getInstance();
    }

    /**
     * Kaynak veritabanına bağlan
     */
    public function connect(array $config): bool
    {
        $host    = $config['host'] ?? '';
        $name    = $config['name'] ?? '';
        $user    = $config['user'] ?? '';
        $pass    = $config['pass'] ?? '';
        $port    = (int)($config['port'] ?? 3306);

        if ($host === '' || $name === '' || $user === '') {
            throw new \RuntimeException('Bağlantı bilgileri eksik.');
        }

        $dsn = "mysql:host=$host;dbname=$name;port=$port;charset=utf8mb4";

        try {
            $this->sourceDb = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
            return true;
        } catch (PDOException $e) {
            throw new \RuntimeException('Kaynak DB bağlantı hatası: ' . $e->getMessage());
        }
    }

    /**
     * Kaynak DB'de tablo var mı?
     */
    public function tableExists(string $table): bool
    {
        if (!$this->sourceDb) return false;
        try {
            $stmt = $this->sourceDb->prepare("SHOW TABLES LIKE ?");
            $stmt->execute([$table]);
            return $stmt->fetch() !== false;
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Kaynak DB'de kolon var mı?
     */
    protected function columnExists(string $table, string $column): bool
    {
        if (!$this->sourceDb) return false;
        try {
            $stmt = $this->sourceDb->prepare("SHOW COLUMNS FROM `$table` LIKE ?");
            $stmt->execute([$column]);
            return $stmt->fetch() !== false;
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Dry-run modu
     */
    public function setDryRun(bool $dry): void
    {
        $this->dryRun = $dry;
    }

    public function isDryRun(): bool
    {
        return $this->dryRun;
    }

    /**
     * İstatistik başlat
     */
    protected function startStep(string $step): void
    {
        $this->stats[$step] = [
            'total'    => 0,
            'imported' => 0,
            'skipped'  => 0,
            'failed'   => 0,
            'started'  => time(),
            'finished' => 0,
        ];
    }

    /**
     * İstatistik güncelle
     */
    protected function updateStats(string $step, string $key, int $count = 1): void
    {
        if (!isset($this->stats[$step])) {
            $this->startStep($step);
        }
        $this->stats[$step][$key] += $count;
    }

    /**
     * Adımı bitir
     */
    protected function finishStep(string $step): void
    {
        if (isset($this->stats[$step])) {
            $this->stats[$step]['finished'] = time();
        }
    }

    /**
     * Import log kaydı oluştur
     */
    protected function logStart(string $step, int $total = 0): void
    {
        if ($this->dryRun) return;

        $this->targetDb->query(
            "INSERT INTO nuke_import_log (source, step, total, dry_run, started_at, status)
             VALUES (?, ?, ?, 0, ?, 'running')",
            [$this->sourceName, $step, $total, time()]
        );
        $this->importLogId = (int)$this->targetDb->getConnection()->lastInsertId();
    }

    /**
     * Import log güncelle
     */
    protected function logFinish(string $step, string $status = 'done'): void
    {
        if ($this->dryRun || $this->importLogId === 0) return;

        $s = $this->stats[$step] ?? [];
        $this->targetDb->query(
            "UPDATE nuke_import_log
             SET imported = ?, skipped = ?, failed = ?, finished_at = ?, status = ?, details = ?
             WHERE id = ?",
            [
                $s['imported'] ?? 0,
                $s['skipped'] ?? 0,
                $s['failed'] ?? 0,
                time(),
                $status,
                json_encode($this->errors),
                $this->importLogId,
            ]
        );
        $this->importLogId = 0;
    }

    /**
     * Sonuç raporu
     */
    public function getReport(): array
    {
        return [
            'source'   => $this->sourceName,
            'dry_run'  => $this->dryRun,
            'stats'    => $this->stats,
            'errors'   => $this->errors,
            'warnings' => $this->warnings,
        ];
    }

    public function getStats(): array
    {
        return $this->stats;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getWarnings(): array
    {
        return $this->warnings;
    }

    // ─── Abstract metodlar ─────────────────────────────
    abstract public function importCategories(): array;
    abstract public function importUsers(): array;
    abstract public function importNews(): array;
    abstract public function importComments(): array;

    /**
     * Tüm adımları sırayla çalıştır
     */
    public function importAll(array $steps = ['categories', 'users', 'news', 'comments']): array
    {
        $results = [];

        foreach ($steps as $step) {
            $method = 'import' . ucfirst($step);
            if (method_exists($this, $method)) {
                try {
                    $results[$step] = $this->$method();
                } catch (\Throwable $e) {
                    $this->errors[] = "$step: " . $e->getMessage();
                    $results[$step] = ['error' => $e->getMessage()];
                }
            }
        }

        return $results;
    }
}
