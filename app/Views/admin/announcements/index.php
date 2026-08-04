<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="fas fa-bullhorn ms-2 text-primary"></i>إدارة الإعلانات</h4>
        <p class="text-muted small mb-0">نشر وإدارة الإعلانات والأخبار للطلاب</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?php echo url('/admin/announcements/create'); ?>" class="btn btn-primary rounded-pill px-3 shadow-sm">
            <i class="fas fa-plus ms-1"></i>إضافة إعلان
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between py-3">
        <h6 class="fw-bold mb-0"><i class="fas fa-bullhorn text-primary ms-1"></i>قائمة الإعلانات</h6>
        <?php if (!empty($announcements)): ?>
            <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3"><?php echo count($announcements); ?> إعلان</span>
        <?php endif; ?>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>العنوان</th>
                        <th>المنشئ</th>
                        <th>مثبت</th>
                        <th>الحالة</th>
                        <th>تاريخ البدء</th>
                        <th>تاريخ الانتهاء</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($announcements)): ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">لا توجد إعلانات</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($announcements as $announcement): ?>
                            <tr>
                                <td><?php echo escape($announcement->id); ?></td>
                                <td class="fw-bold text-dark"><i class="fas fa-bullhorn text-warning ms-2"></i><?php echo escape($announcement->title); ?></td>
                                <td><span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2"><i class="fas fa-user-edit ms-1"></i><?php echo escape($announcement->creator_name ?? '-'); ?></span></td>
                                <td>
                                    <?php if ($announcement->is_pinned): ?>
                                        <span class="badge bg-warning bg-opacity-10 text-dark rounded-pill px-3 py-2"><i class="fas fa-thumbtack text-warning ms-1"></i>مثبت</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary bg-opacity-10 text-muted rounded-pill px-3 py-2">عادي</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($announcement->is_active): ?>
                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2"><i class="fas fa-check-circle ms-1"></i>نشط</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3 py-2"><i class="fas fa-times-circle ms-1"></i>غير نشط</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo $announcement->starts_at ? escape(format_date($announcement->starts_at, 'Y-m-d')) : '-'; ?></td>
                                <td><?php echo $announcement->expires_at ? escape(format_date($announcement->expires_at, 'Y-m-d')) : '-'; ?></td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="<?php echo url('/admin/announcements/' . escape($announcement->id) . '/edit'); ?>" class="btn btn-warning btn-sm rounded-pill px-3">
                                            <i class="fas fa-edit ms-1"></i>تعديل
                                        </a>
                                        <a href="<?php echo url('/admin/announcements/' . escape($announcement->id) . '/delete?_csrf_token=' . csrf_token()); ?>" class="btn btn-danger btn-sm rounded-pill px-3" onclick="return confirm('هل أنت متأكد من الحذف؟')">
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