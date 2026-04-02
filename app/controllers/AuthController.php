<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;


final class AuthController extends Controller
{
    public function showLogin(): void
    {
        // Zobrazi login stranku.
        $this->render('auth/login', [
            'pageTitle' => 'Prihlásenie | FlowTrack',
            'activeTab' => 'login',
        ]);
    }

    public function showRegister(): void
    {
        // Zobrazi registracnu stranku.
        $this->render('auth/register', [
            'pageTitle' => 'Registrácia | FlowTrack',
            'activeTab' => 'register',
        ]);
    }

    public function login(): void
    {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($email === '' || $password === '') {
            $this->render('auth/login', [
                'pageTitle' => 'Prihlásenie | FlowTrack',
                'activeTab' => 'login',
                'authError' => 'Vyplň email aj heslo.',
            ]);
            return;
        }

        $pdo = Database::connection();

        $statement = $pdo->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $statement->execute([
            'email' => $email,
        ]);

        $user = $statement->fetch();

        if (! $user || ! password_verify($password, $user['password_hash'])) {
            $this->render('auth/login', [
                'pageTitle' => 'Prihlásenie | FlowTrack',
                'activeTab' => 'login',
                'authError' => 'Nesprávny email alebo heslo.',
            ]);
            return;
        }

        $_SESSION['user'] = [
            'id' => $user['id'],
            'workspace_id' => $user['workspace_id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role'],
        ];

        redirect('/dashboard');
    }

}
