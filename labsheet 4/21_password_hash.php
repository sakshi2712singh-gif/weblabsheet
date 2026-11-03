<?php
declare(strict_types=1);

require_once __DIR__ . '/00_database.php';
if (getenv('COLLEGE_DB_SETUP') === '1') {
    db()->exec(
        'CREATE TABLE IF NOT EXISTS users (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(80) NOT NULL UNIQUE,
            password_hash VARCHAR(255) NOT NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
    );
}

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = (string) ($_POST['action'] ?? '');
    $username = trim((string) ($_POST['username'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    if ($username === '' || strlen($username) > 80 || strlen($password) < 12 || strlen($password) > 4096) {
        $message = 'Enter a username (up to 80 characters) and a password of 12-4096 characters.';
    } elseif ($action === 'register') {
        try {
            $statement = db()->prepare(
                'INSERT INTO users (username, password_hash) VALUES (:username, :password_hash)'
            );
            $statement->execute([
                'username' => $username,
                'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            ]);
            $message = 'Account created. The database stores a password hash, not the password.';
        } catch (PDOException $exception) {
            if ($exception->getCode() === '23000') {
                $message = 'That username is already registered.';
            } else {
                throw $exception;
            }
        }
    } elseif ($action === 'verify') {
        $statement = db()->prepare('SELECT password_hash FROM users WHERE username = :username LIMIT 1');
        $statement->execute(['username' => $username]);
        $hash = $statement->fetchColumn();
        $message = is_string($hash) && password_verify($password, $hash)
            ? 'Password verified successfully.'
            : 'Invalid username or password.';
    } else {
        http_response_code(400);
        $message = 'Choose register or verify.';
    }
}
?>
<!doctype html>
<html lang="en">
<head><meta charset="utf-8"><title>Password hashing</title></head>
<body>
  <h1>Secure password storage</h1>
  <?php if ($message !== ''): ?><p role="status"><?= e($message) ?></p><?php endif; ?>
  <form method="post">
    <label>Username <input name="username" maxlength="80" autocomplete="username" required></label><br>
    <label>Password <input type="password" name="password" minlength="12" maxlength="4096" required></label><br>
    <button type="submit" name="action" value="register">Register</button>
    <button type="submit" name="action" value="verify">Verify password</button>
  </form>
</body>
</html>
