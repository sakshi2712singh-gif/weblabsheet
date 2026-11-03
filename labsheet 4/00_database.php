<?php
declare(strict_types=1);

function db(): PDO
{
    static $connection = null;
    if ($connection instanceof PDO) {
        return $connection;
    }

    $host = getenv('COLLEGE_DB_HOST') ?: '127.0.0.1';
    $port = getenv('COLLEGE_DB_PORT') ?: '3306';
    $user = getenv('COLLEGE_DB_USER');
    $password = getenv('COLLEGE_DB_PASSWORD');
    if (!is_string($user) || $user === '' || $password === false) {
        throw new RuntimeException(
            'Set COLLEGE_DB_USER and COLLEGE_DB_PASSWORD for the Apache process.'
        );
    }

    $dsn = sprintf(
        'mysql:host=%s;port=%s;dbname=college_db;charset=utf8mb4',
        $host,
        $port
    );
    $connection = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    return $connection;
}

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
