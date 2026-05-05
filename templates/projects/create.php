<?php

declare(strict_types=1);

$pageTitle = $pageTitle ?? 'Nový projekt | FlowTrack';
$styles    = ['css/app.css'];
$scripts   = ['js/app.js'];

require template_path('partials/header.php');

?>
<div class="app-layout">

  <?php require template_path('partials/sidebar.php'); ?>

  <div class="app-main">

    <?php require template_path('partials/topbar.php'); ?>

    <div class="app-content">

      <!-- Page header -->
      <div class="page-header">
        <div class="page-header-row">
          <div>
            <h1 class="page-title">Nový projekt</h1>
            <p class="page-subtitle"><a href="<?= htmlspecialchars(app_url('/projects'), ENT_QUOTES, 'UTF-8') ?>" style="color: var(--accent);">Projects</a> / Nový projekt</p>
          </div>
        </div>
      </div>

      <!-- Form -->
      <div class="form-card">

        <form method="POST" action="<?= htmlspecialchars(app_url('/projects'), ENT_QUOTES, 'UTF-8') ?>">

          <div class="form-group">
            <label class="form-label" for="name">Názov projektu <span class="required">*</span></label>
            <input
              type="text"
              id="name"
              name="name"
              class="form-control"
              placeholder="napr. FlowTrack MVP"
              autocomplete="off"
            />
          </div>

          <div class="form-group">
            <label class="form-label" for="description">Popis</label>
            <textarea
              id="description"
              name="description"
              class="form-control"
              placeholder="Krátky popis projektu..."
              rows="3"
            ></textarea>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label class="form-label" for="status">Status</label>
              <select id="status" name="status" class="form-control">
                <option value="active">Active</option>
                <option value="on-hold">On Hold</option>
                <option value="completed">Completed</option>
                <option value="archived">Archived</option>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label" for="deadline">Deadline</label>
              <input type="date" id="deadline" name="deadline" class="form-control" />
            </div>
          </div>

          <div class="form-group">
            <label class="form-label" for="hourly_rate">Hodinová sadzba (€/h)</label>
            <div class="input-prefix-wrap">
              <span class="input-prefix">€</span>
              <input
                type="number"
                id="hourly_rate"
                name="hourly_rate"
                class="form-control"
                placeholder="0"
                min="0"
                step="0.01"
              />
            </div>
            <p class="form-hint">Používa sa na výpočet billable revenue.</p>
          </div>

          <div class="form-group">
            <label class="form-label">Farba projektu</label>
            <div class="color-swatches" id="colorSwatches">
              <div class="color-swatch selected" data-color="#6366f1" style="background: #6366f1;" title="Indigo"></div>
              <div class="color-swatch" data-color="#3b82f6" style="background: #3b82f6;" title="Blue"></div>
              <div class="color-swatch" data-color="#22c55e" style="background: #22c55e;" title="Green"></div>
              <div class="color-swatch" data-color="#f59e0b" style="background: #f59e0b;" title="Amber"></div>
              <div class="color-swatch" data-color="#ef4444" style="background: #ef4444;" title="Red"></div>
              <div class="color-swatch" data-color="#a855f7" style="background: #a855f7;" title="Purple"></div>
              <div class="color-swatch" data-color="#ec4899" style="background: #ec4899;" title="Pink"></div>
              <div class="color-swatch" data-color="#14b8a6" style="background: #14b8a6;" title="Teal"></div>
              <div class="color-swatch" data-color="#f97316" style="background: #f97316;" title="Orange"></div>
            </div>
            <input type="hidden" id="color" name="color" value="#6366f1" />
          </div>

          <div class="form-actions">
            <button type="submit" class="btn btn-primary">Vytvoriť projekt</button>
            <a href="<?= htmlspecialchars(app_url('/projects'), ENT_QUOTES, 'UTF-8') ?>" class="btn btn-ghost">Zrušiť</a>
          </div>

        </form>
      </div><!-- /.form-card -->

    </div><!-- /.app-content -->
  </div><!-- /.app-main -->
</div><!-- /.app-layout -->

<script>
  // Vyber farby projektu.
  document.querySelectorAll('.color-swatch').forEach(function (swatch) {
    swatch.addEventListener('click', function () {
      document.querySelectorAll('.color-swatch').forEach(function (s) { s.classList.remove('selected'); });
      swatch.classList.add('selected');
      document.getElementById('color').value = swatch.getAttribute('data-color');
    });
  });
</script>

<?php require template_path('partials/footer.php'); ?>
