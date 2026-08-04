<?php

namespace App\Models;

use App\Core\Model;
use App\Config\Database;

class Semester extends Model
{
    protected static string $table = 'semesters';

    public static function all(): array
    {
        return Database::fetchAll("SELECT * FROM semesters ORDER BY sort_order");
    }

    private static ?array $cacheActive = null;

    public static function getActive(): array
    {
        if (self::$cacheActive !== null) {
            return self::$cacheActive;
        }
        self::$cacheActive = Database::fetchAll("SELECT * FROM semesters ORDER BY sort_order");
        return self::$cacheActive;
    }

    public static function getActiveSorted(): array
    {
        return static::getActive();
    }
}

