<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

final class TaskModel
{
    public static function allByWorkspace(int $workspaceId, array $filters = []): array
    {
        $pdo    = Database::connection();
        $where  = ['t.workspace_id = :wid'];
        $params = ['wid' => $workspaceId];

        if (isset($filters['project_id']) && $filters['project_id'] !== '') {
            $where[]              = 't.project_id = :pid';
            $params['pid']        = (int) $filters['project_id'];
        }
        if (isset($filters['status']) && $filters['status'] !== '') {
            $where[]              = 't.status = :status';
            $params['status']     = $filters['status'];
        }
        if (isset($filters['priority']) && $filters['priority'] !== '') {
            $where[]              = 't.priority = :priority';
            $params['priority']   = $filters['priority'];
        }
        if (isset($filters['assigned_to']) && $filters['assigned_to'] !== '') {
            $where[]              = 't.assigned_to = :assigned';
            $params['assigned']   = (int) $filters['assigned_to'];
        }

        $sql = 'SELECT t.*, p.name AS project_name, p.color AS project_color,
                       u.name AS assignee_name
                FROM tasks t
                JOIN projects p ON p.id = t.project_id
                LEFT JOIN users u ON u.id = t.assigned_to
                WHERE ' . implode(' AND ', $where) . '
                ORDER BY FIELD(t.status,"in_progress","review","backlog","done"), t.priority DESC, t.created_at DESC';

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function allByProject(int $projectId): array
    {
        $pdo  = Database::connection();
        $stmt = $pdo->prepare(
            'SELECT t.*, u.name AS assignee_name
             FROM tasks t
             LEFT JOIN users u ON u.id = t.assigned_to
             WHERE t.project_id = :pid
             ORDER BY FIELD(t.status,"in_progress","review","backlog","done"), t.created_at DESC'
        );
        $stmt->execute(['pid' => $projectId]);
        return $stmt->fetchAll();
    }

    public static function findById(int $id, int $workspaceId): array|false
    {
        $pdo  = Database::connection();
        $stmt = $pdo->prepare(
            'SELECT t.*, p.name AS project_name, p.color AS project_color, p.hourly_rate,
                    u.name AS assignee_name
             FROM tasks t
             JOIN projects p ON p.id = t.project_id
             LEFT JOIN users u ON u.id = t.assigned_to
             WHERE t.id = :id AND t.workspace_id = :wid'
        );
        $stmt->execute(['id' => $id, 'wid' => $workspaceId]);
        return $stmt->fetch();
    }

    public static function create(array $data): int
    {
        $pdo  = Database::connection();
        $stmt = $pdo->prepare(
            'INSERT INTO tasks (project_id, workspace_id, created_by, assigned_to, title, description, status, priority, deadline)
             VALUES (:project_id, :workspace_id, :created_by, :assigned_to, :title, :description, :status, :priority, :deadline)'
        );
        $stmt->execute([
            'project_id'   => $data['project_id'],
            'workspace_id' => $data['workspace_id'],
            'created_by'   => $data['created_by'],
            'assigned_to'  => $data['assigned_to'] ?: null,
            'title'        => $data['title'],
            'description'  => $data['description'] ?? null,
            'status'       => $data['status'] ?? 'backlog',
            'priority'     => $data['priority'] ?? 'medium',
            'deadline'     => $data['deadline'] ?: null,
        ]);
        return (int) $pdo->lastInsertId();
    }

    public static function update(int $id, int $workspaceId, array $data): void
    {
        $pdo  = Database::connection();
        $stmt = $pdo->prepare(
            'UPDATE tasks
             SET title = :title, description = :description, status = :status,
                 priority = :priority, assigned_to = :assigned_to, deadline = :deadline
             WHERE id = :id AND workspace_id = :wid'
        );
        $stmt->execute([
            'title'       => $data['title'],
            'description' => $data['description'] ?? null,
            'status'      => $data['status'],
            'priority'    => $data['priority'],
            'assigned_to' => $data['assigned_to'] ?: null,
            'deadline'    => $data['deadline'] ?: null,
            'id'          => $id,
            'wid'         => $workspaceId,
        ]);
    }

    public static function updateStatus(int $id, int $workspaceId, string $status): void
    {
        $allowed = ['backlog', 'in_progress', 'review', 'done'];
        if (!in_array($status, $allowed, true)) {
            return;
        }

        $pdo  = Database::connection();
        $stmt = $pdo->prepare('UPDATE tasks SET status = :status WHERE id = :id AND workspace_id = :wid');
        $stmt->execute(['status' => $status, 'id' => $id, 'wid' => $workspaceId]);
    }

    public static function delete(int $id, int $workspaceId): void
    {
        $pdo  = Database::connection();
        $stmt = $pdo->prepare('DELETE FROM tasks WHERE id = :id AND workspace_id = :wid');
        $stmt->execute(['id' => $id, 'wid' => $workspaceId]);
    }

    public static function countByStatus(int $workspaceId): array
    {
        $pdo  = Database::connection();
        $stmt = $pdo->prepare(
            'SELECT status, COUNT(*) AS cnt FROM tasks WHERE workspace_id = :wid GROUP BY status'
        );
        $stmt->execute(['wid' => $workspaceId]);
        $rows   = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        return [
            'backlog'     => (int) ($rows['backlog'] ?? 0),
            'in_progress' => (int) ($rows['in_progress'] ?? 0),
            'review'      => (int) ($rows['review'] ?? 0),
            'done'        => (int) ($rows['done'] ?? 0),
        ];
    }
}
