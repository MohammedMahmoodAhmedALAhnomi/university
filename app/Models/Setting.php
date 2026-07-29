<?php

namespace App\Models;

use App\Core\Model;
use App\Config\Database;

class Setting extends Model
{
    protected static string $table = 'settings';

    public static function get(string $key, $default = null): ?string
    {
        $result = Database::fetch("SELECT setting_value FROM settings WHERE setting_key = ?", [$key]);
        return $result->setting_value ?? $default;
    }

    public static function set(string $key, string $value): void
    {
        $existing = Database::fetch("SELECT id FROM settings WHERE setting_key = ?", [$key]);
        if ($existing) {
            Database::update(static::$table, ['setting_value' => $value], "setting_key = :key", ['key' => $key]);
        } else {
            Database::insert(static::$table, [
                'setting_key' => $key,
                'setting_value' => $value,
            ]);
        }
    }

    public static function getAllGrouped(): array
    {
        // Auto-seed Google OAuth keys if not in database yet
        $checkGoogle = Database::fetch("SELECT id FROM settings WHERE setting_key = 'google_client_id'");
        if (!$checkGoogle) {
            Database::insert(static::$table, [
                'setting_key' => 'google_client_id',
                'setting_value' => '',
                'setting_type' => 'text',
                'setting_group' => 'Google OAuth (تسجيل الدخول بقوقل)',
            ]);
            Database::insert(static::$table, [
                'setting_key' => 'google_client_secret',
                'setting_value' => '',
                'setting_type' => 'text',
                'setting_group' => 'Google OAuth (تسجيل الدخول بقوقل)',
            ]);
        }

        $rows = Database::fetchAll("SELECT * FROM settings ORDER BY setting_group, id");
        $grouped = [];
        foreach ($rows as $row) {
            $grouped[$row->setting_group][] = $row;
        }
        return $grouped;
    }

    public static function getAllPublic(): array
    {
        $rows = Database::fetchAll("SELECT setting_key, setting_value FROM settings WHERE is_public = 1");
        $result = [];
        foreach ($rows as $row) {
            $result[$row->setting_key] = $row->setting_value;
        }
        return $result;
    }
}
