<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;

class AuthController extends Controller
{
    public function loginForm(): void
    {
        if (isset($_SESSION['user_id'])) {
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

        flash('success', 'مرحبًا بعودتك، ' . $user->full_name);
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
