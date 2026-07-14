<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Major;
use App\Models\Level;
use App\Models\Semester;
use App\Models\Course;
use App\Models\File;
use App\Models\User;
use App\Models\Announcement;
use App\Models\ActivityLog;
use App\Config\Database;

class DashboardController extends Controller
{
    public function index(): void
    {
        $managedLevelId = get_managed_level_id();
        $managedMajorId = get_managed_major_id();
        $totalSize = File::getTotalSize($managedLevelId, $managedMajorId);
        $sizeFormatted = $totalSize > 1048576 ? round($totalSize / 1048576, 2) . ' MB' : ($totalSize > 1024 ? round($totalSize / 1024, 2) . ' KB' : $totalSize . ' B');

        $this->view('admin/dashboard', [
            'majorsCount' => Major::count(),
            'levelsCount' => Level::count(),
            'semestersCount' => Semester::count(),
            'coursesCount' => $managedLevelId
                ? (int) Database::fetch("SELECT COUNT(*) as total FROM courses WHERE level_id = ?" . ($managedMajorId ? " AND major_id = ?" : ""), $managedMajorId ? [$managedLevelId, $managedMajorId] : [$managedLevelId])->total
                : Course::count(),
            'filesCount' => $managedLevelId
                ? (int) Database::fetch("SELECT COUNT(*) as total FROM files f JOIN courses c ON c.id = f.course_id WHERE c.level_id = ?" . ($managedMajorId ? " AND c.major_id = ?" : ""), $managedMajorId ? [$managedLevelId, $managedMajorId] : [$managedLevelId])->total
                : File::count(),
            'usersCount' => User::count(),
            'announcementsCount' => Announcement::count(),
            'recentFiles' => File::getRecent(8, $managedLevelId, $managedMajorId),
            'recentLogs' => ActivityLog::getRecent(8),
            'totalFileSize' => $sizeFormatted,
            'totalSizeBytes' => $totalSize,
            'fileTypes' => $this->getFileTypeStats($managedLevelId, $managedMajorId),
            'downloadStats' => Course::getDownloadStats($managedLevelId, $managedMajorId),
            'newFilesCount' => File::getRecentCount(48, $managedLevelId, $managedMajorId),
            'userRoles' => User::getCountByRole(),
            'courseCountsByMajor' => Course::getCountByMajor($managedLevelId, $managedMajorId),
            'storageLimit' => 500 * 1024 * 1024,
        ]);
    }

    private function getFileTypeStats(?int $levelId = null, ?int $majorId = null): array
    {
        $types = ['lecture', 'summary', 'model', 'exam', 'other'];
        $stats = [];
        $labels = ['lecture' => 'محاضرة', 'summary' => 'ملخص', 'model' => 'نموذج', 'exam' => 'اختبار', 'other' => 'أخرى'];
        $icons = ['lecture' => 'fa-book', 'summary' => 'fa-file-lines', 'model' => 'fa-clipboard', 'exam' => 'fa-pen-to-square', 'other' => 'fa-folder'];
        $colors = ['lecture' => '#1a73e8', 'summary' => '#34a853', 'model' => '#f59e0b', 'exam' => '#dc3545', 'other' => '#6b7280'];

        foreach ($types as $type) {
            $count = count(File::getByType($type, 9999, $levelId, $majorId));
            $stats[] = [
                'type' => $type,
                'label' => $labels[$type],
                'icon' => $icons[$type],
                'color' => $colors[$type],
                'count' => $count,
            ];
        }
        return $stats;
    }
}
