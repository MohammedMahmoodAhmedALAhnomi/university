<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول - اللجنة العلمية</title>
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
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card shadow-lg border-0 rounded-4">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <i class="fas fa-graduation-cap fa-3x text-primary mb-3"></i>
                            <h3 class="fw-bold">اللجنة العلمية</h3>
                            <p class="text-muted">تسجيل الدخول إلى المنصة التعليمية</p>
                        </div>
                        <?php if (flash_has('error')): ?>
                            <div class="alert alert-danger text-center">
                                <i class="fas fa-exclamation-circle ms-1"></i>
                                <?php echo flash('error'); ?>
                            </div>
                        <?php endif; ?>
                        <form action="<?php echo url('/login/post'); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <div class="mb-3">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope ms-1"></i>البريد الإلكتروني
                                </label>
                                <input type="email" class="form-control <?php echo flash_has('email_error') ? 'is-invalid' : ''; ?>" id="email" name="email" value="<?php echo escape(old('email')); ?>" required autofocus>
                                <?php if (flash_has('email_error')): ?>
                                    <div class="invalid-feedback"><?php echo flash('email_error'); ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="mb-4">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock ms-1"></i>كلمة المرور
                                </label>
                                <div class="input-group">
                                    <input type="password" class="form-control <?php echo flash_has('password_error') ? 'is-invalid' : ''; ?>" id="password" name="password" required>
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword" tabindex="-1">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <?php if (flash_has('password_error')): ?>
                                        <div class="invalid-feedback"><?php echo flash('password_error'); ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 btn-lg">
                                <i class="fas fa-sign-in-alt ms-1"></i>
                                تسجيل الدخول
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
<script>
document.getElementById('togglePassword').addEventListener('click', function() {
    var input = document.getElementById('password');
    var icon = this.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
});
</script>
</body>
</html>