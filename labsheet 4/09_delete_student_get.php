<?php
declare(strict_types=1);

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$confirmed = ($_GET['confirm'] ?? '') === '1';
$message = '';

if ($id === false || $id === null) {
    http_response_code(400);
    $message = 'Provide a valid student ID in the URL, for example ?id=1.';
} elseif (!$confirmed) {
    $message = 'Confirm deletion of student ' . $id . '?';
} else {
    $user = getenv('COLLEGE_ADMIN_DB_USER');
    $password = getenv('COLLEGE_ADMIN_DB_PASSWORD');
    if (!is_string($user) || $user === '' || $password === false) {
        throw new RuntimeException('Set COLLEGE_ADMIN_DB_USER and COLLEGE_ADMIN_DB_PASSWORD for this exercise.');
    }
    $host = getenv('COLLEGE_DB_HOST') ?: '127.0.0.1';
    $port = getenv('COLLEGE_DB_PORT') ?: '3306';
    $pdo = new PDO(
        sprintf('mysql:host=%s;port=%s;dbname=college_db;charset=utf8mb4', $host, $port),
        $user,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
    $statement = $pdo->prepare('DELETE FROM students WHERE id = :id');
    $statement->execute(['id' => $id]);
    $message = $statement->rowCount() === 1 ? 'Student deleted.' : 'No student matched that ID.';
}
?>
<!doctype html>
<html lang="en">
<head><meta charset="utf-8"><title>Delete student</title></head>
<body>
  <h1>Delete student (GET exercise)</h1>
  <p role="status"><?= e($message) ?></p>
  <?php if (is_int($id) && !$confirmed): ?>
    <p><a href="?id=<?= $id ?>&amp;confirm=1">Confirm deletion</a></p>
  <?php endif; ?>
  <p>GET-based deletion is for this assignment only. Use authenticated POST with CSRF protection in real applications.</p>
</body>
</html>
