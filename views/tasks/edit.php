<h1>Edit task</h1>

<?php if (!empty($errors)): ?>
<ul class="errors">
    <?php foreach ($errors as $error): ?>
        <li><?= htmlspecialchars($error) ?></li>
    <?php endforeach; ?>
</ul>
<?php endif; ?>

<form method="POST" action="/tasks/<?= (int) $task['id'] ?>">
    <input type="hidden" name="_method" value="PATCH">
    <label>Title
        <input type="text" name="title" value="<?= htmlspecialchars($task['title']) ?>">
    </label>
    <label>Description
        <textarea name="description" rows="4"><?= htmlspecialchars($task['description'] ?? '') ?></textarea>
    </label>
    <label>Status
        <select name="status">
            <option value="pending" <?= $task['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
            <option value="completed" <?= $task['status'] === 'completed' ? 'selected' : '' ?>>Completed</option>
        </select>
    </label>
    <button type="submit">Save</button>
</form>
