<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="fas fa-users-cog ms-2 text-primary"></i>إدارة المستخدمين والأدوار</h4>
        <p class="text-muted small mb-0">عرض وبحث وتعديل أدوار جميع حسابات الطلاب والمندوبين والمشرفين والمدراء</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?php echo url('/admin/users/create'); ?>" class="btn btn-primary rounded-pill px-4 shadow-sm">
            <i class="fas fa-user-plus ms-1"></i>إضافة حساب جديد
        </a>
    </div>
</div>

<!-- Search & Filter Bar -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <form action="<?php echo url('/admin/users'); ?>" method="GET" class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-search"></i></span>
                    <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="ابحث بالاسم، البريد الإلكتروني، أو رقم الهاتف..." value="<?php echo escape($search ?? ''); ?>">
                </div>
            </div>
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-white text-muted"><i class="fas fa-filter me-1"></i>تصفية الدور:</span>
                    <select name="role" class="form-select" onchange="this.form.submit()">
                        <option value="all" <?php echo ($role === 'all' || empty($role)) ? 'selected' : ''; ?>>جميع الأدوار (الكل)</option>
                        <option value="student" <?php echo ($role === 'student') ? 'selected' : ''; ?>>👤 طلاب وزوار</option>
                        <option value="manager" <?php echo ($role === 'manager') ? 'selected' : ''; ?>>🎖️ مناديب مستويات</option>
                        <option value="major_admin" <?php echo ($role === 'major_admin') ? 'selected' : ''; ?>>🛡️ مسؤولي تخصصات</option>
                        <?php if (($currentRole ?? '') === 'admin'): ?>
                            <option value="admin" <?php echo ($role === 'admin') ? 'selected' : ''; ?>>👑 مدراء النظام الإداريين</option>
                        <?php endif; ?>
                    </select>
                </div>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary rounded-pill w-100 fw-bold">
                    <i class="fas fa-search ms-1"></i>بحث وتصفية
                </button>
                <?php if (!empty($search) || ($role !== 'all' && !empty($role))): ?>
                    <a href="<?php echo url('/admin/users'); ?>" class="btn btn-outline-secondary rounded-pill px-3" title="إعادة ضبط">
                        <i class="fas fa-redo"></i>
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<!-- Users List Table -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between py-3">
        <h6 class="fw-bold mb-0"><i class="fas fa-list text-primary ms-1"></i>قائمة الحسابات والمستخدمين</h6>
        <?php if (!empty($users)): ?>
            <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2 fw-bold">إجمالي النتائج: <?php echo count($users); ?> مستخدم</span>
        <?php endif; ?>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>اسم المستخدم</th>
                        <th>البريد والتواصل</th>
                        <th>الدور الحالي</th>
                        <th>التخصص المعتمد</th>
                        <th>المستوى</th>
                        <th>الحالة</th>
                        <th>الإجراءات والتحكم</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-5">
                                <i class="fas fa-user-slash fs-1 d-block mb-2 text-secondary opacity-50"></i>
                                لا يوجد مستخدمين مطبقين للبحث أو التصفية الحالية
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($users as $user): ?>
                            <?php 
                                $isOwnerRow = \App\Models\User::isSystemOwner($user);
                                $isLoggedInOwner = strtolower(trim($_SESSION['user_email'] ?? '')) === 'mohammedalahnomi04@gmail.com' || (int)($_SESSION['user_id'] ?? 0) === (int)$user->id;
                            ?>
                            <tr class="<?php echo $isOwnerRow ? 'table-warning bg-warning bg-opacity-10' : ''; ?>">
                                <td class="ps-3 text-muted fw-bold">#<?php echo escape($user->id); ?></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar-circle <?php echo $isOwnerRow ? 'bg-warning text-dark' : 'bg-primary bg-opacity-10 text-primary'; ?> fw-bold d-flex align-items-center justify-content-center rounded-circle" style="width: 38px; height: 38px;">
                                            <?php echo $isOwnerRow ? '👑' : mb_substr(escape($user->full_name ?? 'U'), 0, 1); ?>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark">
                                                <?php echo escape($user->full_name ?? $user->name ?? 'بدون اسم'); ?>
                                                <?php if ($isOwnerRow): ?>
                                                    <span class="badge bg-warning text-dark border border-warning rounded-pill ms-1 small"><i class="fas fa-crown ms-1"></i>مالك النظام (الرئيسي)</span>
                                                <?php endif; ?>
                                            </div>
                                            <small class="text-muted"><i class="fas fa-clock me-1"></i>انضم: <?php echo escape(format_date($user->created_at, 'Y-m-d')); ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div><small class="text-muted"><i class="fas fa-envelope me-1 text-primary"></i><?php echo escape($user->email); ?></small></div>
                                    <?php if (!empty($user->phone)): ?>
                                        <div><small class="text-muted"><i class="fas fa-phone me-1 text-success"></i><?php echo escape($user->phone); ?></small></div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php $r = $user->role ?? 'student'; ?>
                                    <?php if ($isOwnerRow): ?>
                                        <span class="badge bg-warning text-dark border border-dark rounded-pill px-3 py-2 fw-bold shadow-sm" style="font-size: 0.85rem;">
                                            <i class="fas fa-crown ms-1"></i>👑 مالك النظام الشامل
                                        </span>
                                    <?php elseif ($r === 'admin'): ?>
                                        <span class="badge bg-danger text-white rounded-pill px-3 py-2 fw-bold shadow-sm" style="font-size: 0.85rem;">
                                            <i class="fas fa-user-shield ms-1"></i>👑 مدير عام بالنظام
                                        </span>
                                    <?php elseif ($r === 'major_admin'): ?>
                                        <span class="badge bg-primary text-white rounded-pill px-3 py-2 fw-bold shadow-sm" style="font-size: 0.85rem;">
                                            <i class="fas fa-user-shield ms-1"></i>🛡️ مسؤول تخصص
                                        </span>
                                    <?php elseif ($r === 'manager'): ?>
                                        <span class="badge bg-success text-white rounded-pill px-3 py-2 fw-bold shadow-sm" style="font-size: 0.85rem;">
                                            <i class="fas fa-award ms-1"></i>🎖️ مندوب مستوى
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary text-white rounded-pill px-3 py-2 fw-bold shadow-sm" style="font-size: 0.85rem;">
                                            <i class="fas fa-user ms-1"></i>👤 طالب / زائر
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($user->major_name)): ?>
                                        <span class="badge bg-info text-dark rounded-pill px-3 py-2 fw-bold"><i class="fas fa-graduation-cap ms-1"></i><?php echo escape($user->major_name); ?></span>
                                    <?php else: ?>
                                        <span class="text-muted small">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($user->level_name)): ?>
                                        <span class="badge bg-light text-dark border rounded-pill px-3 py-2 fw-bold"><i class="fas fa-layer-group ms-1"></i><?php echo escape($user->level_name); ?></span>
                                    <?php else: ?>
                                        <span class="text-muted small">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($user->is_active): ?>
                                        <span class="badge bg-success text-white rounded-pill px-3 py-2 fw-bold"><i class="fas fa-check-circle ms-1"></i>نشط</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger text-white rounded-pill px-3 py-2 fw-bold"><i class="fas fa-times-circle ms-1"></i>معطل</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <?php if ($isOwnerRow): ?>
                                            <?php if ($isLoggedInOwner): ?>
                                                <a href="<?php echo url('/admin/users/' . escape($user->id) . '/edit'); ?>" class="btn btn-outline-warning btn-sm rounded-pill px-3 fw-bold" title="تعديل حسابك الشخصي">
                                                    <i class="fas fa-user-edit ms-1"></i>تعديل حسابي
                                                </a>
                                            <?php else: ?>
                                                <span class="badge bg-secondary bg-opacity-10 text-muted border rounded-pill px-3 py-2" title="حساب مالك النظام محمي ولا يمكن تعديله أو حذفه إلا بواسطة المالك شخصياً">
                                                    <i class="fas fa-lock ms-1 text-warning"></i>حساب محمي (المالك)
                                                </span>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <a href="<?php echo url('/admin/users/' . escape($user->id) . '/edit'); ?>" class="btn btn-outline-warning btn-sm rounded-pill px-3 fw-bold" title="تعديل الدور والبيانات">
                                                <i class="fas fa-user-edit ms-1"></i>تعديل الدور
                                            </a>
                                            <?php if ((int)$user->id !== (int)($_SESSION['user_id'] ?? 0)): ?>
                                                <a href="<?php echo url('/admin/users/' . escape($user->id) . '/delete?_csrf_token=' . csrf_token()); ?>" class="btn btn-outline-danger btn-sm rounded-pill px-2" onclick="return confirm('هل أنت متأكد من حذف هذا الحساب نهائياً؟')" title="حذف الحساب">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>