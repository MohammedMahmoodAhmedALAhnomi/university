<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="fas fa-plus-circle ms-2 text-primary"></i>إضافة تخصص جديد</h4>
        <p class="text-muted small mb-0">إضافة تخصص أكاديمي جديد</p>
    </div>
    <div>
        <a href="<?php echo url('/admin/majors'); ?>" class="btn btn-outline-secondary rounded-pill px-3">
            <i class="fas fa-arrow-right ms-1"></i>عودة
        </a>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="<?php echo url('/admin/majors/store'); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="name" class="form-label">الاسم <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="name" name="name" value="<?php echo escape(old('name')); ?>" required>
                </div>
                <div class="col-md-6">
                    <label for="icon" class="form-label">الأيقونة</label>
                    <select class="form-select" id="icon" name="icon">
                        <option value="fas fa-university" <?php echo old('icon') === 'fas fa-university' ? 'selected' : ''; ?>>جامعة</option>
                        <option value="fas fa-flask" <?php echo old('icon') === 'fas fa-flask' ? 'selected' : ''; ?>>مختبر</option>
                        <option value="fas fa-calculator" <?php echo old('icon') === 'fas fa-calculator' ? 'selected' : ''; ?>>حاسبة</option>
                        <option value="fas fa-laptop-code" <?php echo old('icon') === 'fas fa-laptop-code' ? 'selected' : ''; ?>>برمجة</option>
                        <option value="fas fa-heartbeat" <?php echo old('icon') === 'fas fa-heartbeat' ? 'selected' : ''; ?>>صحة</option>
                        <option value="fas fa-balance-scale" <?php echo old('icon') === 'fas fa-balance-scale' ? 'selected' : ''; ?>>قانون</option>
                        <option value="fas fa-chart-line" <?php echo old('icon') === 'fas fa-chart-line' ? 'selected' : ''; ?>>إدارة</option>
                        <option value="fas fa-palette" <?php echo old('icon') === 'fas fa-palette' ? 'selected' : ''; ?>>فن</option>
                        <option value="fas fa-language" <?php echo old('icon') === 'fas fa-language' ? 'selected' : ''; ?>>لغات</option>
                        <option value="fas fa-microscope" <?php echo old('icon') === 'fas fa-microscope' ? 'selected' : ''; ?>>أحياء</option>
                    </select>
                </div>
                <div class="col-12">
                    <label for="description" class="form-label">الوصف</label>
                    <textarea class="form-control" id="description" name="description" rows="4" placeholder="وصف مختصر عن التخصص..."><?php echo escape(old('description')); ?></textarea>
                </div>
                <div class="col-md-6">
                    <div class="form-check form-switch mt-2">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" <?php echo (old('is_active') !== '0' && old('is_active') !== 0) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="is_active">نشط</label>
                    </div>
                </div>
                <div class="col-12 pt-2 border-top">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save ms-1"></i>حفظ
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
