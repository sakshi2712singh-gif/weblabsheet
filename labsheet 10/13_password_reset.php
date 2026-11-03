<?php
// Question 13: Implement a "Forgot Password" feature using a unique, time-limited reset token stored in the database.
declare(strict_types=1);

function create_password_reset(PDO $pdo, int $userId): string
{
    $token = bin2hex(random_bytes(32));
    $statement = $pdo->prepare(
        'INSERT INTO password_reset_tokens (user_id, token_hash, expires_at)
         VALUES (:user_id, :token_hash, :expires_at)'
    );
    $statement->execute([
        'user_id' => $userId,
        'token_hash' => hash('sha256', $token),
        'expires_at' => gmdate('Y-m-d H:i:s', time() + 3600),
    ]);
    return $token;
}

function consume_password_reset(PDO $pdo, string $token, string $password): bool
{
    if (!preg_match('/^[a-f0-9]{64}$/', $token) || strlen($password) < 12 || strlen($password) > 4096) {
        return false;
    }
    $pdo->beginTransaction();
    try {
        $query = $pdo->prepare(
            'SELECT id, user_id FROM password_reset_tokens
             WHERE token_hash = :hash AND expires_at > UTC_TIMESTAMP() FOR UPDATE'
        );
        $query->execute(['hash' => hash('sha256', $token)]);
        $row = $query->fetch();
        if ($row === false) {
            $pdo->rollBack();
            return false;
        }
        $update = $pdo->prepare(
            'UPDATE users SET password_hash = :hash, failed_login_attempts = 0, locked_until = NULL WHERE id = :id'
        );
        $update->execute(['hash' => password_hash($password, PASSWORD_DEFAULT), 'id' => $row['user_id']]);
        $delete = $pdo->prepare('DELETE FROM password_reset_tokens WHERE user_id = :user_id');
        $delete->execute(['user_id' => $row['user_id']]);
        $pdo->commit();
        return true;
    } catch (Throwable $exception) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $exception;
    }
}
