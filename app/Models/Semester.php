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

    public static function getActive(): array
    {
        return Database::fetchAll("SELECT * FROM semesters WHERE is_active = 1 ORDER BY sort_order");
    }
}
