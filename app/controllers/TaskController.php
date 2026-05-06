<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\CommentModel;
use App\Models\ProjectModel;
use App\Models\TaskModel;
use App\Models\TimeEntryModel;

final class TaskController extends Controller
{
    public function create(string $projectId): void
    {
        auth_guard();
        $user    = auth_user();
        $project = ProjectModel::findById((int) $projectId, (int) $user['workspace_id']);

        if ($project === false) {
            http_response_code(404);
            return;
        }

        $this->render('tasks/create', [
            'pageTitle' => 'Nový task | FlowTrack',
            'project'   => $project,
        ]);
    }

    public function store(string $projectId): void
    {
        auth_guard();
        $user    = auth_user();
        $project = ProjectModel::findById((int) $projectId, (int) $user['workspace_id']);

        if ($project === false) {
            http_response_code(404);
            return;
        }

        $title = trim($_POST['title'] ?? '');
        if ($title === '') {
            $this->render('tasks/create', [
                'pageTitle' => 'Nový task | FlowTrack',
                'project'   => $project,
                'formError' => 'Názov tasku je povinný.',
                'old'       => $_POST,
            ]);
            return;
        }

        $id = TaskModel::create([
            'project_id'   => $projectId,
            'workspace_id' => $user['workspace_id'],
            'created_by'   => $user['id'],
            'assigned_to'  => $_POST['assigned_to'] ?? '',
            'title'        => $title,
            'description'  => trim($_POST['description'] ?? ''),
            'status'       => $_POST['status'] ?? 'backlog',
            'priority'     => $_POST['priority'] ?? 'medium',
            'deadline'     => $_POST['deadline'] ?? '',
        ]);

        log_activity('task', $id, 'created', ['title' => $title, 'project_id' => (int) $projectId]);
        redirect('/tasks/' . $id);
    }

    public function show(string $id): void
    {
        auth_guard();
        $user = auth_user();
        $task = TaskModel::findById((int) $id, (int) $user['workspace_id']);

        if ($task === false) {
            http_response_code(404);
            echo '404 | Task neexistuje.';
            return;
        }

        $comments = CommentModel::allByTask((int) $id);
        $entries  = TimeEntryModel::allByTask((int) $id);

        $this->render('tasks/show', [
            'pageTitle' => htmlspecialchars($task['title']) . ' | FlowTrack',
            'task'      => $task,
            'comments'  => $comments,
            'entries'   => $entries,
        ]);
    }

    public function edit(string $id): void
    {
        auth_guard();
        $user = auth_user();
        $task = TaskModel::findById((int) $id, (int) $user['workspace_id']);

        if ($task === false) {
            http_response_code(404);
            return;
        }

        $this->render('tasks/edit', [
            'pageTitle' => 'Upraviť task | FlowTrack',
            'task'      => $task,
        ]);
    }

    public function update(string $id): void
    {
        auth_guard();
        $user = auth_user();
        $task = TaskModel::findById((int) $id, (int) $user['workspace_id']);

        if ($task === false) {
            http_response_code(404);
            return;
        }

        $title = trim($_POST['title'] ?? '');
        if ($title === '') {
            $this->render('tasks/edit', [
                'pageTitle' => 'Upraviť task | FlowTrack',
                'task'      => array_merge($task, $_POST),
                'formError' => 'Názov tasku je povinný.',
            ]);
            return;
        }

        $oldStatus = $task['status'];
        TaskModel::update((int) $id, (int) $user['workspace_id'], [
            'title'       => $title,
            'description' => trim($_POST['description'] ?? ''),
            'status'      => $_POST['status'] ?? $task['status'],
            'priority'    => $_POST['priority'] ?? $task['priority'],
            'assigned_to' => $_POST['assigned_to'] ?? '',
            'deadline'    => $_POST['deadline'] ?? '',
        ]);

        $newStatus = $_POST['status'] ?? $task['status'];
        $meta      = ['title' => $title];
        if ($oldStatus !== $newStatus) {
            $meta['from'] = $oldStatus;
            $meta['to']   = $newStatus;
        }
        log_activity('task', (int) $id, 'updated', $meta);
        redirect('/tasks/' . $id);
    }

    public function destroy(string $id): void
    {
        auth_guard();
        $user    = auth_user();
        $task    = TaskModel::findById((int) $id, (int) $user['workspace_id']);
        $redirect = $task ? '/projects/' . $task['project_id'] : '/board';
        TaskModel::delete((int) $id, (int) $user['workspace_id']);
        log_activity('task', (int) $id, 'deleted');
        redirect($redirect);
    }

    public function updateStatus(string $id): void
    {
        auth_guard();
        $user   = auth_user();
        $status = trim($_POST['status'] ?? '');
        $task   = TaskModel::findById((int) $id, (int) $user['workspace_id']);

        if ($task === false) {
            http_response_code(403);
            return;
        }

        TaskModel::updateStatus((int) $id, (int) $user['workspace_id'], $status);
        log_activity('task', (int) $id, 'status_changed', ['from' => $task['status'], 'to' => $status]);

        // JSON response pre kanban JS fetch().
        header('Content-Type: application/json');
        echo json_encode(['ok' => true]);
    }

    public function storeComment(string $id): void
    {
        auth_guard();
        $user = auth_user();
        $task = TaskModel::findById((int) $id, (int) $user['workspace_id']);

        if ($task === false) {
            http_response_code(404);
            return;
        }

        $body = trim($_POST['body'] ?? '');
        if ($body !== '') {
            CommentModel::create([
                'task_id' => (int) $id,
                'user_id' => $user['id'],
                'body'    => $body,
            ]);
            log_activity('task', (int) $id, 'comment_added', ['title' => $task['title']]);
        }

        redirect('/tasks/' . $id);
    }

    public function storeTimeEntry(string $id): void
    {
        auth_guard();
        $user = auth_user();
        $task = TaskModel::findById((int) $id, (int) $user['workspace_id']);

        if ($task === false) {
            http_response_code(404);
            return;
        }

        $durationMinutes = (int) ($_POST['duration_minutes'] ?? 0);
        if ($durationMinutes > 0) {
            TimeEntryModel::create([
                'task_id'          => (int) $id,
                'project_id'       => $task['project_id'],
                'user_id'          => $user['id'],
                'workspace_id'     => $user['workspace_id'],
                'description'      => trim($_POST['description'] ?? ''),
                'started_at'       => $_POST['started_at'] ?? date('Y-m-d H:i:s'),
                'duration_minutes' => $durationMinutes,
                'billable'         => $_POST['billable'] ?? '',
            ]);
            log_activity('time_entry', (int) $id, 'logged', ['minutes' => $durationMinutes, 'title' => $task['title']]);
        }

        redirect('/tasks/' . $id);
    }
}
