<?php

declare(strict_types=1);

$pageTitle = $pageTitle ?? 'Upraviť projekt | FlowTrack';
$styles    = ['css/app.css'];
$scripts   = ['js/app.js'];

$project  = $project  ?? [];
$formError = $formError ?? null;

$colors = ['#6366f1','#3b82f6','#22c55e','#f59e0b','#ef4444','#a855f7','#ec4899','#14b8a6','#f97316'];
$currentColor = $project['color'] ?? '#6366f1';

require template_path('partials/header.php');

?>
<div class="app-layout">

  <?php require template_path('partials/sidebar.php'); ?>

  <div class="app-main">

    <?php require template_path('partials/topbar.php'); ?>

    <div class="app-content">

      <div class="page-header">
        <div class="page-header-row">
          <div>
            <h1 class="page-title">Upraviť projekt</h1>
            <p class="page-subtitle">
              <a href="<?= htmlspecialchars(app_url('/projects'), ENT_QUOTES, 'UTF-8') ?>" style="color:var(--accent);">Projekty</a> /
              <a href="<?= htmlspecialchars(app_url('/projects/' . $project['id']), ENT_QUOTES, 'UTF-8') ?>" style="color:var(--accent);"><?= htmlspecialchars($project['name'] ?? '', ENT_QUOTES, 'UTF-8') ?></a> /
              Upraviť
            </p>
          </div>
        </div>
      </div>

      <?php if ($formError): ?>
        <div class="alert alert-error" style="max-width:680px;margin-bottom:16px;"><?= htmlspecialchars($formError, ENT_QUOTES, 'UTF-8') ?></div>
      <?php endif; ?>

      <div class="form-card">
        <form method="POST" action="<?= htmlspecialchars(app_url('/projects/' . $project['id'] . '/update'), ENT_QUOTES, 'UTF-8') ?>">

          <div class="form-group">
            <label class="form-label" for="name">Názov projektu <span class="required">*</span></label>
            <input type="text" id="name" name="name" class="form-control" value="<?= htmlspecialchars($project['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required />
          </div>

          <div class="form-group">
            <label class="form-label" for="description">Popis</label>
            <textarea id="description" name="description" class="form-control" rows="3"><?= htmlspecialchars($project['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label class="form-label" for="status">Status</label>
              <select id="status" name="status" class="form-control">
                <?php foreach (['active'=>'Active','on_hold'=>'On Hold','completed'=>'Completed','archived'=>'Archived'] as $val => $label): ?>
                  <option value="<?= $val ?>" <?= ($project['status'] ?? '') === $val ? 'selected' : '' ?>><?= $label ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label" for="deadline">Deadline</label>
              <input type="date" id="deadline" name="deadline" class="form-control" value="<?= htmlspecialchars($project['deadline'] ?? '', ENT_QUOTES, 'UTF-8') ?>" />
            </div>
          </div>

          <div class="form-group">
            <label class="form-label" for="hourly_rate">Hodinová sadzba (€/h)</label>
            <div class="input-prefix-wrap">
              <span class="input-prefix">€</span>
              <input type="number" id="hourly_rate" name="hourly_rate" class="form-control" value="<?= htmlspecialchars((string)($project['hourly_rate'] ?? '0'), ENT_QUOTES, 'UTF-8') ?>" min="0" step="0.01" />
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">Farba projektu</label>
            <div class="color-swatches" id="colorSwatches">
              <?php foreach ($colors as $c): ?>
                <div class="color-swatch <?= $c === $currentColor ? 'selected' : '' ?>" data-color="<?= $c ?>" style="background:<?= $c ?>;" title="<?= $c ?>"></div>
              <?php endforeach; ?>
            </div>
            <input type="hidden" id="color" name="color" value="<?= htmlspecialchars($currentColor, ENT_QUOTES, 'UTF-8') ?>" />
          </div>

          <div class="form-actions">
            <button type="submit" class="btn btn-primary">Uložiť zmeny</button>
            <a href="<?= htmlspecialchars(app_url('/projects/' . $project['id']), ENT_QUOTES, 'UTF-8') ?>" class="btn btn-ghost">Zrušiť</a>
          </div>

        </form>
      </div>

      <div class="danger-zone" style="max-width:680px;margin-top:24px;">
        <div class="danger-zone-title">Nebezpečná zóna</div>
        <div class="danger-zone-text">Zmazanie projektu je nenávratné. Spolu s projektom sa zmažú všetky tasky, time entries a komentáre.</div>
        <form method="POST" action="<?= htmlspecialchars(app_url('/projects/' . $project['id'] . '/delete'), ENT_QUOTES, 'UTF-8') ?>">
          <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Naozaj chceš zmazať tento projekt? Táto akcia je nenávratná.')">
            Zmazať projekt
          </button>
        </form>
      </div>

    </div><!-- /.app-content -->
  </div><!-- /.app-main -->
</div><!-- /.app-layout -->

<script>
  document.querySelectorAll('.color-swatch').forEach(function (swatch) {
    swatch.addEventListener('click', function () {
      document.querySelectorAll('.color-swatch').forEach(function (s) { s.classList.remove('selected'); });
      swatch.classList.add('selected');
      document.getElementById('color').value = swatch.getAttribute('data-color');
    });
  });
</script>

<?php require template_path('partials/footer.php'); ?>
