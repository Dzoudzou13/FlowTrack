<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\ProjectModel;
use App\Models\TaskModel;
use App\Models\TimeEntryModel;

final class TimeController extends Controller
{
    public function index(): void
    {
        auth_guard();
        $user    = auth_user();
        $wid     = (int) $user['workspace_id'];
        $month   = $_GET['month'] ?? date('Y-m');

        $entries  = TimeEntryModel::allByWorkspace($wid, [
            'project_id' => $_GET['project_id'] ?? '',
            'billable'   => $_GET['billable'] ?? '',
            'month'      => $month,
        ]);
        $projects = ProjectModel::listForSelect($wid);
        $tasks    = TaskModel::allByWorkspace($wid);
        $totals   = TimeEntryModel::sumByWorkspace($wid, $month);

        // Tyzden stats.
        $weekStart = date('Y-m-d', strtotime('monday this week'));
        $weekEnd   = date('Y-m-d', strtotime('sunday this week'));
        $weekStmt  = \App\Core\Database::connection()->prepare(
            'SELECT COALESCE(SUM(duration_minutes),0) FROM time_entries
             WHERE workspace_id = :wid AND DATE(started_at) BETWEEN :s AND :e'
        );
        $weekStmt->execute(['wid' => $wid, 's' => $weekStart, 'e' => $weekEnd]);
        $weekMinutes = (int) $weekStmt->fetchColumn();

        $weekBillableStmt = \App\Core\Database::connection()->prepare(
            'SELECT COALESCE(SUM(duration_minutes),0) FROM time_entries
             WHERE workspace_id = :wid AND billable = 1 AND DATE(started_at) BETWEEN :s AND :e'
        );
        $weekBillableStmt->execute(['wid' => $wid, 's' => $weekStart, 'e' => $weekEnd]);
        $weekBillableMinutes = (int) $weekBillableStmt->fetchColumn();

        $this->render('time/index', [
            'pageTitle'           => 'Time Tracking | FlowTrack',
            'entries'             => $entries,
            'projects'            => $projects,
            'tasks'               => $tasks,
            'totals'              => $totals,
            'weekMinutes'         => $weekMinutes,
            'weekBillableMinutes' => $weekBillableMinutes,
            'month'               => $month,
        ]);
    }

    public function store(): void
    {
        auth_guard();
        $user = auth_user();

        $projectId = (int) ($_POST['project_id'] ?? 0);
        $duration  = (int) ($_POST['duration_minutes'] ?? 0);

        if ($projectId === 0 || $duration <= 0) {
            redirect('/time');
        }

        $startedAt = trim($_POST['started_at'] ?? '');
        $startedAt = $startedAt !== '' ? str_replace('T', ' ', $startedAt) : date('Y-m-d H:i:s');

        TimeEntryModel::create([
            'task_id'          => $_POST['task_id'] ?? '',
            'project_id'       => $projectId,
            'user_id'          => $user['id'],
            'workspace_id'     => $user['workspace_id'],
            'description'      => trim($_POST['description'] ?? ''),
            'started_at'       => $startedAt,
            'duration_minutes' => $duration,
            'billable'         => $_POST['billable'] ?? '',
        ]);

        log_activity('time_entry', $projectId, 'logged', ['minutes' => $duration]);
        redirect('/time');
    }

    public function destroy(string $id): void
    {
        auth_guard();
        $user = auth_user();
        TimeEntryModel::delete((int) $id, (int) $user['workspace_id']);
        redirect('/time');
    }
}
