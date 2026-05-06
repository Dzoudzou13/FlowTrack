<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;

final class AuthController extends Controller
{
    public function showLogin(): void
    {
        guest_guard();
        $this->render('auth/login', [
            'pageTitle' => 'Prihlásenie | FlowTrack',
            'activeTab' => 'login',
        ]);
    }

    public function showRegister(): void
    {
        guest_guard();
        $this->render('auth/register', [
            'pageTitle' => 'Registrácia | FlowTrack',
            'activeTab' => 'register',
        ]);
    }

    public function login(): void
    {
        guest_guard();

        $email      = trim($_POST['email'] ?? '');
        $password   = $_POST['password'] ?? '';
        $rememberMe = isset($_POST['remember_me']);

        if ($email === '' || $password === '') {
            $this->render('auth/login', [
                'pageTitle' => 'Prihlásenie | FlowTrack',
                'activeTab' => 'login',
                'authError' => 'Vyplň email aj heslo.',
            ]);
            return;
        }

        $pdo  = Database::connection();
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password_hash'])) {
            $this->render('auth/login', [
                'pageTitle' => 'Prihlásenie | FlowTrack',
                'activeTab' => 'login',
                'authError' => 'Nesprávny email alebo heslo.',
            ]);
            return;
        }

        $_SESSION['user'] = [
            'id'           => $user['id'],
            'workspace_id' => $user['workspace_id'],
            'name'         => $user['name'],
            'email'        => $user['email'],
            'role'         => $user['role'],
        ];

        if ($rememberMe) {
            $this->setRememberCookie((int) $user['id']);
        }

        redirect('/dashboard');
    }

    public function register(): void
    {
        guest_guard();

        $name     = trim($_POST['name'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        $error = null;
        if ($name === '' || $email === '' || $password === '') {
            $error = 'Vyplň všetky polia.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Neplatný formát emailu.';
        } elseif (strlen($password) < 8) {
            $error = 'Heslo musí mať aspoň 8 znakov.';
        }

        if ($error !== null) {
            $this->render('auth/register', [
                'pageTitle' => 'Registrácia | FlowTrack',
                'activeTab' => 'register',
                'authError' => $error,
                'old'       => compact('name', 'email'),
            ]);
            return;
        }

        $pdo = Database::connection();

        // Skontroluj unikatnost emailu.
        $check = $pdo->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
        $check->execute(['email' => $email]);
        if ($check->fetch()) {
            $this->render('auth/register', [
                'pageTitle' => 'Registrácia | FlowTrack',
                'activeTab' => 'register',
                'authError' => 'Tento email je už registrovaný.',
                'old'       => compact('name', 'email'),
            ]);
            return;
        }

        // Vytvor workspace.
        $workspaceName = trim($_POST['workspace'] ?? '') !== '' ? trim($_POST['workspace']) : ($name . ' Workspace');
        $slug = $this->generateSlug($workspaceName);
        $wsStmt = $pdo->prepare('INSERT INTO workspaces (name, slug) VALUES (:name, :slug)');
        $wsStmt->execute(['name' => $workspaceName, 'slug' => $slug]);
        $workspaceId = (int) $pdo->lastInsertId();

        // Vytvor usera ako admin.
        $userStmt = $pdo->prepare(
            'INSERT INTO users (workspace_id, name, email, password_hash, role)
             VALUES (:workspace_id, :name, :email, :password_hash, "admin")'
        );
        $userStmt->execute([
            'workspace_id'  => $workspaceId,
            'name'          => $name,
            'email'         => $email,
            'password_hash' => password_hash($password, PASSWORD_BCRYPT),
        ]);
        $userId = (int) $pdo->lastInsertId();

        $_SESSION['user'] = [
            'id'           => $userId,
            'workspace_id' => $workspaceId,
            'name'         => $name,
            'email'        => $email,
            'role'         => 'admin',
        ];

        redirect('/dashboard');
    }

    public function logout(): void
    {
        // Vymaž remember-me token z DB aj cookie.
        $token = $_COOKIE['remember_me'] ?? '';
        if ($token !== '') {
            $hash = hash('sha256', $token);
            $pdo  = Database::connection();
            $pdo->prepare('DELETE FROM remember_tokens WHERE token_hash = :hash')->execute(['hash' => $hash]);
            setcookie('remember_me', '', time() - 3600, '/', '', false, true);
        }

        $_SESSION = [];
        session_destroy();
        redirect('/login');
    }

    private function setRememberCookie(int $userId): void
    {
        $token    = bin2hex(random_bytes(32));
        $hash     = hash('sha256', $token);
        $expires  = new \DateTime('+30 days');

        $pdo  = Database::connection();
        // Vymaž stare tokeny tohto usera (max 1 aktívny).
        $pdo->prepare('DELETE FROM remember_tokens WHERE user_id = :uid')->execute(['uid' => $userId]);
        $pdo->prepare(
            'INSERT INTO remember_tokens (user_id, token_hash, expires_at) VALUES (:uid, :hash, :exp)'
        )->execute(['uid' => $userId, 'hash' => $hash, 'exp' => $expires->format('Y-m-d H:i:s')]);

        setcookie('remember_me', $token, $expires->getTimestamp(), '/', '', false, true);
    }

    private function generateSlug(string $name): string
    {
        $slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $name) ?? $name);
        $slug = trim($slug, '-') . '-workspace';

        $pdo  = Database::connection();
        $base = $slug;
        $i    = 1;
        while ($pdo->prepare('SELECT id FROM workspaces WHERE slug = :s')->execute(['s' => $slug]) &&
               $pdo->query("SELECT id FROM workspaces WHERE slug = '$slug'")->fetch()) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }
}
