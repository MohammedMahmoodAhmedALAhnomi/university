<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Level;

class LevelController extends Controller
{
    public function index(): void
    {
        $levels = Level::all();
        $this->view('admin/levels/index', ['levels' => $levels]);
    }

    public function create(): void
    {
        $this->view('admin/levels/create');
    }

    public function store(): void
    {
        if (!$this->isPost() || !verify_csrf()) {
            redirect(url('/admin/levels'));
        }

        $data = [
            'name' => trim($this->postParam('name', '')),
            'level_number' => $this->postParam('level_number', ''),
        ];

        $errors = $this->validate($data, [
            'name' => 'required',
            'level_number' => 'required|numeric',
        ]);

        if (!empty($errors)) {
            flash('error', 'يرجى التحقق من الحقول المطلوبة');
            $this->view('admin/levels/create', ['errors' => $errors, 'data' => $data]);
            return;
        }

        $levelId = Level::create($data);
        log_activity('create', 'levels', $levelId, 'إضافة مستوى جديد: ' . $data['name']);
        flash('success', 'تم إضافة المستوى بنجاح');
        redirect(url('/admin/levels'));
    }

    public function edit(): void
    {
        $id = $this->getParam('id');
        $level = Level::find($id);

        if (!$level) {
            flash('error', 'المستوى غير موجود');
            redirect(url('/admin/levels'));
        }

        $this->view('admin/levels/edit', ['level' => $level]);
    }

    public function update(): void
    {
        if (!$this->isPost() || !verify_csrf()) {
            redirect(url('/admin/levels'));
        }

        $id = $this->postParam('id');

        $data = [
            'name' => trim($this->postParam('name', '')),
            'level_number' => $this->postParam('level_number', ''),
        ];

        $errors = $this->validate($data, [
            'name' => 'required',
            'level_number' => 'required|numeric',
        ]);

        if (!empty($errors)) {
            flash('error', 'يرجى التحقق من الحقول المطلوبة');
            $level = Level::find($id);
            $this->view('admin/levels/edit', ['level' => $level, 'errors' => $errors]);
            return;
        }

        Level::updateRecord($id, $data);
        log_activity('update', 'levels', $id, 'تحديث المستوى: ' . $data['name']);
        flash('success', 'تم تحديث المستوى بنجاح');
        redirect(url('/admin/levels'));
    }

    public function delete(): void
    {
        $id = $this->getParam('id');
        $level = Level::find($id);

        if (!$level) {
            flash('error', 'المستوى غير موجود');
            redirect(url('/admin/levels'));
        }

        Level::deleteRecord($id);
        log_activity('delete', 'levels', $id, 'حذف المستوى: ' . $level->name);
        flash('success', 'تم حذف المستوى بنجاح');
        redirect(url('/admin/levels'));
    }
}
