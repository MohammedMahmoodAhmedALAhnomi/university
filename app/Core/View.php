<?php

namespace App\Core;

use App\Config\Database;

class View
{
    public static function render(string $view, array $data = []): void
    {
        extract($data);

        $viewFile = base_path('Views' . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $view) . '.php');

        if (!file_exists($viewFile)) {
            throw new \RuntimeException("View not found: {$viewFile}");
        }

        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

        if ($isAjax) {
            require $viewFile;
            return;
        }

        $layout = self::getLayout($view);
        if ($layout) {
            $content = self::capture($viewFile, $data);
            $layoutFile = base_path('Views' . DIRECTORY_SEPARATOR . 'layouts' . DIRECTORY_SEPARATOR . $layout . '.php');
            if (file_exists($layoutFile)) {
                require $layoutFile;
            } else {
                echo $content;
            }
        } else {
            require $viewFile;
        }
    }

    private static function getLayout(string $view): ?string
    {
        if (str_starts_with($view, 'admin')) {
            return 'admin';
        }
        if (str_starts_with($view, 'auth')) {
            return null;
        }
        return 'front';
    }

    private static function capture(string $viewFile, array $data): string
    {
        extract($data);
        ob_start();
        require $viewFile;
        return ob_get_clean();
    }

    public static function renderComponent(string $component, array $data = []): void
    {
        $componentFile = base_path('Views' . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . $component . '.php');
        if (file_exists($componentFile)) {
            extract($data);
            require $componentFile;
        }
    }

    public static function getSettings(): array
    {
        $rows = Database::fetchAll("SELECT setting_key, setting_value FROM settings");
        $settings = [];
        foreach ($rows as $row) {
            $settings[$row->setting_key] = $row->setting_value;
        }
        return $settings;
    }
}
