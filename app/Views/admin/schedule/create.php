<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                <h5 class="fw-bold text-dark mb-0"><i class="fas fa-plus-circle text-primary me-2"></i>إضافة عنصر جديد للجداول الدراسية</h5>
            </div>
            <div class="card-body p-4">
                <form action="<?php echo url('/admin/schedule/store'); ?>" method="POST">
                    <?php echo csrf_field(); ?>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">التخصص <span class="text-danger">*</span></label>
                            <select name="major_id" class="form-select rounded-3" required>
                                <option value="">اختر التخصص...</option>
                                <?php foreach ($majors as $m): ?>
                                    <option value="<?php echo $m->id; ?>"><?php echo escape($m->name); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">المستوى <span class="text-danger">*</span></label>
                            <select name="level_id" class="form-select rounded-3" required>
                                <option value="">اختر المستوى...</option>
                                <?php foreach ($levels as $l): ?>
                                    <option value="<?php echo $l->id; ?>"><?php echo escape($l->name); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">المجموعة / القروب</label>
                            <input type="text" name="group_name" class="form-control rounded-3" placeholder="مثال: قروب 1 / مجموعة A">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">رمز الكود <small class="text-muted">(اختياري - يتولد تلقائياً مثل IT1_G1)</small></label>
                            <input type="text" name="section_code" class="form-control rounded-3" placeholder="مثال: IT1_G1">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">نوع العنصر <span class="text-danger">*</span></label>
                            <select name="type" id="scheduleType" class="form-select rounded-3" required>
                                <option value="lecture" selected>محاضرة أسبوعية</option>
                                <option value="exam">امتحان (نصفي / نهائي)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">اسم المادة <span class="text-danger">*</span></label>
                            <input type="text" name="subject_name" class="form-control rounded-3" placeholder="مثال: ذكاء اصطناعي" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">عنوان الجدول / الوصف البسيط <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control rounded-3" placeholder="مثال: محاضرة عملي / اختبار الجزء الأول" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">اسم الدكتور / المحاضر</label>
                            <input type="text" name="doctor_name" class="form-control rounded-3" placeholder="د. أحمد...">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">القاعة / المكان</label>
                            <input type="text" name="location_hall" class="form-control rounded-3" placeholder="مثال: قاعة 302 / معامل الحاسوب">
                        </div>
                    </div>

                    <!-- خيارات المحاضرة الأسبوعية -->
                    <div id="lectureOptions" class="p-3 bg-light rounded-3 mb-3 border">
                        <h6 class="fw-bold text-primary mb-3"><i class="fas fa-clock me-1"></i>توقيت المحاضرة الأسبوعية</h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">اليوم</label>
                                <select name="day_of_week" class="form-select form-select-sm">
                                    <option value="Sunday">الأحد</option>
                                    <option value="Monday">الإثنين</option>
                                    <option value="Tuesday">الثلاثاء</option>
                                    <option value="Wednesday">الأربعاء</option>
                                    <option value="Thursday">الخميس</option>
                                    <option value="Friday">الجمعة</option>
                                    <option value="Saturday">السبت</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">وقت البداية</label>
                                <input type="time" name="start_time" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">وقت النهاية</label>
                                <input type="time" name="end_time" class="form-control form-control-sm">
                            </div>
                        </div>
                    </div>

                    <!-- خيارات الامتحان -->
                    <div id="examOptions" class="p-3 bg-light rounded-3 mb-3 border d-none">
                        <h6 class="fw-bold text-danger mb-3"><i class="fas fa-calendar-alt me-1"></i>موعد ووقت الامتحان</h6>
                        <div class="mb-2">
                            <label class="form-label small fw-bold">تاريخ ووقت الاختبار</label>
                            <input type="datetime-local" name="exam_date" class="form-control">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">ملاحظات إضافية</label>
                        <textarea name="notes" class="form-control rounded-3" rows="2" placeholder="ملاحظات مفردات الامتحان أو مستلزمات القاعة..."></textarea>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="<?php echo url('/admin/schedule'); ?>" class="btn btn-light rounded-pill px-4">إلغاء</a>
                        <button type="submit" class="btn btn-primary rounded-pill px-5 shadow-sm fw-bold">حفظ العنصر</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.getElementById('scheduleType');
    const lecOpt = document.getElementById('lectureOptions');
    const examOpt = document.getElementById('examOptions');

    typeSelect.addEventListener('change', function() {
        if (this.value === 'exam') {
            lecOpt.classList.add('d-none');
            examOpt.classList.remove('d-none');
        } else {
            lecOpt.classList.remove('d-none');
            examOpt.classList.add('d-none');
        }
    });
});
</script>
