<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

final class ProjectModel
{
    public static function allByWorkspace(int $workspaceId): array
    {
        $pdo  = Database::connection();
        $stmt = $pdo->prepare(
            'SELECT p.*,
                    COUNT(DISTINCT t.id)                                        AS task_count,
                    COALESCE(SUM(te.duration_minutes), 0)                       AS total_minutes,
                    COALESCE(SUM(CASE WHEN te.billable = 1 THEN te.duration_minutes ELSE 0 END), 0) AS billable_minutes
             FROM projects p
             LEFT JOIN tasks t       ON t.project_id = p.id
             LEFT JOIN time_entries te ON te.project_id = p.id
             WHERE p.workspace_id = :wid
             GROUP BY p.id
             ORDER BY p.created_at DESC'
        );
        $stmt->execute(['wid' => $workspaceId]);
        return $stmt->fetchAll();
    }

    public static function findById(int $id, int $workspaceId): array|false
    {
        $pdo  = Database::connection();
        $stmt = $pdo->prepare(
            'SELECT p.*,
                    COALESCE(SUM(te.duration_minutes), 0)                       AS total_minutes,
                    COALESCE(SUM(CASE WHEN te.billable = 1 THEN te.duration_minutes ELSE 0 END), 0) AS billable_minutes,
                    COUNT(DISTINCT t.id)                                        AS task_count,
                    COUNT(DISTINCT CASE WHEN t.status = "done" THEN t.id END)   AS done_count
             FROM projects p
             LEFT JOIN tasks t        ON t.project_id = p.id
             LEFT JOIN time_entries te ON te.project_id = p.id
             WHERE p.id = :id AND p.workspace_id = :wid
             GROUP BY p.id'
        );
        $stmt->execute(['id' => $id, 'wid' => $workspaceId]);
        return $stmt->fetch();
    }

    public static function create(array $data): int
    {
        $pdo  = Database::connection();
        $stmt = $pdo->prepare(
            'INSERT INTO projects (workspace_id, created_by, name, description, status, color, hourly_rate, deadline)
             VALUES (:workspace_id, :created_by, :name, :description, :status, :color, :hourly_rate, :deadline)'
        );
        $stmt->execute([
            'workspace_id' => $data['workspace_id'],
            'created_by'   => $data['created_by'],
            'name'         => $data['name'],
            'description'  => $data['description'] ?? null,
            'status'       => $data['status'] ?? 'active',
            'color'        => $data['color'] ?? '#6366f1',
            'hourly_rate'  => $data['hourly_rate'] ?? 0,
            'deadline'     => $data['deadline'] ?: null,
        ]);
        return (int) $pdo->lastInsertId();
    }

    public static function update(int $id, int $workspaceId, array $data): void
    {
        $pdo  = Database::connection();
        $stmt = $pdo->prepare(
            'UPDATE projects
             SET name = :name, description = :description, status = :status,
                 color = :color, hourly_rate = :hourly_rate, deadline = :deadline
             WHERE id = :id AND workspace_id = :wid'
        );
        $stmt->execute([
            'name'        => $data['name'],
            'description' => $data['description'] ?? null,
            'status'      => $data['status'],
            'color'       => $data['color'],
            'hourly_rate' => $data['hourly_rate'] ?? 0,
            'deadline'    => $data['deadline'] ?: null,
            'id'          => $id,
            'wid'         => $workspaceId,
        ]);
    }

    public static function delete(int $id, int $workspaceId): void
    {
        $pdo  = Database::connection();
        $stmt = $pdo->prepare('DELETE FROM projects WHERE id = :id AND workspace_id = :wid');
        $stmt->execute(['id' => $id, 'wid' => $workspaceId]);
    }

    public static function listForSelect(int $workspaceId): array
    {
        $pdo  = Database::connection();
        $stmt = $pdo->prepare('SELECT id, name, color FROM projects WHERE workspace_id = :wid ORDER BY name');
        $stmt->execute(['wid' => $workspaceId]);
        return $stmt->fetchAll();
    }
}
