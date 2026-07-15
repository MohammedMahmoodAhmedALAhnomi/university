<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Models\Level;
use App\Models\Major;

class ManagerController extends Controller
{
    private function canManage(): bool
    {
        $role = $_SESSION['user_role'] ?? '';
        return $role === 'admin' || ($role === 'manager' && !empty($_SESSION['managed_major_id']));
    }

    private function getManagedMajor(): ?int
    {
        if ($_SESSION['user_role'] === 'admin') return null;
        return isset($_SESSION['managed_major_id']) ? (int)$_SESSION['managed_major_id'] : null;
    }

    public function index(): void
    {
        if (!$this->canManage()) {
            flash('error', 'لا تملك صلاحية الوصول');
            redirect(url('/admin/dashboard'));
        }
        $managedMajor = $this->getManagedMajor();
        if ($managedMajor) {
            $managers = User::getManagersByMajor($managedMajor);
        } else {
            $managers = User::getManagers();
        }
        $this->view('admin/managers/index', ['managers' => $managers]);
    }

    public function create(): void
    {
        if (!$this->canManage()) {
            flash('error', 'لا تملك صلاحية الوصول');
            redirect(url('/admin/dashboard'));
        }
        $managedMajor = $this->getManagedMajor();
        $levels = Level::getActive();
        if ($managedMajor) {
            $majors = [Major::find($managedMajor)];
        } else {
            $majors = Major::getActive();
        }
        $this->view('admin/managers/create', ['levels' => $levels, 'majors' => $majors, 'managedMajor' => $managedMajor]);
    }

    public function store(): void
    {
        if (!$this->isPost() || !verify_csrf()) {
            redirect(url('/admin/managers'));
        }
        if (!$this->canManage()) {
            flash('error', 'لا تملك صلاحية الوصول');
            redirect(url('/admin/dashboard'));
        }

        $managedMajor = $this->getManagedMajor();
        $data = [
            'full_name' => trim($this->postParam('full_name', '')),
            'email' => trim($this->postParam('email', '')),
            'password' => $this->postParam('password', ''),
            'managed_level_id' => $this->postParam('managed_level_id', ''),
            'managed_major_id' => $managedMajor ?: $this->postParam('managed_major_id', ''),
        ];

        $errors = $this->validate($data, [
            'full_name' => 'required|min:3',
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        if (empty($data['managed_level_id'])) {
            $errors['managed_level_id'][] = 'حقل المستوى مطلوب';
        }
        if (empty($data['managed_major_id'])) {
            $errors['managed_major_id'][] = 'حقل التخصص مطلوب';
        }

        if (User::findByEmail($data['email'])) {
            $errors['email'][] = 'البريد الإلكتروني مستخدم بالفعل';
        }

        if (!empty($errors)) {
            flash('error', 'يرجى التحقق من الحقول');
            $levels = Level::getActive();
            $majors = $managedMajor ? [Major::find($managedMajor)] : Major::getActive();
            $this->view('admin/managers/create', ['errors' => $errors, 'data' => $data, 'levels' => $levels, 'majors' => $majors, 'managedMajor' => $managedMajor]);
            return;
        }

        $userId = User::create([
            'full_name' => $data['full_name'],
            'email' => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'role' => 'manager',
            'managed_level_id' => (int)$data['managed_level_id'],
            'managed_major_id' => (int)$data['managed_major_id'],
            'is_active' => 1,
        ]);

        log_activity('create', 'users', $userId, 'إضافة مندوب: ' . $data['full_name']);
        flash('success', 'تم إضافة المندوب بنجاح');
        redirect(url('/admin/managers'));
    }

    public function edit(): void
    {
        if (!$this->canManage()) {
            flash('error', 'لا تملك صلاحية الوصول');
            redirect(url('/admin/dashboard'));
        }
        $id = $this->getParam('id');
        $manager = User::find($id);

        if (!$manager || $manager->role !== 'manager') {
            flash('error', 'المندوب غير موجود');
            redirect(url('/admin/managers'));
        }

        $managedMajor = $this->getManagedMajor();
        if ($managedMajor && $manager->managed_major_id != $managedMajor) {
            flash('error', 'لا يمكنك تعديل هذا المندوب');
            redirect(url('/admin/managers'));
        }

        $levels = Level::getActive();
        $majors = $managedMajor ? [Major::find($managedMajor)] : Major::getActive();
        $this->view('admin/managers/edit', ['manager' => $manager, 'levels' => $levels, 'majors' => $majors, 'managedMajor' => $managedMajor]);
    }

    public function update(): void
    {
        if (!$this->isPost() || !verify_csrf()) {
            redirect(url('/admin/managers'));
        }
        if (!$this->canManage()) {
            flash('error', 'لا تملك صلاحية الوصول');
            redirect(url('/admin/dashboard'));
        }

        $id = $this->postParam('id');
        $manager = User::find($id);

        if (!$manager || $manager->role !== 'manager') {
            flash('error', 'المندوب غير موجود');
            redirect(url('/admin/managers'));
        }

        $managedMajor = $this->getManagedMajor();
        if ($managedMajor && $manager->managed_major_id != $managedMajor) {
            flash('error', 'لا يمكنك تعديل هذا المندوب');
            redirect(url('/admin/managers'));
        }

        $data = [
            'full_name' => trim($this->postParam('full_name', '')),
            'email' => trim($this->postParam('email', '')),
            'managed_level_id' => (int)$this->postParam('managed_level_id', ''),
            'managed_major_id' => $managedMajor ?: (int)$this->postParam('managed_major_id', ''),
        ];

        $errors = $this->validate($data, [
            'full_name' => 'required|min:3',
            'email' => 'required|email',
        ]);

        $existing = User::findByEmail($data['email']);
        if ($existing && $existing->id != $id) {
            $errors['email'][] = 'البريد الإلكتروني مستخدم بالفعل';
        }

        $password = $this->postParam('password', '');
        if (!empty($password)) {
            if (strlen($password) < 6) {
                $errors['password'][] = 'كلمة المرور يجب أن تكون 6 أحرف على الأقل';
            } else {
                $data['password'] = password_hash($password, PASSWORD_DEFAULT);
            }
        }

        if (!empty($errors)) {
            flash('error', 'يرجى التحقق من الحقول');
            $levels = Level::getActive();
            $majors = $managedMajor ? [Major::find($managedMajor)] : Major::getActive();
            $manager = User::find($id);
            $this->view('admin/managers/edit', ['errors' => $errors, 'manager' => $manager, 'levels' => $levels, 'majors' => $majors, 'managedMajor' => $managedMajor]);
            return;
        }

        User::updateRecord($id, $data);
        log_activity('update', 'users', $id, 'تحديث المندوب: ' . $data['full_name']);
        flash('success', 'تم تحديث المندوب بنجاح');
        redirect(url('/admin/managers'));
    }

    public function delete(): void
    {
        if (!$this->canManage()) {
            flash('error', 'لا تملك صلاحية الوصول');
            redirect(url('/admin/dashboard'));
        }
        $id = $this->getParam('id');
        $manager = User::find($id);

        if (!$manager || $manager->role !== 'manager') {
            flash('error', 'المندوب غير موجود');
            redirect(url('/admin/managers'));
        }

        $managedMajor = $this->getManagedMajor();
        if ($managedMajor && $manager->managed_major_id != $managedMajor) {
            flash('error', 'لا يمكنك حذف هذا المندوب');
            redirect(url('/admin/managers'));
        }

        User::deleteRecord($id);
        log_activity('delete', 'users', $id, 'حذف المندوب: ' . $manager->full_name);
        flash('success', 'تم حذف المندوب بنجاح');
        redirect(url('/admin/managers'));
    }
}
