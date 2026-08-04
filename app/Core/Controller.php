<?php

namespace App\Core;

abstract class Controller
{
    protected function view(string $view, array $data = []): void
    {
        View::render($view, $data);
    }

    protected function json($data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    protected function redirect(string $url): void
    {
        redirect($url);
    }

    protected function back(): void
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? url('/');
        $this->redirect($referer);
    }

    protected function validate(array $data, array $rules): array
    {
        $errors = [];
        foreach ($rules as $field => $ruleSet) {
            $value = $data[$field] ?? '';
            $ruleList = explode('|', $ruleSet);

            foreach ($ruleList as $rule) {
                if ($rule === 'required' && empty($value) && $value !== '0') {
                    $errors[$field][] = "حقل {$field} مطلوب";
                }
                if (str_starts_with($rule, 'min:')) {
                    $min = (int) explode(':', $rule)[1];
                    if (mb_strlen($value) < $min) {
                        $errors[$field][] = "حقل {$field} يجب أن يكون {$min} حرفًا على الأقل";
                    }
                }
                if (str_starts_with($rule, 'max:')) {
                    $max = (int) explode(':', $rule)[1];
                    if (mb_strlen($value) > $max) {
                        $errors[$field][] = "حقل {$field} يجب أن لا يتجاوز {$max} حرفًا";
                    }
                }
                if ($rule === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $errors[$field][] = "حقل {$field} يجب أن يكون بريدًا إلكترونيًا صالحًا";
                }
                if ($rule === 'numeric' && !is_numeric($value)) {
                    $errors[$field][] = "حقل {$field} يجب أن يكون رقمًا";
                }
            }
        }
        return $errors;
    }

    protected function isPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    protected function isGet(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }

    protected function getParam(string $key, $default = null)
    {
        return $_GET[$key] ?? $default;
    }

    protected function postParam(string $key, $default = null)
    {
        return $_POST[$key] ?? $default;
    }

    protected function fileParam(string $key): ?array
    {
        return $_FILES[$key] ?? null;
    }
}
