<h1>Your tasks</h1>

<form method="GET" action="/tasks">
    <label>Search
        <input type="text" name="search" value="<?= htmlspecialchars($search ?? '') ?>">
    </label>
    <label>Status
        <select name="status">
            <option value="" <?= ($status ?? '') === '' ? 'selected' : '' ?>>All</option>
            <option value="pending" <?= ($status ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
            <option value="completed" <?= ($status ?? '') === 'completed' ? 'selected' : '' ?>>Completed</option>
        </select>
    </label>
    <button type="submit">Filter</button>
</form>

<p><a href="/tasks/create">+ New task</a></p>

<ul class="task-list">
    <?php foreach ($tasks as $task): ?>
        <li>
            <a href="/tasks/<?= (int) $task['id'] ?>"><?= htmlspecialchars($task['title']) ?></a>
            &mdash; <?= htmlspecialchars($task['status']) ?>
        </li>
    <?php endforeach; ?>
    <?php if (empty($tasks)): ?>
        <li>No tasks yet.</li>
    <?php endif; ?>
</ul>
