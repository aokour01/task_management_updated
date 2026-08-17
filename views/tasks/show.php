<h1><?= htmlspecialchars($task['title']) ?></h1>
<p>Status: <?= htmlspecialchars($task['status']) ?></p>
<p><?= nl2br(htmlspecialchars($task['description'] ?? '')) ?></p>

<p>
    <a href="/tasks/<?= (int) $task['id'] ?>/edit">Edit</a>
    &nbsp;
    <form method="POST" action="/tasks/<?= (int) $task['id'] ?>" style="display:inline">
        <input type="hidden" name="_method" value="DELETE">
        <button type="submit">Delete</button>
    </form>
</p>

<p><a href="/tasks">&larr; Back to tasks</a></p>
