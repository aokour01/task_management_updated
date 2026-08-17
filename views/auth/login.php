<h1>Log in</h1>

<?php if (!empty($errors)): ?>
<ul class="errors">
    <?php foreach ($errors as $error): ?>
        <li><?= htmlspecialchars($error) ?></li>
    <?php endforeach; ?>
</ul>
<?php endif; ?>

<form method="POST" action="/login">
    <label>Email
        <input type="email" name="email" value="<?= htmlspecialchars($email ?? '') ?>">
    </label>
    <label>Password
        <input type="password" name="password">
    </label>
    <button type="submit">Log in</button>
</form>

<p>Need an account? <a href="/register">Register</a></p>
