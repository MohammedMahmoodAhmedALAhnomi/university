<?php

namespace App\Models;

use App\Core\Model;
use App\Config\Database;
use App\Models\Rating;

class Course extends Model
{
    protected static string $table = 'courses';

    public static function getAllWithDetails(?string $where = null, array $params = []): array
    {
        $sql = "SELECT c.*, m.name as major_name, l.name as level_name, s.name as semester_name
                FROM courses c
                JOIN majors m ON m.id = c.major_id
                JOIN levels l ON l.id = c.level_id
                JOIN semesters s ON s.id = c.semester_id";
        if ($where) {
            $sql .= " $where";
        }
        $sql .= " ORDER BY c.created_at DESC";
        return Database::fetchAll($sql, $params);
    }

    public static function findByMajorAndLevel(int $majorId, int $levelId): array
    {
        $rows = Database::fetchAll(
            "SELECT c.*, s.name as semester_name
             FROM courses c
             JOIN semesters s ON s.id = c.semester_id
             WHERE c.major_id = ? AND c.level_id = ?
             ORDER BY s.sort_order, c.name",
            [$majorId, $levelId]
        );
        $courseIds = array_map(fn($c) => $c->id, $rows);
        $ratings = Rating::getForCourses($courseIds);
        foreach ($rows as $c) {
            $r = $ratings[$c->id] ?? null;
            $c->avg_rating = $r->avg_rating ?? 0;
            $c->rating_count = $r->total ?? 0;
        }
        return $rows;
    }

    public static function findWithDetails(int $id)
    {
        $course = Database::fetch(
            "SELECT c.*, m.name as major_name, l.name as level_name, l.level_number,
                    s.name as semester_name, s.semester_number
             FROM courses c
             JOIN majors m ON m.id = c.major_id
             JOIN levels l ON l.id = c.level_id
             JOIN semesters s ON s.id = c.semester_id
             WHERE c.id = ?",
            [$id]
        );
        if ($course) {
            $course->avg_rating = Rating::getAverageForCourse($id);
            $course->rating_count = Rating::getCountForCourse($id);
        }
        return $course;
    }

    public static function search(string $query, array $filters = []): array
    {
        $q = '%' . $query . '%';
        $sql = "SELECT c.*, m.name as major_name, l.name as level_name, s.name as semester_name
                FROM courses c
                JOIN majors m ON m.id = c.major_id
                JOIN levels l ON l.id = c.level_id
                JOIN semesters s ON s.id = c.semester_id
                WHERE (c.name LIKE ? OR c.description LIKE ?)";
        $params = [$q, $q];

        if (!empty($filters['major_id'])) {
            $sql .= " AND c.major_id = ?";
            $params[] = (int)$filters['major_id'];
        }
        if (!empty($filters['level_id'])) {
            $sql .= " AND c.level_id = ?";
            $params[] = (int)$filters['level_id'];
        }
        if (!empty($filters['semester_id'])) {
            $sql .= " AND c.semester_id = ?";
            $params[] = (int)$filters['semester_id'];
        }

        $sql .= " ORDER BY c.name LIMIT 50";
        $rows = Database::fetchAll($sql, $params);
        $courseIds = array_map(fn($c) => $c->id, $rows);
        $ratings = Rating::getForCourses($courseIds);
        foreach ($rows as $c) {
            $r = $ratings[$c->id] ?? null;
            $c->avg_rating = $r->avg_rating ?? 0;
            $c->rating_count = $r->total ?? 0;
        }
        return $rows;
    }

    public static function getByMajorGrouped(int $majorId, ?int $levelId = null): array
    {
        $params = [$majorId];
        $levelFilter = '';
        if ($levelId) {
            $levelFilter = ' AND c.level_id = ?';
            $params[] = $levelId;
        }
        $rows = Database::fetchAll(
            "SELECT c.id, c.name, c.description, c.is_active, c.created_at, c.updated_at,
                    c.major_id, c.level_id, c.semester_id,
                    l.name as level_name, l.level_number, s.name as semester_name, s.semester_number
             FROM courses c
             JOIN levels l ON l.id = c.level_id
             JOIN semesters s ON s.id = c.semester_id
             WHERE c.major_id = ? AND c.is_active = 1{$levelFilter}
             ORDER BY l.sort_order, s.sort_order, c.name",
            $params
        );
        $courseIds = array_map(fn($c) => $c->id, $rows);
        $ratings = Rating::getForCourses($courseIds);
        $grouped = [];
        foreach ($rows as $c) {
            $key = $c->level_number . '-' . $c->semester_number;
            if (!isset($grouped[$key])) {
                $grouped[$key] = ['level_name' => $c->level_name, 'level_number' => $c->level_number, 'semester_name' => $c->semester_name, 'semester_number' => $c->semester_number, 'courses' => []];
            }
            $r = $ratings[$c->id] ?? null;
            $c->avg_rating = $r->avg_rating ?? 0;
            $c->rating_count = $r->total ?? 0;
            $grouped[$key]['courses'][] = $c;
        }
        return $grouped;
    }

    public static function getCountByMajor(?int $levelId = null, ?int $majorId = null): array
    {
        $sql = "SELECT m.id, m.name, COUNT(c.id) as total
                FROM majors m
                LEFT JOIN courses c ON c.major_id = m.id";
        $params = [];
        if ($levelId) {
            $sql .= " AND c.level_id = ?";
            $params[] = $levelId;
        }
        if ($majorId) {
            $sql .= " AND m.id = ?";
            $params[] = $majorId;
        }
        $sql .= " GROUP BY m.id, m.name";
        return Database::fetchAll($sql, $params);
    }

    public static function getDownloadStats(?int $levelId = null, ?int $majorId = null): array
    {
        $sql = "SELECT c.id, c.name, COALESCE(SUM(f.download_count), 0) as total_downloads
                FROM courses c
                LEFT JOIN files f ON f.course_id = c.id";
        $params = [];
        $conditions = [];
        if ($levelId) {
            $conditions[] = "c.level_id = ?";
            $params[] = $levelId;
        }
        if ($majorId) {
            $conditions[] = "c.major_id = ?";
            $params[] = $majorId;
        }
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }
        $sql .= " GROUP BY c.id, c.name ORDER BY total_downloads DESC LIMIT 10";
        return Database::fetchAll($sql, $params);
    }
}
