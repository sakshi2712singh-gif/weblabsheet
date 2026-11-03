<form method="post" action="/register" class="form-card">
  <?= csrf_field() ?>
  <?php if ($error !== ''): ?><p class="error" role="alert"><?= escape($error) ?></p><?php endif; ?>
  <label>Name <input name="name" maxlength="120" autocomplete="name" required></label>
  <label>Email <input type="email" name="email" maxlength="254" autocomplete="email" required></label>
  <label>Password (at least 12 characters) <input type="password" name="password" minlength="12" maxlength="4096" autocomplete="new-password" required></label>
  <button type="submit">Register</button>
</form>
