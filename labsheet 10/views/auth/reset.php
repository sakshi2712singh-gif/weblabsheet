<form method="post" action="/reset-password" class="form-card">
  <?= csrf_field() ?>
  <input type="hidden" name="token" value="<?= escape($token) ?>">
  <?php if ($error !== ''): ?><p class="error" role="alert"><?= escape($error) ?></p><?php endif; ?>
  <label>New password <input type="password" name="password" minlength="12" maxlength="4096" autocomplete="new-password" required></label>
  <button type="submit">Update password</button>
</form>
