<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Major;
use App\Models\Level;
use App\Models\Semester;
use App\Models\Course;
use App\Models\File;
use App\Models\Announcement;
use App\Models\Rating;
use App\Models\Setting;
use App\Models\User;
use App\Config\Database;

class ApiController extends Controller
{
    public function __construct()
    {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit;
        }
    }

    private function getJsonInput(): array
    {
        $raw = file_get_contents('php://input');
        if (!empty($raw)) {
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }
        return $_POST ?? [];
    }

    protected function jsonResponse(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    public function login(): void
    {
        $input = $this->getJsonInput();
        $email = trim($input['email'] ?? '');
        $password = $input['password'] ?? '';

        if (empty($email) || empty($password)) {
            $this->jsonResponse(['status' => 'error', 'message' => 'يرجى إدخال البريد الإلكتروني وكلمة المرور'], 400);
        }

        $user = User::findByEmail($email);
        if (!$user || !User::verifyPassword($password, $user->password)) {
            $this->jsonResponse(['status' => 'error', 'message' => 'البريد الإلكتروني أو كلمة المرور غير صحيحة'], 401);
        }

        if (!$user->is_active) {
            $this->jsonResponse(['status' => 'error', 'message' => 'الحساب غير نشط، يرجى التواصل مع الإدارة'], 403);
        }

        User::updateLastLogin($user->id);

        // Generate a token representation
        $token = base64_encode($user->id . ':' . hash('sha256', $user->password . 'university_salt'));

        $this->jsonResponse([
            'status' => 'success',
            'message' => 'تم تسجيل الدخول بنجاح',
            'user' => [
                'id' => (int)$user->id,
                'full_name' => $user->full_name,
                'email' => $user->email,
                'phone' => $user->phone ?? '',
                'role' => $user->role,
                'major_id' => $user->major_id ? (int)$user->major_id : null,
                'managed_level_id' => $user->managed_level_id ? (int)$user->managed_level_id : null,
                'managed_major_id' => $user->managed_major_id ? (int)$user->managed_major_id : null,
            ],
            'token' => $token
        ]);
    }

    public function register(): void
    {
        $input = $this->getJsonInput();
        $fullName = trim($input['full_name'] ?? '');
        $email = trim($input['email'] ?? '');
        $password = $input['password'] ?? '';
        $phone = trim($input['phone'] ?? '');
        $majorId = !empty($input['major_id']) ? (int)$input['major_id'] : null;

        if (empty($fullName) || empty($email) || empty($password)) {
            $this->jsonResponse(['status' => 'error', 'message' => 'جميع الحقول الأساسية مطلوبة (الاسم الكامل، البريد الإلكتروني، كلمة المرور)'], 400);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->jsonResponse(['status' => 'error', 'message' => 'البريد الإلكتروني غير صالح'], 400);
        }

        $existing = User::findByEmail($email);
        if ($existing) {
            $this->jsonResponse(['status' => 'error', 'message' => 'البريد الإلكتروني مسجل بالفعل'], 409);
        }

        $userId = User::create([
            'full_name' => $fullName,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'phone' => $phone,
            'major_id' => $majorId,
            'role' => 'guest',
            'is_active' => 1
        ]);

        $user = User::findById($userId);
        $token = base64_encode($user->id . ':' . hash('sha256', $user->password . 'university_salt'));

        $this->jsonResponse([
            'status' => 'success',
            'message' => 'تم إنشاء الحساب بنجاح',
            'user' => [
                'id' => (int)$user->id,
                'full_name' => $user->full_name,
                'email' => $user->email,
                'phone' => $user->phone ?? '',
                'role' => $user->role,
                'major_id' => $user->major_id ? (int)$user->major_id : null,
            ],
            'token' => $token
        ]);
    }

    public function home(): void
    {
        $pinned = Announcement::getPinned();
        $majors = Major::getWithCourseCount();
        $recentFiles = Database::fetchAll(
            "SELECT f.*, c.name as course_name, m.name as major_name
             FROM files f
             JOIN courses c ON c.id = f.course_id
             JOIN majors m ON m.id = c.major_id
             WHERE f.is_approved = 1
             ORDER BY f.created_at DESC
             LIMIT 10"
        );

        $totalMajors = Database::fetch("SELECT COUNT(*) as total FROM majors WHERE is_active = 1")->total ?? 0;
        $totalCourses = Database::fetch("SELECT COUNT(*) as total FROM courses WHERE is_active = 1")->total ?? 0;
        $totalFiles = Database::fetch("SELECT COUNT(*) as total FROM files WHERE is_approved = 1")->total ?? 0;
        $totalDownloads = Database::fetch("SELECT SUM(download_count) as total FROM files WHERE is_approved = 1")->total ?? 0;

        $settings = Setting::getAll();

        $this->jsonResponse([
            'status' => 'success',
            'data' => [
                'pinned_announcements' => $pinned,
                'majors' => $majors,
                'recent_files' => $recentFiles,
                'stats' => [
                    'majors' => (int)$totalMajors,
                    'courses' => (int)$totalCourses,
                    'files' => (int)$totalFiles,
                    'downloads' => (int)$totalDownloads,
                ],
                'settings' => $settings
            ]
        ]);
    }

    public function majors(): void
    {
        $majors = Major::getWithCourseCount();
        $this->jsonResponse([
            'status' => 'success',
            'data' => $majors
        ]);
    }

    public function majorDetails(): void
    {
        $id = (int)($this->getParam('id') ?? $_GET['id'] ?? 0);
        if (!$id) {
            $this->jsonResponse(['status' => 'error', 'message' => 'معرف التخصص غير صحيح'], 400);
        }

        $major = Major::findById($id);
        if (!$major) {
            $this->jsonResponse(['status' => 'error', 'message' => 'التخصص غير موجود'], 404);
        }

        $levels = Level::getActiveSorted();
        $semesters = Semester::getActiveSorted();

        // Get courses for this major
        $courses = Database::fetchAll(
            "SELECT c.*, l.name as level_name, l.level_number, s.name as semester_name, s.semester_number
             FROM courses c
             JOIN levels l ON l.id = c.level_id
             JOIN semesters s ON s.id = c.semester_id
             WHERE c.major_id = ? AND c.is_active = 1
             ORDER BY l.level_number, s.semester_number, c.name",
            [$id]
        );

        $courseIds = array_map(fn($c) => $c->id, $courses);
        $ratings = !empty($courseIds) ? Rating::getForCourses($courseIds) : [];
        foreach ($courses as $c) {
            $r = $ratings[$c->id] ?? null;
            $c->avg_rating = $r ? round((float)$r->avg_rating, 1) : 0;
            $c->rating_count = $r ? (int)$r->total : 0;

            // Get file count for each course
            $fileCount = Database::fetch(
                "SELECT COUNT(*) as total FROM files WHERE course_id = ? AND is_approved = 1",
                [$c->id]
            )->total ?? 0;
            $c->files_count = (int)$fileCount;
        }

        $this->jsonResponse([
            'status' => 'success',
            'data' => [
                'major' => $major,
                'levels' => $levels,
                'semesters' => $semesters,
                'courses' => $courses
            ]
        ]);
    }

    public function courseDetails(): void
    {
        $id = (int)($this->getParam('id') ?? $_GET['id'] ?? 0);
        if (!$id) {
            $this->jsonResponse(['status' => 'error', 'message' => 'معرف المادة غير صحيح'], 400);
        }

        $course = Course::findWithDetails($id);
        if (!$course) {
            $this->jsonResponse(['status' => 'error', 'message' => 'المادة الدراسية غير موجودة'], 404);
        }

        $files = File::getApprovedByCourse($id);

        $categorizedFiles = [
            'lecture' => [],
            'summary' => [],
            'model' => [],
            'exam' => [],
            'other' => []
        ];

        foreach ($files as $f) {
            $type = $f->file_type ?? 'other';
            if (isset($categorizedFiles[$type])) {
                $categorizedFiles[$type][] = $f;
            } else {
                $categorizedFiles['other'][] = $f;
            }
        }

        $this->jsonResponse([
            'status' => 'success',
            'data' => [
                'course' => $course,
                'all_files' => $files,
                'categorized_files' => $categorizedFiles
            ]
        ]);
    }

    public function rateCourse(): void
    {
        $id = (int)($this->getParam('id') ?? $_GET['id'] ?? 0);
        $input = $this->getJsonInput();
        $ratingValue = (int)($input['rating'] ?? 0);
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

        if (!$id || $ratingValue < 1 || $ratingValue > 5) {
            $this->jsonResponse(['status' => 'error', 'message' => 'القيم المدخلة غير صحيحة'], 400);
        }

        Rating::addOrUpdate($id, $ratingValue, $ip);

        $newAvg = Rating::getAverageForCourse($id);
        $newCount = Rating::getCountForCourse($id);

        $this->jsonResponse([
            'status' => 'success',
            'message' => 'شكراً على تقييمك للمادة!',
            'data' => [
                'avg_rating' => round($newAvg, 1),
                'rating_count' => $newCount
            ]
        ]);
    }

    public function announcements(): void
    {
        $announcements = Announcement::getActive();
        $this->jsonResponse([
            'status' => 'success',
            'data' => $announcements
        ]);
    }

    public function announcementDetails(): void
    {
        $id = (int)($this->getParam('id') ?? $_GET['id'] ?? 0);
        if (!$id) {
            $this->jsonResponse(['status' => 'error', 'message' => 'معرف الإعلان غير صحيح'], 400);
        }

        $announcement = Announcement::findById($id);
        if (!$announcement) {
            $this->jsonResponse(['status' => 'error', 'message' => 'الإعلان غير موجود'], 404);
        }

        $this->jsonResponse([
            'status' => 'success',
            'data' => $announcement
        ]);
    }

    public function search(): void
    {
        $q = trim($this->getParam('q', $_GET['q'] ?? ''));
        $majorId = (int)($this->getParam('major_id', $_GET['major_id'] ?? 0));
        $levelId = (int)($this->getParam('level_id', $_GET['level_id'] ?? 0));
        $semesterId = (int)($this->getParam('semester_id', $_GET['semester_id'] ?? 0));

        $filters = [];
        if ($majorId) $filters['major_id'] = $majorId;
        if ($levelId) $filters['level_id'] = $levelId;
        if ($semesterId) $filters['semester_id'] = $semesterId;

        $courses = [];
        $files = [];

        if (!empty($q) || !empty($filters)) {
            $courses = Course::search($q, $filters);
            $files = File::search($q);
        }

        $this->jsonResponse([
            'status' => 'success',
            'data' => [
                'query' => $q,
                'courses' => $courses,
                'files' => $files
            ]
        ]);
    }

    public function downloadFile(): void
    {
        $id = (int)($this->getParam('id') ?? $_GET['id'] ?? 0);
        $file = File::findById($id);
        if (!$file || !$file->is_approved) {
            $this->jsonResponse(['status' => 'error', 'message' => 'الملف غير موجود'], 404);
        }

        File::incrementDownloads($id);

        $appUrl = Setting::get('app_url', 'http://localhost/university');
        $downloadUrl = rtrim($appUrl, '/') . '/files/' . $file->id . '/download';

        $this->jsonResponse([
            'status' => 'success',
            'data' => [
                'file' => $file,
                'download_url' => $downloadUrl
            ]
        ]);
    }

    public function settings(): void
    {
        $settings = Setting::getAll();
        $this->jsonResponse([
            'status' => 'success',
            'data' => $settings
        ]);
    }
}
