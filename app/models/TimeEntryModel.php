<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class TimeEntryModel
{
    public static function allByWorkspace(int $workspaceId, array $filters = []): array
    {
        $pdo    = Database::connection();
        $where  = ['te.workspace_id = :wid'];
        $params = ['wid' => $workspaceId];

        if (isset($filters['project_id']) && $filters['project_id'] !== '') {
            $where[]        = 'te.project_id = :pid';
            $params['pid']  = (int) $filters['project_id'];
        }
        if (isset($filters['billable']) && $filters['billable'] !== '') {
            $where[]            = 'te.billable = :billable';
            $params['billable'] = (int) $filters['billable'];
        }
        if (isset($filters['month']) && $filters['month'] !== '') {
            $where[]          = 'DATE_FORMAT(te.started_at, "%Y-%m") = :month';
            $params['month']  = $filters['month'];
        }

        $sql = 'SELECT te.*, p.name AS project_name, p.color AS project_color,
                       t.title AS task_title, u.name AS user_name
                FROM time_entries te
                JOIN projects p   ON p.id = te.project_id
                LEFT JOIN tasks t ON t.id = te.task_id
                JOIN users u      ON u.id = te.user_id
                WHERE ' . implode(' AND ', $where) . '
                ORDER BY te.started_at DESC';

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function allByProject(int $projectId): array
    {
        $pdo  = Database::connection();
        $stmt = $pdo->prepare(
            'SELECT te.*, t.title AS task_title, u.name AS user_name
             FROM time_entries te
             LEFT JOIN tasks t ON t.id = te.task_id
             JOIN users u      ON u.id = te.user_id
             WHERE te.project_id = :pid
             ORDER BY te.started_at DESC'
        );
        $stmt->execute(['pid' => $projectId]);
        return $stmt->fetchAll();
    }

    public static function allByTask(int $taskId): array
    {
        $pdo  = Database::connection();
        $stmt = $pdo->prepare(
            'SELECT te.*, u.name AS user_name
             FROM time_entries te
             JOIN users u ON u.id = te.user_id
             WHERE te.task_id = :tid
             ORDER BY te.started_at DESC'
        );
        $stmt->execute(['tid' => $taskId]);
        return $stmt->fetchAll();
    }

    public static function create(array $data): int
    {
        $pdo  = Database::connection();
        $stmt = $pdo->prepare(
            'INSERT INTO time_entries (task_id, project_id, user_id, workspace_id, description, started_at, duration_minutes, billable)
             VALUES (:task_id, :project_id, :user_id, :workspace_id, :description, :started_at, :duration_minutes, :billable)'
        );
        $stmt->execute([
            'task_id'          => $data['task_id'] ?: null,
            'project_id'       => $data['project_id'],
            'user_id'          => $data['user_id'],
            'workspace_id'     => $data['workspace_id'],
            'description'      => $data['description'] ?? null,
            'started_at'       => $data['started_at'],
            'duration_minutes' => (int) $data['duration_minutes'],
            'billable'         => isset($data['billable']) ? 1 : 0,
        ]);
        return (int) $pdo->lastInsertId();
    }

    public static function delete(int $id, int $workspaceId): void
    {
        $pdo  = Database::connection();
        $stmt = $pdo->prepare('DELETE FROM time_entries WHERE id = :id AND workspace_id = :wid');
        $stmt->execute(['id' => $id, 'wid' => $workspaceId]);
    }

    public static function sumByWorkspace(int $workspaceId, ?string $month = null): array
    {
        $pdo    = Database::connection();
        $where  = 'te.workspace_id = :wid';
        $params = ['wid' => $workspaceId];

        if ($month !== null) {
            $where         .= ' AND DATE_FORMAT(te.started_at, "%Y-%m") = :month';
            $params['month'] = $month;
        }

        $stmt = $pdo->prepare(
            "SELECT COALESCE(SUM(te.duration_minutes), 0)                                          AS total_minutes,
                    COALESCE(SUM(CASE WHEN te.billable = 1 THEN te.duration_minutes ELSE 0 END), 0) AS billable_minutes,
                    COALESCE(SUM(CASE WHEN te.billable = 1 THEN te.duration_minutes / 60 * p.hourly_rate ELSE 0 END), 0) AS revenue
             FROM time_entries te
             JOIN projects p ON p.id = te.project_id
             WHERE $where"
        );
        $stmt->execute($params);
        return $stmt->fetch();
    }

    public static function billingByProject(int $workspaceId, string $month): array
    {
        $pdo  = Database::connection();
        $stmt = $pdo->prepare(
            'SELECT p.id, p.name, p.color, p.hourly_rate, p.status AS project_status,
                    COALESCE(SUM(CASE WHEN te.billable = 1 THEN te.duration_minutes ELSE 0 END), 0) AS billable_minutes,
                    COALESCE(SUM(CASE WHEN te.billable = 0 THEN te.duration_minutes ELSE 0 END), 0) AS non_billable_minutes,
                    COALESCE(SUM(CASE WHEN te.billable = 1 THEN te.duration_minutes / 60 * p.hourly_rate ELSE 0 END), 0) AS revenue
             FROM projects p
             LEFT JOIN time_entries te ON te.project_id = p.id
                                      AND DATE_FORMAT(te.started_at, "%Y-%m") = :month
             WHERE p.workspace_id = :wid
             GROUP BY p.id
             ORDER BY revenue DESC'
        );
        $stmt->execute(['wid' => $workspaceId, 'month' => $month]);
        return $stmt->fetchAll();
    }
}
