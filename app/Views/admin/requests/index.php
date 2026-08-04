<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1"><i class="fas fa-user-check me-2 text-primary"></i>طلبات الانضمام وتحديد الصلاحيات</h3>
        <p class="text-muted small mb-0">مراجعة واعتتماد طلبات المندوبين ومسؤولي التخصصات</p>
    </div>
</div>

<?php if (flash_has('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle ms-1"></i><?php echo flash('success'); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (flash_has('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle ms-1"></i><?php echo flash('error'); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card shadow-sm border-0 rounded-4 overflow-hidden">
    <div class="card-body p-0">
        <?php if (empty($requests)): ?>
            <div class="text-center py-5">
                <i class="fas fa-clipboard-check fa-4x text-muted mb-3"></i>
                <h5 class="fw-bold text-muted">لا توجد طلبات انضمام حالياً</h5>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">المستخدم</th>
                            <th>البريد الإلكتروني</th>
                            <th>نوع الحساب المطلوب</th>
                            <th>التخصص</th>
                            <th>المستوى</th>
                            <th>تاريخ الطلب</th>
                            <th>الحالة</th>
                            <th class="text-end pe-4">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($requests as $req): ?>
                            <tr>
                                <td class="ps-4 fw-bold text-dark">
                                    <i class="fas fa-user-circle me-1 text-primary"></i>
                                    <?php echo escape($req->user_name); ?>
                                </td>
                                <td><?php echo escape($req->user_email); ?></td>
                                <td>
                                    <?php if ($req->account_type === 'representative'): ?>
                                        <span class="badge bg-info text-dark rounded-pill px-3 py-2">
                                            <i class="fas fa-user-graduate me-1"></i>مندوب مستوى
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-primary text-white rounded-pill px-3 py-2">
                                            <i class="fas fa-user-shield me-1"></i>مسؤول تخصص
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="fw-bold text-dark"><?php echo escape($req->major_name); ?></span>
                                </td>
                                <td>
                                    <?php if ($req->account_type === 'representative' && !empty($req->level_name)): ?>
                                        <span class="badge bg-light text-dark border rounded-pill px-3 py-1"><?php echo escape($req->level_name); ?></span>
                                    <?php else: ?>
                                        <span class="text-muted small">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-muted small">
                                    <?php echo escape(format_date($req->created_at, 'Y-m-d H:i')); ?>
                                </td>
                                <td>
                                    <?php if ($req->status === 'pending'): ?>
                                        <span class="badge bg-warning text-dark rounded-pill px-3 py-1">
                                            <i class="fas fa-hourglass-half me-1"></i>بانتظار الموافقة
                                        </span>
                                    <?php elseif ($req->status === 'approved'): ?>
                                        <span class="badge bg-success text-white rounded-pill px-3 py-1">
                                            <i class="fas fa-check-circle me-1"></i>موافق عليه
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-danger text-white rounded-pill px-3 py-1">
                                            <i class="fas fa-times-circle me-1"></i>مرفوض
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end pe-4">
                                    <?php if ($req->status === 'pending'): ?>
                                        <a href="<?php echo url('/admin/requests/' . $req->id . '/approve?_csrf_token=' . csrf_token()); ?>" class="btn btn-success btn-sm rounded-pill px-3 me-1" onclick="return confirm('هل أنت تأكد من الموافقة على الطلب ومنح الصلاحيات؟')">
                                            <i class="fas fa-check ms-1"></i>موافقة
                                        </a>
                                        <a href="<?php echo url('/admin/requests/' . $req->id . '/reject?_csrf_token=' . csrf_token()); ?>" class="btn btn-outline-danger btn-sm rounded-pill px-3" onclick="return confirm('هل أنت تأكد من رفض هذا الطلب؟')">
                                            <i class="fas fa-times ms-1"></i>رفض
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted small">مكتمل</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
