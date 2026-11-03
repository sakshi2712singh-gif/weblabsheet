<?php
declare(strict_types=1);

$themes = [
    'light' => '#ffffff',
    'blue' => '#e8f2ff',
    'green' => '#e8f6ee',
];
$theme = 'light';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $choice = (string) ($_POST['theme'] ?? '');
    if (!array_key_exists($choice, $themes)) {
        http_response_code(400);
        $message = 'Choose one of the available themes.';
    } else {
        setcookie('theme', $choice, [
            'expires' => time() + 60 * 60 * 24 * 30,
            'path' => '/',
            'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        $theme = $choice;
        $message = 'Preference saved for 30 days.';
    }
} elseif (isset($_COOKIE['theme']) && is_string($_COOKIE['theme'])
    && array_key_exists($_COOKIE['theme'], $themes)) {
    $theme = $_COOKIE['theme'];
}
?>
<!doctype html>
<html lang="en">
<head><meta charset="utf-8"><title>Theme preference cookie</title></head>
<body style="background:<?= e($themes[$theme]) ?>;min-height:100vh;padding:2rem">
  <h1>Theme preference</h1>
  <?php if ($message !== ''): ?><p role="status"><?= e($message) ?></p><?php endif; ?>
  <p>Current theme: <?= e($theme) ?>. The cookie is read on later requests.</p>
  <form method="post">
    <label for="theme">Choose a theme</label>
    <select name="theme" id="theme">
      <?php foreach ($themes as $name => $color): ?>
        <option value="<?= e($name) ?>" <?= $theme === $name ? 'selected' : '' ?>><?= e(ucfirst($name)) ?></option>
      <?php endforeach; ?>
    </select>
    <button type="submit">Save preference</button>
  </form>
</body>
</html>
