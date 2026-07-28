<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb mb-0 fs-6">
        <li class="breadcrumb-item"><a href="<?php echo url('/?home=1'); ?>" class="text-decoration-none fw-bold"><i class="fas fa-home ms-1"></i>الرئيسية</a></li>
        <?php if ($selectedLevel): ?>
            <li class="breadcrumb-item"><a href="<?php echo url('/majors/' . $major->id); ?>" class="text-decoration-none fw-bold"><?php echo escape($major->name); ?></a></li>
            <li class="breadcrumb-item active fw-bold text-primary"><?php echo escape($selectedLevel->name); ?></li>
        <?php else: ?>
            <li class="breadcrumb-item active fw-bold text-primary"><?php echo escape($major->name); ?></li>
        <?php endif; ?>
    </ol>
</nav>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-body text-center p-4">
        <div class="display-5 text-primary mb-2">
            <i class="fas fa-university"></i>
        </div>
        <h3 class="fw-bold mb-1"><?php echo escape($major->name); ?></h3>
        <p class="text-muted small mb-2"><?php echo escape($major->description ?? ''); ?></p>
        <?php if ($selectedLevel): ?>
            <div class="d-flex align-items-center justify-content-center gap-2 mt-2">
                <span class="badge bg-primary fs-6 px-4 py-2 rounded-pill">
                    <i class="fas fa-layer-group ms-1"></i>
                    <?php echo escape($selectedLevel->name); ?>
                </span>
                <a href="<?php echo url('/majors/' . $major->id); ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-bold">
                    <i class="fas fa-arrow-right ms-1"></i>الرجوع إلى <?php echo escape($major->name); ?> لجميع المستويات
                </a>
            </div>
        <?php else: ?>
            <span class="badge bg-secondary fs-6 px-4 py-2 rounded-pill">
                <i class="fas fa-globe ms-1"></i>جميع المستويات
            </span>
        <?php endif; ?>
    </div>
</div>

<?php if (!$selectedLevel && !empty($levels)): ?>
    <div class="mb-4">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h5 class="fw-bold mb-0 text-dark"><i class="fas fa-layer-group text-primary ms-2"></i>اختر المستوى الدراسي لدخوله:</h5>
        </div>
        <div class="row g-3">
            <?php foreach ($levels as $lv): ?>
                <div class="col-6 col-md-3">
                    <a href="<?php echo url('/majors/' . $major->id . '?level=' . $lv->id); ?>" class="text-decoration-none">
                        <div class="card border-0 shadow-sm rounded-4 text-center p-3 card-hover h-100 bg-white border-top border-primary border-4" style="transition: transform 0.2s;">
                            <div class="rounded-circle bg-primary bg-opacity-10 text-primary mx-auto mb-2 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                <i class="fas fa-layer-group fs-5"></i>
                            </div>
                            <h6 class="fw-bold mb-1 text-dark"><?php echo escape($lv->name); ?></h6>
                            <small class="text-primary fw-bold d-block mt-1">
                                دخول للمستوى <i class="fas fa-arrow-left ms-1"></i>
                            </small>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>



<?php if (empty($grouped)): ?>
    <div class="text-center py-5">
        <i class="fas fa-folder-open fa-4x text-muted mb-3"></i>
        <h5>لا توجد مواد بعد في هذا التخصص</h5>
    </div>
<?php else: ?>
    <?php
    $currentLevel = null;
    $semColors = ['teal', 'teal', 'warning', 'info'];
    foreach ($grouped as $g):
        if ($currentLevel !== $g['level_number']):
            if ($currentLevel !== null) echo '</div></div>';
            $currentLevel = $g['level_number'];
    ?>
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-primary text-white fw-bold py-3">
            <i class="fas fa-layer-group ms-1"></i>
            <?php echo escape($g['level_name']); ?>
        </div>
        <div class="card-body">
    <?php endif; ?>
            <?php
                $sIdx = ($g['semester_number'] ?? 1) - 1;
                $sColor = $semColors[$sIdx % count($semColors)];
                $isLight = in_array($sColor, ['warning', 'info']);
            ?>
            <div class="card border border-<?php echo $sColor; ?> border-opacity-25 mb-3 shadow-sm overflow-hidden">
                <div class="card-header bg-<?php echo $sColor; ?> <?php echo $isLight ? 'text-dark' : 'text-white'; ?> fw-bold py-2 d-flex align-items-center">
                    <i class="fas fa-calendar-alt ms-2"></i>
                    <?php echo escape($g['semester_name']); ?>
                </div>
                <div class="card-body bg-<?php echo $sColor; ?> bg-opacity-10">
                    <div class="row g-2">
                        <?php foreach ($g['courses'] as $course): ?>
                            <div class="col-md-6">
                                <a href="<?php echo url('/courses/' . $course->id); ?>" class="text-decoration-none">
                                    <div class="card border-start border-<?php echo $sColor; ?> border-3 shadow-sm card-hover h-100">
                                        <div class="card-body py-3">
                                            <span class="fw-bold text-dark"><?php echo escape($course->name); ?></span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
    <?php endforeach; ?>
    <?php if ($currentLevel !== null) echo '</div></div>'; ?>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    if (window.AppPref) {
        window.AppPref.save(
            '<?php echo $major->id; ?>',
            '<?php echo escape($major->name); ?>',
            '<?php echo $selectedLevel ? $selectedLevel->id : ''; ?>',
            '<?php echo $selectedLevel ? escape($selectedLevel->name) : 'جميع المستويات'; ?>'
        );
    }
});
</script>
