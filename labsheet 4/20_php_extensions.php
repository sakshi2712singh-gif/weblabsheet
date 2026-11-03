<?php
declare(strict_types=1);

$required = ['mysqli', 'pdo_mysql', 'mbstring', 'curl', 'fileinfo'];
?>
<!doctype html>
<html lang="en">
<head><meta charset="utf-8"><title>PHP extension status</title></head>
<body>
  <h1>PHP extension status</h1>
  <p>PHP <?= htmlspecialchars(PHP_VERSION, ENT_QUOTES, 'UTF-8') ?></p>
  <ul>
    <?php foreach ($required as $extension): ?>
      <li><?= htmlspecialchars($extension, ENT_QUOTES, 'UTF-8') ?>:
        <?= extension_loaded($extension) ? 'enabled' : 'NOT enabled' ?></li>
    <?php endforeach; ?>
  </ul>
  <p>In the active php.ini, uncomment the matching extension directives (for example
  extension=mysqli, extension=pdo_mysql, extension=mbstring, extension=curl,
  extension=fileinfo), then
  restart Apache. Confirm the Loaded Configuration File in question 2 if changes
  do not appear.</p>
</body>
</html>
