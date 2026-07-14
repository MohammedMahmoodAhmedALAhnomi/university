<div class="d-flex justify-content-between align-items-center mb-4">
    <h3><i class="fas fa-plus-circle ms-2"></i>إضافة ملف جديد</h3>
    <a href="<?php echo url('/admin/files'); ?>" class="btn btn-secondary">
        <i class="fas fa-arrow-right ms-1"></i>عودة
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="<?php echo url('/admin/files/store'); ?>" method="POST" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="course_id" class="form-label">المادة <span class="text-danger">*</span></label>
                    <select class="form-select" id="course_id" name="course_id" required>
                        <option value="">اختر المادة</option>
                        <?php foreach ($courses as $course): ?>
                            <option value="<?php echo escape($course->id); ?>" <?php echo old('course_id') == $course->id ? 'selected' : ''; ?>>
                                <?php echo escape($course->name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="file_type" class="form-label">تصنيف الملف <span class="text-danger">*</span></label>
                    <select class="form-select" id="file_type" name="file_type" required>
                        <option value="">اختر التصنيف</option>
                        <option value="lecture" <?php echo old('file_type') === 'lecture' ? 'selected' : ''; ?>>محاضرة</option>
                        <option value="summary" <?php echo old('file_type') === 'summary' ? 'selected' : ''; ?>>ملخص</option>
                        <option value="model" <?php echo old('file_type') === 'model' ? 'selected' : ''; ?>>نموذج اختبار</option>
                        <option value="exam" <?php echo old('file_type') === 'exam' ? 'selected' : ''; ?>>اختبار</option>
                        <option value="other" <?php echo old('file_type') === 'other' ? 'selected' : ''; ?>>أخرى</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="title" class="form-label">العنوان <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="title" name="title" value="<?php echo escape(old('title')); ?>" required>
                </div>
                <div class="col-md-6">
                    <label for="file" class="form-label">الملف <span class="text-danger">*</span></label>
                    <input type="file" class="form-control" id="file" name="file" required>
                </div>
                <div class="col-12">
                    <label for="description" class="form-label">الوصف</label>
                    <textarea class="form-control" id="description" name="description" rows="3"><?php echo escape(old('description')); ?></textarea>
                </div>
                <div class="col-md-6">
                    <div class="form-check form-switch mt-2">
                        <input class="form-check-input" type="checkbox" id="is_approved" name="is_approved" value="1" <?php echo old('is_approved') ? 'checked' : ''; ?> checked>
                        <label class="form-check-label" for="is_approved">موافق عليه</label>
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