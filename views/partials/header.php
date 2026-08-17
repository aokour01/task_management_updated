<?php $currentUserName = $_SESSION['user_name'] ?? null; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Task Manager</title>
    <style>
        body { font-family: system-ui, sans-serif; max-width: 640px; margin: 2rem auto; padding: 0 1rem; color: #1a1a1a; }
        header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
        header a { color: inherit; text-decoration: none; font-weight: bold; }
        nav a, nav span { margin-left: 1rem; }
        form { display: flex; flex-direction: column; gap: 0.75rem; max-width: 420px; }
        label { display: flex; flex-direction: column; gap: 0.25rem; font-size: 0.9rem; }
        input, textarea, select { padding: 0.5rem; font-size: 1rem; }
        button { padding: 0.5rem 1rem; cursor: pointer; }
        ul.errors { color: #b00020; padding-left: 1.2rem; }
        ul.task-list { list-style: none; padding: 0; }
        ul.task-list li { padding: 0.5rem 0; border-bottom: 1px solid #eee; }
    </style>
</head>
<body>
<header>
    <a href="/tasks">Task Manager</a>
    <nav>
        <?php if ($currentUserName): ?>
            <span>Hi, <?= htmlspecialchars($currentUserName) ?></span>
            <form method="POST" action="/logout" style="display:inline">
                <button type="submit">Log out</button>
            </form>
        <?php else: ?>
            <a href="/login">Log in</a>
            <a href="/register">Register</a>
        <?php endif; ?>
    </nav>
</header>
<main>
