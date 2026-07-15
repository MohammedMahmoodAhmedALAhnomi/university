<?php

namespace App\Middleware;

use App\Core\Middleware;

class Admin extends Middleware
{
    public function handle(): bool
    {
        if (!isset($_SESSION['user_id'])) {
            $this->redirectToLogin();
            return false;
        }

        $role = $_SESSION['user_role'] ?? 'guest';
        if ($role !== 'admin') {
            $this->deny();
            return false;
        }

        return true;
    }
}
