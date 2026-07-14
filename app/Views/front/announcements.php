<div class="d-flex justify-content-between align-items-center mb-4">
    <h3><i class="fas fa-bullhorn ms-2"></i>الإعلانات</h3>
</div>

<?php if (empty($announcements)): ?>
    <div class="text-center py-5">
        <i class="fas fa-bullhorn fa-4x text-muted mb-3"></i>
        <h5>لا توجد إعلانات حالياً</h5>
        <p class="text-muted">سيتم إضافة الإعلانات قريباً</p>
    </div>
<?php else: ?>
    <div class="row g-4">
        <?php foreach ($announcements as $announcement): ?>
            <div class="col-12">
                <div class="card shadow-sm border-0 <?php echo $announcement->is_pinned ? 'border-start border-warning border-4' : ''; ?>">
                    <?php if ($announcement->is_pinned): ?>
                        <div class="card-header bg-warning bg-opacity-10 border-0">
                            <span class="badge bg-warning text-dark">
                                <i class="fas fa-thumbtack ms-1"></i>
                                مثبت
                            </span>
                        </div>
                    <?php endif; ?>
                    <div class="card-body">
                        <h5 class="card-title fw-bold"><?php echo escape($announcement->title); ?></h5>
                        <p class="card-text"><?php echo nl2br(escape($announcement->content)); ?></p>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div>
                                <?php if (isset($announcement->creator_name)): ?>
                                    <small class="text-muted ms-3">
                                        <i class="fas fa-user ms-1"></i>
                                        <?php echo escape($announcement->creator_name); ?>
                                    </small>
                                <?php endif; ?>
                                <small class="text-muted">
                                    <i class="fas fa-calendar ms-1"></i>
                                    <?php echo escape(format_date($announcement->created_at, 'Y-m-d H:i')); ?>
                                </small>
                            </div>
                            <?php if (!empty($announcement->image)): ?>
                                <a href="<?php echo asset('uploads/' . escape($announcement->image)); ?>" class="btn btn-outline-primary btn-sm" target="_blank">
                                    <i class="fas fa-image ms-1"></i>عرض المرفق
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>