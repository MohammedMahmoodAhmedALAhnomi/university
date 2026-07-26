<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="fas fa-layer-group ms-2 text-primary"></i>المستويات</h4>
        <p class="text-muted small mb-0">إدارة المستويات الدراسية</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?php echo url('/admin/levels/create'); ?>" class="btn btn-primary rounded-pill px-3 shadow-sm">
            <i class="fas fa-plus ms-1"></i>إضافة مستوى
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between py-3">
        <h6 class="fw-bold mb-0"><i class="fas fa-layer-group text-primary ms-1"></i>قائمة المستويات</h6>
        <?php if (!empty($levels)): ?>
            <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3"><?php echo count($levels); ?> مستوى</span>
        <?php endif; ?>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">#</th>
                        <th>الاسم</th>
                        <th>رقم المستوى</th>
                        <th>ترتيب العرض</th>
                        <th>الحالة</th>
                        <th class="pe-3">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($levels)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-5">
                                <i class="fas fa-layer-group fa-2x mb-2 d-block text-gray-300"></i>
                                لا توجد مستويات
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($levels as $level): ?>
                            <tr>
                                <td class="ps-3 text-muted fw-bold">#<?php echo escape($level->id); ?></td>
                                <td class="fw-bold text-dark">
                                    <span class="rounded-circle bg-teal bg-opacity-10 d-inline-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                        <i class="fas fa-layer-group text-teal"></i>
                                    </span>
                                    <?php echo escape($level->name); ?>
                                </td>
                                <td><span class="badge bg-teal bg-opacity-10 text-teal rounded-pill px-3 py-2"><i class="fas fa-cubes ms-1"></i>المستوى رقم <?php echo escape($level->level_number ?? ''); ?></span></td>
                                <td><span class="badge bg-secondary bg-opacity-10 text-dark rounded-pill px-3 py-2"><i class="fas fa-sort-amount-down ms-1"></i>الترتيب: <?php echo escape($level->sort_order ?? 0); ?></span></td>
                                <td>
                                    <?php if ($level->is_active): ?>
                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2"><i class="fas fa-check-circle ms-1"></i>نشط</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3 py-2"><i class="fas fa-times-circle ms-1"></i>غير نشط</span>
                                    <?php endif; ?>
                                </td>
                                <td class="pe-3">
                                    <div class="d-flex gap-1">
                                        <a href="<?php echo url('/admin/levels/' . escape($level->id) . '/edit'); ?>" class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit ms-1"></i>تعديل
                                        </a>
                                        <a href="<?php echo url('/admin/levels/' . escape($level->id) . '/delete'); ?>" class="btn btn-danger btn-sm" onclick="return confirm('هل أنت متأكد من الحذف؟')">
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
