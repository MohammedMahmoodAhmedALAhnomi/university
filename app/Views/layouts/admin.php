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
                <a href="<?php echo (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') ? url('/admin/dashboard') : url('/admin/courses'); ?>" class="sidebar-brand">
                    <i class="fas fa-graduation-cap text-primary ms-1"></i>
                    <span>اللجنة العلمية</span>
                </a>
                <button type="button" class="btn-close btn-close-white d-lg-none" id="mobileSidebarClose" aria-label="إغلاق"></button>
            </div>
            <ul class="sidebar-nav">
                <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                <li class="sidebar-li">
                    <a class="sidebar-a <?php echo is_active_route('/admin') || is_active_route('/admin/dashboard') ? 'active' : ''; ?>" href="<?php echo url('/admin/dashboard'); ?>">
                        <i class="fa-solid fa-chart-pie me-2 text-primary"></i>
                        <span>لوحة التحكم</span>
                    </a>
                </li>
                <?php endif; ?>
                <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                <li class="sidebar-li">
                    <a class="sidebar-a <?php echo is_active_route('/admin/requests') ? 'active' : ''; ?>" href="<?php echo url('/admin/requests'); ?>">
                        <i class="fa-solid fa-user-check me-2 text-success"></i>
                        <span>طلبات الانضمام</span>
                        <?php
                            $pendingReqCount = \App\Config\Database::fetch("SELECT COUNT(*) as cnt FROM join_requests WHERE status = 'pending'")->cnt ?? 0;
                            if ($pendingReqCount > 0):
                        ?>
                            <span class="badge bg-warning text-dark me-auto"><?php echo $pendingReqCount; ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li class="sidebar-li">
                    <a class="sidebar-a <?php echo is_active_route('/admin/majors') ? 'active' : ''; ?>" href="<?php echo url('/admin/majors'); ?>">
                        <i class="fa-solid fa-graduation-cap me-2 text-warning"></i>
                        <span>التخصصات</span>
                    </a>
                </li>
                <li class="sidebar-li">
                    <a class="sidebar-a <?php echo is_active_route('/admin/levels') ? 'active' : ''; ?>" href="<?php echo url('/admin/levels'); ?>">
                        <i class="fa-solid fa-layer-group me-2 text-info"></i>
                        <span>المستويات</span>
                    </a>
                </li>
                <li class="sidebar-li">
                    <a class="sidebar-a <?php echo is_active_route('/admin/semesters') ? 'active' : ''; ?>" href="<?php echo url('/admin/semesters'); ?>">
                        <i class="fa-solid fa-calendar-days me-2 text-success"></i>
                        <span>الفصول الدراسية</span>
                    </a>
                </li>
                <?php endif; ?>
                <li class="sidebar-li">
                    <a class="sidebar-a <?php echo is_active_route('/admin/courses') ? 'active' : ''; ?>" href="<?php echo url('/admin/courses'); ?>">
                        <i class="fa-solid fa-book-bookmark me-2 text-danger"></i>
                        <span>المواد الدراسية</span>
                    </a>
                </li>
                <li class="sidebar-li">
                    <a class="sidebar-a <?php echo is_active_route('/admin/files') ? 'active' : ''; ?>" href="<?php echo url('/admin/files'); ?>">
                        <i class="fa-solid fa-folder-open me-2 text-primary"></i>
                        <span>الملفات</span>
                        <?php
                            $badgeCount = $newFilesCount ?? \App\Models\File::getRecentCount(48);
                            if ($badgeCount > 0):
                        ?>
                            <span class="badge bg-danger">+<?php echo $badgeCount; ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                <li class="sidebar-li">
                    <a class="sidebar-a <?php echo is_active_route('/admin/users') ? 'active' : ''; ?>" href="<?php echo url('/admin/users'); ?>">
                        <i class="fa-solid fa-user-shield me-2 text-info"></i>
                        <span>المسؤولين</span>
                    </a>
                </li>
                <?php endif; ?>
                <li class="sidebar-li">
                    <a class="sidebar-a <?php echo is_active_route('/admin/announcements') ? 'active' : ''; ?>" href="<?php echo url('/admin/announcements'); ?>">
                        <i class="fa-solid fa-bullhorn me-2 text-warning"></i>
                        <span>الإعلانات</span>
                    </a>
                </li>
                <?php if (isset($_SESSION['user_role']) && in_array($_SESSION['user_role'], ['admin', 'major_admin'])): ?>
                <li class="sidebar-li">
                    <a class="sidebar-a <?php echo is_active_route('/admin/managers') ? 'active' : ''; ?>" href="<?php echo url('/admin/managers'); ?>">
                        <i class="fa-solid fa-user-tie me-2 text-success"></i>
                        <span>المندوبين</span>
                    </a>
                </li>
                <?php endif; ?>
                <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                <li class="sidebar-li">
                    <a class="sidebar-a <?php echo is_active_route('/admin/settings') ? 'active' : ''; ?>" href="<?php echo url('/admin/settings'); ?>">
                        <i class="fa-solid fa-sliders me-2 text-secondary"></i>
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
                    <div class="topbar-right d-flex align-items-center gap-3">
                        <!-- Notification Bell Dropdown -->
                        <?php
                            $currentUserId = $_SESSION['user_id'] ?? null;
                            $currentUserRole = $_SESSION['user_role'] ?? 'guest';
                            $notifTargetUserId = ($currentUserRole === 'admin') ? null : $currentUserId;
                            $unreadNotifCount = \App\Models\Notification::getUnreadCount($notifTargetUserId);
                            $recentNotifications = \App\Models\Notification::getForUser($notifTargetUserId, 6);
                        ?>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary position-relative rounded-circle p-2" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="الإشعارات" style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-bell"></i>
                                <?php if ($unreadNotifCount > 0): ?>
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.65rem;">
                                        <?php echo $unreadNotifCount > 9 ? '+9' : $unreadNotifCount; ?>
                                    </span>
                                <?php endif; ?>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end shadow-lg border-0 p-0 mt-2" style="width: 320px; max-height: 420px; overflow-y: auto; z-index: 1050;">
                                <div class="p-3 bg-light border-bottom d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0 fw-bold"><i class="fas fa-bell text-primary me-2"></i>الإشعارات</h6>
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
