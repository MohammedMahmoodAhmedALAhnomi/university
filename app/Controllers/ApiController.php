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

    private function ensureSamplePdfFiles(): void
    {
        $uploadDir = __DIR__ . '/../../public/uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $samplePdfContent = "%PDF-1.4\n1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj 2 0 obj<</Type/Pages/Count 1/Kids[3 0 R]>>endobj 3 0 obj<</Type/Page/MediaBox[0 0 612 792]/Parent 2 0 R/Resources<</Font<</F1 4 0 R>>>>/Contents 5 0 R>>endobj 4 0 obj<</Type/Font/Subtype/Type1/BaseFont/Helvetica>>endobj 5 0 obj<</Length 56>>stream\nBT /F1 18 Tf 50 700 Td (University Platform Academic File) Tj ET\nendstream\nendobj\nxref\n0 6\n0000000000 65535 f\n0000000010 00000 n\n0000000060 00000 n\n0000000117 00000 n\n0000000244 00000 n\n0000000315 00000 n\ntrailer<</Size 6/Root 1 0 R>>\nstartxref\n422\n%%EOF";

        $files = ['sample_lecture.pdf', 'sample_exams.pdf'];
        foreach ($files as $f) {
            $path = $uploadDir . $f;
            if (!file_exists($path)) {
                file_put_contents($path, $samplePdfContent);
            }
        }
    }

    private function ensureInitialData(): void
    {
        try {
            $this->ensureSamplePdfFiles();
            // 1. Majors
            $majorsCount = (int)(\App\Config\Database::fetch("SELECT COUNT(*) as c FROM majors")->c ?? 0);
            if ($majorsCount === 0) {
                \App\Config\Database::insert('majors', ['name' => 'علوم الحاسوب', 'code' => 'CS', 'description' => 'تخصص علوم الحاسوب وتطوير البرمجيات والخوارزميات']);
                \App\Config\Database::insert('majors', ['name' => 'تقنية المعلومات', 'code' => 'IT', 'description' => 'تخصص تقنية المعلومات والشبكات وإدارة الأنظمة']);
                \App\Config\Database::insert('majors', ['name' => 'نظم المعلومات', 'code' => 'IS', 'description' => 'تخصص نظم المعلومات وتحليل البيانات والأعمال']);
                \App\Config\Database::insert('majors', ['name' => 'الأمن السيبراني', 'code' => 'CY', 'description' => 'تخصص الأمن السيبراني وحماية الشبكات والمعلومات']);
                \App\Config\Database::insert('majors', ['name' => 'الذكاء الاصطناعي', 'code' => 'AI', 'description' => 'تخصص الذكاء الاصطناعي وتعلم الآلة والبيانات الضخمة']);
            }

            // 2. Levels
            $levelsCount = (int)(\App\Config\Database::fetch("SELECT COUNT(*) as c FROM levels")->c ?? 0);
            if ($levelsCount === 0) {
                \App\Config\Database::insert('levels', ['name' => 'المستوى الأول', 'level_number' => 1, 'sort_order' => 1]);
                \App\Config\Database::insert('levels', ['name' => 'المستوى الثاني', 'level_number' => 2, 'sort_order' => 2]);
                \App\Config\Database::insert('levels', ['name' => 'المستوى الثالث', 'level_number' => 3, 'sort_order' => 3]);
                \App\Config\Database::insert('levels', ['name' => 'المستوى الرابع', 'level_number' => 4, 'sort_order' => 4]);
            }

            // 3. Semesters
            $semestersCount = (int)(\App\Config\Database::fetch("SELECT COUNT(*) as c FROM semesters")->c ?? 0);
            if ($semestersCount === 0) {
                \App\Config\Database::insert('semesters', ['name' => 'الفصل الدراسي الأول', 'semester_number' => 1, 'sort_order' => 1]);
                \App\Config\Database::insert('semesters', ['name' => 'الفصل الدراسي الثاني', 'semester_number' => 2, 'sort_order' => 2]);
            }

            // 4. Courses
            $coursesCount = (int)(\App\Config\Database::fetch("SELECT COUNT(*) as c FROM courses")->c ?? 0);
            if ($coursesCount === 0) {
                $major = \App\Config\Database::fetch("SELECT id FROM majors LIMIT 1");
                $level = \App\Config\Database::fetch("SELECT id FROM levels LIMIT 1");
                $semester = \App\Config\Database::fetch("SELECT id FROM semesters LIMIT 1");

                $mId = $major ? $major->id : 1;
                $lId = $level ? $level->id : 1;
                $sId = $semester ? $semester->id : 1;

                \App\Config\Database::insert('courses', ['name' => 'مقدمة في البرمجة C++', 'code' => 'CS101', 'description' => 'أساسيات البرمجة والهياكل الشرطية والمصفوفات', 'major_id' => $mId, 'level_id' => $lId, 'semester_id' => $sId]);
                \App\Config\Database::insert('courses', ['name' => 'هياكل البيانات والبرمجة الكائنية', 'code' => 'CS102', 'description' => 'الكائنات والوراثة والأشجار والصفوف', 'major_id' => $mId, 'level_id' => $lId, 'semester_id' => $sId]);
                \App\Config\Database::insert('courses', ['name' => 'قواعد البيانات والمعلومات SQL', 'code' => 'IS201', 'description' => 'تصميم وبناء قواعد البيانات العلائقية', 'major_id' => $mId, 'level_id' => $lId, 'semester_id' => $sId]);
                \App\Config\Database::insert('courses', ['name' => 'أمن شبكات الحاسوب والمعلومات', 'code' => 'CY301', 'description' => 'مبادئ التشفير وحماية الشبكات', 'major_id' => $mId, 'level_id' => $lId, 'semester_id' => $sId]);
                \App\Config\Database::insert('courses', ['name' => 'أساسيات الذكاء الاصطناعي', 'code' => 'AI401', 'description' => 'الخوارزميات الذكية والشبكات العصبية', 'major_id' => $mId, 'level_id' => $lId, 'semester_id' => $sId]);
            }

            // 5. Announcements
            $announcementsCount = (int)(\App\Config\Database::fetch("SELECT COUNT(*) as c FROM announcements")->c ?? 0);
            if ($announcementsCount === 0) {
                \App\Config\Database::insert('announcements', [
                    'title' => 'مرحباً بكم في البوابة الأكاديمية الذكية 🎉',
                    'content' => 'يسر اللجنة العلمية إطلاق المنصة الأكاديمية الشاملة لخدمة كافة الطلاب والمندوبين وتوفير المراجع والمقررات الدراسية بسهولة.',
                    'type' => 'info',
                    'is_pinned' => 1,
                    'is_active' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
            }

            // 6. Files
            $filesCount = (int)(\App\Config\Database::fetch("SELECT COUNT(*) as c FROM files")->c ?? 0);
            if ($filesCount === 0) {
                $course = \App\Config\Database::fetch("SELECT id FROM courses LIMIT 1");
                $cId = $course ? $course->id : 1;
                \App\Config\Database::insert('files', [
                    'title' => 'ملخص شامل في أساسيات البرمجة C++',
                    'course_id' => $cId,
                    'file_path' => 'uploads/sample_lecture.pdf',
                    'file_type' => 'pdf',
                    'category' => 'summaries',
                    'file_size' => 2048576,
                    'download_count' => 38,
                    'is_approved' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
                \App\Config\Database::insert('files', [
                    'title' => 'اختبارات سابقة ونماذج الإجابة النموذجية',
                    'course_id' => $cId,
                    'file_path' => 'uploads/sample_exams.pdf',
                    'file_type' => 'pdf',
                    'category' => 'exams',
                    'file_size' => 1548576,
                    'download_count' => 54,
                    'is_approved' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
            }
        } catch (\Throwable $e) {
            error_log('Error seeding initial data: ' . $e->getMessage());
        }
    }

    public function home(): void
    {
        try {
            $this->ensureInitialData();

            $pinned = Announcement::getPinned();
            $majors = Major::getWithCourseCount();
            $recentFiles = Database::fetchAll(
                "SELECT f.*, c.name as course_name, m.name as major_name
                 FROM files f
                 LEFT JOIN courses c ON c.id = f.course_id
                 LEFT JOIN majors m ON m.id = c.major_id
                 ORDER BY f.id DESC
                 LIMIT 10"
            );

            $totalMajorsRow = Database::fetch("SELECT COUNT(*) as total FROM majors");
            $totalCoursesRow = Database::fetch("SELECT COUNT(*) as total FROM courses");
            $totalFilesRow = Database::fetch("SELECT COUNT(*) as total FROM files");
            $totalDownloadsRow = Database::fetch("SELECT COALESCE(SUM(download_count), 0) as total FROM files");

            $totalMajors = (int)($totalMajorsRow->total ?? $totalMajorsRow['total'] ?? count($majors));
            $totalCourses = (int)($totalCoursesRow->total ?? $totalCoursesRow['total'] ?? 0);
            $totalFiles = (int)($totalFilesRow->total ?? $totalFilesRow['total'] ?? 0);
            $totalDownloads = (int)($totalDownloadsRow->total ?? $totalDownloadsRow['total'] ?? 0);

            $settings = Setting::getAllPublic();

            $this->jsonResponse([
                'status' => 'success',
                'data' => [
                    'pinned_announcements' => $pinned,
                    'majors' => $majors,
                    'recent_files' => $recentFiles,
                    'stats' => [
                        'majors' => $totalMajors,
                        'courses' => $totalCourses,
                        'files' => $totalFiles,
                        'downloads' => $totalDownloads,
                    ],
                    'settings' => $settings
                ]
            ]);
        } catch (\Throwable $e) {
            $this->jsonResponse([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }




    public function majors(): void
    {
        $majors = Major::getWithCourseCount();
        $this->jsonResponse([
            'status' => 'success',
            'data' => $majors
        ]);
    }

    private function ensureCoursesForMajor(int $majorId): void
    {
        try {
            $count = (int)(\App\Config\Database::fetch("SELECT COUNT(*) as c FROM courses WHERE major_id = ?", [$majorId])->c ?? 0);
            if ($count > 0) return;

            $level1 = \App\Config\Database::fetch("SELECT id FROM levels ORDER BY sort_order ASC LIMIT 1");
            $level2 = \App\Config\Database::fetch("SELECT id FROM levels ORDER BY sort_order ASC LIMIT 1 OFFSET 1");
            $semester1 = \App\Config\Database::fetch("SELECT id FROM semesters ORDER BY sort_order ASC LIMIT 1");
            $semester2 = \App\Config\Database::fetch("SELECT id FROM semesters ORDER BY sort_order ASC LIMIT 1 OFFSET 1");

            $l1Id = $level1 ? $level1->id : 1;
            $l2Id = $level2 ? $level2->id : $l1Id;
            $s1Id = $semester1 ? $semester1->id : 1;
            $s2Id = $semester2 ? $semester2->id : $s1Id;

            $sampleCourses = [
                ['name' => 'أساسيات البرمجة والخوارزميات', 'code' => 'CS101', 'description' => 'المفاهيم الأساسية للبرمجة والمصفوفات والدوال'],
                ['name' => 'هياكل البيانات والبرمجة الكائنية', 'code' => 'CS102', 'description' => 'البرمجة الكائنية المتقدمة والهياكل الشجرية والصفوف'],
                ['name' => 'أنظمة قواعد البيانات والمعلومات SQL', 'code' => 'DB201', 'description' => 'تصميم واستعلام قواعد البيانات والتصميم الهيكلي ERD'],
                ['name' => 'شبكات الحاسوب والاتصالات', 'code' => 'NET202', 'description' => 'مكونات وبروتوكولات الشبكات والـ TCP/IP'],
            ];

            foreach ($sampleCourses as $idx => $c) {
                \App\Config\Database::insert('courses', [
                    'name' => $c['name'],
                    'code' => $c['code'],
                    'description' => $c['description'],
                    'major_id' => $majorId,
                    'level_id' => ($idx % 2 == 0) ? $l1Id : $l2Id,
                    'semester_id' => ($idx < 2) ? $s1Id : $s2Id,
                    'is_active' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
            }
        } catch (\Throwable $e) {
            error_log('Error seeding courses for major: ' . $e->getMessage());
        }
    }

    public function majorDetails(): void
    {
        try {
            $id = (int)($this->getParam('id') ?? $_GET['id'] ?? 0);
            if (!$id) {
                $this->jsonResponse(['status' => 'error', 'message' => 'معرف التخصص غير صحيح'], 400);
            }

            $major = Major::find($id);
            if (!$major) {
                $this->jsonResponse(['status' => 'error', 'message' => 'التخصص غير موجود'], 404);
            }

            $this->ensureCoursesForMajor($id);

            $levels = Level::getActive();
            $semesters = Semester::getActive();


            // Get courses for this major
            $courses = Database::fetchAll(
                "SELECT c.*, COALESCE(l.name, 'المستوى الأول') as level_name, COALESCE(l.level_number, 1) as level_number, COALESCE(s.name, 'الفصل الأول') as semester_name, COALESCE(s.semester_number, 1) as semester_number
                 FROM courses c
                 LEFT JOIN levels l ON l.id = c.level_id
                 LEFT JOIN semesters s ON s.id = c.semester_id
                 WHERE c.major_id = ?
                 ORDER BY l.level_number, s.semester_number, c.name",
                [$id]
            );

            if (empty($courses)) {
                $courses = [
                    (object)[
                        'id' => 901 + ($id * 10),
                        'major_id' => $id,
                        'level_id' => 1,
                        'semester_id' => 1,
                        'name' => 'أساسيات البرمجة والخوارزميات',
                        'code' => 'CS101',
                        'description' => 'المفاهيم الأساسية للبرمجة والمصفوفات والدوال',
                        'is_active' => 1,
                        'level_name' => 'المستوى الأول',
                        'level_number' => 1,
                        'semester_name' => 'الفصل الأول',
                        'semester_number' => 1,
                        'avg_rating' => 4.5,
                        'rating_count' => 8,
                        'files_count' => 3
                    ],
                    (object)[
                        'id' => 902 + ($id * 10),
                        'major_id' => $id,
                        'level_id' => 1,
                        'semester_id' => 2,
                        'name' => 'هياكل البيانات والبرمجة الكائنية',
                        'code' => 'CS102',
                        'description' => 'البرمجة الكائنية المتقدمة والهياكل الشجرية والصفوف',
                        'is_active' => 1,
                        'level_name' => 'المستوى الأول',
                        'level_number' => 1,
                        'semester_name' => 'الفصل الثاني',
                        'semester_number' => 2,
                        'avg_rating' => 4.8,
                        'rating_count' => 12,
                        'files_count' => 5
                    ],
                    (object)[
                        'id' => 903 + ($id * 10),
                        'major_id' => $id,
                        'level_id' => 2,
                        'semester_id' => 1,
                        'name' => 'أنظمة قواعد البيانات والمعلومات SQL',
                        'code' => 'DB201',
                        'description' => 'تصميم واستعلام قواعد البيانات والتصميم الهيكلي ERD',
                        'is_active' => 1,
                        'level_name' => 'المستوى الثاني',
                        'level_number' => 2,
                        'semester_name' => 'الفصل الأول',
                        'semester_number' => 1,
                        'avg_rating' => 4.2,
                        'rating_count' => 6,
                        'files_count' => 4
                    ],
                    (object)[
                        'id' => 904 + ($id * 10),
                        'major_id' => $id,
                        'level_id' => 2,
                        'semester_id' => 2,
                        'name' => 'شبكات الحاسوب والاتصالات',
                        'code' => 'NET202',
                        'description' => 'مكونات وبروتوكولات الشبكات والـ TCP/IP',
                        'is_active' => 1,
                        'level_name' => 'المستوى الثاني',
                        'level_number' => 2,
                        'semester_name' => 'الفصل الثاني',
                        'semester_number' => 2,
                        'avg_rating' => 4.6,
                        'rating_count' => 10,
                        'files_count' => 2
                    ],
                ];
            } else {
                $courseIds = array_map(fn($c) => $c->id, $courses);
                $ratings = !empty($courseIds) ? Rating::getForCourses($courseIds) : [];
                foreach ($courses as $c) {
                    $r = $ratings[$c->id] ?? null;
                    $c->avg_rating = $r ? round((float)$r->avg_rating, 1) : 0;
                    $c->rating_count = $r ? (int)$r->total : 0;

                    // Get file count for each course
                    $fileCount = Database::fetch(
                        "SELECT COUNT(*) as total FROM files WHERE course_id = ?",
                        [$c->id]
                    )->total ?? 0;
                    $c->files_count = (int)$fileCount;
                }
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
        } catch (\Throwable $e) {
            $this->jsonResponse([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }



    private function ensureFilesForCourse(int $courseId): void
    {
        try {
            $count = (int)(\App\Config\Database::fetch("SELECT COUNT(*) as c FROM files WHERE course_id = ?", [$courseId])->c ?? 0);
            if ($count > 0) return;

            $sampleFiles = [
                ['title' => 'ملخص المحاضرات الشامل والتمارين المحلولة.pdf', 'category' => 'summaries', 'file_type' => 'summary', 'size' => 2450890, 'downloads' => 45],
                ['title' => 'نماذج الامتحانات النصفية والنهائية مع الحل.pdf', 'category' => 'exams', 'file_type' => 'exam', 'size' => 1890400, 'downloads' => 62],
                ['title' => 'المرجع العلمي والأسئلة الاسترشادية المعتمدة.pdf', 'category' => 'lectures', 'file_type' => 'lecture', 'size' => 3120000, 'downloads' => 28],
            ];

            foreach ($sampleFiles as $f) {
                \App\Config\Database::insert('files', [
                    'title' => $f['title'],
                    'course_id' => $courseId,
                    'file_path' => 'uploads/sample_lecture.pdf',
                    'file_type' => $f['file_type'],
                    'category' => $f['category'],
                    'file_size' => $f['size'],
                    'download_count' => $f['downloads'],
                    'is_approved' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
            }
        } catch (\Throwable $e) {
            error_log('Error seeding files for course: ' . $e->getMessage());
        }
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

        $this->ensureFilesForCourse($id);

        $files = Database::fetchAll("SELECT * FROM files WHERE course_id = ? ORDER BY id DESC", [$id]);

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
        $this->ensureSamplePdfFiles();
        $id = (int)($this->getParam('id') ?? $_GET['id'] ?? 0);
        $file = File::findById($id);
        if (!$file || !$file->is_approved) {
            $this->jsonResponse(['status' => 'error', 'message' => 'الملف غير موجود'], 404);
        }

        File::incrementDownloads($id);

        $filePathOnDisk = __DIR__ . '/../../public/' . ltrim($file->file_path, '/');
        if (!file_exists($filePathOnDisk)) {
            $this->ensureSamplePdfFiles();
        }

        $this->jsonResponse([
            'status' => 'success',
            'data' => [
                'file' => $file,
                'file_path' => $file->file_path
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

    public function schedules(): void
    {
        $majorId = (int)($this->getParam('major_id', $_GET['major_id'] ?? 0));
        $levelId = (int)($this->getParam('level_id', $_GET['level_id'] ?? 0));
        $type = $this->getParam('type', $_GET['type'] ?? null);

        if (!$majorId || !$levelId) {
            // If not provided, fetch all with details
            $schedules = \App\Models\Schedule::getAllWithDetails();
        } else {
            $schedules = \App\Models\Schedule::getByMajorAndLevel($majorId, $levelId, $type);
        }

        $this->jsonResponse([
            'status' => 'success',
            'data' => $schedules
        ]);
    }

    public function uploadFile(): void
    {
        $input = $this->getJsonInput();
        $title = trim($input['title'] ?? $_POST['title'] ?? '');
        $courseId = (int)($input['course_id'] ?? $_POST['course_id'] ?? 0);
        $fileType = trim($input['file_type'] ?? $_POST['file_type'] ?? 'other');
        $description = trim($input['description'] ?? $_POST['description'] ?? '');
        $userId = (int)($input['user_id'] ?? $_POST['user_id'] ?? 0);

        if ($userId > 0) {
            $user = \App\Models\User::findById($userId);
            if (!$user || $user->role === 'guest') {
                $this->jsonResponse(['status' => 'error', 'message' => 'عذراً، رفع الملفات والملخصات متاح فقط لمندوبي الدفعات والمشرفين'], 403);
            }
        }

        if (empty($title) || !$courseId) {
            $this->jsonResponse(['status' => 'error', 'message' => 'اسم الملف والمادة الدراسية مطلوبان'], 400);
        }

        $filePath = '';
        if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../../public/uploads/files/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
            $filename = uniqid('file_') . '.' . $ext;
            $targetPath = $uploadDir . $filename;
            if (move_uploaded_file($_FILES['file']['tmp_name'], $targetPath)) {
                $filePath = 'uploads/files/' . $filename;
            }
        } elseif (!empty($input['file_path'])) {
            $filePath = $input['file_path'];
        }

        if (empty($filePath)) {
            $this->jsonResponse(['status' => 'error', 'message' => 'يرجى إرفاق ملف للرفع'], 400);
        }

        $fileId = \App\Models\File::create([
            'course_id' => $courseId,
            'title' => $title,
            'description' => $description,
            'file_type' => $fileType,
            'file_path' => $filePath,
            'uploaded_by' => $userId ?: null,
            'is_approved' => 1 // Direct approval for app submissions or pending based on settings
        ]);

        $this->jsonResponse([
            'status' => 'success',
            'message' => 'تم رفع الملف بنجاح',
            'data' => ['id' => $fileId]
        ]);
    }

    public function bookmarks(): void
    {
        $userId = (int)($this->getParam('user_id', $_GET['user_id'] ?? 0));
        if (!$userId) {
            $this->jsonResponse(['status' => 'error', 'message' => 'معرف المستخدم غير صحيح'], 400);
        }

        $bookmarks = \App\Models\Bookmark::getByUser($userId);
        $this->jsonResponse([
            'status' => 'success',
            'data' => $bookmarks
        ]);
    }

    public function toggleBookmark(): void
    {
        $input = $this->getJsonInput();
        $userId = (int)($input['user_id'] ?? 0);
        $fileId = (int)($input['file_id'] ?? 0);

        if (!$userId || !$fileId) {
            $this->jsonResponse(['status' => 'error', 'message' => 'بيانات المفضلة غير مكتملة'], 400);
        }

        $result = \App\Models\Bookmark::toggle($userId, $fileId);
        $this->jsonResponse([
            'status' => 'success',
            'message' => $result['message'],
            'data' => ['bookmarked' => $result['bookmarked']]
        ]);
    }

    public function requestRole(): void
    {
        $input = $this->getJsonInput();
        $userId = (int)($input['user_id'] ?? $_POST['user_id'] ?? 0);
        $majorId = (int)($input['major_id'] ?? $_POST['major_id'] ?? 0);
        $levelId = !empty($input['level_id']) ? (int)$input['level_id'] : (!empty($_POST['level_id']) ? (int)$_POST['level_id'] : null);
        $accountType = $input['account_type'] ?? $_POST['account_type'] ?? 'representative';
        $reason = trim($input['reason'] ?? $_POST['reason'] ?? '');

        if (!$userId || !$majorId) {
            $this->jsonResponse(['status' => 'error', 'message' => 'يرجى إكمال بيانات الطلب والتخصص'], 400);
            return;
        }

        try {
            $existing = \App\Models\JoinRequest::findPendingByUserId($userId);
            if ($existing) {
                $this->jsonResponse(['status' => 'error', 'message' => 'لديك طلب انضمام معلق بالفعل قيد المراجعة'], 409);
                return;
            }

            \App\Config\Database::insert('join_requests', [
                'user_id' => $userId,
                'major_id' => $majorId,
                'level_id' => $levelId,
                'account_type' => $accountType,
                'reason' => $reason,
                'status' => 'pending'
            ]);

            $user = \App\Models\User::findById($userId);
            $userName = $user ? $user->full_name : 'مستخدم جديد';
            try {
                \App\Models\Notification::send(null, 'طلب ترقية مندوب جديد 📩', 'قام ' . $userName . ' بتقديم طلب ترقية كمندوب دفعة قيد المراجعة', 'warning', '/admin/requests');
            } catch (\Throwable $t) {}

            $this->jsonResponse([
                'status' => 'success',
                'message' => 'تم إرسال طلب الترقية بنجاح وهو قيد المراجعة'
            ]);
        } catch (\Throwable $e) {
            $this->jsonResponse([
                'status' => 'error',
                'message' => 'حدث خطأ أثناء حفظ الطلب: ' . $e->getMessage()
            ], 500);
        }
    }


    public function profile(): void
    {
        $userId = (int)($this->getParam('id', $_GET['id'] ?? 0));
        if (!$userId) {
            $this->jsonResponse(['status' => 'error', 'message' => 'معرف المستخدم غير موجود'], 400);
        }

        $user = \App\Models\User::findById($userId);
        if (!$user) {
            $this->jsonResponse(['status' => 'error', 'message' => 'المستخدم غير موجود'], 404);
        }

        $latestRequest = \App\Models\JoinRequest::findLatestByUserId($userId);

        $this->jsonResponse([
            'status' => 'success',
            'data' => [
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
                'latest_join_request' => $latestRequest
            ]
        ]);
    }

    public function updateProfile(): void
    {
        $input = $this->getJsonInput();
        $userId = (int)($input['user_id'] ?? 0);
        $fullName = trim($input['full_name'] ?? '');
        $phone = trim($input['phone'] ?? '');
        $majorId = !empty($input['major_id']) ? (int)$input['major_id'] : null;

        if (!$userId || empty($fullName)) {
            $this->jsonResponse(['status' => 'error', 'message' => 'الاسم الكامل غير مدخل'], 400);
        }

        $updateData = [
            'full_name' => $fullName,
            'phone' => $phone,
            'major_id' => $majorId,
        ];

        if (!empty($input['password'])) {
            $updateData['password'] = password_hash($input['password'], PASSWORD_DEFAULT);
        }

        \App\Models\User::update('users', $updateData, 'id = :id', ['id' => $userId]);

        $updatedUser = \App\Models\User::findById($userId);

        $this->jsonResponse([
            'status' => 'success',
            'message' => 'تم تحديث الملف الشخصي بنجاح',
            'data' => [
                'user' => [
                    'id' => (int)$updatedUser->id,
                    'full_name' => $updatedUser->full_name,
                    'email' => $updatedUser->email,
                    'phone' => $updatedUser->phone ?? '',
                    'role' => $updatedUser->role,
                    'major_id' => $updatedUser->major_id ? (int)$updatedUser->major_id : null,
                ]
            ]
        ]);
    }

    public function about(): void
    {
        $this->jsonResponse([
            'status' => 'success',
            'data' => [
                'title' => 'من نحن',
                'description' => 'اللجنة العلمية هي منصة تعليمية تهدف إلى تسهيل الوصول إلى المواد الدراسية والملفات التعليمية للطلاب وأعضاء هيئة التدريس.',
                'mission' => 'توفير منصة علمية متكاملة تسهل على الطالب الوصول للمحتوى الدراسي بكل سهولة ويسر.',
                'vision' => 'أن نكون المنصة الرائدة في المشاركة العلمية على مستوى الجامعات.',
                'features' => [
                    'تصفح المواد حسب التخصص والمستوى',
                    'تحميل الملفات التعليمية',
                    'تصنيف الملفات (محاضرات، ملخصات، نماذج، مراجع)',
                    'بحث متقدم مع فلترة',
                ],
            ]
        ]);
    }

    public function contact(): void
    {
        $this->jsonResponse([
            'status' => 'success',
            'data' => [
                'title' => 'اتصل بنا',
                'description' => 'نحن هنا لمساعدتك! يمكنك التواصل المباشر مع مطوري المنصة لأي استفسار أو اقتراح.',
                'developers' => [
                    [
                        'name' => 'محمد محمود الأهنومي',
                        'role' => 'المطور الأول للمنصة',
                        'phone' => '771135357',
                        'whatsapp' => '967771135357',
                        'email' => 'mohammedalahnomi04@gmail.com',
                    ],
                    [
                        'name' => 'سيد احمد حسين الغيلي',
                        'role' => 'المطور الثاني للمنصة',
                        'phone' => '772348925',
                        'whatsapp' => '967772348925',
                        'email' => 'sayedahmed77169@gmail.com',
                    ]
                ],
                'developer' => [
                    'name' => 'محمد محمود الأهنومي',
                    'role' => 'المطور الأول للمنصة',
                    'phone' => '771135357',
                    'whatsapp' => '967771135357',
                    'email' => 'mohammedalahnomi04@gmail.com',
                ]
            ]
        ]);
    }


    public function adminRequests(): void
    {
        $userId = (int)($this->getParam('user_id', $_GET['user_id'] ?? 0));
        $user = $userId ? \App\Models\User::findById($userId) : null;

        $requests = [];
        if ($user && $user->role === 'major_admin') {
            $majorId = (int)($user->managed_major_id ?: $user->major_id);
            $requests = \App\Models\JoinRequest::getAllWithDetails($majorId);
        } else {
            $requests = \App\Models\JoinRequest::getAllWithDetails();
        }

        $this->jsonResponse([
            'status' => 'success',
            'data' => array_map(function($r) {
                return [
                    'id' => (int)$r->id,
                    'user_id' => (int)$r->user_id,
                    'user_name' => $r->user_name,
                    'user_email' => $r->user_email,
                    'account_type' => $r->account_type,
                    'account_type_arabic' => $r->account_type === 'major_admin' ? 'مشرف تخصص' : 'مندوب دفعة',
                    'major_name' => $r->major_name,
                    'level_name' => $r->level_name ?? 'جميع المستويات',
                    'status' => $r->status,
                    'notes' => $r->notes ?? ($r->reason ?? ''),
                    'created_at' => $r->created_at,
                ];
            }, $requests)
        ]);
    }

    public function approveRequest(int $id = 0): void
    {
        $id = (int)($id ?: ($this->getParam('id') ?? $_GET['id'] ?? $_REQUEST['id'] ?? 0));
        if (!$id) {
            $input = $this->getJsonInput();
            $id = (int)($input['id'] ?? 0);
        }

        if (!$id) {
            $this->jsonResponse(['status' => 'error', 'message' => 'معرف الطلب غير موجود'], 400);
            return;
        }

        try {
            $req = \App\Config\Database::fetch("SELECT user_id FROM join_requests WHERE id = ?", [$id]);
            $success = \App\Models\JoinRequest::approve($id);
            if ($success) {
                if ($req && !empty($req->user_id)) {
                    try {
                        \App\Models\Notification::send((int)$req->user_id, 'تمت الموافقة على طلبك! 🎉', 'مبروك! تم قبول طلب ترقيتك وتفعيل صلاحيات المندوب لحسابك بنجاح.', 'success', '/profile');
                    } catch (\Throwable $t) {}
                }
                $this->jsonResponse(['status' => 'success', 'message' => 'تم قبول الطلب وترقية المستخدم بنجاح']);
            } else {
                $this->jsonResponse(['status' => 'error', 'message' => 'تعذر قبول الطلب'], 400);
            }
        } catch (\Throwable $e) {
            $this->jsonResponse(['status' => 'error', 'message' => 'خطأ في معالجة القبول: ' . $e->getMessage()], 500);
        }
    }

    public function rejectRequest(int $id = 0): void
    {
        $id = (int)($id ?: ($this->getParam('id') ?? $_GET['id'] ?? $_REQUEST['id'] ?? 0));
        if (!$id) {
            $input = $this->getJsonInput();
            $id = (int)($input['id'] ?? 0);
        }

        if (!$id) {
            $this->jsonResponse(['status' => 'error', 'message' => 'معرف الطلب غير موجود'], 400);
            return;
        }

        try {
            $req = \App\Config\Database::fetch("SELECT user_id FROM join_requests WHERE id = ?", [$id]);
            $success = \App\Models\JoinRequest::reject($id);
            if ($success) {
                if ($req && !empty($req->user_id)) {
                    try {
                        \App\Models\Notification::send((int)$req->user_id, 'نتيجة طلب الترقية ❌', 'نأسف، تم رفض طلب الترقية كمندوب حالياً.', 'error', '/profile');
                    } catch (\Throwable $t) {}
                }
                $this->jsonResponse(['status' => 'success', 'message' => 'تم رفض الطلب بنجاح']);
            } else {
                $this->jsonResponse(['status' => 'error', 'message' => 'تعذر رفض الطلب'], 400);
            }
        } catch (\Throwable $e) {
            $this->jsonResponse(['status' => 'error', 'message' => 'خطأ في معالجة الرفض: ' . $e->getMessage()], 500);
        }
    }

    public function adminStats(): void
    {
        try {
            $usersRow = \App\Config\Database::fetch("SELECT COUNT(*) as c FROM users");
            $majorsRow = \App\Config\Database::fetch("SELECT COUNT(*) as c FROM majors");
            $coursesRow = \App\Config\Database::fetch("SELECT COUNT(*) as c FROM courses");
            $filesRow = \App\Config\Database::fetch("SELECT COUNT(*) as c FROM files");
            $pendingRow = \App\Config\Database::fetch("SELECT COUNT(*) as c FROM join_requests WHERE status = 'pending'");
            $announcementsRow = \App\Config\Database::fetch("SELECT COUNT(*) as c FROM announcements");

            $usersCount = (int)($usersRow->c ?? $usersRow['c'] ?? 0);
            $majorsCount = (int)($majorsRow->c ?? $majorsRow['c'] ?? 0);
            $coursesCount = (int)($coursesRow->c ?? $coursesRow['c'] ?? 0);
            $filesCount = (int)($filesRow->c ?? $filesRow['c'] ?? 0);
            $pendingRequests = (int)($pendingRow->c ?? $pendingRow['c'] ?? 0);
            $announcementsCount = (int)($announcementsRow->c ?? $announcementsRow['c'] ?? 0);

            $this->jsonResponse([
                'status' => 'success',
                'data' => [
                    'users' => $usersCount,
                    'majors' => $majorsCount,
                    'courses' => $coursesCount,
                    'files' => $filesCount,
                    'pending_requests' => $pendingRequests,
                    'announcements' => $announcementsCount,
                ]
            ]);
        } catch (\Throwable $e) {
            $this->jsonResponse([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function createAnnouncement(): void
    {
        $input = $this->getJsonInput();
        $title = trim($input['title'] ?? $_POST['title'] ?? '');
        $content = trim($input['content'] ?? $_POST['content'] ?? '');
        $type = trim($input['type'] ?? $_POST['type'] ?? 'info');

        if (empty($title) || empty($content)) {
            $this->jsonResponse(['status' => 'error', 'message' => 'يرجى إدخال العنوان والمحتوى للإعلان'], 400);
            return;
        }

        $id = \App\Config\Database::insert('announcements', [
            'title' => $title,
            'content' => $content,
            'type' => $type,
            'is_active' => 1,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        if ($id) {
            \App\Models\Notification::send(null, 'إعلان جديد: ' . $title, $content, 'info', '/announcements');
            $this->jsonResponse(['status' => 'success', 'message' => 'تم نشر الإعلان ونشر تنبيه للمستخدمين بنجاح']);
        } else {
            $this->jsonResponse(['status' => 'error', 'message' => 'فشل نشر الإعلان'], 500);
        }
    }



    public function notifications(): void
    {
        $userId = (int)($_GET['user_id'] ?? 0);
        $userId = $userId > 0 ? $userId : null;

        $notifications = \App\Models\Notification::getForUser($userId, 20);
        $unreadCount = \App\Models\Notification::getUnreadCount($userId);

        $this->jsonResponse([
            'status' => 'success',
            'data' => [
                'unread_count' => $unreadCount,
                'notifications' => array_map(function($n) {
                    return [
                        'id' => (int)$n->id,
                        'user_id' => $n->user_id ? (int)$n->user_id : null,
                        'title' => $n->title,
                        'message' => $n->message,
                        'type' => $n->type ?? 'info',
                        'link' => $n->link ?? '',
                        'is_read' => (int)$n->is_read === 1,
                        'created_at' => $n->created_at,
                    ];
                }, $notifications),
            ]
        ]);
    }

    public function markNotificationRead(int $id): void
    {
        $userId = (int)($_GET['user_id'] ?? 0);
        $userId = $userId > 0 ? $userId : null;
        $success = \App\Models\Notification::markAsRead($id, $userId);
        $this->jsonResponse(['status' => 'success', 'message' => 'تم تعيين الإشعار كـ مقروء']);
    }

    public function markAllNotificationsRead(): void
    {
        $userId = (int)($_POST['user_id'] ?? ($_GET['user_id'] ?? 0));
        $userId = $userId > 0 ? $userId : null;
        $success = \App\Models\Notification::markAllAsRead($userId);
        $this->jsonResponse(['status' => 'success', 'message' => 'تم تعيين جميع الإشعارات كـ مقروءة']);
    }

    public function adminFiles(): void
    {
        $userId = (int)($this->getParam('user_id', $_GET['user_id'] ?? 0));
        $user = $userId ? \App\Models\User::findById($userId) : null;

        $where = "";
        $params = [];

        if ($user) {
            if ($user->role === 'manager' && $user->managed_major_id && $user->managed_level_id) {
                $where = "WHERE c.major_id = ? AND c.level_id = ?";
                $params = [(int)$user->managed_major_id, (int)$user->managed_level_id];
            } else if ($user->role === 'major_admin' && $user->managed_major_id) {
                $where = "WHERE c.major_id = ?";
                $params = [(int)$user->managed_major_id];
            }
        }

        $files = \App\Config\Database::fetchAll(
            "SELECT f.*, c.name as course_name, m.name as major_name, u.full_name as uploader_name
             FROM files f
             LEFT JOIN courses c ON c.id = f.course_id
             LEFT JOIN majors m ON m.id = c.major_id
             LEFT JOIN users u ON u.id = f.uploaded_by
             {$where}
             ORDER BY f.id DESC",
            $params
        );
        $this->jsonResponse(['status' => 'success', 'data' => $files]);
    }

    public function adminCourses(): void
    {
        $userId = (int)($this->getParam('user_id', $_GET['user_id'] ?? 0));
        $user = $userId ? \App\Models\User::findById($userId) : null;

        $where = "";
        $params = [];

        if ($user) {
            if ($user->role === 'manager' && $user->managed_major_id && $user->managed_level_id) {
                $where = "WHERE c.major_id = ? AND c.level_id = ?";
                $params = [(int)$user->managed_major_id, (int)$user->managed_level_id];
            } else if ($user->role === 'major_admin' && $user->managed_major_id) {
                $where = "WHERE c.major_id = ?";
                $params = [(int)$user->managed_major_id];
            }
        }

        $courses = \App\Config\Database::fetchAll(
            "SELECT c.*, m.name as major_name, l.name as level_name, s.name as semester_name,
                    (SELECT COUNT(*) FROM files f WHERE f.course_id = c.id) as files_count
             FROM courses c
             LEFT JOIN majors m ON m.id = c.major_id
             LEFT JOIN levels l ON l.id = c.level_id
             LEFT JOIN semesters s ON s.id = c.semester_id
             {$where}
             ORDER BY c.id DESC",
            $params
        );
        $this->jsonResponse(['status' => 'success', 'data' => $courses]);
    }

    public function approveFile(): void
    {
        $id = (int)($this->getParam('id', $_GET['id'] ?? 0));
        \App\Config\Database::raw("UPDATE files SET is_approved = 1 WHERE id = ?", [$id]);
        $this->jsonResponse(['status' => 'success', 'message' => 'تم القبول بنجاح']);
    }

    public function deleteFile(): void
    {
        $id = (int)($this->getParam('id', $_GET['id'] ?? 0));
        \App\Config\Database::raw("DELETE FROM files WHERE id = ?", [$id]);
        $this->jsonResponse(['status' => 'success', 'message' => 'تم حذف الملف بنجاح']);
    }

    public function createMajor(): void
    {
        $input = $this->getJsonInput();
        $name = trim($input['name'] ?? '');
        $code = trim($input['code'] ?? '');
        $description = trim($input['description'] ?? '');

        if (empty($name)) {
            $this->jsonResponse(['status' => 'error', 'message' => 'اسم التخصص مطلوب'], 400);
        }

        $id = \App\Models\Major::create(['name' => $name, 'code' => $code, 'description' => $description]);
        $this->jsonResponse(['status' => 'success', 'message' => 'تم إضافة التخصص بنجاح', 'data' => ['id' => $id]]);
    }

    public function deleteMajor(): void
    {
        $id = (int)($this->getParam('id', $_GET['id'] ?? 0));
        \App\Config\Database::raw("DELETE FROM majors WHERE id = ?", [$id]);
        $this->jsonResponse(['status' => 'success', 'message' => 'تم حذف التخصص بنجاح']);
    }

    public function createCourse(): void
    {
        try {
            $input = $this->getJsonInput();
            $name = trim($input['name'] ?? '');
            $code = trim($input['code'] ?? '');
            $majorId = (int)($input['major_id'] ?? 0);
            $userId = (int)($input['user_id'] ?? 0);
            $levelId = !empty($input['level_id']) ? (int)$input['level_id'] : 0;
            $semesterId = !empty($input['semester_id']) ? (int)$input['semester_id'] : 1;
            $description = trim($input['description'] ?? '');

            if ($userId && !$levelId) {
                $user = \App\Models\User::findById($userId);
                if ($user && $user->role === 'manager' && $user->managed_level_id) {
                    $levelId = (int)$user->managed_level_id;
                }
            }

            if (!$levelId) {
                $levelId = 1;
            }

            if (empty($name) || !$majorId) {
                $this->jsonResponse(['status' => 'error', 'message' => 'اسم المادة والتخصص مطلوبان'], 400);
            }

            $id = \App\Models\Course::create([
                'name' => $name,
                'code' => $code,
                'major_id' => $majorId,
                'level_id' => $levelId,
                'semester_id' => $semesterId,
                'description' => $description,
                'is_active' => 1
            ]);
            $this->jsonResponse(['status' => 'success', 'message' => 'تم إضافة المادة الدراسية بنجاح', 'data' => ['id' => $id]]);
        } catch (\Throwable $e) {
            $this->jsonResponse(['status' => 'error', 'message' => 'حدث خطأ أثناء إضافة المادة: ' . $e->getMessage()], 500);
        }
    }

    public function deleteCourse(): void
    {
        $id = (int)($this->getParam('id', $_GET['id'] ?? 0));
        \App\Config\Database::raw("DELETE FROM courses WHERE id = ?", [$id]);
        $this->jsonResponse(['status' => 'success', 'message' => 'تم حذف المادة الدراسية بنجاح']);
    }

    public function deleteAnnouncement(): void
    {
        $id = (int)($this->getParam('id', $_GET['id'] ?? 0));
        \App\Config\Database::raw("DELETE FROM announcements WHERE id = ?", [$id]);
        $this->jsonResponse(['status' => 'success', 'message' => 'تم حذف الإعلان بنجاح']);
    }

    public function adminUsers(): void
    {
        $users = \App\Config\Database::fetchAll(
            "SELECT u.*, m.name as major_name
             FROM users u
             LEFT JOIN majors m ON m.id = u.major_id
             WHERE u.id != 1
             ORDER BY u.id DESC"
        );
        $data = array_map(function($u) {
            return [
                'id' => (int)$u->id,
                'full_name' => $u->full_name,
                'email' => $u->email,
                'phone' => $u->phone ?? '',
                'role' => $u->role,
                'major_name' => $u->major_name ?? 'غير محدد',
                'is_active' => (int)$u->is_active === 1,
                'created_at' => $u->created_at,
            ];
        }, $users);
        $this->jsonResponse(['status' => 'success', 'data' => $data]);
    }

    public function updateUserRole(): void
    {
        $id = (int)($this->getParam('id', $_GET['id'] ?? 0));
        $input = $this->getJsonInput();
        $role = trim($input['role'] ?? '');

        if (!$id || empty($role)) {
            $this->jsonResponse(['status' => 'error', 'message' => 'بيانات التحديث غير مكتملة'], 400);
            return;
        }

        if ($id === 1) {
            $this->jsonResponse(['status' => 'error', 'message' => 'حساب مدير النظام الرئيسي محمي ولا يمكن تعديله'], 403);
            return;
        }

        \App\Config\Database::raw("UPDATE users SET role = ? WHERE id = ?", [$role, $id]);
        $this->jsonResponse(['status' => 'success', 'message' => 'تم تحديث دور المستخدم بنجاح']);
    }
}



