<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Notification;

class NotificationController extends Controller
{
    public function markRead(): void
    {
        $userId = $_SESSION['user_id'] ?? null;
        $id = (int)$this->getParam('id');

        if ($id > 0) {
            Notification::markAsRead($id, $userId);
        }

        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
            exit;
        }

        $referer = $_SERVER['HTTP_REFERER'] ?? url('/admin/dashboard');
        redirect($referer);
    }

    public function readAll(): void
    {
        $userId = $_SESSION['user_id'] ?? null;
        Notification::markAllAsRead($userId);

        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
            exit;
        }

        $referer = $_SERVER['HTTP_REFERER'] ?? url('/admin/dashboard');
        redirect($referer);
    }
}
