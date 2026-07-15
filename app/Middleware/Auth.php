<?php

namespace App\Middleware;

use App\Core\Middleware;

class Auth extends Middleware
{
    public function handle(): bool
    {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['_flash']['error'] = 'يرجى تسجيل الدخول أولاً';
            $this->redirectToLogin();
            return false;
        }
        return true;
    }
}
