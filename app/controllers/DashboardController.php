<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\ActivityModel;
use App\Models\ProjectModel;
use App\Models\TaskModel;
use App\Models\TimeEntryModel;

final class DashboardController extends Controller
{
    public function index(): void
    {
        auth_guard();
        $user = auth_user();
        $wid  = (int) $user['workspace_id'];

        $taskCounts   = TaskModel::countByStatus($wid);
        $projects     = ProjectModel::allByWorkspace($wid);
        $recentTasks  = TaskModel::allByWorkspace($wid, ['status' => 'in_progress']);
        $totalsMonth  = TimeEntryModel::sumByWorkspace($wid, date('Y-m'));
        $activities   = ActivityModel::recentByWorkspace($wid, 8);

        $activeProjects = count(array_filter($projects, static fn($p) => $p['status'] === 'active'));

        $this->render('dashboard/index', [
            'pageTitle'      => 'Dashboard | FlowTrack',
            'taskCounts'     => $taskCounts,
            'recentTasks'    => array_slice($recentTasks, 0, 5),
            'totalsMonth'    => $totalsMonth,
            'activities'     => $activities,
            'activeProjects' => $activeProjects,
        ]);
    }
}
