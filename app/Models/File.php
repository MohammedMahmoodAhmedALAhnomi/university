<?php

namespace App\Models;

use App\Core\Model;
use App\Config\Database;

class File extends Model
{
    protected static string $table = 'files';

    public static function getByCourse(int $courseId, string $sort = 'newest', int $page = 1, int $perPage = 20): array
    {
        $orderBy = match ($sort) {
            'downloads' => 'f.download_count DESC, f.created_at DESC',
            default => 'f.created_at DESC',
        };
        $total = Database::raw(
            "SELECT COUNT(*) FROM files f WHERE f.course_id = ? AND f.is_approved = 1",
            [$courseId]
        )->fetchColumn();
        $offset = ($page - 1) * $perPage;
        $rows = Database::fetchAll(
            "SELECT f.*, u.full_name as uploader_name
             FROM files f
             JOIN users u ON u.id = f.uploaded_by
             WHERE f.course_id = ? AND f.is_approved = 1
             ORDER BY $orderBy
             LIMIT ? OFFSET ?",
            [$courseId, $perPage, $offset]
        );
        return ['files' => $rows, 'total' => (int)$total, 'pages' => max(1, (int)ceil($total / $perPage))];
    }

    public static function getAllWithDetails(?string $where = null, array $params = []): array
    {
        $sql = "SELECT f.*, c.name as course_name,
                       u.full_name as uploader_name
                FROM files f
                JOIN courses c ON c.id = f.course_id
                JOIN users u ON u.id = f.uploaded_by";
        if ($where) {
            $sql .= " $where";
        }
        $sql .= " ORDER BY f.created_at DESC";
        return Database::fetchAll($sql, $params);
    }

    public static function findWithDetails(int $id)
    {
        return Database::fetch(
            "SELECT f.*, c.name as course_name, c.level_id, u.full_name as uploader_name
             FROM files f
             JOIN courses c ON c.id = f.course_id
             JOIN users u ON u.id = f.uploaded_by
             WHERE f.id = ?",
            [$id]
        );
    }

    public static function incrementDownload(int $id): void
    {
        Database::raw("UPDATE files SET download_count = download_count + 1 WHERE id = ?", [$id]);
    }

    public static function getRecentCount(int $hours = 48, ?int $levelId = null, ?int $majorId = null): int
    {
        $sql = "SELECT COUNT(*) FROM files f";
        $params = [];
        if ($levelId && $majorId) {
            $sql .= " JOIN courses c ON c.id = f.course_id WHERE c.level_id = ? AND c.major_id = ? AND f.created_at >= DATE_SUB(NOW(), INTERVAL ? HOUR)";
            $params = [$levelId, $majorId, $hours];
        } elseif ($levelId) {
            $sql .= " JOIN courses c ON c.id = f.course_id WHERE c.level_id = ? AND f.created_at >= DATE_SUB(NOW(), INTERVAL ? HOUR)";
            $params = [$levelId, $hours];
        } else {
            $sql .= " WHERE f.created_at >= DATE_SUB(NOW(), INTERVAL ? HOUR)";
            $params = [$hours];
        }
        return (int) Database::raw($sql, $params)->fetchColumn();
    }

    public static function getRecent(int $limit = 10, ?int $levelId = null, ?int $majorId = null): array
    {
        $sql = "SELECT f.*, c.name as course_name
                FROM files f
                JOIN courses c ON c.id = f.course_id
                WHERE f.is_approved = 1";
        $params = [];
        if ($levelId) {
            $sql .= " AND c.level_id = ?";
            $params[] = $levelId;
        }
        if ($majorId) {
            $sql .= " AND c.major_id = ?";
            $params[] = $majorId;
        }
        $sql .= " ORDER BY f.created_at DESC LIMIT ?";
        $params[] = $limit;
        return Database::fetchAll($sql, $params);
    }

    public static function getByType(string $type, int $limit = 20, ?int $levelId = null, ?int $majorId = null): array
    {
        $sql = "SELECT f.*, c.name as course_name
                FROM files f
                JOIN courses c ON c.id = f.course_id
                WHERE f.file_type = ? AND f.is_approved = 1";
        $params = [$type];
        if ($levelId) {
            $sql .= " AND c.level_id = ?";
            $params[] = $levelId;
        }
        if ($majorId) {
            $sql .= " AND c.major_id = ?";
            $params[] = $majorId;
        }
        $sql .= " ORDER BY f.created_at DESC LIMIT ?";
        $params[] = $limit;
        return Database::fetchAll($sql, $params);
    }

    public static function getTotalSize(?int $levelId = null, ?int $majorId = null): int
    {
        $sql = "SELECT COALESCE(SUM(f.file_size), 0) as total FROM files f";
        $params = [];
        if ($levelId && $majorId) {
            $sql .= " JOIN courses c ON c.id = f.course_id WHERE c.level_id = ? AND c.major_id = ?";
            $params = [$levelId, $majorId];
        } elseif ($levelId) {
            $sql .= " JOIN courses c ON c.id = f.course_id WHERE c.level_id = ?";
            $params[] = $levelId;
        }
        $result = Database::fetch($sql, $params);
        return (int) ($result->total ?? 0);
    }
}
