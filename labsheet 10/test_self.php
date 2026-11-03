<?php
declare(strict_types=1);

require_once __DIR__ . '/11_validation.php';
require_once __DIR__ . '/09_csrf.php';
require_once __DIR__ . '/16_error_handling.php';
require_once __DIR__ . '/05_Database.php';

$checks = 0;
$assert = static function (bool $condition, string $message) use (&$checks): void {
    if (!$condition) {
        throw new RuntimeException($message);
    }
    $checks++;
};

$assert(validate_email('Student@example.com') === 'student@example.com', 'Email validation failed.');
$assert(validate_url('https://example.com/a') === 'https://example.com/a', 'HTTPS URL validation failed.');
$assert(validate_integer('12', 1, 20) === 12, 'Integer validation failed.');
$product = validate_product(['name' => 'Pen', 'description' => 'Blue pen', 'price' => '2.5']);
$assert($product['price'] === '2.50', 'Product price normalization failed.');
$assert(validate_plain_string("A\u{0007}B") === 'AB', 'String control-character sanitization failed.');
$rejected = false;
try {
    validate_email('invalid email');
} catch (InvalidArgumentException) {
    $rejected = true;
}
$assert($rejected, 'Invalid email should be rejected.');
$rejected = false;
try {
    validate_url('javascript:alert(1)');
} catch (InvalidArgumentException) {
    $rejected = true;
}
$assert($rejected, 'Non-HTTPS URL should be rejected.');

$_SESSION = [];
$token = csrf_token();
$assert(strlen($token) === 64 && hash_equals($token, csrf_token()), 'CSRF token generation/storage failed.');
$_POST['csrf_token'] = $token;
require_valid_csrf();
$_POST['csrf_token'] = str_repeat('0', 64);
$rejected = false;
try {
    require_valid_csrf();
} catch (RuntimeException) {
    $rejected = true;
}
$assert($rejected, 'Invalid CSRF token should be rejected.');
$assert(password_verify('example-password', password_hash('example-password', PASSWORD_DEFAULT)), 'Password hash verification failed.');
$pdo = new PDO('sqlite::memory:', null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$pdo->exec('CREATE TABLE products (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT, description TEXT, price NUMERIC, image_path TEXT)');
$products = new Product($pdo);
$id = $products->create(['name' => 'Notebook', 'description' => 'Ruled pages', 'price' => '3.25']);
$assert($id > 0 && $products->find($id)['name'] === 'Notebook', 'Product create/read failed.');
$assert($products->update($id, ['name' => 'Journal', 'description' => 'Hard cover', 'price' => '4.50']), 'Product update failed.');
$assert($products->find($id)['price'] == 4.5, 'Updated product was not persisted.');
$assert($products->delete($id) && $products->find($id) === null, 'Product delete failed.');
$assert(str_contains(file_get_contents(__DIR__ . '/public/.htaccess'), 'Options -Indexes'), 'Directory listing must be disabled.');
$assert(str_contains(file_get_contents(__DIR__ . '/public/.htaccess'), 'RewriteRule ^ index.php'), 'Clean URL front-controller rule is missing.');

echo "PASS: {$checks} validation, CSRF, password hashing, and routing configuration checks.\n";
