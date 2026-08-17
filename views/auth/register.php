<h1>Register</h1>

<?php if (!empty($errors)): ?>
<ul class="errors">
    <?php foreach ($errors as $error): ?>
        <li><?= htmlspecialchars($error) ?></li>
    <?php endforeach; ?>
</ul>
<?php endif; ?>

<form method="POST" action="/register">
    <label>Name
        <input type="text" name="name" value="<?= htmlspecialchars($name ?? '') ?>">
    </label>
    <label>Email
        <input type="email" name="email" value="<?= htmlspecialchars($email ?? '') ?>">
    </label>
    <label>Password
        <input type="password" name="password">
    </label>
    <label>Confirm password
        <input type="password" name="password_confirm">
    </label>
    <button type="submit">Register</button>
</form>

<p>Already have an account? <a href="/login">Log in</a></p>
