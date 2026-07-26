<?php

namespace App\Models;

use App\Core\Model;
use App\Config\Database;

class ActivityLog extends Model
{
    protected static string $table = 'activity_logs';

    public static function getRecent(int $limit = 20): array
    {
        return Database::fetchAll(
            "SELECT al.*, u.full_name as user_name
             FROM activity_logs al
             LEFT JOIN users u ON u.id = al.user_id
             ORDER BY al.created_at DESC LIMIT ?",
            [$limit]
        );
    }

    public static function getByUser(int $userId, int $limit = 20): array
    {
        return Database::fetchAll(
            "SELECT * FROM activity_logs WHERE user_id = ? ORDER BY created_at DESC LIMIT ?",
            [$userId, $limit]
        );
    }

    public static function getByAction(string $action, int $limit = 20): array
    {
        return Database::fetchAll(
            "SELECT al.*, u.full_name as user_name
             FROM activity_logs al
             LEFT JOIN users u ON u.id = al.user_id
             WHERE al.action = ? ORDER BY created_at DESC LIMIT ?",
            [$action, $limit]
        );
    }

    public static function clean(int $daysOlderThan = 30): int
    {
        return Database::delete(static::$table, "created_at < DATE_SUB(NOW(), INTERVAL ? DAY)", [$daysOlderThan]);
    }
}
