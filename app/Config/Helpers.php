<?php

if (!function_exists('env')) {
    function env(string $key, $default = null)
    {
        $value = getenv($key);
        if ($value === false) {
            return $default;
        }
        switch (strtolower($value)) {
            case 'true':
                return true;
            case 'false':
                return false;
            case 'null':
                return null;
        }
        return $value;
    }
}

if (!function_exists('base_path')) {
    function base_path(string $path = ''): string
    {
        $base = dirname(__DIR__);
        return $path ? $base . DIRECTORY_SEPARATOR . ltrim($path, '/\\') : $base;
    }
}

if (!function_exists('view_path')) {
    function view_path(string $path = ''): string
    {
        return base_path('Views' . DIRECTORY_SEPARATOR . ltrim($path, '/\\'));
    }
}

if (!function_exists('public_path')) {
    function public_path(string $path = ''): string
    {
        $base = dirname(__DIR__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'public';
        return $path ? $base . DIRECTORY_SEPARATOR . ltrim($path, '/\\') : $base;
    }
}

if (!function_exists('asset')) {
    function asset(string $path): string
    {
        $appUrl = env('APP_URL', 'http://localhost/university_system');
        return rtrim($appUrl, '/') . '/' . ltrim($path, '/');
    }
}

if (!function_exists('url')) {
    function url(string $path = ''): string
    {
        $appUrl = env('APP_URL', 'http://localhost/university_system');
        return rtrim($appUrl, '/') . '/' . ltrim($path, '/');
    }
}

if (!function_exists('redirect')) {
    function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }
}

if (!function_exists('old')) {
    function old(string $key, $default = '')
    {
        return $_SESSION['_old_input'][$key] ?? $default;
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token(): string
    {
        if (empty($_SESSION['_csrf_token'])) {
            $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['_csrf_token'];
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field(): string
    {
        return '<input type="hidden" name="_csrf_token" value="' . csrf_token() . '">';
    }
}

if (!function_exists('verify_csrf')) {
    function verify_csrf(): bool
    {
        $token = $_POST['_csrf_token'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
        return hash_equals($_SESSION['_csrf_token'] ?? '', $token);
    }
}

if (!function_exists('flash')) {
    function flash(string $key, ?string $value = null)
    {
        if ($value !== null) {
            $_SESSION['_flash'][$key] = $value;
            return;
        }
        $val = $_SESSION['_flash'][$key] ?? null;
        unset($_SESSION['_flash'][$key]);
        return $val;
    }
}

if (!function_exists('flash_has')) {
    function flash_has(string $key): bool
    {
        return isset($_SESSION['_flash'][$key]);
    }
}

if (!function_exists('str_slug')) {
    function str_slug(string $string): string
    {
        $string = mb_strtolower($string, 'UTF-8');
        $string = preg_replace('/[^\w\s\-]/u', '', $string);
        $string = preg_replace('/[\s\-]+/', '-', $string);
        return trim($string, '-');
    }
}

if (!function_exists('truncate')) {
    function truncate(?string $text, int $length = 100, string $append = '...'): string
    {
        $text = $text ?? '';
        if (mb_strlen($text) <= $length) {
            return $text;
        }
        return mb_substr($text, 0, $length) . $append;
    }
}

if (!function_exists('escape')) {
    function escape(?string $value): string
    {
        return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('format_date')) {
    function format_date(?string $date, string $format = 'Y-m-d'): string
    {
        if (!$date) return '';
        $dt = new DateTime($date);
        return $dt->format($format);
    }
}

if (!function_exists('is_active_route')) {
    function is_active_route(string $path): string
    {
        $currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        $basePath = dirname($_SERVER['SCRIPT_NAME']);
        $normalized = str_replace($basePath, '', $currentPath);
        return $normalized === $path ? 'active' : '';
    }
}

if (!function_exists('log_activity')) {
    function log_activity(string $action, ?string $table = null, ?int $recordId = null, ?string $details = null): void
    {
        $userId = $_SESSION['user_id'] ?? null;
        $ip = $_SERVER['REMOTE_ADDR'] ?? null;
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;

        \App\Config\Database::insert('activity_logs', [
            'user_id' => $userId,
            'action' => $action,
            'table_name' => $table,
            'record_id' => $recordId,
            'details' => $details,
            'ip_address' => $ip,
            'user_agent' => $userAgent,
        ]);
    }
}

if (!function_exists('get_managed_level_id')) {
    function get_managed_level_id(): ?int
    {
        if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'manager' && isset($_SESSION['managed_level_id'])) {
            return (int)$_SESSION['managed_level_id'];
        }
        return null;
    }
}

if (!function_exists('get_managed_major_id')) {
    function get_managed_major_id(): ?int
    {
        if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'manager' && isset($_SESSION['managed_major_id'])) {
            return (int)$_SESSION['managed_major_id'];
        }
        return null;
    }
}

if (!function_exists('is_manager')) {
    function is_manager(): bool
    {
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'manager';
    }
}
