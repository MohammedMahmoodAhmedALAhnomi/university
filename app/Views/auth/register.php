<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إنشاء حساب جديد - اللجنة العلمية</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #0d6efd, #0a58ca);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card shadow-lg border-0 rounded-4">
                    <div class="card-body p-4 p-md-5">
                        <div class="text-center mb-4">
                            <i class="fas fa-user-plus fa-3x text-primary mb-2"></i>
                            <h3 class="fw-bold">إنشاء حساب جديد</h3>
                            <p class="text-muted small">قم بإنشاء حسابك للوصول إلى كافة المحتويات والمراجع الأكاديمية</p>
                        </div>
                        <?php if (flash_has('error')): ?>
                            <div class="alert alert-danger text-center small py-2 mb-3">
                                <i class="fas fa-exclamation-circle ms-1"></i>
                                <?php echo flash('error'); ?>
                            </div>
                        <?php endif; ?>
                        <form action="<?php echo url('/register/post'); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <div class="mb-3">
                                <label for="full_name" class="form-label">
                                    <i class="fas fa-user ms-1"></i>الاسم الكامل
                                </label>
                                <input type="text" class="form-control" id="full_name" name="full_name" required autofocus>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope ms-1"></i>البريد الإلكتروني
                                </label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="phone" class="form-label">
                                    <i class="fas fa-phone ms-1"></i>رقم الهاتف (اختياري)
                                </label>
                                <input type="text" class="form-control" id="phone" name="phone">
                            </div>
                            <div class="mb-4">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock ms-1"></i>كلمة المرور
                                </label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 btn-lg rounded-pill fw-bold shadow-sm mb-3">
                                <i class="fas fa-user-check ms-1"></i>
                                إنشاء الحساب
                            </button>

                        </form>

                        <div class="position-relative my-4 text-center">
                            <hr class="text-muted opacity-25">
                            <span class="position-absolute top-50 start-50 translate-middle bg-white px-3 text-muted small">أو</span>
                        </div>

                        <a href="<?php echo url('/auth/google'); ?>" class="btn btn-outline-dark w-100 rounded-pill py-2 mb-3 fw-bold d-flex align-items-center justify-content-center gap-2 text-decoration-none">
                            <svg width="18" height="18" viewBox="0 0 18 18"><path fill="#4285F4" d="M17.64 9.2c0-.74-.06-1.28-.19-1.84H9v3.34h4.96c-.1.83-.64 2.08-1.84 2.92l2.84 2.2c1.7-1.57 2.68-3.88 2.68-6.62z"/><path fill="#34A853" d="M9 18c2.43 0 4.47-.8 5.96-2.18l-2.84-2.2c-.76.53-1.78.9-3.12.9-2.38 0-4.41-1.57-5.13-3.74L.97 13.04C2.45 15.98 5.48 18 9 18z"/><path fill="#FBBC05" d="M3.87 10.78c-.18-.53-.28-1.09-.28-1.78s.1-1.25.28-1.78L.97 4.96C.35 6.18 0 7.55 0 9s.35 2.82.97 4.04l2.9-2.26z"/><path fill="#EA4335" d="M9 3.58c1.32 0 2.5.45 3.44 1.35l2.58-2.58C13.46.89 11.43 0 9 0 5.48 0 2.45 2.02.97 4.96l2.9 2.26C4.59 5.05 6.62 3.58 9 3.58z"/></svg>
                            التسجيل باستخدام Google
                        </a>

                        <div class="text-center mt-3">
                            <span class="text-muted small">لديك حساب بالفعل؟</span>
                            <a href="<?php echo url('/login'); ?>" class="fw-bold text-primary ms-1 text-decoration-none">تسجيل الدخول</a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
