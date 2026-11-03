<?php
declare(strict_types=1);

require_once __DIR__ . '/00_database.php';

try {
    db()->query('SELECT 1');
    echo 'Connected to college_db successfully using PDO.';
} catch (PDOException $exception) {
    error_log('PDO connection check failed: ' . $exception->getMessage());
    http_response_code(500);
    echo 'Connection failed. Check the Apache error log and database settings.';
}
