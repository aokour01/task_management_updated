<?php

namespace App\Controllers;

use App\Core\Validator;
use App\Repositories\TaskRepository;

class TaskController extends Controller
{
    public function __construct(private TaskRepository $tasks)
    {
    }

    private function findOwnedTaskOrAbort(string $id): array
    {
        if (!ctype_digit($id)) {
            $this->abort(404, 'Task not found.');
        }

        $task = $this->tasks->find((int) $id);

        if (!$task) {
            $this->abort(404, 'Task not found.');
        }

        if (!$this->tasks->belongsToUser($task, (int) $_SESSION['user_id'])) {
            $this->abort(403, 'You are not allowed to access this task.');
        }

        return $task;
    }

    public function index(): void
    {
        $userId = $_SESSION['user_id'];
        $search = trim($_GET['search'] ?? '');
        $status = $_GET['status'] ?? '';

        $tasks = $this->tasks->allForUser($userId, $search, $status);

        $this->render('tasks/index', compact('tasks', 'search', 'status'));
    }

    public function show(string $id): void
    {
        $task = $this->findOwnedTaskOrAbort($id);
        $this->render('tasks/show', compact('task'));
    }

    public function showCreateForm(): void
    {
        $this->render('tasks/create', ['errors' => [], 'title' => '', 'description' => '']);
    }

    public function create(): void
    {
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');

        $validator = new Validator($_POST);
        $validator->required('title', 'Title')
            ->minLength('title', 3, 'Title')
            ->maxLength('title', 150, 'Title');

        $errors = $validator->errors();

        if (!empty($errors)) {
            $this->render('tasks/create', compact('errors', 'title', 'description'));
            return;
        }

        $this->tasks->create($_SESSION['user_id'], $title, $description);
        $this->redirect('/tasks');
    }

    public function showEditForm(string $id): void
    {
        $task = $this->findOwnedTaskOrAbort($id);
        $this->render('tasks/edit', ['errors' => [], 'task' => $task]);
    }

    public function update(string $id): void
    {
        $task = $this->findOwnedTaskOrAbort($id);

        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $status = $_POST['status'] ?? 'pending';

        $validator = new Validator($_POST);
        $validator->required('title', 'Title')
            ->minLength('title', 3, 'Title')
            ->maxLength('title', 150, 'Title')
            ->in('status', ['pending', 'completed'], 'Status');

        $errors = $validator->errors();

        if (!empty($errors)) {
            $task['title'] = $title;
            $task['description'] = $description;
            $task['status'] = $status;
            $this->render('tasks/edit', compact('errors', 'task'));
            return;
        }

        $this->tasks->update((int) $task['id'], $_SESSION['user_id'], $title, $description, $status);
        $this->redirect('/tasks/' . $task['id']);
    }

    public function delete(string $id): void
    {
        $task = $this->findOwnedTaskOrAbort($id);
        $this->tasks->delete((int) $task['id'], $_SESSION['user_id']);
        $this->redirect('/tasks');
    }
}
