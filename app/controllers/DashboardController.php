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

        $weekStart = date('Y-m-d', strtotime('monday this week'));
        $weekEnd   = date('Y-m-d', strtotime('sunday this week'));
        $weekStmt  = \App\Core\Database::connection()->prepare(
            'SELECT COALESCE(SUM(duration_minutes), 0) FROM time_entries
             WHERE workspace_id = :wid AND DATE(started_at) BETWEEN :s AND :e'
        );
        $weekStmt->execute(['wid' => $wid, 's' => $weekStart, 'e' => $weekEnd]);
        $weekMinutes = (int) $weekStmt->fetchColumn();

        $this->render('dashboard/index', [
            'pageTitle'      => 'Dashboard | FlowTrack',
            'taskCounts'     => $taskCounts,
            'recentTasks'    => array_slice($recentTasks, 0, 5),
            'totalsMonth'    => $totalsMonth,
            'activities'     => $activities,
            'activeProjects' => $activeProjects,
            'weekMinutes'    => $weekMinutes,
        ]);
    }
}
