<?php

namespace App\Core;

abstract class Middleware
{
    abstract public function handle(): bool;

    protected function deny(): void
    {
        http_response_code(403);
        echo json_encode(['error' => 'غير مصرح بالوصول']);
        exit;
    }

    protected function redirectToLogin(): void
    {
        redirect(url('/login'));
    }
}
