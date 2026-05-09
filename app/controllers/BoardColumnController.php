<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\BoardColumnModel;

final class BoardColumnController extends Controller
{
    public function store(string $projectId): void
    {
        auth_guard();
        $user = auth_user();
        $name = trim($_POST['name'] ?? '');

        if ($name !== '') {
            BoardColumnModel::create((int) $projectId, (int) $user['workspace_id'], $name);
        }

        redirect('/projects/' . $projectId . '/board');
    }

    public function storeWorkspace(): void
    {
        auth_guard();
        $user = auth_user();
        $name = trim($_POST['name'] ?? '');

        if ($name !== '') {
            BoardColumnModel::create(null, (int) $user['workspace_id'], $name);
        }

        redirect('/board');
    }

    public function destroy(string $projectId, string $colId): void
    {
        auth_guard();
        $user = auth_user();
        BoardColumnModel::delete((int) $colId, (int) $user['workspace_id']);
        redirect('/projects/' . $projectId . '/board');
    }

    public function reorder(): void
    {
        auth_guard();
        $user = auth_user();
        $ids  = json_decode($_POST['ids'] ?? '[]', true);
        if (is_array($ids) && !empty($ids)) {
            BoardColumnModel::reorder($ids, (int) $user['workspace_id']);
        }
        header('Content-Type: application/json');
        echo json_encode(['ok' => true]);
    }

    public function destroyWorkspace(string $colId): void
    {
        auth_guard();
        $user = auth_user();
        BoardColumnModel::delete((int) $colId, (int) $user['workspace_id']);
        redirect('/board');
    }
}
