<?php

namespace App\Models;

use App\Core\Model;
use App\Config\Database;

class Major extends Model
{
    protected static string $table = 'majors';

    public static function getWithCourseCount(): array
    {
        return Database::fetchAll(
            "SELECT m.*, COUNT(c.id) as courses_count
             FROM majors m
             LEFT JOIN courses c ON c.major_id = m.id
             GROUP BY m.id
             ORDER BY m.name"
        );
    }

    public static function getActive(): array
    {
        return Database::fetchAll("SELECT * FROM majors WHERE is_active = 1 ORDER BY name");
    }
}
