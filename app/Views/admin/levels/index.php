<div class="d-flex justify-content-between align-items-center mb-4">
    <h3><i class="fas fa-layer-group ms-2"></i>إدارة المستويات</h3>
    <a href="<?php echo url('/admin/levels/create'); ?>" class="btn btn-primary">
        <i class="fas fa-plus ms-1"></i>إضافة مستوى
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
                        <th>رقم المستوى</th>
                        <th>ترتيب العرض</th>
                        <th>الحالة</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($levels)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">لا توجد مستويات</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($levels as $level): ?>
                            <tr>
                                <td><?php echo escape($level->id); ?></td>
                                <td class="fw-bold"><?php echo escape($level->name); ?></td>
                                <td><span class="badge bg-secondary"><?php echo escape($level->level_number ?? ''); ?></span></td>
                                <td><?php echo escape($level->sort_order ?? 0); ?></td>
                                <td>
                                    <?php if ($level->is_active): ?>
                                        <span class="badge bg-success">نشط</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">غير نشط</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="<?php echo url('/admin/levels/' . escape($level->id) . '/edit'); ?>" class="btn btn-warning btn-sm rounded-pill px-3">
                                            <i class="fas fa-edit ms-1"></i>تعديل
                                        </a>
                                        <a href="<?php echo url('/admin/levels/' . escape($level->id) . '/delete'); ?>" class="btn btn-danger btn-sm rounded-pill px-3" onclick="return confirm('هل أنت متأكد من الحذف؟')">
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