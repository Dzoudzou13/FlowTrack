<?php

declare(strict_types=1);

use App\Controllers\ActivityController;
use App\Controllers\AuthController;
use App\Controllers\BillingController;
use App\Controllers\DashboardController;
use App\Controllers\ProjectController;
use App\Controllers\SettingsController;
use App\Controllers\TaskController;
use App\Controllers\TeamController;
use App\Controllers\TimeController;
use App\Controllers\PageController;
use App\Core\Router;

$router = new Router();

$auth       = new AuthController();
$dashboard  = new DashboardController();
$project    = new ProjectController();
$task       = new TaskController();
$time       = new TimeController();
$settings   = new SettingsController();
$team       = new TeamController();
$billing    = new BillingController();
$activity   = new ActivityController();
$page       = new PageController();

// Landing page.
$router->get('/', [$page, 'home']);

// Auth.
$router->get('/login',      [$auth, 'showLogin']);
$router->post('/login',     [$auth, 'login']);
$router->get('/register',   [$auth, 'showRegister']);
$router->post('/register',  [$auth, 'register']);
$router->get('/logout',     [$auth, 'logout']);

// Dashboard.
$router->get('/dashboard',  [$dashboard, 'index']);

// Projects.
$router->get('/projects',                   [$project, 'index']);
$router->get('/projects/create',            [$project, 'create']);
$router->post('/projects',                  [$project, 'store']);
$router->get('/projects/{id}',              [$project, 'show']);
$router->get('/projects/{id}/edit',         [$project, 'edit']);
$router->post('/projects/{id}/update',      [$project, 'update']);
$router->post('/projects/{id}/delete',      [$project, 'destroy']);
$router->get('/projects/{id}/board',        [$project, 'board']);

// Tasks.
$router->get('/projects/{projectId}/tasks/create',  [$task, 'create']);
$router->post('/projects/{projectId}/tasks',        [$task, 'store']);
$router->get('/tasks/{id}',                         [$task, 'show']);
$router->get('/tasks/{id}/edit',                    [$task, 'edit']);
$router->post('/tasks/{id}/update',                 [$task, 'update']);
$router->post('/tasks/{id}/delete',                 [$task, 'destroy']);
$router->post('/tasks/{id}/status',                 [$task, 'updateStatus']);
$router->post('/tasks/{id}/comments',               [$task, 'storeComment']);
$router->post('/tasks/{id}/time',                   [$task, 'storeTimeEntry']);

// General board (všetky tasky).
$router->get('/board', static function () {
    auth_guard();
    $user    = auth_user();
    $wid     = (int) $user['workspace_id'];
    $filters = [
        'project_id'  => $_GET['project_id'] ?? '',
        'priority'    => $_GET['priority'] ?? '',
        'assigned_to' => $_GET['assigned_to'] ?? '',
    ];
    $tasks    = \App\Models\TaskModel::allByWorkspace($wid, $filters);
    $projects = \App\Models\ProjectModel::listForSelect($wid);

    $grouped = ['backlog' => [], 'in_progress' => [], 'review' => [], 'done' => []];
    foreach ($tasks as $t) {
        $grouped[$t['status']][] = $t;
    }

    \App\Core\View::render('board/index', [
        'pageTitle' => 'Board | FlowTrack',
        'grouped'   => $grouped,
        'projects'  => $projects,
    ]);
});

// Time tracking.
$router->get('/time',              [$time, 'index']);
$router->post('/time',             [$time, 'store']);
$router->post('/time/{id}/delete', [$time, 'destroy']);

// Billing.
$router->get('/billing', [$billing, 'index']);

// Team.
$router->get('/team',          [$team, 'index']);
$router->post('/team/invite',  [$team, 'invite']);

// Activity.
$router->get('/activity', [$activity, 'index']);

// Settings.
$router->get('/settings',              [$settings, 'index']);
$router->post('/settings/profile',     [$settings, 'updateProfile']);
$router->post('/settings/password',    [$settings, 'updatePassword']);

return $router;
