<div class="container py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
        <div>
            <h3 class="fw-bold text-dark mb-1"><i class="fas fa-bookmark text-primary me-2"></i>المحتوى والملفات المحفوظة</h3>
            <p class="text-muted small mb-0">جميع الملازم والملفات الأكاديمية التي قمت بحفظها للرجوع إليها سريعاً.</p>
        </div>
        <a href="<?php echo url('/search'); ?>" class="btn btn-outline-primary rounded-pill px-3 shadow-sm">
            <i class="fas fa-search me-1"></i> استكشاف المزيد من الملفات
        </a>
    </div>

    <?php if (empty($bookmarks)): ?>
        <div class="card border-0 shadow-sm rounded-4 text-center p-5 my-4">
            <div class="card-body">
                <i class="fas fa-folder-open display-1 text-muted opacity-50 mb-3"></i>
                <h4 class="fw-bold text-secondary mb-2">لا توجد ملفات محفوظة حتى الآن</h4>
                <p class="text-muted max-w-md mx-auto mb-4">يمكنك حفظ الملازم ونماذج الاختبارات بسهولة عن طريق النقر على أيقونة (النجمة/الحفظ ⭐) في بطاقات الملفات.</p>
                <a href="<?php echo url('/?home=1'); ?>" class="btn btn-primary rounded-pill px-4 shadow-sm">
                    <i class="fas fa-graduation-cap me-1"></i> تصفح المواد الدراسية
                </a>
            </div>
        </div>
    <?php else: ?>
        <div class="row g-3">
            <?php foreach ($bookmarks as $file): ?>
                <div class="col-md-6 col-lg-4" id="bookmark-card-<?php echo $file->id; ?>">
                    <div class="card border-0 shadow-sm rounded-4 h-100 position-relative transition-hover">
                        <div class="card-body p-4 d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1 rounded-pill small">
                                    <i class="fas fa-file-pdf me-1"></i> <?php echo escape($file->file_type ?? 'ملزمة'); ?>
                                </span>
                                <button type="button" 
                                        class="btn btn-sm btn-light text-danger rounded-circle shadow-sm remove-bookmark-btn" 
                                        data-file-id="<?php echo $file->id; ?>" 
                                        title="إزالة من المحفوظات">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>

                            <h5 class="fw-bold text-dark mb-2 text-truncate" title="<?php echo escape($file->title); ?>">
                                <?php echo escape($file->title); ?>
                            </h5>

                            <div class="small text-muted mb-3">
                                <div class="mb-1"><i class="fas fa-book me-1 text-secondary"></i> <strong>المادة:</strong> <?php echo escape($file->course_name); ?></div>
                                <?php if (!empty($file->doctor_name)): ?>
                                    <div><i class="fas fa-user-tie me-1 text-secondary"></i> <strong>الدكتور:</strong> <?php echo escape($file->doctor_name); ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center">
                                <span class="text-muted small">
                                    <i class="fas fa-download me-1"></i> <?php echo number_format($file->download_count ?? 0); ?>
                                </span>
                                <div class="d-flex gap-2">
                                    <a href="<?php echo url('/files/' . $file->id . '/preview'); ?>" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                                        <i class="fas fa-eye me-1"></i> معاينة
                                    </a>
                                    <a href="<?php echo url('/files/' . $file->id . '/download'); ?>" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm">
                                        <i class="fas fa-download me-1"></i> تحميل
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.remove-bookmark-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const fileId = this.getAttribute('data-file-id');
            const card = document.getElementById('bookmark-card-' + fileId);

            if (!confirm('هل انت متأكد من إزالة هذا الملف من المحفوظات؟')) return;

            const formData = new FormData();
            formData.append('file_id', fileId);

            fetch('<?php echo url('/student/bookmarks/toggle'); ?>', {
                method: 'POST',
                body: formData,
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    if (card) {
                        card.style.transition = 'all 0.3s ease';
                        card.style.opacity = '0';
                        card.style.transform = 'scale(0.9)';
                        setTimeout(() => card.remove(), 300);
                    }
                } else {
                    alert(data.message || 'حدث خطأ أثناء التحديث');
                }
            })
            .catch(err => console.error(err));
        });
    });
});
</script>
