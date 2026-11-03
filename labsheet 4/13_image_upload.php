<?php
declare(strict_types=1);

require_once __DIR__ . '/00_database.php';
$message = '';
$maxBytes = 2 * 1024 * 1024;
$allowedTypes = [
    'jpg' => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'png' => 'image/png',
    'gif' => 'image/gif',
    'webp' => 'image/webp',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $file = $_FILES['image'] ?? null;
    if (!is_array($file) || !isset($file['error'], $file['tmp_name'], $file['name'], $file['size'])
        || $file['error'] !== UPLOAD_ERR_OK || !is_string($file['tmp_name'])
        || !is_string($file['name']) || !is_int($file['size'])
        || !is_uploaded_file($file['tmp_name'])) {
        $message = 'Choose an image that uploaded successfully.';
    } elseif ($file['size'] < 1 || $file['size'] > $maxBytes) {
        $message = 'The image must be smaller than 2 MB.';
    } else {
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
        if (!isset($allowedTypes[$extension]) || $mime !== $allowedTypes[$extension]) {
            $message = 'Only valid JPG, PNG, GIF, and WebP images are allowed.';
        } else {
            $directory = __DIR__ . '/uploads';
            if (!is_dir($directory) && !mkdir($directory, 0750) && !is_dir($directory)) {
                throw new RuntimeException('Could not create the uploads directory.');
            }
            $filename = bin2hex(random_bytes(16)) . '.' . $extension;
            if (!move_uploaded_file($file['tmp_name'], $directory . DIRECTORY_SEPARATOR . $filename)) {
                throw new RuntimeException('The validated image could not be saved.');
            }
            $message = 'Image saved as uploads/' . $filename . '.';
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head><meta charset="utf-8"><title>Image upload</title></head>
<body>
  <h1>Upload an image</h1>
  <?php if ($message !== ''): ?><p role="status"><?= e($message) ?></p><?php endif; ?>
  <form method="post" enctype="multipart/form-data">
    <input type="hidden" name="MAX_FILE_SIZE" value="<?= $maxBytes ?>">
    <label>Image (JPG, PNG, GIF, or WebP; up to 2 MB)
      <input type="file" name="image" accept=".jpg,.jpeg,.png,.gif,.webp" required>
    </label>
    <button type="submit">Upload</button>
  </form>
</body>
</html>
