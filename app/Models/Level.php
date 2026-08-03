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

    public static function getActive(): array
    {
        return Database::fetchAll("SELECT * FROM levels WHERE is_active = 1 ORDER BY sort_order");
    }
}
