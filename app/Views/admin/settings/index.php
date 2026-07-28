<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="fas fa-cogs ms-2 text-primary"></i>الإعدادات العامة</h4>
        <p class="text-muted small mb-0">ضبط إعدادات الكلية والنظام والألوان</p>
    </div>
</div>

<form action="<?php echo url('/admin/settings/update'); ?>" method="POST" enctype="multipart/form-data">
    <?php echo csrf_field(); ?>
    <?php foreach ($settings as $group => $items): ?>
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="fw-bold mb-0 text-primary">
                    <i class="fas fa-sliders-h ms-2"></i>
                    <?php echo escape($group); ?>
                </h6>
            </div>
            <div class="card-body">
                <?php foreach ($items as $setting): ?>
                    <div class="row mb-3 align-items-center">
                        <label class="col-md-3 col-form-label fw-bold">
                            <?php
                            $labels = [
                                'site_name' => 'اسم الموقع',
                                'site_description' => 'وصف الموقع',
                                'university_name' => 'اسم الجامعة',
                                'primary_color' => 'اللون الأساسي',
                                'secondary_color' => 'اللون الثانوي',
                                'college_logo' => 'شعار الكلية',
                                'facebook_url' => 'رابط فيسبوك',
                                'twitter_url' => 'رابط تويتر',
                                'instagram_url' => 'رابط انستغرام',
                                'youtube_url' => 'رابط يوتيوب',
                                'max_file_size' => 'الحد الأقصى للملفات (بايت)',
                                'allowed_extensions' => 'امتدادات الملفات المسموحة',
                                'maintenance_mode' => 'وضع الصيانة',
                                'google_client_id' => 'Google Client ID (معرف التطبيق)',
                                'google_client_secret' => 'Google Client Secret (المفتاح السري)',
                            ];
                            echo escape($labels[$setting->setting_key] ?? $setting->setting_key);
                            ?>
                        </label>
                        <div class="col-md-9">
                            <?php if ($setting->setting_type === 'text'): ?>
                                <input type="text" class="form-control" name="settings[<?php echo escape($setting->id); ?>]" value="<?php echo escape($setting->setting_value ?? ''); ?>">

                            <?php elseif ($setting->setting_type === 'textarea'): ?>
                                <textarea class="form-control" name="settings[<?php echo escape($setting->id); ?>]" rows="3"><?php echo escape($setting->setting_value ?? ''); ?></textarea>

                            <?php elseif ($setting->setting_type === 'image'): ?>
                                <?php if (!empty($setting->setting_value)): ?>
                                    <div class="mb-2">
                                        <img src="<?php echo asset(escape($setting->setting_value)); ?>" class="img-thumbnail" style="max-height: 100px;">
                                    </div>
                                <?php endif; ?>
                                <input type="file" class="form-control" name="settings[<?php echo escape($setting->id); ?>]" accept="image/*">

                            <?php elseif ($setting->setting_type === 'color'): ?>
                                <div class="input-group">
                                    <input type="color" class="form-control form-control-color" name="settings[<?php echo escape($setting->id); ?>]" value="<?php echo escape($setting->setting_value ?? '#0d6efd'); ?>" oninput="this.nextElementSibling.value=this.value">
                                    <input type="text" class="form-control" name="settings_display[<?php echo escape($setting->id); ?>]" value="<?php echo escape($setting->setting_value ?? '#0d6efd'); ?>" oninput="this.previousElementSibling.value=this.value">
                                </div>

                            <?php elseif ($setting->setting_type === 'url'): ?>
                                <input type="url" class="form-control" name="settings[<?php echo escape($setting->id); ?>]" value="<?php echo escape($setting->setting_value ?? ''); ?>">

                            <?php else: ?>
                                <input type="text" class="form-control" name="settings[<?php echo escape($setting->id); ?>]" value="<?php echo escape($setting->setting_value ?? ''); ?>">
                            <?php endif; ?>

                            <?php if (!empty($setting->description)): ?>
                                <div class="form-text text-muted"><?php echo escape($setting->description); ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>
    <div class="text-center pb-4">
        <button type="submit" class="btn btn-primary rounded-pill px-5 shadow-sm">
            <i class="fas fa-save ms-1"></i>حفظ الإعدادات
        </button>
    </div>
</form>