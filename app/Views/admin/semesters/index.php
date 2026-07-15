<div class="d-flex justify-content-between align-items-center mb-4">
    <h3><i class="fas fa-calendar-alt ms-2"></i>إدارة الفصول الدراسية</h3>
    <a href="<?php echo url('/admin/semesters/create'); ?>" class="btn btn-primary">
        <i class="fas fa-plus ms-1"></i>إضافة فصل
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
                        <th>رقم الفصل</th>
                        <th>ترتيب العرض</th>
                        <th>الحالة</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($semesters)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">لا توجد فصول دراسية</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($semesters as $semester): ?>
                            <tr>
                                <td><?php echo escape($semester->id); ?></td>
                                <td class="fw-bold"><?php echo escape($semester->name); ?></td>
                                <td><span class="badge bg-secondary"><?php echo escape($semester->semester_number ?? ''); ?></span></td>
                                <td><?php echo escape($semester->sort_order ?? 0); ?></td>
                                <td>
                                    <?php if ($semester->is_active): ?>
                                        <span class="badge bg-success">نشط</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">غير نشط</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="<?php echo url('/admin/semesters/' . escape($semester->id) . '/edit'); ?>" class="btn btn-warning btn-sm rounded-pill px-3">
                                            <i class="fas fa-edit ms-1"></i>تعديل
                                        </a>
                                        <a href="<?php echo url('/admin/semesters/' . escape($semester->id) . '/delete'); ?>" class="btn btn-danger btn-sm rounded-pill px-3" onclick="return confirm('هل أنت متأكد من الحذف؟')">
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