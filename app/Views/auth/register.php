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
                            <p class="text-muted small">قم بإنشاء حساب للانضمام لإدارة اللجنة العلمية</p>
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
                                <i class="fas fa-arrow-left ms-1"></i>
                                المتابعة لاختيار الدور
                            </button>
                        </form>
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
