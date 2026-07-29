<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="fas fa-file ms-2 text-primary"></i>إدارة الملفات</h4>
        <p class="text-muted small mb-0">إدارة الملفات والمستندات التعليمية</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?php echo url('/admin/files/create'); ?>" class="btn btn-primary rounded-pill px-3 shadow-sm">
            <i class="fas fa-plus ms-1"></i>إضافة ملف
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
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
                    <a href="<?php echo url('/admin/files'); ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3">إلغاء الفلتر</a>
                </div>
            <?php endif; ?>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between py-3">
        <h6 class="fw-bold mb-0"><i class="fas fa-file text-primary ms-1"></i>قائمة الملفات</h6>
        <?php if (!empty($files)): ?>
            <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3"><?php echo count($files); ?> ملف</span>
        <?php endif; ?>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>العنوان</th>
                        <th>المادة</th>
                        <th>الدكتور</th>
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
                                <td colspan="9" class="text-center text-muted py-4">لا توجد ملفات</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($files as $file): ?>
                            <tr>
                                <td class="fw-bold"><?php echo escape($file->title); ?></td>
                                <td><?php echo escape($file->course_name ?? ''); ?></td>
                                <td><?php echo escape($file->doctor_name ?? '-'); ?></td>
                                <td>
                                    <?php
                                    $typeLabels = ['lecture' => 'محاضرة', 'summary' => 'ملخص', 'model' => 'نماذج أسئلة', 'reference' => 'مرجع'];
                                    $typeIcons = ['lecture' => 'fas fa-chalkboard-teacher', 'summary' => 'fas fa-file-alt', 'model' => 'fas fa-tasks', 'reference' => 'fas fa-atlas'];
                                    $typeColors = [
                                        'lecture' => 'bg-primary bg-opacity-10 text-primary',
                                        'summary' => 'bg-success bg-opacity-10 text-success',
                                        'model' => 'bg-warning bg-opacity-10 text-dark',
                                        'reference' => 'bg-purple bg-opacity-10 text-purple'
                                    ];
                                    $fileType = $file->file_type ?? 'lecture';
                                    $label = $typeLabels[$fileType] ?? 'محاضرة';
                                    $icon = $typeIcons[$fileType] ?? 'fas fa-file-alt';
                                    $color = $typeColors[$fileType] ?? 'bg-primary bg-opacity-10 text-primary';
                                    ?>
                                    <span class="badge <?php echo $color; ?> rounded-pill px-3 py-2">
                                        <i class="<?php echo $icon; ?> ms-1"></i><?php echo escape($label); ?>
                                    </span>
                                    <small class="text-muted d-block mt-1 fw-bold"><?php echo escape(strtoupper($file->file_extension ?? '')); ?></small>
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
                                        <a href="<?php echo url('/admin/files/' . escape($file->id) . '/delete?_csrf_token=' . csrf_token()); ?>" class="btn btn-danger btn-sm rounded-pill px-3" onclick="return confirm('هل أنت متأكد من الحذف؟')">
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