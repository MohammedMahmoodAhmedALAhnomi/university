<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <h3><i class="fas fa-file ms-2"></i>إدارة الملفات</h3>
    <a href="<?php echo url('/admin/files/create'); ?>" class="btn btn-primary">
        <i class="fas fa-plus ms-1"></i>إضافة ملف
    </a>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-body py-3">
        <form method="GET" action="<?php echo url('/admin/files'); ?>" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label small">المادة</label>
                <select name="course_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">جميع المواد</option>
                    <?php foreach ($courses ?? [] as $c): ?>
                        <option value="<?php echo $c->id; ?>" <?php echo (isset($_GET['course_id']) && $_GET['course_id'] == $c->id) ? 'selected' : ''; ?>>
                            <?php echo escape($c->name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php if (isset($_GET['course_id']) && $_GET['course_id']): ?>
                <div class="col-md-2">
                    <a href="<?php echo url('/admin/files'); ?>" class="btn btn-outline-secondary btn-sm">إلغاء الفلتر</a>
                </div>
            <?php endif; ?>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-primary">
                    <tr>
                        <th>العنوان</th>
                        <th>المادة</th>
                        <th>التصنيف</th>
                        <th>الحجم</th>
                        <th>التحميلات</th>
                        <th>الرافع</th>
                        <th>موافق عليه</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($files)): ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">لا توجد ملفات</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($files as $file): ?>
                            <tr>
                                <td class="fw-bold"><?php echo escape($file->title); ?></td>
                                <td><?php echo escape($file->course_name ?? ''); ?></td>
                                <td>
                                    <?php
                                    $typeLabels = ['lecture' => 'محاضرة', 'summary' => 'ملخص', 'model' => 'نموذج', 'exam' => 'اختبار', 'other' => 'أخرى'];
                                    $typeColors = ['lecture' => 'bg-info', 'summary' => 'bg-success', 'model' => 'bg-warning text-dark', 'exam' => 'bg-danger', 'other' => 'bg-secondary'];
                                    $label = $typeLabels[$file->file_type ?? 'other'] ?? 'أخرى';
                                    $color = $typeColors[$file->file_type ?? 'other'] ?? 'bg-secondary';
                                    ?>
                                    <span class="badge <?php echo $color; ?>"><?php echo escape($label); ?></span>
                                    <small class="text-muted d-block"><?php echo escape(strtoupper($file->file_extension ?? '')); ?></small>
                                </td>
                                <td><?php echo escape($file->file_size ?? ''); ?></td>
                                <td><span class="badge bg-secondary"><?php echo escape($file->download_count ?? 0); ?></span></td>
                                <td><?php echo escape($file->uploader_name ?? ''); ?></td>
                                <td>
                                    <?php if ($file->is_approved): ?>
                                        <span class="badge bg-success">نعم</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark">لا</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="<?php echo url('/admin/files/' . escape($file->id) . '/edit'); ?>" class="btn btn-warning btn-sm rounded-pill px-3">
                                            <i class="fas fa-edit ms-1"></i>تعديل
                                        </a>
                                        <a href="<?php echo url('/admin/files/' . escape($file->id) . '/delete'); ?>" class="btn btn-danger btn-sm rounded-pill px-3" onclick="return confirm('هل أنت متأكد من الحذف؟')">
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