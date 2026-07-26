<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="fas fa-plus-circle ms-2 text-primary"></i>إضافة مسؤول جديد</h4>
        <p class="text-muted small mb-0">إنشاء حساب مسؤول جديد في النظام</p>
    </div>
    <div>
        <a href="<?php echo url('/admin/users'); ?>" class="btn btn-outline-secondary rounded-pill px-3">
            <i class="fas fa-arrow-right ms-1"></i>عودة
        </a>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="<?php echo url('/admin/users/store'); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="full_name" class="form-label">الاسم الكامل <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo escape(old('full_name')); ?>" required>
                </div>
                <div class="col-md-6">
                    <label for="email" class="form-label">البريد الإلكتروني <span class="text-danger">*</span></label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo escape(old('email')); ?>" required>
                </div>
                <div class="col-md-6">
                    <label for="password" class="form-label">كلمة المرور <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="password" name="password" required>
                        <button type="button" class="btn btn-outline-secondary" id="toggleCreateUserPassword" tabindex="-1">
                            <i class="fas fa-eye" id="createUserPasswordIcon"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="phone" class="form-label">رقم الهاتف</label>
                    <input type="text" class="form-control" id="phone" name="phone" value="<?php echo escape(old('phone')); ?>">
                </div>
                <div class="col-md-4">
                    <label for="role" class="form-label">الدور <span class="text-danger">*</span></label>
                    <select class="form-select" id="role" name="role" required>
                        <option value="manager" <?php echo old('role') === 'manager' ? 'selected' : ''; ?>>مسؤول</option>
                        <option value="admin" <?php echo old('role') === 'admin' ? 'selected' : ''; ?>>مدير</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="major_id" class="form-label">التخصص</label>
                    <select class="form-select" id="major_id" name="major_id">
                        <option value="">بدون تخصص</option>
                        <?php foreach ($majors as $major): ?>
                            <option value="<?php echo escape($major->id); ?>" <?php echo old('major_id') == $major->id ? 'selected' : ''; ?>>
                                <?php echo escape($major->name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <div class="form-check form-switch mt-4">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" <?php echo old('is_active') ? 'checked' : ''; ?> checked>
                        <label class="form-check-label" for="is_active">نشط</label>
                    </div>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save ms-1"></i>حفظ
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('toggleCreateUserPassword')?.addEventListener('click', function() {
    const p = document.getElementById('password');
    const i = document.getElementById('createUserPasswordIcon');
    if (p.type === 'password') { p.type = 'text'; i.classList.replace('fa-eye', 'fa-eye-slash'); }
    else { p.type = 'password'; i.classList.replace('fa-eye-slash', 'fa-eye'); }
});
</script>