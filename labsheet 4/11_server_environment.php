<?php
declare(strict_types=1);

$keys = [
    'SERVER_SOFTWARE',
    'SERVER_NAME',
    'SERVER_PORT',
    'DOCUMENT_ROOT',
    'SCRIPT_NAME',
    'REQUEST_METHOD',
    'REQUEST_URI',
    'REMOTE_ADDR',
    'HTTP_HOST',
];
?>
<!doctype html>
<html lang="en">
<head><meta charset="utf-8"><title>Apache server environment</title></head>
<body>
  <h1>Apache server environment</h1>
  <dl>
    <?php foreach ($keys as $key): ?>
      <dt><?= e($key) ?></dt>
      <dd><?= e((string) ($_SERVER[$key] ?? '(not set)')) ?></dd>
    <?php endforeach; ?>
  </dl>
</body>
</html>
