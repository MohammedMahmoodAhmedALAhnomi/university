<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Models\Major;
use App\Models\Level;

class UserController extends Controller
{
    private function canManage(): bool
    {
        $role = $_SESSION['user_role'] ?? '';
        return $role === 'admin' || $role === 'major_admin';
    }

    public function index(): void
    {
        if (!$this->canManage()) {
            flash('error', 'لا تملك صلاحية الوصول');
            redirect(url('/admin/courses'));
        }

        $currentRole = $_SESSION['user_role'] ?? '';
        $currentUserId = $_SESSION['user_id'] ?? 0;
        $scopedMajorId = null;

        if ($currentRole === 'major_admin') {
            $currentUser = User::find($currentUserId);
            $scopedMajorId = $currentUser ? ($currentUser->managed_major_id ?: $currentUser->major_id) : null;
        }

        $search = trim($_GET['search'] ?? '');
        $role = trim($_GET['role'] ?? 'all');

        $users = User::getFilteredUsers($search, $role, $scopedMajorId);

        $this->view('admin/users/index', [
            'users' => $users,
            'search' => $search,
            'role' => $role,
            'currentRole' => $currentRole,
        ]);
    }

    public function create(): void
    {
        if (!$this->canManage()) {
            flash('error', 'لا تملك صلاحية الوصول');
            redirect(url('/admin/courses'));
        }
        $majors = Major::getActive();
        $levels = Level::getAll();
        $this->view('admin/users/create', ['majors' => $majors, 'levels' => $levels]);
    }

    public function store(): void
    {
        if (!$this->canManage()) {
            flash('error', 'لا تملك صلاحية الوصول');
            redirect(url('/admin/courses'));
        }
        if (!$this->isPost() || !verify_csrf()) {
            redirect(url('/admin/users'));
        }

        $requestedRole = $this->postParam('role', 'student');
        $currentRole = $_SESSION['user_role'] ?? '';

        if ($currentRole !== 'admin' && $requestedRole === 'admin') {
            $requestedRole = 'manager';
        }

        $majorId = $this->postParam('major_id', '');
        $levelId = $this->postParam('managed_level_id', '');

        $data = [
            'full_name' => trim($this->postParam('full_name', '')),
            'email' => trim($this->postParam('email', '')),
            'phone' => trim($this->postParam('phone', '')),
            'password' => $this->postParam('password', ''),
            'role' => $requestedRole,
            'major_id' => !empty($majorId) ? (int)$majorId : null,
            'managed_major_id' => !empty($majorId) ? (int)$majorId : null,
            'managed_level_id' => !empty($levelId) ? (int)$levelId : null,
            'is_active' => $this->postParam('is_active', '1') ? 1 : 0,
        ];

        $errors = $this->validate($data, [
            'full_name' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);

        if (!empty($errors)) {
            flash('error', 'يرجى التحقق من الحقول المطلوبة');
            $majors = Major::getActive();
            $levels = Level::getAll();
            $this->view('admin/users/create', [
                'errors' => $errors,
                'data' => $data,
                'majors' => $majors,
                'levels' => $levels,
            ]);
            return;
        }

        $existingUser = User::findByEmail($data['email']);
        if ($existingUser) {
            flash('error', 'البريد الإلكتروني مستخدم بالفعل');
            $majors = Major::getActive();
            $levels = Level::getAll();
            $this->view('admin/users/create', [
                'errors' => ['email' => ['البريد الإلكتروني مستخدم بالفعل']],
                'data' => $data,
                'majors' => $majors,
                'levels' => $levels,
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
        if (!$this->canManage()) {
            flash('error', 'لا تملك صلاحية الوصول');
            redirect(url('/admin/courses'));
        }
        $id = (int)$this->getParam('id');

        if ($id === 1) {
            flash('error', 'لا يمكن تعديل الحساب الرئيسي للنظام');
            redirect(url('/admin/users'));
        }

        $user = User::find($id);

        if (!$user) {
            flash('error', 'المستخدم غير موجود');
            redirect(url('/admin/users'));
        }

        $majors = Major::getActive();
        $levels = Level::getAll();
        $this->view('admin/users/edit', ['user' => $user, 'majors' => $majors, 'levels' => $levels]);
    }

    public function update(): void
    {
        if (!$this->canManage()) {
            flash('error', 'لا تملك صلاحية الوصول');
            redirect(url('/admin/courses'));
        }
        if (!$this->isPost() || !verify_csrf()) {
            redirect(url('/admin/users'));
        }

        $id = (int)$this->postParam('id');

        if ($id === 1) {
            flash('error', 'لا يمكن تعديل الحساب الرئيسي للنظام');
            redirect(url('/admin/users'));
        }

        $user = User::find($id);

        if (!$user) {
            flash('error', 'المستخدم غير موجود');
            redirect(url('/admin/users'));
        }

        $requestedRole = $this->postParam('role', $user->role);
        $currentRole = $_SESSION['user_role'] ?? '';

        if ($currentRole !== 'admin' && $requestedRole === 'admin') {
            $requestedRole = $user->role;
        }

        $majorId = $this->postParam('major_id', $user->major_id);
        $levelId = $this->postParam('managed_level_id', $user->managed_level_id);

        $data = [
            'full_name' => trim($this->postParam('full_name', '')),
            'email' => trim($this->postParam('email', '')),
            'phone' => trim($this->postParam('phone', '')),
            'role' => $requestedRole,
            'major_id' => !empty($majorId) ? (int)$majorId : null,
            'managed_major_id' => !empty($majorId) ? (int)$majorId : null,
            'managed_level_id' => !empty($levelId) ? (int)$levelId : null,
            'is_active' => $this->postParam('is_active', '1') ? 1 : 0,
        ];

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
            $levels = Level::getAll();
            $this->view('admin/users/edit', [
                'user' => $user,
                'errors' => $errors,
                'majors' => $majors,
                'levels' => $levels,
            ]);
            return;
        }

        if ($data['email'] !== $user->email) {
            $existingUser = User::findByEmail($data['email']);
            if ($existingUser) {
                flash('error', 'البريد الإلكتروني مستخدم بالفعل');
                $majors = Major::getActive();
                $levels = Level::getAll();
                $this->view('admin/users/edit', [
                    'user' => $user,
                    'errors' => ['email' => ['البريد الإلكتروني مستخدم بالفعل']],
                    'majors' => $majors,
                    'levels' => $levels,
                ]);
                return;
            }
        }

        if (!empty($password)) {
            $data['password'] = User::hashPassword($password);
        }

        User::updateRecord($id, $data);
        log_activity('update', 'users', $id, 'تحديث المستخدم: ' . $data['full_name']);
        flash('success', 'تم تحديث حساب ودور المستخدم بنجاح');
        redirect(url('/admin/users'));
    }

    public function delete(): void
    {
        if (!$this->canManage()) {
            flash('error', 'لا تملك صلاحية الوصول');
            redirect(url('/admin/courses'));
        }
        if (!verify_csrf()) {
            flash('error', 'طلب غير صالح أو انتهت مهلة الجلسة');
            redirect(url('/admin/users'));
        }
        $id = (int)$this->getParam('id');

        if ($id === 1) {
            flash('error', 'لا يمكن حذف الحساب الرئيسي للنظام');
            redirect(url('/admin/users'));
        }

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
