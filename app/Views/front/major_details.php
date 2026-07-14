<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?php echo url('/'); ?>"><i class="fas fa-home ms-1"></i>الرئيسية</a></li>
        <li class="breadcrumb-item active"><?php echo escape($major->name); ?></li>
        <?php if ($selectedLevel): ?>
            <li class="breadcrumb-item active"><?php echo escape($selectedLevel->name); ?></li>
        <?php endif; ?>
    </ol>
</nav>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-body text-center p-5">
        <div class="display-4 text-primary mb-3">
            <i class="fas fa-university"></i>
        </div>
        <h2 class="fw-bold"><?php echo escape($major->name); ?></h2>
        <p class="text-muted"><?php echo escape($major->description ?? ''); ?></p>
        <?php if ($selectedLevel): ?>
            <span class="badge bg-primary fs-6 px-4 py-2 rounded-pill">
                <i class="fas fa-layer-group ms-1"></i>
                <?php echo escape($selectedLevel->name); ?>
            </span>
        <?php endif; ?>
    </div>
</div>

<?php if (!empty($levels)): ?>
    <div class="d-flex flex-wrap gap-2 mb-4">
        <?php if ($selectedLevel): ?>
            <a href="<?php echo url('/majors/' . $major->id); ?>" class="btn btn-outline-secondary btn-sm rounded-pill">
                <i class="fas fa-globe ms-1"></i>جميع المستويات
            </a>
        <?php endif; ?>
        <?php foreach ($levels as $lv): ?>
            <?php if ($selectedLevel && $lv->id == $selectedLevel->id) continue; ?>
            <a href="<?php echo url('/majors/' . $major->id . '?level=' . $lv->id); ?>" class="btn btn-outline-primary btn-sm rounded-pill">
                <i class="fas fa-layer-group ms-1"></i><?php echo escape($lv->name); ?>
            </a>
        <?php endforeach; ?>
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
