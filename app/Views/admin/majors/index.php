<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="fas fa-university ms-2 text-primary"></i>التخصصات</h4>
        <p class="text-muted small mb-0">إدارة التخصصات الأكاديمية بالكلية</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?php echo url('/admin/majors/create'); ?>" class="btn btn-primary rounded-pill px-3 shadow-sm">
            <i class="fas fa-plus ms-1"></i>إضافة تخصص
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between py-3">
        <h6 class="fw-bold mb-0"><i class="fas fa-university text-primary ms-1"></i>قائمة التخصصات</h6>
        <?php if (!empty($majors)): ?>
            <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3"><?php echo count($majors); ?> تخصص</span>
        <?php endif; ?>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">#</th>
                        <th>الاسم</th>
                        <th>الوصف</th>
                        <th>الأيقونة</th>
                        <th>المواد</th>
                        <th>الحالة</th>
                        <th class="pe-3">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($majors)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">
                                <i class="fas fa-university fa-2x mb-2 d-block text-gray-300"></i>
                                لا توجد تخصصات
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($majors as $major): ?>
                            <?php
                                $iconClass = get_major_icon($major->name, $major->icon ?? 'fas fa-university');
                                $codeTag = get_major_badge_code($major->name);
                            ?>
                            <tr>
                                <td class="ps-3 text-muted fw-bold">#<?php echo escape($major->id); ?></td>
                                <td class="fw-bold text-dark">
                                    <span class="rounded-circle bg-primary bg-opacity-10 d-inline-flex align-items-center justify-content-center me-2" style="width: 34px; height: 34px;">
                                        <i class="<?php echo escape($iconClass); ?> text-primary"></i>
                                    </span>
                                    <?php echo escape($major->name); ?>
                                    <?php if ($codeTag): ?>
                                        <span class="badge bg-primary text-white rounded-pill px-2 py-1 ms-1"><?php echo $codeTag; ?></span>
                                    <?php endif; ?>
                                </td>
                                <td><small class="text-muted"><?php echo escape(truncate($major->description ?? '-', 60)); ?></small></td>
                                <td><span class="badge bg-info bg-opacity-10 text-info rounded-pill px-3 py-2"><i class="<?php echo escape($iconClass); ?> ms-1"></i><?php echo $codeTag ?: 'تخصص'; ?></span></td>
                                <td><span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2"><i class="fas fa-book-open ms-1"></i><?php echo escape($major->courses_count ?? 0); ?> مادة</span></td>
                                <td>
                                    <?php if ($major->is_active): ?>
                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2"><i class="fas fa-check-circle ms-1"></i>نشط</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3 py-2"><i class="fas fa-times-circle ms-1"></i>غير نشط</span>
                                    <?php endif; ?>
                                </td>
                                <td class="pe-3">
                                    <div class="d-flex gap-1">
                                        <a href="<?php echo url('/admin/majors/' . escape($major->id) . '/edit'); ?>" class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit ms-1"></i>تعديل
                                        </a>
                                        <a href="<?php echo url('/admin/majors/' . escape($major->id) . '/delete?_csrf_token=' . csrf_token()); ?>" class="btn btn-danger btn-sm" onclick="return confirm('هل أنت متأكد من الحذف؟')">
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
