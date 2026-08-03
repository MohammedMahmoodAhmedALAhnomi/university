<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Semester;

class SemesterController extends Controller
{
    public function index(): void
    {
        $semesters = Semester::all();
        $this->view('admin/semesters/index', ['semesters' => $semesters]);
    }

    public function create(): void
    {
        $this->view('admin/semesters/create');
    }

    public function store(): void
    {
        if (!$this->isPost() || !verify_csrf()) {
            redirect(url('/admin/semesters'));
        }

        $data = [
            'name' => trim($this->postParam('name', '')),
        ];

        $errors = $this->validate($data, ['name' => 'required']);

        if (!empty($errors)) {
            flash('error', 'يرجى التحقق من الحقول المطلوبة');
            $this->view('admin/semesters/create', ['errors' => $errors, 'data' => $data]);
            return;
        }

        $semesterId = Semester::create($data);
        log_activity('create', 'semesters', $semesterId, 'إضافة فصل دراسي جديد: ' . $data['name']);
        flash('success', 'تم إضافة الفصل الدراسي بنجاح');
        redirect(url('/admin/semesters'));
    }

    public function edit(): void
    {
        $id = $this->getParam('id');
        $semester = Semester::find($id);

        if (!$semester) {
            flash('error', 'الفصل الدراسي غير موجود');
            redirect(url('/admin/semesters'));
        }

        $this->view('admin/semesters/edit', ['semester' => $semester]);
    }

    public function update(): void
    {
        if (!$this->isPost() || !verify_csrf()) {
            redirect(url('/admin/semesters'));
        }

        $id = $this->postParam('id');

        $data = [
            'name' => trim($this->postParam('name', '')),
        ];

        $errors = $this->validate($data, ['name' => 'required']);

        if (!empty($errors)) {
            flash('error', 'يرجى التحقق من الحقول المطلوبة');
            $semester = Semester::find($id);
            $this->view('admin/semesters/edit', ['semester' => $semester, 'errors' => $errors]);
            return;
        }

        Semester::updateRecord($id, $data);
        log_activity('update', 'semesters', $id, 'تحديث الفصل الدراسي: ' . $data['name']);
        flash('success', 'تم تحديث الفصل الدراسي بنجاح');
        redirect(url('/admin/semesters'));
    }

    public function delete(): void
    {
        $id = $this->getParam('id');
        $semester = Semester::find($id);

        if (!$semester) {
            flash('error', 'الفصل الدراسي غير موجود');
            redirect(url('/admin/semesters'));
        }

        Semester::deleteRecord($id);
        log_activity('delete', 'semesters', $id, 'حذف الفصل الدراسي: ' . $semester->name);
        flash('success', 'تم حذف الفصل الدراسي بنجاح');
        redirect(url('/admin/semesters'));
    }
}
