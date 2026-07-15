<div class="d-flex justify-content-between align-items-center mb-4">
    <h3><i class="fas fa-book ms-2"></i>المواد الدراسية</h3>
    <div class="d-flex gap-2">
        <a href="<?php echo url('/search'); ?>" class="btn btn-outline-primary">
            <i class="fas fa-search ms-1"></i>بحث متقدم
        </a>
    </div>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="<?php echo url('/courses'); ?>" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label for="major_id" class="form-label">التخصص</label>
                <select name="major_id" id="major_id" class="form-select" onchange="this.form.submit()">
                    <option value="">جميع التخصصات</option>
                    <?php foreach ($majors ?? [] as $major): ?>
                        <option value="<?php echo escape($major->id); ?>" <?php echo (isset($_GET['major_id']) && $_GET['major_id'] == $major->id) ? 'selected' : ''; ?>>
                            <?php echo escape($major->name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </form>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-hover align-middle bg-white shadow-sm rounded">
        <thead class="table-primary">
            <tr>
                <th>اسم المادة</th>
                <th>التخصص</th>
                <th>المستوى</th>
                <th>الفصل</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($courses)): ?>
                <tr>
                        <td colspan="5" class="text-center text-muted py-4">لا توجد مواد دراسية متاحة</td>
                </tr>
            <?php else: ?>
                <?php foreach ($courses as $course): ?>
                    <tr>
                        <td class="fw-bold"><?php echo escape($course->name); ?></td>
                        <td><?php echo escape($course->major_name ?? ''); ?></td>
                        <td><?php echo escape($course->level_name ?? ''); ?></td>
                        <td><?php echo escape($course->semester_name ?? ''); ?></td>
                        <td>
                            <a href="<?php echo url('/courses/' . escape($course->id)); ?>" class="btn btn-primary btn-sm">
                                <i class="fas fa-eye ms-1"></i>تفاصيل
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>