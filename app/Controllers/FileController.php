<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\File;
use App\Models\Course;
use App\Models\Level;
use App\Config\Database;

class FileController extends Controller
{
    public function index(): void
    {
        $managedLevelId = get_managed_level_id();
        $managedMajorId = get_managed_major_id();
        $courseId = $_GET['course_id'] ?? null;

        if ($managedLevelId && $managedMajorId) {
            $courses = Database::fetchAll(
                "SELECT id, name FROM courses WHERE level_id = ? AND major_id = ? ORDER BY name",
                [$managedLevelId, $managedMajorId]
            );
            if ($courseId) {
                $files = File::getAllWithDetails("WHERE f.course_id = ? AND c.level_id = ? AND c.major_id = ?", [$courseId, $managedLevelId, $managedMajorId]);
            } else {
                $files = File::getAllWithDetails("WHERE c.level_id = ? AND c.major_id = ?", [$managedLevelId, $managedMajorId]);
            }
        } elseif ($managedLevelId) {
            $courses = Database::fetchAll(
                "SELECT id, name FROM courses WHERE level_id = ? ORDER BY name",
                [$managedLevelId]
            );
            if ($courseId) {
                $files = File::getAllWithDetails("WHERE f.course_id = ? AND c.level_id = ?", [$courseId, $managedLevelId]);
            } else {
                $files = File::getAllWithDetails("WHERE c.level_id = ?", [$managedLevelId]);
            }
        } elseif ($managedMajorId) {
            $courses = Database::fetchAll(
                "SELECT id, name FROM courses WHERE major_id = ? ORDER BY name",
                [$managedMajorId]
            );
            if ($courseId) {
                $files = File::getAllWithDetails("WHERE f.course_id = ? AND c.major_id = ?", [$courseId, $managedMajorId]);
            } else {
                $files = File::getAllWithDetails("WHERE c.major_id = ?", [$managedMajorId]);
            }
        } else {
            $courses = \App\Models\Course::all();
            if ($courseId) {
                $files = File::getAllWithDetails("WHERE f.course_id = ?", [$courseId]);
            } else {
                $files = File::getAllWithDetails();
            }
        }
        $this->view('admin/files/index', ['files' => $files, 'courses' => $courses, 'managedLevelId' => $managedLevelId]);
    }

    public function create(): void
    {
        $managedLevelId = get_managed_level_id();
        $managedMajorId = get_managed_major_id();
        if ($managedLevelId && $managedMajorId) {
            $courses = Database::fetchAll(
                "SELECT id, name FROM courses WHERE level_id = ? AND major_id = ? ORDER BY name",
                [$managedLevelId, $managedMajorId]
            );
        } elseif ($managedLevelId) {
            $courses = Database::fetchAll(
                "SELECT id, name FROM courses WHERE level_id = ? ORDER BY name",
                [$managedLevelId]
            );
        } elseif ($managedMajorId) {
            $courses = Database::fetchAll(
                "SELECT id, name FROM courses WHERE major_id = ? ORDER BY name",
                [$managedMajorId]
            );
        } else {
            $courses = Course::all();
        }
        $this->view('admin/files/create', ['courses' => $courses, 'managedLevelId' => $managedLevelId]);
    }

    public function store(): void
    {
        if (!$this->isPost() || !verify_csrf()) {
            redirect(url('/admin/files'));
        }

        $managedLevelId = get_managed_level_id();
        $managedMajorId = get_managed_major_id();

        $data = [
            'title' => trim($this->postParam('title', '')),
            'course_id' => $this->postParam('course_id', ''),
            'description' => trim($this->postParam('description', '')),
            'doctor_name' => trim($this->postParam('doctor_name', '')),
            'uploaded_by' => $_SESSION['user_id'] ?? 0,
        ];

        $errors = $this->validate($data, [
            'title' => 'required',
            'course_id' => 'required|numeric',
        ]);

        if ($managedLevelId) {
            $course = Course::find((int)$data['course_id']);
            if (!$course || $course->level_id != $managedLevelId) {
                $errors['course_id'][] = 'لا يمكنك رفع ملف لهذه المادة';
            } elseif ($managedMajorId && $course->major_id != $managedMajorId) {
                $errors['course_id'][] = 'لا يمكنك رفع ملف لهذه المادة';
            }
        }

        $file = $this->fileParam('file');

        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            $errors['file'][] = 'يرجى رفع ملف صالح';
        } else {
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $forbiddenExts = ['php', 'phtml', 'php3', 'php4', 'php5', 'phar', 'exe', 'sh', 'bat', 'cmd', 'pl', 'py', 'cgi', 'js', 'html', 'htm'];
            if (in_array($ext, $forbiddenExts, true)) {
                $errors['file'][] = 'امتداد الملف المرفوع غير مسموح به لأسباب أمنية';
            }
        }

        if (!empty($errors)) {
            flash('error', 'يرجى التحقق من الحقول المطلوبة');
            if ($managedLevelId && $managedMajorId) {
                $courses = Database::fetchAll("SELECT id, name FROM courses WHERE level_id = ? AND major_id = ? ORDER BY name", [$managedLevelId, $managedMajorId]);
            } elseif ($managedLevelId) {
                $courses = Database::fetchAll("SELECT id, name FROM courses WHERE level_id = ? ORDER BY name", [$managedLevelId]);
            } else {
                $courses = Course::all();
            }
            $this->view('admin/files/create', [
                'errors' => $errors,
                'data' => $data,
                'courses' => $courses,
                'managedLevelId' => $managedLevelId,
            ]);
            return;
        }

        $uploadDir = __DIR__ . '/../../public/uploads/files/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $fileName = uniqid('file_') . '.' . $extension;
        $filePath = $uploadDir . $fileName;

        if (!move_uploaded_file($file['tmp_name'], $filePath)) {
            flash('error', 'فشل رفع الملف');
            redirect(url('/admin/files/create'));
        }

        $data['file_name'] = $fileName;
        $data['file_path'] = 'uploads/files/' . $fileName;
        $data['file_extension'] = $extension;
        $data['file_type'] = $this->postParam('file_type', 'lecture');
        $data['file_size'] = $file['size'];
        $data['is_approved'] = $this->postParam('is_approved', 0) ? 1 : 0;

        $fileId = File::create($data);
        log_activity('create', 'files', $fileId, 'رفع ملف جديد: ' . $data['title']);
        flash('success', 'تم رفع الملف بنجاح');
        redirect(url('/admin/files'));
    }

    public function edit(): void
    {
        $id = $this->getParam('id');
        $file = File::findWithDetails($id);

        if (!$file) {
            flash('error', 'الملف غير موجود');
            redirect(url('/admin/files'));
        }

        $managedLevelId = get_managed_level_id();
        $managedMajorId = get_managed_major_id();
        if ($managedLevelId && $managedMajorId) {
            $course = Course::find($file->course_id);
            if ($file->level_id != $managedLevelId || ($course && $course->major_id != $managedMajorId)) {
                flash('error', 'لا يمكنك تعديل هذا الملف');
                redirect(url('/admin/files'));
            }
        } elseif ($managedLevelId && $file->level_id != $managedLevelId) {
            flash('error', 'لا يمكنك تعديل هذا الملف');
            redirect(url('/admin/files'));
        }

        if ($managedLevelId && $managedMajorId) {
            $courses = Database::fetchAll("SELECT id, name FROM courses WHERE level_id = ? AND major_id = ? ORDER BY name", [$managedLevelId, $managedMajorId]);
        } elseif ($managedLevelId) {
            $courses = Database::fetchAll("SELECT id, name FROM courses WHERE level_id = ? ORDER BY name", [$managedLevelId]);
        } else {
            $courses = Course::all();
        }
        $this->view('admin/files/edit', ['file' => $file, 'courses' => $courses, 'managedLevelId' => $managedLevelId]);
    }

    public function update(): void
    {
        if (!$this->isPost() || !verify_csrf()) {
            redirect(url('/admin/files'));
        }

        $id = $this->postParam('id');
        $file = File::find($id);

        if (!$file) {
            flash('error', 'الملف غير موجود');
            redirect(url('/admin/files'));
        }

        $managedLevelId = get_managed_level_id();
        $managedMajorId = get_managed_major_id();
        if ($managedLevelId) {
            $course = Course::find($file->course_id);
            if (!$course || $course->level_id != $managedLevelId) {
                flash('error', 'لا يمكنك تعديل هذا الملف');
                redirect(url('/admin/files'));
            } elseif ($managedMajorId && $course->major_id != $managedMajorId) {
                flash('error', 'لا يمكنك تعديل هذا الملف');
                redirect(url('/admin/files'));
            }
        }

        $data = [
            'title' => trim($this->postParam('title', '')),
            'course_id' => $this->postParam('course_id', ''),
            'description' => trim($this->postParam('description', '')),
            'doctor_name' => trim($this->postParam('doctor_name', '')),
            'file_type' => $this->postParam('file_type', 'lecture'),
            'is_approved' => $this->postParam('is_approved', 0) ? 1 : 0,
        ];

        $errors = $this->validate($data, [
            'title' => 'required',
            'course_id' => 'required|numeric',
        ]);

        $uploadedFile = $this->fileParam('file');

        if ($uploadedFile && $uploadedFile['error'] === UPLOAD_ERR_OK) {
            $extension = strtolower(pathinfo($uploadedFile['name'], PATHINFO_EXTENSION));
            $forbiddenExts = ['php', 'phtml', 'php3', 'php4', 'php5', 'phar', 'exe', 'sh', 'bat', 'cmd', 'pl', 'py', 'cgi', 'js', 'html', 'htm'];
            if (in_array($extension, $forbiddenExts, true)) {
                $errors['file'][] = 'امتداد الملف المرفوع غير مسموح به لأسباب أمنية';
            } else {
                $uploadDir = __DIR__ . '/../../public/uploads/files/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $fileName = uniqid('file_') . '.' . $extension;
                $filePath = $uploadDir . $fileName;

                if (!move_uploaded_file($uploadedFile['tmp_name'], $filePath)) {
                    $errors['file'][] = 'فشل رفع الملف';
                } else {
                    $oldPath = __DIR__ . '/../../public/' . $file->file_path;
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }

                    $data['file_name'] = $fileName;
                    $data['file_path'] = 'uploads/files/' . $fileName;
                    $data['file_extension'] = $extension;
                    $data['file_size'] = $uploadedFile['size'];
                }
            }
        }

        if (!empty($errors)) {
            flash('error', 'يرجى التحقق من الحقول المطلوبة');
            if ($managedLevelId && $managedMajorId) {
                $courses = Database::fetchAll("SELECT id, name FROM courses WHERE level_id = ? AND major_id = ? ORDER BY name", [$managedLevelId, $managedMajorId]);
            } elseif ($managedLevelId) {
                $courses = Database::fetchAll("SELECT id, name FROM courses WHERE level_id = ? ORDER BY name", [$managedLevelId]);
            } else {
                $courses = Course::all();
            }
            $fileData = File::findWithDetails($id);
            $this->view('admin/files/edit', [
                'file' => $fileData,
                'errors' => $errors,
                'courses' => $courses,
                'managedLevelId' => $managedLevelId,
            ]);
            return;
        }

        File::updateRecord($id, $data);
        log_activity('update', 'files', $id, 'تحديث الملف: ' . $data['title']);
        flash('success', 'تم تحديث الملف بنجاح');
        redirect(url('/admin/files'));
    }

    public function delete(): void
    {
        if (!verify_csrf()) {
            flash('error', 'طلب غير صالح أو انتهت مهلة الجلسة');
            redirect(url('/admin/files'));
        }

        $id = $this->getParam('id');
        $file = File::find($id);

        if (!$file) {
            flash('error', 'الملف غير موجود');
            redirect(url('/admin/files'));
        }

        $managedLevelId = get_managed_level_id();
        $managedMajorId = get_managed_major_id();
        if ($managedLevelId) {
            $course = Course::find($file->course_id);
            if (!$course || $course->level_id != $managedLevelId) {
                flash('error', 'لا يمكنك حذف هذا الملف');
                redirect(url('/admin/files'));
            } elseif ($managedMajorId && $course->major_id != $managedMajorId) {
                flash('error', 'لا يمكنك حذف هذا الملف');
                redirect(url('/admin/files'));
            }
        }

        $filePath = __DIR__ . '/../../public/' . $file->file_path;
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        File::deleteRecord($id);
        log_activity('delete', 'files', $id, 'حذف الملف: ' . $file->title);
        flash('success', 'تم حذف الملف بنجاح');
        redirect(url('/admin/files'));
    }

    public function toggleApproval(): void
    {
        if (!verify_csrf()) {
            flash('error', 'طلب غير صالح أو انتهت مهلة الجلسة');
            redirect(url('/admin/files'));
        }

        $id = $this->getParam('id');
        $file = File::find($id);

        if (!$file) {
            flash('error', 'الملف غير موجود');
            redirect(url('/admin/files'));
        }

        $managedLevelId = get_managed_level_id();
        $managedMajorId = get_managed_major_id();
        if ($managedLevelId) {
            $course = Course::find($file->course_id);
            if (!$course || $course->level_id != $managedLevelId) {
                flash('error', 'لا يمكنك تعديل هذا الملف');
                redirect(url('/admin/files'));
            } elseif ($managedMajorId && $course->major_id != $managedMajorId) {
                flash('error', 'لا يمكنك تعديل هذا الملف');
                redirect(url('/admin/files'));
            }
        }

        $isApproved = $file->is_approved ? 0 : 1;
        File::updateRecord($id, ['is_approved' => $isApproved]);

        $status = $isApproved ? 'تم اعتماد الملف' : 'تم إلغاء اعتماد الملف';
        log_activity('update', 'files', $id, $status . ': ' . $file->title);
        flash('success', $status);
        redirect(url('/admin/files'));
    }

    public function download(): void
    {
        $id = $this->getParam('id');
        $file = File::find($id);

        if (!$file || !$file->is_approved) {
            http_response_code(404);
            require base_path('Views' . DIRECTORY_SEPARATOR . 'errors' . DIRECTORY_SEPARATOR . '404.php');
            return;
        }

        $path = __DIR__ . '/../../public/' . $file->file_path;
        if (!file_exists($path)) {
            http_response_code(404);
            echo 'الملف غير موجود على الخادم';
            return;
        }

        File::incrementDownload($id);

        $ext = strtolower(pathinfo($file->file_name ?? $path, PATHINFO_EXTENSION));
        $mimeTypes = [
            'pdf' => 'application/pdf', 'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'ppt' => 'application/vnd.ms-powerpoint',
            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'zip' => 'application/zip', 'rar' => 'application/x-rar-compressed',
            'png' => 'image/png', 'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg',
            'mp4' => 'video/mp4', 'mp3' => 'audio/mpeg',
        ];

        header('Content-Type: ' . ($mimeTypes[$ext] ?? 'application/octet-stream'));
        header('Content-Disposition: attachment; filename="' . basename($file->file_name ?? 'file.' . $ext) . '"');
        header('Content-Length: ' . filesize($path));
        header('X-File-Name: ' . $file->title);
        readfile($path);
        exit;
    }

    public function preview(): void
    {
        $id = $this->getParam('id');
        $file = File::find($id);

        if (!$file || !$file->is_approved) {
            http_response_code(404);
            require base_path('Views' . DIRECTORY_SEPARATOR . 'errors' . DIRECTORY_SEPARATOR . '404.php');
            return;
        }

        $path = __DIR__ . '/../../public/' . $file->file_path;
        if (!file_exists($path)) {
            http_response_code(404);
            echo 'الملف غير موجود على الخادم';
            return;
        }

        $ext = strtolower(pathinfo($file->file_name ?? $path, PATHINFO_EXTENSION));

        // If raw parameter is set, stream raw file directly
        if (isset($_GET['raw']) && $_GET['raw'] == '1') {
            $mimeTypes = [
                'pdf' => 'application/pdf',
                'doc' => 'application/msword',
                'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'ppt' => 'application/vnd.ms-powerpoint',
                'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                'xls' => 'application/vnd.ms-excel',
                'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'png' => 'image/png', 'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg',
                'gif' => 'image/gif', 'webp' => 'image/webp',
                'mp4' => 'video/mp4', 'webm' => 'video/webm',
                'mp3' => 'audio/mpeg',
            ];

            header('Content-Type: ' . ($mimeTypes[$ext] ?? 'application/octet-stream'));
            header('Content-Disposition: inline; filename="' . basename($file->file_name ?? 'file.' . $ext) . '"');
            header('Content-Length: ' . filesize($path));
            readfile($path);
            exit;
        }

        // Render Dedicated Interactive File Preview Page
        $backUrl = isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])
            ? $_SERVER['HTTP_REFERER']
            : url('/courses/' . $file->course_id);

        $this->view('front/file_preview', [
            'file' => $file,
            'fileExtension' => $ext,
            'backUrl' => $backUrl,
        ]);
    }
}
