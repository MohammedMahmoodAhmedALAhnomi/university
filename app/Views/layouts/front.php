<!DOCTYPE html>
<html dir="rtl" lang="ar" class="h-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>اللجنة العلمية</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?php echo asset('assets/css/style.css'); ?>">
</head>
<body class="d-flex flex-column h-100">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm flex-shrink-0">
        <div class="container">
            <a class="navbar-brand fw-bold d-flex align-items-center" href="<?php echo url('/'); ?>">
                <?php
                $collegeLogo = \App\Models\Setting::get('college_logo');
                if ($collegeLogo): ?>
                    <img src="<?php echo asset($collegeLogo); ?>" alt="شعار الكلية" style="height: 40px; width: auto;" class="ms-2">
                <?php else: ?>
                    <i class="fas fa-university ms-1"></i>
                <?php endif; ?>
                <span class="d-none d-sm-inline">اللجنة العلمية</span>
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNavbar">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link <?php echo is_active_route('/') ? 'active' : ''; ?>" href="<?php echo url('/'); ?>">
                            <i class="fas fa-home ms-1"></i>الرئيسية
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo is_active_route('/about') ? 'active' : ''; ?>" href="<?php echo url('/about'); ?>">
                            <i class="fas fa-info-circle ms-1"></i>من نحن
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo is_active_route('/contact') ? 'active' : ''; ?>" href="<?php echo url('/contact'); ?>">
                            <i class="fas fa-envelope ms-1"></i>اتصل بنا
                        </a>
                    </li>
                </ul>
                <form class="d-flex mx-2 my-2 my-lg-0" action="<?php echo url('/search'); ?>" method="GET" role="search">
                    <div class="input-group">
                        <input class="form-control bg-white bg-opacity-15 border-0 text-white" type="search" name="q" placeholder="بحث في المواد..." aria-label="بحث" style="backdrop-filter: blur(4px);">
                        <button class="btn btn-light btn-sm" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
                <div class="d-flex align-items-center gap-2 ms-lg-2 my-2 my-lg-0">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="<?php echo url('/admin/dashboard'); ?>" class="btn btn-light btn-sm rounded-pill px-3">
                            <i class="fas fa-user ms-1"></i><?php echo escape($_SESSION['user_name']); ?>
                        </a>
                    <?php else: ?>
                        <a href="<?php echo url('/login'); ?>" class="btn btn-outline-light btn-sm rounded-pill px-3">
                            <i class="fas fa-sign-in-alt ms-1"></i>تسجيل الدخول
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button id="darkModeToggle" class="theme-toggle" title="الوضع الليلي" aria-label="الوضع الليلي">
                    <i class="fas fa-moon"></i>
                    <i class="fas fa-sun"></i>
                    <span class="theme-toggle-label d-none d-lg-inline">ليلي</span>
                </button>
                <?php
                $uniLogo = \App\Models\Setting::get('university_logo');
                if ($uniLogo): ?>
                    <img src="<?php echo asset($uniLogo); ?>" alt="شعار الجامعة" style="height: 36px; width: auto;">
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <main class="flex-shrink-0">
        <div class="container px-3 px-md-4">
            <?php if (flash_has('success')): ?>
                <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                    <i class="fas fa-check-circle ms-1"></i>
                    <?php echo flash('success'); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            <?php if (flash_has('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                    <i class="fas fa-exclamation-circle ms-1"></i>
                    <?php echo flash('error'); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            <?php echo $content; ?>
        </div>
    </main>

    <footer class="bg-dark text-white py-4 mt-auto flex-shrink-0">
        <div class="container text-center">
            <p class="mb-0">&copy; <?php echo date('Y'); ?> اللجنة العلمية. جميع الحقوق محفوظة.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo asset('assets/js/main.js'); ?>"></script>



<div class="modal fade" id="downloadConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-body text-center py-5 px-4">
                <div class="mb-3">
                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-success bg-opacity-10" style="width: 80px; height: 80px;">
                        <i class="fas fa-download fa-2x text-success"></i>
                    </span>
                </div>
                <h5 class="fw-bold mb-1">تأكيد التحميل</h5>
                <p class="text-muted small mb-0" id="confirmFileName" style="direction: ltr; text-align: center;"></p>
            </div>
            <div class="modal-footer border-0 justify-content-center pb-4 pt-0">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">إلغاء</button>
                <a href="#" id="confirmDownloadBtn" class="btn btn-success rounded-pill px-4 shadow-sm">
                    <i class="fas fa-download ms-1"></i>تحميل
                </a>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="officePreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-body text-center py-5 px-4">
                <div class="mb-3">
                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-warning bg-opacity-10" style="width: 80px; height: 80px;">
                        <i class="fas fa-file-alt fa-2x text-warning"></i>
                    </span>
                </div>
                <h5 class="fw-bold mb-1">المعاينة غير متاحة</h5>
                <p class="text-muted small mb-0">هذا النوع من الملفات لا يمكن معاينته في المتصفح.<br>يمكنك تحميل الملف لفتحه على جهازك.</p>
                <div class="mt-3">
                    <span class="badge bg-light text-dark rounded-pill px-4 py-2" id="officeFileExt"></span>
                </div>
            </div>
            <div class="modal-footer border-0 justify-content-center pb-4 pt-0">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">إغلاق</button>
                <a href="#" id="officeDownloadBtn" class="btn btn-success rounded-pill px-4 shadow-sm">
                    <i class="fas fa-download ms-1"></i>تحميل الملف
                </a>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.preview-office-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            document.getElementById('officeFileExt').textContent = btn.getAttribute('data-ext');
            document.getElementById('officeDownloadBtn').href = btn.getAttribute('data-download');
            new bootstrap.Modal(document.getElementById('officePreviewModal')).show();
        });
    });
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('[data-confirm-download]').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            var url = btn.getAttribute('href');
            var title = btn.getAttribute('data-title') || '';
            document.getElementById('confirmFileName').textContent = title;
            document.getElementById('confirmDownloadBtn').href = url;
            var modal = new bootstrap.Modal(document.getElementById('downloadConfirmModal'));
            modal.show();
        });
    });
});
</script>

<script>
(function() {
    var saved = localStorage.getItem('darkMode');
    if (saved === 'true') {
        document.documentElement.setAttribute('data-bs-theme', 'dark');
    }
    document.addEventListener('DOMContentLoaded', function() {
        var btn = document.getElementById('darkModeToggle');
        if (!btn) return;
        btn.addEventListener('click', function() {
            var html = document.documentElement;
            var isDark = html.getAttribute('data-bs-theme') === 'dark';
            if (isDark) {
                html.removeAttribute('data-bs-theme');
                localStorage.setItem('darkMode', 'false');
            } else {
                html.setAttribute('data-bs-theme', 'dark');
                localStorage.setItem('darkMode', 'true');
            }
        });
    });
})();
</script>
</body>
</html>