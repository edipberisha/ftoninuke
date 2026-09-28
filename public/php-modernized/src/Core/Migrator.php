<?php

declare(strict_types=1);

namespace FtoniNuke\Core;

class Migrator
{
    private Database $db;
    private string $migrationsDir;
    private string $table = 'nuke_migrations';

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->migrationsDir = __DIR__ . '/../../migrations';
    }

    public function migrate(): array
    {
        $executed = $this->getExecuted();
        $all      = $this->getAllMigrations();
        $pending  = array_diff($all, $executed);

        if (empty($pending)) {
            return ['status' => 'empty', 'message' => 'Çalıştırılacak migration yok.'];
        }

        $batch = $this->getNextBatch();
        $results = [];

        foreach ($pending as $name) {
            $file = $this->migrationsDir . '/' . $name . '.php';
            if (!file_exists($file)) continue;

            $migration = require $file;
            if (!is_array($migration) || !isset($migration['up'])) {
                $results[] = ['name' => $name, 'status' => 'error', 'message' => 'Geçersiz migration dosyası'];
                continue;
            }

            try {
                $migration['up']($this->db);
                $this->markExecuted($name, $batch);
                $results[] = ['name' => $name, 'status' => 'ok'];
            } catch (\Throwable $e) {
                $results[] = ['name' => $name, 'status' => 'error', 'message' => $e->getMessage()];
                break;
            }
        }

        return ['status' => 'done', 'batch' => $batch, 'results' => $results];
    }

    public function rollback(): array
    {
        $lastBatch = $this->getLastBatch();
        if ($lastBatch === 0) {
            return ['status' => 'empty', 'message' => 'Geri alınacak batch yok.'];
        }

        $migrations = $this->db->fetchAll(
            "SELECT migration FROM {$this->table} WHERE batch = ? ORDER BY id DESC",
            [$lastBatch]
        );

        $results = [];
        foreach ($migrations as $row) {
            $name = $row['migration'];
            $file = $this->migrationsDir . '/' . $name . '.php';
            if (!file_exists($file)) continue;

            $migration = require $file;
            if (!is_array($migration) || !isset($migration['down'])) {
                $results[] = ['name' => $name, 'status' => 'error', 'message' => 'down() metodu yok'];
                continue;
            }

            try {
                $migration['down']($this->db);
                $this->db->query("DELETE FROM {$this->table} WHERE migration = ?", [$name]);
                $results[] = ['name' => $name, 'status' => 'ok'];
            } catch (\Throwable $e) {
                $results[] = ['name' => $name, 'status' => 'error', 'message' => $e->getMessage()];
            }
        }

        return ['status' => 'done', 'batch' => $lastBatch, 'results' => $results];
    }

    public function status(): array
    {
        $executed = $this->getExecuted();
        $all      = $this->getAllMigrations();
        $out      = [];

        foreach ($all as $name) {
            $out[] = [
                'name'     => $name,
                'executed' => in_array($name, $executed, true),
            ];
        }
        return $out;
    }

    private function getAllMigrations(): array
    {
        if (!is_dir($this->migrationsDir)) {
            return [];
        }
        $files = scandir($this->migrationsDir);
        $out = [];
        foreach ($files as $f) {
            if (substr($f, -4) === '.php') {
                $out[] = substr($f, 0, -4);
            }
        }
        sort($out);
        return $out;
    }

    private function getExecuted(): array
    {
        try {
            $rows = $this->db->fetchAll("SELECT migration FROM {$this->table}");
            return array_column($rows, 'migration');
        } catch (\Throwable $e) {
            return [];
        }
    }

    private function getNextBatch(): int
    {
        $row = $this->db->fetch("SELECT MAX(batch) AS b FROM {$this->table}");
        return ((int)($row['b'] ?? 0)) + 1;
    }

    private function getLastBatch(): int
    {
        $row = $this->db->fetch("SELECT MAX(batch) AS b FROM {$this->table}");
        return (int)($row['b'] ?? 0);
    }

    private function markExecuted(string $name, int $batch): void
    {
        $this->db->query(
            "INSERT INTO {$this->table} (migration, batch, executed_at) VALUES (?, ?, ?)",
            [$name, $batch, time()]
        );
    }
}
