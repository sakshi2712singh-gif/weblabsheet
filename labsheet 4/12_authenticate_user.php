<?php
declare(strict_types=1);

require_once __DIR__ . '/00_database.php';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim((string) ($_POST['username'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    if ($username === '' || strlen($username) > 80 || $password === '' || strlen($password) > 4096) {
        $message = 'Enter your username and password.';
    } else {
        $statement = db()->prepare(
            'SELECT password_hash FROM users WHERE username = :username LIMIT 1'
        );
        $statement->execute(['username' => $username]);
        $hash = $statement->fetchColumn();
        $message = is_string($hash) && password_verify($password, $hash)
            ? 'Authentication successful.'
            : 'Invalid username or password.';
    }
}
?>
<!doctype html>
<html lang="en">
<head><meta charset="utf-8"><title>User authentication</title></head>
<body>
  <h1>Sign in</h1>
  <?php if ($message !== ''): ?><p role="status"><?= e($message) ?></p><?php endif; ?>
  <form method="post">
    <label>Username <input name="username" maxlength="80" autocomplete="username" required></label><br>
    <label>Password <input type="password" name="password" autocomplete="current-password" required></label><br>
    <button type="submit">Sign in</button>
  </form>
  <p>Create a test account with 21_password_hash.php first.</p>
</body>
</html>
