<?php
declare(strict_types=1);

require_once __DIR__ . '/00_database.php';

session_set_cookie_params([
    'httponly' => true,
    'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    'samesite' => 'Lax',
]);
session_start();
$message = '';

if (isset($_GET['logout'])) {
    $_SESSION = [];
    session_destroy();
    header('Location: 15_session_login.php', true, 303);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim((string) ($_POST['username'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    $account = null;
    if ($username !== '' && strlen($username) <= 80 && $password !== '' && strlen($password) <= 4096) {
        $statement = db()->prepare('SELECT id, password_hash FROM users WHERE username = :username LIMIT 1');
        $statement->execute(['username' => $username]);
        $account = $statement->fetch();
    }
    if (is_array($account) && password_verify($password, $account['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int) $account['id'];
        $_SESSION['username'] = $username;
        header('Location: 15_session_login.php?page=protected', true, 303);
        exit;
    }
    $message = 'Invalid username or password.';
}

$signedIn = isset($_SESSION['user_id'], $_SESSION['username']);
?>
<!doctype html>
<html lang="en">
<head><meta charset="utf-8"><title>Session login</title></head>
<body>
  <h1>PHP session demonstration</h1>
  <?php if ($message !== ''): ?><p role="alert"><?= e($message) ?></p><?php endif; ?>
  <?php if ($signedIn): ?>
    <p>Signed in as <?= e((string) $_SESSION['username']) ?>.</p>
    <?php if (($_GET['page'] ?? '') === 'protected'): ?>
      <p>This protected page reads the login state saved in the session.</p>
    <?php else: ?>
      <p><a href="15_session_login.php?page=protected">Open protected page</a></p>
    <?php endif; ?>
    <p><a href="15_session_login.php?logout=1">Log out</a></p>
  <?php else: ?>
    <form method="post">
      <label>Username <input name="username" autocomplete="username" required></label><br>
      <label>Password <input type="password" name="password" autocomplete="current-password" required></label><br>
      <button type="submit">Sign in</button>
    </form>
  <?php endif; ?>
</body>
</html>
