<?php

namespace App\Controllers;

use App\Core\View;
use App\Models\Bookmark;
use App\Models\File;

class StudentController
{
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
}
