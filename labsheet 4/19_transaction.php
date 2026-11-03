<?php
declare(strict_types=1);

require_once __DIR__ . '/00_database.php';
$pdo = db();
if (getenv('COLLEGE_DB_SETUP') === '1') {
    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS student_events (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            student_id INT UNSIGNED NOT NULL,
            event_name VARCHAR(80) NOT NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            CONSTRAINT fk_student_events_student
                FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
    );
}

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim((string) ($_POST['name'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    $date = (string) ($_POST['enrollment_date'] ?? '');
    $parsedDate = DateTimeImmutable::createFromFormat('!Y-m-d', $date);
    if ($name === '' || strlen($name) > 120 || !filter_var($email, FILTER_VALIDATE_EMAIL)
        || strlen($email) > 254 || !$parsedDate || $parsedDate->format('Y-m-d') !== $date) {
        $message = 'Enter valid student details.';
    } else {
        try {
            $pdo->beginTransaction();
            $insertStudent = $pdo->prepare(
                'INSERT INTO students (name, email, enrollment_date)
                 VALUES (:name, :email, :enrollment_date)'
            );
            $insertStudent->execute([
                'name' => $name,
                'email' => $email,
                'enrollment_date' => $date,
            ]);
            $studentId = (int) $pdo->lastInsertId();
            $insertEvent = $pdo->prepare(
                'INSERT INTO student_events (student_id, event_name)
                 VALUES (:student_id, :event_name)'
            );
            $insertEvent->execute(['student_id' => $studentId, 'event_name' => 'student_created']);
            $pdo->commit();
            $message = 'Student and audit event saved together (ID ' . $studentId . ').';
        } catch (Throwable $exception) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            if ($exception instanceof PDOException && $exception->getCode() === '23000') {
                $message = 'That email address already exists; no changes were saved.';
            } else {
                error_log('Student transaction failed: ' . $exception->getMessage());
                http_response_code(500);
                $message = 'The transaction failed and was rolled back. Check the Apache error log.';
            }
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head><meta charset="utf-8"><title>PDO transaction</title></head>
<body>
  <h1>Multi-table transaction</h1>
  <?php if ($message !== ''): ?><p role="status"><?= e($message) ?></p><?php endif; ?>
  <form method="post">
    <label>Name <input name="name" maxlength="120" required></label><br>
    <label>Email <input type="email" name="email" maxlength="254" required></label><br>
    <label>Enrollment date <input type="date" name="enrollment_date" required></label><br>
    <button type="submit">Save student and event</button>
  </form>
</body>
</html>
