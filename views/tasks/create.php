<h1>New task</h1>

<?php if (!empty($errors)): ?>
<ul class="errors">
    <?php foreach ($errors as $error): ?>
        <li><?= htmlspecialchars($error) ?></li>
    <?php endforeach; ?>
</ul>
<?php endif; ?>

<form method="POST" action="/tasks">
    <label>Title
        <input type="text" name="title" value="<?= htmlspecialchars($title ?? '') ?>">
    </label>
    <label>Description
        <textarea name="description" rows="4"><?= htmlspecialchars($description ?? '') ?></textarea>
    </label>
    <button type="submit">Create</button>
</form>
