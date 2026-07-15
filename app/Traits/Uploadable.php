<?php

namespace App\Traits;

trait Uploadable
{
    protected function uploadFile(array $file, string $subDir = 'files'): ?string
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $uploadDir = public_path('assets/uploads/' . $subDir);
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $newName = uniqid('file_', true) . '.' . $extension;
        $destPath = $uploadDir . DIRECTORY_SEPARATOR . $newName;

        if (move_uploaded_file($file['tmp_name'], $destPath)) {
            return 'assets/uploads/' . $subDir . '/' . $newName;
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
