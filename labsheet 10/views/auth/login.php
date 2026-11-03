<form method="post" action="/login" class="form-card">
  <?= csrf_field() ?>
  <?php if ($error !== ''): ?><p class="error" role="alert"><?= escape($error) ?></p><?php endif; ?>
  <label>Email <input type="email" name="email" maxlength="254" autocomplete="email" required></label>
  <label>Password <input type="password" name="password" maxlength="4096" autocomplete="current-password" required></label>
  <button type="submit">Sign in</button>
</form>
<p><a href="/forgot-password">Forgot password?</a></p>
