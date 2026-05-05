<?php

declare(strict_types=1);

$pageTitle = $pageTitle ?? config('app.name', 'FlowTrack');
$styles = $styles ?? ['css/auth.css'];
$bodyTheme = $bodyTheme ?? 'dark';
?>
<!doctype html>
<html lang="sk">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></title>
    <script>
      // Nastavi temu pred renderom, aby nebolo blikanie.
      (function(){var t=localStorage.getItem('ft-theme')||'dark';document.documentElement.setAttribute('data-theme',t);})();
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Sora:wght@500;600;700&display=swap"
      rel="stylesheet"
    />
    <?php foreach ($styles as $style): ?>
      <link rel="stylesheet" href="<?= htmlspecialchars(asset($style), ENT_QUOTES, 'UTF-8') ?>" />
    <?php endforeach; ?>
  </head>
  <body data-theme="<?= htmlspecialchars($bodyTheme, ENT_QUOTES, 'UTF-8') ?>">
<?php require template_path('partials/cookie-bar.php'); ?>
