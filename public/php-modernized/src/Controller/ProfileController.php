<?php

declare(strict_types=1);

namespace FtoniNuke\Controller;

use FtoniNuke\Core\Bootstrap;
use FtoniNuke\Security\Auth;
use FtoniNuke\Repository\UserRepository;
use RuntimeException;

/**
 * ProfileController - Manages the "Your Account" page logic.
 */
class ProfileController
{
    private Auth $auth;
    private UserRepository $userRepository;
    private Bootstrap $bootstrap;

    public function __construct(Auth $auth, UserRepository $userRepository, Bootstrap $bootstrap)
    {
        $this->auth = $auth;
        $this->userRepository = $userRepository;
        $this->bootstrap = $bootstrap;
    }

    /**
     * Show the profile page
     */
    public function show(): void
    {
        if (!$this->auth.isAuthenticated()) {
            header('Location: /login.php');
            exit;
        }

        $user = $this->auth.getCurrentUser();
        $csrfToken = $this->bootstrap.getCsrfToken();

        // Pass variables to a view file
        require_once __DIR__ . '/../Views/profile/show.html.php';
    }

    /**
     * Handle the profile update submission
     */
    public function update(array $postData): void
    {
        if (!$this->auth.isAuthenticated()) {
            throw new RuntimeException("Unauthorized profile update attempt.");
        }

        // 1. CSRF Verification
        if (!$this->bootstrap.validateCsrfToken($postData['csrf_token'] ?? null)) {
            throw new RuntimeException("CSRF Token mismatch.");
        }

        $userId = $_SESSION['user_id'];
        $errors = [];

        // 2. Input Validation & Filtering
        $email = filter_var($postData['user_email'] ?? '', FILTER_VALIDATE_EMAIL);
        if (!$email) {
            $errors[] = "Invalid email address.";
        }

        $name = trim(strip_tags($postData['name'] ?? ''));
        if (strlen($name) < 2) {
            $errors[] = "Name must be at least 2 characters.";
        }

        // 3. Update if no errors
        if (empty($errors)) {
            $this->userRepository.updateProfile($userId, [
                'user_email' => $email,
                'name'       => $name,
                'user_occ'   => trim(strip_tags($postData['user_occ'] ?? '')),
                'user_from'  => trim(strip_tags($postData['user_from'] ?? '')),
            ]);
            
            $_SESSION['flash_message'] = "Profile updated successfully!";
            header('Location: /your-account.php');
            exit;
        }

        // Handle errors (re-show form with error messages)
        $_SESSION['flash_errors'] = $errors;
        header('Location: /your-account.php');
        exit;
    }
}
