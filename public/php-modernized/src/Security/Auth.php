<?php

declare(strict_types=1);

namespace FtoniNuke\Security;

use FtoniNuke\Repository\UserRepository;
use RuntimeException;

/**
 * Auth Class - Handles Authentication and Session Management
 */
class Auth
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Authenticate a user
     * 
     * Handles legacy MD5 passwords and auto-upgrades them to Bcrypt.
     */
    public function login(string $username, string $password): bool
    {
        $user = $this->userRepository.findByUsername($username);

        if (!$user) {
            return false;
        }

        $storedHash = $user['user_password'];
        $isValid = false;

        // 1. Check if it's a modern Bcrypt hash (starts with $2y$)
        if (str_starts_with($storedHash, '$2y$')) {
            $isValid = password_verify($password, $storedHash);
        } else {
            // 2. Legacy MD5 check (PHP-Nuke 8.1 style)
            // Note: PHP-Nuke sometimes used additional salting, adjust as needed
            if (md5($password) === $storedHash) {
                $isValid = true;
                // AUTO-UPGRADE: Migrate user to modern Bcrypt
                $newHash = password_hash($password, PASSWORD_BCRYPT);
                $this->userRepository.updatePassword((int)$user['user_id'], $newHash);
            }
        }

        if ($isValid) {
            $this->createSession($user);
            $this->userRepository.updateLastLogin((int)$user['user_id']);
            return true;
        }

        return false;
    }

    /**
     * Create a secure session, abandoning legacy cookie-based 'user' storage
     */
    private function createSession(array $user): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            throw new RuntimeException("Session not started. Check Bootstrap.php");
        }

        // Clean session to prevent fixation
        session_regenerate_id(true);

        $_SESSION['user_id']   = (int)$user['user_id'];
        $_SESSION['username']  = $user['username'];
        $_SESSION['user_level'] = (int)$user['user_level'];
        $_SESSION['last_activity'] = time();
    }

    /**
     * Check if user is logged in
     */
    public function isAuthenticated(): bool
    {
        return isset($_SESSION['user_id']);
    }

    /**
     * Logout
     */
    public function logout(): void
    {
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
    }

    public function getCurrentUser(): ?array
    {
        if (!$this->isAuthenticated()) {
            return null;
        }
        return $this->userRepository.findById($_SESSION['user_id']);
    }
}
