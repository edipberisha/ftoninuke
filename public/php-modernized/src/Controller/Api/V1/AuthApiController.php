<?php

declare(strict_types=1);

namespace FtoniNuke\Controller\Api\V1;

use FtoniNuke\Security\Auth;
use FtoniNuke\Security\JwtManager;
use FtoniNuke\Middleware\ApiMiddleware;

/**
 * AuthApiController - Manages mobile authentication and JWT distribution.
 */
class AuthApiController
{
    private Auth $auth;
    private JwtManager $jwt;
    private ApiMiddleware $middleware;

    public function __construct(Auth $auth, JwtManager $jwt)
    {
        $this->auth = $auth;
        $this->jwt = $jwt;
        $this->middleware = new ApiMiddleware();
        $this->middleware->handle();
    }

    /**
     * POST /api/v1/auth/login
     */
    public function login(): void
    {
        header('Content-Type: application/json');
        
        $input = json_decode(file_get_contents('php://input'), true);
        $username = $input['username'] ?? '';
        $password = $input['password'] ?? '';

        if (empty($username) || empty($password)) {
            http_response_code(400);
            echo json_encode(['error' => 'Bad Request', 'message' => 'Credentials required']);
            return;
        }

        if ($this->auth->login($username, $password)) {
            $user = $this->auth->getCurrentUser();
            
            $token = $this->jwt->generateToken([
                'user_id' => $user['user_id'],
                'role'    => $user['user_level']
            ]);

            echo json_encode([
                'status' => 'success',
                'message' => 'Authentication successful',
                'token'   => $token,
                'user'    => [
                    'username' => $user['username'],
                    'email'    => $user['user_email']
                ]
            ]);
        } else {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized', 'message' => 'Invalid username or password']);
        }
    }
}
