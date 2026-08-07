<?php

namespace App\Middleware;

use App\Core\Middleware;

class AdminAccess extends Middleware
{
    public function handle(): bool
    {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['_flash']['error'] = 'يرجى تسجيل الدخول أولاً';
            $this->redirectToLogin();
            return false;
        }

        $role = $_SESSION['user_role'] ?? 'guest';
        if (!in_array($role, ['admin', 'major_admin', 'manager'], true)) {
            $_SESSION['_flash']['error'] = 'لا تملك صلاحية الوصول للوحة التحكم';
            header('Location: ' . url('/'));
            exit;
        }

        return true;
    }
}
