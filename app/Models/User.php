<?php

namespace App\Models;

use App\Core\Model;
use App\Config\Database;

class User extends Model
{
    protected static string $table = 'users';
    private static bool $migrated = false;

    public static function ensureColumns(): void
    {
        if (self::$migrated) return;
        try {
            Database::raw("ALTER TABLE users ADD COLUMN google_id VARCHAR(255) NULL AFTER email");
        } catch (\Throwable $e) {}
        try {
            Database::raw("ALTER TABLE users ADD COLUMN avatar VARCHAR(500) NULL AFTER google_id");
        } catch (\Throwable $e) {}
        self::$migrated = true;
    }

    public static function findByEmail(string $email)
    {
        self::ensureColumns();
        return Database::fetch("SELECT * FROM users WHERE email = ? LIMIT 1", [$email]);
    }

    public static function findByGoogleId(string $googleId)
    {
        self::ensureColumns();
        return Database::fetch("SELECT * FROM users WHERE google_id = ? LIMIT 1", [$googleId]);
    }

    public static function getAllWithMajor(): array
    {
        return self::getFilteredUsers();
    }

    public static function getFilteredUsers(?string $search = null, ?string $role = null, ?int $scopedMajorId = null): array
    {
        $where = ["u.id != 1"]; // Protect primary super admin
        $params = [];

        if ($scopedMajorId) {
            $where[] = "(u.managed_major_id = ? OR u.major_id = ?)";
            $params[] = $scopedMajorId;
            $params[] = $scopedMajorId;
        }

        if ($role && $role !== 'all') {
            if ($role === 'student') {
                $where[] = "(u.role = 'student' OR u.role = 'guest' OR u.role IS NULL OR u.role = '')";
            } else {
                $where[] = "u.role = ?";
                $params[] = $role;
            }
        }

        if ($search && trim($search) !== '') {
            $q = '%' . trim($search) . '%';
            $where[] = "(u.full_name LIKE ? OR u.email LIKE ? OR u.phone LIKE ?)";
            $params[] = $q;
            $params[] = $q;
            $params[] = $q;
        }

        $whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

        return Database::fetchAll(
            "SELECT u.*, m.name as major_name, l.name as level_name
             FROM users u
             LEFT JOIN majors m ON m.id = COALESCE(u.managed_major_id, u.major_id)
             LEFT JOIN levels l ON l.id = u.managed_level_id
             {$whereClause}
             ORDER BY u.created_at DESC",
            $params
        );
    }

    public static function updateLastLogin(int $id): void
    {
        Database::raw("UPDATE users SET last_login = NOW() WHERE id = ?", [$id]);
    }

    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    public static function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    public static function getCountByRole(): array
    {
        return Database::fetchAll(
            "SELECT role, COUNT(*) as total FROM users GROUP BY role"
        );
    }

    public static function getManagers(): array
    {
        return Database::fetchAll(
            "SELECT u.*, l.name as level_name, m.name as major_name
             FROM users u
             LEFT JOIN levels l ON l.id = u.managed_level_id
             LEFT JOIN majors m ON m.id = COALESCE(u.managed_major_id, u.major_id)
             WHERE u.role = 'manager'
             ORDER BY u.created_at DESC"
        );
    }

    public static function getManagersByMajor(int $majorId): array
    {
        return Database::fetchAll(
            "SELECT u.*, l.name as level_name, m.name as major_name
             FROM users u
             LEFT JOIN levels l ON l.id = u.managed_level_id
             LEFT JOIN majors m ON m.id = COALESCE(u.managed_major_id, u.major_id)
             WHERE u.role = 'manager' AND (u.managed_major_id = ? OR u.major_id = ?)
             ORDER BY u.created_at DESC",
            [$majorId, $majorId]
        );
    }

    public static function countActive(): int
    {
        $row = Database::fetch("SELECT COUNT(*) as cnt FROM users WHERE is_active = 1");
        return (int)($row->cnt ?? 0);
    }
}

