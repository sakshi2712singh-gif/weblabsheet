<?php
declare(strict_types=1);

require_once __DIR__ . '/00_database.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim((string) ($_POST['name'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    $date = (string) ($_POST['enrollment_date'] ?? '');
    $parsedDate = DateTimeImmutable::createFromFormat('!Y-m-d', $date);

    if ($name === '' || strlen($name) > 120 || !filter_var($email, FILTER_VALIDATE_EMAIL)
        || strlen($email) > 254 || !$parsedDate || $parsedDate->format('Y-m-d') !== $date) {
        $message = 'Enter a name, valid email address, and valid enrollment date.';
    } else {
        $statement = db()->prepare(
            'INSERT INTO students (name, email, enrollment_date)
             VALUES (:name, :email, :enrollment_date)'
        );
        $statement->execute([
            'name' => $name,
            'email' => $email,
            'enrollment_date' => $date,
        ]);
        $message = 'Student added with ID ' . db()->lastInsertId() . '.';
    }
}
?>
<!doctype html>
<html lang="en">
<head><meta charset="utf-8"><title>Add a student</title></head>
<body>
  <h1>Add a student</h1>
  <?php if ($message !== ''): ?><p role="status"><?= e($message) ?></p><?php endif; ?>
  <form method="post">
    <label>Name <input name="name" maxlength="120" required></label><br>
    <label>Email <input type="email" name="email" maxlength="254" required></label><br>
    <label>Enrollment date <input type="date" name="enrollment_date" required></label><br>
    <button type="submit">Save student</button>
  </form>
</body>
</html>
