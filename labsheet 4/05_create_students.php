<?php
declare(strict_types=1);

require_once __DIR__ . '/00_database.php';

if (getenv('COLLEGE_DB_SETUP') !== '1') {
    http_response_code(403);
    exit('For initial setup only: configure COLLEGE_DB_SETUP=1 and an authorized database account.');
}

db()->exec(
    'CREATE TABLE IF NOT EXISTS students (
        id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(120) NOT NULL,
        email VARCHAR(254) NOT NULL UNIQUE,
        enrollment_date DATE NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
);

echo 'The students table is ready. Remove COLLEGE_DB_SETUP and restore the restricted account.';
