<?php

declare(strict_types=1);

$pageTitle = $pageTitle ?? 'Registrácia | FlowTrack';
$styles = ['css/auth.css'];
$scripts = ['js/auth.js'];
$bodyTheme = 'dark';

require template_path('partials/header.php');
require template_path('partials/auth-screen.php');
require template_path('partials/footer.php');
