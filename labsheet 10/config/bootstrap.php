<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/10_sessions.php';
start_secure_session();
require_once dirname(__DIR__) . '/09_csrf.php';
require_once dirname(__DIR__) . '/11_validation.php';
require_once dirname(__DIR__) . '/06_view_templates.php';
require_once dirname(__DIR__) . '/16_error_handling.php';
configure_error_handling();
require_once dirname(__DIR__) . '/05_Database.php';
require_once dirname(__DIR__) . '/12_login_limiter.php';
require_once dirname(__DIR__) . '/13_password_reset.php';
require_once dirname(__DIR__) . '/14_mailer.php';
require_once dirname(__DIR__) . '/07_products_api.php';

function db(): PDO
{
    return Database::instance()->pdo();
}

function require_login(): array
{
    $user = $_SESSION['user'] ?? null;
    if (!is_array($user)) {
        header('Location: /login', true, 303);
        exit;
    }
    return $user;
}

function redirect(string $path): never
{
    header('Location: ' . $path, true, 303);
    exit;
}
