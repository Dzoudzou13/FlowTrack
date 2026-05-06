<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class ActivityModel
{
    public static function allByWorkspace(int $workspaceId, array $filters = []): array
    {
        $pdo    = Database::connection();
        $where  = ['al.workspace_id = :wid'];
        $params = ['wid' => $workspaceId];

        if (isset($filters['entity_type']) && $filters['entity_type'] !== '') {
            $where[]              = 'al.entity_type = :et';
            $params['et']         = $filters['entity_type'];
        }
        if (isset($filters['user_id']) && $filters['user_id'] !== '') {
            $where[]              = 'al.user_id = :uid';
            $params['uid']        = (int) $filters['user_id'];
        }

        $sql = 'SELECT al.*, u.name AS user_name
                FROM activity_log al
                JOIN users u ON u.id = al.user_id
                WHERE ' . implode(' AND ', $where) . '
                ORDER BY al.created_at DESC
                LIMIT 100';

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function recentByWorkspace(int $workspaceId, int $limit = 10): array
    {
        $pdo  = Database::connection();
        $stmt = $pdo->prepare(
            'SELECT al.*, u.name AS user_name
             FROM activity_log al
             JOIN users u ON u.id = al.user_id
             WHERE al.workspace_id = :wid
             ORDER BY al.created_at DESC
             LIMIT :lim'
        );
        $stmt->bindValue('wid', $workspaceId, \PDO::PARAM_INT);
        $stmt->bindValue('lim', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
