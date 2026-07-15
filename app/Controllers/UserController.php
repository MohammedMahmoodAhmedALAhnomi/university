<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Models\Major;

class UserController extends Controller
{
    public function index(): void
    {
        $users = User::getAllWithMajor();
        $this->view('admin/users/index', ['users' => $users]);
    }

    public function create(): void
    {
        $majors = Major::getActive();
        $this->view('admin/users/create', ['majors' => $majors]);
    }

    public function store(): void
    {
        if (!$this->isPost() || !verify_csrf()) {
            redirect(url('/admin/users'));
        }

        $data = [
            'full_name' => trim($this->postParam('full_name', '')),
            'email' => trim($this->postParam('email', '')),
            'password' => $this->postParam('password', ''),
            'role' => $this->postParam('role', 'user'),
            'major_id' => $this->postParam('major_id', ''),
        ];

        $majorId = $this->postParam('major_id', '');
        if (!empty($majorId)) {
            $data['managed_major_id'] = (int)$majorId;
            if ($data['role'] !== 'admin') {
                $data['role'] = 'manager';
            }
        }

        $errors = $this->validate($data, [
            'full_name' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);

        if (!empty($errors)) {
            flash('error', 'يرجى التحقق من الحقول المطلوبة');
            $majors = Major::getActive();
            $this->view('admin/users/create', [
                'errors' => $errors,
                'data' => $data,
                'majors' => $majors,
            ]);
            return;
        }

        $existingUser = User::findByEmail($data['email']);
        if ($existingUser) {
            flash('error', 'البريد الإلكتروني مستخدم بالفعل');
            $majors = Major::getActive();
            $this->view('admin/users/create', [
                'errors' => ['email' => ['البريد الإلكتروني مستخدم بالفعل']],
                'data' => $data,
                'majors' => $majors,
            ]);
            return;
        }

        $data['password'] = User::hashPassword($data['password']);
        $userId = User::create($data);
        log_activity('create', 'users', $userId, 'إضافة مستخدم جديد: ' . $data['full_name']);
        flash('success', 'تم إضافة المستخدم بنجاح');
        redirect(url('/admin/users'));
    }

    public function edit(): void
    {
        $id = $this->getParam('id');
        $user = User::find($id);

        if (!$user) {
            flash('error', 'المستخدم غير موجود');
            redirect(url('/admin/users'));
        }

        $majors = Major::getActive();
        $this->view('admin/users/edit', ['user' => $user, 'majors' => $majors]);
    }

    public function update(): void
    {
        if (!$this->isPost() || !verify_csrf()) {
            redirect(url('/admin/users'));
        }

        $id = $this->postParam('id');
        $user = User::find($id);

        if (!$user) {
            flash('error', 'المستخدم غير موجود');
            redirect(url('/admin/users'));
        }

        $data = [
            'full_name' => trim($this->postParam('full_name', '')),
            'email' => trim($this->postParam('email', '')),
            'role' => $this->postParam('role', $user->role),
            'major_id' => $this->postParam('major_id', $user->major_id),
        ];

        $majorId = $this->postParam('major_id', $user->major_id);
        if (!empty($majorId)) {
            $data['managed_major_id'] = (int)$majorId;
            if ($data['role'] !== 'admin') {
                $data['role'] = 'manager';
            }
        } else {
            $data['managed_major_id'] = null;
        }

        $errors = $this->validate($data, [
            'full_name' => 'required',
            'email' => 'required|email',
        ]);

        $password = $this->postParam('password', '');
        if (!empty($password)) {
            if (mb_strlen($password) < 8) {
                $errors['password'][] = 'حقل password يجب أن يكون 8 حرفًا على الأقل';
            }
        }

        if (!empty($errors)) {
            flash('error', 'يرجى التحقق من الحقول المطلوبة');
            $majors = Major::getActive();
            $this->view('admin/users/edit', [
                'user' => $user,
                'errors' => $errors,
                'majors' => $majors,
            ]);
            return;
        }

        if ($data['email'] !== $user->email) {
            $existingUser = User::findByEmail($data['email']);
            if ($existingUser) {
                flash('error', 'البريد الإلكتروني مستخدم بالفعل');
                $majors = Major::getActive();
                $this->view('admin/users/edit', [
                    'user' => $user,
                    'errors' => ['email' => ['البريد الإلكتروني مستخدم بالفعل']],
                    'majors' => $majors,
                ]);
                return;
            }
        }

        if (!empty($password)) {
            $data['password'] = User::hashPassword($password);
        }

        User::updateRecord($id, $data);
        log_activity('update', 'users', $id, 'تحديث المستخدم: ' . $data['full_name']);
        flash('success', 'تم تحديث المستخدم بنجاح');
        redirect(url('/admin/users'));
    }

    public function delete(): void
    {
        $id = $this->getParam('id');

        if ($id == $_SESSION['user_id']) {
            flash('error', 'لا يمكنك حذف حسابك الخاص');
            redirect(url('/admin/users'));
        }

        $user = User::find($id);

        if (!$user) {
            flash('error', 'المستخدم غير موجود');
            redirect(url('/admin/users'));
        }

        User::deleteRecord($id);
        log_activity('delete', 'users', $id, 'حذف المستخدم: ' . $user->full_name);
        flash('success', 'تم حذف المستخدم بنجاح');
        redirect(url('/admin/users'));
    }
}
