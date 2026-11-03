<?php
declare(strict_types=1);

require_once __DIR__ . '/00_database.php';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    $email = trim((string) ($_POST['email'] ?? ''));
    if ($id === false || $id === null || !filter_var($email, FILTER_VALIDATE_EMAIL)
        || strlen($email) > 254) {
        $message = 'Enter a valid student ID and email address.';
    } else {
        $statement = db()->prepare('UPDATE students SET email = :email WHERE id = :id');
        $statement->execute(['email' => $email, 'id' => $id]);
        $message = $statement->rowCount() > 0
            ? 'Student email updated.'
            : 'No row changed. Check the ID or whether the email was already the same.';
    }
}
?>
<!doctype html>
<html lang="en">
<head><meta charset="utf-8"><title>Update student email</title></head>
<body>
  <h1>Update student email</h1>
  <?php if ($message !== ''): ?><p role="status"><?= e($message) ?></p><?php endif; ?>
  <form method="post">
    <label>Student ID <input type="number" name="id" min="1" required></label><br>
    <label>New email <input type="email" name="email" maxlength="254" required></label><br>
    <button type="submit">Update email</button>
  </form>
</body>
</html>
