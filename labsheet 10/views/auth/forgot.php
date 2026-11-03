<form method="post" action="/forgot-password" class="form-card">
  <?= csrf_field() ?>
  <label>Email <input type="email" name="email" maxlength="254" autocomplete="email" required></label>
  <button type="submit">Send reset link</button>
</form>
