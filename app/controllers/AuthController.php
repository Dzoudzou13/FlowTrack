<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;

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
}
