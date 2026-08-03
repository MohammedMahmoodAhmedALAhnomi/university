<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-1"><i class="fas fa-calendar-alt text-primary me-2"></i>إدارة جداول المحاضرات والامتحانات</h4>
        <p class="text-muted small mb-0">إضافة وتعديل مواعيد المحاضرات الأسبوعية ومواعيد الاختبارات واستيراد الجداول من Excel.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?php echo url('/admin/schedule/import'); ?>" class="btn btn-success rounded-pill px-4 shadow-sm fw-bold">
            <i class="fas fa-file-excel me-1"></i> استيراد من Excel
        </a>
        <a href="<?php echo url('/admin/schedule/create'); ?>" class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold">
            <i class="fas fa-plus me-1"></i> إضافة موعد جديد
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">التخصص والمستوى</th>
                        <th>النوع</th>
                        <th>اسم المادة والعنوان</th>
                        <th>التوقيت / الملف</th>
                        <th>القاعة والدكتور / التفاصيل</th>
                        <th class="pe-4 text-end">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($schedules)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fas fa-inbox display-4 mb-3 opacity-50 d-block"></i>
                                لا توجد عناصر مسجلة في الجداول حتى الآن.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($schedules as $s): ?>
                            <tr>
                                <td class="ps-4">
                                    <span class="fw-bold text-dark d-block"><?php echo escape($s->major_name); ?></span>
                                    <small class="badge bg-light text-secondary border"><?php echo escape($s->level_name); ?></small>
                                    <?php if (!empty($s->section_code)): ?>
                                        <span class="badge bg-dark text-warning border ms-1"><i class="fas fa-layer-group me-1"></i><?php echo escape($s->section_code); ?></span>
                                    <?php endif; ?>
                                    <?php if (!empty($s->group_name) || !empty($s->sub_group_name)): ?>
                                        <small class="text-muted d-block mt-1">
                                            <i class="fas fa-users me-1 text-primary"></i><?php echo escape($s->group_name); ?>
                                            <?php if (!empty($s->sub_group_name)): ?>
                                                <span class="badge bg-light text-dark border ms-1"><i class="fas fa-flask text-info me-1"></i><?php echo escape($s->sub_group_name); ?></span>
                                            <?php endif; ?>
                                        </small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($s->type === 'exam'): ?>
                                        <span class="badge bg-danger px-3 py-1 rounded-pill"><i class="fas fa-exclamation-circle me-1"></i>امتحان</span>
                                    <?php elseif ($s->type === 'pdf_file'): ?>
                                        <span class="badge bg-info text-dark px-3 py-1 rounded-pill"><i class="fas fa-file-pdf me-1"></i>ملف جدول</span>
                                    <?php else: ?>
                                        <span class="badge bg-primary px-3 py-1 rounded-pill"><i class="fas fa-chalkboard-teacher me-1"></i>محاضرة</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong class="text-dark d-block"><?php echo escape($s->subject_name ?? 'جدول مرفق'); ?></strong>
                                    <span class="small text-muted"><?php echo escape($s->title); ?></span>
                                </td>
                                <td>
                                    <?php if (!empty($s->file_path)): ?>
                                        <a href="<?php echo url('/' . escape($s->file_path)); ?>" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                            <i class="fas fa-download me-1"></i> عرض / تحميل الملف
                                        </a>
                                    <?php elseif ($s->type === 'exam'): ?>
                                        <div class="text-danger fw-bold small"><i class="fas fa-clock me-1"></i><?php echo format_date($s->exam_date, 'Y-m-d H:i'); ?></div>
                                    <?php else: ?>
                                        <div class="small"><i class="fas fa-calendar-day me-1"></i><?php echo escape($s->day_of_week); ?></div>
                                        <div class="small text-muted"><?php echo format_date($s->start_time, 'H:i'); ?> - <?php echo format_date($s->end_time, 'H:i'); ?></div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($s->notes)): ?>
                                        <div class="small text-muted"><i class="fas fa-info-circle me-1"></i><?php echo escape($s->notes); ?></div>
                                    <?php else: ?>
                                        <div class="small"><i class="fas fa-user-tie me-1 text-secondary"></i><?php echo escape($s->doctor_name ?? '-'); ?></div>
                                        <div class="small text-muted"><i class="fas fa-door-open me-1 text-secondary"></i><?php echo escape($s->location_hall ?? '-'); ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="pe-4 text-end">
                                    <a href="<?php echo url('/admin/schedule/' . $s->id . '/delete'); ?>" class="btn btn-outline-danger btn-sm rounded-circle p-2" title="حذف" onclick="return confirm('هل انت متأكد من حذف هذا العنصر؟')">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
