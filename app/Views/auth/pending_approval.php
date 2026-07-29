<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بانتظار موافقة المشرف - اللجنة العلمية</title>
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
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
                    <div class="card-body p-4 p-md-5 text-center">
                        <div class="mb-4">
                            <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-warning bg-opacity-15 p-3" style="width: 90px; height: 90px;">
                                <i class="fas fa-clock fa-3x text-warning"></i>
                            </span>
                        </div>
                        <h4 class="fw-bold text-dark mb-2">تم إرسال طلبك بنجاح!</h4>
                        <p class="text-muted fs-6 mb-4">
                            طلبك حالياً <strong>بانتظار موافقة المشرف العام (Super Admin)</strong>. سيتم تفعيل حسابك فور اعتماد الطلب.
                        </p>

                        <div class="card bg-light border-0 rounded-3 p-3 mb-4 text-start">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small">نوع الحساب المطلوب:</span>
                                <strong class="text-dark">
                                    <?php echo $request->account_type === 'representative' ? 'مندوب مستوى' : 'مسؤول تخصص'; ?>
                                </strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small">التخصص العلمي:</span>
                                <strong class="text-primary"><?php echo escape($request->major_name); ?></strong>
                            </div>
                            <?php if ($request->account_type === 'representative' && !empty($request->level_name)): ?>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted small">المستوى الدراسي:</span>
                                    <strong class="text-info"><?php echo escape($request->level_name); ?></strong>
                                </div>
                            <?php endif; ?>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted small">حالة الطلب:</span>
                                <span class="badge bg-warning text-dark px-3 py-1 rounded-pill">قيد المراجعة</span>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <a href="<?php echo url('/pending-approval'); ?>" class="btn btn-outline-primary w-100 rounded-pill py-2">
                                <i class="fas fa-sync-alt ms-1"></i>تحديث الحالة
                            </a>
                            <a href="<?php echo url('/logout'); ?>" class="btn btn-light w-100 rounded-pill py-2 border">
                                <i class="fas fa-sign-out-alt ms-1"></i>تسجيل الخروج
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
