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
        $levels = Level::getActive();
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

        $currentRole = $_SESSION['user_role'] ?? '';
        $currentUserId = $_SESSION['user_id'] ?? 0;
        $scopedMajorId = null;

        if ($currentRole === 'major_admin') {
            $currentUser = User::find($currentUserId);
            $scopedMajorId = $currentUser ? ($currentUser->managed_major_id ?: $currentUser->major_id) : null;
        }

        $requestedRole = $this->postParam('role', 'manager');

        if ($currentRole === 'major_admin') {
            if ($requestedRole === 'admin' || $requestedRole === 'major_admin') {
                $requestedRole = 'manager';
            }
            $majorId = $scopedMajorId;
        } else {
            $majorId = $this->postParam('major_id', '');
            if ($currentRole !== 'admin' && $requestedRole === 'admin') {
                $requestedRole = 'manager';
            }
        }

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
            $levels = Level::getActive();
            $this->view('admin/users/create', [
                'errors' => $errors,
                'data' => $data,
                'majors' => $majors,
                'levels' => $levels,
                'currentRole' => $currentRole,
                'scopedMajorId' => $scopedMajorId,
            ]);
            return;
        }

        $existingUser = User::findByEmail($data['email']);
        if ($existingUser) {
            flash('error', 'البريد الإلكتروني مستخدم بالفعل');
            $majors = Major::getActive();
            $levels = Level::getActive();
            $this->view('admin/users/create', [
                'errors' => ['email' => ['البريد الإلكتروني مستخدم بالفعل']],
                'data' => $data,
                'majors' => $majors,
                'levels' => $levels,
                'currentRole' => $currentRole,
                'scopedMajorId' => $scopedMajorId,
            ]);
            return;
        }

        User::ensureColumns();
        $data['password'] = User::hashPassword($data['password']);
        $userId = User::create($data);
        log_activity('create', 'users', $userId, 'إضافة مستخدم جديد: ' . $data['full_name']);
        flash('success', 'تم إضافة المستخدم بنجاح');
        redirect(url('/admin/users'));
    }

    public function edit($id = 0): void
    {
        if (!$this->canManage()) {
            flash('error', 'لا تملك صلاحية الوصول');
            redirect(url('/admin/courses'));
        }
        $id = (int)($id ?: $this->getParam('id'));

        $user = User::find($id);

        if (!$user) {
            flash('error', 'المستخدم غير موجود');
            redirect(url('/admin/users'));
        }

        $currentRole = $_SESSION['user_role'] ?? '';
        $currentUserId = (int)($_SESSION['user_id'] ?? 0);
        $currentUserEmail = strtolower(trim($_SESSION['user_email'] ?? ''));

        if (User::isSystemOwner($user)) {
            $isLoggedInOwner = ($currentUserId === (int)$user->id) || ($currentUserEmail === 'mohammedalahnomi04@gmail.com');
            if (!$isLoggedInOwner) {
                flash('error', 'حساب مالك النظام الرئيسي محمي، ولا يمكن تعديله إلا من قِبَل المالك شخصياً.');
                redirect(url('/admin/users'));
            }
        }

        $scopedMajorId = null;

        if ($currentRole === 'major_admin') {
            $currentUser = User::find($currentUserId);
            $scopedMajorId = $currentUser ? ($currentUser->managed_major_id ?: $currentUser->major_id) : null;
            $userMajor = $user->managed_major_id ?: $user->major_id;
            if ((int)$userMajor !== (int)$scopedMajorId) {
                flash('error', 'لا يمكنك تعديل مستخدم ينتمي لتخصص آخر');
                redirect(url('/admin/users'));
            }
        }

        $majors = Major::getActive();
        $levels = Level::getActive();
        $this->view('admin/users/edit', [
            'user' => $user,
            'majors' => $majors,
            'levels' => $levels,
            'currentRole' => $currentRole,
            'scopedMajorId' => $scopedMajorId,
            'isOwnerTarget' => User::isSystemOwner($user),
        ]);
    }

    public function update($id = 0): void
    {
        if (!$this->canManage()) {
            flash('error', 'لا تملك صلاحية الوصول');
            redirect(url('/admin/courses'));
        }
        if (!$this->isPost() || !verify_csrf()) {
            redirect(url('/admin/users'));
        }

        $id = (int)($id ?: $this->postParam('id', $this->getParam('id', 0)));

        $user = User::find($id);

        if (!$user) {
            flash('error', 'المستخدم غير موجود');
            redirect(url('/admin/users'));
        }

        $currentRole = $_SESSION['user_role'] ?? '';
        $currentUserId = (int)($_SESSION['user_id'] ?? 0);
        $currentUserEmail = strtolower(trim($_SESSION['user_email'] ?? ''));

        if (User::isSystemOwner($user)) {
            $isLoggedInOwner = ($currentUserId === (int)$user->id) || ($currentUserEmail === 'mohammedalahnomi04@gmail.com');
            if (!$isLoggedInOwner) {
                flash('error', 'حساب مالك النظام الرئيسي محمي، ولا يمكن تعديله إلا من قِبَل المالك شخصياً.');
                redirect(url('/admin/users'));
            }
        }

        $scopedMajorId = null;

        if ($currentRole === 'major_admin') {
            $currentUser = User::find($currentUserId);
            $scopedMajorId = $currentUser ? ($currentUser->managed_major_id ?: $currentUser->major_id) : null;
            $userMajor = $user->managed_major_id ?: $user->major_id;
            if ((int)$userMajor !== (int)$scopedMajorId) {
                flash('error', 'لا يمكنك تعديل مستخدم ينتمي لتخصص آخر');
                redirect(url('/admin/users'));
            }
        }

        $requestedRole = $this->postParam('role', $user->role);

        if (User::isSystemOwner($user)) {
            $requestedRole = 'admin';
        } elseif ($currentRole === 'major_admin') {
            if ($requestedRole === 'admin' || $requestedRole === 'major_admin') {
                $requestedRole = $user->role;
            }
            $majorId = $scopedMajorId;
        } else {
            $majorId = $this->postParam('major_id', $user->major_id);
            if ($currentRole !== 'admin' && $requestedRole === 'admin') {
                $requestedRole = $user->role;
            }
        }

        $levelId = $this->postParam('managed_level_id', $user->managed_level_id);

        $data = [
            'full_name' => trim($this->postParam('full_name', '')),
            'email' => trim($this->postParam('email', '')),
            'phone' => trim($this->postParam('phone', '')),
            'role' => $requestedRole,
            'major_id' => !empty($majorId) ? (int)$majorId : null,
            'managed_major_id' => !empty($majorId) ? (int)$majorId : null,
            'managed_level_id' => !empty($levelId) ? (int)$levelId : null,
            'is_active' => User::isSystemOwner($user) ? 1 : ($this->postParam('is_active', '1') ? 1 : 0),
        ];

        $errors = $this->validate($data, [
            'full_name' => 'required',
            'email' => 'required|email',
        ]);

        $password = $this->postParam('password', '');
        if (!empty($password)) {
            if (mb_strlen($password) < 8) {
                $errors['password'][] = 'حقل كلمة المرور يجب أن يكون 8 حروف على الأقل';
            }
        }

        if (!empty($errors)) {
            flash('error', 'يرجى التحقق من الحقول المطلوبة');
            $majors = Major::getActive();
            $levels = Level::getActive();
            $this->view('admin/users/edit', [
                'user' => $user,
                'errors' => $errors,
                'majors' => $majors,
                'levels' => $levels,
                'currentRole' => $currentRole,
                'scopedMajorId' => $scopedMajorId,
                'isOwnerTarget' => User::isSystemOwner($user),
            ]);
            return;
        }

        if ($data['email'] !== $user->email) {
            $existingUser = User::findByEmail($data['email']);
            if ($existingUser) {
                flash('error', 'البريد الإلكتروني مستخدم بالفعل');
                $majors = Major::getActive();
                $levels = Level::getActive();
                $this->view('admin/users/edit', [
                    'user' => $user,
                    'errors' => ['email' => ['البريد الإلكتروني مستخدم بالفعل']],
                    'majors' => $majors,
                    'levels' => $levels,
                    'currentRole' => $currentRole,
                    'scopedMajorId' => $scopedMajorId,
                    'isOwnerTarget' => User::isSystemOwner($user),
                ]);
                return;
            }
        }

        if (!empty($password)) {
            $data['password'] = User::hashPassword($password);
        }

        User::ensureColumns();
        User::updateRecord($id, $data);
        log_activity('update', 'users', $id, 'تحديث المستخدم: ' . $data['full_name']);
        flash('success', 'تم تحديث حساب ودور المستخدم بنجاح');
        redirect(url('/admin/users'));
    }

    public function delete($id = 0): void
    {
        if (!$this->canManage()) {
            flash('error', 'لا تملك صلاحية الوصول');
            redirect(url('/admin/courses'));
        }
        if (!verify_csrf()) {
            flash('error', 'طلب غير صالح أو انتهت مهلة الجلسة');
            redirect(url('/admin/users'));
        }
        $id = (int)($id ?: $this->getParam('id'));

        if ($id === 1 || User::isSystemOwner($id)) {
            flash('error', 'لا يمكن حذف حساب مالك النظام الرئيسي فهو محمي نهائياً');
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

        if (User::isSystemOwner($user)) {
            flash('error', 'لا يمكن حذف حساب مالك النظام الرئيسي فهو محمي نهائياً');
            redirect(url('/admin/users'));
        }

        $currentRole = $_SESSION['user_role'] ?? '';
        $currentUserId = $_SESSION['user_id'] ?? 0;

        if ($currentRole === 'major_admin') {
            $currentUser = User::find($currentUserId);
            $scopedMajorId = $currentUser ? ($currentUser->managed_major_id ?: $currentUser->major_id) : null;
            $userMajor = $user->managed_major_id ?: $user->major_id;
            if ((int)$userMajor !== (int)$scopedMajorId) {
                flash('error', 'لا يمكنك حذف مستخدم ينتمي لتخصص آخر');
                redirect(url('/admin/users'));
            }
        }

        User::deleteRecord($id);
        log_activity('delete', 'users', $id, 'حذف المستخدم: ' . $user->full_name);
        flash('success', 'تم حذف المستخدم بنجاح');
        redirect(url('/admin/users'));
    }
}
