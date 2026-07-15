<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Course;
use App\Models\Major;
use App\Models\Level;
use App\Models\Semester;
use App\Models\File;
use App\Models\Rating;

class CourseController extends Controller
{
    public function index(): void
    {
        $majorId = $_GET['major_id'] ?? null;
        $majors = \App\Models\Major::getActive();
        if ($majorId) {
            $courses = Course::getAllWithDetails("WHERE c.major_id = ?", [$majorId]);
        } else {
            $courses = Course::getAllWithDetails();
        }
        $this->view('front/courses', ['courses' => $courses, 'majors' => $majors]);
    }

    public function show(): void
    {
        $id = $this->getParam('id');
        $course = Course::findWithDetails($id);

        if (!$course) {
            flash('error', 'المادة غير موجودة');
            redirect(url('/courses'));
        }

        $sort = $this->getParam('sort', 'newest');
        $page = max(1, (int)$this->getParam('page', 1));
        $result = File::getByCourse($id, $sort, $page);
        $ratingDistribution = Rating::getForCourse($id);
        $this->view('front/course_details', [
            'course' => $course,
            'files' => $result['files'],
            'currentSort' => $sort,
            'currentPage' => $page,
            'totalPages' => $result['pages'],
            'totalFiles' => $result['total'],
            'ratingDistribution' => $ratingDistribution,
        ]);
    }

    public function files(): void
    {
        $id = $this->getParam('id');
        $course = Course::findWithDetails($id);

        if (!$course) {
            flash('error', 'المادة غير موجودة');
            redirect(url('/courses'));
        }

        $files = File::getByCourse($id);
        $this->view('front/course_details', ['course' => $course, 'files' => $files]);
    }

    public function adminIndex(): void
    {
        $majorId = $_GET['major_id'] ?? null;
        $majors = \App\Models\Major::getActive();
        $managedLevelId = get_managed_level_id();
        $managedMajorId = get_managed_major_id();

        if ($managedLevelId && $managedMajorId) {
            $courses = Course::getAllWithDetails("WHERE c.level_id = ? AND c.major_id = ?", [$managedLevelId, $managedMajorId]);
        } elseif ($managedLevelId) {
            $courses = Course::getAllWithDetails("WHERE c.level_id = ?", [$managedLevelId]);
        } elseif ($majorId) {
            $courses = Course::getAllWithDetails("WHERE c.major_id = ?", [$majorId]);
        } else {
            $courses = Course::getAllWithDetails();
        }
        $this->view('admin/courses/index', ['courses' => $courses, 'majors' => $majors, 'managedLevelId' => $managedLevelId]);
    }

    public function create(): void
    {
        $managedLevelId = get_managed_level_id();
        $managedMajorId = get_managed_major_id();
        $majors = Major::getActive();
        $levels = Level::getActive();
        $semesters = Semester::getActive();
        $this->view('admin/courses/create', [
            'majors' => $majors,
            'levels' => $levels,
            'semesters' => $semesters,
            'managedLevelId' => $managedLevelId,
            'managedMajorId' => $managedMajorId,
        ]);
    }

    public function store(): void
    {
        if (!$this->isPost() || !verify_csrf()) {
            redirect(url('/admin/courses'));
        }

        $managedLevelId = get_managed_level_id();
        $managedMajorId = get_managed_major_id();

        $data = [
            'name' => trim($this->postParam('name', '')),
            'major_id' => $managedMajorId ?: $this->postParam('major_id', ''),
            'level_id' => $managedLevelId ?: $this->postParam('level_id', ''),
            'semester_id' => $this->postParam('semester_id', ''),
        ];

        $errors = $this->validate($data, [
            'name' => 'required',
            'major_id' => 'required|numeric',
            'level_id' => 'required|numeric',
            'semester_id' => 'required|numeric',
        ]);

        if (!empty($errors)) {
            flash('error', 'يرجى التحقق من الحقول المطلوبة');
            $majors = Major::getActive();
            $levels = Level::getActive();
            $semesters = Semester::getActive();
            $this->view('admin/courses/create', [
                'errors' => $errors,
                'data' => $data,
                'majors' => $majors,
                'levels' => $levels,
                'semesters' => $semesters,
                'managedLevelId' => $managedLevelId,
                'managedMajorId' => $managedMajorId,
            ]);
            return;
        }

        $courseId = Course::create($data);
        log_activity('create', 'courses', $courseId, 'إضافة مادة جديدة: ' . $data['name']);
        flash('success', 'تم إضافة المادة بنجاح');
        redirect(url('/admin/courses'));
    }

    public function edit(): void
    {
        $id = $this->getParam('id');
        $course = Course::find($id);

        if (!$course) {
            flash('error', 'المادة غير موجودة');
            redirect(url('/admin/courses'));
        }

        $managedLevelId = get_managed_level_id();
        $managedMajorId = get_managed_major_id();
        if ($managedLevelId && $managedMajorId && ($course->level_id != $managedLevelId || $course->major_id != $managedMajorId)) {
            flash('error', 'لا يمكنك تعديل هذه المادة');
            redirect(url('/admin/courses'));
        }
        if ($managedLevelId && !$managedMajorId && $course->level_id != $managedLevelId) {
            flash('error', 'لا يمكنك تعديل هذه المادة');
            redirect(url('/admin/courses'));
        }

        $majors = Major::getActive();
        $levels = Level::getActive();
        $semesters = Semester::getActive();
        $this->view('admin/courses/edit', [
            'course' => $course,
            'majors' => $majors,
            'levels' => $levels,
            'semesters' => $semesters,
            'managedLevelId' => $managedLevelId,
            'managedMajorId' => $managedMajorId,
        ]);
    }

    public function update(): void
    {
        if (!$this->isPost() || !verify_csrf()) {
            redirect(url('/admin/courses'));
        }

        $id = $this->postParam('id');
        $course = Course::find($id);

        if (!$course) {
            flash('error', 'المادة غير موجودة');
            redirect(url('/admin/courses'));
        }

        $managedLevelId = get_managed_level_id();
        $managedMajorId = get_managed_major_id();
        if ($managedLevelId && $managedMajorId && ($course->level_id != $managedLevelId || $course->major_id != $managedMajorId)) {
            flash('error', 'لا يمكنك تعديل هذه المادة');
            redirect(url('/admin/courses'));
        }
        if ($managedLevelId && !$managedMajorId && $course->level_id != $managedLevelId) {
            flash('error', 'لا يمكنك تعديل هذه المادة');
            redirect(url('/admin/courses'));
        }

        $data = [
            'name' => trim($this->postParam('name', '')),
            'major_id' => $managedMajorId ?: $this->postParam('major_id', ''),
            'level_id' => $managedLevelId ?: $this->postParam('level_id', ''),
            'semester_id' => $this->postParam('semester_id', ''),
        ];

        $errors = $this->validate($data, [
            'name' => 'required',
            'major_id' => 'required|numeric',
            'level_id' => 'required|numeric',
            'semester_id' => 'required|numeric',
        ]);

        if (!empty($errors)) {
            flash('error', 'يرجى التحقق من الحقول المطلوبة');
            $majors = Major::getActive();
            $levels = Level::getActive();
            $semesters = Semester::getActive();
            $this->view('admin/courses/edit', [
                'course' => $course,
                'errors' => $errors,
                'majors' => $majors,
                'levels' => $levels,
                'semesters' => $semesters,
                'managedLevelId' => $managedLevelId,
                'managedMajorId' => $managedMajorId,
            ]);
            return;
        }

        Course::updateRecord($id, $data);
        log_activity('update', 'courses', $id, 'تحديث المادة: ' . $data['name']);
        flash('success', 'تم تحديث المادة بنجاح');
        redirect(url('/admin/courses'));
    }

    public function delete(): void
    {
        $id = $this->getParam('id');
        $course = Course::find($id);

        if (!$course) {
            flash('error', 'المادة غير موجودة');
            redirect(url('/admin/courses'));
        }

        $managedLevelId = get_managed_level_id();
        $managedMajorId = get_managed_major_id();
        if ($managedLevelId && $managedMajorId && ($course->level_id != $managedLevelId || $course->major_id != $managedMajorId)) {
            flash('error', 'لا يمكنك حذف هذه المادة');
            redirect(url('/admin/courses'));
        }
        if ($managedLevelId && !$managedMajorId && $course->level_id != $managedLevelId) {
            flash('error', 'لا يمكنك حذف هذه المادة');
            redirect(url('/admin/courses'));
        }

        Course::deleteRecord($id);
        log_activity('delete', 'courses', $id, 'حذف المادة: ' . $course->name);
        flash('success', 'تم حذف المادة بنجاح');
        redirect(url('/admin/courses'));
    }

    public function rate(): void
    {
        $courseId = $this->postParam('course_id');
        $rating = (int)$this->postParam('rating');

        if (!$courseId || $rating < 1 || $rating > 5) {
            $this->json(['success' => false, 'message' => 'بيانات غير صالحة'], 400);
            return;
        }

        $course = Course::find($courseId);
        if (!$course) {
            $this->json(['success' => false, 'message' => 'المادة غير موجودة'], 404);
            return;
        }

        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $sessionId = session_id();

        if (Rating::hasVoted($courseId, $ip, $sessionId)) {
            $this->json(['success' => false, 'message' => 'لقد قمت بالتقييم مسبقاً']);
            return;
        }

        Rating::vote($courseId, $rating, $ip, $sessionId);

        $avg = Rating::getAverageForCourse($courseId);
        $count = Rating::getCountForCourse($courseId);

        $this->json([
            'success' => true,
            'avg_rating' => $avg,
            'rating_count' => $count,
            'message' => 'تم تسجيل تقييمك بنجاح',
        ]);
    }
}
