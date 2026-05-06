<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Models\ActivityModel;

final class ActivityController extends Controller
{
    public function index(): void
    {
        auth_guard();
        $user = auth_user();

        $activities = ActivityModel::allByWorkspace((int) $user['workspace_id'], [
            'entity_type' => $_GET['type'] ?? '',
            'user_id'     => $_GET['user_id'] ?? '',
        ]);

        // Zisti vsetkych userov workspace pre filter.
        $pdo   = Database::connection();
        $stmt  = $pdo->prepare('SELECT id, name FROM users WHERE workspace_id = :wid ORDER BY name');
        $stmt->execute(['wid' => $user['workspace_id']]);
        $users = $stmt->fetchAll();

        $this->render('activity/index', [
            'pageTitle'  => 'Activity Log | FlowTrack',
            'activities' => $activities,
            'users'      => $users,
        ]);
    }
}
