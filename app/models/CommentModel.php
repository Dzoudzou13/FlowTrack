<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class CommentModel
{
    public static function allByTask(int $taskId): array
    {
        $pdo  = Database::connection();
        $stmt = $pdo->prepare(
            'SELECT c.*, u.name AS user_name
             FROM comments c
             JOIN users u ON u.id = c.user_id
             WHERE c.task_id = :tid
             ORDER BY c.created_at ASC'
        );
        $stmt->execute(['tid' => $taskId]);
        return $stmt->fetchAll();
    }

    public static function create(array $data): int
    {
        $pdo  = Database::connection();
        $stmt = $pdo->prepare(
            'INSERT INTO comments (task_id, user_id, body) VALUES (:task_id, :user_id, :body)'
        );
        $stmt->execute([
            'task_id' => $data['task_id'],
            'user_id' => $data['user_id'],
            'body'    => $data['body'],
        ]);
        return (int) $pdo->lastInsertId();
    }

    public static function delete(int $id, int $userId): void
    {
        $pdo  = Database::connection();
        $stmt = $pdo->prepare('DELETE FROM comments WHERE id = :id AND user_id = :uid');
        $stmt->execute(['id' => $id, 'uid' => $userId]);
    }
}
