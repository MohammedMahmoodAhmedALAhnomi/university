<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="fas fa-book ms-2 text-primary"></i>المواد الدراسية</h4>
        <p class="text-muted small mb-0">إدارة المواد الدراسية والتخصصات</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?php echo url('/admin/courses/create'); ?>" class="btn btn-primary rounded-pill px-3 shadow-sm">
            <i class="fas fa-plus ms-1"></i>إضافة مادة
        </a>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm stat-card h-100" style="border-bottom: 3px solid var(--primary);">
            <div class="card-body text-center p-3">
                <div class="rounded-circle bg-primary bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-2" style="width: 44px; height: 44px;">
                    <i class="fas fa-book text-primary"></i>
                </div>
                <h3 class="fw-bold mb-0 text-dark"><?php echo escape($coursesCount ?? count($courses ?? [])); ?></h3>
                <small class="text-muted">إجمالي المواد</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm stat-card h-100" style="border-bottom: 3px solid var(--success);">
            <div class="card-body text-center p-3">
                <div class="rounded-circle bg-success bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-2" style="width: 44px; height: 44px;">
                    <i class="fas fa-check-circle text-success"></i>
                </div>
                <h3 class="fw-bold mb-0 text-dark"><?php echo escape(array_reduce($courses ?? [], fn($c, $cr) => $c + ($cr->is_active ? 1 : 0), 0)); ?></h3>
                <small class="text-muted">مواد نشطة</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm stat-card h-100" style="border-bottom: 3px solid var(--info);">
            <div class="card-body text-center p-3">
                <div class="rounded-circle bg-info bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-2" style="width: 44px; height: 44px;">
                    <i class="fas fa-paperclip text-info"></i>
                </div>
                <h3 class="fw-bold mb-0 text-dark"><?php echo escape(array_reduce($courses ?? [], fn($c, $cr) => $c + (int)($cr->files_count ?? 0), 0)); ?></h3>
                <small class="text-muted">إجمالي الملفات</small>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
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
                    <a href="<?php echo url('/admin/courses'); ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3">إلغاء الفلتر</a>
                </div>
            <?php endif; ?>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between py-3">
        <h6 class="fw-bold mb-0"><i class="fas fa-list text-primary ms-1"></i>قائمة المواد الدراسية</h6>
        <?php if (!empty($courses)): ?>
            <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3"><?php echo count($courses); ?> مادة</span>
        <?php endif; ?>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">الاسم</th>
                        <th>التخصص</th>
                        <th>المستوى</th>
                        <th>الفصل</th>
                        <th>الملفات</th>
                        <th>الحالة</th>
                        <th class="pe-3">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($courses)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">
                                <i class="fas fa-book fa-2x mb-2 d-block text-gray-300"></i>
                                لا توجد مواد دراسية
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($courses as $course): ?>
                            <tr>
                                <td class="ps-3 fw-bold text-dark"><i class="fas fa-book-open text-primary ms-2"></i><?php echo escape($course->name); ?></td>
                                <td><span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2"><i class="fas fa-university ms-1"></i><?php echo escape($course->major_name ?? '-'); ?></span></td>
                                <td><span class="badge bg-info bg-opacity-10 text-info rounded-pill px-3 py-2"><i class="fas fa-layer-group ms-1"></i><?php echo escape($course->level_name ?? '-'); ?></span></td>
                                <td><span class="badge bg-purple bg-opacity-10 text-purple rounded-pill px-3 py-2"><i class="fas fa-calendar-alt ms-1"></i><?php echo escape($course->semester_name ?? '-'); ?></span></td>
                                <td><span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3 py-2"><i class="fas fa-paperclip ms-1"></i><?php echo escape($course->files_count ?? 0); ?> ملف</span></td>
                                <td>
                                    <?php if ($course->is_active): ?>
                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2"><i class="fas fa-check-circle ms-1"></i>نشط</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3 py-2"><i class="fas fa-times-circle ms-1"></i>غير نشط</span>
                                    <?php endif; ?>
                                </td>
                                <td class="pe-3">
                                    <div class="d-flex gap-1">
                                        <a href="<?php echo url('/admin/courses/' . escape($course->id) . '/edit'); ?>" class="btn btn-warning btn-sm rounded-pill px-3">
                                            <i class="fas fa-edit ms-1"></i>تعديل
                                        </a>
                                        <a href="<?php echo url('/admin/courses/' . escape($course->id) . '/delete?_csrf_token=' . csrf_token()); ?>" class="btn btn-danger btn-sm rounded-pill px-3" onclick="return confirm('هل أنت متأكد من الحذف؟')">
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
