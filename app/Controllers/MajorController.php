<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Major;

class MajorController extends Controller
{
    public function index(): void
    {
        $majors = Major::getWithCourseCount();
        $this->view('admin/majors/index', ['majors' => $majors]);
    }

    public function show(): void
    {
        $id = $this->getParam('id');
        $major = Major::find($id);

        if (!$major) {
            flash('error', 'التخصص غير موجود');
            redirect(url('/'));
        }

        $levelId = $this->getParam('level');
        $levels = \App\Models\Level::getActive();
        $grouped = \App\Models\Course::getByMajorGrouped($id, $levelId ? (int)$levelId : null);
        $selectedLevel = null;
        if ($levelId) {
            $selectedLevel = \App\Models\Level::find((int)$levelId);
        }
        $this->view('front/major_details', [
            'major' => $major,
            'grouped' => $grouped,
            'levels' => $levels,
            'selectedLevel' => $selectedLevel,
        ]);
    }

    public function create(): void
    {
        $levels = \App\Models\Level::all();
        $semesters = \App\Models\Semester::all();
        $this->view('admin/majors/create', ['levels' => $levels, 'semesters' => $semesters]);
    }

    public function store(): void
    {
        if (!$this->isPost() || !verify_csrf()) {
            redirect(url('/admin/majors'));
        }

        $data = [
            'name' => trim($this->postParam('name', '')),
        ];

        $errors = $this->validate($data, ['name' => 'required']);

        if (!empty($errors)) {
            flash('error', 'يرجى التحقق من الحقول المطلوبة');
            $this->view('admin/majors/create', ['errors' => $errors, 'data' => $data]);
            return;
        }

        $majorId = Major::create($data);
        log_activity('create', 'majors', $majorId, 'إضافة تخصص جديد: ' . $data['name']);
        flash('success', 'تم إضافة التخصص بنجاح');
        redirect(url('/admin/majors'));
    }

    public function edit(): void
    {
        $id = $this->getParam('id');
        $major = Major::find($id);

        if (!$major) {
            flash('error', 'التخصص غير موجود');
            redirect(url('/admin/majors'));
        }

        $levels = \App\Models\Level::all();
        $semesters = \App\Models\Semester::all();
        $this->view('admin/majors/edit', ['major' => $major, 'levels' => $levels, 'semesters' => $semesters]);
    }

    public function update(): void
    {
        if (!$this->isPost() || !verify_csrf()) {
            redirect(url('/admin/majors'));
        }

        $id = $this->postParam('id');

        $data = [
            'name' => trim($this->postParam('name', '')),
        ];

        $errors = $this->validate($data, ['name' => 'required']);

        if (!empty($errors)) {
            flash('error', 'يرجى التحقق من الحقول المطلوبة');
            $major = Major::find($id);
            $this->view('admin/majors/edit', ['major' => $major, 'errors' => $errors]);
            return;
        }

        Major::updateRecord($id, $data);
        log_activity('update', 'majors', $id, 'تحديث التخصص: ' . $data['name']);
        flash('success', 'تم تحديث التخصص بنجاح');
        redirect(url('/admin/majors'));
    }

    public function delete(): void
    {
        $id = $this->getParam('id');
        $major = Major::find($id);

        if (!$major) {
            flash('error', 'التخصص غير موجود');
            redirect(url('/admin/majors'));
        }

        Major::deleteRecord($id);
        log_activity('delete', 'majors', $id, 'حذف التخصص: ' . $major->name);
        flash('success', 'تم حذف التخصص بنجاح');
        redirect(url('/admin/majors'));
    }
}
