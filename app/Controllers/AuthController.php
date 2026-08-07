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
            if ($role === 'admin' || $role === 'major_admin' || $role === 'manager') {
                redirect(url('/admin/dashboard'));
            }
            redirect(url('/'));
        }
        $this->view('auth/login');
    }


    public function login(): void
    {
        if (!$this->isPost() || !verify_csrf()) {
            flash('error', 'طلب غير صالح أو انتهت مهلة الجلسة');
            redirect(url('/login'));
        }

        $email = trim($this->postParam('email', ''));
        $password = $this->postParam('password', '');

        if (empty($email) || empty($password)) {
            flash('error', 'يرجى إدخال البريد الإلكتروني وكلمة المرور');
            redirect(url('/login'));
        }

        $user = User::findByEmail($email);

        if (!$user || !User::verifyPassword($password, $user->password, (int)$user->id)) {
            flash('error', 'البريد الإلكتروني أو كلمة المرور غير صحيحة');
            redirect(url('/login'));
        }

        if (!$user->is_active) {
            flash('error', 'الحساب غير نشط، يرجى التواصل مع الإدارة');
            redirect(url('/login'));
        }

        session_regenerate_id(true);
        $_SESSION['user_id'] = $user->id;
        $_SESSION['user_name'] = $user->full_name;
        $_SESSION['user_role'] = $user->role;
        $_SESSION['managed_major_id'] = $user->managed_major_id;
        $_SESSION['managed_level_id'] = $user->managed_level_id;

        log_activity('login', 'users', $user->id, 'تسجيل دخول ناجح');

        flash('success', 'مرحبًا بعودتك، ' . $user->full_name);
        if ($user->role === 'admin' || $user->role === 'major_admin' || $user->role === 'manager') {
            redirect(url('/admin/dashboard'));
        } else {
            redirect(url('/'));
        }
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

        session_regenerate_id(true);
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_name'] = $fullName;
        $_SESSION['user_role'] = 'guest';

        log_activity('create', 'users', $userId, 'تسجيل حساب جديد كـ guest');
        flash('success', 'تم إنشاء حسابك بنجاح! أهلاً بك في المنصة التعليمية.');
        redirect(url('/'));
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

        // Check if user already exists by email or google_id
        $user = User::findByEmail($email);
        if (!$user && $googleId) {
            $user = User::findByGoogleId($googleId);
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

        session_regenerate_id(true);
        $_SESSION['user_id'] = $user->id;
        $_SESSION['user_name'] = $user->full_name;
        $_SESSION['user_role'] = $user->role;
        $_SESSION['managed_level_id'] = $user->managed_level_id;
        $_SESSION['managed_major_id'] = $user->managed_major_id;

        User::updateLastLogin($user->id);
        log_activity('login', 'users', $user->id, 'تسجيل دخول عبر Google');

        if ($user->role === 'guest') {
            flash('success', 'مرحباً بعودتك، ' . $user->full_name);
            redirect(url('/'));
        }

        flash('success', 'مرحباً بعودتك، ' . $user->full_name);
        if ($user->role === 'admin') {
            redirect(url('/admin/dashboard'));
        } else {
            redirect(url('/admin/courses'));
        }
    }

    public function logout(): void
    {
        if (isset($_SESSION['user_id'])) {
            log_activity('logout', 'users', $_SESSION['user_id'], 'تسجيل خروج');
        }
        $_SESSION = [];
        session_destroy();
        session_start();
        flash('success', 'تم تسجيل الخروج بنجاح.');
        redirect(url('/login'));
    }
}
