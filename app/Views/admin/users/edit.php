<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="fas fa-user-edit ms-2 text-warning"></i>تعديل حساب ودور المستخدم</h4>
        <p class="text-muted small mb-0">تغيير دور وتخصص ومستوى حساب المستخدم في النظام</p>
    </div>
    <div>
        <a href="<?php echo url('/admin/users'); ?>" class="btn btn-outline-secondary rounded-pill px-3">
            <i class="fas fa-arrow-right ms-1"></i>عودة لقائمة المستخدمين
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <form action="<?php echo url('/admin/users/' . escape($user->id) . '/update'); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="id" value="<?php echo escape($user->id); ?>">
            
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="full_name" class="form-label fw-bold">الاسم الكامل <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo escape(old('full_name', $user->full_name ?? $user->name ?? '')); ?>" required>
                </div>
                
                <div class="col-md-6">
                    <label for="email" class="form-label fw-bold">البريد الإلكتروني <span class="text-danger">*</span></label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo escape(old('email', $user->email)); ?>" required>
                </div>
                
                <div class="col-md-6">
                    <label for="password" class="form-label fw-bold">كلمة المرور (اتركه فارغاً للاحتفاظ بالقديمة)</label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="password" name="password" placeholder="أدخل كلمة مرور جديدة إذا أردت تغييرها">
                        <button type="button" class="btn btn-outline-secondary" id="toggleEditUserPassword" tabindex="-1">
                            <i class="fas fa-eye" id="editUserPasswordIcon"></i>
                        </button>
                    </div>
                </div>

                <div class="col-md-6">
                    <label for="phone" class="form-label fw-bold">رقم الهاتف</label>
                    <input type="text" class="form-control" id="phone" name="phone" value="<?php echo escape(old('phone', $user->phone ?? '')); ?>" placeholder="مثال: 771234567">
                </div>

                <?php if (!empty($isOwnerTarget)): ?>
                    <div class="col-12">
                        <div class="alert alert-warning border-warning d-flex align-items-center mb-2">
                            <i class="fas fa-crown me-2 fs-4"></i>
                            <div>
                                <strong>حساب مالك النظام الرئيسي:</strong> أنت تقوم بتعديل حساب مالك النظام الرئيسي (mohammedalahnomi04@gmail.com). هذا الحساب محمي بصلاحية مدير عام دائماً وحالة نشطة.
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="col-md-4">
                    <label for="role" class="form-label fw-bold">الدور في النظام <span class="text-danger">*</span></label>
                    <?php if (!empty($isOwnerTarget)): ?>
                        <input type="hidden" name="role" value="admin">
                        <input type="text" class="form-control border-warning bg-warning bg-opacity-10 fw-bold" value="👑 مدير إداري شامل (مالك النظام)" disabled readonly>
                    <?php else: ?>
                        <select class="form-select border-primary" id="role" name="role" required>
                            <option value="student" <?php echo (old('role', $user->role ?? '') === 'student' || old('role', $user->role ?? '') === 'guest') ? 'selected' : ''; ?>>👤 طالب / زائر عادي</option>
                            <option value="manager" <?php echo (old('role', $user->role ?? '') === 'manager') ? 'selected' : ''; ?>>🎖️ مندوب مستوى ودفعة</option>
                            <option value="major_admin" <?php echo (old('role', $user->role ?? '') === 'major_admin') ? 'selected' : ''; ?>>🛡️ مسؤول تخصص أكاديمي</option>
                            <?php if (($_SESSION['user_role'] ?? '') === 'admin'): ?>
                                <option value="admin" <?php echo (old('role', $user->role ?? '') === 'admin') ? 'selected' : ''; ?>>👑 مدير إداري / نظام شامل</option>
                            <?php endif; ?>
                        </select>
                    <?php endif; ?>
                </div>

                <div class="col-md-4">
                    <label for="major_id" class="form-label fw-bold">التخصص التابع له</label>
                    <?php if (($_SESSION['user_role'] ?? '') === 'major_admin' && !empty($scopedMajorId)): ?>
                        <?php 
                            $myMajor = array_filter($majors, fn($m) => $m->id == $scopedMajorId);
                            $myMajorName = !empty($myMajor) ? reset($myMajor)->name : 'تخصصك المحجوز';
                        ?>
                        <input type="hidden" name="major_id" value="<?php echo escape($scopedMajorId); ?>">
                        <input type="text" class="form-control" value="<?php echo escape($myMajorName); ?>" disabled readonly>
                    <?php else: ?>
                        <select class="form-select" id="major_id" name="major_id">
                            <option value="">-- اختر التخصص --</option>
                            <?php foreach ($majors as $major): ?>
                                <option value="<?php echo escape($major->id); ?>" <?php echo (old('major_id', $user->managed_major_id ?? $user->major_id) == $major->id) ? 'selected' : ''; ?>>
                                    <?php echo escape($major->name); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    <?php endif; ?>
                </div>

                <div class="col-md-4">
                    <label for="managed_level_id" class="form-label fw-bold">المستوى الدراسي للمندوب</label>
                    <select class="form-select" id="managed_level_id" name="managed_level_id">
                        <option value="">-- اختر المستوى --</option>
                        <?php if (!empty($levels)): ?>
                            <?php foreach ($levels as $lvl): ?>
                                <option value="<?php echo escape($lvl->id); ?>" <?php echo (old('managed_level_id', $user->managed_level_id) == $lvl->id) ? 'selected' : ''; ?>>
                                    <?php echo escape($lvl->name); ?>
                                </option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <option value="1" <?php echo (old('managed_level_id', $user->managed_level_id) == 1) ? 'selected' : ''; ?>>المستوى الأول</option>
                            <option value="2" <?php echo (old('managed_level_id', $user->managed_level_id) == 2) ? 'selected' : ''; ?>>المستوى الثاني</option>
                            <option value="3" <?php echo (old('managed_level_id', $user->managed_level_id) == 3) ? 'selected' : ''; ?>>المستوى الثالث</option>
                            <option value="4" <?php echo (old('managed_level_id', $user->managed_level_id) == 4) ? 'selected' : ''; ?>>المستوى الرابع</option>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="col-12 mt-4">
                    <div class="form-check form-switch">
                        <?php if (!empty($isOwnerTarget)): ?>
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" checked disabled>
                            <input type="hidden" name="is_active" value="1">
                            <label class="form-check-label fw-bold" for="is_active">حالة الحساب (نشط ومفعل دائماً)</label>
                        <?php else: ?>
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" <?php echo (old('is_active', $user->is_active ?? '1')) ? 'checked' : ''; ?>>
                            <label class="form-check-label fw-bold" for="is_active">حالة الحساب (نشط ومفعل)</label>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="col-12 mt-4">
                    <button type="submit" class="btn btn-primary rounded-pill px-5 shadow-sm">
                        <i class="fas fa-save ms-1"></i>حفظ التعديلات والدور الجديد
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('toggleEditUserPassword')?.addEventListener('click', function() {
    const p = document.getElementById('password');
    const i = document.getElementById('editUserPasswordIcon');
    if (p.type === 'password') { p.type = 'text'; i.classList.replace('fa-eye', 'fa-eye-slash'); }
    else { p.type = 'password'; i.classList.replace('fa-eye-slash', 'fa-eye'); }
});
</script>