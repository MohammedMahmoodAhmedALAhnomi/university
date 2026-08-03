<div class="container-fluid px-md-5 py-4">
    <!-- Header & Filter Banner (No Print) -->
    <div class="card border-0 shadow-lg rounded-4 mb-4 text-white overflow-hidden no-print" style="background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #312e81 100%);">
        <div class="card-body p-4 p-md-5 position-relative">
            <div class="row align-items-center g-4">
                <div class="col-lg-6">
                    <div class="d-inline-flex align-items-center gap-2 px-3 py-1 bg-white bg-opacity-10 rounded-pill text-warning small fw-bold mb-3 border border-white border-opacity-10">
                        <i class="fas fa-sparkles"></i> المحرك الذكي لجداول الكلية الرسمية
                    </div>
                    <h2 class="fw-bold mb-2 display-6"><i class="fas fa-calendar-alt me-2 text-warning"></i>جدول المحاضرات والمعامل الرسمية</h2>
                    <p class="mb-0 text-white-50 leading-relaxed">
                        تصفية وعرض الجدول الأسبوعي الشبكي الرسمي المعتمد للتخصص والمستوى والقروب والمجاميع الفرعية.
                    </p>
                </div>

                <div class="col-lg-6">
                    <div class="p-3 bg-white bg-opacity-10 backdrop-blur rounded-4 border border-white border-opacity-20 shadow-sm">
                        <form method="GET" action="<?php echo url('/student/schedule'); ?>" class="row g-2">
                            <div class="col-12 col-md-4">
                                <label class="form-label small text-white-50 fw-bold mb-1"><i class="fas fa-graduation-cap me-1"></i> التخصص</label>
                                <select name="major_id" class="form-select form-select-sm rounded-3 fw-bold border-0 shadow-sm" onchange="this.form.submit()">
                                    <?php foreach ($majors as $m): ?>
                                        <option value="<?php echo $m->id; ?>" <?php echo $m->id == $selectedMajorId ? 'selected' : ''; ?>>
                                            <?php echo escape($m->name); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label small text-white-50 fw-bold mb-1"><i class="fas fa-layer-group me-1"></i> المستوى</label>
                                <select name="level_id" class="form-select form-select-sm rounded-3 fw-bold border-0 shadow-sm" onchange="this.form.submit()">
                                    <?php foreach ($levels as $l): ?>
                                        <option value="<?php echo $l->id; ?>" <?php echo $l->id == $selectedLevelId ? 'selected' : ''; ?>>
                                            <?php echo escape($l->name); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label small text-white-50 fw-bold mb-1"><i class="fas fa-users me-1"></i> القروب / العملي</label>
                                <select name="group_name" class="form-select form-select-sm rounded-3 fw-bold bg-warning text-dark border-0 shadow-sm" onchange="this.form.submit()">
                                    <option value="all" <?php echo $selectedGroup === 'all' ? 'selected' : ''; ?>>جميع القروبات والعملي</option>
                                    <?php foreach ($groups as $g): ?>
                                        <?php 
                                            $gVal = $g->section_code ?: ($g->sub_group_name ?: $g->group_name); 
                                            $gLabel = $g->section_code ? $g->section_code : $g->group_name;
                                            if (!empty($g->sub_group_name)) $gLabel .= ' (' . $g->sub_group_name . ')';
                                        ?>
                                        <option value="<?php echo escape($gVal); ?>" <?php echo $selectedGroup === $gVal ? 'selected' : ''; ?>>
                                            <?php echo escape($gLabel); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </form>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-3 no-print">
                        <button onclick="window.print()" class="btn btn-sm btn-light rounded-pill px-3 fw-bold shadow-sm">
                            <i class="fas fa-print me-1 text-primary"></i> طباعة / تصدير PDF
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- العداد التنازلي للاختبار القادم -->
    <?php if (!empty($upcomingExams)): ?>
        <?php $nextExam = $upcomingExams[0]; ?>
        <div class="card border-0 shadow-sm rounded-4 mb-4 bg-danger bg-gradient text-white no-print">
            <div class="card-body p-4 text-center position-relative overflow-hidden">
                <div class="position-relative z-1">
                    <span class="badge bg-white text-danger px-3 py-1 rounded-pill fw-bold mb-2 shadow-sm">
                        <i class="fas fa-exclamation-triangle me-1"></i> الاختبار القادم
                    </span>
                    <h4 class="fw-bold mb-1"><?php echo escape($nextExam->subject_name); ?> - <?php echo escape($nextExam->title); ?></h4>
                    <p class="mb-3 opacity-90 small">
                        <i class="fas fa-calendar me-1"></i> <?php echo format_date($nextExam->exam_date, 'Y-m-d H:i'); ?>
                        <?php if (!empty($nextExam->location_hall)): ?>
                            | <i class="fas fa-map-marker-alt me-1"></i> القاعة: <?php echo escape($nextExam->location_hall); ?>
                        <?php endif; ?>
                    </p>

                    <!-- عداد تنازلي حي -->
                    <div id="examCountdown" class="d-flex justify-content-center gap-3 text-center my-2" data-target-date="<?php echo $nextExam->exam_date; ?>">
                        <div class="bg-white text-dark rounded-3 px-3 py-2 shadow-sm min-w-70">
                            <span id="cd-days" class="display-6 fw-bold text-danger d-block">00</span>
                            <small class="text-muted fw-bold">أيام</small>
                        </div>
                        <div class="bg-white text-dark rounded-3 px-3 py-2 shadow-sm min-w-70">
                            <span id="cd-hours" class="display-6 fw-bold text-danger d-block">00</span>
                            <small class="text-muted fw-bold">ساعات</small>
                        </div>
                        <div class="bg-white text-dark rounded-3 px-3 py-2 shadow-sm min-w-70">
                            <span id="cd-minutes" class="display-6 fw-bold text-danger d-block">00</span>
                            <small class="text-muted fw-bold">دقائق</small>
                        </div>
                        <div class="bg-white text-dark rounded-3 px-3 py-2 shadow-sm min-w-70">
                            <span id="cd-seconds" class="display-6 fw-bold text-danger d-block">00</span>
                            <small class="text-muted fw-bold">ثواني</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- تبويبات العرض وسويتش طريقة العرض الشبكية الرسمية -->
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-3 no-print gap-2">
        <ul class="nav nav-pills gap-2 bg-light p-2 rounded-4 shadow-sm" id="scheduleTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active rounded-3 fw-bold py-2 px-3" id="matrix-tab" data-bs-toggle="pill" data-bs-target="#matrixGrid" type="button" role="tab">
                    <i class="fas fa-th me-2"></i> الجدول الشبكي الرسمي للجامعة
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link rounded-3 fw-bold py-2 px-3" id="lectures-tab" data-bs-toggle="pill" data-bs-target="#lectures" type="button" role="tab">
                    <i class="fas fa-grip-horizontal me-2"></i> عرض البطاقات والتفاصيل
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link rounded-3 fw-bold py-2 px-3" id="exams-tab" data-bs-toggle="pill" data-bs-target="#exams" type="button" role="tab">
                    <i class="fas fa-file-signature me-2"></i> جدول الامتحانات (<?php echo count($exams); ?>)
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link rounded-3 fw-bold py-2 px-3" id="files-tab" data-bs-toggle="pill" data-bs-target="#files" type="button" role="tab">
                    <i class="fas fa-file-pdf me-2"></i> ملفات الجداول (<?php echo count($pdfSchedules); ?>)
                </button>
            </li>
        </ul>
    </div>

    <?php
        // Prepare Matrix Grid Data (5 Time Slots x 6 Days)
        $matrixDays = [
            'Saturday'  => 'السبت',
            'Sunday'    => 'احد',
            'Monday'    => 'اثنين',
            'Tuesday'   => 'ثلاثاء',
            'Wednesday' => 'اربعاء',
            'Thursday'  => 'الخميس'
        ];

        $matrixPeriods = [
            'P1' => ['num' => 'الأولى', 'time' => '8:00 - 10:00'],
            'P2' => ['num' => 'الثانية', 'time' => '10:00 - 12:00'],
            'P3' => ['num' => 'الثالثة', 'time' => '12:00 - 14:00'],
            'P4' => ['num' => 'الرابعة', 'time' => '14:00 - 16:00'],
            'P5' => ['num' => 'الخامسة', 'time' => '16:00 - 18:00'],
        ];

        $grid = [];
        foreach ($lectures as $lec) {
            $dayKey = $lec->day_of_week;
            $startHour = (int)date('H', strtotime($lec->start_time ?: '08:00'));
            
            $pKey = 'P1';
            if ($startHour >= 8 && $startHour < 10) $pKey = 'P1';
            elseif ($startHour >= 10 && $startHour < 12) $pKey = 'P2';
            elseif ($startHour >= 12 && $startHour < 14) $pKey = 'P3';
            elseif ($startHour >= 14 && $startHour < 16) $pKey = 'P4';
            elseif ($startHour >= 16) $pKey = 'P5';

            $grid[$dayKey][$pKey][] = $lec;
        }

        // Active Major & Level Names
        $curMajorName = 'تكنولوجيا المعلومات';
        foreach ($majors as $m) {
            if ($m->id == $selectedMajorId) {
                $curMajorName = $m->name;
                break;
            }
        }
        $curLevelName = 'مستوى أول';
        foreach ($levels as $l) {
            if ($l->id == $selectedLevelId) {
                $curLevelName = $l->name;
                break;
            }
        }

        // Active Section Code or Group
        $activeCode = 'IT1_G';
        if (!empty($lectures[0]->section_code)) {
            $activeCode = $lectures[0]->section_code;
        }
    ?>

    <div class="tab-content" id="scheduleTabsContent">
        <!-- 1. الجدول الشبكي الرسمي للجامعة (Official Matrix Table Grid) -->
        <div class="tab-pane fade show active" id="matrixGrid" role="tabpanel">
            <div class="card border border-dark rounded-0 shadow-sm p-3 p-md-4 bg-white printable-table-card">
                
                <!-- ترويسة الجدول الرسمية المعتمدة للجامعة -->
                <div class="text-center mb-3">
                    <h5 class="fw-bold text-dark mb-1" style="font-size: 1.15rem; font-family: 'Cairo', sans-serif;">
                        جدول الفصل الدراسي الأول للعام الجامعي 1448هـ _ 2026/2027م كلية الحاسوب وتكنولوجيا المعلومات-جامعة صنعاء
                    </h5>
                    <h3 class="fw-bold text-dark mb-1" style="letter-spacing: 2px; font-family: monospace;">
                        `<?php echo escape($activeCode); ?>`
                    </h3>
                    <div class="fw-semibold text-secondary small">
                        <?php echo escape($curMajorName); ?> - <?php echo escape($curLevelName); ?> <?php echo $selectedGroup !== 'all' ? '- (' . escape($selectedGroup) . ')' : ''; ?>
                    </div>
                </div>

                <!-- الجدول الشبكي الممتد -->
                <div class="table-responsive">
                    <table class="table table-bordered border-dark text-center align-middle mb-0 official-schedule-matrix" style="border-width: 1px; font-size: 13px;">
                        <thead>
                            <tr class="table-light border-dark">
                                <th style="width: 10%; font-size: 14px;" class="fw-bold border-dark">اليوم \ الفترة</th>
                                <?php foreach ($matrixPeriods as $pKey => $pInfo): ?>
                                    <th style="width: 18%;" class="border-dark py-2">
                                        <div class="fw-bold text-dark" style="font-size: 14px;"><?php echo $pInfo['num']; ?></div>
                                        <div class="small dir-ltr fw-semibold text-muted" style="font-size: 11px;"><?php echo $pInfo['time']; ?></div>
                                    </th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($matrixDays as $dayEng => $dayAr): ?>
                                <tr>
                                    <!-- العمود الأول: اسم اليوم -->
                                    <th class="fw-bold bg-light text-dark border-dark py-3" style="font-size: 14px;">
                                        <?php echo $dayAr; ?>
                                    </th>

                                    <!-- أعمدة الفترات الزمنية 5 فترات -->
                                    <?php foreach ($matrixPeriods as $pKey => $pInfo): ?>
                                        <?php 
                                            $cellItems = $grid[$dayEng][$pKey] ?? []; 
                                        ?>
                                        <td class="border-dark align-top p-1 position-relative" style="height: 90px; min-width: 140px;">
                                            <?php if (empty($cellItems)): ?>
                                                &nbsp;
                                            <?php else: ?>
                                                <?php foreach ($cellItems as $cIdx => $item): ?>
                                                    <div class="schedule-cell-box p-1 text-center <?php echo $cIdx > 0 ? 'border-top border-secondary pt-2 mt-2' : ''; ?>">
                                                        
                                                        <!-- رمز المجموعة أو العملي إن وجد -->
                                                        <?php if (!empty($item->section_code) || !empty($item->sub_group_name)): ?>
                                                            <div class="small fw-bold text-uppercase text-muted" style="font-size: 10px; line-height: 1.1;">
                                                                <?php echo escape($item->section_code ?: $item->sub_group_name); ?>
                                                            </div>
                                                        <?php endif; ?>

                                                        <!-- اسم المادة -->
                                                        <div class="fw-bold text-dark text-wrap mb-1" style="font-size: 12px; line-height: 1.2;">
                                                            <?php echo escape($item->subject_name); ?>
                                                        </div>

                                                        <!-- نوع المقرر + الكلية -->
                                                        <?php if (!empty($item->title)): ?>
                                                            <div class="text-secondary" style="font-size: 10px; line-height: 1.1;">
                                                                <?php echo escape($item->title); ?>
                                                            </div>
                                                        <?php endif; ?>

                                                        <!-- اسم الدكتور -->
                                                        <?php if (!empty($item->doctor_name)): ?>
                                                            <div class="fw-semibold text-dark mt-1" style="font-size: 11px; line-height: 1.2;">
                                                                <?php echo escape($item->doctor_name); ?>
                                                            </div>
                                                        <?php endif; ?>

                                                        <!-- القاعة أو المعمل -->
                                                        <?php if (!empty($item->location_hall)): ?>
                                                            <div class="text-muted small" style="font-size: 10px;">
                                                                <?php echo escape($item->location_hall); ?>
                                                            </div>
                                                        <?php endif; ?>

                                                    </div>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

        <!-- 2. تبويب المحاضرات (عرض البطاقات والتفاصيل) -->
        <div class="tab-pane fade" id="lectures" role="tabpanel">
            <?php if (empty($lectures)): ?>
                <div class="card border-0 shadow-sm rounded-4 text-center p-5">
                    <i class="fas fa-calendar-times display-1 text-muted opacity-50 mb-3"></i>
                    <h5 class="fw-bold text-secondary">لا يوجد جدول محاضرات مضاف لهذا التخصص والمستوى والقروب حالياً</h5>
                </div>
            <?php else: ?>
                <div class="row g-3">
                    <?php 
                    $daysArabic = [
                        'Sunday' => 'الأحد', 'Monday' => 'الإثنين', 'Tuesday' => 'الثلاثاء',
                        'Wednesday' => 'الأربعاء', 'Thursday' => 'الخميس', 'Friday' => 'الجمعة', 'Saturday' => 'السبت'
                    ];
                    foreach ($lectures as $lec): 
                    ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="card border-0 shadow-sm rounded-4 h-100 border-start border-4 <?php echo !empty($lec->sub_group_name) ? 'border-info' : 'border-primary'; ?>">
                                <div class="card-body p-4">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div>
                                            <span class="badge bg-primary text-white px-2 py-1 rounded-3">
                                                <i class="fas fa-calendar-day me-1"></i> <?php echo $daysArabic[$lec->day_of_week] ?? $lec->day_of_week; ?>
                                            </span>
                                            <?php if (!empty($lec->section_code)): ?>
                                                <span class="badge bg-dark text-warning border px-2 py-1 rounded-3 ms-1" title="رمز التخصص والمجموعة">
                                                    <i class="fas fa-layer-group me-1"></i><?php echo escape($lec->section_code); ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <small class="text-muted fw-bold">
                                            <i class="fas fa-clock me-1 text-warning"></i> <?php echo format_date($lec->start_time, 'H:i'); ?> - <?php echo format_date($lec->end_time, 'H:i'); ?>
                                        </small>
                                    </div>
                                    <h5 class="fw-bold text-dark mb-1"><?php echo escape($lec->subject_name); ?></h5>
                                    <p class="text-muted small mb-2">
                                        <?php echo escape($lec->title); ?>
                                        <?php if (!empty($lec->group_name)): ?>
                                            <span class="badge bg-light text-secondary border ms-1"><?php echo escape($lec->group_name); ?></span>
                                        <?php endif; ?>
                                        <?php if (!empty($lec->sub_group_name)): ?>
                                            <span class="badge bg-info text-dark border ms-1"><i class="fas fa-flask me-1"></i><?php echo escape($lec->sub_group_name); ?></span>
                                        <?php endif; ?>
                                    </p>
                                    <div class="small text-secondary pt-2 border-top">
                                        <?php if (!empty($lec->doctor_name)): ?>
                                            <div class="mb-1"><i class="fas fa-user-tie me-1 text-primary"></i> <strong>الدكتور:</strong> <?php echo escape($lec->doctor_name); ?></div>
                                        <?php endif; ?>
                                        <?php if (!empty($lec->location_hall)): ?>
                                            <div><i class="fas fa-map-marker-alt me-1 text-danger"></i> <strong>القاعة / المعمل:</strong> <?php echo escape($lec->location_hall); ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- 3. تبويب الامتحانات -->
        <div class="tab-pane fade" id="exams" role="tabpanel">
            <?php if (empty($exams)): ?>
                <div class="card border-0 shadow-sm rounded-4 text-center p-5">
                    <i class="fas fa-clipboard-check display-1 text-muted opacity-50 mb-3"></i>
                    <h5 class="fw-bold text-secondary">لا توجد مواعيد امتحانات مسجلة حالياً</h5>
                </div>
            <?php else: ?>
                <div class="table-responsive bg-white rounded-4 shadow-sm p-3">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>رمز الكود / القروب</th>
                                <th>اسم المادة</th>
                                <th>نوع الاختبار</th>
                                <th>التاريخ والوقت</th>
                                <th>القاعة / المكان</th>
                                <th>ملاحظات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($exams as $ex): ?>
                                <tr>
                                    <td>
                                        <span class="badge bg-dark text-warning border px-2 py-1 mb-1 d-inline-block">
                                            <i class="fas fa-layer-group me-1"></i><?php echo escape($ex->section_code ?: 'IT1_G1'); ?>
                                        </span>
                                        <?php if (!empty($ex->group_name)): ?>
                                            <br><small class="text-muted"><?php echo escape($ex->group_name); ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td class="fw-bold text-dark"><?php echo escape($ex->subject_name); ?></td>
                                    <td><span class="badge bg-warning text-dark px-3 py-1 rounded-pill"><?php echo escape($ex->title); ?></span></td>
                                    <td>
                                        <i class="fas fa-calendar-alt text-primary me-1"></i> <?php echo format_date($ex->exam_date, 'Y-m-d H:i'); ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border px-2 py-1"><i class="fas fa-door-open me-1"></i><?php echo escape($ex->location_hall ?? 'غير محدد'); ?></span>
                                    </td>
                                    <td class="small text-muted"><?php echo escape($ex->notes ?? '-'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <!-- 4. تبويب ملفات الجداول -->
        <div class="tab-pane fade" id="files" role="tabpanel">
            <?php if (empty($pdfSchedules)): ?>
                <div class="card border-0 shadow-sm rounded-4 text-center p-5">
                    <i class="fas fa-file-pdf display-1 text-muted opacity-50 mb-3"></i>
                    <h5 class="fw-bold text-secondary">لا توجد ملفات جداول مرفوعة حالياً</h5>
                </div>
            <?php else: ?>
                <div class="row g-3">
                    <?php foreach ($pdfSchedules as $pdf): ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="card border-0 shadow-sm rounded-4 h-100">
                                <div class="card-body p-4 text-center">
                                    <i class="fas fa-file-pdf text-danger display-4 mb-3"></i>
                                    <h5 class="fw-bold text-dark mb-2"><?php echo escape($pdf->title); ?></h5>
                                    <p class="text-muted small mb-3"><?php echo escape($pdf->notes ?: 'جدول رسمي مرفق بصيغة PDF'); ?></p>
                                    <a href="<?php echo url('/' . escape($pdf->file_path)); ?>" target="_blank" class="btn btn-outline-danger rounded-pill px-4">
                                        <i class="fas fa-download me-1"></i> تحميل الجدول
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.official-schedule-matrix th, .official-schedule-matrix td {
    border-color: #000 !important;
}
@media print {
    .no-print, header, footer, navbar, .navbar, .btn, .nav-pills {
        display: none !important;
    }
    body {
        background: #fff !important;
        color: #000 !important;
        padding: 0 !important;
        margin: 0 !important;
    }
    .container-fluid {
        width: 100% !important;
        max-width: 100% !important;
        padding: 0 !important;
    }
    .printable-table-card {
        border: none !important;
        box-shadow: none !important;
        padding: 0 !important;
    }
    .official-schedule-matrix {
        font-size: 11px !important;
    }
}
</style>
