<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="fas fa-user-tie ms-2 text-primary"></i>المندوبين</h4>
        <p class="text-muted small mb-0">إدارة مندوبي الدفعات والتخصصات</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?php echo url('/admin/managers/create'); ?>" class="btn btn-primary rounded-pill px-3 shadow-sm">
            <i class="fas fa-plus-circle ms-1"></i>إضافة مندوب
        </a>
    </div>
</div>

<?php if (empty($managers)): ?>
    <div class="card shadow-sm">
        <div class="card-body text-center text-muted py-5">
            <i class="fas fa-user-tie fa-4x mb-3"></i>
            <p>لا يوجد مندوبين بعد</p>
        </div>
    </div>
<?php else: ?>
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between py-3">
            <h6 class="fw-bold mb-0"><i class="fas fa-user-tie text-primary ms-1"></i>قائمة المندوبين</h6>
            <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3"><?php echo count($managers); ?> مندوب</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>الاسم</th>
                            <th>البريد الإلكتروني</th>
                            <th>التخصص</th>
                            <th>المستوى</th>
                            <th>الحالة</th>
                            <th>آخر دخول</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($managers as $m): ?>
                            <tr>
                                <td class="fw-bold text-dark"><i class="fas fa-user-tie text-primary ms-2"></i><?php echo escape($m->full_name); ?></td>
                                <td><small class="text-muted"><i class="fas fa-envelope me-1"></i><?php echo escape($m->email); ?></small></td>
                                <td><span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2"><i class="fas fa-university ms-1"></i><?php echo escape($m->major_name ?? 'غير محدد'); ?></span></td>
                                <td><span class="badge bg-info bg-opacity-10 text-info rounded-pill px-3 py-2"><i class="fas fa-layer-group ms-1"></i><?php echo escape($m->level_name ?? 'غير محدد'); ?></span></td>
                                <td>
                                    <?php if ($m->is_active): ?>
                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2"><i class="fas fa-check-circle ms-1"></i>نشط</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3 py-2"><i class="fas fa-times-circle ms-1"></i>غير نشط</span>
                                    <?php endif; ?>
                                </td>
                                <td><small class="text-muted"><?php echo $m->last_login ? escape(format_date($m->last_login, 'Y-m-d H:i')) : '—'; ?></small></td>
                                <td>
                                    <a href="<?php echo url('/admin/managers/' . escape($m->id) . '/edit'); ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="<?php echo url('/admin/managers/' . escape($m->id) . '/delete'); ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('حذف المندوب؟')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>
