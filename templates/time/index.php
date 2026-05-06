<?php

declare(strict_types=1);

$pageTitle = $pageTitle ?? 'Time Tracking | FlowTrack';
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
            <h1 class="page-title">Time Tracking</h1>
            <p class="page-subtitle">Prehľad odpracovaného času</p>
          </div>
          <button class="btn btn-primary" id="logTimeBtn">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
              <path d="M12 4.5v15m7.5-7.5h-15"/>
            </svg>
            Zaznamenať čas
          </button>
        </div>
      </div>

      <!-- Log time form (skrytý) -->
      <div id="logTimeForm" style="display: none; margin-bottom: 20px;">
        <div class="form-card" style="max-width: 100%;">
          <p class="form-section-title">Nový time entry</p>
          <form method="POST" action="<?= htmlspecialchars(app_url('/time'), ENT_QUOTES, 'UTF-8') ?>">
            <div class="form-row">
              <div class="form-group">
                <label class="form-label" for="project_id">Projekt</label>
                <select id="project_id" name="project_id" class="form-control">
                  <option value="">Vyber projekt</option>
                  <option value="1">FlowTrack MVP</option>
                  <option value="2">API Integrácia</option>
                </select>
              </div>
              <div class="form-group">
                <label class="form-label" for="task_id">Task</label>
                <select id="task_id" name="task_id" class="form-control">
                  <option value="">Vyber task</option>
                  <option value="1">Navrhnúť databázovú schému</option>
                  <option value="2">Implementovať auth modul</option>
                </select>
              </div>
            </div>
            <div class="form-row">
              <div class="form-group">
                <label class="form-label" for="started_at">Dátum a čas začiatku</label>
                <input type="datetime-local" id="started_at" name="started_at" class="form-control" />
              </div>
              <div class="form-group">
                <label class="form-label" for="duration_minutes">Trvanie (minúty)</label>
                <input type="number" id="duration_minutes" name="duration_minutes" class="form-control" placeholder="napr. 90" min="1" />
              </div>
            </div>
            <div class="form-group">
              <label class="form-label" for="description">Popis práce</label>
              <input type="text" id="description" name="description" class="form-control" placeholder="Čo si robil?" />
            </div>
            <div class="form-actions">
              <label style="display: flex; align-items: center; gap: 7px; font-size: 13px; color: var(--text-secondary); cursor: pointer; margin-right: 4px;">
                <input type="checkbox" name="billable" value="1" checked style="accent-color: var(--accent);" />
                Billable
              </label>
              <button type="submit" class="btn btn-primary">Uložiť</button>
              <button type="button" class="btn btn-ghost" id="cancelLogTime">Zrušiť</button>
            </div>
          </form>
        </div>
      </div>

      <!-- Summary cards -->
      <div class="stat-grid" style="margin-bottom: 24px;">
        <div class="stat-card">
          <div class="stat-card-top">
            <span class="stat-card-label">Tento týždeň</span>
            <div class="stat-card-icon indigo">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
          </div>
          <div class="stat-card-value">7h 45m</div>
          <div class="stat-card-sub">5h billable</div>
        </div>
        <div class="stat-card">
          <div class="stat-card-top">
            <span class="stat-card-label">Tento mesiac</span>
            <div class="stat-card-icon green">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
          </div>
          <div class="stat-card-value">24h</div>
          <div class="stat-card-sub">18h billable</div>
        </div>
        <div class="stat-card">
          <div class="stat-card-top">
            <span class="stat-card-label">Billable hodnota</span>
            <div class="stat-card-icon amber">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                <path d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75"/>
              </svg>
            </div>
          </div>
          <div class="stat-card-value">630 €</div>
          <div class="stat-card-sub">za máj 2026</div>
        </div>
        <div class="stat-card">
          <div class="stat-card-top">
            <span class="stat-card-label">Záznamy</span>
            <div class="stat-card-icon blue">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                <path d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/>
              </svg>
            </div>
          </div>
          <div class="stat-card-value">8</div>
          <div class="stat-card-sub">za máj 2026</div>
        </div>
      </div>

      <!-- Filter bar -->
      <div class="filter-bar">
        <span class="filter-bar-label">Filter:</span>
        <select class="filter-select">
          <option value="">Všetky projekty</option>
          <option value="1">FlowTrack MVP</option>
          <option value="2">API Integrácia</option>
        </select>
        <select class="filter-select">
          <option value="">Billable aj nie</option>
          <option value="1">Len billable</option>
          <option value="0">Len non-billable</option>
        </select>
        <input type="month" class="filter-select" value="2026-05" />
      </div>

      <!-- Time entries table -->
      <div class="table-wrap">
        <table class="data-table">
          <thead>
            <tr>
              <th>Dátum</th>
              <th>Projekt</th>
              <th>Task</th>
              <th>Popis</th>
              <th>Trvanie</th>
              <th>Billable</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td class="muted">dnes, 10:00</td>
              <td><a href="<?= htmlspecialchars(app_url('/projects/1'), ENT_QUOTES, 'UTF-8') ?>" style="color: var(--accent);">FlowTrack MVP</a></td>
              <td><a href="<?= htmlspecialchars(app_url('/tasks/2'), ENT_QUOTES, 'UTF-8') ?>" style="color: var(--text-primary);">Auth modul</a></td>
              <td>Implementácia AuthController a login metódy</td>
              <td><strong>2h 30m</strong></td>
              <td><span class="billable-dot yes"></span>Áno</td>
              <td class="right"><button class="btn btn-ghost btn-sm">Zmazať</button></td>
            </tr>
            <tr>
              <td class="muted">včera, 14:30</td>
              <td><a href="<?= htmlspecialchars(app_url('/projects/1'), ENT_QUOTES, 'UTF-8') ?>" style="color: var(--accent);">FlowTrack MVP</a></td>
              <td><a href="<?= htmlspecialchars(app_url('/tasks/2'), ENT_QUOTES, 'UTF-8') ?>" style="color: var(--text-primary);">Auth modul</a></td>
              <td>Napojenie na databázu a testovanie</td>
              <td><strong>1h 00m</strong></td>
              <td><span class="billable-dot yes"></span>Áno</td>
              <td class="right"><button class="btn btn-ghost btn-sm">Zmazať</button></td>
            </tr>
            <tr>
              <td class="muted">30.4., 09:00</td>
              <td><a href="<?= htmlspecialchars(app_url('/projects/1'), ENT_QUOTES, 'UTF-8') ?>" style="color: var(--accent);">FlowTrack MVP</a></td>
              <td><a href="<?= htmlspecialchars(app_url('/tasks/1'), ENT_QUOTES, 'UTF-8') ?>" style="color: var(--text-primary);">DB schéma</a></td>
              <td>DB schéma a migrácia tabuliek</td>
              <td><strong>1h 15m</strong></td>
              <td><span class="billable-dot yes"></span>Áno</td>
              <td class="right"><button class="btn btn-ghost btn-sm">Zmazať</button></td>
            </tr>
            <tr>
              <td class="muted">29.4., 11:00</td>
              <td><a href="<?= htmlspecialchars(app_url('/projects/1'), ENT_QUOTES, 'UTF-8') ?>" style="color: var(--accent);">FlowTrack MVP</a></td>
              <td><a href="<?= htmlspecialchars(app_url('/tasks/8'), ENT_QUOTES, 'UTF-8') ?>" style="color: var(--text-primary);">Routing setup</a></td>
              <td>Router, front controller, autoload</td>
              <td><strong>3h 00m</strong></td>
              <td><span class="billable-dot no"></span>Nie</td>
              <td class="right"><button class="btn btn-ghost btn-sm">Zmazať</button></td>
            </tr>
          </tbody>
        </table>
      </div>

    </div><!-- /.app-content -->
  </div><!-- /.app-main -->
</div><!-- /.app-layout -->

<script>
  var logTimeBtn = document.getElementById('logTimeBtn');
  var logTimeForm = document.getElementById('logTimeForm');
  var cancelBtn = document.getElementById('cancelLogTime');

  logTimeBtn && logTimeBtn.addEventListener('click', function () {
    logTimeForm.style.display = logTimeForm.style.display === 'none' ? 'block' : 'none';
  });

  cancelBtn && cancelBtn.addEventListener('click', function () {
    logTimeForm.style.display = 'none';
  });
</script>

<?php require template_path('partials/footer.php'); ?>
