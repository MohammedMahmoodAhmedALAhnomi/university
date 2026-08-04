<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="fas fa-users ms-2 text-primary"></i>إدارة المسؤولين</h4>
        <p class="text-muted small mb-0">إدارة حسابات مسؤولين النظام والمناديب</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?php echo url('/admin/users/create'); ?>" class="btn btn-primary rounded-pill px-3 shadow-sm">
            <i class="fas fa-plus ms-1"></i>إضافة مسؤول
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between py-3">
        <h6 class="fw-bold mb-0"><i class="fas fa-users text-primary ms-1"></i>قائمة المسؤولين</h6>
        <?php if (!empty($users)): ?>
            <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3"><?php echo count($users); ?> مسؤول</span>
        <?php endif; ?>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>الاسم</th>
                        <th>البريد الإلكتروني</th>
                        <th>الدور</th>
                        <th>التخصص</th>
                        <th>المستوى</th>
                        <th>الحالة</th>
                        <th>آخر تسجيل دخول</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">لا يوجد مسؤولين</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td class="ps-3 text-muted fw-bold">#<?php echo escape($user->id); ?></td>
                                <td class="fw-bold text-dark"><i class="fas fa-user-shield text-primary ms-2"></i><?php echo escape($user->full_name ?? $user->name ?? ''); ?></td>
                                <td><small class="text-muted"><i class="fas fa-envelope me-1"></i><?php echo escape($user->email); ?></small></td>
                                <td>
                                    <?php $role = $user->role ?? ''; ?>
                                    <?php if ($role === 'admin'): ?>
                                        <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3 py-2"><i class="fas fa-crown ms-1"></i>مدير النظام</span>
                                    <?php elseif ($role === 'major_admin'): ?>
                                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2"><i class="fas fa-user-shield ms-1"></i>مسؤول تخصص</span>
                                    <?php else: ?>
                                        <span class="badge bg-info bg-opacity-10 text-info rounded-pill px-3 py-2"><i class="fas fa-user-tie ms-1"></i>مندوب مستوى</span>
                                    <?php endif; ?>
                                </td>
                                <td><span class="badge bg-info bg-opacity-10 text-info rounded-pill px-3 py-2"><i class="fas fa-university ms-1"></i><?php echo escape($user->major_name ?? 'الكل'); ?></span></td>
                                <td>
                                    <?php if (!empty($user->level_name)): ?>
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3 py-2"><i class="fas fa-layer-group ms-1"></i><?php echo escape($user->level_name); ?></span>
                                    <?php else: ?>
                                        <span class="text-muted small">كل المستويات</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($user->is_active): ?>
                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2"><i class="fas fa-check-circle ms-1"></i>نشط</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3 py-2"><i class="fas fa-times-circle ms-1"></i>غير نشط</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo $user->last_login ? escape(format_date($user->last_login, 'Y-m-d H:i')) : '-'; ?></td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="<?php echo url('/admin/users/' . escape($user->id) . '/edit'); ?>" class="btn btn-warning btn-sm rounded-pill px-3">
                                            <i class="fas fa-edit ms-1"></i>تعديل
                                        </a>
                                        <a href="<?php echo url('/admin/users/' . escape($user->id) . '/delete?_csrf_token=' . csrf_token()); ?>" class="btn btn-danger btn-sm rounded-pill px-3" onclick="return confirm('هل أنت متأكد من الحذف؟')">
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