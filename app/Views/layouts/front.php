<!DOCTYPE html>
<html dir="rtl" lang="ar" class="h-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>اللجنة العلمية</title>
    <link rel="dns-prefetch" href="//cdn.jsdelivr.net">
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?php echo asset('assets/css/style.css'); ?>">
</head>
<body class="d-flex flex-column h-100">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm flex-shrink-0">
        <div class="container d-flex align-items-center flex-wrap">
            <div class="navbar-logo-wrapper">
                <?php $collegeLogo = \App\Models\Setting::get('college_logo'); ?>
                <?php if ($collegeLogo): ?>
                    <img src="<?php echo asset($collegeLogo); ?>" alt="شعار الكلية" class="navbar-logo-img">
                <?php endif; ?>
            </div>
            <div class="d-flex align-items-center gap-2 order-3 order-lg-2">
                <button class="navbar-toggler border-0 d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <button id="darkModeToggle" class="theme-toggle" title="الوضع الليلي" aria-label="الوضع الليلي">
                    <i class="fas fa-moon"></i>
                    <i class="fas fa-sun"></i>
                    <span class="theme-toggle-label d-none d-lg-inline">ليلي</span>
                </button>
            </div>
            <a class="navbar-brand fw-bold d-flex align-items-center navbar-brand-centered" href="<?php echo url('/?home=1'); ?>">
                <span class="navbar-brand-text">اللجنة العلمية</span>
            </a>
            <div class="d-flex align-items-center gap-2 order-lg-3">
                <div id="userPrefNavWidget" class="d-none d-lg-flex align-items-center me-1">
                    <button type="button" class="btn btn-light btn-sm rounded-pill shadow-sm px-3 py-1 d-flex align-items-center gap-2 border" data-bs-toggle="modal" data-bs-target="#preferenceModal" style="background: rgba(255,255,255,0.92); font-size: 0.85rem;" title="انقر لتغيير التخصص والمستوى">
                        <span class="badge bg-primary rounded-circle p-1"><i class="fas fa-graduation-cap"></i></span>
                        <span id="userPrefNavLabel" class="fw-bold text-dark"></span>
                        <i class="fas fa-sliders-h text-primary ms-1" style="font-size: 0.75rem;"></i>
                    </button>
                </div>
                <div class="d-none d-lg-flex align-items-center gap-2">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <?php
                            $frontUserId = $_SESSION['user_id'];
                            $frontUserRole = $_SESSION['user_role'] ?? 'guest';
                            $notifTargetUserId = ($frontUserRole === 'admin') ? null : $frontUserId;
                            $unreadNotifCount = \App\Models\Notification::getUnreadCount($notifTargetUserId);
                            $recentNotifications = \App\Models\Notification::getForUser($notifTargetUserId, 6);
                        ?>
                        <div class="dropdown me-1">
                            <button class="btn btn-light btn-sm position-relative rounded-circle p-2 shadow-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="الإشعارات" style="width: 34px; height: 34px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-bell text-primary"></i>
                                <?php if ($unreadNotifCount > 0): ?>
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.65rem;">
                                        <?php echo $unreadNotifCount > 9 ? '+9' : $unreadNotifCount; ?>
                                    </span>
                                <?php endif; ?>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end shadow-lg border-0 p-0 mt-2 text-dark" style="width: 320px; max-height: 400px; overflow-y: auto; z-index: 1050;">
                                <div class="p-3 bg-light border-bottom d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-bell text-primary me-2"></i>الإشعارات</h6>
                                    <?php if ($unreadNotifCount > 0): ?>
                                        <form action="<?php echo url('/notifications/read-all'); ?>" method="POST" class="d-inline">
                                            <button type="submit" class="btn btn-link p-0 text-decoration-none small text-muted" style="font-size: 0.8rem;">تحديد الكل كمقروء</button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                                <div class="list-group list-group-flush">
                                    <?php if (empty($recentNotifications)): ?>
                                        <div class="p-4 text-center text-muted small"><i class="fas fa-inbox fs-4 d-block mb-2 text-secondary opacity-50"></i>لا توجد إشعارات حالياً</div>
                                    <?php else: ?>
                                        <?php foreach ($recentNotifications as $notif): ?>
                                            <div class="list-group-item p-3 <?php echo !$notif->is_read ? 'bg-light border-start border-3 border-primary' : ''; ?>">
                                                <div class="d-flex w-100 justify-content-between align-items-center mb-1">
                                                    <span class="badge bg-<?php echo $notif->type === 'success' ? 'success' : ($notif->type === 'danger' ? 'danger' : ($notif->type === 'warning' ? 'warning' : 'info')); ?>">
                                                        <?php echo escape($notif->title); ?>
                                                    </span>
                                                    <small class="text-muted" style="font-size: 0.7rem;"><?php echo format_date($notif->created_at, 'H:i Y/m/d'); ?></small>
                                                </div>
                                                <p class="mb-1 text-dark small" style="font-size: 0.85rem; line-height: 1.4;"><?php echo escape($notif->message); ?></p>
                                                <div class="d-flex justify-content-between align-items-center mt-2">
                                                    <?php if ($notif->link): ?>
                                                        <a href="<?php echo $notif->link; ?>" class="small text-primary text-decoration-none fw-bold" style="font-size: 0.78rem;">التفاصيل <i class="fas fa-arrow-left ms-1"></i></a>
                                                    <?php else: ?>
                                                        <span></span>
                                                    <?php endif; ?>
                                                    <?php if (!$notif->is_read): ?>
                                                        <form action="<?php echo url('/notifications/' . $notif->id . '/read'); ?>" method="POST" class="d-inline">
                                                            <button type="submit" class="btn btn-sm btn-link text-muted p-0 text-decoration-none" style="font-size: 0.75rem;"><i class="fas fa-check ms-1"></i>تم القراءة</button>
                                                        </form>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <a href="<?php echo url('/admin/dashboard'); ?>" class="btn btn-light btn-sm rounded-pill px-3">
                            <i class="fas fa-user ms-1"></i><?php echo escape($_SESSION['user_name']); ?>
                        </a>
                    <?php else: ?>
                        <a href="<?php echo url('/login'); ?>" class="btn btn-light btn-sm rounded-pill px-3">
                            <i class="fas fa-sign-in-alt ms-1"></i>تسجيل الدخول
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="d-none d-lg-flex align-items-center order-lg-4 flex-grow-1">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link <?php echo is_active_route('/') ? 'active' : ''; ?>" href="<?php echo url('/?home=1'); ?>">
                            <i class="fas fa-home ms-1"></i>الرئيسية
                        </a>
                    </li>
                    <?php /* مخفي للتطوير مستقبلاً: أدوات الطالب
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?php echo (is_active_route('/student/gpa-calculator') || is_active_route('/student/bookmarks') || is_active_route('/student/schedule')) ? 'active' : ''; ?>" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-graduate ms-1"></i>أدوات الطالب
                        </a>
                        <ul class="dropdown-menu shadow-lg border-0 rounded-3 mt-1">
                            <li>
                                <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="<?php echo url('/student/gpa-calculator'); ?>">
                                    <i class="fas fa-calculator text-primary"></i> حاسبة المعدل (GPA)
                                </a>
                            <li>
                                <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="<?php echo url('/student/bookmarks'); ?>">
                                    <i class="fas fa-bookmark text-warning"></i> الملفات المحفوظة
                                </a>
                            </li>
                        </ul>
                    </li>
                    */ ?>
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
            </div>
            <!-- Modern Enhanced Mobile Offcanvas Menu -->
            <div class="offcanvas offcanvas-end d-lg-none custom-offcanvas" tabindex="-1" id="mainNavbar" aria-labelledby="mainNavbarLabel">
                <div class="offcanvas-header-custom d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle bg-white bg-opacity-20 p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class="fas fa-graduation-cap text-white fs-5"></i>
                        </div>
                        <div>
                            <div class="offcanvas-brand-title">اللجنة العلمية</div>
                            <div class="offcanvas-brand-subtitle">كلية الحاسوب وتقنية المعلومات</div>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="إغلاق"></button>
                </div>

                <div class="offcanvas-body p-3 d-flex flex-column justify-content-between">
                    <div>
                        <!-- User Card / Auth Actions -->
                        <div class="offcanvas-user-card mb-3">
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-2 d-flex align-items-center justify-content-center fw-bold" style="width: 42px; height: 42px;">
                                            <i class="fas fa-user fs-5"></i>
                                        </div>
                                        <div>
                                            <strong class="d-block text-dark small"><?php echo escape($_SESSION['user_name']); ?></strong>
                                            <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill" style="font-size: 0.7rem;">
                                                <?php echo escape($_SESSION['user_role'] ?? 'طالب'); ?>
                                            </span>
                                        </div>
                                    </div>
                                    <a href="<?php echo url('/admin/dashboard'); ?>" class="btn btn-primary btn-sm rounded-circle p-2" title="لوحة التحكم" style="width: 34px; height: 34px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-arrow-left"></i>
                                    </a>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-1">
                                    <p class="small text-muted mb-2" style="font-size: 0.82rem;">أهلاً بك! قم بتسجيل الدخول للاستفادة الكاملة من الميزات</p>
                                    <div class="d-flex gap-2">
                                        <a href="<?php echo url('/login'); ?>" class="btn btn-primary btn-sm rounded-pill w-100 fw-bold">
                                            <i class="fas fa-sign-in-alt ms-1"></i>تسجيل الدخول
                                        </a>
                                        <a href="<?php echo url('/register'); ?>" class="btn btn-outline-primary btn-sm rounded-pill w-100 fw-bold">
                                            <i class="fas fa-user-plus ms-1"></i>حساب جديد
                                        </a>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Mobile Search -->
                        <form class="mb-3" action="<?php echo url('/search'); ?>" method="GET" role="search">
                            <div class="input-group">
                                <input class="form-control rounded-pill-start border-end-0 bg-light shadow-none" type="search" name="q" placeholder="ابحث عن مادة..." aria-label="بحث" style="font-size: 0.88rem;">
                                <button class="btn btn-primary offcanvas-search-btn rounded-pill-end px-3 fw-bold d-flex align-items-center gap-1" type="submit">
                                    <i class="fas fa-search"></i>
                                    <span>بحث</span>
                                </button>
                            </div>
                        </form>

                        <!-- Nav List -->
                        <div class="nav-menu-mobile">
                            <a class="offcanvas-nav-item <?php echo is_active_route('/') ? 'active' : ''; ?>" href="<?php echo url('/?home=1'); ?>">
                                <i class="fas fa-home text-primary"></i>
                                <span>الرئيسية</span>
                            </a>
                            <a class="offcanvas-nav-item <?php echo is_active_route('/courses') ? 'active' : ''; ?>" href="<?php echo url('/courses'); ?>">
                                <i class="fas fa-book-open text-warning"></i>
                                <span>المواد الدراسية</span>
                            </a>
                            <a class="offcanvas-nav-item <?php echo is_active_route('/announcements') ? 'active' : ''; ?>" href="<?php echo url('/announcements'); ?>">
                                <i class="fas fa-bullhorn text-danger"></i>
                                <span>الإعلانات</span>
                            </a>
                            <a class="offcanvas-nav-item <?php echo is_active_route('/about') ? 'active' : ''; ?>" href="<?php echo url('/about'); ?>">
                                <i class="fas fa-info-circle text-info"></i>
                                <span>من نحن</span>
                            </a>
                            <a class="offcanvas-nav-item <?php echo is_active_route('/contact') ? 'active' : ''; ?>" href="<?php echo url('/contact'); ?>">
                                <i class="fas fa-envelope text-success"></i>
                                <span>اتصل بنا</span>
                            </a>
                        </div>

                        <!-- Saved Preference Banner for Mobile -->
                        <div id="userPrefNavWidgetMobile" class="d-none mt-3">
                            <div class="card border-primary border-opacity-25 bg-primary bg-opacity-10 rounded-3">
                                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                                    <div>
                                        <small class="text-muted d-block mb-1" style="font-size: 0.75rem;">تخصصك ومستواك المحفوظ:</small>
                                        <strong id="userPrefNavLabelMobile" class="text-primary fs-6"></strong>
                                    </div>
                                    <button type="button" class="btn btn-primary btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#preferenceModal">
                                        <i class="fas fa-sliders-h ms-1"></i>تغيير
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Offcanvas Footer -->
                    <div class="pt-3 border-top mt-3 d-flex align-items-center justify-content-between text-muted small">
                        <span>اللجنة العلمية &copy; <?php echo date('Y'); ?></span>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <a href="<?php echo url('/logout'); ?>" class="text-danger text-decoration-none fw-bold small">
                                <i class="fas fa-sign-out-alt ms-1"></i>خروج
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
    </nav>

    <main class="flex-shrink-0">
        <div class="container">
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

    <button id="backToTop" class="back-to-top" title="العودة للأعلى" aria-label="العودة للأعلى">
        <i class="fas fa-arrow-up"></i>
    </button>

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

<?php
$globalMajors = \App\Models\Major::getActive();
$globalLevels = \App\Models\Level::getActive();
?>

<!-- Preference Selection Modal -->
<div class="modal fade" id="preferenceModal" tabindex="-1" aria-labelledby="preferenceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-primary text-white py-3 border-0">
                <h5 class="modal-title fw-bold" id="preferenceModalLabel">
                    <i class="fas fa-graduation-cap ms-2"></i>حدد تخصصك ومستواك الدراسي
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                <p class="text-muted small mb-4 text-center">
                    سيتم حفظ اختيارك في متصفحك لفتح موادك مباشرة في كل زيارة. يمكنك تغيير الاختيار في أي وقت!
                </p>

                <!-- Step 1: Major Selection -->
                <div class="mb-4">
                    <label class="form-label fw-bold text-dark mb-2">
                        <span class="badge bg-primary rounded-circle me-1">1</span> اختر التخصص العلمي:
                    </label>
                    <div class="row g-2" id="prefMajorOptions">
                        <?php foreach ($globalMajors as $m): ?>
                            <div class="col-md-6">
                                <input type="radio" class="btn-check" name="pref_major" id="pref_major_<?php echo $m->id; ?>" value="<?php echo $m->id; ?>" data-name="<?php echo escape($m->name); ?>" autocomplete="off">
                                <label class="btn btn-outline-primary w-100 p-3 text-start rounded-3 shadow-sm d-flex align-items-center justify-content-between pref-major-label" for="pref_major_<?php echo $m->id; ?>">
                                    <span class="fw-bold"><i class="fas fa-university ms-2 text-primary opacity-75"></i><?php echo escape($m->name); ?></span>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Step 2: Level Selection -->
                <div class="mb-4">
                    <label class="form-label fw-bold text-dark mb-2">
                        <span class="badge bg-primary rounded-circle me-1">2</span> اختر المستوى الدراسي:
                    </label>
                    <div class="row g-2" id="prefLevelOptions">
                        <?php foreach ($globalLevels as $l): ?>
                            <div class="col-6 col-md-3">
                                <input type="radio" class="btn-check" name="pref_level" id="pref_level_<?php echo $l->id; ?>" value="<?php echo $l->id; ?>" data-name="<?php echo escape($l->name); ?>" autocomplete="off">
                                <label class="btn btn-outline-secondary w-100 p-2 text-center rounded-3 pref-level-label" for="pref_level_<?php echo $l->id; ?>">
                                    <small class="fw-bold d-block"><?php echo escape($l->name); ?></small>
                                </label>
                            </div>
                        <?php endforeach; ?>
                        <div class="col-6 col-md-3">
                            <input type="radio" class="btn-check" name="pref_level" id="pref_level_all" value="" data-name="جميع المستويات" autocomplete="off" checked>
                            <label class="btn btn-outline-secondary w-100 p-2 text-center rounded-3 pref-level-label" for="pref_level_all">
                                <small class="fw-bold d-block"><i class="fas fa-globe ms-1"></i>الكل</small>
                            </label>
                        </div>
                    </div>
                </div>
                <div id="prefErrorMsg" class="alert alert-danger d-none py-2 mb-0 small text-center" role="alert"></div>
            </div>
            <div class="modal-footer border-0 bg-white justify-content-center p-3">
                <button type="button" id="savePrefBtn" class="btn btn-primary btn-lg rounded-pill px-5 fw-bold shadow">
                    <i class="fas fa-sign-in-alt ms-2"></i>دخول وحفظ الاختيار
                </button>
            </div>
        </div>
    </div>
</div>

<script>
window.AppPref = {
    getKey: function(k) { return localStorage.getItem('app_' + k); },
    setKey: function(k, v) { localStorage.setItem('app_' + k, v); },
    clear: function() {
        localStorage.removeItem('app_major_id');
        localStorage.removeItem('app_major_name');
        localStorage.removeItem('app_level_id');
        localStorage.removeItem('app_level_name');
    },
    get: function() {
        var mid = this.getKey('major_id');
        if (!mid) return null;
        return {
            majorId: mid,
            majorName: this.getKey('major_name') || '',
            levelId: this.getKey('level_id') || '',
            levelName: this.getKey('level_name') || ''
        };
    },
    save: function(majorId, majorName, levelId, levelName) {
        this.setKey('major_id', majorId);
        this.setKey('major_name', majorName);
        this.setKey('level_id', levelId || '');
        this.setKey('level_name', levelName || '');
        this.updateNav();
    },
    updateNav: function() {
        var pref = this.get();
        var desktopWidget = document.getElementById('userPrefNavWidget');
        var mobileWidget = document.getElementById('userPrefNavWidgetMobile');
        var labelDesktop = document.getElementById('userPrefNavLabel');
        var labelMobile = document.getElementById('userPrefNavLabelMobile');
        
        if (pref && pref.majorId) {
            var text = pref.majorName;
            if (pref.levelName && pref.levelName !== 'جميع المستويات') {
                text += ' • ' + pref.levelName;
            }
            if (labelDesktop) labelDesktop.textContent = text;
            if (labelMobile) labelMobile.textContent = text;
            if (desktopWidget) desktopWidget.classList.remove('d-none');
            if (mobileWidget) mobileWidget.classList.remove('d-none');
        } else {
            if (desktopWidget) desktopWidget.classList.add('d-none');
            if (mobileWidget) mobileWidget.classList.add('d-none');
        }
    }
};

document.addEventListener('DOMContentLoaded', function() {
    AppPref.updateNav();

    var prefModalEl = document.getElementById('preferenceModal');
    if (prefModalEl) {
        prefModalEl.addEventListener('show.bs.modal', function() {
            var pref = AppPref.get();
            if (pref) {
                var mRadio = document.getElementById('pref_major_' + pref.majorId);
                if (mRadio) mRadio.checked = true;
                
                var lRadio = pref.levelId ? document.getElementById('pref_level_' + pref.levelId) : document.getElementById('pref_level_all');
                if (lRadio) lRadio.checked = true;
            }
        });

        var saveBtn = document.getElementById('savePrefBtn');
        if (saveBtn) {
            saveBtn.addEventListener('click', function() {
                var selectedMajor = document.querySelector('input[name="pref_major"]:checked');
                var errDiv = document.getElementById('prefErrorMsg');
                
                if (!selectedMajor) {
                    if (errDiv) {
                        errDiv.textContent = 'يرجى اختيار التخصص العلمي أولاً';
                        errDiv.classList.remove('d-none');
                    }
                    return;
                }
                if (errDiv) errDiv.classList.add('d-none');

                var majorId = selectedMajor.value;
                var majorName = selectedMajor.getAttribute('data-name');

                var selectedLevel = document.querySelector('input[name="pref_level"]:checked');
                var levelId = selectedLevel ? selectedLevel.value : '';
                var levelName = selectedLevel ? selectedLevel.getAttribute('data-name') : '';

                AppPref.save(majorId, majorName, levelId, levelName);

                var targetUrl = '<?php echo url('/majors/'); ?>' + majorId + (levelId ? '?level=' + levelId : '');
                window.location.href = targetUrl;
            });
        }
    }
});
</script>
<script src="https://cdn.jsdelivr.net/npm/instant.page@5.2.0/instantpage.min.js" type="module" integrity="sha384-jnGIBoR65n6E2p85ybC46eD55KzP4rVj1/z5kG9Xl6J3n5" crossorigin="anonymous"></script>
</body>
</html>