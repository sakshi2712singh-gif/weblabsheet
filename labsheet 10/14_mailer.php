<?php
// Question 14: Send registration confirmation and password-reset emails from PHP using PHPMailer (SMTP).
declare(strict_types=1);

function send_application_email(string $to, string $subject, string $text): void
{
    $autoload = __DIR__ . '/vendor/autoload.php';
    if (!is_file($autoload)) {
        throw new RuntimeException('Install PHPMailer with Composer before sending email.');
    }
    require_once $autoload;
    $host = getenv('SMTP_HOST');
    $username = getenv('SMTP_USER');
    $password = getenv('SMTP_PASSWORD');
    $from = getenv('SMTP_FROM');
    if (!$host || !$username || !$password || !$from) {
        throw new RuntimeException('Configure SMTP_HOST, SMTP_USER, SMTP_PASSWORD, and SMTP_FROM.');
    }
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = $host;
    $mail->SMTPAuth = true;
    $mail->Username = $username;
    $mail->Password = $password;
    $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = (int) (getenv('SMTP_PORT') ?: 587);
    $mail->CharSet = 'UTF-8';
    $mail->setFrom($from);
    $mail->addAddress($to);
    $mail->Subject = $subject;
    $mail->Body = $text;
    $mail->isHTML(false);
    $mail->send();
}
