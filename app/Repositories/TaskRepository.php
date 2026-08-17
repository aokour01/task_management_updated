<?php

namespace App\Repositories;

use PDO;

class TaskRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function allForUser(int $userId, string $search = '', string $status = ''): array
    {
        $sql = 'SELECT id, title, status, created_at FROM tasks WHERE user_id = ?';
        $params = [$userId];

        if ($search !== '') {
            $sql .= ' AND title LIKE ?';
            $params[] = '%' . $search . '%';
        }

        if ($status === 'pending' || $status === 'completed') {
            $sql .= ' AND status = ?';
            $params[] = $status;
        }

        $sql .= ' ORDER BY created_at DESC';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function find(int $id): array|false
    {
        $stmt = $this->pdo->prepare('SELECT * FROM tasks WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function belongsToUser(array $task, int $userId): bool
    {
        return (int) $task['user_id'] === $userId;
    }

    public function create(int $userId, string $title, string $description): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO tasks (user_id, title, description, status) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([$userId, $title, $description, 'pending']);
        return (int) $this->pdo->lastInsertId();
    }

    public function update(int $id, int $userId, string $title, string $description, string $status): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE tasks SET title = ?, description = ?, status = ? WHERE id = ? AND user_id = ?'
        );
        $stmt->execute([$title, $description, $status, $id, $userId]);
    }

    public function delete(int $id, int $userId): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM tasks WHERE id = ? AND user_id = ?');
        $stmt->execute([$id, $userId]);
    }
}
