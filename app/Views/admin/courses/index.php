<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <h3><i class="fas fa-book ms-2"></i>إدارة المواد الدراسية</h3>
    <a href="<?php echo url('/admin/courses/create'); ?>" class="btn btn-primary">
        <i class="fas fa-plus ms-1"></i>إضافة مادة
    </a>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-body py-3">
        <form method="GET" action="<?php echo url('/admin/courses'); ?>" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label small">التخصص</label>
                <select name="major_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">جميع التخصصات</option>
                    <?php foreach ($majors ?? [] as $m): ?>
                        <option value="<?php echo $m->id; ?>" <?php echo (isset($_GET['major_id']) && $_GET['major_id'] == $m->id) ? 'selected' : ''; ?>>
                            <?php echo escape($m->name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php if (isset($_GET['major_id']) && $_GET['major_id']): ?>
                <div class="col-md-2">
                    <a href="<?php echo url('/admin/courses'); ?>" class="btn btn-outline-secondary btn-sm">إلغاء الفلتر</a>
                </div>
            <?php endif; ?>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-primary">
                    <tr>
                        <th>الاسم</th>
                        <th>التخصص</th>
                        <th>المستوى</th>
                        <th>الفصل</th>
                        <th>الحالة</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($courses)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">لا توجد مواد دراسية</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($courses as $course): ?>
                            <tr>
                                <td class="fw-bold"><?php echo escape($course->name); ?></td>
                                <td><?php echo escape($course->major_name ?? ''); ?></td>
                                <td><?php echo escape($course->level_name ?? ''); ?></td>
                                <td><?php echo escape($course->semester_name ?? ''); ?></td>
                                <td>
                                    <?php if ($course->is_active): ?>
                                        <span class="badge bg-success">نشط</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">غير نشط</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="<?php echo url('/admin/courses/' . escape($course->id) . '/edit'); ?>" class="btn btn-warning btn-sm rounded-pill px-3">
                                            <i class="fas fa-edit ms-1"></i>تعديل
                                        </a>
                                        <a href="<?php echo url('/admin/courses/' . escape($course->id) . '/delete'); ?>" class="btn btn-danger btn-sm rounded-pill px-3" onclick="return confirm('هل أنت متأكد من الحذف؟')">
                                            <i class="fas fa-trash ms-1"></i>حذف
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>