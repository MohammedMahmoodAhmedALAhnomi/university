<div class="d-flex justify-content-between align-items-center mb-4">
    <h3><i class="fas fa-edit ms-2"></i>تعديل الفصل الدراسي</h3>
    <a href="<?php echo url('/admin/semesters'); ?>" class="btn btn-secondary">
        <i class="fas fa-arrow-right ms-1"></i>عودة
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="<?php echo url('/admin/semesters/' . escape($semester->id) . '/update'); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="id" value="<?php echo escape($semester->id); ?>">
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="name" class="form-label">الاسم <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="name" name="name" value="<?php echo escape(old('name', $semester->name)); ?>" required>
                </div>
                <div class="col-md-4">
                    <label for="semester_number" class="form-label">رقم الفصل</label>
                    <input type="number" class="form-control" id="semester_number" name="semester_number" value="<?php echo escape(old('semester_number', $semester->semester_number ?? '')); ?>">
                </div>
                <div class="col-md-4">
                    <label for="sort_order" class="form-label">ترتيب العرض</label>
                    <input type="number" class="form-control" id="sort_order" name="sort_order" value="<?php echo escape(old('sort_order', $semester->sort_order ?? 0)); ?>">
                </div>
                <div class="col-md-6">
                    <div class="form-check form-switch mt-2">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" <?php echo (old('is_active', $semester->is_active ?? '1')) ? 'checked' : ''; ?>>
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