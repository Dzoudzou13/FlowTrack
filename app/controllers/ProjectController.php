<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\ProjectModel;
use App\Models\TaskModel;
use App\Models\TimeEntryModel;

final class ProjectController extends Controller
{
    public function index(): void
    {
        auth_guard();
        $user     = auth_user();
        $projects = ProjectModel::allByWorkspace((int) $user['workspace_id']);

        $this->render('projects/index', [
            'pageTitle' => 'Projekty | FlowTrack',
            'projects'  => $projects,
        ]);
    }

    public function create(): void
    {
        auth_guard();
        $this->render('projects/create', [
            'pageTitle' => 'Nový projekt | FlowTrack',
        ]);
    }

    public function store(): void
    {
        auth_guard();
        $user = auth_user();

        $name = trim($_POST['name'] ?? '');
        if ($name === '') {
            $this->render('projects/create', [
                'pageTitle'  => 'Nový projekt | FlowTrack',
                'formError'  => 'Názov projektu je povinný.',
                'old'        => $_POST,
            ]);
            return;
        }

        $id = ProjectModel::create([
            'workspace_id' => $user['workspace_id'],
            'created_by'   => $user['id'],
            'name'         => $name,
            'description'  => trim($_POST['description'] ?? ''),
            'status'       => $_POST['status'] ?? 'active',
            'color'        => $_POST['color'] ?? '#6366f1',
            'hourly_rate'  => (float) ($_POST['hourly_rate'] ?? 0),
            'deadline'     => $_POST['deadline'] ?? '',
        ]);

        log_activity('project', $id, 'created', ['name' => $name]);
        redirect('/projects/' . $id);
    }

    public function show(string $id): void
    {
        auth_guard();
        $user    = auth_user();
        $project = ProjectModel::findById((int) $id, (int) $user['workspace_id']);

        if ($project === false) {
            http_response_code(404);
            echo '404 | Projekt neexistuje.';
            return;
        }

        $tasks   = TaskModel::allByProject((int) $id);
        $entries = TimeEntryModel::allByProject((int) $id);

        $this->render('projects/show', [
            'pageTitle' => htmlspecialchars($project['name']) . ' | FlowTrack',
            'project'   => $project,
            'tasks'     => $tasks,
            'entries'   => $entries,
        ]);
    }

    public function edit(string $id): void
    {
        auth_guard();
        $user    = auth_user();
        $project = ProjectModel::findById((int) $id, (int) $user['workspace_id']);

        if ($project === false) {
            http_response_code(404);
            echo '404 | Projekt neexistuje.';
            return;
        }

        $this->render('projects/edit', [
            'pageTitle' => 'Upraviť projekt | FlowTrack',
            'project'   => $project,
        ]);
    }

    public function update(string $id): void
    {
        auth_guard();
        $user    = auth_user();
        $project = ProjectModel::findById((int) $id, (int) $user['workspace_id']);

        if ($project === false) {
            http_response_code(404);
            return;
        }

        $name = trim($_POST['name'] ?? '');
        if ($name === '') {
            $this->render('projects/edit', [
                'pageTitle' => 'Upraviť projekt | FlowTrack',
                'project'   => array_merge($project, $_POST),
                'formError' => 'Názov projektu je povinný.',
            ]);
            return;
        }

        ProjectModel::update((int) $id, (int) $user['workspace_id'], [
            'name'        => $name,
            'description' => trim($_POST['description'] ?? ''),
            'status'      => $_POST['status'] ?? 'active',
            'color'       => $_POST['color'] ?? '#6366f1',
            'hourly_rate' => (float) ($_POST['hourly_rate'] ?? 0),
            'deadline'    => $_POST['deadline'] ?? '',
        ]);

        log_activity('project', (int) $id, 'updated', ['name' => $name]);
        redirect('/projects/' . $id);
    }

    public function destroy(string $id): void
    {
        auth_guard();
        $user = auth_user();
        ProjectModel::delete((int) $id, (int) $user['workspace_id']);
        log_activity('project', (int) $id, 'deleted');
        redirect('/projects');
    }

    public function board(string $id): void
    {
        auth_guard();
        $user    = auth_user();
        $project = ProjectModel::findById((int) $id, (int) $user['workspace_id']);

        if ($project === false) {
            http_response_code(404);
            echo '404 | Projekt neexistuje.';
            return;
        }

        $tasks = TaskModel::allByProject((int) $id);

        $grouped = ['backlog' => [], 'in_progress' => [], 'review' => [], 'done' => []];
        foreach ($tasks as $task) {
            $grouped[$task['status']][] = $task;
        }

        $this->render('projects/board', [
            'pageTitle' => 'Board — ' . htmlspecialchars($project['name']) . ' | FlowTrack',
            'project'   => $project,
            'grouped'   => $grouped,
        ]);
    }
}
