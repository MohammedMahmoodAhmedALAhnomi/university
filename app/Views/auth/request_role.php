<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تحديد نوع الحساب - اللجنة العلمية</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #0d6efd, #0a58ca);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: system-ui, -apple-system, sans-serif;
        }
        .account-card-label {
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .btn-check:checked + .account-card-label {
            border-color: #0d6efd !important;
            background-color: rgba(13, 110, 253, 0.08) !important;
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.15) !important;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-7 col-lg-6">
                <div class="card shadow-lg border-0 rounded-4">
                    <div class="card-body p-4 p-md-5">
                        <div class="text-center mb-4">
                            <span class="badge bg-warning bg-opacity-20 text-dark rounded-pill px-3 py-2 fs-6 mb-2"><i class="fas fa-user-graduate me-1 text-warning"></i>طلب ترقية</span>
                            <h3 class="fw-bold text-dark mb-1">طلب ترقية لمندوب أو مسؤول تخصص</h3>
                            <p class="text-muted small">يرجى تحديد التخصص والمستوى المطلوب وإرسال الطلب للموافقة عليه من قِبَل مسؤول التخصص أو المدير العام</p>
                        </div>

                        <?php if (flash_has('error')): ?>
                            <div class="alert alert-danger text-center py-2 mb-3 small">
                                <i class="fas fa-exclamation-circle ms-1"></i><?php echo flash('error'); ?>
                            </div>
                        <?php endif; ?>

                        <form action="<?php echo url('/request-role/post'); ?>" method="POST">
                            <?php echo csrf_field(); ?>

                            <!-- Account Type Selection -->
                            <label class="form-label fw-bold text-dark mb-2">1. حدد نوع الحساب:</label>
                            <div class="row g-3 mb-4">
                                <div class="col-6">
                                    <input type="radio" class="btn-check" name="account_type" id="type_rep" value="representative" checked autocomplete="off">
                                    <label class="btn btn-outline-light text-dark w-100 p-3 rounded-4 border text-center account-card-label h-100" for="type_rep">
                                        <i class="fas fa-user-graduate fa-2x text-primary mb-2 d-block"></i>
                                        <strong class="d-block mb-1">مندوب مستوى</strong>
                                        <small class="text-muted d-block" style="font-size: 0.75rem;">إدارة مستوى محدد داخل تخصص</small>
                                    </label>
                                </div>
                                <div class="col-6">
                                    <input type="radio" class="btn-check" name="account_type" id="type_major" value="major_admin" autocomplete="off">
                                    <label class="btn btn-outline-light text-dark w-100 p-3 rounded-4 border text-center account-card-label h-100" for="type_major">
                                        <i class="fas fa-user-shield fa-2x text-primary mb-2 d-block"></i>
                                        <strong class="d-block mb-1">مسؤول تخصص</strong>
                                        <small class="text-muted d-block" style="font-size: 0.75rem;">إدارة تخصص كامل بكل مستوياته</small>
                                    </label>
                                </div>
                            </div>

                            <!-- Major Selection -->
                            <div class="mb-3">
                                <label for="major_id" class="form-label fw-bold text-dark">2. اختر التخصص العلمي:</label>
                                <select class="form-select form-select-lg rounded-3 fs-6" name="major_id" id="major_id" required>
                                    <option value="" selected disabled>-- اختر التخصص --</option>
                                    <?php foreach ($majors as $m): ?>
                                        <option value="<?php echo $m->id; ?>"><?php echo escape($m->name); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Level Selection (For Representative) -->
                            <div class="mb-4" id="levelContainer">
                                <label for="level_id" class="form-label fw-bold text-dark">3. اختر المستوى الدراسي (للمندوب):</label>
                                <select class="form-select form-select-lg rounded-3 fs-6" name="level_id" id="level_id">
                                    <option value="" selected disabled>-- اختر المستوى --</option>
                                    <?php foreach ($levels as $l): ?>
                                        <option value="<?php echo $l->id; ?>"><?php echo escape($l->name); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill fw-bold shadow-sm">
                                <i class="fas fa-paper-plane ms-2"></i>إرسال الطلب للمشرف
                            </button>
                        </form>

                        <div class="text-center mt-3">
                            <a href="<?php echo url('/logout'); ?>" class="text-muted small text-decoration-none">
                                <i class="fas fa-sign-out-alt ms-1"></i>تسجيل الخروج
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var repRadio = document.getElementById('type_rep');
        var majorRadio = document.getElementById('type_major');
        var levelContainer = document.getElementById('levelContainer');
        var levelSelect = document.getElementById('level_id');

        function toggleLevel() {
            if (repRadio.checked) {
                levelContainer.style.display = 'block';
                levelSelect.required = true;
            } else {
                levelContainer.style.display = 'none';
                levelSelect.required = false;
            }
        }

        repRadio.addEventListener('change', toggleLevel);
        majorRadio.addEventListener('change', toggleLevel);
        toggleLevel();
    });
    </script>
</body>
</html>
