<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Announcement;

class AnnouncementController extends Controller
{
    public function index(): void
    {
        $announcements = Announcement::getActive();
        $this->view('front/announcements', ['announcements' => $announcements]);
    }

    public function show(): void
    {
        $id = $this->getParam('id');
        $announcement = Announcement::findWithCreator($id);

        if (!$announcement) {
            flash('error', 'الإعلان غير موجود');
            redirect(url('/announcements'));
        }

        $this->view('front/announcement_details', ['announcement' => $announcement]);
    }

    public function adminIndex(): void
    {
        $announcements = Announcement::getAllWithCreator();
        $this->view('admin/announcements/index', ['announcements' => $announcements]);
    }

    public function create(): void
    {
        $this->view('admin/announcements/create');
    }

    public function store(): void
    {
        if (!$this->isPost() || !verify_csrf()) {
            redirect(url('/admin/announcements'));
        }

        $data = [
            'title' => trim($this->postParam('title', '')),
            'content' => trim($this->postParam('content', '')),
            'is_active' => $this->postParam('is_active') ? 1 : 0,
            'created_by' => $_SESSION['user_id'],
        ];

        $errors = $this->validate($data, [
            'title' => 'required',
            'content' => 'required',
        ]);

        if (!empty($errors)) {
            flash('error', 'يرجى التحقق من الحقول المطلوبة');
            $this->view('admin/announcements/create', ['errors' => $errors, 'data' => $data]);
            return;
        }

        $announcementId = Announcement::create($data);
        log_activity('create', 'announcements', $announcementId, 'إضافة إعلان جديد: ' . $data['title']);
        flash('success', 'تم إضافة الإعلان بنجاح');
        redirect(url('/admin/announcements'));
    }

    public function edit(): void
    {
        $id = $this->getParam('id');
        $announcement = Announcement::findWithCreator($id);

        if (!$announcement) {
            flash('error', 'الإعلان غير موجود');
            redirect(url('/admin/announcements'));
        }

        $this->view('admin/announcements/edit', ['announcement' => $announcement]);
    }

    public function update(): void
    {
        if (!$this->isPost() || !verify_csrf()) {
            redirect(url('/admin/announcements'));
        }

        $id = $this->postParam('id');

        $data = [
            'title' => trim($this->postParam('title', '')),
            'content' => trim($this->postParam('content', '')),
            'is_active' => $this->postParam('is_active') ? 1 : 0,
        ];

        $errors = $this->validate($data, [
            'title' => 'required',
            'content' => 'required',
        ]);

        if (!empty($errors)) {
            flash('error', 'يرجى التحقق من الحقول المطلوبة');
            $announcement = Announcement::findWithCreator($id);
            $this->view('admin/announcements/edit', [
                'announcement' => $announcement,
                'errors' => $errors,
            ]);
            return;
        }

        Announcement::updateRecord($id, $data);
        log_activity('update', 'announcements', $id, 'تحديث الإعلان: ' . $data['title']);
        flash('success', 'تم تحديث الإعلان بنجاح');
        redirect(url('/admin/announcements'));
    }

    public function delete(): void
    {
        $id = $this->getParam('id');
        $announcement = Announcement::find($id);

        if (!$announcement) {
            flash('error', 'الإعلان غير موجود');
            redirect(url('/admin/announcements'));
        }

        Announcement::deleteRecord($id);
        log_activity('delete', 'announcements', $id, 'حذف الإعلان: ' . $announcement->title);
        flash('success', 'تم حذف الإعلان بنجاح');
        redirect(url('/admin/announcements'));
    }
}
