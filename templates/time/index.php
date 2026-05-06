<?php

declare(strict_types=1);

$pageTitle = $pageTitle ?? 'Time Tracking | FlowTrack';
$styles    = ['css/app.css'];
$scripts   = ['js/app.js'];

$entries             = $entries ?? [];
$projects            = $projects ?? [];
$tasks               = $tasks ?? [];
$totals              = $totals ?? ['total_minutes' => 0, 'billable_minutes' => 0, 'revenue' => 0];
$weekMinutes         = (int) ($weekMinutes ?? 0);
$weekBillableMinutes = (int) ($weekBillableMinutes ?? 0);
$month               = $month ?? date('Y-m');

function minutesLabel(int $minutes): string
{
    $hours = intdiv($minutes, 60);
    $mins  = $minutes % 60;

    return $hours . 'h' . ($mins > 0 ? ' ' . $mins . 'm' : '');
}

$totalMinutes    = (int) ($totals['total_minutes'] ?? 0);
$billableMinutes = (int) ($totals['billable_minutes'] ?? 0);
$revenue         = (float) ($totals['revenue'] ?? 0);

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
            <h1 class="page-title">Time Tracking</h1>
            <p class="page-subtitle">Prehľad odpracovaného času</p>
          </div>
          <button class="btn btn-primary" id="logTimeBtn" type="button">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
              <path d="M12 4.5v15m7.5-7.5h-15"/>
            </svg>
            Zaznamenať čas
          </button>
        </div>
      </div>

      <div id="logTimeForm" style="display: none; margin-bottom: 20px;">
        <div class="form-card" style="max-width: 100%;">
          <p class="form-section-title">Nový time entry</p>
          <form method="POST" action="<?= htmlspecialchars(app_url('/time'), ENT_QUOTES, 'UTF-8') ?>">
            <div class="form-row">
              <div class="form-group">
                <label class="form-label" for="project_id">Projekt</label>
                <select id="project_id" name="project_id" class="form-control" required>
                  <option value="">Vyber projekt</option>
                  <?php foreach ($projects as $project): ?>
                    <option value="<?= (int) $project['id'] ?>"><?= htmlspecialchars($project['name'], ENT_QUOTES, 'UTF-8') ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="form-group">
                <label class="form-label" for="task_id">Task</label>
                <select id="task_id" name="task_id" class="form-control">
                  <option value="">Bez tasku</option>
                  <?php foreach ($tasks as $task): ?>
                    <option value="<?= (int) $task['id'] ?>" data-project-id="<?= (int) $task['project_id'] ?>">
                      <?= htmlspecialchars(($task['project_name'] ?? 'Projekt') . ' / ' . $task['title'], ENT_QUOTES, 'UTF-8') ?>
                    </option>
                  <?php endforeach; ?>
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
                <input type="number" id="duration_minutes" name="duration_minutes" class="form-control" placeholder="napr. 90" min="1" required />
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
          <div class="stat-card-value"><?= htmlspecialchars(minutesLabel($weekMinutes), ENT_QUOTES, 'UTF-8') ?></div>
          <div class="stat-card-sub"><?= htmlspecialchars(minutesLabel($weekBillableMinutes), ENT_QUOTES, 'UTF-8') ?> billable</div>
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
          <div class="stat-card-value"><?= htmlspecialchars(minutesLabel($totalMinutes), ENT_QUOTES, 'UTF-8') ?></div>
          <div class="stat-card-sub"><?= htmlspecialchars(minutesLabel($billableMinutes), ENT_QUOTES, 'UTF-8') ?> billable</div>
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
          <div class="stat-card-value"><?= number_format($revenue, 0, ',', ' ') ?> €</div>
          <div class="stat-card-sub">za <?= htmlspecialchars($month, ENT_QUOTES, 'UTF-8') ?></div>
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
          <div class="stat-card-value"><?= count($entries) ?></div>
          <div class="stat-card-sub">za <?= htmlspecialchars($month, ENT_QUOTES, 'UTF-8') ?></div>
        </div>
      </div>

      <form method="GET" action="<?= htmlspecialchars(app_url('/time'), ENT_QUOTES, 'UTF-8') ?>" class="filter-bar">
        <span class="filter-bar-label">Filter:</span>
        <select name="project_id" class="filter-select" onchange="this.form.submit()">
          <option value="">Všetky projekty</option>
          <?php foreach ($projects as $project): ?>
            <option value="<?= (int) $project['id'] ?>" <?= ((string) ($_GET['project_id'] ?? '') === (string) $project['id']) ? 'selected' : '' ?>>
              <?= htmlspecialchars($project['name'], ENT_QUOTES, 'UTF-8') ?>
            </option>
          <?php endforeach; ?>
        </select>
        <select name="billable" class="filter-select" onchange="this.form.submit()">
          <option value="">Billable aj nie</option>
          <option value="1" <?= (($_GET['billable'] ?? '') === '1') ? 'selected' : '' ?>>Len billable</option>
          <option value="0" <?= (($_GET['billable'] ?? '') === '0') ? 'selected' : '' ?>>Len non-billable</option>
        </select>
        <input type="month" name="month" class="filter-select" value="<?= htmlspecialchars($month, ENT_QUOTES, 'UTF-8') ?>" onchange="this.form.submit()" />
      </form>

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
            <?php if (empty($entries)): ?>
              <tr>
                <td colspan="7" class="muted" style="text-align:center;padding:28px;">Žiadne time entries pre zvolený filter.</td>
              </tr>
            <?php else: ?>
              <?php foreach ($entries as $entry): ?>
                <tr>
                  <td class="muted"><?= htmlspecialchars(date('d.m.Y H:i', strtotime($entry['started_at'])), ENT_QUOTES, 'UTF-8') ?></td>
                  <td>
                    <a href="<?= htmlspecialchars(app_url('/projects/' . $entry['project_id']), ENT_QUOTES, 'UTF-8') ?>" style="color: var(--accent);">
                      <?= htmlspecialchars($entry['project_name'], ENT_QUOTES, 'UTF-8') ?>
                    </a>
                  </td>
                  <td>
                    <?php if (!empty($entry['task_id'])): ?>
                      <a href="<?= htmlspecialchars(app_url('/tasks/' . $entry['task_id']), ENT_QUOTES, 'UTF-8') ?>" style="color: var(--text-primary);">
                        <?= htmlspecialchars($entry['task_title'] ?? 'Task #' . $entry['task_id'], ENT_QUOTES, 'UTF-8') ?>
                      </a>
                    <?php else: ?>
                      <span class="muted">Bez tasku</span>
                    <?php endif; ?>
                  </td>
                  <td><?= htmlspecialchars($entry['description'] ?: 'Bez popisu', ENT_QUOTES, 'UTF-8') ?></td>
                  <td><strong><?= htmlspecialchars(minutesLabel((int) $entry['duration_minutes']), ENT_QUOTES, 'UTF-8') ?></strong></td>
                  <td><span class="billable-dot <?= ((int) $entry['billable'] === 1) ? 'yes' : 'no' ?>"></span><?= ((int) $entry['billable'] === 1) ? 'Áno' : 'Nie' ?></td>
                  <td class="right">
                    <form method="POST" action="<?= htmlspecialchars(app_url('/time/' . $entry['id'] . '/delete'), ENT_QUOTES, 'UTF-8') ?>" style="display:inline;">
                      <button type="submit" class="btn btn-ghost btn-sm" onclick="return confirm('Naozaj chceš zmazať tento záznam?')">Zmazať</button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

    </div>
  </div>
</div>

<script>
  var logTimeBtn = document.getElementById('logTimeBtn');
  var logTimeForm = document.getElementById('logTimeForm');
  var cancelBtn = document.getElementById('cancelLogTime');
  var projectSelect = document.getElementById('project_id');
  var taskSelect = document.getElementById('task_id');

  logTimeBtn && logTimeBtn.addEventListener('click', function () {
    logTimeForm.style.display = logTimeForm.style.display === 'none' ? 'block' : 'none';
  });

  cancelBtn && cancelBtn.addEventListener('click', function () {
    logTimeForm.style.display = 'none';
  });

  function filterTaskOptions() {
    if (!projectSelect || !taskSelect) {
      return;
    }

    var selectedProject = projectSelect.value;
    Array.prototype.forEach.call(taskSelect.options, function (option) {
      if (option.value === '') {
        option.hidden = false;
        return;
      }

      option.hidden = selectedProject !== '' && option.getAttribute('data-project-id') !== selectedProject;
    });

    if (taskSelect.selectedOptions.length && taskSelect.selectedOptions[0].hidden) {
      taskSelect.value = '';
    }
  }

  projectSelect && projectSelect.addEventListener('change', filterTaskOptions);
  filterTaskOptions();
</script>

<?php require template_path('partials/footer.php'); ?>
