<?php

declare(strict_types=1);

$styles = ['css/auth.css'];
$scripts = ['js/auth.js'];
$bodyTheme = 'dark';

require template_path('partials/header.php');
?>
<div class="page-glow glow-a"></div>
<div class="page-glow glow-b"></div>
<div class="page-grid"></div>

<main class="auth-shell">
  <section class="hero-panel">
    <div class="hero-topbar">
      <a class="brand" href="<?= htmlspecialchars(app_url('/dashboard'), ENT_QUOTES, 'UTF-8') ?>">
        <span class="brand-mark">FT</span>
        <span class="brand-copy">
          <strong><?= htmlspecialchars(config('app.name', 'FlowTrack'), ENT_QUOTES, 'UTF-8') ?></strong>
          <small>Project management workspace</small>
        </span>
      </a>

      <a class="theme-toggle" href="<?= htmlspecialchars(app_url('/login'), ENT_QUOTES, 'UTF-8') ?>">
        <span class="theme-toggle-label">Späť na auth</span>
      </a>
    </div>

    <div class="hero-copy">
      <p class="eyebrow">Router ready</p>
      <h1>Dashboard route už funguje cez čistý PHP front controller.</h1>
      <p class="hero-lead">
        Toto je zatiaľ len placeholder obrazovka, ale routing už máme pripravený tak, aby sme
        ďalšie moduly pridávali cez controllery a templates, nie cez samostatné súbory v `public`.
      </p>
    </div>
  </section>

  <section class="auth-panel">
    <div class="auth-card">
      <div class="auth-card-top">
        <p class="auth-kicker">Next step</p>
        <h2>Čo máme hotové</h2>
      </div>

      <div class="demo-box" style="margin-top: 24px;">
        <div>
          <p>Aktívne routy</p>
          <small>`/login`, `/register`, `/dashboard` cez `public/index.php`</small>
        </div>
      </div>

      <div class="demo-box">
        <div>
          <p>Architektúra</p>
          <small>`app/core`, `app/controllers`, `templates`, `config`, `storage`, `public/assets`</small>
        </div>
      </div>

      <div class="demo-box">
        <div>
          <p>Ďalší krok</p>
          <small>Napojíme reálne POST formuláre a potom session login.</small>
        </div>
      </div>
    </div>
  </section>
</main>
<?php require template_path('partials/footer.php'); ?>
