<?php

namespace App\Core;

use App\Config\Database;

abstract class Model
{
    protected static string $table;
    protected static string $primaryKey = 'id';

    public static function all(): array
    {
        return Database::fetchAll("SELECT * FROM " . static::$table . " ORDER BY id DESC");
    }

    public static function find(int $id)
    {
        return Database::fetch("SELECT * FROM " . static::$table . " WHERE id = ?", [$id]);
    }

    public static function where(string $column, $value, string $operator = '=')
    {
        return Database::fetchAll("SELECT * FROM " . static::$table . " WHERE {$column} {$operator} ?", [$value]);
    }

    public static function whereFirst(string $column, $value, string $operator = '=')
    {
        return Database::fetch("SELECT * FROM " . static::$table . " WHERE {$column} {$operator} ? LIMIT 1", [$value]);
    }

    public static function create(array $data): int
    {
        return Database::insert(static::$table, $data);
    }

    public static function updateRecord(int $id, array $data): int
    {
        return Database::update(static::$table, $data, "id = :id", ['id' => $id]);
    }

    public static function deleteRecord(int $id): int
    {
        return Database::delete(static::$table, "id = ?", [$id]);
    }

    public static function count(): int
    {
        $result = Database::fetch("SELECT COUNT(*) as count FROM " . static::$table);
        return (int) ($result->count ?? 0);
    }

    public static function paginate(int $page = 1, int $perPage = 10): array
    {
        $offset = ($page - 1) * $perPage;
        $total = static::count();
        $items = Database::fetchAll("SELECT * FROM " . static::$table . " ORDER BY id DESC LIMIT ? OFFSET ?", [$perPage, $offset]);
        return [
            'items' => $items,
            'total' => $total,
            'page' => $page,
            'perPage' => $perPage,
            'totalPages' => ceil($total / $perPage),
        ];
    }
}
