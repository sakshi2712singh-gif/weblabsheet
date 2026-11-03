<?php
// Question 15: Write a secure file upload handler that checks MIME type and size, renames files, and stores them outside the public web root.
declare(strict_types=1);

require_login();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_valid_csrf();
    $file = $_FILES['photo'] ?? null;
    if (!is_array($file) || ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Choose a file smaller than 2 MiB to upload.');
    }
    if (($file['size'] ?? PHP_INT_MAX) > 2 * 1024 * 1024 || !is_uploaded_file($file['tmp_name'] ?? '')) {
        throw new RuntimeException('The uploaded image is too large or invalid.');
    }
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
    $extensions = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    $image = @getimagesize($file['tmp_name']);
    if (!is_string($mime) || !isset($extensions[$mime]) || $image === false || $image['mime'] !== $mime) {
        throw new RuntimeException('Only JPEG, PNG, and WebP images are accepted.');
    }
    $directory = dirname(__DIR__) . '/storage/uploads';
    if (!is_dir($directory) && !mkdir($directory, 0700, true) && !is_dir($directory)) {
        throw new RuntimeException('Could not create the private upload directory.');
    }
    $filename = bin2hex(random_bytes(24)) . '.' . $extensions[$mime];
    if (!move_uploaded_file($file['tmp_name'], $directory . '/' . $filename)) {
        throw new RuntimeException('Could not store the uploaded image.');
    }
    $notice = 'Upload saved outside the public web root.';
}
?>
<?php render('auth/upload', ['notice' => $notice ?? null]); ?>
