<div class="d-flex justify-content-between align-items-center mb-4">
    <h3><i class="fas fa-cogs ms-2"></i>الإعدادات</h3>
</div>

<form action="<?php echo url('/admin/settings/update'); ?>" method="POST" enctype="multipart/form-data">
    <?php echo csrf_field(); ?>
    <?php foreach ($settings as $group => $items): ?>
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white fw-bold">
                <i class="fas fa-folder ms-1"></i>
                <?php echo escape($group); ?>
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
                                'logo_path' => 'الشعار (قديم)',
                                'college_logo' => 'شعار الكلية',
                                'university_logo' => 'شعار الجامعة',
                                'facebook_url' => 'رابط فيسبوك',
                                'twitter_url' => 'رابط تويتر',
                                'instagram_url' => 'رابط انستغرام',
                                'youtube_url' => 'رابط يوتيوب',
                                'max_file_size' => 'الحد الأقصى للملفات (بايت)',
                                'allowed_extensions' => 'امتدادات الملفات المسموحة',
                                'maintenance_mode' => 'وضع الصيانة',
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
                                    <input type="color" class="form-control form-control-color" name="settings[<?php echo escape($setting->id); ?>]" value="<?php echo escape($setting->setting_value ?? '#0d6efd'); ?>">
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
    <div class="text-center">
        <button type="submit" class="btn btn-primary btn-lg">
            <i class="fas fa-save ms-1"></i>حفظ الإعدادات
        </button>
    </div>
</form>