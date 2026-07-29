<?php

namespace App\Models;

use App\Core\Model;
use App\Config\Database;

class JoinRequest extends Model
{
    protected static string $table = 'join_requests';
    public static function findPendingByUserId(int $userId)
    {
        return Database::fetch(
            "SELECT * FROM join_requests WHERE user_id = ? AND status = 'pending' ORDER BY id DESC LIMIT 1",
            [$userId]
        );
    }

    public static function findLatestByUserId(int $userId)
    {
        return Database::fetch(
            "SELECT jr.*, m.name as major_name, l.name as level_name
             FROM join_requests jr
             LEFT JOIN majors m ON m.id = jr.major_id
             LEFT JOIN levels l ON l.id = jr.level_id
             WHERE jr.user_id = ?
             ORDER BY jr.id DESC LIMIT 1",
            [$userId]
        );
    }

    public static function getAllWithDetails(): array
    {
        return Database::fetchAll(
            "SELECT jr.*, u.full_name as user_name, u.email as user_email,
                    m.name as major_name, l.name as level_name
             FROM join_requests jr
             JOIN users u ON u.id = jr.user_id
             JOIN majors m ON m.id = jr.major_id
             LEFT JOIN levels l ON l.id = jr.level_id
             ORDER BY FIELD(jr.status, 'pending', 'approved', 'rejected'), jr.created_at DESC"
        );
    }

    public static function approve(int $requestId): bool
    {
        $req = Database::fetch("SELECT * FROM join_requests WHERE id = ?", [$requestId]);
        if (!$req) return false;

        $targetRole = ($req->account_type === 'major_admin') ? 'major_admin' : 'manager';

        // Update User Account Role & Scope
        Database::update('users', [
            'role' => $targetRole,
            'major_id' => $req->major_id,
            'managed_major_id' => $req->major_id,
            'managed_level_id' => $req->account_type === 'representative' ? $req->level_id : null,
            'is_active' => 1
        ], 'id = :id', ['id' => $req->user_id]);

        // Update Join Request Status
        Database::update('join_requests', [
            'status' => 'approved'
        ], 'id = :id', ['id' => $requestId]);

        return true;
    }

    public static function reject(int $requestId): bool
    {
        return Database::update('join_requests', [
            'status' => 'rejected'
        ], 'id = :id', ['id' => $requestId]) > 0;
    }
}
