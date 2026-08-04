<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Setting;
use App\Config\Database;

class SettingController extends Controller
{
    public function index(): void
    {
        $settings = Setting::getAllGrouped();
        $this->view('admin/settings/index', ['settings' => $settings]);
    }

    public function update(): void
    {
        if (!$this->isPost() || !verify_csrf()) {
            redirect(url('/admin/settings'));
        }

        $settingsPost = $this->postParam('settings', []);
        if (is_array($settingsPost)) {
            foreach ($settingsPost as $id => $value) {
                $row = Database::fetch("SELECT setting_key, setting_type FROM settings WHERE id = ?", [(int)$id]);
                if (!$row) continue;
                if ($row->setting_type !== 'image') {
                    Setting::set($row->setting_key, $value);
                }
            }
        }

        $files = $this->fileParam('settings');
        if ($files && is_array($files['tmp_name'])) {
            foreach ($files['tmp_name'] as $id => $tmpName) {
                if ($files['error'][$id] !== UPLOAD_ERR_OK) continue;
                $row = Database::fetch("SELECT setting_key, setting_type FROM settings WHERE id = ?", [(int)$id]);
                if (!$row || $row->setting_type !== 'image') continue;
                $uploadDir = __DIR__ . '/../../public/uploads/settings/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $extension = pathinfo($files['name'][$id], PATHINFO_EXTENSION);
                $fileName = 'setting_' . $id . '_' . uniqid() . '.' . $extension;
                if (move_uploaded_file($tmpName, $uploadDir . $fileName)) {
                    Setting::set($row->setting_key, '/uploads/settings/' . $fileName);
                }
            }
        }

        log_activity('update', 'settings', 0, 'تحديث الإعدادات');
        flash('success', 'تم تحديث الإعدادات بنجاح');
        redirect(url('/admin/settings'));
    }
}
