<?php

namespace App\Models;

use App\Core\Model;
use App\Config\Database;

class Notification extends Model
{
    protected static string $table = 'notifications';
    public static function send(?int $userId, string $title, string $message, string $type = 'info', ?string $link = null): bool
    {
        return Database::insert('notifications', [
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'link' => $link,
            'is_read' => 0
        ]) > 0;
    }

    public static function getForUser(?int $userId, int $limit = 10): array
    {
        $limit = max(1, (int)$limit);
        if ($userId === null) {
            return Database::fetchAll(
                "SELECT * FROM notifications WHERE user_id IS NULL ORDER BY id DESC LIMIT {$limit}"
            );
        }

        return Database::fetchAll(
            "SELECT * FROM notifications WHERE user_id = ? OR user_id IS NULL ORDER BY id DESC LIMIT {$limit}",
            [$userId]
        );
    }

    public static function getUnreadCount(?int $userId): int
    {
        if ($userId === null) {
            $row = Database::fetch("SELECT COUNT(*) as cnt FROM notifications WHERE user_id IS NULL AND is_read = 0");
            return (int)($row->cnt ?? 0);
        }

        $row = Database::fetch("SELECT COUNT(*) as cnt FROM notifications WHERE (user_id = ? OR user_id IS NULL) AND is_read = 0", [$userId]);
        return (int)($row->cnt ?? 0);
    }

    public static function markAsRead(int $id, ?int $userId = null): bool
    {
        if ($userId !== null) {
            return Database::update('notifications', ['is_read' => 1], 'id = :id AND (user_id = :uid OR user_id IS NULL)', ['id' => $id, 'uid' => $userId]) > 0;
        }
        return Database::update('notifications', ['is_read' => 1], 'id = :id', ['id' => $id]) > 0;
    }

    public static function markAllAsRead(?int $userId = null): bool
    {
        if ($userId !== null) {
            return Database::update('notifications', ['is_read' => 1], 'user_id = :uid OR user_id IS NULL', ['uid' => $userId]) > 0;
        }
        return Database::update('notifications', ['is_read' => 1], 'user_id IS NULL', []) > 0;
    }
}
