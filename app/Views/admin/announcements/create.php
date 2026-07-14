<div class="d-flex justify-content-between align-items-center mb-4">
    <h3><i class="fas fa-plus-circle ms-2"></i>إضافة إعلان جديد</h3>
    <a href="<?php echo url('/admin/announcements'); ?>" class="btn btn-secondary">
        <i class="fas fa-arrow-right ms-1"></i>عودة
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="<?php echo url('/admin/announcements/store'); ?>" method="POST" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <div class="row g-3">
                <div class="col-12">
                    <label for="title" class="form-label">العنوان <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="title" name="title" value="<?php echo escape(old('title')); ?>" required>
                </div>
                <div class="col-12">
                    <label for="content" class="form-label">المحتوى <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="content" name="content" rows="6" required><?php echo escape(old('content')); ?></textarea>
                </div>
                <div class="col-md-4">
                    <label for="image" class="form-label">الصورة (اختياري)</label>
                    <input type="file" class="form-control" id="image" name="image" accept="image/*">
                </div>
                <div class="col-md-4">
                    <label for="starts_at" class="form-label">تاريخ البدء</label>
                    <input type="date" class="form-control" id="starts_at" name="starts_at" value="<?php echo escape(old('starts_at')); ?>">
                </div>
                <div class="col-md-4">
                    <label for="expires_at" class="form-label">تاريخ الانتهاء</label>
                    <input type="date" class="form-control" id="expires_at" name="expires_at" value="<?php echo escape(old('expires_at')); ?>">
                </div>
                <div class="col-md-4">
                    <div class="form-check form-switch mt-2">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" <?php echo old('is_active') ? 'checked' : ''; ?> checked>
                        <label class="form-check-label" for="is_active">نشط</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-check form-switch mt-2">
                        <input class="form-check-input" type="checkbox" id="is_pinned" name="is_pinned" value="1" <?php echo old('is_pinned') ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="is_pinned">مثبت</label>
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