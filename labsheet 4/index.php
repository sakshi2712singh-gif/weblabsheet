<?php
declare(strict_types=1);

$server = $_SERVER['SERVER_SOFTWARE'] ?? 'Apache server details are unavailable';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Assignment 4 - Apache and PHP</title>
</head>
<body>
  <h1>Apache and PHP are working</h1>
  <p>This default index page was served by <?= htmlspecialchars($server, ENT_QUOTES, 'UTF-8') ?>.</p>
  <p>PHP version: <?= htmlspecialchars(PHP_VERSION, ENT_QUOTES, 'UTF-8') ?></p>
</body>
</html>
