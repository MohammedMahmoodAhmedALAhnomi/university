<?php

namespace App\Config;

use PDO;
use PDOException;

class Database
{
    private static ?Database $instance = null;
    private ?PDO $connection = null;

    private function __construct()
    {
        $config = require __DIR__ . '/../../config/database.php';
        try {
            $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset={$config['charset']}";
            $this->connection = new PDO($dsn, $config['username'], $config['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            die('Database connection failed: ' . $e->getMessage());
        }
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection(): PDO
    {
        return $this->connection;
    }

    public static function raw(string $sql, array $params = []): \PDOStatement
    {
        $stmt = self::getInstance()->getConnection()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public static function fetch(string $sql, array $params = [])
    {
        return self::raw($sql, $params)->fetch();
    }

    public static function fetchAll(string $sql, array $params = []): array
    {
        return self::raw($sql, $params)->fetchAll();
    }

    public static function insert(string $table, array $data): int
    {
        if ($table === 'files' && isset($data['doctor_name'])) {
            try {
                self::raw("ALTER TABLE files ADD COLUMN doctor_name VARCHAR(255) DEFAULT NULL AFTER description");
            } catch (\Throwable $t) {}
        }

        try {
            $columns = implode(', ', array_keys($data));
            $placeholders = ':' . implode(', :', array_keys($data));
            $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
            self::raw($sql, $data);
            return (int) self::getInstance()->getConnection()->lastInsertId();
        } catch (\PDOException $e) {
            if ($table === 'files' && isset($data['doctor_name']) && str_contains($e->getMessage(), 'doctor_name')) {
                unset($data['doctor_name']);
                $columns = implode(', ', array_keys($data));
                $placeholders = ':' . implode(', :', array_keys($data));
                $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
                self::raw($sql, $data);
                return (int) self::getInstance()->getConnection()->lastInsertId();
            }
            throw $e;
        }
    }

    public static function update(string $table, array $data, string $where, array $whereParams = []): int
    {
        if ($table === 'files' && isset($data['doctor_name'])) {
            try {
                self::raw("ALTER TABLE files ADD COLUMN doctor_name VARCHAR(255) DEFAULT NULL AFTER description");
            } catch (\Throwable $t) {}
        }

        try {
            $sets = '';
            foreach (array_keys($data) as $col) {
                $sets .= "{$col} = :{$col}, ";
            }
            $sets = rtrim($sets, ', ');
            $sql = "UPDATE {$table} SET {$sets} WHERE {$where}";
            $params = array_merge($data, $whereParams);
            return self::raw($sql, $params)->rowCount();
        } catch (\PDOException $e) {
            if ($table === 'files' && isset($data['doctor_name']) && str_contains($e->getMessage(), 'doctor_name')) {
                unset($data['doctor_name']);
                $sets = '';
                foreach (array_keys($data) as $col) {
                    $sets .= "{$col} = :{$col}, ";
                }
                $sets = rtrim($sets, ', ');
                $sql = "UPDATE {$table} SET {$sets} WHERE {$where}";
                $params = array_merge($data, $whereParams);
                return self::raw($sql, $params)->rowCount();
            }
            throw $e;
        }
    }

    public static function delete(string $table, string $where, array $params = []): int
    {
        $sql = "DELETE FROM {$table} WHERE {$where}";
        return self::raw($sql, $params)->rowCount();
    }

    public static function table(string $table): string
    {
        return $table;
    }
}
