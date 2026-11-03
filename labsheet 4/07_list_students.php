<?php
declare(strict_types=1);

require_once __DIR__ . '/00_database.php';
$students = db()->query(
    'SELECT id, name, email, enrollment_date FROM students ORDER BY id'
)->fetchAll();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Students</title>
  <style>
    body{font:16px/1.5 system-ui,sans-serif;margin:2rem;color:#18323a}
    table{border-collapse:collapse;width:min(100%,800px)}
    th,td{border:1px solid #9aacae;padding:.65rem;text-align:left}
    th{background:#e8f1ef}
    tr:nth-child(even){background:#f6f9f8}
  </style>
</head>
<body>
  <h1>Student records</h1>
  <table>
    <thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Enrollment date</th></tr></thead>
    <tbody>
      <?php foreach ($students as $student): ?>
        <tr>
          <td><?= (int) $student['id'] ?></td>
          <td><?= e($student['name']) ?></td>
          <td><?= e($student['email']) ?></td>
          <td><?= e($student['enrollment_date']) ?></td>
        </tr>
      <?php endforeach; ?>
      <?php if ($students === []): ?><tr><td colspan="4">No students found.</td></tr><?php endif; ?>
    </tbody>
  </table>
</body>
</html>
