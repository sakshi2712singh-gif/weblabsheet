<?php
declare(strict_types=1);

$start = microtime(true);
$requestId = bin2hex(random_bytes(6));
$requestUri = preg_replace('/[\r\n\x00]/', '', (string) ($_SERVER['REQUEST_URI'] ?? 'unknown'));
error_log(sprintf(
    'Assignment 4 log test request=%s method=%s uri=%s',
    $requestId,
    $_SERVER['REQUEST_METHOD'] ?? 'unknown',
    $requestUri
));
$elapsedMs = (microtime(true) - $start) * 1000;
?>
<!doctype html>
<html lang="en">
<head><meta charset="utf-8"><title>Apache log and timing test</title></head>
<body>
  <h1>Log test request recorded</h1>
  <p>Request ID: <?= htmlspecialchars($requestId, ENT_QUOTES, 'UTF-8') ?></p>
  <p>Page-generation time: <?= number_format($elapsedMs, 3) ?> ms (not a full server benchmark).</p>
  <p>Check Apache access.log for this request and error.log for the matching PHP log entry.</p>
</body>
</html>
