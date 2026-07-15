<div class="row justify-content-center mb-4">
    <div class="col-md-10">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <h3 class="text-center mb-4">
                    <i class="fas fa-search ms-2 text-primary"></i>
                    بحث في المواد الدراسية
                </h3>
                <form action="<?php echo url('/search'); ?>" method="GET">
                    <div class="row g-2 mb-3">
                        <div class="col-12">
                            <div class="input-group input-group-lg">
                                <input type="text" name="q" class="form-control" placeholder="ابحث عن مادة دراسية..." value="<?php echo escape($query ?? ''); ?>" autofocus>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> بحث
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="col-md-4">
                            <select name="major_id" class="form-select" onchange="this.form.submit()">
                                <option value="">جميع التخصصات</option>
                                <?php foreach ($majors as $m): ?>
                                    <option value="<?php echo $m->id; ?>" <?php echo (isset($_GET['major_id']) && $_GET['major_id'] == $m->id) ? 'selected' : ''; ?>>
                                        <?php echo escape($m->name); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <select name="level_id" class="form-select" onchange="this.form.submit()">
                                <option value="">جميع المستويات</option>
                                <?php foreach ($levels as $l): ?>
                                    <option value="<?php echo $l->id; ?>" <?php echo (isset($_GET['level_id']) && $_GET['level_id'] == $l->id) ? 'selected' : ''; ?>>
                                        <?php echo escape($l->name); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <select name="semester_id" class="form-select" onchange="this.form.submit()">
                                <option value="">جميع الفصول</option>
                                <?php foreach ($semesters as $s): ?>
                                    <option value="<?php echo $s->id; ?>" <?php echo (isset($_GET['semester_id']) && $_GET['semester_id'] == $s->id) ? 'selected' : ''; ?>>
                                        <?php echo escape($s->name); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php if ($query): ?>
    <div class="mb-3">
        <p class="text-muted">
            <i class="fas fa-list ms-1"></i>
            نتائج البحث عن: "<strong><?php echo escape($query); ?></strong>"
            -
            <span class="badge bg-primary"><?php echo count($results ?? []); ?> نتيجة</span>
        </p>
    </div>

    <?php if (empty($results)): ?>
        <div class="text-center py-5">
            <i class="fas fa-exclamation-circle fa-4x text-muted mb-3"></i>
            <h5>لا توجد نتائج مطابقة</h5>
            <p class="text-muted">حاول استخدام كلمات بحث مختلفة</p>
        </div>
    <?php else: ?>
        <div class="row g-3">
            <?php foreach ($results as $course): ?>
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body">
                            <div>
                                <h5 class="card-title fw-bold"><?php echo escape($course->name); ?></h5>
                            </div>
                            <p class="card-text text-muted small mb-2">
                                <?php echo escape($course->major_name ?? ''); ?>
                                <span class="mx-2">|</span>
                                <?php echo escape($course->level_name ?? ''); ?>
                                <span class="mx-2">|</span>
                                <?php echo escape($course->semester_name ?? ''); ?>
                            </p>
                            <?php if (!empty($course->description)): ?>
                                <p class="card-text text-muted small">
                                    <?php echo escape(truncate($course->description, 120)); ?>
                                </p>
                            <?php endif; ?>
                            <div class="mt-3">
                                <a href="<?php echo url('/courses/' . escape($course->id)); ?>" class="btn btn-primary btn-sm">
                                    <i class="fas fa-eye ms-1"></i>عرض التفاصيل
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
<?php endif; ?>