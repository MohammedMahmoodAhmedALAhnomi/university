<?php

namespace App\Traits;

trait Uploadable
{
    protected function uploadFile(array $file, string $subDir = 'files'): ?string
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $forbidden = ['php', 'phtml', 'php3', 'php4', 'php5', 'phar', 'exe', 'sh', 'bat', 'cmd', 'pl', 'py', 'cgi', 'js', 'html', 'htm'];
        if (in_array($extension, $forbidden, true)) {
            return null;
        }

        $uploadDir = public_path('uploads/' . $subDir);
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $newName = uniqid('file_', true) . '.' . $extension;
        $destPath = $uploadDir . DIRECTORY_SEPARATOR . $newName;

        if (move_uploaded_file($file['tmp_name'], $destPath)) {
            return 'uploads/' . $subDir . '/' . $newName;
        }

        return null;
    }

    protected function deleteFile(?string $filePath): bool
    {
        if (!$filePath) return false;
        $fullPath = public_path($filePath);
        if (file_exists($fullPath)) {
            return unlink($fullPath);
        }
        return false;
    }
}
