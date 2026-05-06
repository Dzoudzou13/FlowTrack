<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;

final class SettingsController extends Controller
{
    public function index(): void
    {
        auth_guard();
        $user = auth_user();

        // Nacitaj workspace info.
        $pdo  = Database::connection();
        $ws   = $pdo->prepare('SELECT * FROM workspaces WHERE id = :id');
        $ws->execute(['id' => $user['workspace_id']]);
        $workspace = $ws->fetch();

        $members = $pdo->prepare(
            'SELECT COUNT(*) FROM users WHERE workspace_id = :wid'
        );
        $members->execute(['wid' => $user['workspace_id']]);
        $memberCount = (int) $members->fetchColumn();

        $this->render('settings/index', [
            'pageTitle'   => 'Settings | FlowTrack',
            'currentUser' => $user,
            'workspace'   => $workspace,
            'memberCount' => $memberCount,
        ]);
    }

    public function updateProfile(): void
    {
        auth_guard();
        $user = auth_user();

        $name  = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');

        if ($name === '' || $email === '') {
            redirect('/settings');
        }

        $pdo = Database::connection();

        // Over unikatnost emailu (okrem seba).
        $check = $pdo->prepare('SELECT id FROM users WHERE email = :email AND id != :id LIMIT 1');
        $check->execute(['email' => $email, 'id' => $user['id']]);
        if ($check->fetch()) {
            redirect('/settings');
        }

        $pdo->prepare('UPDATE users SET name = :name, email = :email WHERE id = :id')
            ->execute(['name' => $name, 'email' => $email, 'id' => $user['id']]);

        // Aktualizuj session.
        $_SESSION['user']['name']  = $name;
        $_SESSION['user']['email'] = $email;

        redirect('/settings');
    }

    public function updatePassword(): void
    {
        auth_guard();
        $user = auth_user();

        $current = $_POST['current_password'] ?? '';
        $new     = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        if ($new !== $confirm || strlen($new) < 8) {
            redirect('/settings');
        }

        $pdo  = Database::connection();
        $stmt = $pdo->prepare('SELECT password_hash FROM users WHERE id = :id');
        $stmt->execute(['id' => $user['id']]);
        $row = $stmt->fetch();

        if (!$row || !password_verify($current, $row['password_hash'])) {
            redirect('/settings');
        }

        $pdo->prepare('UPDATE users SET password_hash = :h WHERE id = :id')
            ->execute(['h' => password_hash($new, PASSWORD_BCRYPT), 'id' => $user['id']]);

        redirect('/settings');
    }
}
