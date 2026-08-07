<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\JoinRequest;
use App\Models\Major;
use App\Models\Level;
use App\Models\User;

use App\Models\Notification;

class JoinRequestController extends Controller
{
    public function requestForm(): void
    {
        if (!isset($_SESSION['user_id'])) {
            redirect(url('/login'));
        }

        $role = $_SESSION['user_role'] ?? 'guest';
        if (in_array($role, ['admin', 'major_admin', 'manager'])) {
            flash('info', 'أنت تمتلك بالفعل صلاحية إدارية أو دور مندوب في المنصة');
            redirect(url('/'));
        }

        // Check if there is already a pending request
        $pending = JoinRequest::findPendingByUserId($_SESSION['user_id']);
        if ($pending) {
            redirect(url('/pending-approval'));
        }

        $majors = Major::getActive();
        $levels = Level::getActive();

        $this->view('auth/request_role', [
            'majors' => $majors,
            'levels' => $levels,
        ]);
    }

    public function submitRequest(): void
    {
        if (!isset($_SESSION['user_id']) || !$this->isPost() || !verify_csrf()) {
            redirect(url('/login'));
        }

        $userId = $_SESSION['user_id'];
        $accountType = trim($this->postParam('account_type', 'representative'));
        $majorId = (int)$this->postParam('major_id', 0);
        $levelId = $accountType === 'representative' ? (int)$this->postParam('level_id', 0) : null;

        if (empty($majorId)) {
            flash('error', 'يرجى اختيار التخصص العلمي');
            redirect(url('/request-role'));
        }

        if ($accountType === 'representative' && empty($levelId)) {
            flash('error', 'يرجى اختيار المستوى الدراسي للمندوب');
            redirect(url('/request-role'));
        }

        \App\Config\Database::insert('join_requests', [
            'user_id' => $userId,
            'account_type' => $accountType,
            'major_id' => $majorId,
            'level_id' => $levelId,
            'status' => 'pending',
        ]);

        $user = User::find($userId);
        $userName = $user->full_name ?? 'مستخدم جديد';
        Notification::send(null, 'طلب انضمام جديد 📩', 'قام ' . $userName . ' بتقديم طلب انضمام جديد كـ ' . ($accountType === 'representative' ? 'مندوب مستوى' : 'مسؤول تخصص'), 'warning', url('/admin/requests'));

        log_activity('create', 'join_requests', $userId, 'إرسال طلب انضمام كـ ' . ($accountType === 'representative' ? 'مندوب' : 'مسؤول'));
        redirect(url('/pending-approval'));
    }

    public function pendingApproval(): void
    {
        if (!isset($_SESSION['user_id'])) {
            redirect(url('/login'));
        }

        $userId = $_SESSION['user_id'];
        $request = JoinRequest::findLatestByUserId($userId);

        if (!$request) {
            redirect(url('/request-role'));
        }

        if ($request->status === 'approved') {
            $user = User::find($userId);
            if ($user) {
                $_SESSION['user_role'] = $user->role;
                $_SESSION['managed_major_id'] = $user->managed_major_id;
                $_SESSION['managed_level_id'] = $user->managed_level_id;
            }
            flash('success', 'تمت الموافقة على طلبك! أهلاً بك في لوحة التحكم.');
            redirect(url('/admin/dashboard'));
        }

        $this->view('auth/pending_approval', [
            'request' => $request
        ]);
    }

    private function canManage(): bool
    {
        $role = $_SESSION['user_role'] ?? '';
        return $role === 'admin' || $role === 'major_admin';
    }

    public function adminIndex(): void
    {
        if (!$this->canManage()) {
            flash('error', 'لا تملك صلاحية الوصول');
            redirect(url('/admin/courses'));
        }

        $role = $_SESSION['user_role'] ?? '';
        $managedMajorId = get_managed_major_id();

        if ($role === 'major_admin' && $managedMajorId) {
            $requests = JoinRequest::getAllWithDetails($managedMajorId);
        } else {
            $requests = JoinRequest::getAllWithDetails();
        }

        $this->view('admin/requests/index', [
            'requests' => $requests
        ]);
    }

    public function approve(): void
    {
        if (!$this->canManage()) {
            flash('error', 'لا تملك صلاحية الوصول');
            redirect(url('/admin/courses'));
        }
        if (!verify_csrf()) {
            flash('error', 'طلب غير صالح أو انتهت مهلة الجلسة');
            redirect(url('/admin/requests'));
        }

        $id = (int)$this->getParam('id');
        $req = \App\Config\Database::fetch("SELECT * FROM join_requests WHERE id = ?", [$id]);

        if (!$req) {
            flash('error', 'الطلب غير موجود');
            redirect(url('/admin/requests'));
        }

        $role = $_SESSION['user_role'] ?? '';
        $managedMajorId = get_managed_major_id();

        if ($role === 'major_admin' && $managedMajorId && (int)$req->major_id !== (int)$managedMajorId) {
            flash('error', 'لا يمكنك الموافقة على طلبات في تخصص آخر');
            redirect(url('/admin/requests'));
        }

        if (JoinRequest::approve($id)) {
            Notification::send((int)$req->user_id, 'تمت الموافقة على طلبك 🎉', 'تمت الموافقة على طلب الانضمام وتفعيل صلاحياتك بنجاح. أهلاً بك في لوحة التحكم!', 'success', url('/admin/dashboard'));
            log_activity('update', 'join_requests', $id, 'الموافقة على طلب الانضمام رقم ' . $id);
            flash('success', 'تمت الموافقة على الطلب وتعيين الصلاحيات بنجاح');
        } else {
            flash('error', 'تعذر الموافقة على الطلب');
        }
        redirect(url('/admin/requests'));
    }

    public function reject(): void
    {
        if (!$this->canManage()) {
            flash('error', 'لا تملك صلاحية الوصول');
            redirect(url('/admin/courses'));
        }
        if (!verify_csrf()) {
            flash('error', 'طلب غير صالح أو انتهت مهلة الجلسة');
            redirect(url('/admin/requests'));
        }

        $id = (int)$this->getParam('id');
        $req = \App\Config\Database::fetch("SELECT * FROM join_requests WHERE id = ?", [$id]);

        if (!$req) {
            flash('error', 'الطلب غير موجود');
            redirect(url('/admin/requests'));
        }

        $role = $_SESSION['user_role'] ?? '';
        $managedMajorId = get_managed_major_id();

        if ($role === 'major_admin' && $managedMajorId && (int)$req->major_id !== (int)$managedMajorId) {
            flash('error', 'لا يمكنك رفض طلبات في تخصص آخر');
            redirect(url('/admin/requests'));
        }

        if (JoinRequest::reject($id)) {
            Notification::send((int)$req->user_id, 'نتيجة طلب الانضمام ❌', 'نأسف، تم رفض طلب الانضمام الخاص بك.', 'danger', url('/request-role'));
            log_activity('update', 'join_requests', $id, 'رفض طلب الانضمام رقم ' . $id);
            flash('success', 'تم رفض طلب الانضمام');
        } else {
            flash('error', 'تعذر رفض الطلب');
        }
        redirect(url('/admin/requests'));
    }
}
