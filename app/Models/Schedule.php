<?php

namespace App\Models;

use App\Core\Model;
use App\Config\Database;

class Schedule extends Model
{
    protected static string $table = 'schedules';

    private static bool $migrated = false;

    private static function ensureColumns(): void
    {
        if (self::$migrated) return;
        try {
            Database::raw("ALTER TABLE schedules ADD COLUMN file_path VARCHAR(500) NULL AFTER type");
        } catch (\Throwable $e) {}
        try {
            Database::raw("ALTER TABLE schedules ADD COLUMN group_name VARCHAR(100) NULL AFTER level_id");
        } catch (\Throwable $e) {}
        try {
            Database::raw("ALTER TABLE schedules ADD COLUMN sub_group_name VARCHAR(100) NULL AFTER group_name");
        } catch (\Throwable $e) {}
        try {
            Database::raw("ALTER TABLE schedules ADD COLUMN section_code VARCHAR(100) NULL AFTER sub_group_name");
        } catch (\Throwable $e) {}
        try {
            Database::raw("ALTER TABLE schedules MODIFY COLUMN subject_name VARCHAR(150) NULL");
        } catch (\Throwable $e) {}
        try {
            Database::raw("ALTER TABLE schedules MODIFY COLUMN type ENUM('lecture', 'exam', 'pdf_file') DEFAULT 'lecture'");
        } catch (\Throwable $e) {}
        self::$migrated = true;
    }

    public static function getByMajorAndLevel(int $majorId, int $levelId, ?string $type = null, ?string $groupName = null): array
    {
        self::ensureColumns();
        $sql = "SELECT s.*, m.name as major_name, l.name as level_name, sem.name as semester_name, u.full_name as creator_name
                FROM schedules s
                JOIN majors m ON m.id = s.major_id
                JOIN levels l ON l.id = s.level_id
                LEFT JOIN semesters sem ON sem.id = s.semester_id
                LEFT JOIN users u ON u.id = s.created_by
                WHERE s.major_id = ? AND s.level_id = ?";
        $params = [$majorId, $levelId];

        if ($groupName && $groupName !== 'all') {
            $sql .= " AND (s.group_name = ? OR s.sub_group_name = ? OR s.section_code = ?)";
            $params[] = $groupName;
            $params[] = $groupName;
            $params[] = $groupName;
        }

        if ($type) {
            $sql .= " AND s.type = ?";
            $params[] = $type;
        }

        if ($type === 'exam') {
            $sql .= " ORDER BY s.exam_date ASC";
        } elseif ($type === 'pdf_file') {
            $sql .= " ORDER BY s.created_at DESC";
        } else {
            $sql .= " ORDER BY FIELD(s.day_of_week, 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'), s.start_time ASC";
        }

        return Database::fetchAll($sql, $params);
    }

    public static function getDistinctGroups(int $majorId, int $levelId): array
    {
        self::ensureColumns();
        return Database::fetchAll(
            "SELECT DISTINCT group_name, sub_group_name, section_code 
             FROM schedules 
             WHERE major_id = ? AND level_id = ? AND ((group_name IS NOT NULL AND group_name != '') OR (sub_group_name IS NOT NULL AND sub_group_name != '') OR (section_code IS NOT NULL AND section_code != ''))
             ORDER BY group_name ASC, sub_group_name ASC",
            [$majorId, $levelId]
        );
    }

    public static function getUpcomingExams(int $majorId, int $levelId, int $limit = 5): array
    {
        self::ensureColumns();
        return Database::fetchAll(
            "SELECT s.*, m.name as major_name, l.name as level_name
             FROM schedules s
             JOIN majors m ON m.id = s.major_id
             JOIN levels l ON l.id = s.level_id
             WHERE s.major_id = ? AND s.level_id = ? AND s.type = 'exam' AND s.exam_date >= NOW()
             ORDER BY s.exam_date ASC
             LIMIT {$limit}",
            [$majorId, $levelId]
        );
    }

    public static function getAllWithDetails(): array
    {
        self::ensureColumns();
        return Database::fetchAll(
            "SELECT s.*, m.name as major_name, l.name as level_name, u.full_name as creator_name
             FROM schedules s
             JOIN majors m ON m.id = s.major_id
             JOIN levels l ON l.id = s.level_id
             LEFT JOIN users u ON u.id = s.created_by
             ORDER BY s.created_at DESC"
        );
    }
}
