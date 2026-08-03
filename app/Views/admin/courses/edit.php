<div class="d-flex justify-content-between align-items-center mb-4">
    <h3><i class="fas fa-edit ms-2"></i>تعديل المادة الدراسية</h3>
    <a href="<?php echo url('/admin/courses'); ?>" class="btn btn-secondary">
        <i class="fas fa-arrow-right ms-1"></i>عودة
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="<?php echo url('/admin/courses/' . escape($course->id) . '/update'); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="id" value="<?php echo escape($course->id); ?>">
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="major_id" class="form-label">التخصص <span class="text-danger">*</span></label>
                    <?php if (isset($managedMajorId) && $managedMajorId): ?>
                        <input type="hidden" name="major_id" value="<?php echo $managedMajorId; ?>">
                        <input type="text" class="form-control" value="<?php foreach ($majors as $m) { if ($m->id == $managedMajorId) { echo escape($m->name); break; } } ?>" disabled>
                    <?php else: ?>
                    <select class="form-select" id="major_id" name="major_id" required>
                        <option value="">اختر التخصص</option>
                        <?php foreach ($majors as $major): ?>
                            <option value="<?php echo escape($major->id); ?>" <?php echo (old('major_id', $course->major_id) == $major->id) ? 'selected' : ''; ?>>
                                <?php echo escape($major->name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php endif; ?>
                </div>
                <div class="col-md-4">
                    <label for="level_id" class="form-label">المستوى <span class="text-danger">*</span></label>
                    <?php if (isset($managedLevelId) && $managedLevelId): ?>
                        <input type="hidden" name="level_id" value="<?php echo $managedLevelId; ?>">
                        <input type="text" class="form-control" value="<?php foreach ($levels as $l) { if ($l->id == $managedLevelId) { echo escape($l->name); break; } } ?>" disabled>
                    <?php else: ?>
                    <select class="form-select" id="level_id" name="level_id" required>
                        <option value="">اختر المستوى</option>
                        <?php foreach ($levels as $level): ?>
                            <option value="<?php echo escape($level->id); ?>" <?php echo (old('level_id', $course->level_id) == $level->id) ? 'selected' : ''; ?>>
                                <?php echo escape($level->name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php endif; ?>
                </div>
                <div class="col-md-4">
                    <label for="semester_id" class="form-label">الفصل الدراسي <span class="text-danger">*</span></label>
                    <select class="form-select" id="semester_id" name="semester_id" required>
                        <option value="">اختر الفصل</option>
                        <?php foreach ($semesters as $semester): ?>
                            <option value="<?php echo escape($semester->id); ?>" <?php echo (old('semester_id', $course->semester_id) == $semester->id) ? 'selected' : ''; ?>>
                                <?php echo escape($semester->name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12">
                    <label for="name" class="form-label">اسم المادة <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="name" name="name" value="<?php echo escape(old('name', $course->name)); ?>" required>
                </div>
                <div class="col-12">
                    <label for="description" class="form-label">الوصف</label>
                    <textarea class="form-control" id="description" name="description" rows="4"><?php echo escape(old('description', $course->description ?? '')); ?></textarea>
                </div>
                <div class="col-md-6">
                    <div class="form-check form-switch mt-2">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" <?php echo (old('is_active', $course->is_active ?? '1')) ? 'checked' : ''; ?>>
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