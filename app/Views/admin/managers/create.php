<div class="d-flex justify-content-between align-items-center mb-4">
    <h3><i class="fas fa-user-plus ms-2"></i>إضافة مندوب جديد</h3>
    <a href="<?php echo url('/admin/managers'); ?>" class="btn btn-secondary">
        <i class="fas fa-arrow-right ms-1"></i>عودة
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="<?php echo url('/admin/managers/store'); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="full_name" class="form-label">الاسم <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo escape(old('full_name')); ?>" required>
                </div>
                <div class="col-md-6">
                    <label for="email" class="form-label">البريد الإلكتروني <span class="text-danger">*</span></label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo escape(old('email')); ?>" required>
                </div>
                <div class="col-md-6">
                    <label for="password" class="form-label">كلمة المرور <span class="text-danger">*</span></label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <?php if (empty($managedMajor)): ?>
                <div class="col-md-6">
                    <label for="managed_major_id" class="form-label">التخصص المسؤول عنه <span class="text-danger">*</span></label>
                    <select class="form-select" id="managed_major_id" name="managed_major_id" required>
                        <option value="">اختر التخصص</option>
                        <?php foreach ($majors as $major): ?>
                            <option value="<?php echo $major->id; ?>"><?php echo escape($major->name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php else: ?>
                    <input type="hidden" name="managed_major_id" value="<?php echo $managedMajor; ?>">
                <?php endif; ?>
                <div class="col-md-6">
                    <label for="managed_level_id" class="form-label">المستوى المسؤول عنه <span class="text-danger">*</span></label>
                    <select class="form-select" id="managed_level_id" name="managed_level_id" required>
                        <option value="">اختر المستوى</option>
                        <?php foreach ($levels as $level): ?>
                            <option value="<?php echo $level->id; ?>"><?php echo escape($level->name); ?></option>
                        <?php endforeach; ?>
                    </select>
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
