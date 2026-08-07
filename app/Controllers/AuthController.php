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
            redirect(url('/'));
        }
        $this->view('auth/login');
    }


    private function setRememberCookie($user): void
    {
        if (empty($user)) return;
        $secret = env('APP_SECRET', 'university_app_secret_2026');
        $userId = is_object($user) ? $user->id : ($user['id'] ?? 0);
        $userEmail = is_object($user) ? $user->email : ($user['email'] ?? '');
        $userPass = is_object($user) ? $user->password : ($user['password'] ?? '');
        $hash = hash_hmac('sha256', $userId . '|' . $userEmail . '|' . $userPass, $secret);
        $token = $userId . ':' . $hash;
        setcookie('app_remember_token', $token, time() + 31536000, '/', '', false, true);
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
        $_SESSION['user_email'] = $user->email;
        $_SESSION['managed_major_id'] = $user->managed_major_id;
        $_SESSION['managed_level_id'] = $user->managed_level_id;

        $this->setRememberCookie($user);
        log_activity('login', 'users', $user->id, 'تسجيل دخول ناجح');

        flash('success', 'مرحبًا بعودتك، ' . $user->full_name);
        redirect(url('/'));
    }


    public function registerForm(): void
    {
        if (isset($_SESSION['user_id'])) {
            redirect(url('/'));
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

        $createdUser = User::find($userId);

        session_regenerate_id(true);
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_name'] = $fullName;
        $_SESSION['user_role'] = 'guest';
        $_SESSION['user_email'] = $email;

        if ($createdUser) {
            $this->setRememberCookie($createdUser);
        }

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
        $_SESSION['user_email'] = $user->email;
        $_SESSION['managed_level_id'] = $user->managed_level_id;
        $_SESSION['managed_major_id'] = $user->managed_major_id;

        $this->setRememberCookie($user);
        User::updateLastLogin($user->id);
        log_activity('login', 'users', $user->id, 'تسجيل دخول عبر Google');

        flash('success', 'مرحباً بعودتك، ' . $user->full_name);
        redirect(url('/'));
    }

    public function logout(): void
    {
        if (isset($_SESSION['user_id'])) {
            log_activity('logout', 'users', $_SESSION['user_id'], 'تسجيل خروج');
        }
        $_SESSION = [];
        setcookie('app_remember_token', '', time() - 3600, '/');
        session_destroy();
        session_start();
        flash('success', 'تم تسجيل الخروج بنجاح.');
        redirect(url('/login'));
    }
}
