<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Models\JoinRequest;

class AuthController extends Controller
{
    public function loginForm(): void
    {
        if (isset($_SESSION['user_id'])) {
            $role = $_SESSION['user_role'] ?? 'guest';
            if ($role === 'guest') {
                $req = JoinRequest::findPendingByUserId($_SESSION['user_id']);
                redirect(url($req ? '/pending-approval' : '/request-role'));
            }
            redirect(url('/admin/dashboard'));
        }
        $this->view('auth/login');
    }

    public function login(): void
    {
        if (!$this->isPost()) {
            redirect(url('/login'));
        }

        $email = trim($this->postParam('email', ''));
        $password = $this->postParam('password', '');

        if (empty($email) || empty($password)) {
            flash('error', 'يرجى إدخال البريد الإلكتروني وكلمة المرور');
            redirect(url('/login'));
        }

        $user = User::findByEmail($email);

        if (!$user || !User::verifyPassword($password, $user->password)) {
            flash('error', 'البريد الإلكتروني أو كلمة المرور غير صحيحة');
            redirect(url('/login'));
        }

        if (!$user->is_active) {
            flash('error', 'الحساب غير نشط، يرجى التواصل مع الإدارة');
            redirect(url('/login'));
        }

        $_SESSION['user_id'] = $user->id;
        $_SESSION['user_name'] = $user->full_name;
        $_SESSION['user_role'] = $user->role;
        $_SESSION['managed_level_id'] = $user->managed_level_id;
        $_SESSION['managed_major_id'] = $user->managed_major_id;

        User::updateLastLogin($user->id);
        log_activity('login', 'users', $user->id, 'تسجيل دخول');

        if ($user->role === 'guest') {
            $req = JoinRequest::findPendingByUserId($user->id);
            if ($req) {
                redirect(url('/pending-approval'));
            } else {
                redirect(url('/request-role'));
            }
        }

        flash('success', 'مرحبًا بعودتك، ' . $user->full_name);
        redirect(url('/admin/dashboard'));
    }

    public function registerForm(): void
    {
        if (isset($_SESSION['user_id'])) {
            redirect(url('/admin/dashboard'));
        }
        $this->view('auth/register');
    }

    public function register(): void
    {
        if (!$this->isPost() || !verify_csrf()) {
            redirect(url('/register'));
        }

        $fullName = trim($this->postParam('full_name', ''));
        $email = trim($this->postParam('email', ''));
        $password = $this->postParam('password', '');
        $phone = trim($this->postParam('phone', ''));

        if (empty($fullName) || empty($email) || empty($password)) {
            flash('error', 'يرجى تعبئة كافة الحقول المطلوبة');
            redirect(url('/register'));
        }

        if (User::findByEmail($email)) {
            flash('error', 'البريد الإلكتروني مستخدم بالفعل');
            redirect(url('/register'));
        }

        $userId = \App\Config\Database::insert('users', [
            'full_name' => $fullName,
            'email' => $email,
            'password' => User::hashPassword($password),
            'phone' => $phone,
            'role' => 'guest',
            'is_active' => 1,
        ]);

        $_SESSION['user_id'] = $userId;
        $_SESSION['user_name'] = $fullName;
        $_SESSION['user_role'] = 'guest';

        log_activity('create', 'users', $userId, 'تسجيل حساب جديد كـ guest');
        flash('success', 'تم إنشاء حسابك بنجاح! يرجى اختيار نوع الحساب الآن.');
        redirect(url('/request-role'));
    }

    public function googleRedirect(): void
    {
        $url = \App\Services\GoogleAuthService::getAuthUrl();
        if (!$url) {
            flash('error', 'تنبيه: يتطلب تفعيل تسجيل الدخول بـ Google إدخال (Google Client ID & Client Secret) من لوحة التحكم -> الإعدادات.');
            redirect(url('/login'));
        }
        redirect($url);
    }

    public function googleCallback(): void
    {
        $code = $this->getParam('code');
        if (!$code) {
            flash('error', 'فشل الحصول على رمز التفويض من Google');
            redirect(url('/login'));
        }

        $googleUser = \App\Services\GoogleAuthService::getUserInfoFromCode($code);
        if (!$googleUser || empty($googleUser['email'])) {
            flash('error', 'تعذر جلب بيانات الحساب من Google');
            redirect(url('/login'));
        }

        $email = strtolower(trim($googleUser['email']));
        $googleId = $googleUser['id'] ?? null;
        $fullName = $googleUser['name'] ?? $email;

        // Check if user already exists
        $user = User::findByEmail($email);
        if (!$user && $googleId) {
            $user = \App\Config\Database::fetch("SELECT * FROM users WHERE google_id = ? LIMIT 1", [$googleId]);
        }

        if (!$user) {
            // Auto register new user from Google
            $userId = \App\Config\Database::insert('users', [
                'full_name' => $fullName,
                'email' => $email,
                'google_id' => $googleId,
                'password' => User::hashPassword(bin2hex(random_bytes(10))),
                'role' => 'guest',
                'is_active' => 1,
            ]);
            $user = User::find($userId);
        } else {
            if (empty($user->google_id) && $googleId) {
                \App\Config\Database::update('users', ['google_id' => $googleId], 'id = :id', ['id' => $user->id]);
            }
        }

        if (!$user->is_active) {
            flash('error', 'الحساب غير نشط، يرجى التواصل مع الإدارة');
            redirect(url('/login'));
        }

        $_SESSION['user_id'] = $user->id;
        $_SESSION['user_name'] = $user->full_name;
        $_SESSION['user_role'] = $user->role;
        $_SESSION['managed_level_id'] = $user->managed_level_id;
        $_SESSION['managed_major_id'] = $user->managed_major_id;

        User::updateLastLogin($user->id);
        log_activity('login', 'users', $user->id, 'تسجيل دخول عبر Google');

        if ($user->role === 'guest') {
            $req = \App\Models\JoinRequest::findPendingByUserId($user->id);
            redirect(url($req ? '/pending-approval' : '/request-role'));
        }

        flash('success', 'مرحباً بعودتك، ' . $user->full_name);
        redirect(url('/admin/dashboard'));
    }

    public function logout(): void
    {
        if (isset($_SESSION['user_id'])) {
            log_activity('logout', 'users', $_SESSION['user_id'], 'تسجيل خروج');
        }
        $_SESSION = [];
        session_destroy();
        redirect(url('/'));
    }
}
