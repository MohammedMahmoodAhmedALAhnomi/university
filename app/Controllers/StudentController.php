<?php

namespace App\Controllers;

use App\Core\View;
use App\Models\Bookmark;
use App\Models\Schedule;
use App\Models\Major;
use App\Models\Level;
use App\Models\File;

class StudentController
{
    public function gpaCalculator(): void
    {
        View::render('student/gpa_calculator', [
            'pageTitle' => 'حاسبة المعدل الأكاديمي (GPA)',
        ]);
    }

    public function bookmarks(): void
    {
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            flash('danger', 'يرجى تسجيل الدخول لعرض الملفات المحفوظة');
            redirect(url('/login'));
            return;
        }

        $bookmarks = Bookmark::getByUser((int)$userId);

        View::render('student/bookmarks', [
            'pageTitle' => 'المحافظ والملفات المحفوظة',
            'bookmarks' => $bookmarks,
        ]);
    }

    public function toggleBookmark(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            echo json_encode(['success' => false, 'message' => 'يرجى تسجيل الدخول أولاً لإضافة الملف إلى المفضلة'], JSON_UNESCAPED_UNICODE);
            return;
        }

        $fileId = (int)($_POST['file_id'] ?? 0);
        if (!$fileId) {
            echo json_encode(['success' => false, 'message' => 'معرف الملف غير صالح'], JSON_UNESCAPED_UNICODE);
            return;
        }

        $result = Bookmark::toggle((int)$userId, $fileId);
        echo json_encode(array_merge(['success' => true], $result), JSON_UNESCAPED_UNICODE);
    }

    public function schedule(): void
    {
        $majors = Major::getActive();
        $levels = Level::getActive();

        $selectedMajorId = (int)($_GET['major_id'] ?? ($_SESSION['user_major_id'] ?? ($majors[0]->id ?? 0)));
        $selectedLevelId = (int)($_GET['level_id'] ?? ($_SESSION['user_level_id'] ?? ($levels[0]->id ?? 0)));
        $selectedGroup = trim($_GET['group_name'] ?? 'all');

        $groups = [];
        $lectures = [];
        $exams = [];
        $upcomingExams = [];
        $pdfSchedules = [];

        if ($selectedMajorId && $selectedLevelId) {
            $groups = Schedule::getDistinctGroups($selectedMajorId, $selectedLevelId);
            $lectures = Schedule::getByMajorAndLevel($selectedMajorId, $selectedLevelId, 'lecture', $selectedGroup);
            $exams = Schedule::getByMajorAndLevel($selectedMajorId, $selectedLevelId, 'exam', $selectedGroup);
            $pdfSchedules = Schedule::getByMajorAndLevel($selectedMajorId, $selectedLevelId, 'pdf_file', $selectedGroup);
            $upcomingExams = Schedule::getUpcomingExams($selectedMajorId, $selectedLevelId, 5);
        }

        View::render('student/schedule', [
            'pageTitle' => 'جدول المحاضرات والامتحانات',
            'majors' => $majors,
            'levels' => $levels,
            'groups' => $groups,
            'selectedMajorId' => $selectedMajorId,
            'selectedLevelId' => $selectedLevelId,
            'selectedGroup' => $selectedGroup,
            'lectures' => $lectures,
            'exams' => $exams,
            'pdfSchedules' => $pdfSchedules,
            'upcomingExams' => $upcomingExams,
        ]);
    }
}
