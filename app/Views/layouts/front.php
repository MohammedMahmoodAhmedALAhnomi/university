<!DOCTYPE html>
<html dir="rtl" lang="ar" class="h-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>اللجنة العلمية</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
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
            <div class="offcanvas offcanvas-end d-lg-none" tabindex="-1" id="mainNavbar" aria-labelledby="mainNavbarLabel">
                <div class="offcanvas-header bg-primary text-white">
                    <h5 class="offcanvas-title" id="mainNavbarLabel">
                        <i class="fas fa-bars ms-1"></i>القائمة
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
                </div>
                <div class="offcanvas-body">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link <?php echo is_active_route('/') ? 'active' : ''; ?>" href="<?php echo url('/?home=1'); ?>">
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
                <form class="d-flex mx-2 my-2 my-lg-0 offcanvas-search" action="<?php echo url('/search'); ?>" method="GET" role="search">
                    <div class="input-group">
                        <input class="form-control border" type="search" name="q" placeholder="بحث في المواد..." aria-label="بحث">
                        <button class="btn btn-outline-primary btn-sm" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
                <div id="userPrefNavWidgetMobile" class="d-none mt-3 px-2">
                    <div class="card border-primary border-opacity-25 bg-primary bg-opacity-10 rounded-3">
                        <div class="card-body p-3 d-flex align-items-center justify-content-between">
                            <div>
                                <small class="text-muted d-block mb-1" style="font-size: 0.75rem;">تخصصك ومستواك الحالي:</small>
                                <strong id="userPrefNavLabelMobile" class="text-primary fs-6"></strong>
                            </div>
                            <button type="button" class="btn btn-primary btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#preferenceModal">
                                <i class="fas fa-sliders-h ms-1"></i>تغيير
                            </button>
                        </div>
                    </div>
                </div>
                <hr class="my-2">
                <div class="d-flex flex-column gap-2 px-2 pb-2">
                    <?php if (isset($_SESSION['user_id'])): ?>
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
</body>
</html>