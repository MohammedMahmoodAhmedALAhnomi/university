<div class="d-flex justify-content-between align-items-center mb-4">
    <h3><i class="fas fa-university ms-2"></i>إدارة التخصصات</h3>
    <a href="<?php echo url('/admin/majors/create'); ?>" class="btn btn-primary">
        <i class="fas fa-plus ms-1"></i>إضافة تخصص
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-primary">
                    <tr>
                        <th>#</th>
                        <th>الاسم</th>
                        <th>الوصف</th>
                        <th>الأيقونة</th>
                        <th>عدد المواد</th>
                        <th>الحالة</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($majors)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">لا توجد تخصصات</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($majors as $major): ?>
                            <tr>
                                <td><?php echo escape($major->id); ?></td>
                                <td class="fw-bold"><?php echo escape($major->name); ?></td>
                                <td><?php echo escape(truncate($major->description ?? '', 80)); ?></td>
                                <td><i class="<?php echo escape($major->icon ?? 'fas fa-university'); ?> fa-lg text-primary"></i></td>
                                <td><span class="badge bg-info"><?php echo escape($major->courses_count ?? 0); ?></span></td>
                                <td>
                                    <?php if ($major->is_active): ?>
                                        <span class="badge bg-success">نشط</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">غير نشط</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="<?php echo url('/admin/majors/' . escape($major->id) . '/edit'); ?>" class="btn btn-warning btn-sm rounded-pill px-3">
                                            <i class="fas fa-edit ms-1"></i>تعديل
                                        </a>
                                        <a href="<?php echo url('/admin/majors/' . escape($major->id) . '/delete'); ?>" class="btn btn-danger btn-sm rounded-pill px-3" onclick="return confirm('هل أنت متأكد من الحذف؟')">
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