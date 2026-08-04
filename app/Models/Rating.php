<?php

namespace App\Models;

use App\Core\Model;
use App\Config\Database;

class Rating extends Model
{
    protected static string $table = 'ratings';

    public static function getAverageForCourse(int $courseId): float
    {
        $result = Database::fetch(
            "SELECT ROUND(AVG(rating), 1) as avg_rating, COUNT(*) as total
             FROM ratings WHERE course_id = ?",
            [$courseId]
        );
        return (float) ($result->avg_rating ?? 0);
    }

    public static function getCountForCourse(int $courseId): int
    {
        $result = Database::fetch(
            "SELECT COUNT(*) as total FROM ratings WHERE course_id = ?",
            [$courseId]
        );
        return (int) ($result->total ?? 0);
    }

    public static function hasVoted(int $courseId, string $ip, string $sessionId): bool
    {
        $result = Database::fetch(
            "SELECT id FROM ratings WHERE course_id = ? AND ip_address = ? AND session_id = ? LIMIT 1",
            [$courseId, $ip, $sessionId]
        );
        return $result !== false;
    }

    public static function vote(int $courseId, int $rating, string $ip, string $sessionId): int
    {
        return self::create([
            'course_id' => $courseId,
            'rating' => $rating,
            'ip_address' => $ip,
            'session_id' => $sessionId,
        ]);
    }

    public static function getForCourse(int $courseId): array
    {
        return Database::fetchAll(
            "SELECT rating, COUNT(*) as cnt FROM ratings WHERE course_id = ? GROUP BY rating ORDER BY rating DESC",
            [$courseId]
        );
    }

    public static function getForCourses(array $courseIds): array
    {
        if (empty($courseIds)) return [];
        $placeholders = implode(',', array_fill(0, count($courseIds), '?'));
        $rows = Database::fetchAll(
            "SELECT course_id, ROUND(AVG(rating), 1) as avg_rating, COUNT(*) as total
             FROM ratings WHERE course_id IN ($placeholders) GROUP BY course_id",
            $courseIds
        );
        $result = [];
        foreach ($rows as $row) {
            $result[$row->course_id] = $row;
        }
        return $result;
    }
}
