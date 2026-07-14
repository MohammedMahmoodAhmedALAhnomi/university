<!DOCTYPE html>
<html dir="rtl" lang="ar" class="h-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة التحكم - اللجنة العلمية</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?php echo asset('assets/css/admin.css'); ?>">
</head>
<body>
    <div class="admin-wrapper">
        <aside class="admin-sidebar">
            <div class="sidebar-header">
                <a href="<?php echo url('/admin/dashboard'); ?>" class="sidebar-brand">
                    <i class="fas fa-graduation-cap"></i>
                    <span>اللجنة العلمية</span>
                </a>
            </div>
            <ul class="sidebar-nav">
                <li class="nav-item">
                    <a class="nav-link <?php echo is_active_route('/admin') || is_active_route('/admin/dashboard') ? 'active' : ''; ?>" href="<?php echo url('/admin/dashboard'); ?>">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>لوحة التحكم</span>
                    </a>
                </li>
                <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo is_active_route('/admin/majors') ? 'active' : ''; ?>" href="<?php echo url('/admin/majors'); ?>">
                        <i class="fas fa-university"></i>
                        <span>التخصصات</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo is_active_route('/admin/levels') ? 'active' : ''; ?>" href="<?php echo url('/admin/levels'); ?>">
                        <i class="fas fa-layer-group"></i>
                        <span>المستويات</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo is_active_route('/admin/semesters') ? 'active' : ''; ?>" href="<?php echo url('/admin/semesters'); ?>">
                        <i class="fas fa-calendar-alt"></i>
                        <span>الفصول الدراسية</span>
                    </a>
                </li>
                <?php endif; ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo is_active_route('/admin/courses') ? 'active' : ''; ?>" href="<?php echo url('/admin/courses'); ?>">
                        <i class="fas fa-book"></i>
                        <span>المواد الدراسية</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo is_active_route('/admin/files') ? 'active' : ''; ?>" href="<?php echo url('/admin/files'); ?>">
                        <i class="fas fa-file"></i>
                        <span>الملفات</span>
                        <?php
                            $badgeCount = $newFilesCount ?? \App\Models\File::getRecentCount(48);
                            if ($badgeCount > 0):
                        ?>
                            <span class="badge bg-danger ms-auto">+<?php echo $badgeCount; ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo is_active_route('/admin/users') ? 'active' : ''; ?>" href="<?php echo url('/admin/users'); ?>">
                        <i class="fas fa-users"></i>
                        <span>المستخدمين</span>
                    </a>
                </li>
                <?php endif; ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo is_active_route('/admin/announcements') ? 'active' : ''; ?>" href="<?php echo url('/admin/announcements'); ?>">
                        <i class="fas fa-bullhorn"></i>
                        <span>الإعلانات</span>
                    </a>
                </li>
                <?php if (isset($_SESSION['user_role']) && ($_SESSION['user_role'] === 'admin' || ($_SESSION['user_role'] === 'manager' && !empty($_SESSION['managed_major_id'])))): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo is_active_route('/admin/managers') ? 'active' : ''; ?>" href="<?php echo url('/admin/managers'); ?>">
                        <i class="fas fa-user-tie"></i>
                        <span>المندوبين</span>
                    </a>
                </li>
                <?php endif; ?>
                <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo is_active_route('/admin/settings') ? 'active' : ''; ?>" href="<?php echo url('/admin/settings'); ?>">
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

        <div class="sidebar-overlay" onclick="closeSidebar()"></div>

        <main class="admin-main">
            <nav class="admin-topbar">
                <div class="d-flex align-items-center justify-content-between w-100">
                    <div>
                        <button class="btn btn-sm btn-outline-secondary sidebar-toggle" onclick="toggleSidebar()" title="إظهار/إخفاء القائمة">
                            <i class="fas fa-bars"></i>
                        </button>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <button id="darkModeToggle" class="btn btn-sm btn-outline-secondary rounded-pill" title="الوضع الليلي">
                            <i class="fas fa-moon"></i>
                        </button>
                        <span class="text-muted small">
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
    function initSidebar() {
        var sidebar = document.querySelector('.admin-sidebar');
        var wrapper = document.querySelector('.admin-wrapper');
        var overlay = document.querySelector('.sidebar-overlay');
        if (!sidebar || !wrapper) return;

        var sidebarWidth = getComputedStyle(document.documentElement).getPropertyValue('--sidebar-width').trim();
        var hiddenRight = '-' + sidebarWidth;

        function isMobile() { return window.innerWidth <= 992; }

        function updateSidebar() {
            var isOpen = wrapper.classList.contains('sidebar-collapsed');
            if (isMobile()) {
                sidebar.style.transition = 'right 0.3s ease';
                sidebar.style.right = isOpen ? '0' : hiddenRight;
                if (overlay) overlay.style.display = isOpen ? 'block' : 'none';
            } else {
                sidebar.style.right = '';
                sidebar.style.transition = '';
                if (overlay) overlay.style.display = 'none';
            }
        }

        window.toggleSidebar = function () {
            wrapper.classList.toggle('sidebar-collapsed');
            updateSidebar();
        };

        window.closeSidebar = function () {
            wrapper.classList.remove('sidebar-collapsed');
            updateSidebar();
        };

        updateSidebar();
        window.addEventListener('resize', updateSidebar);
    }

    document.addEventListener('DOMContentLoaded', initSidebar);

    (function() {
        var saved = localStorage.getItem('darkMode');
        if (saved === 'true') {
            document.documentElement.setAttribute('data-bs-theme', 'dark');
        }
        document.addEventListener('DOMContentLoaded', function() {
            var btn = document.getElementById('darkModeToggle');
            if (!btn) return;
            if (document.documentElement.getAttribute('data-bs-theme') === 'dark') {
                btn.innerHTML = '<i class="fas fa-sun"></i>';
            }
            btn.addEventListener('click', function() {
                var html = document.documentElement;
                var isDark = html.getAttribute('data-bs-theme') === 'dark';
                if (isDark) {
                    html.removeAttribute('data-bs-theme');
                    localStorage.setItem('darkMode', 'false');
                    btn.innerHTML = '<i class="fas fa-moon"></i>';
                } else {
                    html.setAttribute('data-bs-theme', 'dark');
                    localStorage.setItem('darkMode', 'true');
                    btn.innerHTML = '<i class="fas fa-sun"></i>';
                }
            });
        });
    })();
    </script>
</body>
</html>
