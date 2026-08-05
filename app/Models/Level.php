<?php

namespace App\Models;

use App\Core\Model;
use App\Config\Database;

class Level extends Model
{
    protected static string $table = 'levels';

    public static function all(): array
    {
        return Database::fetchAll("SELECT * FROM levels ORDER BY sort_order");
    }

    public static function getAll(): array
    {
        return static::getActive();
    }

    private static ?array $cacheActive = null;

    public static function getActive(): array
    {
        if (self::$cacheActive !== null) {
            return self::$cacheActive;
        }
        self::$cacheActive = Database::fetchAll("SELECT * FROM levels ORDER BY sort_order");
        return self::$cacheActive;
    }

    public static function getActiveSorted(): array
    {
        return static::getActive();
    }
}

