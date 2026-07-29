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

        $role = $_SESSION['user_role'] ?? 'guest';
        $currentUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

        if ($role === 'guest') {
            $req = \App\Models\JoinRequest::findPendingByUserId($_SESSION['user_id']);
            if ($req && !str_contains($currentUri, '/pending-approval') && !str_contains($currentUri, '/logout')) {
                redirect(url('/pending-approval'));
                return false;
            }
            if (!$req && !str_contains($currentUri, '/request-role') && !str_contains($currentUri, '/logout')) {
                redirect(url('/request-role'));
                return false;
            }
        }

        return true;
    }
}
