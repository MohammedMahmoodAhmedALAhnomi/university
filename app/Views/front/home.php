<div class="hero-section">
    <div class="hero-overlay"></div>
    <div class="container position-relative" style="z-index: 2;">
        <div class="mb-4">
            <span class="badge bg-dark bg-opacity-25 text-white px-4 py-2 rounded-pill fs-6 fw-normal border border-white border-opacity-25">
                <i class="fas fa-graduation-cap ms-2"></i>اللجنة العلمية - كلية الحاسوب وتقنية المعلومات
            </span>
        </div>
        <h1 class="display-4 fw-bold mb-3 text-white">مرحباً بكم في اللجنة العلمية</h1>
        <p class="lead mb-4 fs-5 opacity-90 mx-auto" style="max-width: 700px;">منصة المشاركة العلمية - نوفر لكم جميع المواد الدراسية والملفات التعليمية بأحدث الوسائل التقنية</p>
        <div class="d-flex justify-content-center gap-3 flex-wrap mb-5">
            <a href="#majors-section" class="btn btn-light btn-lg rounded-pill px-5 fw-bold shadow-sm">
                <i class="fas fa-university ms-2"></i>تصفح التخصصات
            </a>
            <a href="<?php echo url('/announcements'); ?>" class="btn btn-outline-light btn-lg rounded-pill px-5 fw-bold">
                <i class="fas fa-bullhorn ms-2"></i>الإعلانات
            </a>
        </div>
    </div>
    <div class="hero-wave position-absolute bottom-0 start-0 end-0" style="z-index: 1;">
        <svg viewBox="0 0 1440 60" preserveAspectRatio="none">
            <path d="M0,30 C360,60 720,0 1440,30 L1440,60 L0,60 Z" fill="#f8f9fa"></path>
        </svg>
    </div>
</div>

<div class="container" style="margin-top: -2rem; position: relative; z-index: 3;">
    <div class="row g-3 mb-5">
        <div class="col-6 col-md-3">
            <div class="stat-card-modern text-center p-4 bg-white shadow-sm rounded-4 border-0 h-100">
                <div class="stat-icon-modern mx-auto mb-2 rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                    <i class="fas fa-book text-primary fs-4"></i>
                </div>
                <div class="stat-number-modern fs-2 fw-bold text-dark"><?php echo $courseCount; ?></div>
                <div class="stat-label-modern small text-muted">مادة دراسية</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card-modern text-center p-4 bg-white shadow-sm rounded-4 border-0 h-100">
                <div class="stat-icon-modern mx-auto mb-2 rounded-circle bg-success bg-opacity-10 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                    <i class="fas fa-file-alt text-success fs-4"></i>
                </div>
                <div class="stat-number-modern fs-2 fw-bold text-dark"><?php echo $fileCount; ?></div>
                <div class="stat-label-modern small text-muted">ملف تعليمي</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card-modern text-center p-4 bg-white shadow-sm rounded-4 border-0 h-100">
                <div class="stat-icon-modern mx-auto mb-2 rounded-circle bg-warning bg-opacity-10 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                    <i class="fas fa-layer-group text-warning fs-4"></i>
                </div>
                <div class="stat-number-modern fs-2 fw-bold text-dark"><?php echo $majorCount; ?></div>
                <div class="stat-label-modern small text-muted">تخصص علمي</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card-modern text-center p-4 bg-white shadow-sm rounded-4 border-0 h-100">
                <div class="stat-icon-modern mx-auto mb-2 rounded-circle bg-info bg-opacity-10 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                    <i class="fas fa-clock text-info fs-4"></i>
                </div>
                <div class="stat-number-modern fs-2 fw-bold text-dark"><?php echo $newFilesCount; ?></div>
                <div class="stat-label-modern small text-muted">مضاف حديثاً</div>
            </div>
        </div>
    </div>

    <section id="majors-section" class="mb-5">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h2 class="fw-bold mb-1">التخصصات العلمية</h2>
                <p class="text-muted mb-0">اختر تخصصك لتصفح المواد الدراسية والملفات التعليمية</p>
            </div>
        </div>
        <?php if (!empty($majors)): ?>
            <div class="row g-4">
                <?php foreach ($majors as $major): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 border-0 card-hover" role="button" data-bs-toggle="modal" data-bs-target="#levelModal" data-major-id="<?php echo $major->id; ?>" data-major-name="<?php echo escape($major->name); ?>">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 54px; height: 54px;">
                                        <i class="<?php echo escape($major->icon ?? 'fas fa-university'); ?> fs-5 text-primary"></i>
                                    </div>
                                    <div class="me-3">
                                        <h5 class="fw-bold mb-1"><?php echo escape($major->name); ?></h5>
                                        <span class="badge bg-primary text-white rounded-pill px-3 py-1">
                                            <i class="fas fa-book ms-1"></i><?php echo escape($major->courses_count ?? 0); ?> مادة
                                        </span>
                                    </div>
                                </div>
                                <p class="card-text text-muted small mb-0"><?php echo escape(truncate($major->description, 120)); ?></p>
                            </div>
                            <div class="card-footer bg-white border-0 pt-0 px-4 pb-3">
                                <span class="btn btn-outline-primary btn-sm w-100 rounded-pill">
                                    <i class="fas fa-arrow-left ms-1"></i>تصفح المواد
                                </span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-5 bg-white rounded-4 shadow-sm">
                <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                <p class="text-muted">لا توجد تخصصات متاحة حالياً</p>
            </div>
        <?php endif; ?>
    </section>

    <?php if (!empty($announcements)): ?>
        <section class="mb-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold mb-1"><i class="fas fa-bullhorn ms-2 text-primary"></i>الإعلانات</h3>
                    <p class="text-muted small mb-0">آخر الإعلانات والتحديثات</p>
                </div>
                <a href="<?php echo url('/announcements'); ?>" class="btn btn-outline-primary rounded-pill px-4">عرض الكل <i class="fas fa-arrow-left ms-1"></i></a>
            </div>
            <div class="row g-4">
                <?php foreach ($announcements as $announcement): ?>
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm h-100 card-hover overflow-hidden <?php echo $announcement->is_pinned ? 'border-end border-warning border-5' : ''; ?>">
                            <?php if ($announcement->is_pinned): ?>
                                <div class="position-absolute top-0 start-0 bg-warning text-dark px-3 py-1 rounded-bottom-left fw-bold small" style="border-radius: 0 0 12px 0; z-index: 1;">
                                    <i class="fas fa-thumbtack ms-1"></i>مثبت
                                </div>
                            <?php endif; ?>
                            <div class="card-body p-4">
                                <h5 class="card-title fw-bold"><?php echo escape($announcement->title); ?></h5>
                                <p class="card-text text-muted small"><?php echo escape(truncate($announcement->content, 150)); ?></p>
                                <div class="d-flex align-items-center text-muted small mt-3">
                                    <span class="d-flex align-items-center">
                                        <i class="fas fa-calendar ms-1"></i>
                                        <?php echo escape(format_date($announcement->created_at, 'Y-m-d')); ?>
                                    </span>
                                    <?php if (isset($announcement->created_by_name)): ?>
                                        <span class="d-flex align-items-center me-3">
                                            <i class="fas fa-user ms-1"></i>
                                            <?php echo escape($announcement->created_by_name); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>
</div>

<!-- Level Selection Modal -->
<div class="modal fade" id="levelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold"><i class="fas fa-layer-group ms-2 text-primary"></i><span id="levelModalTitle">اختر المستوى</span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body py-4">
                <p class="text-muted mb-3">اختر المستوى لعرض مواده الدراسية:</p>
                <div class="d-flex flex-column gap-2" id="levelList">
                    <?php foreach ($levels as $level): ?>
                        <a href="#" class="btn btn-outline-primary btn-lg text-start px-4 level-link"
                           data-level-id="<?php echo $level->id; ?>"
                           style="border-radius: 12px;">
                            <i class="fas fa-layer-group ms-2"></i>
                            <?php echo escape($level->name); ?>
                        </a>
                    <?php endforeach; ?>
                    <hr class="my-2">
                    <a href="#" class="btn btn-outline-secondary btn-lg text-start px-4 level-link-all"
                       style="border-radius: 12px;">
                        <i class="fas fa-globe ms-2"></i>
                        جميع المستويات
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var modal = document.getElementById('levelModal');
    if (!modal) return;

    modal.addEventListener('show.bs.modal', function(event) {
        var card = event.relatedTarget;
        var majorId = card.getAttribute('data-major-id');
        var majorName = card.getAttribute('data-major-name');
        document.getElementById('levelModalTitle').textContent = majorName;

        var links = modal.querySelectorAll('.level-link, .level-link-all');
        links.forEach(function(link) {
            var levelId = link.getAttribute('data-level-id');
            if (levelId) {
                link.href = '<?php echo url('/'); ?>majors/' + majorId + '?level=' + levelId;
            } else {
                link.href = '<?php echo url('/'); ?>majors/' + majorId;
            }
        });
    });
});
</script>
