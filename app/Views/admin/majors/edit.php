<div class="d-flex justify-content-between align-items-center mb-4">
    <h3><i class="fas fa-edit ms-2"></i>تعديل التخصص</h3>
    <a href="<?php echo url('/admin/majors'); ?>" class="btn btn-secondary">
        <i class="fas fa-arrow-right ms-1"></i>عودة
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="<?php echo url('/admin/majors/' . escape($major->id) . '/update'); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="id" value="<?php echo escape($major->id); ?>">
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="name" class="form-label">الاسم <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="name" name="name" value="<?php echo escape(old('name', $major->name)); ?>" required>
                </div>
                <div class="col-md-6">
                    <label for="icon" class="form-label">الأيقونة</label>
                    <select class="form-select" id="icon" name="icon">
                        <option value="fas fa-university" <?php echo (old('icon', $major->icon ?? '') === 'fas fa-university') ? 'selected' : ''; ?>>جامعة</option>
                        <option value="fas fa-flask" <?php echo (old('icon', $major->icon ?? '') === 'fas fa-flask') ? 'selected' : ''; ?>>مختبر</option>
                        <option value="fas fa-calculator" <?php echo (old('icon', $major->icon ?? '') === 'fas fa-calculator') ? 'selected' : ''; ?>>حاسبة</option>
                        <option value="fas fa-laptop-code" <?php echo (old('icon', $major->icon ?? '') === 'fas fa-laptop-code') ? 'selected' : ''; ?>>برمجة</option>
                        <option value="fas fa-heartbeat" <?php echo (old('icon', $major->icon ?? '') === 'fas fa-heartbeat') ? 'selected' : ''; ?>>صحة</option>
                        <option value="fas fa-balance-scale" <?php echo (old('icon', $major->icon ?? '') === 'fas fa-balance-scale') ? 'selected' : ''; ?>>قانون</option>
                        <option value="fas fa-chart-line" <?php echo (old('icon', $major->icon ?? '') === 'fas fa-chart-line') ? 'selected' : ''; ?>>إدارة</option>
                        <option value="fas fa-palette" <?php echo (old('icon', $major->icon ?? '') === 'fas fa-palette') ? 'selected' : ''; ?>>فن</option>
                        <option value="fas fa-language" <?php echo (old('icon', $major->icon ?? '') === 'fas fa-language') ? 'selected' : ''; ?>>لغات</option>
                        <option value="fas fa-microscope" <?php echo (old('icon', $major->icon ?? '') === 'fas fa-microscope') ? 'selected' : ''; ?>>أحياء</option>
                    </select>
                </div>
                <div class="col-12">
                    <label for="description" class="form-label">الوصف</label>
                    <textarea class="form-control" id="description" name="description" rows="4"><?php echo escape(old('description', $major->description ?? '')); ?></textarea>
                </div>
                <div class="col-md-6">
                    <div class="form-check form-switch mt-2">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" <?php echo (old('is_active', $major->is_active ?? '1')) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="is_active">نشط</label>
                    </div>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save ms-1"></i>حفظ التغييرات
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>