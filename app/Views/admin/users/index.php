<div class="d-flex justify-content-between align-items-center mb-4">
    <h3><i class="fas fa-users ms-2"></i>إدارة المستخدمين</h3>
    <a href="<?php echo url('/admin/users/create'); ?>" class="btn btn-primary">
        <i class="fas fa-plus ms-1"></i>إضافة مستخدم
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
                        <th>البريد الإلكتروني</th>
                        <th>الدور</th>
                        <th>التخصص</th>
                        <th>الحالة</th>
                        <th>آخر تسجيل دخول</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">لا توجد مستخدمين</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo escape($user->id); ?></td>
                                <td class="fw-bold"><?php echo escape($user->full_name ?? $user->name ?? ''); ?></td>
                                <td><?php echo escape($user->email); ?></td>
                                <td>
                                    <?php $role = $user->role ?? ''; ?>
                                    <?php if ($role === 'admin'): ?>
                                        <span class="badge bg-danger">مشرف</span>
                                    <?php elseif ($role === 'manager'): ?>
                                        <span class="badge bg-warning text-dark">مسؤول</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">مستخدم</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo escape($user->major_name ?? '-'); ?></td>
                                <td>
                                    <?php if ($user->is_active): ?>
                                        <span class="badge bg-success">نشط</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">غير نشط</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo $user->last_login ? escape(format_date($user->last_login, 'Y-m-d H:i')) : '-'; ?></td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="<?php echo url('/admin/users/' . escape($user->id) . '/edit'); ?>" class="btn btn-warning btn-sm rounded-pill px-3">
                                            <i class="fas fa-edit ms-1"></i>تعديل
                                        </a>
                                        <a href="<?php echo url('/admin/users/' . escape($user->id) . '/delete'); ?>" class="btn btn-danger btn-sm rounded-pill px-3" onclick="return confirm('هل أنت متأكد من الحذف؟')">
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