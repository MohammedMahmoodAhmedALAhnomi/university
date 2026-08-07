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
        try {
            Database::raw("ALTER TABLE users MODIFY COLUMN role VARCHAR(50) NOT NULL DEFAULT 'student'");
        } catch (\Throwable $e) {}
        try {
            Database::raw("ALTER TABLE join_requests MODIFY COLUMN account_type VARCHAR(50) NOT NULL DEFAULT 'representative'");
        } catch (\Throwable $e) {}
        self::$migrated = true;
    }

    public static function isSystemOwner($user): bool
    {
        if (empty($user)) return false;
        if (is_numeric($user)) {
            $id = (int)$user;
            if ($id === 1) return true;
            $u = Database::fetch("SELECT email FROM users WHERE id = ? LIMIT 1", [$id]);
            return $u && strtolower(trim($u->email ?? '')) === 'mohammedalahnomi04@gmail.com';
        }
        if (is_string($user)) {
            return strtolower(trim($user)) === 'mohammedalahnomi04@gmail.com';
        }
        if (is_object($user)) {
            return ((int)($user->id ?? 0) === 1) || (strtolower(trim($user->email ?? '')) === 'mohammedalahnomi04@gmail.com');
        }
        if (is_array($user)) {
            return ((int)($user['id'] ?? 0) === 1) || (strtolower(trim($user['email'] ?? '')) === 'mohammedalahnomi04@gmail.com');
        }
        return false;
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
        self::ensureColumns();
        $where = [];
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
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    public static function verifyPassword(string $password, string $hash, ?int $userId = null): bool
    {
        if (password_verify($password, $hash)) {
            // Rehash if algorithm/cost parameters changed
            if ($userId && password_needs_rehash($hash, PASSWORD_BCRYPT, ['cost' => 12])) {
                $newHash = self::hashPassword($password);
                Database::raw("UPDATE users SET password = ? WHERE id = ?", [$newHash, $userId]);
            }
            return true;
        }

        // Fallback for legacy plaintext password & auto-encrypt to secure bcrypt hash
        if ($password === $hash) {
            if ($userId) {
                $newHash = self::hashPassword($password);
                Database::raw("UPDATE users SET password = ? WHERE id = ?", [$newHash, $userId]);
            }
            return true;
        }

        return false;
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

