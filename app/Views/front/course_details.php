<nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-3">
        <li class="breadcrumb-item"><a href="<?php echo url('/'); ?>"><i class="fas fa-home ms-1"></i>الرئيسية</a></li>
        <li class="breadcrumb-item"><a href="<?php echo url('/majors/' . escape($course->major_id)); ?>"><?php echo escape($course->major_name ?? ''); ?></a></li>
        <li class="breadcrumb-item active"><?php echo escape($course->name); ?></li>
    </ol>
</nav>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <div class="d-flex align-items-center gap-4 flex-wrap">
            <h4 class="fw-bold mb-0">
                <i class="fas fa-info-circle ms-2 text-primary"></i>
                <?php echo escape($course->name); ?>
            </h4>
            <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2">
                <i class="fas fa-graduation-cap ms-1"></i>
                <?php echo escape($course->major_name ?? ''); ?>
            </span>
            <span class="badge bg-teal bg-opacity-10 text-teal rounded-pill px-3 py-2">
                <i class="fas fa-layer-group ms-1"></i>
                <?php echo escape($course->level_name ?? ''); ?>
            </span>
            <span class="badge bg-purple bg-opacity-10 text-purple rounded-pill px-3 py-2">
                <i class="fas fa-calendar-alt ms-1"></i>
                <?php echo escape($course->semester_name ?? ''); ?>
            </span>
        </div>
        <?php if (!empty($course->description)): ?>
            <p class="text-muted mt-3 mb-0"><?php echo escape($course->description); ?></p>
        <?php endif; ?>
        <div class="d-flex align-items-start gap-3 mt-3 border-top pt-3 flex-wrap">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <small class="text-muted fw-bold">قيم المادة:</small>
                    <div class="stars-container" data-course-id="<?php echo escape($course->id); ?>" data-current="<?php echo escape($course->avg_rating ?? 0); ?>" style="direction: ltr; display: inline-flex;">
                        <?php $labels = ['', 'ضعيف', 'مقبول', 'جيد', 'جيد جداً', 'ممتاز']; ?>
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <span class="star-rating"
                               data-value="<?php echo $i; ?>"
                               title="<?php echo $labels[$i]; ?>"
                               style="cursor: pointer; font-size: 1.6rem; line-height: 1; color: <?php echo $i <= round($course->avg_rating ?? 0) ? '#ffc107' : '#bbb'; ?>; transition: color 0.15s; user-select: none;">
                               ★
                            </span>
                        <?php endfor; ?>
                    </div>
                    <small class="text-muted" id="ratingInfo">
                        <?php if (($course->rating_count ?? 0) > 0): ?>
                            <?php echo escape($course->avg_rating); ?> / 5 (<?php echo escape($course->rating_count); ?> تقييم)
                        <?php else: ?>
                            لم يتم التقييم بعد
                        <?php endif; ?>
                    </small>
                    <small class="text-success fw-bold d-none" id="userRatingBadge">
                        <i class="fas fa-check-circle ms-1"></i>
                        <span id="userRatingText"></span>
                    </small>
                </div>
                <?php if (!empty($ratingDistribution) && ($course->rating_count ?? 0) > 0): ?>
                    <div class="mt-2" style="max-width: 280px;">
                        <?php 
                        $dist = [];
                        foreach ($ratingDistribution as $r) { $dist[$r->rating] = $r->cnt; }
                        ?>
                        <?php for ($s = 5; $s >= 1; $s--): ?>
                            <?php $cnt = (int)($dist[$s] ?? 0); ?>
                            <div class="d-flex align-items-center gap-2 small text-muted" style="line-height: 1.4;">
                                <span style="width: 14px;"><?php echo $s; ?></span>
                                <span style="color: #ffc107;">★</span>
                                <div class="progress flex-grow-1" style="height: 6px;">
                                    <div class="progress-bar bg-warning" style="width: <?php echo ($course->rating_count > 0) ? ($cnt / $course->rating_count * 100) : 0; ?>%;"></div>
                                </div>
                                <span style="width: 30px; text-align: left;"><?php echo $cnt; ?></span>
                            </div>
                        <?php endfor; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div>
        <?php
        $typeLabels = ['lecture' => 'محاضرات', 'summary' => 'ملخصات', 'model' => 'نماذج اختبار', 'exam' => 'اختبارات', 'other' => 'أخرى'];
        $typeIcons = ['lecture' => 'fa-book', 'summary' => 'fa-file-lines', 'model' => 'fa-clipboard', 'exam' => 'fa-pen-to-square', 'other' => 'fa-folder'];
        $typeColors = ['lecture' => 'primary', 'summary' => 'success', 'model' => 'warning', 'exam' => 'danger', 'other' => 'secondary'];
        $grouped = [];
        foreach ($files as $file) {
            $t = $file->file_type ?? 'other';
            $grouped[$t][] = $file;
        }
        ?>
        <?php if (empty($files)): ?>
            <div class="card shadow-sm border-0">
                <div class="card-body text-center text-muted py-5">
                    <i class="fas fa-folder-open fa-3x mb-3"></i>
                    <p>لا توجد ملفات لهذه المادة بعد</p>
                </div>
            </div>
        <?php else: ?>
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                <ul class="nav nav-tabs mb-0" id="fileTabs" role="tablist">
                    <?php $first = true; foreach ($typeLabels as $key => $label): if (empty($grouped[$key])) continue; ?>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link <?php echo $first ? 'active' : ''; ?>" id="tab-<?php echo $key; ?>" data-bs-toggle="tab" data-bs-target="#content-<?php echo $key; ?>" type="button" role="tab">
                                <i class="fas <?php echo $typeIcons[$key]; ?> ms-1"></i>
                                <?php echo $label; ?>
                                <span class="badge bg-<?php echo $typeColors[$key]; ?> ms-1"><?php echo count($grouped[$key]); ?></span>
                            </button>
                        </li>
                    <?php $first = false; endforeach; ?>
                </ul>
                <div class="d-flex align-items-center gap-2">
                    <small class="text-muted"><i class="fas fa-sort ms-1"></i>ترتيب:</small>
                    <a href="<?php echo url('/courses/' . escape($course->id) . '?sort=newest'); ?>" class="btn btn-sm <?php echo ($currentSort ?? 'newest') === 'newest' ? 'btn-primary' : 'btn-outline-secondary'; ?> rounded-pill px-3">الأحدث</a>
                    <a href="<?php echo url('/courses/' . escape($course->id) . '?sort=downloads'); ?>" class="btn btn-sm <?php echo ($currentSort ?? 'newest') === 'downloads' ? 'btn-primary' : 'btn-outline-secondary'; ?> rounded-pill px-3">الأكثر تحميلاً</a>
                </div>
            </div>
            <div class="tab-content" id="fileTabsContent">
                <?php $first = true; foreach ($typeLabels as $key => $label): if (empty($grouped[$key])) continue; ?>
                    <div class="tab-pane fade <?php echo $first ? 'show active' : ''; ?>" id="content-<?php echo $key; ?>" role="tabpanel">
                        <div class="card shadow-sm border-0">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="table-<?php echo $typeColors[$key]; ?>">
                                            <tr>
                                                <th>الملف</th>
                                                <th>النوع</th>
                                                <th>تاريخ الرفع</th>
                                                <th>التحميلات</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($grouped[$key] as $file): ?>
                                                <tr>
                                                    <td>
                                                        <i class="fas fa-file-<?php echo $file->file_extension === 'pdf' ? 'pdf text-danger' : ($file->file_extension === 'image' ? 'image text-success' : ($file->file_extension === 'doc' || $file->file_extension === 'docx' ? 'word text-primary' : 'alt text-secondary')); ?> ms-1"></i>
                                                        <?php echo escape($file->title); ?>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-info"><?php echo escape(strtoupper($file->file_extension ?? '')); ?></span>
                                                    </td>
                                                    <td>
                                                        <small class="text-muted">
                                                            <i class="fas fa-calendar ms-1"></i>
                                                            <?php echo escape(format_date($file->created_at ?? '', 'Y-m-d')); ?>
                                                        </small>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-secondary">
                                                            <i class="fas fa-download ms-1"></i>
                                                            <?php echo escape($file->download_count ?? 0); ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex gap-1">
                                                            <?php
                                                                $previewExt = $file->file_extension ?? '';
                                                                $isPreviewable = !in_array($previewExt, ['zip', 'rar', '7z', 'tar', 'gz']);
                                                                $isOfficeDoc = in_array($previewExt, ['doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx']);
                                                            ?>
                                                            <?php if ($isPreviewable && !$isOfficeDoc): ?>
                                                                <a href="<?php echo url('/files/' . escape($file->id) . '/preview'); ?>" target="_blank" class="btn btn-outline-primary btn-sm">
                                                                    <i class="fas fa-eye ms-1"></i>معاينة
                                                                </a>
                                                            <?php elseif ($isPreviewable && $isOfficeDoc): ?>
                                                                <button type="button" class="btn btn-outline-primary btn-sm preview-office-btn"
                                                                    data-title="<?php echo escape($file->title); ?>"
                                                                    data-ext="<?php echo escape(strtoupper($previewExt)); ?>"
                                                                    data-download="<?php echo url('/files/' . escape($file->id) . '/download'); ?>">
                                                                    <i class="fas fa-eye ms-1"></i>معاينة
                                                                </button>
                                                            <?php endif; ?>
                                                            <a href="<?php echo url('/files/' . escape($file->id) . '/download'); ?>" class="btn btn-success btn-sm" data-confirm-download="1" data-title="<?php echo escape($file->title); ?>">
                                                                <i class="fas fa-download ms-1"></i>تحميل
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php $first = false; endforeach; ?>
            </div>
            <?php if ($totalPages > 1): ?>
                <nav class="mt-3">
                    <ul class="pagination pagination-sm justify-content-center mb-0">
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?php echo $i === $currentPage ? 'active' : ''; ?>">
                                <a class="page-link" href="<?php echo url('/courses/' . escape($course->id) . '?sort=' . urlencode($currentSort ?? 'newest') . '&amp;page=' . $i); ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        <?php endif; ?>
    </div>

<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 9999;">
    <div id="ratingToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body d-flex align-items-center gap-2">
                <i class="fas fa-check-circle fa-lg"></i>
                <span id="toastMessage">تم تسجيل تقييمك بنجاح</span>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var container = document.querySelector('.stars-container');
    if (!container) return;
    var stars = container.querySelectorAll('.star-rating');
    var courseId = container.getAttribute('data-course-id');
    var labels = ['', 'ضعيف', 'مقبول', 'جيد', 'جيد جداً', 'ممتاز'];
    var storedKey = 'rated_' + courseId;

    var userRating = localStorage.getItem(storedKey);
    if (userRating) {
        var badge = document.getElementById('userRatingBadge');
        if (badge) {
            badge.classList.remove('d-none');
            document.getElementById('userRatingText').textContent = 'تقييمك: ' + labels[parseInt(userRating)] + ' (' + userRating + ' نجوم)';
        }
        stars.forEach(function(s) { s.style.cursor = 'default'; });
    }

    function showToast(msg) {
        var el = document.getElementById('ratingToast');
        document.getElementById('toastMessage').textContent = msg;
        var toast = new bootstrap.Toast(el, { delay: 3000 });
        toast.show();
    }

    function setStars(value) {
        stars.forEach(function(s) {
            var val = parseInt(s.getAttribute('data-value'));
            s.style.color = val <= value && value > 0 ? '#ffc107' : '#bbb';
        });
    }

    stars.forEach(function(star) {
        star.addEventListener('mouseenter', function() {
            if (userRating) return;
            var hoverVal = parseInt(this.getAttribute('data-value'));
            stars.forEach(function(s) {
                var val = parseInt(s.getAttribute('data-value'));
                s.style.color = val <= hoverVal ? '#ffc107' : '#bbb';
            });
        });
        star.addEventListener('mouseleave', function() {
            if (userRating) return;
            var currentAvg = parseFloat(container.getAttribute('data-current') || '0');
            setStars(Math.round(currentAvg));
        });
        star.addEventListener('click', function() {
            if (userRating) {
                showToast('لقد قيمت هذه المادة من قبل بمقدار ' + userRating + ' نجوم');
                return;
            }
            var value = parseInt(this.getAttribute('data-value'));
            var formData = new FormData();
            formData.append('course_id', courseId);
            formData.append('rating', value);

            fetch('<?php echo url('/courses/' . escape($course->id) . '/rate'); ?>', {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.success) {
                    localStorage.setItem(storedKey, value);
                    userRating = value;
                    var avg = parseFloat(data.avg_rating);
                    container.setAttribute('data-current', avg);
                    setStars(Math.round(avg));
                    document.getElementById('ratingInfo').textContent =
                        data.avg_rating + ' / 5 (' + data.rating_count + ' تقييم)';
                    var badge = document.getElementById('userRatingBadge');
                    if (badge) {
                        badge.classList.remove('d-none');
                        document.getElementById('userRatingText').textContent = 'تقييمك: ' + labels[value] + ' (' + value + ' نجوم)';
                    }
                    stars.forEach(function(s) { s.style.cursor = 'default'; });
                    showToast('شكراً لك! تم تسجيل تقييمك (' + labels[value] + ')');
                } else {
                    showToast(data.message);
                }
            })
            .catch(function() {
                showToast('حدث خطأ أثناء تسجيل التقييم');
            });
        });
    });
});
</script>
