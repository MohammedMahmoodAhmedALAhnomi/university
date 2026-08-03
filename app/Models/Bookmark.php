<?php

namespace App\Models;

use App\Core\Model;
use App\Config\Database;

class Bookmark extends Model
{
    protected static string $table = 'user_bookmarks';

    public static function toggle(int $userId, int $fileId): array
    {
        $existing = Database::fetch(
            "SELECT id FROM user_bookmarks WHERE user_id = ? AND file_id = ?",
            [$userId, $fileId]
        );

        if ($existing) {
            Database::execute(
                "DELETE FROM user_bookmarks WHERE user_id = ? AND file_id = ?",
                [$userId, $fileId]
            );
            return ['bookmarked' => false, 'message' => 'تم إزالة الملف من المحفوظات'];
        } else {
            Database::insert('user_bookmarks', [
                'user_id' => $userId,
                'file_id' => $fileId,
            ]);
            return ['bookmarked' => true, 'message' => 'تم حفظ الملف في المحفوظات بنجاح'];
        }
    }

    public static function isBookmarked(int $userId, int $fileId): bool
    {
        $row = Database::fetch(
            "SELECT id FROM user_bookmarks WHERE user_id = ? AND file_id = ?",
            [$userId, $fileId]
        );
        return !empty($row);
    }

    public static function getUserBookmarkedFileIds(int $userId): array
    {
        $rows = Database::fetchAll(
            "SELECT file_id FROM user_bookmarks WHERE user_id = ?",
            [$userId]
        );
        return array_column($rows, 'file_id');
    }

    public static function getByUser(int $userId): array
    {
        return Database::fetchAll(
            "SELECT f.*, c.name as course_name, m.name as major_name, l.name as level_number, b.created_at as bookmarked_at
             FROM user_bookmarks b
             JOIN files f ON f.id = b.file_id
             JOIN courses c ON c.id = f.course_id
             LEFT JOIN majors m ON m.id = c.major_id
             LEFT JOIN levels l ON l.id = c.level_id
             WHERE b.user_id = ? AND f.is_approved = 1
             ORDER BY b.created_at DESC",
            [$userId]
        );
    }
}
