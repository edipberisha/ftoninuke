<?php

declare(strict_types=1);

namespace FtoniNuke\Service;

use FtoniNuke\Repository\UserRepository;

class UserService
{
    private UserRepository $repo;

    public function __construct()
    {
        $this->repo = new UserRepository();
    }

    public function create(array $data): array
    {
        $username = trim($data['username'] ?? '');
        $email    = trim($data['user_email'] ?? '');
        $password = $data['password'] ?? '';
        $level    = (int)($data['user_level'] ?? 1);

        if (strlen($username) < 3) {
            return ['ok' => false, 'error' => 'Kullanıcı adı en az 3 karakter olmalı.'];
        }
        if (!preg_match('/^[a-zA-Z0-9_.\-]+$/', $username)) {
            return ['ok' => false, 'error' => 'Kullanıcı adı sadece harf, rakam, nokta, tire ve alt çizgi içerebilir.'];
        }
        if ($this->repo->usernameExists($username)) {
            return ['ok' => false, 'error' => 'Bu kullanıcı adı zaten kayıtlı.'];
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['ok' => false, 'error' => 'Geçersiz e-posta adresi.'];
        }
        if ($this->repo->emailExists($email)) {
            return ['ok' => false, 'error' => 'Bu e-posta zaten kayıtlı.'];
        }
        if (strlen($password) < 6) {
            return ['ok' => false, 'error' => 'Şifre en az 6 karakter olmalı.'];
        }
        if (!in_array($level, [0, 1, 2], true)) {
            return ['ok' => false, 'error' => 'Geçersiz kullanıcı rolü.'];
        }

        try {
            $id = $this->repo->create([
                'username'   => $username,
                'user_email' => $email,
                'password'   => $password,
                'user_level' => $level,
            ]);
            return ['ok' => true, 'id' => $id];
        } catch (\Throwable $e) {
            return ['ok' => false, 'error' => 'Sistem hatası: ' . $e->getMessage()];
        }
    }

    public function update(int $id, array $data): array
    {
        $user = $this->repo->findById($id);
        if (!$user) {
            return ['ok' => false, 'error' => 'Kullanıcı bulunamadı.'];
        }

        $username = trim($data['username'] ?? '');
        $email    = trim($data['user_email'] ?? '');
        $password = $data['password'] ?? '';
        $level    = (int)($data['user_level'] ?? 1);

        if (strlen($username) < 3) {
            return ['ok' => false, 'error' => 'Kullanıcı adı en az 3 karakter olmalı.'];
        }
        if (!preg_match('/^[a-zA-Z0-9_.\-]+$/', $username)) {
            return ['ok' => false, 'error' => 'Geçersiz karakter içeriyor.'];
        }
        if ($this->repo->usernameExists($username, $id)) {
            return ['ok' => false, 'error' => 'Bu kullanıcı adı başkası tarafından kullanılıyor.'];
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['ok' => false, 'error' => 'Geçersiz e-posta.'];
        }
        if ($this->repo->emailExists($email, $id)) {
            return ['ok' => false, 'error' => 'Bu e-posta başkası tarafından kullanılıyor.'];
        }
        if ($password !== '' && strlen($password) < 6) {
            return ['ok' => false, 'error' => 'Yeni şifre en az 6 karakter olmalı.'];
        }
        if (!in_array($level, [0, 1, 2], true)) {
            return ['ok' => false, 'error' => 'Geçersiz rol.'];
        }

        if ($id === (int)($_SESSION['user_id'] ?? 0) && $level < 2) {
            return ['ok' => false, 'error' => 'Kendi admin rolünüzü kaldıramazsınız.'];
        }

        try {
            $this->repo->update($id, [
                'username'   => $username,
                'user_email' => $email,
                'password'   => $password,
                'user_level' => $level,
            ]);
            return ['ok' => true];
        } catch (\Throwable $e) {
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }

    public function delete(int $id): array
    {
        if ($id === (int)($_SESSION['user_id'] ?? 0)) {
            return ['ok' => false, 'error' => 'Kendi hesabınızı silemezsiniz.'];
        }

        $user = $this->repo->findById($id);
        if (!$user) {
            return ['ok' => false, 'error' => 'Kullanıcı bulunamadı.'];
        }
        if ((int)$user['user_level'] === 2 && $this->repo->countByLevel(2) <= 1) {
            return ['ok' => false, 'error' => 'Son admin silinemez.'];
        }

        try {
            $this->repo->delete($id);
            return ['ok' => true];
        } catch (\Throwable $e) {
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }

    public function getRepo(): UserRepository
    {
        return $this->repo;
    }
}
