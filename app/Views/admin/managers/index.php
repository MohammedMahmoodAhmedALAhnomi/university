<div class="d-flex justify-content-between align-items-center mb-4">
    <h3><i class="fas fa-user-tie ms-2"></i>المندوبين</h3>
    <a href="<?php echo url('/admin/managers/create'); ?>" class="btn btn-primary">
        <i class="fas fa-plus-circle ms-1"></i>إضافة مندوب
    </a>
</div>

<?php if (empty($managers)): ?>
    <div class="card shadow-sm">
        <div class="card-body text-center text-muted py-5">
            <i class="fas fa-user-tie fa-4x mb-3"></i>
            <p>لا يوجد مندوبين بعد</p>
        </div>
    </div>
<?php else: ?>
    <div class="card shadow-sm">
        <div class="card-body">
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
                                <td><i class="fas fa-user ms-1"></i><?php echo escape($m->full_name); ?></td>
                                <td><?php echo escape($m->email); ?></td>
                                <td><span class="badge bg-primary"><?php echo escape($m->major_name ?? 'غير محدد'); ?></span></td>
                                <td><span class="badge bg-info"><?php echo escape($m->level_name ?? 'غير محدد'); ?></span></td>
                                <td>
                                    <?php if ($m->is_active): ?>
                                        <span class="badge bg-success">نشط</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">غير نشط</span>
                                    <?php endif; ?>
                                </td>
                                <td><small class="text-muted"><?php echo $m->last_login ? escape(format_date($m->last_login, 'Y-m-d H:i')) : '—'; ?></small></td>
                                <td>
                                    <a href="<?php echo url('/admin/managers/' . $m->id . '/edit'); ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="<?php echo url('/admin/managers/' . $m->id . '/delete'); ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('حذف المندوب؟')">
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
