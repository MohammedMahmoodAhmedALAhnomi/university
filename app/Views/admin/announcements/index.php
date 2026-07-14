<div class="d-flex justify-content-between align-items-center mb-4">
    <h3><i class="fas fa-bullhorn ms-2"></i>إدارة الإعلانات</h3>
    <a href="<?php echo url('/admin/announcements/create'); ?>" class="btn btn-primary">
        <i class="fas fa-plus ms-1"></i>إضافة إعلان
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-primary">
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
                                <td class="fw-bold"><?php echo escape($announcement->title); ?></td>
                                <td><?php echo escape($announcement->creator_name ?? ''); ?></td>
                                <td>
                                    <?php if ($announcement->is_pinned): ?>
                                        <span class="badge bg-warning text-dark">مثبت</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($announcement->is_active): ?>
                                        <span class="badge bg-success">نشط</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">غير نشط</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo $announcement->starts_at ? escape(format_date($announcement->starts_at, 'Y-m-d')) : '-'; ?></td>
                                <td><?php echo $announcement->expires_at ? escape(format_date($announcement->expires_at, 'Y-m-d')) : '-'; ?></td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="<?php echo url('/admin/announcements/' . escape($announcement->id) . '/edit'); ?>" class="btn btn-warning btn-sm rounded-pill px-3">
                                            <i class="fas fa-edit ms-1"></i>تعديل
                                        </a>
                                        <a href="<?php echo url('/admin/announcements/' . escape($announcement->id) . '/delete'); ?>" class="btn btn-danger btn-sm rounded-pill px-3" onclick="return confirm('هل أنت متأكد من الحذف؟')">
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