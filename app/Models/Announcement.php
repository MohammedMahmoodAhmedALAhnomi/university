<?php

namespace App\Models;

use App\Core\Model;
use App\Config\Database;

class Announcement extends Model
{
    protected static string $table = 'announcements';

    public static function getActive(): array
    {
        return Database::fetchAll(
            "SELECT a.*, u.full_name as created_by_name
             FROM announcements a
             JOIN users u ON u.id = a.created_by
             WHERE a.is_active = 1
             AND (a.starts_at IS NULL OR a.starts_at <= NOW())
             AND (a.expires_at IS NULL OR a.expires_at >= NOW())
             ORDER BY a.is_pinned DESC, a.created_at DESC"
        );
    }

    public static function getAllWithCreator(): array
    {
        return Database::fetchAll(
            "SELECT a.*, u.full_name as created_by_name
             FROM announcements a
             JOIN users u ON u.id = a.created_by
             ORDER BY a.created_at DESC"
        );
    }

    public static function getPinned(): array
    {
        return Database::fetchAll(
            "SELECT a.*, u.full_name as created_by_name
             FROM announcements a
             JOIN users u ON u.id = a.created_by
             WHERE a.is_active = 1 AND a.is_pinned = 1
             ORDER BY a.created_at DESC"
        );
    }

    public static function findWithCreator(int $id)
    {
        return Database::fetch(
            "SELECT a.*, u.full_name as created_by_name
             FROM announcements a
             JOIN users u ON u.id = a.created_by
             WHERE a.id = ?",
            [$id]
        );
    }
}
