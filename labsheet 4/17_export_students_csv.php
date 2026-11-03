<?php
declare(strict_types=1);

require_once __DIR__ . '/00_database.php';

function csvSafe(string $value): string
{
    if (preg_match('/^[\s]*[=+\-@]/', $value) === 1) {
        return "'" . $value;
    }
    return $value;
}

$students = db()->query(
    'SELECT id, name, email, enrollment_date FROM students ORDER BY id'
)->fetchAll();

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="students.csv"');
header('X-Content-Type-Options: nosniff');
$output = fopen('php://output', 'wb');
if ($output === false) {
    throw new RuntimeException('Could not open the CSV response stream.');
}
fputcsv($output, ['ID', 'Name', 'Email', 'Enrollment date'], ',', '"', '');
foreach ($students as $student) {
    fputcsv($output, [
        (string) $student['id'],
        csvSafe((string) $student['name']),
        csvSafe((string) $student['email']),
        (string) $student['enrollment_date'],
    ], ',', '"', '');
}
fclose($output);
