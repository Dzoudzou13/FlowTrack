<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class BoardColumnModel
{
    private const DEFAULTS = [
        ['name' => 'Backlog',     'slug' => 'backlog'],
        ['name' => 'In Progress', 'slug' => 'in_progress'],
        ['name' => 'Review',      'slug' => 'review'],
        ['name' => 'Done',        'slug' => 'done'],
    ];

    public static function allByProject(int $projectId): array
    {
        $pdo  = Database::connection();
        $stmt = $pdo->prepare('SELECT * FROM board_columns WHERE project_id = :pid ORDER BY position ASC');
        $stmt->execute(['pid' => $projectId]);
        return $stmt->fetchAll();
    }

    public static function allByWorkspace(int $workspaceId): array
    {
        $pdo  = Database::connection();
        $stmt = $pdo->prepare('SELECT * FROM board_columns WHERE workspace_id = :wid AND project_id IS NULL ORDER BY position ASC');
        $stmt->execute(['wid' => $workspaceId]);
        return $stmt->fetchAll();
    }

    public static function getOrCreateDefaults(int $projectId, int $workspaceId): array
    {
        $columns = self::allByProject($projectId);
        if (!empty($columns)) {
            return $columns;
        }
        foreach (self::DEFAULTS as $i => $col) {
            self::insert($projectId, $workspaceId, $col['name'], $col['slug'], $i);
        }
        return self::allByProject($projectId);
    }

    public static function getOrCreateWorkspaceDefaults(int $workspaceId): array
    {
        $columns = self::allByWorkspace($workspaceId);
        if (!empty($columns)) {
            return $columns;
        }
        foreach (self::DEFAULTS as $i => $col) {
            self::insert(null, $workspaceId, $col['name'], $col['slug'], $i);
        }
        return self::allByWorkspace($workspaceId);
    }

    public static function create(?int $projectId, int $workspaceId, string $name): int
    {
        $pdo  = Database::connection();
        if ($projectId !== null) {
            $stmt = $pdo->prepare('SELECT COALESCE(MAX(position), -1) FROM board_columns WHERE project_id = :pid');
            $stmt->execute(['pid' => $projectId]);
        } else {
            $stmt = $pdo->prepare('SELECT COALESCE(MAX(position), -1) FROM board_columns WHERE workspace_id = :wid AND project_id IS NULL');
            $stmt->execute(['wid' => $workspaceId]);
        }
        $pos = (int) $stmt->fetchColumn() + 1;
        return self::insert($projectId, $workspaceId, $name, self::toSlug($name), $pos);
    }

    public static function reorder(array $ids, int $workspaceId): void
    {
        $pdo  = Database::connection();
        $stmt = $pdo->prepare('UPDATE board_columns SET position = :pos WHERE id = :id AND workspace_id = :wid');
        foreach ($ids as $pos => $id) {
            $stmt->execute(['pos' => $pos, 'id' => (int) $id, 'wid' => $workspaceId]);
        }
    }

    public static function delete(int $id, int $workspaceId): void
    {
        $pdo  = Database::connection();
        $stmt = $pdo->prepare('DELETE FROM board_columns WHERE id = :id AND workspace_id = :wid');
        $stmt->execute(['id' => $id, 'wid' => $workspaceId]);
    }

    private static function insert(?int $projectId, int $workspaceId, string $name, string $slug, int $pos): int
    {
        $pdo  = Database::connection();
        $stmt = $pdo->prepare(
            'INSERT INTO board_columns (project_id, workspace_id, name, slug, position)
             VALUES (:pid, :wid, :name, :slug, :pos)'
        );
        $stmt->execute(['pid' => $projectId, 'wid' => $workspaceId, 'name' => $name, 'slug' => $slug, 'pos' => $pos]);
        return (int) $pdo->lastInsertId();
    }

    private static function toSlug(string $name): string
    {
        $slug = strtolower((string) preg_replace('/[^a-zA-Z0-9]+/', '_', $name));
        $slug = trim($slug, '_');
        return $slug !== '' ? $slug : 'col_' . time();
    }
}
