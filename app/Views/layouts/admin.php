<!DOCTYPE html>
<html dir="rtl" lang="ar" class="h-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة التحكم - اللجنة العلمية</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="<?php echo asset('assets/css/admin.css'); ?>">
</head>
<body>
    <div class="admin-wrapper">
        <aside class="admin-sidebar" id="adminSidebar">
            <div class="sidebar-logo-wrapper">
                <?php $collegeLogo = \App\Models\Setting::get('college_logo'); ?>
                <?php if ($collegeLogo): ?>
                    <img src="<?php echo asset($collegeLogo); ?>" alt="شعار الكلية" class="sidebar-top-logo">
                <?php endif; ?>
            </div>
            <div class="sidebar-header d-flex align-items-center justify-content-between">
                <a href="<?php echo url('/admin/dashboard'); ?>" class="sidebar-brand">
                    <i class="fas fa-graduation-cap text-primary ms-1"></i>
                    <span>اللجنة العلمية</span>
                </a>
                <button type="button" class="btn-close btn-close-white d-lg-none" id="mobileSidebarClose" aria-label="إغلاق"></button>
            </div>
            <ul class="sidebar-nav">
                <li class="sidebar-li">
                    <a class="sidebar-a <?php echo is_active_route('/admin') || is_active_route('/admin/dashboard') ? 'active' : ''; ?>" href="<?php echo url('/admin/dashboard'); ?>">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>لوحة التحكم</span>
                    </a>
                </li>
                <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                <li class="sidebar-li">
                    <a class="sidebar-a <?php echo is_active_route('/admin/majors') ? 'active' : ''; ?>" href="<?php echo url('/admin/majors'); ?>">
                        <i class="fas fa-university"></i>
                        <span>التخصصات</span>
                    </a>
                </li>
                <li class="sidebar-li">
                    <a class="sidebar-a <?php echo is_active_route('/admin/levels') ? 'active' : ''; ?>" href="<?php echo url('/admin/levels'); ?>">
                        <i class="fas fa-layer-group"></i>
                        <span>المستويات</span>
                    </a>
                </li>
                <li class="sidebar-li">
                    <a class="sidebar-a <?php echo is_active_route('/admin/semesters') ? 'active' : ''; ?>" href="<?php echo url('/admin/semesters'); ?>">
                        <i class="fas fa-calendar-alt"></i>
                        <span>الفصول الدراسية</span>
                    </a>
                </li>
                <?php endif; ?>
                <li class="sidebar-li">
                    <a class="sidebar-a <?php echo is_active_route('/admin/courses') ? 'active' : ''; ?>" href="<?php echo url('/admin/courses'); ?>">
                        <i class="fas fa-book"></i>
                        <span>المواد الدراسية</span>
                    </a>
                </li>
                <li class="sidebar-li">
                    <a class="sidebar-a <?php echo is_active_route('/admin/files') ? 'active' : ''; ?>" href="<?php echo url('/admin/files'); ?>">
                        <i class="fas fa-file"></i>
                        <span>الملفات</span>
                        <?php
                            $badgeCount = $newFilesCount ?? \App\Models\File::getRecentCount(48);
                            if ($badgeCount > 0):
                        ?>
                            <span class="badge bg-danger">+<?php echo $badgeCount; ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <?php if (isset($_SESSION['user_role']) && ($_SESSION['user_role'] === 'admin' || ($_SESSION['user_role'] === 'manager' && !empty($_SESSION['managed_major_id'])))): ?>
                <li class="sidebar-li">
                    <a class="sidebar-a <?php echo is_active_route('/admin/users') ? 'active' : ''; ?>" href="<?php echo url('/admin/users'); ?>">
                        <i class="fas fa-users"></i>
                        <span>المسؤولين</span>
                    </a>
                </li>
                <?php endif; ?>
                <li class="sidebar-li">
                    <a class="sidebar-a <?php echo is_active_route('/admin/announcements') ? 'active' : ''; ?>" href="<?php echo url('/admin/announcements'); ?>">
                        <i class="fas fa-bullhorn"></i>
                        <span>الإعلانات</span>
                    </a>
                </li>
                <?php if (isset($_SESSION['user_role']) && ($_SESSION['user_role'] === 'admin' || ($_SESSION['user_role'] === 'manager' && !empty($_SESSION['managed_major_id'])))): ?>
                <li class="sidebar-li">
                    <a class="sidebar-a <?php echo is_active_route('/admin/managers') ? 'active' : ''; ?>" href="<?php echo url('/admin/managers'); ?>">
                        <i class="fas fa-user-tie"></i>
                        <span>المندوبين</span>
                    </a>
                </li>
                <?php endif; ?>
                <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                <li class="sidebar-li">
                    <a class="sidebar-a <?php echo is_active_route('/admin/settings') ? 'active' : ''; ?>" href="<?php echo url('/admin/settings'); ?>">
                        <i class="fas fa-cogs"></i>
                        <span>الإعدادات</span>
                    </a>
                </li>
                <?php endif; ?>
            </ul>
            <div class="sidebar-footer">
                <a href="<?php echo url('/'); ?>" class="btn btn-sm btn-outline-light w-100 mb-2">
                    <i class="fas fa-eye ms-1"></i>عرض الموقع
                </a>
                <a href="<?php echo url('/logout'); ?>" class="btn btn-sm btn-danger w-100">
                    <i class="fas fa-sign-out-alt ms-1"></i>تسجيل خروج
                </a>
            </div>
        </aside>
        <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

        <main class="admin-main">
            <nav class="admin-topbar">
                <div class="d-flex align-items-center justify-content-between w-100">
                    <div class="topbar-left">
                        <button class="sidebar-toggle" id="sidebarToggle" title="إظهار/إخفاء القائمة">
                            <i class="fas fa-bars"></i>
                        </button>
                    </div>
                    <div class="topbar-right">
                        <button id="darkModeToggle" class="btn btn-sm btn-outline-secondary rounded-pill theme-toggle-admin px-3" title="الوضع الليلي">
                            <i class="fas fa-moon"></i>
                            <i class="fas fa-sun"></i>
                        </button>
                        <span class="text-muted small d-none d-md-inline">
                            <i class="fas fa-user ms-1"></i><?php echo escape($_SESSION['user_name'] ?? ''); ?>
                        </span>
                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2">
                            <?php echo escape($_SESSION['user_role'] ?? ''); ?>
                        </span>
                    </div>
                </div>
            </nav>

            <div class="admin-content">
                <?php if (flash_has('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle ms-1"></i>
                        <?php echo flash('success'); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                <?php if (flash_has('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle ms-1"></i>
                        <?php echo flash('error'); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                <?php echo $content; ?>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script src="<?php echo asset('assets/js/admin.js'); ?>"></script>
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
