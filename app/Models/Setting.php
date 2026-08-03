<?php

namespace App\Models;

use App\Core\Model;
use App\Config\Database;

class Setting extends Model
{
    protected static string $table = 'settings';
    private static array $cache = [];
    private static bool $loadedAll = false;

    /**
     * Load all settings into memory cache in a single query for maximum performance
     */
    public static function loadAll(): void
    {
        if (static::$loadedAll) {
            return;
        }
        $rows = Database::fetchAll("SELECT setting_key, setting_value FROM settings");
        foreach ($rows as $row) {
            static::$cache[$row->setting_key] = $row->setting_value;
        }
        static::$loadedAll = true;
    }

    /**
     * Get a setting value safely without fatal errors
     */
    public static function get(string $key, $default = null): ?string
    {
        if (array_key_exists($key, static::$cache)) {
            return static::$cache[$key] ?? $default;
        }
        
        $result = Database::fetch("SELECT setting_value FROM settings WHERE setting_key = ?", [$key]);
        $val = (is_object($result) && isset($result->setting_value)) ? $result->setting_value : $default;
        
        static::$cache[$key] = $val;
        return $val;
    }

    /**
     * Set/Update a setting using a single atomic query
     */
    public static function set(string $key, string $value): void
    {
        static::$cache[$key] = $value;
        Database::raw(
            "INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) 
             ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)",
            [$key, $value]
        );
    }

    /**
     * Get all settings grouped by setting_group and populate memory cache
     */
    public static function getAllGrouped(): array
    {
        // Seed Google OAuth keys safely if missing
        Database::raw("INSERT IGNORE INTO settings (setting_key, setting_value, setting_type, setting_group) 
                       VALUES ('google_client_id', '', 'text', 'Google OAuth (تسجيل الدخول بقوقل)'),
                              ('google_client_secret', '', 'text', 'Google OAuth (تسجيل الدخول بقوقل)')");

        $rows = Database::fetchAll("SELECT * FROM settings ORDER BY setting_group, id");
        $grouped = [];
        foreach ($rows as $row) {
            static::$cache[$row->setting_key] = $row->setting_value;
            $grouped[$row->setting_group][] = $row;
        }
        return $grouped;
    }

    /**
     * Get all public settings and populate memory cache
     */
    public static function getAllPublic(): array
    {
        $rows = Database::fetchAll("SELECT setting_key, setting_value FROM settings WHERE is_public = 1");
        $result = [];
        foreach ($rows as $row) {
            static::$cache[$row->setting_key] = $row->setting_value;
            $result[$row->setting_key] = $row->setting_value;
        }
        return $result;
    }
}

