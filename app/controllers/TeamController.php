<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;

final class TeamController extends Controller
{
    public function index(): void
    {
        auth_guard();
        $user = auth_user();

        $pdo  = Database::connection();
        $stmt = $pdo->prepare(
            'SELECT id, name, email, role, created_at FROM users WHERE workspace_id = :wid ORDER BY created_at ASC'
        );
        $stmt->execute(['wid' => $user['workspace_id']]);
        $members = $stmt->fetchAll();

        $ws = $pdo->prepare('SELECT * FROM workspaces WHERE id = :id');
        $ws->execute(['id' => $user['workspace_id']]);
        $workspace = $ws->fetch();

        $this->render('team/index', [
            'pageTitle' => 'Team | FlowTrack',
            'members'   => $members,
            'workspace' => $workspace,
        ]);
    }

    public function invite(): void
    {
        auth_guard();
        $user = auth_user();

        // Len admin moze pozivat.
        if ($user['role'] !== 'admin') {
            redirect('/team');
        }

        $email = trim($_POST['email'] ?? '');
        $role  = $_POST['role'] ?? 'user';

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            redirect('/team');
        }

        $pdo = Database::connection();

        // Over ci uz existuje.
        $exists = $pdo->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
        $exists->execute(['email' => $email]);
        if ($exists->fetch()) {
            redirect('/team');
        }

        // Vytvor usera s docasnym heslom.
        $tempPass = bin2hex(random_bytes(8));
        $pdo->prepare(
            'INSERT INTO users (workspace_id, name, email, password_hash, role)
             VALUES (:wid, :name, :email, :hash, :role)'
        )->execute([
            'wid'  => $user['workspace_id'],
            'name' => explode('@', $email)[0],
            'email' => $email,
            'hash' => password_hash($tempPass, PASSWORD_BCRYPT),
            'role' => in_array($role, ['admin', 'user'], true) ? $role : 'user',
        ]);

        log_activity('team', null, 'member_invited', ['email' => $email]);
        redirect('/team');
    }
}
