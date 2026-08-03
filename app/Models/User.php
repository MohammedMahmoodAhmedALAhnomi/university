<?php

namespace App\Models;

use App\Core\Model;
use App\Config\Database;

class User extends Model
{
    protected static string $table = 'users';

    public static function findByEmail(string $email)
    {
        return Database::fetch("SELECT * FROM users WHERE email = ? LIMIT 1", [$email]);
    }

    public static function getAllWithMajor(): array
    {
        return Database::fetchAll(
            "SELECT u.*, m.name as major_name
             FROM users u
             LEFT JOIN majors m ON m.id = u.major_id
             ORDER BY u.created_at DESC"
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
             LEFT JOIN majors m ON m.id = u.managed_major_id
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
             LEFT JOIN majors m ON m.id = u.managed_major_id
             WHERE u.role = 'manager' AND u.managed_major_id = ?
             ORDER BY u.created_at DESC",
            [$majorId]
        );
    }
}
